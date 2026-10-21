<?php

namespace Tests;

require_once __DIR__ . '/../vendor/autoload.php';

class VerifyInsumoOptimization
{
    public function run(): void
    {
        echo "Testing Insumo model optimization...\n";

        $filePath = __DIR__ . '/../app/Models/Insumo.php';
        $content = file_get_contents($filePath);

        // Remove comments to avoid false positives
        $contentWithoutComments = preg_replace('!/\*.*?\*/!s', '', $content);
        $contentWithoutComments = preg_replace('!//.*!', '', $contentWithoutComments);

        // 1. Verify no scalar correlated SELECT subqueries exist for costo_ultimo
        if (preg_match('/SELECT\s+.*?\(\s*SELECT\s+cd\.costo_unitario/i', $contentWithoutComments)) {
            echo "❌ FAILURE: Correlated subquery for costo_ultimo still found in SELECT clause.\n";
            exit(1);
        } else {
            echo "✅ SUCCESS: No correlated subqueries found in SELECT clause.\n";
        }

        // 2. Verify derived table LEFT JOIN is present
        if (strpos($contentWithoutComments, 'SUBSTRING_INDEX(GROUP_CONCAT(cd.costo_unitario') !== false &&
            strpos($contentWithoutComments, 'ult.costo_ultimo') !== false) {
            echo "✅ SUCCESS: Derived table LEFT JOIN with GROUP_CONCAT/SUBSTRING_INDEX is used for costo_ultimo.\n";
        } else {
            echo "❌ FAILURE: Derived table with GROUP_CONCAT/SUBSTRING_INDEX not found.\n";
            exit(1);
        }

        // 3. Verify parameter passing for getAllBySucursal
        if (strpos($contentWithoutComments, '[$sucursal_id, $sucursal_id]') !== false) {
            echo "✅ SUCCESS: Correct parameter array [\$sucursal_id, \$sucursal_id] passed to fetchAll in getAllBySucursal.\n";
        } else {
            echo "❌ FAILURE: Expected [\$sucursal_id, \$sucursal_id] parameter array in getAllBySucursal.\n";
            exit(1);
        }

        echo "All Insumo model optimization checks passed!\n";
    }
}

$test = new VerifyInsumoOptimization();
$test->run();
