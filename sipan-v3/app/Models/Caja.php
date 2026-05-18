<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Caja extends Model
{
    use LogsActivity;

    protected $table = 'cajas';

    protected $fillable = [
        'id_sucursal',
        'id_usuario_apertura',
        'id_usuario_cierre',
        'monto_apertura',
        'monto_apertura_usd',
        'monto_apertura_bs',
        'monto_cierre',
        'monto_cierre_usd',
        'monto_cierre_bs',
        'monto_esperado',
        'monto_esperado_usd',
        'monto_esperado_bs',
        'estado',
        'fecha_apertura',
        'fecha_cierre',
        'observaciones',
    ];

    protected $casts = [
        'monto_apertura' => 'decimal:2',
        'monto_apertura_usd' => 'decimal:2',
        'monto_apertura_bs' => 'decimal:2',
        'monto_cierre' => 'decimal:2',
        'monto_cierre_usd' => 'decimal:2',
        'monto_cierre_bs' => 'decimal:2',
        'monto_esperado' => 'decimal:2',
        'monto_esperado_usd' => 'decimal:2',
        'monto_esperado_bs' => 'decimal:2',
        'fecha_apertura' => 'datetime',
        'fecha_cierre' => 'datetime',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logFillable()
            ->logOnlyDirty()
            ->useLogName('Caja Chica');
    }

    public function sucursal(): BelongsTo
    {
        return $this->belongsTo(Sucursal::class, 'id_sucursal');
    }

    public function usuarioApertura(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id_usuario_apertura');
    }

    public function usuarioCierre(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id_usuario_cierre');
    }

    public function movimientos(): HasMany
    {
        return $this->hasMany(CajaMovimiento::class, 'id_caja');
    }

    // ─── Scopes y Helpers ─────────────────────────────────────────

    public function scopeActiva($query)
    {
        return $query->where('estado', 'abierta');
    }

    public function estaAbierta(): bool
    {
        return $this->estado === 'abierta';
    }
}
