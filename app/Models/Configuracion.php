<?php

namespace App\Models;

class Configuracion extends BaseModel
{
    protected $table = 'configuracion';

    /**
     * Cache for request-level storage
     * @var array<string, mixed>
     */
    protected static $cache = [];

    /**
     * Flag to avoid re-checking BCV rate multiple times per request
     * @var bool
     */
    protected static $tasaBcvChecked = false;

    /**
     * Get value by key
     * Bolt Optimization: Uses request-level in-memory cache to avoid redundant DB queries.
     */
    public function get($key, $default = null)
    {
        // Usar array_key_exists para soportar valores null cacheados (negative caching)
        if (array_key_exists($key, self::$cache)) {
            return self::$cache[$key] !== null ? self::$cache[$key] : $default;
        }

        $sql = "SELECT valor FROM {$this->table} WHERE clave = ? LIMIT 1";
        $result = $this->db->fetchOne($sql, [$key]);

        $value = $result ? $result['valor'] : null;

        self::$cache[$key] = $value;
        return $value !== null ? $value : $default;
    }

    /**
     * Set value by key
     * Bolt Optimization: Updates request-level cache and uses ON DUPLICATE KEY UPDATE.
     */
    public function set($key, $value)
    {
        // Bolt: Reducción de 2 round-trips a 1 usando ON DUPLICATE KEY UPDATE.
        // Se usa la sintaxis compatible con MySQL 8.0+ para evitar VALUES() depreciado.
        $sql = "INSERT INTO {$this->table} (clave, valor) VALUES (?, ?)
                AS new_data ON DUPLICATE KEY UPDATE valor = new_data.valor, updated_at = CURRENT_TIMESTAMP";
        
        $this->db->execute($sql, [$key, $value]);

        // Bolt: Siempre actualizamos el cache y retornamos true si no hubo excepción,
        // ya que execute() puede retornar 0 si el valor no cambió físicamente.
        self::$cache[$key] = $value;
        if ($key === 'tasa_bcv') {
            self::$tasaBcvChecked = true;
        }
        
        return true;
    }

    /**
     * Get BCV Rate, updating from API if expired (> 1 hour)
     * Bolt Optimization: Ensures API/DB check happens only once per request.
     */
    public function getTasaBCV()
    {
        $key = 'tasa_bcv';

        // 1. Si ya se verificó en este request, retornar del cache
        if (self::$tasaBcvChecked && array_key_exists($key, self::$cache)) {
            return (float)(self::$cache[$key] ?? 50.00);
        }

        // 2. Buscar en BD
        $sql = "SELECT valor, updated_at FROM {$this->table} WHERE clave = ? LIMIT 1";
        $row = $this->db->fetchOne($sql, [$key]);

        $rate = $row ? (float)$row['valor'] : 50.00;

        // Fix: Usar fallback de timestamp 0 (1970) para forzar actualización si updated_at es nulo
        $lastUpdate = ($row && isset($row['updated_at'])) ? strtotime($row['updated_at']) : 0;

        // 3. Verificar si expiró (1 hora = 3600 segundos)
        if (time() - $lastUpdate > 3600) {
            $newRate = $this->fetchFromApi();
            if ($newRate) {
                $this->set($key, $newRate);
                $rate = $newRate;
            }
        }

        // 4. Cachear para el resto del request
        self::$cache[$key] = $rate;
        self::$tasaBcvChecked = true;

        return (float)$rate;
    }

    /**
     * Manually refresh the BCV Rate
     */
    public function updateTasaBCV()
    {
        $key = 'tasa_bcv';
        $newRate = $this->fetchFromApi();
        if ($newRate) {
            $this->set($key, $newRate);
            self::$cache[$key] = $newRate;
            self::$tasaBcvChecked = true;
            return (float)$newRate;
        }
        return false;
    }

    /**
     * Fetch from Rafnixg API
     * Bolt Optimization: Reduced timeout to 3s for better resilience.
     */
    private function fetchFromApi()
    {
        $url = 'https://bcv-api.rafnixg.dev/rates/';

        try {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, 3);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) SIPAN/2.0');

            $json = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $error = curl_error($ch);
            curl_close($ch);

            if ($httpCode === 200 && $json) {
                $data = json_decode($json, true);

                // Manejo de diferentes formatos de respuesta de la API
                if (isset($data['dollar'])) return (float)$data['dollar'];
                if (isset($data['USD'])) return (float)$data['USD'];
                if (isset($data['usd'])) return (float)$data['usd'];
                if (isset($data['price'])) return (float)$data['price'];
                if (isset($data['rate'])) return (float)$data['rate'];

                if (is_array($data)) {
                    foreach ($data as $key => $val) {
                        if (strtoupper($key) === 'USD') return (float)$val;
                    }
                }
            } else {
                error_log("BCV API Error: HTTP $httpCode - $error");
            }
        } catch (\Exception $e) {
            error_log("BCV API Exception: " . $e->getMessage());
        }
        return null;
    }
}
