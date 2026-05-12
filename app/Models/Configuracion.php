<?php

namespace App\Models;

class Configuracion extends BaseModel
{
    protected $table = 'configuracion';
    private static $cache = [];

    // Bolt Optimization: Static in-memory cache for the BCV exchange rate
    private static $cachedTasa = null;

    /**
     * Bolt Optimization: In-memory cache for BCV rate to avoid redundant queries in same request.
     */
    private static $cachedTasa = null;

    /**
     * Cache en memoria para la duración de la petición (Request-level cache)
     * Optimización Bolt: Evita consultas redundantes a la base de datos.
     */
    private static $cache = [];

    /**
     * Flag para asegurar que la lógica de expiración de la tasa BCV se ejecute una vez por petición.
     */
    private static $tasaBcvChecked = false;

    /**
     * Bolt: In-memory request-level cache to avoid redundant DB queries.
     * Since this model is often called from header.php and other components
     * in the same request, caching values here provides a significant performance boost.
     */
    private static $cache = [];
    private static $tasaBcvChecked = false;

    // Bolt Optimization: Request-level in-memory cache
    private static $cache = [];
    private static $tasaBcvChecked = false;

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
     * Cache para valores de configuración (request-level)
     * @var array<string, mixed>
     */
    private static $cache = [];

    /**
     * Cache para la tasa BCV (request-level)
     * @var float|null
     */
    private static $tasaBcvCached = null;

    // Bolt Optimization: Request-level in-memory cache
    private static $cache = [];
    private static $tasaBcvChecked = null;

    /**
     * Bolt Optimization: Request-level in-memory cache to avoid redundant DB lookups.
     */
    private static $cache = [];
    private static $tasaBcvChecked = false;

    /**
     * Cache for request-level storage
     * @var array<string, mixed>
     */
    private static $cache = [];

    /**
     * Flag to avoid re-checking BCV rate multiple times per request
     * @var bool
     */
    private static $tasaBcvChecked = false;

    // Bolt Optimization: Request-level in-memory cache
    private static $cache = [];
    private static $tasaBcvChecked = false;

    // Bolt: Request-level in-memory cache to avoid redundant DB lookups
    private static $cache = [];
    private static $tasaBcvChecked = false;

    /**
     * Get value by key
<<<<<<< bolt-config-caching-884154159979286291
     * Bolt Optimization: Uses in-memory cache to avoid redundant database queries per request.
=======
     * Bolt Optimization: Uses request-level in-memory cache to avoid redundant DB queries.
>>>>>>> main
     */
    public function get($key, $default = null)
    {
        if (array_key_exists($key, self::$cache)) {
<<<<<<< bolt-config-caching-884154159979286291
            return self::$cache[$key];
=======
            return self::$cache[$key] !== null ? self::$cache[$key] : $default;
>>>>>>> main
        }

        $sql = "SELECT valor FROM {$this->table} WHERE clave = ? LIMIT 1";
        $result = $this->db->fetchOne($sql, [$key]);
<<<<<<< bolt-config-caching-884154159979286291

        $value = $result ? $result['valor'] : $default;
        self::$cache[$key] = $value;

        return $value;
=======
        $value = $result ? $result['valor'] : null;

        self::$cache[$key] = $value;
        return $value !== null ? $value : $default;
>>>>>>> main
    }

    /**
     * Set value by key
<<<<<<< bolt-config-caching-884154159979286291
     * Bolt Optimization: Updates in-memory cache to maintain consistency.
=======
     * Bolt Optimization: Updates request-level cache.
>>>>>>> main
     */
    public function set($key, $value)
    {
        // Update cache if key is BCV rate
        if ($key === 'tasa_bcv') {
            self::$cachedTasa = (float)$value;
        }

        $exists = $this->get($key);
        $success = false;
        if ($exists !== null) {
            $sql = "UPDATE {$this->table} SET valor = ? WHERE clave = ?";
<<<<<<< bolt-config-caching-884154159979286291
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
=======
            $success = $this->db->execute($sql, [$value, $key]);
        } else {
            $sql = "INSERT INTO {$this->table} (clave, valor) VALUES (?, ?)";
            $success = $this->db->execute($sql, [$key, $value]);
        }

        if ($success) {
            self::$cache[$key] = $value;
        }
        return $success;
>>>>>>> main
    }

    /**
     * Get BCV Rate, updating from API if expired (> 1 hour)
<<<<<<< bolt-config-caching-884154159979286291
     * Bolt Optimization: Ensures the expensive check and API call only happen once per request.
=======
     * Bolt Optimization: Ensures API/DB check happens only once per request.
>>>>>>> main
     */
    public function getTasaBCV()
    {
        // Bolt Optimization: Return cached value if already fetched during this request
        if (self::$cachedTasa !== null) {
            return self::$cachedTasa;
        }

        $key = 'tasa_bcv';

<<<<<<< bolt-config-caching-884154159979286291
        // If we already checked/updated it in this request, return from cache
        if (self::$tasaBcvChecked && array_key_exists($key, self::$cache)) {
            return self::$cache[$key];
=======
        if (array_key_exists($key, self::$cache) && self::$cache[$key] !== null) {
            return (float)self::$cache[$key];
>>>>>>> main
        }

        $sql = "SELECT valor, updated_at FROM {$this->table} WHERE clave = ? LIMIT 1";
        $row = $this->db->fetchOne($sql, [$key]);

        $rate = $row ? (float)$row['valor'] : 50.00; // Fallback
        $lastUpdate = $row ? strtotime($row['updated_at']) : 0;

        // Guardar en cache para el resto del request
        self::$cache[$key] = $rate;
        self::$tasaBcvChecked = true;

        // Check if expired (1 hour = 3600 seconds)
        if (time() - $lastUpdate > 3600) {
            $newRate = $this->fetchFromApi();
            if ($newRate) {
                // set() will update self::$cachedTasa
                $this->set($key, $newRate);
<<<<<<< bolt-config-caching-884154159979286291
                self::$tasaBcvChecked = true;
                return $newRate;
            }
        }

        self::$cache[$key] = $rate;
        self::$tasaBcvChecked = true;
        return $rate;
=======
                // self::$cache[$key] updated by set()
                return (float)$newRate;
            }
        }

        self::$cache[$key] = $rate;
        return (float)$rate;
                self::$cachedTasa = (float)$newRate;
                return self::$cachedTasa;
            }
        }

        self::$cachedTasa = $rate;
        return self::$cachedTasa;
>>>>>>> main
    }

    /**
     * Manually refresh the BCV Rate
     * Bolt Optimization: Updates request-level cache.
     */
    public function updateTasaBCV()
    {
        $key = 'tasa_bcv';
        $newRate = $this->fetchFromApi();
        if ($newRate) {
            $this->set($key, $newRate);
<<<<<<< bolt-config-caching-884154159979286291
            self::$tasaBcvChecked = true;
            return $newRate;
=======
            return self::$cachedTasa;
>>>>>>> main
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
<<<<<<< bolt-config-caching-884154159979286291
            curl_setopt($ch, CURLOPT_TIMEOUT, 3); // Bolt: Reduced timeout
=======
            curl_setopt($ch, CURLOPT_TIMEOUT, 3); // Optimización Bolt: Reducido de 10 a 3 segundos
>>>>>>> main
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
