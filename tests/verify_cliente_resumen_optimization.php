<?php

namespace App\Core {
    class Database
    {
        public string $lastSql = '';
        public array $lastParams = [];

        public static function getInstance(): static
        {
            return new static();
        }

        public function fetchAll($sql, $params = []): array
        {
            $this->lastSql = $sql;
            $this->lastParams = $params;
            return [
                [
                    'id' => 1,
                    'nombre' => 'Juan',
                    'apellido' => 'Perez',
                    'total_pedidos' => 2,
                    'total_comprado' => 150.00,
                    'total_pagado' => 100.00,
                    'total_deuda' => 50.00
                ]
            ];
        }
    }
}

namespace {
    require_once __DIR__ . '/../app/Models/BaseModel.php';
    require_once __DIR__ . '/../app/Models/Cliente.php';

    use App\Models\Cliente;

    function verify_cliente_resumen(): void
    {
        echo "--- Testing Cliente::getWithResumen() Optimization ---\n";

        $clienteModel = new Cliente();
        $refProp = new ReflectionProperty(\App\Models\BaseModel::class, 'db');
        $refProp->setAccessible(true);
        /** @var \App\Core\Database $mockDb */
        $mockDb = $refProp->getValue($clienteModel);

        // Test 1: With sucursal_id = 5
        echo "Test 1: Filtered query (sucursal_id = 5)\n";
        $result = $clienteModel->getWithResumen(5);

        assert(str_contains($mockDb->lastSql, 'COALESCE(SUM(p.total), 0) as total_comprado'), 'Missing COALESCE on total_comprado');
        assert(str_contains($mockDb->lastSql, 'COALESCE(SUM(p.monto_pagado), 0) as total_pagado'), 'Missing COALESCE on total_pagado');
        assert(str_contains($mockDb->lastSql, 'COALESCE(SUM(p.monto_deuda), 0) as total_deuda'), 'Missing COALESCE on total_deuda');
        assert(str_contains($mockDb->lastSql, 'LEFT JOIN pedidos p ON c.id = p.id_cliente'), 'Missing LEFT JOIN on pedidos');
        assert(str_contains($mockDb->lastSql, 'WHERE c.id_sucursal = ?'), 'Missing WHERE clause for sucursal');
        assert(!str_contains($mockDb->lastSql, 'v_resumen_pedidos_cliente'), 'Should not use v_resumen_pedidos_cliente');
        assert($mockDb->lastParams === [5], 'Params should be [5]');

        echo "✅ PASS: Filtered query uses unified LEFT JOIN with COALESCE aggregations and correct parameters.\n";

        // Test 2: Without sucursal_id (null)
        echo "\nTest 2: Unfiltered query (sucursal_id = null)\n";
        $resultUnfiltered = $clienteModel->getWithResumen(null);

        assert(str_contains($mockDb->lastSql, 'COALESCE(SUM(p.total), 0) as total_comprado'), 'Missing COALESCE on total_comprado');
        assert(!str_contains($mockDb->lastSql, 'WHERE c.id_sucursal = ?'), 'Should not have WHERE clause when sucursal is null');
        assert(!str_contains($mockDb->lastSql, 'v_resumen_pedidos_cliente'), 'Should not use v_resumen_pedidos_cliente');
        assert(empty($mockDb->lastParams), 'Params should be empty');

        echo "✅ PASS: Unfiltered query uses same unified query structure without WHERE clause or reliance on missing view.\n";

        echo "\nSUCCESS: All Cliente::getWithResumen() tests passed successfully.\n";
    }

    verify_cliente_resumen();
}
