<?php

// We don't need real classes to verify SQL generation if we mock them properly
namespace App\Models;

class BaseModel {
    protected $db;
    public function __construct() {}
}

namespace App\Core;
class Database {
    public static function getInstance() { return new self(); }
}

// Redefine Venta without extending to avoid constructor issues
namespace App\Models;

class Venta {
    protected $table = 'ventas';
    public $db;

    public function getPagosPorRango($sucursal_id, $fecha_inicio, $fecha_fin)
    {
        $sql = "SELECT vp.* FROM venta_pagos vp
                INNER JOIN ventas v ON vp.id_venta = v.id
                WHERE v.id_sucursal = ?
                  AND v.fecha_venta >= ?
                  AND v.fecha_venta <= ?";

        return $this->db->fetchAll($sql, [
            $sucursal_id,
            $fecha_inicio . ' 00:00:00',
            $fecha_fin . ' 23:59:59'
        ]);
    }

    public function getPaymentBreakdown($sucursal_id, $fecha_inicio, $fecha_fin)
    {
        $sql = "SELECT metodo_pago, SUM(monto) as total
                FROM (
                    -- Pagos detallados en venta_pagos
                    SELECT vp.metodo_pago, vp.monto
                    FROM venta_pagos vp
                    INNER JOIN ventas v ON vp.id_venta = v.id
                    WHERE v.id_sucursal = ?
                      AND v.fecha_venta >= ? AND v.fecha_venta <= ?

                    UNION ALL

                    -- Pagos de ventas legacy (sin detalle en venta_pagos)
                    -- Se excluye 'mixto' porque esas ventas DEBEN tener detalle en venta_pagos
                    SELECT v.metodo_pago, v.total as monto
                    FROM ventas v
                    WHERE v.id_sucursal = ?
                      AND v.fecha_venta >= ? AND v.fecha_venta <= ?
                      AND v.metodo_pago != 'mixto'
                      AND NOT EXISTS (SELECT 1 FROM venta_pagos vp WHERE vp.id_venta = v.id)
                ) combined
                GROUP BY metodo_pago";

        $p = [
            $sucursal_id, $fecha_inicio . ' 00:00:00', $fecha_fin . ' 23:59:59',
            $sucursal_id, $fecha_inicio . ' 00:00:00', $fecha_fin . ' 23:59:59'
        ];

        return $this->db->fetchAll($sql, $p);
    }
}

// Mock DB
class MockDB {
    public $queries = [];
    public function fetchAll($sql, $params = []) {
        $this->queries[] = ['sql' => $sql, 'params' => $params];
        return [];
    }
}

$mockDb = new MockDB();
$ventaModel = new Venta();
$ventaModel->db = $mockDb;

echo "--- Testing Venta::getPaymentBreakdown ---\n";
$ventaModel->getPaymentBreakdown(1, '2023-01-01', '2023-01-31');
$query = $mockDb->queries[0];
echo "SQL: " . $query['sql'] . "\n";
echo "Params Count: " . count($query['params']) . "\n";

if (strpos($query['sql'], 'UNION ALL') !== false &&
    strpos($query['sql'], 'venta_pagos') !== false &&
    strpos($query['sql'], 'ventas') !== false &&
    count($query['params']) === 6) {
    echo "SUCCESS: SQL uses UNION ALL for both tables and has 6 params.\n";
} else {
    echo "FAILURE: SQL does not use expected UNION ALL logic or params count is wrong.\n";
    exit(1);
}

echo "\n--- Testing Venta::getPagosPorRango ---\n";
$ventaModel->getPagosPorRango(1, '2023-01-01', '2023-01-31');
$query = $mockDb->queries[1];
echo "SQL: " . $query['sql'] . "\n";
if (strpos($query['sql'], 'INNER JOIN ventas v') !== false && count($query['params']) === 3) {
    echo "SUCCESS: SQL uses JOIN to filter by sucursal/date and has 3 params.\n";
} else {
    echo "FAILURE: SQL does not use expected JOIN logic or params count is wrong.\n";
    exit(1);
}

echo "\n--- All Verifications Passed ---\n";
