<?php

namespace Tests;

require_once __DIR__ . '/../vendor/autoload.php';

use App\Models\Insumo;

class VerifyInsumoOptimization
{
    public function run()
    {
        echo "Testing Insumo model optimization...\n";

        $filePath = __DIR__ . '/../app/Models/Insumo.php';
        $content = file_get_contents($filePath);

        // Remove comments before running regex check to avoid false positives in documentation
        $cleanContent = preg_replace('!/\*.*?\*/!s', '', $content);
        $cleanContent = preg_replace('!//.*!', '', $cleanContent);

        // Check if scalar subqueries in SELECT list exist in getAllBySucursal or all
        if (preg_replace('/\s+/', ' ', $cleanContent) && preg_match('/SELECT\s+i\.\*,\s*\(SELECT/i', $cleanContent)) {
            echo "❌ FAILURE: Correlated scalar subquery still found in Insumo.php SELECT list!\n";
            exit(1);
        }

        echo "✅ SUCCESS: No correlated scalar subquery in SELECT clause of Insumo.php.\n";

        // Check for derived table JOIN pattern
        if (strpos($content, 'LEFT JOIN (') !== false && strpos($content, 'uc.costo_ultimo') !== false) {
            echo "✅ SUCCESS: Derived table JOIN for uc.costo_ultimo correctly implemented.\n";
        } else {
            echo "❌ FAILURE: Derived table JOIN not found in Insumo.php.\n";
            exit(1);
        }

        echo "Insumo optimization static verification passed.\n";
    }
}

$test = new VerifyInsumoOptimization();
$test->run();
