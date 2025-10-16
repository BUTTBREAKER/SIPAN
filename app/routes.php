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
  App::redirect(App::getUrl('dashboard'));
})->addMiddleware(EnsureUserIsLoggedMiddleware::class);

App::route('GET /login', [AuthController::class, 'showLogin'], alias: 'login')->addMiddleware(EnsureUserIsNotLoggedMiddleware::class);
App::route('POST /login', [AuthController::class, 'login'], alias: 'login.post')->addMiddleware(EnsureUserIsNotLoggedMiddleware::class);

App::route('/logout', [AuthController::class, 'logout'], alias: 'logout');

App::route('GET /register', [AuthController::class, 'showRegister'], alias: 'register');
App::route('POST /register', [AuthController::class, 'register'], alias: 'register.post');
App::route('POST /auth/verificar-clave-sucursal', [AuthController::class, 'verificarClaveSucursal'], alias: 'auth.verificar-clave-sucursal');

App::route('GET /notificaciones/no-leidas', [NotificacionesController::class, 'getNoLeidas'], alias: 'notificaciones.no-leidas');
App::route('POST /notificaciones/marcar-leida/{id}', [NotificacionesController::class, 'marcarLeida'], alias: 'notificaciones.marcar-leida.id');
App::route('POST /notificaciones/marcar-todas-leidas', [NotificacionesController::class, 'marcarTodasLeidas'], alias: 'notificaciones.marcar-todas-leidas');
App::route('POST /auth/verificar-sucursal', [AuthController::class, 'verificarSucursal'], alias: 'auth.verificar-sucursal');
App::route('POST /auth/cambiar-sucursal', [AuthController::class, 'cambiarSucursal'], alias: 'auth.cambiar-sucursal');

// Dashboard
App::route('GET /dashboard', [DashboardController::class, 'index'], alias: 'dashboard');
App::route('GET /dashboard/notificaciones', [DashboardController::class, 'getNotificaciones'], alias: 'dashboard.notificaciones');
App::route('POST /dashboard/notificacion/leida', [DashboardController::class, 'marcarNotificacionLeida'], alias: 'dashboard.notificacion.leida');

// Productos
App::route('GET /productos', [ProductosController::class, 'index'], alias: 'productos');
App::route('GET /productos/create', [ProductosController::class, 'create'], alias: 'productos.create');
App::route('POST /productos/store', [ProductosController::class, 'store'], alias: 'productos.store');
App::route('GET /productos/edit/{id}', [ProductosController::class, 'edit'], alias: 'productos.edit.id');
App::route('POST /productos/update/{id}', [ProductosController::class, 'update'], alias: 'productos.update.id');
App::route('POST /productos/delete/{id}', [ProductosController::class, 'delete'], alias: 'productos.delete.id');
App::route('GET /productos/search', [ProductosController::class, 'search'], alias: 'productos.search');
App::route('GET /productos/stock-bajo', [ProductosController::class, 'stockBajo'], alias: 'productos.stock-bajo');

// Insumos
App::route('GET /insumos', [InsumosController::class, 'index'], alias: 'insumos');
App::route('GET /insumos/create', [InsumosController::class, 'create'], alias: 'insumos.create');
App::route('POST /insumos/store', [InsumosController::class, 'store'], alias: 'insumos.store');
App::route('GET /insumos/edit/{id}', [InsumosController::class, 'edit'], alias: 'insumos.edit.id');
App::route('POST /insumos/update/{id}', [InsumosController::class, 'update'], alias: 'insumos.update.id');
App::route('POST /insumos/delete/{id}', [InsumosController::class, 'delete'], alias: 'insumos.delete.id');
App::route('GET /insumos/search', [InsumosController::class, 'search'], alias: 'insumos.search');
App::route('GET /insumos/stock-bajo', [InsumosController::class, 'stockBajo'], alias: 'insumos.stock-bajo');

// Recetas
App::route('GET /recetas', [RecetasController::class, 'index'], alias: 'recetas');
App::route('GET /recetas/create', [RecetasController::class, 'create'], alias: 'recetas.create');
App::route('POST /recetas/store', [RecetasController::class, 'store'], alias: 'recetas.store');
App::route('GET /recetas/edit/{id}', [RecetasController::class, 'edit'], alias: 'recetas.edit.id');
App::route('POST /recetas/update/{id}', [RecetasController::class, 'update'], alias: 'recetas.update.id');
App::route('POST /recetas/delete/{id}', [RecetasController::class, 'delete'], alias: 'recetas.delete.id');
App::route('POST /recetas/calcular', [RecetasController::class, 'calcular'], alias: 'recetas.calcular');
App::route('POST /recetas/add-insumo', [RecetasController::class, 'addInsumo'], alias: 'recetas.add-insumo');
App::route('POST /recetas/remove-insumo', [RecetasController::class, 'removeInsumo'], alias: 'recetas.remove-insumo');

// Ventas
App::route('GET /ventas', [VentasController::class, 'index'], alias: 'ventas');
App::route('GET /ventas/create', [VentasController::class, 'create'], alias: 'ventas.create');
App::route('POST /ventas/store', [VentasController::class, 'store'], alias: 'ventas.store');
App::route('GET /ventas/show/{id}', [VentasController::class, 'show'], alias: 'ventas.show.id');
App::route('GET /ventas/ticket/{id}', [VentasController::class, 'ticket'], alias: 'ventas.ticket.id');

// Clientes
App::route('GET /clientes', [ClientesController::class, 'index'], alias: 'clientes');
App::route('GET /clientes/create', [ClientesController::class, 'create'], alias: 'clientes.create');
App::route('POST /clientes/store', [ClientesController::class, 'store'], alias: 'clientes.store');
App::route('GET /clientes/edit/{id}', [ClientesController::class, 'edit'], alias: 'clientes.edit.id');
App::route('POST /clientes/update/{id}', [ClientesController::class, 'update'], alias: 'clientes.update.id');
App::route('POST /clientes/delete/{id}', [ClientesController::class, 'delete'], alias: 'clientes.delete.id');
App::route('GET /clientes/show/{id}', [ClientesController::class, 'show'], alias: 'clientes.show.id');
App::route('GET /clientes/search', [ClientesController::class, 'search'], alias: 'clientes.search');

// Pedidos
App::route('GET /pedidos', [PedidosController::class, 'index'], alias: 'pedidos');
App::route('GET /pedidos/create', [PedidosController::class, 'create'], alias: 'pedidos.create');
App::route('POST /pedidos/store', [PedidosController::class, 'store'], alias: 'pedidos.store');
App::route('GET /pedidos/show/{id}', [PedidosController::class, 'show'], alias: 'pedidos.show.id');
App::route('POST /pedidos/update/{id}', [PedidosController::class, 'update'], alias: 'pedidos.update.id');
App::route('POST /pedidos/registrar-pago', [PedidosController::class, 'registrarPago'], alias: 'pedidos.registrar-pago');

// Producciones
App::route('GET /producciones', [ProduccionesController::class, 'index'], alias: 'producciones');
App::route('GET /producciones/create', [ProduccionesController::class, 'create'], alias: 'producciones.create');
App::route('POST /producciones/store', [ProduccionesController::class, 'store'], alias: 'producciones.store');
App::route('GET /producciones/show/{id}', [ProduccionesController::class, 'show'], alias: 'producciones.show.id');

// Auditorías
App::route('GET /auditorias', [AuditoriasController::class, 'index'], alias: 'auditorias');
App::route('GET /auditorias/show/{id}', [AuditoriasController::class, 'show'], alias: 'auditorias.show.id');
App::route('POST /auditorias/deshacer', [AuditoriasController::class, 'deshacer'], alias: 'auditorias.deshacer');
App::route('GET /auditorias/estadisticas', [AuditoriasController::class, 'estadisticas'], alias: 'auditorias.estadisticas');

// Respaldos
App::route('GET /respaldos', [RespaldosController::class, 'index'], alias: 'respaldos');
App::route('POST /respaldos/generar', [RespaldosController::class, 'generar'], alias: 'respaldos.generar');
App::route('POST /respaldos/restaurar', [RespaldosController::class, 'restaurar'], alias: 'respaldos.restaurar');
App::route('GET /respaldos/descargar/{id}', [RespaldosController::class, 'descargar'], alias: 'respaldos.descargar.id');

// Sugerencias de Compra
App::route('GET /sugerencias', [SugerenciasController::class, 'index'], alias: 'sugerencias');
App::route('POST /sugerencias/generar', [SugerenciasController::class, 'generar'], alias: 'sugerencias.generar');
App::route('POST /sugerencias/aprobar', [SugerenciasController::class, 'aprobar'], alias: 'sugerencias.aprobar');
App::route('POST /sugerencias/rechazar', [SugerenciasController::class, 'rechazar'], alias: 'sugerencias.rechazar');
App::route('POST /sugerencias/completar', [SugerenciasController::class, 'completar'], alias: 'sugerencias.completar');

// Reportes
App::route('GET /reportes', [ReportesController::class, 'index'], alias: 'reportes');
App::route('GET /reportes/ventas', [ReportesController::class, 'ventas'], alias: 'reportes.ventas');
App::route('GET /reportes/productos', [ReportesController::class, 'productos'], alias: 'reportes.productos');
App::route('GET /reportes/clientes', [ReportesController::class, 'clientes'], alias: 'reportes.clientes');

// Usuarios
App::route('GET /usuarios', [UsuariosController::class, 'index'], alias: 'usuarios');
App::route('GET /usuarios/perfil', [UsuariosController::class, 'perfil'], alias: 'usuarios.perfil');
App::route('GET /usuarios/actividad', [UsuariosController::class, 'actividad'], alias: 'usuarios.actividad');
App::route('POST /usuarios/actualizar-perfil', [UsuariosController::class, 'actualizarPerfil'], alias: 'usuarios.actualizar-perfil');
App::route('POST /usuarios/cambiar-estado', [UsuariosController::class, 'cambiarEstado'], alias: 'usuarios.cambiar-estado');

// Sucursales (Admin only)
App::route('GET /sucursales', [SucursalesController::class, 'index'], alias: 'sucursales');
App::route('GET /sucursales/create', [SucursalesController::class, 'create'], alias: 'sucursales.create');
App::route('POST /sucursales/store', [SucursalesController::class, 'store'], alias: 'sucursales.store');
App::route('GET /sucursales/show/{id}', [SucursalesController::class, 'show'], alias: 'sucursales.show.id');
App::route('GET /sucursales/edit/{id}', [SucursalesController::class, 'edit'], alias: 'sucursales.edit.id');
App::route('POST /sucursales/update/{id}', [SucursalesController::class, 'update'], alias: 'sucursales.update.id');
App::route('POST /sucursales/cambiar-estado', [SucursalesController::class, 'cambiarEstado'], alias: 'sucursales.cambiar-estado');
App::route('POST /sucursales/regenerar-clave/{id}', [SucursalesController::class, 'regenerarClave'], alias: 'sucursales.regenerar-clave.id');

// Cálculo de Insumos
App::route('POST /calculo-insumos/calcular', [CalculoInsumosController::class, 'calcularInsumos'], alias: 'calculo-insumos.calcular');
App::route('POST /calculo-insumos/verificar', [CalculoInsumosController::class, 'verificarDisponibilidad'], alias: 'calculo-insumos.verificar');
App::route('GET /recetas/list', [RecetasController::class, 'list'], alias: 'recetas.list');

// Predicciones
App::route('GET /predicciones', [PrediccionesController::class, 'index'], alias: 'predicciones');
App::route('POST /predicciones/generar', [PrediccionesController::class, 'generarSugerenciasAutomaticas'], alias: 'predicciones.generar');
