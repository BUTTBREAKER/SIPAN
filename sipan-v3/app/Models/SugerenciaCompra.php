<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class SugerenciaCompra extends Model
{
    use LogsActivity;

    protected $table = 'sugerencias_compra';

    protected $fillable = [
        'id_insumo',
        'id_sucursal',
        'cantidad_sugerida',
        'motivo',
        'estado',        // pendiente | aprobada | rechazada
        'atendida_at',
    ];

    protected $casts = [
        'cantidad_sugerida' => 'decimal:4',
        'atendida_at'       => 'datetime',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()->logFillable()->logOnlyDirty();
    }

    // ─── Relaciones ───────────────────────────────────────────────

    public function insumo(): BelongsTo
    {
        return $this->belongsTo(Insumo::class, 'id_insumo');
    }

    public function sucursal(): BelongsTo
    {
        return $this->belongsTo(Sucursal::class, 'id_sucursal');
    }

    // ─── Scopes ───────────────────────────────────────────────────

    public function scopePendientes($query)
    {
        return $query->where('estado', 'pendiente');
    }
}
