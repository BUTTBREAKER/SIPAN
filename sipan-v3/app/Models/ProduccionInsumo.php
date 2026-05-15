<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProduccionInsumo extends Model
{
    protected $table = 'produccion_insumos';

    public $timestamps = false;

    protected $fillable = [
        'id_produccion',
        'id_insumo',
        'cantidad_utilizada',
    ];

    protected $casts = [
        'cantidad_utilizada' => 'decimal:4',
    ];

    public function produccion(): BelongsTo
    {
        return $this->belongsTo(Produccion::class, 'id_produccion');
    }

    public function insumo(): BelongsTo
    {
        return $this->belongsTo(Insumo::class, 'id_insumo');
    }
}
