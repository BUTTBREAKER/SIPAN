<?php

namespace App\Helpers;

class Predictor
{
    /**
     * Calcula la regresión lineal simple (y = mx + b)
     *
     * @param array $datos Array de valores historicos [fecha => cantidad]
     * @param int $dias_a_proyectar Número de días futuros a predecir
     * @return array Array con las proyecciones futuras
     */
    public static function regresionLineal($datos, $dias_a_proyectar = 7)
    {
        $n = count($datos);

        if ($n < 2) {
            return []; // No hay suficientes datos
        }

        // Bolt Optimization: Mathematical calculation of sums to avoid range() and O(N) overhead for X
        // sumX = n * (n + 1) / 2
        // sumXX = n * (n + 1) * (2n + 1) / 6
        $sumX = ($n * ($n + 1)) / 2;
        $sumXX = ($n * ($n + 1) * (2 * $n + 1)) / 6;

        $sumY = 0;
        $sumXY = 0;

        $i = 1;
        foreach ($datos as $valor) {
            $sumY += $valor;
            $sumXY += ($i * $valor);
            $i++;
        }

        // Fórmulas de m (pendiente) y b (intersección)
        // m = (n * sumXY - sumX * sumY) / (n * sumXX - sumX * sumX)
        // b = (sumY - m * sumX) / n

        $divisor = ($n * $sumXX) - ($sumX * $sumX);

        if ($divisor == 0) {
            return []; // Evitar división por cero
        }

        $m = (($n * $sumXY) - ($sumX * $sumY)) / $divisor;
        $b = ($sumY - ($m * $sumX)) / $n;

        // Proyectar futuro
        $proyecciones = [];
        $ultima_fecha = array_key_last($datos);

        for ($i = 1; $i <= $dias_a_proyectar; $i++) {
            $nuevo_x = $n + $i;
            $prediccion = ($m * $nuevo_x) + $b;

            // No permitir valores negativos
            $prediccion = max(0, $prediccion);

            $fecha_futura = date('Y-m-d', strtotime("$ultima_fecha + $i days"));
            $proyecciones[] = [
                'fecha' => $fecha_futura,
                'valor' => round($prediccion, 2),
                'tipo' => 'prediccion'
            ];
        }

        return [
            'pendiente' => $m,
            'interseccion' => $b,
            'proyecciones' => $proyecciones,
            'tendencia' => $m > 0 ? 'creciente' : ($m < 0 ? 'decreciente' : 'estable')
        ];
    }

    /**
     * Calcula la Media Móvil Simple (SMA)
     * Útil para suavizar la curva de demanda
     */
    public static function mediaMovil($datos, $periodo = 3)
    {
        $resultado = [];
        $valores = array_values($datos);
        $count = count($valores);

        if ($count === 0) {
            return [];
        }

        // Bolt Optimization: Sliding window approach to reduce complexity from O(N*P) to O(N)
        $runningSum = 0;
        for ($i = 0; $i < $count; $i++) {
            $runningSum += $valores[$i];

            if ($i < $periodo - 1) {
                $resultado[] = null;
                continue;
            }

            if ($i >= $periodo) {
                $runningSum -= $valores[$i - $periodo];
            }

            $resultado[] = round($runningSum / $periodo, 2);
        }

        return $resultado;
    }
}
