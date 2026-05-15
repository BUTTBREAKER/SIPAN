<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VentaProducto extends Model
{
    protected $table = 'venta_productos';

    public $timestamps = false;

    protected $fillable = [
        'id_venta',
        'id_producto',
        'cantidad',
        'precio_unitario',
        'precio_unitario_usd',
        'precio_unitario_ves',
        'subtotal',
        'subtotal_usd',
        'subtotal_ves',
    ];

    protected $casts = [
        'cantidad'             => 'decimal:4',
        'precio_unitario'      => 'decimal:2',
        'precio_unitario_usd'  => 'decimal:2',
        'precio_unitario_ves'  => 'decimal:2',
        'subtotal'             => 'decimal:2',
        'subtotal_usd'         => 'decimal:2',
        'subtotal_ves'         => 'decimal:2',
    ];

    public function venta(): BelongsTo
    {
        return $this->belongsTo(Venta::class, 'id_venta');
    }

    public function producto(): BelongsTo
    {
        return $this->belongsTo(Producto::class, 'id_producto');
    }
}
