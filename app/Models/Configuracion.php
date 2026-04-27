<?php

namespace App\Models;

class Configuracion extends BaseModel
{
    protected $table = 'configuracion';

    /**
     * Cache para valores de configuración (request-level)
     * @var array<string, mixed>
     */
    private static $cache = [];

    /**
     * Cache para la tasa BCV (request-level)
     * @var float|null
     */
    private static $tasaBcvCached = null;

    /**
     * Get value by key
     */
    public function get($key, $default = null)
    {
        if (array_key_exists($key, self::$cache)) {
            return self::$cache[$key] ?? $default;
        }

        $sql = "SELECT valor FROM {$this->table} WHERE clave = ? LIMIT 1";
        $result = $this->db->fetchOne($sql, [$key]);

        $value = $result ? $result['valor'] : null;
        self::$cache[$key] = $value;

        return $value ?? $default;
    }

    /**
     * Set value by key
     */
    public function set($key, $value)
    {
        // Limpiar cache para esta clave
        unset(self::$cache[$key]);
        if ($key === 'tasa_bcv') {
            self::$tasaBcvCached = null;
        }

        $exists = $this->get($key);
        if ($exists !== null) {
            $sql = "UPDATE {$this->table} SET valor = ? WHERE clave = ?";
            $result = $this->db->execute($sql, [$value, $key]);
        } else {
            $sql = "INSERT INTO {$this->table} (clave, valor) VALUES (?, ?)";
            $result = $this->db->execute($sql, [$key, $value]);
        }

        // Actualizar cache con el nuevo valor
        self::$cache[$key] = $value;
        return $result;
    }

    /**
     * Get BCV Rate, updating from API if expired (> 1 hour)
     */
    public function getTasaBCV()
    {
        // Optimización Bolt: Cache de solicitud para evitar múltiples consultas y expiraciones por request
        if (self::$tasaBcvCached !== null) {
            return self::$tasaBcvCached;
        }

        $key = 'tasa_bcv';
        $sql = "SELECT valor, updated_at FROM {$this->table} WHERE clave = ? LIMIT 1";
        $row = $this->db->fetchOne($sql, [$key]);

        $rate = $row ? (float)$row['valor'] : 50.00; // Fallback
        $lastUpdate = $row ? strtotime($row['updated_at']) : 0;

        // Check if expired (1 hour = 3600 seconds)
        if (time() - $lastUpdate > 3600) {
            $newRate = $this->fetchFromApi();
            if ($newRate) {
                $this->set($key, $newRate);
                self::$tasaBcvCached = $newRate;
                return $newRate;
            }
        }

        self::$tasaBcvCached = $rate;
        return $rate;
    }

    public function updateTasaBCV()
    {
        $key = 'tasa_bcv';
        $newRate = $this->fetchFromApi();
        if ($newRate) {
            $this->set($key, $newRate);
            self::$tasaBcvCached = (float)$newRate;
            return $newRate;
        }
        return false;
    }

    /**
     * Fetch from Rafnixg API
     */
    private function fetchFromApi()
    {
        $url = 'https://bcv-api.rafnixg.dev/rates/';

        try {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, 3); // Optimización Bolt: Reducido de 10s a 3s para resiliencia
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) SIPAN/2.0');

            $json = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $error = curl_error($ch);
            curl_close($ch);

            if ($httpCode === 200 && $json) {
                $data = json_decode($json, true);

                // New API structure found: {"dollar": 325.38, "date": "2026-01-08"}
                if (isset($data['dollar'])) {
                    return (float)$data['dollar'];
                }

                // Rafnixg API typically returns an array of rates.
                // We search for USD or the first rate available.
                if (isset($data['USD'])) {
                    return (float)$data['USD'];
                }
                if (isset($data['usd'])) {
                    return (float)$data['usd'];
                }

                // Fallback for different JSON structures
                if (isset($data['price'])) {
                    return (float)$data['price'];
                }
                if (isset($data['rate'])) {
                    return (float)$data['rate'];
                }

                // If it's an array of objects
                if (is_array($data)) {
                    foreach ($data as $key => $val) {
                        if (strtoupper($key) === 'USD') {
                            return (float)$val;
                        }
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
