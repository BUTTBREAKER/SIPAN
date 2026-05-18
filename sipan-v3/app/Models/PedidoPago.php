<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PedidoPago extends Model
{
    protected $table = 'pedido_pagos';

    public $timestamps = false; // Using fecha_pago instead

    protected $fillable = [
        'id_pedido',
        'id_usuario', // User who registered the payment
        'monto',
        'metodo_pago', // 'efectivo', 'tarjeta', 'transferencia', 'yape', 'plin', 'otro'
        'referencia',
        'fecha_pago',
        'observaciones',
    ];

    protected $casts = [
        'monto' => 'decimal:2',
        'fecha_pago' => 'datetime',
    ];

    public function pedido(): BelongsTo
    {
        return $this->belongsTo(Pedido::class, 'id_pedido');
    }

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id_usuario');
    }
}
