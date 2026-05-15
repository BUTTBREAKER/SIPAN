<?php

namespace App\Filament\Widgets;

use App\Models\Insumo;
use App\Models\Venta;
use App\Models\Produccion;
use App\Models\Compra;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class DashboardStatsWidget extends BaseWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $hoy = now()->toDateString();

        // Ventas del día
        $ventasHoy = Venta::whereDate('created_at', $hoy)->sum('total_usd');
        $ventasCount = Venta::whereDate('created_at', $hoy)->count();

        // Insumos con stock bajo
        $stockBajoCount = Insumo::stockBajo()->count();

        // Producciones del día
        $produccionesHoy = Produccion::whereDate('fecha_produccion', $hoy)->count();

        // Compras pendientes
        $comprasPendientes = Compra::where('estado', 'pendiente')->count();

        return [
            Stat::make('Ventas Hoy', '$' . number_format($ventasHoy, 2))
                ->description($ventasCount . ' transacciones')
                ->descriptionIcon('heroicon-m-banknotes')
                ->color('success')
                ->chart([7, 3, 4, 5, 6, 3, 5]),

            Stat::make('Stock Bajo', $stockBajoCount . ' insumos')
                ->description('Por debajo del mínimo')
                ->descriptionIcon('heroicon-m-exclamation-triangle')
                ->color($stockBajoCount > 0 ? 'danger' : 'success'),

            Stat::make('Producción Hoy', $produccionesHoy . ' lotes')
                ->description('Órdenes completadas')
                ->descriptionIcon('heroicon-m-cog-6-tooth')
                ->color('info'),

            Stat::make('Compras Pendientes', $comprasPendientes)
                ->description('Requieren atención')
                ->descriptionIcon('heroicon-m-shopping-cart')
                ->color($comprasPendientes > 0 ? 'warning' : 'success'),
        ];
    }
}
