<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\DeliveryController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::post('/delivery/auth/login', [DeliveryController::class, 'login']);

Route::middleware('auth:sanctum')->prefix('delivery')->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
    
    Route::get('/pedidos', [DeliveryController::class, 'getPedidos']);
    Route::get('/pedidos/{id}', [DeliveryController::class, 'getPedidoDetalle']);
    Route::post('/pedidos/{id}/estado', [DeliveryController::class, 'actualizarEstado']);
    Route::post('/pedidos/{id}/pago', [DeliveryController::class, 'registrarPago']);
});
