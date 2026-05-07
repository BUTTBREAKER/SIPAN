<?php

namespace App\Helpers;

class Predictor
{
    /**
     * Calcula la regresión lineal simple (y = mx + b)
     * Optimización Bolt: Refactorizado a algoritmo O(N) de una sola pasada y uso de fórmulas de series aritméticas
     * para evitar creación de arrays intermedios con range() y reducir consumo de memoria.
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

        $sumY = 0;
        $sumXY = 0;

        // Cálculo matemático O(1) para la suma de series aritméticas: sum(1..n) y sum(1^2..n^2)
        // sum(i) = n(n+1)/2
        // sum(i^2) = n(n+1)(2n+1)/6
        $sumX = ($n * ($n + 1)) / 2;
        $sumXX = ($n * ($n + 1) * (2 * $n + 1)) / 6;

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
     * Optimización Bolt: Implementado algoritmo de ventana deslizante para reducir complejidad de O(N*P) a O(N).
     *
     * @param array $datos Array de valores historicos [fecha => cantidad]
     * @param int $periodo Ventana de tiempo para el promedio
     * @return array
     */
    public static function mediaMovil($datos, $periodo = 3)
    {
        $resultado = [];
        $valores = array_values($datos);
        $count = count($valores);

        $sumaVentana = 0;

        for ($i = 0; $i < $count; $i++) {
            $sumaVentana += $valores[$i];

            if ($i >= $periodo) {
                // Restar el valor que sale de la ventana
                $sumaVentana -= $valores[$i - $periodo];
            }

            if ($i < $periodo - 1) {
                // No hay suficientes datos para completar el primer periodo
                $resultado[] = null;
            } else {
                // Cálculo O(1) usando suma deslizante
                $resultado[] = round($sumaVentana / $periodo, 2);
            }
        }

        return $resultado;
    }
}
