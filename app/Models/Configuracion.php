<?php

namespace App\Models;

use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;

class Configuracion extends BaseModel implements LoggerAwareInterface
{
    use LoggerAwareTrait;

    protected string $table = 'configuracion';

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
        $url = 'https://api.montosve.com/v1/fx/rates';

        try {
            $ch = curl_init();
            $headers = [];
            $apiKey = $this->get('montosve_api_key'); // optional API key
            if ($apiKey) {
                $headers[] = 'X-API-Key: ' . $apiKey;
            }
            $headers[] = 'Accept: application/json';
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, 5);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) SIPAN/2.0');

            $json = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $error = curl_error($ch);
            curl_close($ch);

            if ($httpCode === 200 && $json) {
                $data = json_decode($json, true);
                // MontosVE response handling
                $inner = $data['data'] ?? $data;
                if (isset($inner['rates']['USD'])) {
                    return (float)$inner['rates']['USD'];
                }
                if (isset($inner['rates']['bcv'])) {
                    return (float)$inner['rates']['bcv'];
                }
                if (isset($inner['rate'])) {
                    return (float)$inner['rate'];
                }
                // Fallback: look for any USD key
                foreach ($inner as $key => $val) {
                    if (is_string($key) && strtoupper($key) === 'USD' && is_numeric($val)) {
                        return (float)$val;
                    }
                    if (is_array($val) && isset($val['USD'])) {
                        return (float)$val['USD'];
                    }
                }
            } else {
                $this->logger?->error('BCV API Error: HTTP {code} - {error}', [
                    'code' => $httpCode,
                    'error' => $error,
                ]);
            }
        } catch (\Exception $e) {
            $this->logger?->error('BCV API Exception: {message}', ['message' => $e->getMessage()]);
        }
        return null;
    }
}
