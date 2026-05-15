<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Producto extends Model
{
    use LogsActivity;

    protected $table = 'productos';

    protected $fillable = [
        'nombre',
        'descripcion',
        'codigo',
        'precio_venta',
        'precio_costo',
        'stock_actual',
        'stock_minimo',
        'categoria',
        'imagen',
        'activo',
        'id_sucursal',
    ];

    protected $casts = [
        'precio_venta' => 'decimal:2',
        'precio_costo' => 'decimal:2',
        'stock_actual' => 'decimal:4',
        'stock_minimo' => 'decimal:4',
        'activo'       => 'boolean',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()->logFillable()->logOnlyDirty();
    }

    // ─── Relaciones ───────────────────────────────────────────────

    public function sucursal(): BelongsTo
    {
        return $this->belongsTo(Sucursal::class, 'id_sucursal');
    }

    /** Un producto puede tener una receta */
    public function receta(): HasOne
    {
        return $this->hasOne(Receta::class, 'id_producto');
    }

    public function ventaItems(): HasMany
    {
        return $this->hasMany(VentaProducto::class, 'id_producto');
    }
}
