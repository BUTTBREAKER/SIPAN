<?php

namespace App\Filament\Pages;

use App\Models\Producto;
use App\Models\Venta;
use App\Models\VentaProducto;
use App\Models\Cliente;
use App\Services\BcvService;
use Filament\Pages\Page;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class POS extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-calculator';
    protected static ?string $navigationLabel = 'Punto de Venta';
    protected static ?string $title = 'Punto de Venta';
    protected static ?string $navigationGroup = 'Ventas';
    protected static ?int $navigationSort = 1;

    protected static string $view = 'filament.pages.p-o-s';

    public $search = '';
    public $cart = [];
    public $tasa_bcv = 1.0;
    public $id_cliente = null;
    public $metodo_pago = 'efectivo';
    public $notas = '';

    public function mount()
    {
        $bcvService = app(BcvService::class);
        $this->tasa_bcv = $bcvService->getTasa();
    }

    public function addToCart($id)
    {
        $producto = Producto::find($id);

        if (!$producto) return;

        if (isset($this->cart[$id])) {
            $this->cart[$id]['cantidad']++;
        } else {
            $this->cart[$id] = [
                'id' => $producto->id,
                'nombre' => $producto->nombre,
                'precio_venta' => (float)$producto->precio_venta,
                'cantidad' => 1,
            ];
        }

        $this->dispatch('cart-updated');
    }

    public function removeFromCart($id)
    {
        unset($this->cart[$id]);
        $this->dispatch('cart-updated');
    }

    public function updateQuantity($id, $cantidad)
    {
        if ($cantidad <= 0) {
            $this->removeFromCart($id);
            return;
        }

        if (isset($this->cart[$id])) {
            $this->cart[$id]['cantidad'] = $cantidad;
        }
    }

    public function getTotalProperty()
    {
        return collect($this->cart)->sum(fn($item) => $item['precio_venta'] * $item['cantidad']);
    }

    public function getTotalVesProperty()
    {
        return round($this->total * $this->tasa_bcv, 2);
    }

    public function processSale()
    {
        if (empty($this->cart)) {
            Notification::make()
                ->title('El carrito está vacío')
                ->danger()
                ->send();
            return;
        }

        try {
            DB::beginTransaction();

            $venta = Venta::create([
                'id_sucursal' => Auth::user()->id_sucursal ?? 1, // Fallback to 1
                'id_usuario' => Auth::id(),
                'id_cliente' => $this->id_cliente,
                'total' => $this->total,
                'total_usd' => $this->total,
                'total_ves' => $this->total_ves,
                'tasa_bcv' => $this->tasa_bcv,
                'metodo_pago' => $this->metodo_pago,
                'estado' => 'completada',
                'notas' => $this->notas,
                'fecha_venta' => now(),
            ]);

            foreach ($this->cart as $item) {
                VentaProducto::create([
                    'id_venta' => $venta->id,
                    'id_producto' => $item['id'],
                    'cantidad' => $item['cantidad'],
                    'precio_unitario' => $item['precio_venta'],
                    'precio_unitario_usd' => $item['precio_venta'],
                    'precio_unitario_ves' => round($item['precio_venta'] * $this->tasa_bcv, 2),
                    'subtotal' => $item['precio_venta'] * $item['cantidad'],
                    'subtotal_usd' => $item['precio_venta'] * $item['cantidad'],
                    'subtotal_ves' => round($item['precio_venta'] * $item['cantidad'] * $this->tasa_bcv, 2),
                ]);

                // Update stock
                $producto = Producto::find($item['id']);
                $producto->decrement('stock_actual', $item['cantidad']);
            }

            DB::commit();

            $this->reset(['cart', 'id_cliente', 'metodo_pago', 'notas']);

            Notification::make()
                ->title('Venta procesada con éxito')
                ->success()
                ->send();

        } catch (\Exception $e) {
            DB::rollBack();
            Notification::make()
                ->title('Error al procesar la venta')
                ->body($e->getMessage())
                ->danger()
                ->send();
        }
    }

    public function getProductosProperty()
    {
        if (strlen($this->search) < 2) return [];

        return Producto::where('nombre', 'like', "%{$this->search}%")
            ->orWhere('codigo', 'like', "%{$this->search}%")
            ->where('activo', true)
            ->limit(10)
            ->get();
    }

    public function getClientesProperty()
    {
        return Cliente::all();
    }
}
