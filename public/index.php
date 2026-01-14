<?php

require_once __DIR__ . '/../vendor/autoload.php';

// SIPAN - Sistema Integral para Panaderías
// Archivo principal de enrutamiento

// Habilitar buffering de salida para prevenir errores de cabeceras
ob_start();

// Cargar configuración
$config = require __DIR__ . '/../config/config.php';

// Configurar errores según entorno
if ($config['app_env'] === 'production') {
    // Producción: No mostrar errores en pantalla, solo registrar en log
    error_reporting(E_ALL);
    ini_set('display_errors', false);
    ini_set('error_log', __DIR__ . '/../logs/php-errors.log');
} else {
    // Desarrollo: Mostrar todos los errores
    error_reporting(E_ALL);
    ini_set('display_errors', $config['app_debug']);
    ini_set('error_log', __DIR__ . '/../logs/sipan-debug.log');
}

// Detectar si estamos detrás de un proxy/túnel con HTTPS
$isSecure = @$_SERVER['HTTPS'] === 'on' || @$_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https';

// Configurar parámetros de la cookie de sesión ANTES de iniciar la sesión
$sessionParams = session_get_cookie_params();

session_set_cookie_params([
    'lifetime' => $config['session_lifetime'] ?? 86400 /* 1 day */,
    'path' => $sessionParams['path'],
    'domain' => $sessionParams['domain'],
    'secure' => $isSecure,
    'httponly' => true,
    'samesite' => 'Lax' // Necesario para túneles y navegadores modernos
]);

if (session_status() === PHP_SESSION_NONE) {
    session_name($config['session_name'] ?? 'SIPAN_SESSION');
    session_start();
}

// Habilitar log de debug
ini_set('log_errors', true);

// Obtener la ruta solicitada
$request_uri = $_SERVER['REQUEST_URI'];
$path = parse_url($request_uri, PHP_URL_PATH);

// Asegurar que la ruta comience con /
if (!str_starts_with($path, '/')) {
    $path = '/' . $path;
}

// Remover index.php si está presente
$path = str_replace('/index.php', '', $path);

// Limpiar la ruta
$path = rtrim($path, '/');
if (empty($path)) $path = '/';

// Debug (comentar en producción)
if ($config['app_debug']) {
    error_log("Path: $path, Method: {$_SERVER['REQUEST_METHOD']}, URI: $request_uri");
} else {
    // Log todas las peticiones
    $log_message = "{$_SERVER['REQUEST_METHOD']} | {$_SERVER['REQUEST_URI']}";
    error_log($log_message);
}

// Método HTTP
$method = $_SERVER['REQUEST_METHOD'];

// Enrutador
$routes = [
    // Autenticación
    'GET|/' => ['AuthController', 'showLogin'],
    'GET|/login' => ['AuthController', 'showLogin'],
    'POST|/login' => ['AuthController', 'login'],
    'GET|/logout' => ['AuthController', 'logout'],
    'GET|/register' => ['AuthController', 'showRegister'],
    'POST|/auth/register' => ['AuthController', 'register'],
    'POST|/auth/verificar-clave-sucursal' => ['AuthController', 'verificarClaveSucursal'],
    'GET|/notificaciones/no-leidas' => ['NotificacionesController', 'getNoLeidas'],
    'POST|/notificaciones/marcar-leida/{id}' => ['NotificacionesController', 'marcarLeida'],
    'POST|/notificaciones/marcar-todas-leidas' => ['NotificacionesController', 'marcarTodasLeidas'],
    'POST|/auth/verificar-sucursal' => ['AuthController', 'verificarSucursal'],
    'POST|/auth/cambiar-sucursal' => ['AuthController', 'cambiarSucursal'],

    // Caja Chica
    'GET|/cajas' => ['CajaController', 'index'],
    'GET|/cajas/aprir' => ['CajaController', 'abrirPanel'],
    'POST|/cajas/abrir' => ['CajaController', 'abrir'],
    'GET|/cajas/cerrar' => ['CajaController', 'cerrarPanel'],
    'POST|/cajas/cerrar' => ['CajaController', 'cerrar'],
    'GET|/cajas/movimientos' => ['CajaController', 'movimientos'],
    'POST|/cajas/movimientos' => ['CajaController', 'addMovimiento'],

    // Dashboard
    'GET|/dashboard' => ['DashboardController', 'index'],
    'GET|/dashboard/notificaciones' => ['DashboardController', 'getNotificaciones'],
    'POST|/dashboard/notificacion/leida' => ['DashboardController', 'marcarNotificacionLeida'],

    // Productos
    'GET|/productos' => ['ProductosController', 'index'],
    'GET|/productos/create' => ['ProductosController', 'create'],
    'POST|/productos/store' => ['ProductosController', 'store'],
    'GET|/productos/edit/{id}' => ['ProductosController', 'edit'],
    'POST|/productos/update/{id}' => ['ProductosController', 'update'],
    'POST|/productos/delete/{id}' => ['ProductosController', 'delete'],
    'GET|/productos/search' => ['ProductosController', 'search'],
    'GET|/productos/stock-bajo' => ['ProductosController', 'stockBajo'],

    // Insumos
    'GET|/insumos' => ['InsumosController', 'index'],
    'GET|/insumos/create' => ['InsumosController', 'create'],
    'POST|/insumos/store' => ['InsumosController', 'store'],
    'GET|/insumos/edit/{id}' => ['InsumosController', 'edit'],
    'POST|/insumos/update/{id}' => ['InsumosController', 'update'],
    'POST|/insumos/delete/{id}' => ['InsumosController', 'delete'],
    'GET|/insumos/search' => ['InsumosController', 'search'],
    'GET|/insumos/stock-bajo' => ['InsumosController', 'stockBajo'],

    // Recetas
    'GET|/recetas' => ['RecetasController', 'index'],
    'GET|/recetas/create' => ['RecetasController', 'create'],
    'POST|/recetas/store' => ['RecetasController', 'store'],
    'GET|/recetas/edit/{id}' => ['RecetasController', 'edit'],
    'GET|/recetas/show/{id}' => ['RecetasController', 'show'],
    'POST|/recetas/update/{id}' => ['RecetasController', 'update'],
    'POST|/recetas/delete/{id}' => ['RecetasController', 'delete'],
    'POST|/recetas/calcular' => ['RecetasController', 'calcular'],
    'GET|/recetas/calcular' => ['RecetasController', 'calcular'],
    'POST|/recetas/add-insumo' => ['RecetasController', 'addInsumo'],
    'POST|/recetas/remove-insumo' => ['RecetasController', 'removeInsumo'],

    // Ventas
    'GET|/ventas' => ['VentasController', 'index'],
    'GET|/ventas/create' => ['VentasController', 'create'],
    'POST|/ventas/store' => ['VentasController', 'store'],
    'GET|/ventas/show/{id}' => ['VentasController', 'show'],
    'GET|/ventas/ticket/{id}' => ['VentasController', 'ticket'],

    // Clientes
    'GET|/clientes' => ['ClientesController', 'index'],
    'GET|/clientes/create' => ['ClientesController', 'create'],
    'POST|/clientes/store' => ['ClientesController', 'store'],
    'GET|/clientes/edit/{id}' => ['ClientesController', 'edit'],
    'POST|/clientes/update/{id}' => ['ClientesController', 'update'],
    'POST|/clientes/delete/{id}' => ['ClientesController', 'delete'],
    'GET|/clientes/show/{id}' => ['ClientesController', 'show'],
    'GET|/clientes/search' => ['ClientesController', 'search'],

    // Pedidos
    'GET|/pedidos' => ['PedidosController', 'index'],
    'GET|/pedidos/create' => ['PedidosController', 'create'],
    'POST|/pedidos/store' => ['PedidosController', 'store'],
    'GET|/pedidos/show/{id}' => ['PedidosController', 'show'],
    'POST|/pedidos/update/{id}' => ['PedidosController', 'update'],
    'POST|/pedidos/registrar-pago' => ['PedidosController', 'registrarPago'],

    // Producciones
    'GET|/producciones' => ['ProduccionesController', 'index'],
    'GET|/producciones/create' => ['ProduccionesController', 'create'],
    'POST|/producciones/store' => ['ProduccionesController', 'store'],
    'GET|/producciones/show/{id}' => ['ProduccionesController', 'show'],

    // Auditorías
    'GET|/auditorias' => ['AuditoriasController', 'index'],
    'GET|/auditorias/show/{id}' => ['AuditoriasController', 'show'],
    'POST|/auditorias/deshacer' => ['AuditoriasController', 'deshacer'],
    'GET|/auditorias/estadisticas' => ['AuditoriasController', 'estadisticas'],

    // Respaldos
    'GET|/respaldos' => ['RespaldosController', 'index'],
    'POST|/respaldos/generar' => ['RespaldosController', 'generar'],
    'POST|/respaldos/restaurar' => ['RespaldosController', 'restaurar'],
    'GET|/respaldos/descargar/{id}' => ['RespaldosController', 'descargar'],

    // Sugerencias de Compra
    'GET|/sugerencias' => ['SugerenciasController', 'index'],
    'POST|/sugerencias/generar' => ['SugerenciasController', 'generar'],
    'POST|/sugerencias/aprobar' => ['SugerenciasController', 'aprobar'],
    'POST|/sugerencias/rechazar' => ['SugerenciasController', 'rechazar'],
    'POST|/sugerencias/completar' => ['SugerenciasController', 'completar'],

    // Reportes
    'GET|/reportes' => ['ReportesController', 'index'],
    'GET|/reportes/ventas' => ['ReportesController', 'ventas'],
    'GET|/reportes/productos' => ['ReportesController', 'productos'],
    'GET|/reportes/clientes' => ['ReportesController', 'clientes'],
    'GET|/reportes/vencimientos' => ['ReportesController', 'vencimientos'],
    'GET|/reportes/compras' => ['ReportesController', 'compras'],
    'GET|/reportes/insumos' => ['ReportesController', 'insumos'],
    'GET|/reportes/producciones' => ['ReportesController', 'producciones'],
    'GET|/reportes/pedidos' => ['ReportesController', 'pedidos'],

    // Usuarios
    'GET|/usuarios' => ['UsuariosController', 'index'],
    'GET|/usuarios/perfil' => ['UsuariosController', 'perfil'],
    'GET|/usuarios/actividad' => ['UsuariosController', 'actividad'],
    'POST|/usuarios/actualizar-perfil' => ['UsuariosController', 'actualizarPerfil'],
    'POST|/usuarios/cambiar-estado' => ['UsuariosController', 'cambiarEstado'],

    // Sucursales (Admin only)
    'GET|/sucursales' => ['SucursalesController', 'index'],
    'GET|/sucursales/create' => ['SucursalesController', 'create'],
    'POST|/sucursales/store' => ['SucursalesController', 'store'],
    'GET|/sucursales/show/{id}' => ['SucursalesController', 'show'],
    'GET|/sucursales/edit/{id}' => ['SucursalesController', 'edit'],
    'POST|/sucursales/update/{id}' => ['SucursalesController', 'update'],
    'POST|/sucursales/cambiar-estado' => ['SucursalesController', 'cambiarEstado'],
    'POST|/sucursales/regenerar-clave/{id}' => ['SucursalesController', 'regenerarClave'],

    // Cálculo de Insumos
    'POST|/calculo-insumos/calcular' => ['CalculoInsumosController', 'calcularInsumos'],
    'POST|/calculo-insumos/verificar' => ['CalculoInsumosController', 'verificarDisponibilidad'],
    'GET|/recetas/list' => ['RecetasController', 'list'],

    // Predicciones
    'GET|/predicciones' => ['PrediccionesController', 'index'],
    'GET|/predicciones/data' => ['PrediccionesController', 'getDatosVentas'],
    'POST|/predicciones/generar' => ['PrediccionesController', 'generarSugerenciasAutomaticas'],

    // Proveedores
    'GET|/proveedores' => ['ProveedoresController', 'index'],
    'GET|/proveedores/create' => ['ProveedoresController', 'create'],
    'POST|/proveedores/store' => ['ProveedoresController', 'store'],
    'GET|/proveedores/edit/{id}' => ['ProveedoresController', 'edit'],
    'POST|/proveedores/update/{id}' => ['ProveedoresController', 'update'],
    'POST|/proveedores/delete/{id}' => ['ProveedoresController', 'delete'],
    'GET|/proveedores/show/{id}' => ['ProveedoresController', 'show'],
    'GET|/proveedores/insumos-sin-proveedor' => ['ProveedoresController', 'insumosSinProveedor'],

    // Compras
    'GET|/compras' => ['ComprasController', 'index'],
    'GET|/compras/create' => ['ComprasController', 'create'],
    'POST|/compras/store' => ['ComprasController', 'store'],
    'GET|/compras/show/{id}' => ['ComprasController', 'show'],

    // Control de Lotes / Vencimientos
    'GET|/lotes' => ['LotesController', 'index'],
    'POST|/lotes/ajustar' => ['LotesController', 'ajustar'],

    // System Config
    'GET|/config/refresh-tasa' => ['ConfigController', 'refreshTasa'],
];

// Buscar ruta coincidente
$matched = false;
$params = [];

foreach ($routes as $route => $handler) {
    list($routeMethod, $routePath) = explode('|', $route);

    if ($routeMethod !== $method) {
        continue;
    }

    $params = matchRoute($routePath, $path);

    if ($params !== false) {
        $matched = true;
        $controllerName = 'App\\Controllers\\' . $handler[0];
        $methodName = $handler[1];

        try {
            if (class_exists($controllerName)) {
                $controller = new $controllerName();

                if (method_exists($controller, $methodName)) {
                    call_user_func_array([$controller, $methodName], $params);
                } else {
                    http_response_code(500);
                    if ($_SERVER['HTTP_ACCEPT'] === 'application/json' || strpos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false) {
                        header('Content-Type: application/json');
                        echo json_encode(['success' => false, 'message' => "Método no encontrado: {$methodName}"]);
                    } else {
                        echo "Método no encontrado: {$methodName}";
                    }
                }
            } else {
                http_response_code(500);
                if ($_SERVER['HTTP_ACCEPT'] === 'application/json' || strpos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false) {
                    header('Content-Type: application/json');
                    echo json_encode(['success' => false, 'message' => "Controlador no encontrado: {$controllerName}"]);
                } else {
                    echo "Controlador no encontrado: {$controllerName}";
                }
            }
        } catch (Exception $e) {
            http_response_code(500);
            if ($_SERVER['HTTP_ACCEPT'] === 'application/json' || strpos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false) {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
            } else {
                echo "Error: " . $e->getMessage();
            }
        }

        break;
    }
}

// Si no se encontró ruta, mostrar 404
if (!$matched) {
    http_response_code(404);
    echo "<!DOCTYPE html>
    <html lang='es'>
    <head>
        <meta charset='UTF-8'>
        <meta name='viewport' content='width=device-width, initial-scale=1.0'>
        <title>404 - Página no encontrada</title>
        <style>
            body {
                font-family: Arial, sans-serif;
                display: flex;
                justify-content: center;
                align-items: center;
                height: 100vh;
                margin: 0;
                background: linear-gradient(135deg, #D4A574 0%, #8B6F47 100%);
                color: white;
            }
            .error-container {
                text-align: center;
            }
            .error-code {
                font-size: 8rem;
                font-weight: bold;
                margin: 0;
            }
            .error-message {
                font-size: 1.5rem;
                margin: 1rem 0;
            }
            .error-link {
                display: inline-block;
                margin-top: 2rem;
                padding: 1rem 2rem;
                background: white;
                color: #8B6F47;
                text-decoration: none;
                border-radius: 8px;
                font-weight: bold;
            }
        </style>
    </head>
    <body>
        <div class='error-container'>
            <h1 class='error-code'>404</h1>
            <p class='error-message'>Página no encontrada</p>
            <p>La ruta solicitada no existe en el sistema.</p>
            <p>Ruta: {$path}</p>
            <a href='/dashboard' class='error-link'>Volver al Dashboard</a>
        </div>
    </body>
    </html>";
}
