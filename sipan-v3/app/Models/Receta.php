<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Receta extends Model
{
    use LogsActivity;

    protected $table = 'recetas';

    protected $fillable = [
        'nombre',
        'id_producto',
        'id_sucursal',
        'rendimiento',
        'instrucciones',
    ];

    protected $casts = [
        'rendimiento' => 'decimal:4',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()->logFillable()->logOnlyDirty();
    }

    // ─── Relaciones ───────────────────────────────────────────────

    public function producto(): BelongsTo
    {
        return $this->belongsTo(Producto::class, 'id_producto');
    }

    public function sucursal(): BelongsTo
    {
        return $this->belongsTo(Sucursal::class, 'id_sucursal');
    }

    /** Detalles/insumos de la receta (tabla: receta_insumos) */
    public function insumos(): HasMany
    {
        return $this->hasMany(RecetaInsumo::class, 'id_receta');
    }

    // ─── Accessors ────────────────────────────────────────────────

    /** Costo total calculado dinámicamente */
    public function getCostoTotalAttribute(): float
    {
        return $this->insumos->sum(fn ($ri) => $ri->cantidad * ($ri->insumo->precio_unitario ?? 0));
    }
}
