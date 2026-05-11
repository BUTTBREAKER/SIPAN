<?php

namespace App\Core;

class Database {
    public static function getInstance() {
        return new self();
    }
    public function fetchAll($sql, $params = []) { return []; }
    public function fetchOne($sql, $params = []) { return []; }
    public function execute($sql, $params = []) { return true; }
    public function lastInsertId() { return 1; }
}

namespace App\Models;
require_once 'app/Models/BaseModel.php';
require_once 'app/Models/Producto.php';

// Mock DB class to verify SQL
class MockDB {
    public $lastQuery;
    public $lastParams;

    public function fetchAll($sql, $params = []) {
        $this->lastQuery = $sql;
        $this->lastParams = $params;
        return [
            ['id' => 1, 'nombre' => 'Pan', 'stock_actual' => 10, 'precio_actual' => 2.0, 'valor_stock' => 20.0],
            ['id' => 2, 'nombre' => 'Torta', 'stock_actual' => 5, 'precio_actual' => 10.0, 'valor_stock' => 50.0]
        ];
    }
}

$mockDb = new MockDB();
$productoModel = new Producto();

// Inject mock DB
$reflection = new \ReflectionClass($productoModel);
$dbProp = $reflection->getProperty('db');
$dbProp->setAccessible(true);
$dbProp->setValue($productoModel, $mockDb);

echo "Testing getInventoryReport SQL...\n";
$sucursal_id = 1;
$result = $productoModel->getInventoryReport($sucursal_id);

echo "SQL: " . $mockDb->lastQuery . "\n";
if (strpos($mockDb->lastQuery, '(stock_actual * precio_actual) as valor_stock') !== false) {
    echo "✅ SQL contains calculation.\n";
} else {
    echo "❌ SQL missing calculation.\n";
}

if (strpos($mockDb->lastQuery, 'ORDER BY nombre') !== false) {
    echo "✅ SQL contains ORDER BY nombre.\n";
} else {
    echo "❌ SQL missing ORDER BY.\n";
}

echo "Testing Controller logic simulation...\n";
$valor_total = array_sum(array_column($result, 'valor_stock'));
echo "Total Calculated: " . $valor_total . "\n";
if ($valor_total == 70.0) {
    echo "✅ Total calculation is correct.\n";
} else {
    echo "❌ Total calculation error. Expected 70.0, got $valor_total\n";
}
