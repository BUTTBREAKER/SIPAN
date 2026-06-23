<?php

namespace App\Models;

class Configuracion extends BaseModel
{
    protected $table = 'configuracion';

    /**
     * Request-level in-memory cache to avoid redundant DB queries.
     * @var array<string, mixed>
     */
    protected static $cache = [];

    /**
     * Flag to ensure BCV rate is verified only once per request.
     * @var bool
     */
    protected static $tasaBcvChecked = false;

    /**
     * Get value by key
     * Uses request-level in-memory cache to avoid redundant DB queries.
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
     * Set value by key
     * Updates request-level cache after a successful write.
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
     * Get BCV Rate, updating from API if expired (> 1 hour).
     * Ensures API/DB check happens only once per request.
     */
    public function getTasaBCV()
    {
        $key = 'tasa_bcv';

        if (self::$tasaBcvChecked && array_key_exists($key, self::$cache)) {
            return (float)(self::$cache[$key] ?? 50.00);
        }

        $sql = "SELECT valor, updated_at FROM {$this->table} WHERE clave = ? LIMIT 1";
        $row = $this->db->fetchOne($sql, [$key]);

        $rate = $row ? (float)$row['valor'] : 50.00; // Fallback
        $lastUpdate = $row ? strtotime($row['updated_at']) : 0;

        self::$cache[$key] = $rate;

        // Check if expired (1 hour = 3600 seconds)
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
     * Manually refresh the BCV Rate from the API.
     */
    public function updateTasaBCV()
    {
        $key = 'tasa_bcv';
        $newRate = $this->fetchFromApi();
        if ($newRate) {
            $this->set($key, $newRate);
            self::$tasaBcvChecked = true;
            return (float)$newRate;
        }
        return false;
    }

    /**
     * Fetch current USD/VES rate from Rafnixg BCV API.
     * Timeout is 3s for resilience.
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
