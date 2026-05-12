<?php

require_once __DIR__ . '/../vendor/autoload.php';

use App\Models\Venta;

// Mock DB class to capture SQL
class MockDB {
    public $lastQuery = '';
    public $lastParams = [];

    public function fetchAll($sql, $params = []) {
        $this->lastQuery = $sql;
        $this->lastParams = $params;
        return [];
    }

    public function fetchOne($sql, $params = []) {
        $this->lastQuery = $sql;
        $this->lastParams = $params;
        return [];
    }
}

// Mock Venta model to use MockDB
class MockVenta extends Venta {
    public function __construct($db) {
        $this->db = $db;
    }
}

$mockDb = new MockDB();
$ventaModel = new MockVenta($mockDb);

echo "--- Testing Venta::getWithDetails Optimization (Mocked) ---\n";

$ventaModel->getWithDetails(1);

$sql = $mockDb->lastQuery;
echo "Generated SQL: $sql\n";

if (strpos($sql, 'LEFT JOIN venta_productos') !== false) {
    echo "❌ Error: Redundant LEFT JOIN venta_productos still present.\n";
    exit(1);
}

if (strpos($sql, 'COUNT(vp.id)') !== false) {
    echo "❌ Error: Redundant COUNT(vp.id) still present.\n";
    exit(1);
}

if (strpos($sql, 'GROUP BY') !== false) {
    echo "❌ Error: Redundant GROUP BY clause still present.\n";
    exit(1);
}

echo "✅ Venta optimization verified (Mocked)!\n";
