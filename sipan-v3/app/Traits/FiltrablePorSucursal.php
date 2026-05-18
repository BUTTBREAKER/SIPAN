<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

trait FiltrablePorSucursal
{
    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();
        
        $user = Auth::user();
        
        if ($user && !$user->hasRole('superadmin')) {
            // Asumimos que los modelos tienen id_sucursal
            $query->where('id_sucursal', $user->id_sucursal);
        }
        
        return $query;
    }
}
