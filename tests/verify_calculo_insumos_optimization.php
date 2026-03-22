<?php

namespace Tests;

require_once __DIR__ . '/../vendor/autoload.php';

use App\Controllers\CalculoInsumosController;

class VerifyCalculoInsumosOptimization
{
    public function run()
    {
        echo "Testing CalculoInsumosController optimization...\n";

        $filePath = __DIR__ . '/../app/Controllers/CalculoInsumosController.php';
        $content = file_get_contents($filePath);

        // 1. Check if the redundant find call is gone
        if (strpos($content, '$this->insumoModel->find(') === false) {
            echo "✅ SUCCESS: No calls to \$this->insumoModel->find() found in CalculoInsumosController.php\n";
        } else {
            echo "❌ FAILURE: \$this->insumoModel->find() still exists in CalculoInsumosController.php\n";
            exit(1);
        }

        // 2. Check if it's using the array data correctly
        if (strpos($content, '$insumo_receta[\'stock_actual\']') !== false &&
            strpos($content, '$insumo_receta[\'nombre\']') !== false &&
            strpos($content, '$insumo_receta[\'unidad_medida\']') !== false &&
            strpos($content, '$insumo_receta[\'precio_unitario\']') !== false) {
            echo "✅ SUCCESS: Controller is correctly using data from \$insumo_receta array.\n";
        } else {
            echo "❌ FAILURE: Controller is not using the expected array keys from \$insumo_receta.\n";
            exit(1);
        }

        echo "Optimization verified via static analysis.\n";
    }
}

$test = new VerifyCalculoInsumosOptimization();
$test->run();
