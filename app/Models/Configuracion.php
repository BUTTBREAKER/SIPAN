<?php

namespace App\Models;

class Configuracion extends BaseModel
{
    protected $table = 'configuracion';

    /**
     * Caching en memoria a nivel de request (Optimización Bolt)
     * @var array<string, mixed>
     */
    protected static $cache = [];

    /**
     * Bandera para asegurar que la tasa BCV se verifique solo una vez por request (Optimización Bolt)
     * @var bool
     */
    protected static $tasaBcvChecked = false;

    /**
     * Obtener valor por clave
     * Optimización Bolt: Utiliza caché en memoria a nivel de request para evitar consultas DB redundantes.
     */
    public function get($key, $default = null)
    {
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
     * Establecer valor por clave
     * Optimización Bolt: Actualiza el caché en memoria.
     */
    public function set($key, $value)
    {
        $exists = $this->get($key);
        $success = false;

        if ($exists !== null) {
            $sql = "UPDATE {$this->table} SET valor = ? WHERE clave = ?";
            $success = $this->db->execute($sql, [$value, $key]);
        } else {
            $sql = "INSERT INTO {$this->table} (clave, valor) VALUES (?, ?)";
            $success = $this->db->execute($sql, [$key, $value]);
        }

        if ($success) {
            self::$cache[$key] = $value;
            if ($key === 'tasa_bcv') {
                self::$tasaBcvChecked = true;
            }
        }

        return $success;
    }

    /**
     * Obtener Tasa BCV, actualizando desde API si expiró (> 1 hora)
     * Optimización Bolt: Garantiza que la verificación API/DB ocurra solo una vez por request.
     */
    public function getTasaBCV()
    {
        $key = 'tasa_bcv';

        if (self::$tasaBcvChecked && array_key_exists($key, self::$cache)) {
            return (float)(self::$cache[$key] ?? 50.00);
        }

        $sql = "SELECT valor, updated_at FROM {$this->table} WHERE clave = ? LIMIT 1";
        $row = $this->db->fetchOne($sql, [$key]);

        $rate = $row ? (float)$row['valor'] : 50.00;
        $lastUpdate = $row ? strtotime($row['updated_at']) : 0;

        // Marcar como verificado para este request
        self::$tasaBcvChecked = true;
        self::$cache[$key] = $rate;

        // Verificar si expiró (1 hora = 3600 segundos)
        if (time() - $lastUpdate > 3600) {
            $newRate = $this->fetchFromApi();
            if ($newRate) {
                $this->set($key, $newRate);
                return (float)$newRate;
            }
        }

        return (float)$rate;
    }

    /**
     * Refrescar manualmente la Tasa BCV
     */
    public function updateTasaBCV()
    {
        $key = 'tasa_bcv';
        $newRate = $this->fetchFromApi();
        if ($newRate) {
            $this->set($key, $newRate);
            return (float)$newRate;
        }
        return false;
    }

    /**
     * Obtener desde Rafnixg API
     * Optimización Bolt: Timeout reducido a 3s para mejor resiliencia.
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

                // Manejar diferentes formatos posibles de la API
                if (isset($data['dollar'])) {
                    return (float)$data['dollar'];
                }
                if (isset($data['USD'])) {
                    return (float)$data['USD'];
                }
                if (isset($data['usd'])) {
                    return (float)$data['usd'];
                }
                if (isset($data['price'])) {
                    return (float)$data['price'];
                }
                if (isset($data['rate'])) {
                    return (float)$data['rate'];
                }

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
