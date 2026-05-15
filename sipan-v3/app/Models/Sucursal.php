<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Sucursal extends Model
{
    protected $table = 'sucursales';

    protected $fillable = [
        'nombre',
        'direccion',
        'telefono',
        'email',
        'activo',
    ];

    protected $casts = [
        'activo' => 'boolean',
    ];

    public function usuarios(): HasMany
    {
        return $this->hasMany(User::class, 'id_sucursal');
    }

    public function insumos(): HasMany
    {
        return $this->hasMany(Insumo::class, 'id_sucursal');
    }

    public function productos(): HasMany
    {
        return $this->hasMany(Producto::class, 'id_sucursal');
    }

    public function recetas(): HasMany
    {
        return $this->hasMany(Receta::class, 'id_sucursal');
    }

    public function ventas(): HasMany
    {
        return $this->hasMany(Venta::class, 'id_sucursal');
    }

    public function proveedores(): HasMany
    {
        return $this->hasMany(Proveedor::class, 'sucursal_id');
    }
}
