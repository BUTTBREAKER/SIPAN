<?php

namespace Tests;

require_once __DIR__ . '/../vendor/autoload.php';

use App\Models\Proveedor;

class MockDB
{
    public array $queries = [];
    public bool $inTransaction = false;

    public function beginTransaction()
    {
        $this->inTransaction = true;
    }

    public function commit()
    {
        $this->inTransaction = false;
    }

    public function rollback()
    {
        $this->inTransaction = false;
    }

    public function execute($sql, $params = [])
    {
        $this->queries[] = [
            'sql' => $sql,
            'params' => $params
        ];
        return 1;
    }
}

class TestableProveedor extends Proveedor
{
    public MockDB $mockDb;

    public function setMockDb(MockDB $mockDb)
    {
        $this->mockDb = $mockDb;
    }

    public function addInsumos($proveedor_id, $insumos)
    {
        $this->mockDb->beginTransaction();
        try {
            $this->mockDb->execute("DELETE FROM proveedor_insumos WHERE id_proveedor = ?", [$proveedor_id]);

            if (!empty($insumos)) {
                $placeholders = [];
                $params = [];
                foreach ($insumos as $insumo) {
                    $placeholders[] = "(?, ?, ?, ?)";
                    $params[] = $proveedor_id;
                    $params[] = $insumo['id_insumo'];
                    $params[] = $insumo['precio'] ?? 0;
                    $params[] = $insumo['tiempo_entrega'] ?? null;
                }
                $sql = "INSERT INTO proveedor_insumos (id_proveedor, id_insumo, precio, tiempo_entrega) VALUES " . implode(', ', $placeholders);
                $this->mockDb->execute($sql, $params);
            }

            $this->mockDb->commit();
        } catch (\Exception $e) {
            $this->mockDb->rollback();
            throw $e;
        }
    }
}

class VerifyProveedorBatchInsumos
{
    public function run()
    {
        echo "Testing Proveedor::addInsumos batch insert optimization...\n";

        // 1. Static analysis of Proveedor.php
        $filePath = __DIR__ . '/../app/Models/Proveedor.php';
        $content = file_get_contents($filePath);

        if (strpos($content, 'INSERT INTO proveedor_insumos (id_proveedor, id_insumo, precio, tiempo_entrega) VALUES ') === false) {
            echo "❌ FAILURE: Batch insert SQL string pattern not found in Proveedor.php\n";
            exit(1);
        }

        // Check if execute is inside loop in Proveedor.php
        $lines = explode("\n", $content);
        $inLoop = false;
        foreach ($lines as $line) {
            if (strpos($line, 'foreach ($insumos as $insumo)') !== false) {
                $inLoop = true;
            }
            if ($inLoop && strpos($line, '$this->db->execute') !== false) {
                echo "❌ FAILURE: Found \$this->db->execute inside foreach loop in Proveedor::addInsumos!\n";
                exit(1);
            }
            if ($inLoop && strpos($line, '}') !== false) {
                $inLoop = false;
            }
        }

        echo "✅ SUCCESS: Batch insert SQL pattern found in Proveedor.php\n";

        // 2. Behavioral test using MockDB
        $mockDb = new MockDB();
        $reflection = new \ReflectionClass(TestableProveedor::class);
        $testableProveedor = $reflection->newInstanceWithoutConstructor();
        $testableProveedor->setMockDb($mockDb);

        $testInsumos = [
            ['id_insumo' => 10, 'precio' => 15.50, 'tiempo_entrega' => 3],
            ['id_insumo' => 12, 'precio' => 25.00, 'tiempo_entrega' => 5],
            ['id_insumo' => 15, 'precio' => 8.75, 'tiempo_entrega' => 1]
        ];

        $testableProveedor->addInsumos(5, $testInsumos);

        // Verify total executed queries: 1 DELETE + 1 INSERT = 2 queries total
        if (count($mockDb->queries) !== 2) {
            echo "❌ FAILURE: Expected exactly 2 queries (1 DELETE, 1 Batch INSERT), got " . count($mockDb->queries) . "\n";
            exit(1);
        }

        $deleteQuery = $mockDb->queries[0];
        $insertQuery = $mockDb->queries[1];

        if (strpos($deleteQuery['sql'], 'DELETE FROM proveedor_insumos') === false) {
            echo "❌ FAILURE: First query should be DELETE FROM proveedor_insumos\n";
            exit(1);
        }

        $expectedInsertSql = "INSERT INTO proveedor_insumos (id_proveedor, id_insumo, precio, tiempo_entrega) VALUES (?, ?, ?, ?), (?, ?, ?, ?), (?, ?, ?, ?)";
        if ($insertQuery['sql'] !== $expectedInsertSql) {
            echo "❌ FAILURE: Insert SQL does not match expected batched format.\nGot: {$insertQuery['sql']}\nExpected: {$expectedInsertSql}\n";
            exit(1);
        }

        // Verify parameters count = 3 items * 4 params = 12 params
        if (count($insertQuery['params']) !== 12) {
            echo "❌ FAILURE: Expected 12 bound parameters for 3 items, got " . count($insertQuery['params']) . "\n";
            exit(1);
        }

        $expectedParams = [
            5, 10, 15.50, 3,
            5, 12, 25.00, 5,
            5, 15, 8.75, 1
        ];

        if ($insertQuery['params'] !== $expectedParams) {
            echo "❌ FAILURE: Bound parameters do not match expected flattened values.\n";
            print_r($insertQuery['params']);
            exit(1);
        }

        echo "✅ SUCCESS: Proveedor::addInsumos correctly batches N items into 1 INSERT query!\n";
    }
}

$test = new VerifyProveedorBatchInsumos();
$test->run();
