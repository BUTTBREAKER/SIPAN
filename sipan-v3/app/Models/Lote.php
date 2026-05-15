<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Lote extends Model
{
    protected $table = 'lotes';

    protected $fillable = [
        'id_sucursal',
        'tipo',
        'id_item',
        'codigo_lote',
        'fecha_entrada',
        'fecha_vencimiento',
        'cantidad_inicial',
        'cantidad_actual',
        'costo_unitario',
        'estado',
    ];

    protected $casts = [
        'cantidad_inicial'  => 'decimal:4',
        'cantidad_actual'   => 'decimal:4',
        'costo_unitario'    => 'decimal:2',
        'fecha_entrada'     => 'date',
        'fecha_vencimiento' => 'date',
    ];

    // ─── Relaciones ───────────────────────────────────────────────

    public function sucursal(): BelongsTo
    {
        return $this->belongsTo(Sucursal::class, 'id_sucursal');
    }

    // ─── Scopes ───────────────────────────────────────────────────

    /** Lotes activos con stock disponible */
    public function scopeActivos($query)
    {
        return $query->where('estado', 'activo')->where('cantidad_actual', '>', 0);
    }

    /** Lotes por vencer en los próximos $dias días */
    public function scopePorVencer($query, int $dias = 30)
    {
        return $query->activos()
            ->whereNotNull('fecha_vencimiento')
            ->whereBetween('fecha_vencimiento', [now(), now()->addDays($dias)]);
    }

    // ─── Helpers ──────────────────────────────────────────────────

    /** Nombre del ítem asociado (insumo o producto) */
    public function getNombreItemAttribute(): ?string
    {
        if ($this->tipo === 'insumo') {
            return Insumo::find($this->id_item)?->nombre;
        }
        return Producto::find($this->id_item)?->nombre;
    }
}
