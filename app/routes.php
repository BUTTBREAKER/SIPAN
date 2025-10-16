<?php

use SIPAN\App;
use SIPAN\Controllers\AuthController;
use SIPAN\Controllers\DashboardController;
use SIPAN\Controllers\GeminiController;
use SIPAN\Controllers\OAuth2Controller;
use SIPAN\Controllers\ProductosController;
use SIPAN\Controllers\InsumosController;
use SIPAN\Controllers\RecetasController;
use SIPAN\Controllers\VentasController;
use SIPAN\Controllers\ClientesController;
use SIPAN\Controllers\PedidosController;
use SIPAN\Controllers\ProduccionesController;
use SIPAN\Controllers\AuditoriasController;
use SIPAN\Controllers\RespaldosController;
use SIPAN\Controllers\SugerenciasController;
use SIPAN\Controllers\ReportesController;
use SIPAN\Controllers\UsuariosController;
use SIPAN\Controllers\SucursalesController;
use SIPAN\Controllers\CalculoInsumosController;
use SIPAN\Controllers\PrediccionesController;
use SIPAN\Controllers\NotificacionesController;
use SIPAN\Middlewares\EnsureUserIsLoggedMiddleware;
use SIPAN\Middlewares\EnsureUserIsNotLoggedMiddleware;

App::group('/api', static function (): void {
  App::route('/gemini', GeminiController::simplePrompt(...));
});

///////////////////////////////
// 📌 Rutas de autenticación //
///////////////////////////////
App::group('/oauth2', static function (): void {
  App::route('GET /facebook', OAuth2Controller::loginWithFacebook(...));
  App::route('GET /twitter', OAuth2Controller::loginWithTwitter(...));
  App::route('GET /github', OAuth2Controller::loginWithGithub(...));
  App::route('GET /google', OAuth2Controller::loginWithGoogle(...));
});

// Autenticación
App::route('GET /', static function (): void {
  App::redirect('/dashboard');
})->addMiddleware(EnsureUserIsLoggedMiddleware::class);

App::group('/login', static function (): void {
  App::route('GET /', [AuthController::class, 'showLogin'], alias: 'login.get');
  App::route('POST /', [AuthController::class, 'login'], alias: 'login.post');
}, [EnsureUserIsNotLoggedMiddleware::class]);

App::route('/logout', [AuthController::class, 'logout'], alias: 'logout');

App::route('GET /register', [AuthController::class, 'showRegister'], alias: 'register.get');
App::route('POST /auth/register', [AuthController::class, 'register'], alias: 'register.post');
App::route('POST /auth/verificar-clave-sucursal', [AuthController::class, 'verificarClaveSucursal']);

App::route('GET /notificaciones/no-leidas', [NotificacionesController::class, 'getNoLeidas']);
App::route('POST /notificaciones/marcar-leida/{id}', [NotificacionesController::class, 'marcarLeida']);
App::route('POST /notificaciones/marcar-todas-leidas', [NotificacionesController::class, 'marcarTodasLeidas']);
App::route('POST /auth/verificar-sucursal', [AuthController::class, 'verificarSucursal']);
App::route('POST /auth/cambiar-sucursal', [AuthController::class, 'cambiarSucursal']);

// Dashboard
App::route('GET /dashboard', [DashboardController::class, 'index'], alias: 'dashboard');
App::route('GET /dashboard/notificaciones', [DashboardController::class, 'getNotificaciones']);
App::route('POST /dashboard/notificacion/leida', [DashboardController::class, 'marcarNotificacionLeida']);

// Productos
App::route('GET /productos', [ProductosController::class, 'index']);
App::route('GET /productos/create', [ProductosController::class, 'create']);
App::route('POST /productos/store', [ProductosController::class, 'store']);
App::route('GET /productos/edit/{id}', [ProductosController::class, 'edit']);
App::route('POST /productos/update/{id}', [ProductosController::class, 'update']);
App::route('POST /productos/delete/{id}', [ProductosController::class, 'delete']);
App::route('GET /productos/search', [ProductosController::class, 'search']);
App::route('GET /productos/stock-bajo', [ProductosController::class, 'stockBajo']);

// Insumos
App::route('GET /insumos', [InsumosController::class, 'index']);
App::route('GET /insumos/create', [InsumosController::class, 'create']);
App::route('POST /insumos/store', [InsumosController::class, 'store']);
App::route('GET /insumos/edit/{id}', [InsumosController::class, 'edit']);
App::route('POST /insumos/update/{id}', [InsumosController::class, 'update']);
App::route('POST /insumos/delete/{id}', [InsumosController::class, 'delete']);
App::route('GET /insumos/search', [InsumosController::class, 'search']);
App::route('GET /insumos/stock-bajo', [InsumosController::class, 'stockBajo']);

// Recetas
App::route('GET /recetas', [RecetasController::class, 'index']);
App::route('GET /recetas/create', [RecetasController::class, 'create']);
App::route('POST /recetas/store', [RecetasController::class, 'store']);
App::route('GET /recetas/edit/{id}', [RecetasController::class, 'edit']);
App::route('POST /recetas/update/{id}', [RecetasController::class, 'update']);
App::route('POST /recetas/delete/{id}', [RecetasController::class, 'delete']);
App::route('POST /recetas/calcular', [RecetasController::class, 'calcular']);
App::route('POST /recetas/add-insumo', [RecetasController::class, 'addInsumo']);
App::route('POST /recetas/remove-insumo', [RecetasController::class, 'removeInsumo']);

// Ventas
App::route('GET /ventas', [VentasController::class, 'index']);
App::route('GET /ventas/create', [VentasController::class, 'create']);
App::route('POST /ventas/store', [VentasController::class, 'store']);
App::route('GET /ventas/show/{id}', [VentasController::class, 'show']);
App::route('GET /ventas/ticket/{id}', [VentasController::class, 'ticket']);

// Clientes
App::route('GET /clientes', [ClientesController::class, 'index']);
App::route('GET /clientes/create', [ClientesController::class, 'create']);
App::route('POST /clientes/store', [ClientesController::class, 'store']);
App::route('GET /clientes/edit/{id}', [ClientesController::class, 'edit']);
App::route('POST /clientes/update/{id}', [ClientesController::class, 'update']);
App::route('POST /clientes/delete/{id}', [ClientesController::class, 'delete']);
App::route('GET /clientes/show/{id}', [ClientesController::class, 'show']);
App::route('GET /clientes/search', [ClientesController::class, 'search']);

// Pedidos
App::route('GET /pedidos', [PedidosController::class, 'index']);
App::route('GET /pedidos/create', [PedidosController::class, 'create']);
App::route('POST /pedidos/store', [PedidosController::class, 'store']);
App::route('GET /pedidos/show/{id}', [PedidosController::class, 'show']);
App::route('POST /pedidos/update/{id}', [PedidosController::class, 'update']);
App::route('POST /pedidos/registrar-pago', [PedidosController::class, 'registrarPago']);

// Producciones
App::route('GET /producciones', [ProduccionesController::class, 'index']);
App::route('GET /producciones/create', [ProduccionesController::class, 'create']);
App::route('POST /producciones/store', [ProduccionesController::class, 'store']);
App::route('GET /producciones/show/{id}', [ProduccionesController::class, 'show']);

// Auditorías
App::route('GET /auditorias', [AuditoriasController::class, 'index']);
App::route('GET /auditorias/show/{id}', [AuditoriasController::class, 'show']);
App::route('POST /auditorias/deshacer', [AuditoriasController::class, 'deshacer']);
App::route('GET /auditorias/estadisticas', [AuditoriasController::class, 'estadisticas']);

// Respaldos
App::route('GET /respaldos', [RespaldosController::class, 'index']);
App::route('POST /respaldos/generar', [RespaldosController::class, 'generar']);
App::route('POST /respaldos/restaurar', [RespaldosController::class, 'restaurar']);
App::route('GET /respaldos/descargar/{id}', [RespaldosController::class, 'descargar']);

// Sugerencias de Compra
App::route('GET /sugerencias', [SugerenciasController::class, 'index']);
App::route('POST /sugerencias/generar', [SugerenciasController::class, 'generar']);
App::route('POST /sugerencias/aprobar', [SugerenciasController::class, 'aprobar']);
App::route('POST /sugerencias/rechazar', [SugerenciasController::class, 'rechazar']);
App::route('POST /sugerencias/completar', [SugerenciasController::class, 'completar']);

// Reportes
App::route('GET /reportes', [ReportesController::class, 'index']);
App::route('GET /reportes/ventas', [ReportesController::class, 'ventas']);
App::route('GET /reportes/productos', [ReportesController::class, 'productos']);
App::route('GET /reportes/clientes', [ReportesController::class, 'clientes']);

// Usuarios
App::route('GET /usuarios', [UsuariosController::class, 'index']);
App::route('GET /usuarios/perfil', [UsuariosController::class, 'perfil']);
App::route('GET /usuarios/actividad', [UsuariosController::class, 'actividad']);
App::route('POST /usuarios/actualizar-perfil', [UsuariosController::class, 'actualizarPerfil']);
App::route('POST /usuarios/cambiar-estado', [UsuariosController::class, 'cambiarEstado']);

// Sucursales (Admin only)
App::route('GET /sucursales', [SucursalesController::class, 'index']);
App::route('GET /sucursales/create', [SucursalesController::class, 'create']);
App::route('POST /sucursales/store', [SucursalesController::class, 'store']);
App::route('GET /sucursales/show/{id}', [SucursalesController::class, 'show']);
App::route('GET /sucursales/edit/{id}', [SucursalesController::class, 'edit']);
App::route('POST /sucursales/update/{id}', [SucursalesController::class, 'update']);
App::route('POST /sucursales/cambiar-estado', [SucursalesController::class, 'cambiarEstado']);
App::route('POST /sucursales/regenerar-clave/{id}', [SucursalesController::class, 'regenerarClave']);

// Cálculo de Insumos
App::route('POST /calculo-insumos/calcular', [CalculoInsumosController::class, 'calcularInsumos']);
App::route('POST /calculo-insumos/verificar', [CalculoInsumosController::class, 'verificarDisponibilidad']);
App::route('GET /recetas/list', [RecetasController::class, 'list']);

// Predicciones
App::route('GET /predicciones', [PrediccionesController::class, 'index']);
App::route('POST /predicciones/generar', [PrediccionesController::class, 'generarSugerenciasAutomaticas']);
