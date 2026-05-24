<?php

use App\Controllers\AuditoriasController;
use App\Controllers\AuthController;
use App\Controllers\CajaController;
use App\Controllers\CalculoInsumosController;
use App\Controllers\ChatController;
use App\Controllers\ClientesController;
use App\Controllers\ComprasController;
use App\Controllers\ConfigController;
use App\Controllers\DashboardController;
use App\Controllers\InsumosController;
use App\Controllers\LotesController;
use App\Controllers\NotificacionesController;
use App\Controllers\PedidosController;
use App\Controllers\PrediccionesController;
use App\Controllers\ProduccionesController;
use App\Controllers\ProductosController;
use App\Controllers\ProveedoresController;
use App\Controllers\RecetasController;
use App\Controllers\ReportesController;
use App\Controllers\RespaldosController;
use App\Controllers\SucursalesController;
use App\Controllers\SugerenciasController;
use App\Controllers\UsuariosController;
use App\Controllers\VentasController;

return [
    // Autenticación
    'GET|/' => [AuthController::class, 'showLogin'],
    'GET|/login' => [AuthController::class, 'showLogin'],
    'POST|/login' => [AuthController::class, 'login'],
    'GET|/logout' => [AuthController::class, 'logout'],
    'GET|/register' => [AuthController::class, 'showRegister'],
    'POST|/auth/register' => [AuthController::class, 'register'],
    'POST|/auth/verificar-clave-sucursal' => [AuthController::class, 'verificarClaveSucursal'],
    'GET|/notificaciones/no-leidas' => [NotificacionesController::class, 'getNoLeidas'],
    'POST|/notificaciones/marcar-leida/{id}' => [NotificacionesController::class, 'marcarLeida'],
    'POST|/notificaciones/marcar-todas-leidas' => [NotificacionesController::class, 'marcarTodasLeidas'],
    'POST|/auth/verificar-sucursal' => [AuthController::class, 'verificarSucursal'],
    'POST|/auth/cambiar-sucursal' => [AuthController::class, 'cambiarSucursal'],

    // Caja Chica
    'GET|/cajas' => [CajaController::class, 'index'],
    'GET|/cajas/aprir' => [CajaController::class, 'abrirPanel'],
    'POST|/cajas/abrir' => [CajaController::class, 'abrir'],
    'GET|/cajas/cerrar' => [CajaController::class, 'cerrarPanel'],
    'POST|/cajas/cerrar' => [CajaController::class, 'cerrar'],
    'GET|/cajas/movimientos' => [CajaController::class, 'movimientos'],
    'POST|/cajas/movimientos' => [CajaController::class, 'addMovimiento'],

    // Dashboard
    'GET|/dashboard' => [DashboardController::class, 'index'],
    'GET|/dashboard/notificaciones' => [DashboardController::class, 'getNotificaciones'],
    'POST|/dashboard/notificacion/leida' => [DashboardController::class, 'marcarNotificacionLeida'],

    // Productos
    'GET|/productos' => [ProductosController::class, 'index'],
    'GET|/productos/create' => [ProductosController::class, 'create'],
    'POST|/productos/store' => [ProductosController::class, 'store'],
    'GET|/productos/edit/{id}' => [ProductosController::class, 'edit'],
    'POST|/productos/update/{id}' => [ProductosController::class, 'update'],
    'POST|/productos/delete/{id}' => [ProductosController::class, 'delete'],
    'GET|/productos/search' => [ProductosController::class, 'search'],
    'GET|/productos/stock-bajo' => [ProductosController::class, 'stockBajo'],

    // Insumos
    'GET|/insumos' => [InsumosController::class, 'index'],
    'GET|/insumos/create' => [InsumosController::class, 'create'],
    'POST|/insumos/store' => [InsumosController::class, 'store'],
    'GET|/insumos/edit/{id}' => [InsumosController::class, 'edit'],
    'POST|/insumos/update/{id}' => [InsumosController::class, 'update'],
    'POST|/insumos/delete/{id}' => [InsumosController::class, 'delete'],
    'GET|/insumos/search' => [InsumosController::class, 'search'],
    'GET|/insumos/stock-bajo' => [InsumosController::class, 'stockBajo'],

    // Recetas
    'GET|/recetas' => [RecetasController::class, 'index'],
    'GET|/recetas/create' => [RecetasController::class, 'create'],
    'POST|/recetas/store' => [RecetasController::class, 'store'],
    'GET|/recetas/edit/{id}' => [RecetasController::class, 'edit'],
    'GET|/recetas/show/{id}' => [RecetasController::class, 'show'],
    'POST|/recetas/update/{id}' => [RecetasController::class, 'update'],
    'POST|/recetas/delete/{id}' => [RecetasController::class, 'delete'],
    'POST|/recetas/calcular' => [RecetasController::class, 'calcular'],
    'GET|/recetas/calcular' => [RecetasController::class, 'calcular'],
    'POST|/recetas/add-insumo' => [RecetasController::class, 'addInsumo'],
    'POST|/recetas/remove-insumo' => [RecetasController::class, 'removeInsumo'],

    // Ventas
    'GET|/ventas' => [VentasController::class, 'index'],
    'GET|/ventas/create' => [VentasController::class, 'create'],
    'POST|/ventas/store' => [VentasController::class, 'store'],
    'GET|/ventas/show/{id}' => [VentasController::class, 'show'],
    'GET|/ventas/ticket/{id}' => [VentasController::class, 'ticket'],

    // Clientes
    'GET|/clientes' => [ClientesController::class, 'index'],
    'GET|/clientes/create' => [ClientesController::class, 'create'],
    'POST|/clientes/store' => [ClientesController::class, 'store'],
    'GET|/clientes/edit/{id}' => [ClientesController::class, 'edit'],
    'POST|/clientes/update/{id}' => [ClientesController::class, 'update'],
    'POST|/clientes/delete/{id}' => [ClientesController::class, 'delete'],
    'GET|/clientes/show/{id}' => [ClientesController::class, 'show'],
    'GET|/clientes/search' => [ClientesController::class, 'search'],

    // Pedidos
    'GET|/pedidos' => [PedidosController::class, 'index'],
    'GET|/pedidos/create' => [PedidosController::class, 'create'],
    'POST|/pedidos/store' => [PedidosController::class, 'store'],
    'GET|/pedidos/show/{id}' => [PedidosController::class, 'show'],
    'POST|/pedidos/update/{id}' => [PedidosController::class, 'update'],
    'POST|/pedidos/registrar-pago' => [PedidosController::class, 'registrarPago'],
    'POST|/pedidos/asignar-repartidor/{id}' => [PedidosController::class, 'asignarRepartidor'],

    // Producciones
    'GET|/producciones' => [ProduccionesController::class, 'index'],
    'GET|/producciones/create' => [ProduccionesController::class, 'create'],
    'POST|/producciones/store' => [ProduccionesController::class, 'store'],
    'GET|/producciones/show/{id}' => [ProduccionesController::class, 'show'],

    // Auditorías
    'GET|/auditorias' => [AuditoriasController::class, 'index'],
    'GET|/auditorias/show/{id}' => [AuditoriasController::class, 'show'],
    'POST|/auditorias/deshacer' => [AuditoriasController::class, 'deshacer'],
    'GET|/auditorias/estadisticas' => [AuditoriasController::class, 'estadisticas'],

    // Respaldos
    'GET|/respaldos' => [RespaldosController::class, 'index'],
    'POST|/respaldos/generar' => [RespaldosController::class, 'generar'],
    'POST|/respaldos/restaurar' => [RespaldosController::class, 'restaurar'],
    'GET|/respaldos/descargar/{id}' => [RespaldosController::class, 'descargar'],

    // Sugerencias de Compra
    'GET|/sugerencias' => [SugerenciasController::class, 'index'],
    'POST|/sugerencias/generar' => [SugerenciasController::class, 'generar'],
    'POST|/sugerencias/aprobar' => [SugerenciasController::class, 'aprobar'],
    'POST|/sugerencias/rechazar' => [SugerenciasController::class, 'rechazar'],
    'POST|/sugerencias/completar' => [SugerenciasController::class, 'completar'],

    // Reportes
    'GET|/reportes' => [ReportesController::class, 'index'],
    'GET|/reportes/ventas' => [ReportesController::class, 'ventas'],
    'GET|/reportes/productos' => [ReportesController::class, 'productos'],
    'GET|/reportes/clientes' => [ReportesController::class, 'clientes'],
    'GET|/reportes/vencimientos' => [ReportesController::class, 'vencimientos'],
    'GET|/reportes/compras' => [ReportesController::class, 'compras'],
    'GET|/reportes/insumos' => [ReportesController::class, 'insumos'],
    'GET|/reportes/producciones' => [ReportesController::class, 'producciones'],
    'GET|/reportes/pedidos' => [ReportesController::class, 'pedidos'],

    // Usuarios
    'GET|/usuarios' => [UsuariosController::class, 'index'],
    'GET|/usuarios/perfil' => [UsuariosController::class, 'perfil'],
    'GET|/usuarios/actividad' => [UsuariosController::class, 'actividad'],
    'POST|/usuarios/actualizar-perfil' => [UsuariosController::class, 'actualizarPerfil'],
    'POST|/usuarios/cambiar-estado' => [UsuariosController::class, 'cambiarEstado'],
    'GET|/usuarios/edit' => [UsuariosController::class, 'edit'],
    'POST|/usuarios/update' => [UsuariosController::class, 'update'],

    // Sucursales (Admin only)
    'GET|/sucursales' => [SucursalesController::class, 'index'],
    'GET|/sucursales/create' => [SucursalesController::class, 'create'],
    'POST|/sucursales/store' => [SucursalesController::class, 'store'],
    'GET|/sucursales/show/{id}' => [SucursalesController::class, 'show'],
    'GET|/sucursales/edit/{id}' => [SucursalesController::class, 'edit'],
    'POST|/sucursales/update/{id}' => [SucursalesController::class, 'update'],
    'POST|/sucursales/cambiar-estado' => [SucursalesController::class, 'cambiarEstado'],
    'POST|/sucursales/regenerar-clave/{id}' => [SucursalesController::class, 'regenerarClave'],

    // Cálculo de Insumos
    'POST|/calculo-insumos/calcular' => [CalculoInsumosController::class, 'calcularInsumos'],
    'POST|/calculo-insumos/verificar' => [CalculoInsumosController::class, 'verificarDisponibilidad'],
    'GET|/recetas/list' => [RecetasController::class, 'list'],

    // Predicciones
    'GET|/predicciones' => [PrediccionesController::class, 'index'],
    'GET|/predicciones/data' => [PrediccionesController::class, 'getDatosVentas'],
    'POST|/predicciones/generar' => [PrediccionesController::class, 'generarSugerenciasAutomaticas'],

    // Proveedores
    'GET|/proveedores' => [ProveedoresController::class, 'index'],
    'GET|/proveedores/create' => [ProveedoresController::class, 'create'],
    'POST|/proveedores/store' => [ProveedoresController::class, 'store'],
    'GET|/proveedores/edit/{id}' => [ProveedoresController::class, 'edit'],
    'POST|/proveedores/update/{id}' => [ProveedoresController::class, 'update'],
    'POST|/proveedores/delete/{id}' => [ProveedoresController::class, 'delete'],
    'GET|/proveedores/show/{id}' => [ProveedoresController::class, 'show'],
    'GET|/proveedores/insumos-sin-proveedor' => [ProveedoresController::class, 'insumosSinProveedor'],

    // Compras
    'GET|/compras' => [ComprasController::class, 'index'],
    'GET|/compras/create' => [ComprasController::class, 'create'],
    'POST|/compras/store' => [ComprasController::class, 'store'],
    'GET|/compras/show/{id}' => [ComprasController::class, 'show'],

    // Control de Lotes / Vencimientos
    'GET|/lotes' => [LotesController::class, 'index'],
    'POST|/lotes/ajustar' => [LotesController::class, 'ajustar'],

    // System Config
    'GET|/config/refresh-tasa' => [ConfigController::class, 'refreshTasa'],

    // Chat Interno
    'GET|/chat'                        => [ChatController::class, 'index'],
    'GET|/chat/conversaciones'         => [ChatController::class, 'getConversaciones'],
    'GET|/chat/usuarios'               => [ChatController::class, 'getUsuarios'],
    'POST|/chat/conversacion-directa'  => [ChatController::class, 'getOrCreateDirecta'],
    'GET|/chat/mensajes/{id}'          => [ChatController::class, 'getMensajes'],
    'POST|/chat/enviar/{id}'           => [ChatController::class, 'enviar'],
    'GET|/chat/poll'                   => [ChatController::class, 'poll'],
    'GET|/chat/sync'                   => [ChatController::class, 'sync'],
];
