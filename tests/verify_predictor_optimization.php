<?php

require_once __DIR__ . '/../app/Helpers/Predictor.php';

use App\Helpers\Predictor;

function generateLargeDataset($n) {
    $data = [];
    $start_date = '2020-01-01';
    for ($i = 0; $i < $n; $i++) {
        $date = date('Y-m-d', strtotime("$start_date + $i days"));
        $data[$date] = rand(10, 100);
    }
    return $data;
}

function testMediaMovil() {
    echo "--- Testing Media Movil ---\n";
    $dataset = generateLargeDataset(100);
    $period = 10;

    $result = Predictor::mediaMovil($dataset, $period);

    if (count($result) !== 100) {
        echo "❌ Result count mismatch: " . count($result) . "\n";
    } else {
        echo "✅ Result count correct\n";
    }

    // Verify a specific value manually
    $values = array_values($dataset);
    $manual_sum = 0;
    for ($i = 0; $i < $period; $i++) {
        $manual_sum += $values[10 - 1 - $i];
    }
    $manual_avg = round($manual_sum / $period, 2);

    if ($result[$period - 1] == $manual_avg) {
        echo "✅ Calculation correct at index " . ($period - 1) . ": $manual_avg\n";
    } else {
        echo "❌ Calculation mismatch at index " . ($period - 1) . ": Expected $manual_avg, Got " . $result[$period - 1] . "\n";
    }
}

function testRegresionLineal() {
    echo "\n--- Testing Regresion Lineal ---\n";
    $dataset = generateLargeDataset(10);
    $result = Predictor::regresionLineal($dataset, 7);

    if (!empty($result['proyecciones'])) {
        echo "✅ Regression generated " . count($result['proyecciones']) . " proyecciones\n";
        echo "✅ Trend: " . $result['tendencia'] . "\n";
    } else {
        echo "❌ Regression failed to generate results\n";
    }
}

function benchmark() {
    echo "\n--- Benchmarking ---\n";
    $large_n = 5000;
    $period = 1000;
    $dataset = generateLargeDataset($large_n);

    $start = microtime(true);
    Predictor::mediaMovil($dataset, $period);
    $time = microtime(true) - $start;
    echo "Media Movil ($large_n points, period $period): " . number_format($time, 4) . "s\n";

    $start = microtime(true);
    Predictor::regresionLineal($dataset, 30);
    $time = microtime(true) - $start;
    echo "Regresion Lineal ($large_n points): " . number_format($time, 4) . "s\n";
}

testMediaMovil();
testRegresionLineal();
benchmark();
