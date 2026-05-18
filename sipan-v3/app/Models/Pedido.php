<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Pedido extends Model
{
    use LogsActivity;

    protected $table = 'pedidos';

    public $timestamps = false; // We use fecha_pedido manually or handled by DB

    protected $fillable = [
        'id_cliente',
        'id_sucursal',
        'id_usuario', // Repartidor o empleado
        'numero_pedido',
        'fecha_pedido',
        'fecha_entrega',
        'estado_pedido', // 'pendiente', 'en_proceso', 'completado', 'entregado', 'cancelado'
        'estado_pago',   // 'pendiente', 'abonado', 'pagado'
        'subtotal',
        'descuento',
        'total',
        'monto_pagado',
        'monto_deuda',
        'observaciones',
    ];

    protected $casts = [
        'subtotal' => 'decimal:2',
        'descuento' => 'decimal:2',
        'total' => 'decimal:2',
        'monto_pagado' => 'decimal:2',
        'monto_deuda' => 'decimal:2',
        'fecha_pedido' => 'datetime',
        'fecha_entrega' => 'date',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logFillable()
            ->logOnlyDirty()
            ->useLogName('Pedido');
    }

    public function cliente(): BelongsTo
    {
        return $this->belongsTo(Cliente::class, 'id_cliente');
    }

    public function sucursal(): BelongsTo
    {
        return $this->belongsTo(Sucursal::class, 'id_sucursal');
    }

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id_usuario');
    }

    public function productos(): HasMany
    {
        return $this->hasMany(PedidoProducto::class, 'id_pedido');
    }

    public function pagos(): HasMany
    {
        return $this->hasMany(PedidoPago::class, 'id_pedido');
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($pedido) {
            if (!$pedido->numero_pedido) {
                $pedido->numero_pedido = 'PED-' . date('Ymd') . '-' . rand(1000, 9999);
            }
        });
    }
}
