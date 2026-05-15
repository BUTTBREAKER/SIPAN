<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Insumo extends Model
{
    use LogsActivity;

    protected $table = 'insumos';

    protected $fillable = [
        'nombre',
        'descripcion',
        'codigo',
        'unidad_medida',
        'stock_actual',
        'stock_minimo',
        'precio_unitario',
        'id_sucursal',
        'activo',
    ];

    protected $casts = [
        'stock_actual'    => 'decimal:4',
        'stock_minimo'    => 'decimal:4',
        'precio_unitario' => 'decimal:2',
        'activo'          => 'boolean',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logFillable()
            ->logOnlyDirty()
            ->setDescriptionForEvent(fn (string $eventName) => "Insumo {$eventName}");
    }

    // ─── Relaciones ───────────────────────────────────────────────

    public function sucursal(): BelongsTo
    {
        return $this->belongsTo(Sucursal::class, 'id_sucursal');
    }

    public function proveedores(): BelongsToMany
    {
        return $this->belongsToMany(Proveedor::class, 'proveedor_insumos', 'id_insumo', 'id_proveedor')
                    ->withPivot(['precio', 'tiempo_entrega'])
                    ->withTimestamps();
    }

    public function recetaItems(): HasMany
    {
        return $this->hasMany(RecetaInsumo::class, 'id_insumo');
    }

    // ─── Scopes ───────────────────────────────────────────────────

    /** Insumos con stock por debajo del mínimo */
    public function scopeStockBajo($query)
    {
        return $query->whereColumn('stock_actual', '<=', 'stock_minimo')
                     ->where('stock_minimo', '>', 0);
    }

    /** Filtrar por sucursal */
    public function scopeSucursal($query, int $sucursalId)
    {
        return $query->where('id_sucursal', $sucursalId);
    }
}
