<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Produccion extends Model
{
    use LogsActivity;

    protected $table = 'producciones';

    protected $fillable = [
        'id_producto',
        'id_sucursal',
        'id_usuario',
        'id_receta',
        'cantidad_producida',
        'fecha_produccion',
        'notas',
        'estado',
    ];

    protected $casts = [
        'cantidad_producida' => 'decimal:4',
        'fecha_produccion'   => 'datetime',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()->logFillable()->logOnlyDirty();
    }

    // ─── Relaciones ───────────────────────────────────────────────

    public function producto(): BelongsTo
    {
        return $this->belongsTo(Producto::class, 'id_producto');
    }

    public function sucursal(): BelongsTo
    {
        return $this->belongsTo(Sucursal::class, 'id_sucursal');
    }

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id_usuario');
    }

    public function receta(): BelongsTo
    {
        return $this->belongsTo(Receta::class, 'id_receta');
    }

    /** Insumos consumidos en esta producción */
    public function insumosUtilizados(): HasMany
    {
        return $this->hasMany(ProduccionInsumo::class, 'id_produccion');
    }
}
