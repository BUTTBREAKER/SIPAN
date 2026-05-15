<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Proveedor extends Model
{
    use LogsActivity;

    protected $table = 'proveedores';

    protected $fillable = [
        'nombre',
        'rif',
        'telefono',
        'email',
        'direccion',
        'contacto',
        'dias_credito',
        'activo',
        'sucursal_id',
    ];

    protected $casts = [
        'activo'       => 'boolean',
        'dias_credito' => 'integer',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logFillable()
            ->logOnlyDirty()
            ->setDescriptionForEvent(fn (string $eventName) => "Proveedor {$eventName}");
    }

    // ─── Relaciones ───────────────────────────────────────────────

    public function sucursal(): BelongsTo
    {
        return $this->belongsTo(Sucursal::class, 'sucursal_id');
    }

    public function insumos(): BelongsToMany
    {
        return $this->belongsToMany(Insumo::class, 'proveedor_insumos', 'id_proveedor', 'id_insumo')
                    ->withPivot(['precio', 'tiempo_entrega'])
                    ->withTimestamps();
    }
}
