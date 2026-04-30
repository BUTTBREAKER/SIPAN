<?php

namespace App\Models;

class Configuracion extends BaseModel
{
    protected $table = 'configuracion';

    /**
     * Bolt Optimization: Request-level in-memory cache to avoid redundant DB lookups.
     */
    private static $cache = [];
    private static $tasaBcvChecked = false;

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

        // Store only the raw value (or null) to correctly handle changing $default values across calls
        self::$cache[$key] = $result ? $result['valor'] : null;

        return self::$cache[$key] ?? $default;
    }

    /**
     * Set value by key
     */
    public function set($key, $value)
    {
        $exists = $this->get($key);
        if ($exists !== null) {
            $sql = "UPDATE {$this->table} SET valor = ? WHERE clave = ?";
            $result = $this->db->execute($sql, [$value, $key]);
        } else {
            $sql = "INSERT INTO {$this->table} (clave, valor) VALUES (?, ?)";
            $result = $this->db->execute($sql, [$key, $value]);
        }

        // Update in-memory cache
        self::$cache[$key] = $value;
        if ($key === 'tasa_bcv') {
            self::$tasaBcvChecked = true;
        }

        return $result;
    }

    /**
     * Get BCV Rate, updating from API if expired (> 1 hour)
     * Bolt Optimization: Uses request-level cache to ensure logic runs only once per request.
     */
    public function getTasaBCV()
    {
        $key = 'tasa_bcv';

        // Return cached rate if we already checked it in this request
        if (self::$tasaBcvChecked && array_key_exists($key, self::$cache) && self::$cache[$key] !== null) {
            return self::$cache[$key];
        }

        $sql = "SELECT valor, updated_at FROM {$this->table} WHERE clave = ? LIMIT 1";
        $row = $this->db->fetchOne($sql, [$key]);

        $rate = $row ? $row['valor'] : 50.00; // Fallback
        $lastUpdate = $row ? strtotime($row['updated_at']) : 0;

        // Check if expired (1 hour = 3600 seconds)
        if (time() - $lastUpdate > 3600) {
            $newRate = $this->fetchFromApi();
            if ($newRate) {
                $this->set($key, $newRate);
                // self::set updates self::$tasaBcvChecked and self::$cache
                return $newRate;
            }
        }

        self::$cache[$key] = $rate;
        self::$tasaBcvChecked = true;
        return $rate;
    }

    public function updateTasaBCV()
    {
        $key = 'tasa_bcv';
        $newRate = $this->fetchFromApi();
        if ($newRate) {
            $this->set($key, $newRate);
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
            // Bolt: Reduced timeout from 10s to 3s for better request resilience
            curl_setopt($ch, CURLOPT_TIMEOUT, 3);
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
