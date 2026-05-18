<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use App\Models\Caja;
use App\Models\CajaMovimiento;
use App\Services\BcvService;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CajaChica extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-banknotes';
    protected static ?string $navigationLabel = 'Caja Chica';
    protected static ?string $title = 'Control de Caja Chica';
    protected static ?string $navigationGroup = 'Finanzas';
    protected static ?int $navigationSort = 1;

    protected static string $view = 'filament.pages.caja-chica';

    public $cajaActiva;
    public $movimientos = [];
    public $tasa_bcv = 1.0;

    // Apertura
    public $monto_apertura_usd = 0;
    public $monto_apertura_bs = 0;
    
    // Cierre
    public $monto_cierre_usd = 0;
    public $monto_cierre_bs = 0;
    public $observaciones = '';

    // Nuevo Movimiento
    public $tipo_movimiento = 'ingreso';
    public $monto_movimiento = 0;
    public $metodo_pago_movimiento = 'efectivo_usd';
    public $descripcion_movimiento = '';

    public function mount()
    {
        $bcvService = app(BcvService::class);
        $this->tasa_bcv = $bcvService->getTasa();
        $this->cargarCajaActiva();
    }

    public function cargarCajaActiva()
    {
        $sucursal_id = Auth::user()->id_sucursal ?? 1;
        $this->cajaActiva = Caja::where('id_sucursal', $sucursal_id)
            ->where('estado', 'abierta')
            ->with(['movimientos' => function($q) {
                $q->orderBy('fecha', 'desc');
            }])
            ->first();

        if ($this->cajaActiva) {
            $this->movimientos = $this->cajaActiva->movimientos;
            
            // Calc expected closure based on initial + inputs - outputs
            $ingresos = $this->cajaActiva->movimientos->where('tipo', 'ingreso')->sum('monto');
            $egresos = $this->cajaActiva->movimientos->where('tipo', 'egreso')->sum('monto');
            $esperado = $this->cajaActiva->monto_apertura_usd + $ingresos - $egresos;
            
            $this->monto_cierre_usd = $esperado;
            $this->monto_cierre_bs = round($esperado * $this->tasa_bcv, 2);
        }
    }

    public function abrirCaja()
    {
        if ($this->cajaActiva) {
            Notification::make()->title('Ya existe una caja abierta.')->danger()->send();
            return;
        }

        try {
            DB::beginTransaction();

            $sucursal_id = Auth::user()->id_sucursal ?? 1;
            
            // Total in USD equivalent
            $total_usd = $this->monto_apertura_usd + ($this->monto_apertura_bs / $this->tasa_bcv);

            Caja::create([
                'id_sucursal' => $sucursal_id,
                'id_usuario_apertura' => Auth::id(),
                'monto_apertura' => $total_usd,
                'monto_apertura_usd' => $this->monto_apertura_usd,
                'monto_apertura_bs' => $this->monto_apertura_bs,
                'estado' => 'abierta',
                'fecha_apertura' => now(),
            ]);

            DB::commit();

            Notification::make()->title('Caja abierta exitosamente')->success()->send();
            $this->cargarCajaActiva();

        } catch (\Exception $e) {
            DB::rollBack();
            Notification::make()->title('Error al abrir caja')->body($e->getMessage())->danger()->send();
        }
    }

    public function cerrarCaja()
    {
        if (!$this->cajaActiva) {
            Notification::make()->title('No hay caja abierta.')->danger()->send();
            return;
        }

        try {
            DB::beginTransaction();

            $ingresos = $this->cajaActiva->movimientos->where('tipo', 'ingreso')->sum('monto');
            $egresos = $this->cajaActiva->movimientos->where('tipo', 'egreso')->sum('monto');
            $esperado = $this->cajaActiva->monto_apertura_usd + $ingresos - $egresos;
            $esperado_bs = round($esperado * $this->tasa_bcv, 2);
            
            $total_cierre_usd = $this->monto_cierre_usd + ($this->monto_cierre_bs / $this->tasa_bcv);

            $this->cajaActiva->update([
                'estado' => 'cerrada',
                'id_usuario_cierre' => Auth::id(),
                'monto_cierre' => $total_cierre_usd,
                'monto_cierre_usd' => $this->monto_cierre_usd,
                'monto_cierre_bs' => $this->monto_cierre_bs,
                'monto_esperado' => $esperado,
                'monto_esperado_usd' => $esperado,
                'monto_esperado_bs' => $esperado_bs,
                'fecha_cierre' => now(),
                'observaciones' => $this->observaciones,
            ]);

            DB::commit();

            Notification::make()->title('Caja cerrada exitosamente')->success()->send();
            
            $this->cajaActiva = null;
            $this->movimientos = [];
            $this->observaciones = '';

        } catch (\Exception $e) {
            DB::rollBack();
            Notification::make()->title('Error al cerrar caja')->body($e->getMessage())->danger()->send();
        }
    }

    public function registrarMovimiento()
    {
        if (!$this->cajaActiva) {
            Notification::make()->title('No hay caja abierta.')->danger()->send();
            return;
        }

        if ($this->monto_movimiento <= 0) {
            Notification::make()->title('El monto debe ser mayor a 0.')->danger()->send();
            return;
        }

        try {
            DB::beginTransaction();

            // All movements are recorded in USD equivalent for accounting
            $monto_final = $this->monto_movimiento;
            if (in_array($this->metodo_pago_movimiento, ['efectivo_ves', 'punto_venta', 'pago_movil'])) {
                $monto_final = round($this->monto_movimiento / $this->tasa_bcv, 2);
            }

            CajaMovimiento::create([
                'id_caja' => $this->cajaActiva->id,
                'tipo' => $this->tipo_movimiento,
                'monto' => $monto_final,
                'descripcion' => $this->descripcion_movimiento,
                'metodo_pago' => $this->metodo_pago_movimiento,
                'fecha' => now(),
            ]);

            DB::commit();

            Notification::make()->title('Movimiento registrado exitosamente')->success()->send();
            
            $this->monto_movimiento = 0;
            $this->descripcion_movimiento = '';
            $this->cargarCajaActiva();

        } catch (\Exception $e) {
            DB::rollBack();
            Notification::make()->title('Error al registrar movimiento')->body($e->getMessage())->danger()->send();
        }
    }
}
