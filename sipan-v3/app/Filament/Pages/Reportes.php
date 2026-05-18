<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Forms\Components;
use Illuminate\Support\Facades\Auth;
use App\Models\Sucursal;
use App\Models\Venta;
use App\Models\Producto;
use App\Models\Insumo;
use App\Models\Caja;
use Barryvdh\DomPDF\Facade\Pdf;

class Reportes extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-document-chart-bar';
    protected static ?string $navigationLabel = 'Reportes';
    protected static ?string $title = 'Reportes del Sistema';
    protected static ?string $navigationGroup = 'Administración';
    protected static ?int $navigationSort = 9;

    protected static string $view = 'filament.pages.reportes';

    public ?array $ventasData = [];
    public ?array $inventarioData = [];
    public ?array $cajasData = [];

    public function mount(): void
    {
        $this->ventasForm->fill([
            'id_sucursal' => Auth::user()->id_sucursal ?? 1,
            'fecha_inicio' => now()->startOfMonth()->format('Y-m-d'),
            'fecha_fin' => now()->format('Y-m-d'),
        ]);

        $this->inventarioForm->fill([
            'id_sucursal' => Auth::user()->id_sucursal ?? 1,
            'tipo' => 'productos',
        ]);

        $this->cajasForm->fill([
            'id_sucursal' => Auth::user()->id_sucursal ?? 1,
            'fecha_inicio' => now()->startOfWeek()->format('Y-m-d'),
            'fecha_fin' => now()->format('Y-m-d'),
        ]);
    }

    protected function getForms(): array
    {
        return [
            'ventasForm',
            'inventarioForm',
            'cajasForm',
        ];
    }

    public function ventasForm(Form $form): Form
    {
        return $form
            ->schema([
                Components\Select::make('id_sucursal')
                    ->label('Sucursal')
                    ->options(Sucursal::query()->pluck('nombre', 'id'))
                    ->disabled(fn () => !Auth::user()->hasRole('superadmin'))
                    ->required(),
                Components\DatePicker::make('fecha_inicio')
                    ->label('Desde')
                    ->required(),
                Components\DatePicker::make('fecha_fin')
                    ->label('Hasta')
                    ->required(),
            ])
            ->statePath('ventasData');
    }

    public function inventarioForm(Form $form): Form
    {
        return $form
            ->schema([
                Components\Select::make('id_sucursal')
                    ->label('Sucursal')
                    ->options(Sucursal::query()->pluck('nombre', 'id'))
                    ->disabled(fn () => !Auth::user()->hasRole('superadmin'))
                    ->required(),
                Components\Select::make('tipo')
                    ->label('Tipo de Inventario')
                    ->options([
                        'productos' => 'Productos Finales',
                        'insumos' => 'Insumos / Materia Prima',
                    ])
                    ->required(),
            ])
            ->statePath('inventarioData');
    }

    public function cajasForm(Form $form): Form
    {
        return $form
            ->schema([
                Components\Select::make('id_sucursal')
                    ->label('Sucursal')
                    ->options(Sucursal::query()->pluck('nombre', 'id'))
                    ->disabled(fn () => !Auth::user()->hasRole('superadmin'))
                    ->required(),
                Components\DatePicker::make('fecha_inicio')
                    ->label('Desde')
                    ->required(),
                Components\DatePicker::make('fecha_fin')
                    ->label('Hasta')
                    ->required(),
            ])
            ->statePath('cajasData');
    }

    public function generarReporteVentas()
    {
        $data = $this->ventasForm->getState();
        $sucursal = Sucursal::find($data['id_sucursal']);
        
        $ventas = Venta::with(['cliente', 'usuario'])
            ->where('id_sucursal', $data['id_sucursal'])
            ->whereBetween('fecha_venta', [$data['fecha_inicio'] . ' 00:00:00', $data['fecha_fin'] . ' 23:59:59'])
            ->where('estado', 'completada')
            ->orderBy('fecha_venta', 'asc')
            ->get();

        $pdf = Pdf::loadView('reportes.ventas', [
            'ventas' => $ventas,
            'fecha_inicio' => $data['fecha_inicio'],
            'fecha_fin' => $data['fecha_fin'],
            'sucursal' => $sucursal,
            'total_usd' => $ventas->sum('total_usd'),
            'total_ves' => $ventas->sum('total_ves'),
        ]);

        return response()->streamDownload(fn () => print($pdf->output()), "reporte_ventas_{$data['fecha_inicio']}.pdf");
    }

    public function generarReporteInventario()
    {
        $data = $this->inventarioForm->getState();
        $sucursal = Sucursal::find($data['id_sucursal']);
        
        if ($data['tipo'] === 'productos') {
            $items = Producto::where('id_sucursal', $data['id_sucursal'])
                ->where('activo', true)
                ->orderBy('nombre')
                ->get();
            $vista = 'reportes.inventario_productos';
        } else {
            $items = Insumo::where('id_sucursal', $data['id_sucursal'])
                ->where('activo', true)
                ->orderBy('nombre')
                ->get();
            $vista = 'reportes.inventario_insumos';
        }

        $pdf = Pdf::loadView($vista, [
            'items' => $items,
            'sucursal' => $sucursal,
            'fecha' => now()->format('Y-m-d H:i'),
        ]);

        return response()->streamDownload(fn () => print($pdf->output()), "reporte_inventario_{$data['tipo']}.pdf");
    }

    public function generarReporteCajas()
    {
        $data = $this->cajasForm->getState();
        $sucursal = Sucursal::find($data['id_sucursal']);
        
        $cajas = Caja::with(['usuarioApertura', 'usuarioCierre'])
            ->where('id_sucursal', $data['id_sucursal'])
            ->whereBetween('fecha_apertura', [$data['fecha_inicio'] . ' 00:00:00', $data['fecha_fin'] . ' 23:59:59'])
            ->orderBy('fecha_apertura', 'asc')
            ->get();

        $pdf = Pdf::loadView('reportes.cajas', [
            'cajas' => $cajas,
            'fecha_inicio' => $data['fecha_inicio'],
            'fecha_fin' => $data['fecha_fin'],
            'sucursal' => $sucursal,
        ]);

        return response()->streamDownload(fn () => print($pdf->output()), "reporte_cajas_chicas.pdf");
    }
}
