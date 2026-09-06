<?php

namespace App\Core {
    class Database
    {
        private static $instance = null;
        public $queries = [];

        public static function getInstance()
        {
            if (self::$instance === null) {
                self::$instance = new self();
            }
            return self::$instance;
        }

        public function beginTransaction()
        {
            $this->queries[] = "BEGIN";
        }

        public function commit()
        {
            $this->queries[] = "COMMIT";
        }

        public function rollback()
        {
            $this->queries[] = "ROLLBACK";
        }

        public function execute($sql, $params = [])
        {
            $this->queries[] = ['sql' => $sql, 'params' => $params];
            return 1;
        }

        public function fetchAll($sql, $params = [])
        {
            $this->queries[] = ['sql' => $sql, 'params' => $params];
            return [];
        }

        public function fetchOne($sql, $params = [])
        {
            $this->queries[] = ['sql' => $sql, 'params' => $params];
            return [];
        }
    }
}

namespace {
    require_once __DIR__ . '/../app/Models/BaseModel.php';
    require_once __DIR__ . '/../app/Models/Proveedor.php';

    use App\Models\Proveedor;
    use App\Core\Database;

    function verifyProveedorBatchInsumos()
    {
        echo "Testing Proveedor::addInsumos Optimization...\n";

        $db = Database::getInstance();
        $model = new Proveedor();

        $insumos = [
            ['id_insumo' => 101, 'precio' => 15.50, 'tiempo_entrega' => 3],
            ['id_insumo' => 102, 'precio' => 30.00, 'tiempo_entrega' => 5],
            ['id_insumo' => 103, 'precio' => 45.25, 'tiempo_entrega' => 1],
        ];

        $model->addInsumos(5, $insumos);

        $batchInsertFound = false;
        foreach ($db->queries as $q) {
            if (is_array($q) && strpos($q['sql'], 'INSERT INTO proveedor_insumos') !== false) {
                if (strpos($q['sql'], 'VALUES (?, ?, ?, ?), (?, ?, ?, ?), (?, ?, ?, ?)') !== false) {
                    $batchInsertFound = true;
                    if (count($q['params']) === 12) {
                        echo "✅ SUCCESS: Batch INSERT generated with 12 parameters (3 rows x 4 cols).\n";
                    } else {
                        echo "❌ FAIL: Expected 12 parameters, got " . count($q['params']) . "\n";
                        exit(1);
                    }
                }
            }
        }

        if ($batchInsertFound) {
            echo "✅ SUCCESS: Proveedor::addInsumos batch optimization verified!\n";
        } else {
            echo "❌ FAIL: Multi-row batch INSERT statement was not found.\n";
            exit(1);
        }
    }

    verifyProveedorBatchInsumos();
}
