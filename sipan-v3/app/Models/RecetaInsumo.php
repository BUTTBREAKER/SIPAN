<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RecetaInsumo extends Model
{
    protected $table = 'receta_insumos';

    public $timestamps = false;

    protected $fillable = [
        'id_receta',
        'id_insumo',
        'cantidad',
        'unidad_medida',
    ];

    protected $casts = [
        'cantidad' => 'decimal:4',
    ];

    // ─── Relaciones ───────────────────────────────────────────────

    public function receta(): BelongsTo
    {
        return $this->belongsTo(Receta::class, 'id_receta');
    }

    public function insumo(): BelongsTo
    {
        return $this->belongsTo(Insumo::class, 'id_insumo');
    }
}
