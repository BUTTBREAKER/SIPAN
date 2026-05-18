<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Sucursal extends Model
{
    protected $table = 'sucursales';

    protected $fillable = [
        'nombre',
        'direccion',
        'telefono',
        'correo',
        'estado',
        'clave_acceso',
    ];

    protected $hidden = [
        'clave_acceso',
    ];

    protected $casts = [
        'estado' => 'string',
    ];

    // ─── Helpers ──────────────────────────────────────────────────

    public function estaActiva(): bool
    {
        return $this->estado === 'activa';
    }

    // ─── Relaciones ───────────────────────────────────────────────

    public function usuarios(): HasMany
    {
        return $this->hasMany(User::class, 'id_sucursal');
    }

    public function negocios(): HasMany
    {
        return $this->hasMany(Negocio::class, 'id_sucursal');
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
        return $this->hasMany(Proveedor::class, 'id_sucursal');
    }

    public function clientes(): HasMany
    {
        return $this->hasMany(Cliente::class, 'id_sucursal');
    }

    public function cajas(): HasMany
    {
        return $this->hasMany(Caja::class, 'id_sucursal');
    }
}
