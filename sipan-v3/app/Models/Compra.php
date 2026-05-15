<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Compra extends Model
{
    use LogsActivity;

    protected $table = 'compras';

    protected $fillable = [
        'id_sucursal',
        'id_proveedor',
        'id_usuario',
        'fecha_compra',
        'numero_factura',
        'total',
        'estado',
        'notas',
    ];

    protected $casts = [
        'total'        => 'decimal:2',
        'fecha_compra' => 'datetime',
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

    public function proveedor(): BelongsTo
    {
        return $this->belongsTo(Proveedor::class, 'id_proveedor');
    }

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id_usuario');
    }

    public function detalles(): HasMany
    {
        return $this->hasMany(CompraDetalle::class, 'id_compra');
    }
}
