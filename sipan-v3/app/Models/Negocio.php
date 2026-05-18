<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Negocio extends Model
{
    use LogsActivity;

    protected $table = 'negocios';

    public $timestamps = false;

    protected $fillable = [
        'id_sucursal',
        'nombre',
        'direccion',
        'telefono',
        'correo',
        'logo',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logFillable()
            ->logOnlyDirty()
            ->useLogName('Negocio');
    }

    // ─── Relaciones ───────────────────────────────────────────────

    public function sucursal(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Sucursal::class, 'id_sucursal');
    }

    public function productos(): HasMany
    {
        return $this->hasMany(Producto::class, 'id_negocio');
    }

    public function ventas(): HasMany
    {
        return $this->hasMany(Venta::class, 'id_negocio');
    }

    public function producciones(): HasMany
    {
        return $this->hasMany(Produccion::class, 'id_negocio');
    }

    public function insumos(): HasMany
    {
        return $this->hasMany(Insumo::class, 'id_negocio');
    }
}
