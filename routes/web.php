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
use App\Middlewares\AuthMiddleware;
use App\Route;

$redirectToLoginIfUserIsNotLogged = static function (): void {
    if (!isset($_SESSION['user_id'])) {
        header('Location: /login');
        exit;
    }
};

$checkIfUserIsAdministrator = static function (): void {
    AuthMiddleware::checkRole(['administrador']);
};

return [
    // Autenticación
    new Route('GET', '/', [AuthController::class, 'showLogin']),
    new Route('GET', '/login', [AuthController::class, 'showLogin']),
    new Route('POST', '/login', [AuthController::class, 'login']),
    new Route('GET', '/logout', [AuthController::class, 'logout']),
    new Route('GET', '/register', [AuthController::class, 'showRegister']),
    new Route('POST', '/auth/register', [AuthController::class, 'register']),
    new Route('POST', '/auth/verificar-clave-sucursal', [AuthController::class, 'verificarClaveSucursal']),
    new Route(
        'GET',
        '/notificaciones/no-leidas',
        [AuthMiddleware::class, 'check'],
        [NotificacionesController::class, 'getNoLeidas'],
    ),
    new Route(
        'POST',
        '/notificaciones/marcar-leida/{id}',
        [AuthMiddleware::class, 'check'],
        [NotificacionesController::class, 'marcarLeida'],
    ),
    new Route(
        'POST',
        '/notificaciones/marcar-todas-leidas',
        [AuthMiddleware::class, 'check'],
        [NotificacionesController::class, 'marcarTodasLeidas'],
    ),
    new Route('POST', '/auth/verificar-sucursal', [AuthController::class, 'verificarSucursal']),
    new Route('POST', '/auth/cambiar-sucursal', [AuthController::class, 'cambiarSucursal']),

    // Caja Chica
    new Route('GET', '/cajas', [CajaController::class, 'index']),
    new Route('GET', '/cajas/aprir', [CajaController::class, 'abrirPanel']),
    new Route('POST', '/cajas/abrir', [CajaController::class, 'abrir']),
    new Route('GET', '/cajas/cerrar', [CajaController::class, 'cerrarPanel']),
    new Route('POST', '/cajas/cerrar', [CajaController::class, 'cerrar']),
    new Route('GET', '/cajas/movimientos', [CajaController::class, 'movimientos']),
    new Route('POST', '/cajas/movimientos', [CajaController::class, 'addMovimiento']),

    // Dashboard
    new Route('GET', '/dashboard', [DashboardController::class, 'index']),
    new Route('GET', '/dashboard/notificaciones', [DashboardController::class, 'getNotificaciones']),
    new Route('POST', '/dashboard/notificacion/leida', [DashboardController::class, 'marcarNotificacionLeida']),

    // Productos
    new Route('GET', '/productos', [ProductosController::class, 'index']),
    new Route('GET', '/productos/create', [ProductosController::class, 'create']),
    new Route('POST', '/productos/store', [ProductosController::class, 'store']),
    new Route('GET', '/productos/edit/{id}', [ProductosController::class, 'edit']),
    new Route('POST', '/productos/update/{id}', [ProductosController::class, 'update']),
    new Route('POST', '/productos/delete/{id}', [ProductosController::class, 'delete']),
    new Route('GET', '/productos/search', [ProductosController::class, 'search']),
    new Route('GET', '/productos/stock-bajo', [ProductosController::class, 'stockBajo']),

    // Insumos
    new Route('GET', '/insumos', [InsumosController::class, 'index']),
    new Route('GET', '/insumos/create', [InsumosController::class, 'create']),
    new Route('POST', '/insumos/store', [InsumosController::class, 'store']),
    new Route('GET', '/insumos/edit/{id}', [InsumosController::class, 'edit']),
    new Route('POST', '/insumos/update/{id}', [InsumosController::class, 'update']),
    new Route('POST', '/insumos/delete/{id}', [InsumosController::class, 'delete']),
    new Route('GET', '/insumos/search', [InsumosController::class, 'search']),
    new Route('GET', '/insumos/stock-bajo', [InsumosController::class, 'stockBajo']),

    // Recetas
    new Route('GET', '/recetas', [RecetasController::class, 'index']),
    new Route('GET', '/recetas/create', [RecetasController::class, 'create']),
    new Route('POST', '/recetas/store', [RecetasController::class, 'store']),
    new Route('GET', '/recetas/edit/{id}', [RecetasController::class, 'edit']),
    new Route('GET', '/recetas/show/{id}', [RecetasController::class, 'show']),
    new Route('POST', '/recetas/update/{id}', [RecetasController::class, 'update']),
    new Route('POST', '/recetas/delete/{id}', [RecetasController::class, 'delete']),
    new Route('POST', '/recetas/calcular', [RecetasController::class, 'calcular']),
    new Route('GET', '/recetas/calcular', [RecetasController::class, 'calcular']),
    new Route('POST', '/recetas/add-insumo', [RecetasController::class, 'addInsumo']),
    new Route('POST', '/recetas/remove-insumo', [RecetasController::class, 'removeInsumo']),

    // Ventas
    new Route('GET', '/ventas', [VentasController::class, 'index']),
    new Route('GET', '/ventas/create', [VentasController::class, 'create']),
    new Route('POST', '/ventas/store', [VentasController::class, 'store']),
    new Route('GET', '/ventas/show/{id}', [VentasController::class, 'show']),
    new Route('GET', '/ventas/ticket/{id}', [VentasController::class, 'ticket']),

    // Clientes
    new Route('GET', '/clientes', [ClientesController::class, 'index']),
    new Route('GET', '/clientes/create', [ClientesController::class, 'create']),
    new Route('POST', '/clientes/store', [ClientesController::class, 'store']),
    new Route('GET', '/clientes/edit/{id}', [ClientesController::class, 'edit']),
    new Route('POST', '/clientes/update/{id}', [ClientesController::class, 'update']),
    new Route('POST', '/clientes/delete/{id}', [ClientesController::class, 'delete']),
    new Route('GET', '/clientes/show/{id}', [ClientesController::class, 'show']),
    new Route('GET', '/clientes/search', [ClientesController::class, 'search']),

    // Pedidos
    new Route('GET', '/pedidos', [PedidosController::class, 'index']),
    new Route('GET', '/pedidos/create', [PedidosController::class, 'create']),
    new Route('POST', '/pedidos/store', [PedidosController::class, 'store']),
    new Route('GET', '/pedidos/show/{id}', [PedidosController::class, 'show']),
    new Route('POST', '/pedidos/update/{id}', [PedidosController::class, 'update']),
    new Route('POST', '/pedidos/registrar-pago', [PedidosController::class, 'registrarPago']),
    new Route('POST', '/pedidos/asignar-repartidor/{id}', [PedidosController::class, 'asignarRepartidor']),

    // Producciones
    new Route('GET', '/producciones', [ProduccionesController::class, 'index']),
    new Route('GET', '/producciones/create', [ProduccionesController::class, 'create']),
    new Route('POST', '/producciones/store', [ProduccionesController::class, 'store']),
    new Route('GET', '/producciones/show/{id}', [ProduccionesController::class, 'show']),

    // Auditorías
    new Route('GET', '/auditorias', [AuditoriasController::class, 'index']),
    new Route('GET', '/auditorias/show/{id}', [AuditoriasController::class, 'show']),
    new Route('POST', '/auditorias/deshacer', [AuditoriasController::class, 'deshacer']),
    new Route('GET', '/auditorias/estadisticas', [AuditoriasController::class, 'estadisticas']),

    // Respaldos
    new Route('GET', '/respaldos', [RespaldosController::class, 'index']),
    new Route('POST', '/respaldos/generar', [RespaldosController::class, 'generar']),
    new Route('POST', '/respaldos/restaurar', [RespaldosController::class, 'restaurar']),
    new Route('GET', '/respaldos/descargar/{id}', [RespaldosController::class, 'descargar']),

    // Sugerencias de Compra
    new Route('GET', '/sugerencias', [SugerenciasController::class, 'index']),
    new Route('POST', '/sugerencias/generar', [SugerenciasController::class, 'generar']),
    new Route('POST', '/sugerencias/aprobar', [SugerenciasController::class, 'aprobar']),
    new Route('POST', '/sugerencias/rechazar', [SugerenciasController::class, 'rechazar']),
    new Route('POST', '/sugerencias/completar', [SugerenciasController::class, 'completar']),

    // Reportes
    new Route(
        'GET',
        '/reportes',
        $redirectToLoginIfUserIsNotLogged,
        [ReportesController::class, 'index'],
    ),
    new Route(
        'GET',
        '/reportes/ventas',
        $redirectToLoginIfUserIsNotLogged,
        [ReportesController::class, 'ventas'],
    ),
    new Route(
        'GET',
        '/reportes/productos',
        $redirectToLoginIfUserIsNotLogged,
        [ReportesController::class, 'productos'],
    ),
    new Route(
        'GET',
        '/reportes/clientes',
        $redirectToLoginIfUserIsNotLogged,
        [ReportesController::class, 'clientes'],
    ),
    // new Route(
    //     'GET',
    //     '/reportes/vencimientos',
    //     $redirectToLoginIfUserIsNotLogged,
    //     [ReportesController::class, 'vencimientos'],
    // ),
    // new Route(
    //     'GET',
    //     '/reportes/compras',
    //     $redirectToLoginIfUserIsNotLogged,
    //     [ReportesController::class, 'compras'],
    // ),
    new Route(
        'GET',
        '/reportes/insumos',
        $redirectToLoginIfUserIsNotLogged,
        [ReportesController::class, 'insumos'],
    ),
    new Route(
        'GET',
        '/reportes/producciones',
        $redirectToLoginIfUserIsNotLogged,
        [ReportesController::class, 'producciones'],
    ),
    new Route(
        'GET',
        '/reportes/pedidos',
        $redirectToLoginIfUserIsNotLogged,
        [ReportesController::class, 'pedidos'],
    ),

    // Usuarios
    new Route(
        'GET',
        '/usuarios',
        [AuthMiddleware::class, 'check'],
        [UsuariosController::class, 'index'],
    ),
    new Route(
        'GET',
        '/usuarios/perfil',
        [AuthMiddleware::class, 'check'],
        [UsuariosController::class, 'perfil'],
    ),
    new Route(
        'GET',
        '/usuarios/actividad',
        [AuthMiddleware::class, 'check'],
        [UsuariosController::class, 'actividad'],
    ),
    new Route(
        'POST',
        '/usuarios/actualizar-perfil',
        [AuthMiddleware::class, 'check'],
        [UsuariosController::class, 'actualizarPerfil'],
    ),
    new Route(
        'POST',
        '/usuarios/cambiar-estado',
        [AuthMiddleware::class, 'check'],
        [UsuariosController::class, 'cambiarEstado'],
    ),
    new Route(
        'GET',
        '/usuarios/edit',
        [AuthMiddleware::class, 'check'],
        [UsuariosController::class, 'edit'],
    ),
    new Route(
        'POST',
        '/usuarios/update',
        [AuthMiddleware::class, 'check'],
        [UsuariosController::class, 'update'],
    ),

    // Sucursales (Admin only)
    new Route(
        'GET',
        '/sucursales',
        $checkIfUserIsAdministrator,
        [SucursalesController::class, 'index'],
    ),
    new Route(
        'GET',
        '/sucursales/create',
        $checkIfUserIsAdministrator,
        [SucursalesController::class, 'create'],
    ),
    new Route(
        'POST',
        '/sucursales/store',
        $checkIfUserIsAdministrator,
        [SucursalesController::class, 'store'],
    ),
    new Route(
        'GET',
        '/sucursales/show/{id}',
        $checkIfUserIsAdministrator,
        [SucursalesController::class, 'show'],
    ),
    new Route(
        'GET',
        '/sucursales/edit/{id}',
        $checkIfUserIsAdministrator,
        [SucursalesController::class, 'edit'],
    ),
    new Route(
        'POST',
        '/sucursales/update/{id}',
        $checkIfUserIsAdministrator,
        [SucursalesController::class, 'update'],
    ),
    new Route(
        'POST',
        '/sucursales/cambiar-estado',
        $checkIfUserIsAdministrator,
        [SucursalesController::class, 'cambiarEstado'],
    ),
    new Route(
        'POST',
        '/sucursales/regenerar-clave/{id}',
        $checkIfUserIsAdministrator,
        [SucursalesController::class, 'regenerarClave'],
    ),

    // Cálculo de Insumos
    new Route(
        'POST',
        '/calculo-insumos/calcular',
        [AuthMiddleware::class, 'check'],
        [CalculoInsumosController::class, 'calcularInsumos'],
    ),
    new Route(
        'POST',
        '/calculo-insumos/verificar',
        [AuthMiddleware::class, 'check'],
        [CalculoInsumosController::class, 'verificarDisponibilidad'],
    ),
    new Route('GET', '/recetas/list', [RecetasController::class, 'list']),

    // Predicciones
    new Route('GET', '/predicciones', [PrediccionesController::class, 'index']),
    new Route('GET', '/predicciones/data', [PrediccionesController::class, 'getDatosVentas']),
    new Route('POST', '/predicciones/generar', [PrediccionesController::class, 'generarSugerenciasAutomaticas']),

    // Proveedores
    new Route('GET', '/proveedores', [ProveedoresController::class, 'index']),
    new Route('GET', '/proveedores/create', [ProveedoresController::class, 'create']),
    new Route('POST', '/proveedores/store', [ProveedoresController::class, 'store']),
    new Route('GET', '/proveedores/edit/{id}', [ProveedoresController::class, 'edit']),
    new Route('POST', '/proveedores/update/{id}', [ProveedoresController::class, 'update']),
    new Route('POST', '/proveedores/delete/{id}', [ProveedoresController::class, 'delete']),
    new Route('GET', '/proveedores/show/{id}', [ProveedoresController::class, 'show']),
    new Route('GET', '/proveedores/insumos-sin-proveedor', [ProveedoresController::class, 'insumosSinProveedor']),

    // Compras
    new Route('GET', '/compras', [ComprasController::class, 'index']),
    new Route('GET', '/compras/create', [ComprasController::class, 'create']),
    new Route('POST', '/compras/store', [ComprasController::class, 'store']),
    new Route('GET', '/compras/show/{id}', [ComprasController::class, 'show']),

    // Control de Lotes / Vencimientos
    new Route('GET', '/lotes', [LotesController::class, 'index']),
    new Route('POST', '/lotes/ajustar', [LotesController::class, 'ajustar']),

    // System Config
    new Route('GET', '/config/refresh-tasa', [ConfigController::class, 'refreshTasa']),

    // Chat Interno
    new Route('GET', '/chat', [ChatController::class, 'index']),
    new Route('GET', '/chat/conversaciones', [ChatController::class, 'getConversaciones']),
    new Route('GET', '/chat/usuarios', [ChatController::class, 'getUsuarios']),
    new Route('POST', '/chat/conversacion-directa', [ChatController::class, 'getOrCreateDirecta']),
    new Route('GET', '/chat/mensajes/{id}', [ChatController::class, 'getMensajes']),
    new Route('POST', '/chat/enviar/{id}', [ChatController::class, 'enviar']),
    new Route('GET', '/chat/poll', [ChatController::class, 'poll']),
    new Route('GET', '/chat/sync', [ChatController::class, 'sync']),
];
