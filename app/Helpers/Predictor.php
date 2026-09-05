<?php

declare(strict_types=1);

namespace App\Helpers;

use InvalidArgumentException;

final class Predictor
{
    /**
     * Calcula la regresión lineal simple (y = mx + b)
     * Optimización Bolt: Refactorizado a algoritmo O(N) de una sola pasada y uso de fórmulas de series aritméticas
     * para evitar creación de arrays intermedios con range() y reducir consumo de memoria.
     *
     * @param array<string, int|float> $datos Array de valores historicos [fecha => cantidad]
     * @param int $dias_a_proyectar Número de días futuros a predecir
     * @return array{}|array{
     *   pendiente: int|float,
     *   interseccion: int|float,
     *   proyecciones: list<array{fecha: string, valor: float, tipo: 'prediccion'}>,
     *   tendencia: 'creciente'|'decreciente'|'estable',
     * } Array con las proyecciones futuras
     */
    public static function regresionLineal(array $datos, int $dias_a_proyectar = 7): array
    {
        $n = count($datos);

        if ($n < 2) {
            return []; // No hay suficientes datos
        }

        /**
         * Bolt Optimization: Replace O(N) range() and array_sum() with mathematical formulas
         * and a single O(N) pass for sumY and sumXY.
         */
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
            $message = "Error al calcular fecha futura";

            $fecha_futura = date(
                'Y-m-d',
                strtotime("$ultima_fecha + $i days") ?: throw new InvalidArgumentException($message),
            );

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
     * @param array<string, int> $datos Array de valores historicos [fecha => cantidad]
     * @param int $periodo Ventana de tiempo para el promedio
     * @return list<null|float>
     */
    public static function mediaMovil(array $datos, int $periodo = 3): array
    {
        $resultado = [];
        $valores = array_values($datos);
        $count = count($valores);

        /**
         * Bolt Optimization: Use a sliding window approach for SMA.
         * Reduces complexity from O(N*P) to O(N) by maintaining a running sum.
         */
        $suma = 0;
        for ($i = 0; $i < $count; $i++) {
            $suma += $valores[$i];

            if ($i < $periodo - 1) {
                // No hay suficientes datos anteriores
                $resultado[] = null;
                continue;
            }

            if ($i >= $periodo) {
                // Subtract the value that is falling out of the window
                $suma -= $valores[$i - $periodo];
            }

            // Cálculo O(1) usando suma deslizante
            $resultado[] = round($suma / $periodo, 2);
        }

        return $resultado;
    }
}
