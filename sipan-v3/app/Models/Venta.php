<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Venta extends Model
{
    use LogsActivity;

    protected $table = 'ventas';

    protected $fillable = [
        'numero_venta',
        'id_sucursal',
        'id_negocio',
        'id_usuario',
        'id_cliente',
        'total',
        'total_usd',
        'total_ves',
        'tasa_bcv',
        'metodo_pago',
        'estado',
        'notas',
        'fecha_venta',
    ];

    protected $casts = [
        'total'     => 'decimal:2',
        'total_usd' => 'decimal:2',
        'total_ves' => 'decimal:2',
        'tasa_bcv'  => 'decimal:4',
        'fecha_venta' => 'datetime',
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

    public function negocio(): BelongsTo
    {
        return $this->belongsTo(Negocio::class, 'id_negocio');
    }

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id_usuario');
    }

    public function cliente(): BelongsTo
    {
        return $this->belongsTo(Cliente::class, 'id_cliente');
    }

    public function detalles(): HasMany
    {
        return $this->hasMany(VentaProducto::class, 'id_venta');
    }
}
