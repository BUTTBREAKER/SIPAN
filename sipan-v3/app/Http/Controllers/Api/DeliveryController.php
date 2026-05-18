<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Pedido;
use Illuminate\Support\Facades\Hash;

class DeliveryController extends Controller
{
    /**
     * Autenticación para repartidores
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Credenciales incorrectas'
            ], 401);
        }

        if (!$user->hasRole('repartidor')) {
            return response()->json([
                'success' => false,
                'message' => 'Acceso denegado: el usuario no es un repartidor'
            ], 403);
        }

        $token = $user->createToken('delivery-app')->plainTextToken;

        return response()->json([
            'success' => true,
            'token' => $token,
            'user' => [
                'id' => $user->id,
                'name' => trim($user->primer_nombre . ' ' . $user->apellido_paterno),
                'email' => $user->email,
            ]
        ]);
    }

    /**
     * Obtener pedidos asignados al repartidor autenticado
     */
    public function getPedidos(Request $request)
    {
        $user = $request->user();
        
        $estado = $request->query('estado'); // Opcional: filtrar por estado
        
        $query = Pedido::with(['cliente', 'sucursal'])
            ->where('id_usuario', $user->id);
            
        if ($estado) {
            $query->where('estado_pedido', $estado);
        } else {
            // Por defecto, no mostrar cancelados ni completados si no se especifica
            $query->whereNotIn('estado_pedido', ['completado', 'cancelado']);
        }
        
        $pedidos = $query->orderBy('fecha_entrega', 'asc')
            ->orderBy('fecha_pedido', 'asc')
            ->get();
            
        return response()->json([
            'success' => true,
            'data' => $pedidos
        ]);
    }

    /**
     * Obtener detalle de un pedido específico
     */
    public function getPedidoDetalle(Request $request, $id)
    {
        $user = $request->user();
        
        $pedido = Pedido::with(['cliente', 'sucursal', 'productos.producto', 'pagos'])
            ->where('id', $id)
            ->where('id_usuario', $user->id) // Solo puede ver sus propios pedidos asignados
            ->first();
            
        if (!$pedido) {
            return response()->json([
                'success' => false,
                'message' => 'Pedido no encontrado o no asignado a este repartidor'
            ], 404);
        }
        
        return response()->json([
            'success' => true,
            'data' => $pedido
        ]);
    }

    /**
     * Actualizar el estado de entrega de un pedido
     */
    public function actualizarEstado(Request $request, $id)
    {
        $request->validate([
            'estado_pedido' => 'required|in:en_proceso,entregado,cancelado',
            'observaciones' => 'nullable|string'
        ]);

        $user = $request->user();
        
        $pedido = Pedido::where('id', $id)
            ->where('id_usuario', $user->id)
            ->first();
            
        if (!$pedido) {
            return response()->json([
                'success' => false,
                'message' => 'Pedido no encontrado'
            ], 404);
        }
        
        $pedido->estado_pedido = $request->estado_pedido;
        
        if ($request->has('observaciones') && $request->observaciones) {
            $observaciones = $pedido->observaciones ? $pedido->observaciones . "\n" : "";
            $observaciones .= "[" . date('d/m/Y H:i') . "] Repartidor: " . $request->observaciones;
            $pedido->observaciones = $observaciones;
        }
        
        $pedido->save();
        
        return response()->json([
            'success' => true,
            'message' => 'Estado actualizado correctamente',
            'data' => $pedido
        ]);
    }

    /**
     * Registrar un pago al momento de la entrega
     */
    public function registrarPago(Request $request, $id)
    {
        $request->validate([
            'monto' => 'required|numeric|min:0.01',
            'metodo_pago' => 'required|string',
            'referencia' => 'nullable|string'
        ]);

        $user = $request->user();
        
        $pedido = Pedido::where('id', $id)
            ->where('id_usuario', $user->id)
            ->first();
            
        if (!$pedido) {
            return response()->json([
                'success' => false,
                'message' => 'Pedido no encontrado'
            ], 404);
        }

        // Crear el pago
        $pago = \App\Models\PedidoPago::create([
            'id_pedido' => $pedido->id,
            'id_usuario' => $user->id,
            'monto' => $request->monto,
            'metodo_pago' => $request->metodo_pago,
            'referencia' => $request->referencia,
            'fecha_pago' => now(),
        ]);

        // Actualizar totales del pedido
        $pagado = $pedido->pagos()->sum('monto');
        $deuda = $pedido->total - $pagado;
        
        $estado_pago = 'pendiente';
        if ($pagado >= $pedido->total) {
            $estado_pago = 'pagado';
        } elseif ($pagado > 0) {
            $estado_pago = 'abonado';
        }

        $pedido->update([
            'monto_pagado' => $pagado,
            'monto_deuda' => max(0, $deuda),
            'estado_pago' => $estado_pago
        ]);
        
        return response()->json([
            'success' => true,
            'message' => 'Pago registrado correctamente',
            'data' => [
                'pedido' => $pedido,
                'pago' => $pago
            ]
        ]);
    }
}
