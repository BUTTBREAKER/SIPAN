<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CompraDetalle extends Model
{
    protected $table = 'compra_detalles';

    public $timestamps = false;

    protected $fillable = [
        'id_compra',
        'tipo_item',
        'id_item',
        'cantidad',
        'costo_unitario',
        'subtotal',
        'lote_codigo',
        'fecha_vencimiento',
    ];

    protected $casts = [
        'cantidad'          => 'decimal:4',
        'costo_unitario'    => 'decimal:2',
        'subtotal'          => 'decimal:2',
        'fecha_vencimiento' => 'date',
    ];

    public function compra(): BelongsTo
    {
        return $this->belongsTo(Compra::class, 'id_compra');
    }
}
