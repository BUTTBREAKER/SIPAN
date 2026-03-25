<?php

/**
 * Test: Dashboard Sales Aggregation Logic Verification
 * This script verifies that the in-memory aggregation logic correctly calculates
 * totals for today, the last 7 days, and the current month from a daily dataset.
 */

function verify_aggregation($ventas_data) {
    $ventas_hoy = 0;
    $ventas_semana = 0;
    $ventas_mes = 0;
    $hoy = date('Y-m-d');
    $hace_7_dias = date('Y-m-d', strtotime('-7 days'));
    $primer_dia_mes = date('Y-m-01');

    foreach ($ventas_data as $v) {
        $fecha = $v['fecha_full'];
        $total = $v['total'];

        if ($fecha === $hoy) {
            $ventas_hoy = $total;
        }
        if ($fecha >= $hace_7_dias) {
            $ventas_semana += $total;
        }
        if ($fecha >= $primer_dia_mes) {
            $ventas_mes += $total;
        }
    }

    return [
        'hoy' => $ventas_hoy,
        'semana' => $ventas_semana,
        'mes' => $ventas_mes
    ];
}

// Mock Data
$hoy = date('Y-m-d');
$ayer = date('Y-m-d', strtotime('-1 day'));
$hace_6_dias = date('Y-m-d', strtotime('-6 days'));
$hace_7_dias = date('Y-m-d', strtotime('-7 days'));
$hace_8_dias = date('Y-m-d', strtotime('-8 days'));
$primer_dia_mes = date('Y-m-01');
$hace_un_mes = date('Y-m-d', strtotime('-1 month'));

$mock_ventas = [
    ['fecha_full' => $hoy, 'total' => 100],
    ['fecha_full' => $ayer, 'total' => 50],
    ['fecha_full' => $hace_6_dias, 'total' => 30],
    ['fecha_full' => $hace_7_dias, 'total' => 20],
    ['fecha_full' => $hace_8_dias, 'total' => 10],
];

// If today is 1st of month, adjust test data to ensure we test monthly logic
if ($hoy === $primer_dia_mes) {
    // Already covered by $hoy
} else {
    // Add a record from earlier this month if possible
    $mock_ventas[] = ['fecha_full' => $primer_dia_mes, 'total' => 200];
}

$results = verify_aggregation($mock_ventas);

echo "--- Dashboard Aggregation Test ---\n";
echo "Today's Total: expected 100, got {$results['hoy']}\n";

$expected_semana = 100 + 50 + 30 + 20;
echo "Weekly Total (last 7 days + today): expected $expected_semana, got {$results['semana']}\n";

$expected_mes = 0;
foreach ($mock_ventas as $v) {
    if ($v['fecha_full'] >= $primer_dia_mes) $expected_mes += $v['total'];
}
echo "Monthly Total: expected $expected_mes, got {$results['mes']}\n";

if ($results['hoy'] === 100 && $results['semana'] === $expected_semana && $results['mes'] === $expected_mes) {
    echo "SUCCESS: Aggregation logic is correct.\n";
    exit(0);
} else {
    echo "FAILURE: Aggregation logic mismatch.\n";
    exit(1);
}
