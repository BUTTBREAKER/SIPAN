<?php

namespace App;

use InvalidArgumentException;

class SIPAN
{
    public static function formatMoney(float $amount): string
    {
        return '$ ' . number_format($amount, 2);
    }

    public static function formatDateTime(string $datetime): string
    {
        if (!$datetime) {
            return '---';
        }

        $date = strtotime($datetime) ?: throw new InvalidArgumentException("Invalid datetime format: $datetime");
        $days = ['Domingo', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado'];
        $months = [
            '',
            'Enero',
            'Febrero',
            'Marzo',
            'Abril',
            'Mayo',
            'Junio',
            'Julio',
            'Agosto',
            'Septiembre',
            'Octubre',
            'Noviembre',
            'Diciembre'
        ];

        return $days[date('w', $date)]
            . ', '
            . date('d', $date)
            . ' de '
            . $months[date('n', $date)]
            . ' - '
            . date('H:i', $date);
    }

    public static function formatDate(string $datetime): string
    {
        if (!$datetime) {
            return '---';
        }

        $date = strtotime($datetime) ?: throw new InvalidArgumentException("Invalid datetime format: $datetime");
        $months = [
            '',
            'Enero',
            'Febrero',
            'Marzo',
            'Abril',
            'Mayo',
            'Junio',
            'Julio',
            'Agosto',
            'Septiembre',
            'Octubre',
            'Noviembre',
            'Diciembre'
        ];

        return date('d', $date) . ' de ' . $months[date('n', $date)] . ' de ' . date('Y', $date);
    }
}
