<?php

class SIPAN
{
    public static function formatMoney($amount)
    {
        return '$ ' . number_format($amount, 2);
    }

    public static function formatDateTime($datetime)
    {
        if (!$datetime) {
            return '---';
        }

        $date = strtotime($datetime);
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

    public static function formatDate($datetime)
    {
        if (!$datetime) {
            return '---';
        }

        $date = strtotime($datetime);
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
