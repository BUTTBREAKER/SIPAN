<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class CajaMovimiento extends Model
{
    use LogsActivity;

    protected $table = 'caja_movimientos';

    public $timestamps = false;

    protected $fillable = [
        'id_caja',
        'tipo', // 'ingreso', 'egreso'
        'monto',
        'descripcion',
        'metodo_pago', // 'efectivo_usd', 'efectivo_ves', 'punto_venta', 'pago_movil', 'zelle'
        'id_venta',
        'fecha',
    ];

    protected $casts = [
        'monto' => 'decimal:2',
        'fecha' => 'datetime',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logFillable()
            ->logOnlyDirty()
            ->useLogName('Movimiento de Caja');
    }

    public function caja(): BelongsTo
    {
        return $this->belongsTo(Caja::class, 'id_caja');
    }

    public function venta(): BelongsTo
    {
        return $this->belongsTo(Venta::class, 'id_venta');
    }
}
