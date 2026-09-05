<?php

declare(strict_types=1);

namespace App;

use InvalidArgumentException;

final class SIPAN
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

        $time = strtotime($datetime) ?: throw new InvalidArgumentException("Invalid datetime format: $datetime");
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

        return $days[date('w', $time)]
            . ', '
            . date('d', $time)
            . ' de '
            . $months[date('n', $time)]
            . ' - '
            . date('H:i', $time);
    }

    public static function formatDate(string $datetime): string
    {
        if (!$datetime) {
            return '---';
        }

        $time = strtotime($datetime) ?: throw new InvalidArgumentException("Invalid datetime format: $datetime");

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

        return date('d', $time) . ' de ' . $months[date('n', $time)] . ' de ' . date('Y', $time);
    }
}
