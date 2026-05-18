<?php

namespace App\Services;

class PredictionService
{
    /**
     * Calcula la regresión lineal simple (y = mx + b)
     * Optimizado a algoritmo O(N) de una sola pasada.
     *
     * @param array $datos Array de valores historicos [fecha => cantidad]
     * @param int $dias_a_proyectar Número de días futuros a predecir
     * @return array Array con las proyecciones futuras
     */
    public function regresionLineal($datos, $dias_a_proyectar = 7)
    {
        $n = count($datos);

        if ($n < 2) {
            return [
                'pendiente' => 0,
                'interseccion' => 0,
                'proyecciones' => [],
                'tendencia' => 'insuficientes_datos'
            ];
        }

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

        $divisor = ($n * $sumXX) - ($sumX * $sumX);

        if ($divisor == 0) {
            return [
                'pendiente' => 0,
                'interseccion' => 0,
                'proyecciones' => [],
                'tendencia' => 'estable'
            ]; 
        }

        $m = (($n * $sumXY) - ($sumX * $sumY)) / $divisor;
        $b = ($sumY - ($m * $sumX)) / $n;

        $proyecciones = [];
        $ultima_fecha = array_key_last($datos);

        for ($i = 1; $i <= $dias_a_proyectar; $i++) {
            $nuevo_x = $n + $i;
            $prediccion = ($m * $nuevo_x) + $b;

            // No permitir ventas negativas
            $prediccion = max(0, $prediccion);

            $fecha_futura = date('Y-m-d', strtotime("$ultima_fecha + $i days"));
            $proyecciones[$fecha_futura] = round($prediccion, 2);
        }

        return [
            'pendiente' => round($m, 4),
            'interseccion' => round($b, 4),
            'proyecciones' => $proyecciones,
            'tendencia' => $m > 0.1 ? 'creciente' : ($m < -0.1 ? 'decreciente' : 'estable')
        ];
    }

    /**
     * Calcula la Media Móvil Simple (SMA) usando ventana deslizante O(N).
     *
     * @param array $datos Array de valores historicos [fecha => cantidad]
     * @param int $periodo Ventana de tiempo para el promedio
     * @return array
     */
    public function mediaMovil($datos, $periodo = 3)
    {
        $resultado = [];
        $valores = array_values($datos);
        $fechas = array_keys($datos);
        $count = count($valores);

        $suma = 0;
        for ($i = 0; $i < $count; $i++) {
            $suma += $valores[$i];

            if ($i >= $periodo) {
                // Restar el valor que sale de la ventana
                $suma -= $valores[$i - $periodo];
            }

            if ($i < $periodo - 1) {
                // No hay suficientes datos para el promedio de este día
                $resultado[$fechas[$i]] = null;
            } else {
                $resultado[$fechas[$i]] = round($suma / $periodo, 2);
            }
        }

        return $resultado;
    }
}
