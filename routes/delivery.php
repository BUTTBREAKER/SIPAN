<?php

declare(strict_types=1);

use App\Route;
use Delivery\Controllers\AuthController;
use Delivery\Controllers\PedidosController;

return [
    new Route('GET', '/', [AuthController::class, 'showLogin']),
    new Route('GET', '/login', [AuthController::class, 'showLogin']),
    new Route('POST', '/login', [AuthController::class, 'login']),
    new Route('POST', '/logout', [AuthController::class, 'logout']),
    new Route('GET', '/dashboard', [PedidosController::class, 'dashboard']),
    new Route(
        'GET',
        '/api/dashboard',
        [PedidosController::class, 'apiDashboard'],
    ),
    new Route('GET', '/pedido/{id}', [PedidosController::class, 'show']),
    new Route(
        'POST',
        '/pedido/{id}/estado',
        [PedidosController::class, 'updateEstado'],
    ),
    new Route(
        'POST',
        '/pedido/{id}/cobro',
        [PedidosController::class, 'registrarCobro'],
    ),
    new Route('GET', '/historial', [PedidosController::class, 'historial']),
    new Route(
        'GET',
        '/estadisticas',
        [PedidosController::class, 'estadisticas'],
    ),
];
