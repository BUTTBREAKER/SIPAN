<?php

namespace App\Models;

class Configuracion extends BaseModel
{
    protected $table = 'configuracion';

    // Bolt: Request-level in-memory cache to avoid redundant DB lookups
    private static $cache = [];
    private static $tasaBcvChecked = false;

    /**
     * Get value by key
     * Bolt Optimization: Uses in-memory cache to avoid redundant database queries per request.
     */
    public function get($key, $default = null)
    {
        if (array_key_exists($key, self::$cache)) {
            return self::$cache[$key];
        }

        $sql = "SELECT valor FROM {$this->table} WHERE clave = ? LIMIT 1";
        $result = $this->db->fetchOne($sql, [$key]);

        $value = $result ? $result['valor'] : $default;
        self::$cache[$key] = $value;

        return $value;
    }

    /**
     * Set value by key
     * Bolt Optimization: Updates in-memory cache to maintain consistency.
     */
    public function set($key, $value)
    {
        $exists = $this->get($key);
        if ($exists !== null) {
            $sql = "UPDATE {$this->table} SET valor = ? WHERE clave = ?";
            $this->db->execute($sql, [$value, $key]);
        } else {
            $sql = "INSERT INTO {$this->table} (clave, valor) VALUES (?, ?)";
            $this->db->execute($sql, [$key, $value]);
        }

        // Update cache
        self::$cache[$key] = $value;
        if ($key === 'tasa_bcv') {
            self::$tasaBcvChecked = true;
        }

        return true;
    }

    /**
     * Get BCV Rate, updating from API if expired (> 1 hour)
     * Bolt Optimization: Ensures the expensive check and API call only happen once per request.
     */
    public function getTasaBCV()
    {
        $key = 'tasa_bcv';

        // If we already checked/updated it in this request, return from cache
        if (self::$tasaBcvChecked && array_key_exists($key, self::$cache)) {
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
                self::$tasaBcvChecked = true;
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
            self::$tasaBcvChecked = true;
            return $newRate;
        }
        return false;
    }

    /**
     * Fetch from Rafnixg API
     * Bolt Optimization: Reduced timeout from 10s to 3s for better resilience.
     */
    private function fetchFromApi()
    {
        $url = 'https://bcv-api.rafnixg.dev/rates/';

        try {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, 3); // Bolt: Reduced timeout
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
