<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Forms\Components;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Sucursal;
use App\Models\Producto;
use App\Services\PredictionService;

class Predicciones extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-presentation-chart-line';
    protected static ?string $navigationLabel = 'Predicciones';
    protected static ?string $title = 'Análisis y Proyección de Demanda';
    protected static ?string $navigationGroup = 'Administración';
    protected static ?int $navigationSort = 8;

    protected static string $view = 'filament.pages.predicciones';

    public ?array $filtroData = [];
    public array $resultados = [];
    public array $chartData = [];

    public function mount(): void
    {
        $this->filtroForm->fill([
            'id_sucursal' => Auth::user()->id_sucursal ?? 1,
            'id_producto' => null,
            'dias_historicos' => 30,
            'dias_proyeccion' => 7,
        ]);
    }

    protected function getForms(): array
    {
        return [
            'filtroForm',
        ];
    }

    public function filtroForm(Form $form): Form
    {
        return $form
            ->schema([
                Components\Select::make('id_sucursal')
                    ->label('Sucursal')
                    ->options(Sucursal::query()->pluck('nombre', 'id'))
                    ->disabled(fn () => !Auth::user()->hasRole('superadmin'))
                    ->required()
                    ->live(),
                Components\Select::make('id_producto')
                    ->label('Producto a Analizar')
                    ->options(function (callable $get) {
                        $sucursal = $get('id_sucursal');
                        if (!$sucursal) return [];
                        return Producto::where('id_sucursal', $sucursal)
                            ->where('activo', true)
                            ->pluck('nombre', 'id');
                    })
                    ->searchable()
                    ->required(),
                Components\Select::make('dias_historicos')
                    ->label('Datos Históricos')
                    ->options([
                        15 => 'Últimos 15 días',
                        30 => 'Últimos 30 días',
                        60 => 'Últimos 60 días',
                        90 => 'Últimos 90 días',
                    ])
                    ->required(),
                Components\Select::make('dias_proyeccion')
                    ->label('Días a Proyectar')
                    ->options([
                        7 => 'Próxima Semana (7 días)',
                        15 => 'Próxima Quincena (15 días)',
                        30 => 'Próximo Mes (30 días)',
                    ])
                    ->required(),
            ])
            ->columns(4)
            ->statePath('filtroData');
    }

    public function calcularPrediccion()
    {
        $data = $this->filtroForm->getState();
        
        // Obtener datos históricos de ventas diarias
        $fechaInicio = now()->subDays($data['dias_historicos'])->format('Y-m-d');
        
        $ventasRaw = DB::table('venta_productos')
            ->join('ventas', 'venta_productos.id_venta', '=', 'ventas.id')
            ->where('ventas.id_sucursal', $data['id_sucursal'])
            ->where('venta_productos.id_producto', $data['id_producto'])
            ->where('ventas.estado', 'completada')
            ->where('ventas.fecha_venta', '>=', $fechaInicio . ' 00:00:00')
            ->select(DB::raw('DATE(ventas.fecha_venta) as fecha'), DB::raw('SUM(venta_productos.cantidad) as total_vendido'))
            ->groupBy(DB::raw('DATE(ventas.fecha_venta)'))
            ->orderBy('fecha', 'asc')
            ->get()
            ->pluck('total_vendido', 'fecha')
            ->toArray();

        // Rellenar días sin ventas con 0
        $datosCompletos = [];
        $period = new \DatePeriod(
            new \DateTime($fechaInicio),
            new \DateInterval('P1D'),
            (new \DateTime())->modify('+1 day')
        );

        foreach ($period as $date) {
            $fechaStr = $date->format('Y-m-d');
            $datosCompletos[$fechaStr] = $ventasRaw[$fechaStr] ?? 0;
        }

        $predictionService = new PredictionService();
        
        // Calcular predicción
        $this->resultados = $predictionService->regresionLineal($datosCompletos, $data['dias_proyeccion']);
        $sma = $predictionService->mediaMovil($datosCompletos, 7); // SMA de 7 días

        // Preparar datos para el gráfico
        $labels = [];
        $historico = [];
        $tendenciaSMA = [];
        $proyeccion = [];

        foreach ($datosCompletos as $f => $v) {
            $labels[] = date('d/m', strtotime($f));
            $historico[] = $v;
            $tendenciaSMA[] = $sma[$f];
            $proyeccion[] = null; // No hay proyección en el pasado
        }

        // Conectar la línea de proyección con el último punto real
        if (!empty($historico)) {
            $ultimoIdx = count($historico) - 1;
            $proyeccion[$ultimoIdx] = $historico[$ultimoIdx];
        }

        foreach ($this->resultados['proyecciones'] as $f => $v) {
            $labels[] = date('d/m', strtotime($f));
            $historico[] = null;
            $tendenciaSMA[] = null;
            $proyeccion[] = $v;
        }

        $productoNombre = Producto::find($data['id_producto'])->nombre;

        $this->chartData = [
            'labels' => $labels,
            'datasets' => [
                [
                    'label' => 'Ventas Históricas',
                    'data' => $historico,
                    'borderColor' => 'rgb(59, 130, 246)', // Blue
                    'backgroundColor' => 'rgba(59, 130, 246, 0.1)',
                    'tension' => 0.1,
                    'fill' => true
                ],
                [
                    'label' => 'Media Móvil (7d)',
                    'data' => $tendenciaSMA,
                    'borderColor' => 'rgb(245, 158, 11)', // Amber
                    'borderDash' => [5, 5],
                    'tension' => 0.4,
                    'fill' => false
                ],
                [
                    'label' => 'Proyección Esperada',
                    'data' => $proyeccion,
                    'borderColor' => 'rgb(16, 185, 129)', // Green
                    'backgroundColor' => 'rgba(16, 185, 129, 0.1)',
                    'borderDash' => [5, 5],
                    'tension' => 0.1,
                    'fill' => true
                ]
            ],
            'producto' => $productoNombre
        ];

        // Dispatch browser event to render chart
        $this->dispatch('update-prediction-chart', data: $this->chartData);
    }
}
