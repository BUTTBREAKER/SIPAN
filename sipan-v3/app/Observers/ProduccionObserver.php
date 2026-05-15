<?php

namespace App\Observers;

use App\Models\Produccion;
use App\Models\Insumo;

class ProduccionObserver
{
    /**
     * Cuando se completa una producción, descontamos los insumos
     * de acuerdo a la receta seleccionada y la cantidad producida.
     */
    public function created(Produccion $produccion): void
    {
        if ($produccion->estado !== 'completada' || !$produccion->id_receta) {
            return;
        }

        $this->descontarInsumos($produccion);
        $this->aumentarStockProducto($produccion);
    }

    /**
     * Al actualizar, si el estado cambia a completada, procesar insumos.
     */
    public function updated(Produccion $produccion): void
    {
        if ($produccion->isDirty('estado') && $produccion->estado === 'completada' && $produccion->id_receta) {
            $this->descontarInsumos($produccion);
            $this->aumentarStockProducto($produccion);
        }
    }

    /**
     * Descontar insumos según la receta × cantidad producida / rendimiento.
     */
    private function descontarInsumos(Produccion $produccion): void
    {
        $receta = $produccion->receta;
        if (!$receta) {
            return;
        }

        $factor = $receta->rendimiento > 0
            ? $produccion->cantidad_producida / $receta->rendimiento
            : $produccion->cantidad_producida;

        foreach ($receta->insumos as $recetaInsumo) {
            $cantidadNecesaria = $recetaInsumo->cantidad * $factor;

            $insumo = Insumo::find($recetaInsumo->id_insumo);
            if ($insumo) {
                $insumo->decrement('stock_actual', $cantidadNecesaria);

                // Registrar en tabla produccion_insumos
                $produccion->insumosUtilizados()->create([
                    'id_insumo'          => $insumo->id,
                    'cantidad_utilizada'  => $cantidadNecesaria,
                ]);
            }
        }
    }

    /**
     * Aumentar el stock del producto terminado.
     */
    private function aumentarStockProducto(Produccion $produccion): void
    {
        $producto = $produccion->producto;
        if ($producto) {
            $producto->increment('stock_actual', $produccion->cantidad_producida);
        }
    }
}
