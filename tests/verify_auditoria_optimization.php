<?php

// Mocking the Database class
namespace App\Core;

class Database {
    private static $instance = null;
    public $queries = [];

    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function fetchAll($sql, $params = []) {
        $this->queries[] = ['sql' => $sql, 'params' => $params];
        return []; // Return empty result for testing
    }

    public function fetchOne($sql, $params = []) {
        $this->queries[] = ['sql' => $sql, 'params' => $params];
        return [];
    }

    public function execute($sql, $params = []) {
        $this->queries[] = ['sql' => $sql, 'params' => $params];
        return true;
    }
}

// Mocking BaseModel
namespace App\Models;

use App\Core\Database;

class BaseModel {
    protected $db;
    protected $table;
    public function __construct() {
        $this->db = Database::getInstance();
    }
}

// Include the Auditoria model
require_once __DIR__ . '/../app/Models/Auditoria.php';

use App\Models\Auditoria;
use App\Core\Database as DB;

$auditoria = new Auditoria();
$db = DB::getInstance();

echo "Running Auditoria::getWithDetails Tests...\n\n";

// Test 1: Default call
$auditoria->getWithDetails(1);
$lastQuery = end($db->queries);
echo "Test 1 (Default): " . ($lastQuery['params'] === [1] ? "PASS" : "FAIL") . "\n";
echo "SQL: " . $lastQuery['sql'] . "\n";
echo "Params: " . json_encode($lastQuery['params']) . "\n\n";

// Test 2: Filter by tabla
$auditoria->getWithDetails(1, 'productos');
$lastQuery = end($db->queries);
echo "Test 2 (Tabla): " . (in_array('productos', $lastQuery['params']) ? "PASS" : "FAIL") . "\n";
echo "SQL: " . $lastQuery['sql'] . "\n\n";

// Test 3: Filter by accion
$auditoria->getWithDetails(1, null, null, 'UPDATE');
$lastQuery = end($db->queries);
echo "Test 3 (Accion): " . (in_array('UPDATE', $lastQuery['params']) ? "PASS" : "FAIL") . "\n";
echo "SQL: " . $lastQuery['sql'] . "\n\n";

// Test 4: Filter by estado (activo)
$auditoria->getWithDetails(1, null, null, null, 'activo');
$lastQuery = end($db->queries);
echo "Test 4 (Estado Activo): " . (strpos($lastQuery['sql'], "a.deshacer = 0") !== false ? "PASS" : "FAIL") . "\n";
echo "SQL: " . $lastQuery['sql'] . "\n\n";

// Test 5: Filter by estado (deshecho)
$auditoria->getWithDetails(1, null, null, null, 'deshecho');
$lastQuery = end($db->queries);
echo "Test 5 (Estado Deshecho): " . (strpos($lastQuery['sql'], "a.deshacer = 1") !== false ? "PASS" : "FAIL") . "\n";
echo "SQL: " . $lastQuery['sql'] . "\n\n";

// Test 6: Custom limit
$auditoria->getWithDetails(1, null, null, null, null, 50);
$lastQuery = end($db->queries);
echo "Test 6 (Limit 50): " . (strpos($lastQuery['sql'], "LIMIT 50") !== false ? "PASS" : "FAIL") . "\n";
echo "SQL: " . $lastQuery['sql'] . "\n\n";
