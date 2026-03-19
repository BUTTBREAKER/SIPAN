<?php

use Symfony\Component\Dotenv\Dotenv;

require_once __DIR__ . '/../vendor/autoload.php';

// Habilitar buffering de salida 
ob_start();

// Autocargador simple para el namespace Delivery\
spl_autoload_register(function ($class) {
    $prefix = 'Delivery\\';
    $base_dir = __DIR__ . '/';
    
    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        return;
    }
    
    $relative_class = substr($class, $len);
    // Cambiar barras invertidas por barras normales y asegurarse de que la carpeta esté en minúscula (convención local)
    // Ejemplo: Middleware\AuthMiddleware -> middleware/AuthMiddleware.php
    $parts = explode('\\', $relative_class);
    if (count($parts) > 1) {
        $parts[0] = strtolower($parts[0]);
    }
    $file = $base_dir . implode('/', $parts) . '.php';

    if (file_exists($file)) {
        require $file;
    }
});

// Cargar configuración
(new Dotenv())->load(__DIR__ . '/../.env.example', __DIR__ . '/../.env');

// Configuración de errores
error_reporting(E_ALL);
ini_set('display_errors', filter_var($_ENV['app_debug'], FILTER_VALIDATE_BOOL));

// Iniciar sesión
$isSecure = @$_SERVER['HTTPS'] === 'on' || @$_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https';
$sessionParams = session_get_cookie_params();

if (session_status() === PHP_SESSION_NONE) {
    session_set_cookie_params([
        'lifetime' => $_ENV['session_lifetime'] ?? 86400,
        'path' => '/', // Importante: path global para compartir sesión con app principal
        'domain' => $sessionParams['domain'],
        'secure' => $isSecure,
        'httponly' => true,
        'samesite' => 'Lax'
    ]);
    session_name('SIPAN_DELIVERY_SESSION');
    session_start();
}

// Obtener ruta (relativa a /delivery)
$request_uri = $_SERVER['REQUEST_URI'];
$path = parse_url($request_uri, PHP_URL_PATH);
$path = str_replace('/delivery', '', $path);
$path = rtrim($path, '/') ?: '/';
$method = $_SERVER['REQUEST_METHOD'];

// Función simple de enrutamiento
// Función simple de enrutamiento
function deliveryMatchRoute($routePath, $requestPath) {
    $pattern = preg_replace('/\{([a-zA-Z0-9_]+)\}/', '([^/]+)', $routePath);
    $pattern = "@^" . $pattern . "$@D";
    
    if (preg_match($pattern, $requestPath, $matches)) {
        array_shift($matches);
        return $matches;
    }
    return false;
}

// Definir rutas de Delivery
$routes = [
    'GET|/' => ['AuthController', 'showLogin'],
    'GET|/login' => ['AuthController', 'showLogin'],
    'POST|/login' => ['AuthController', 'login'],
    'GET|/logout' => ['AuthController', 'logout'],
    
    'GET|/dashboard' => ['PedidosController', 'dashboard'],
    'GET|/api/dashboard' => ['PedidosController', 'apiDashboard'],
    'GET|/pedido/{id}' => ['PedidosController', 'show'],
    'POST|/pedido/{id}/estado' => ['PedidosController', 'updateEstado'],
    'POST|/pedido/{id}/cobro' => ['PedidosController', 'registrarCobro'],
    'GET|/historial' => ['PedidosController', 'historial'],
    'GET|/estadisticas' => ['PedidosController', 'estadisticas'],
];

$matched = false;

// Enrutador
foreach ($routes as $route => $handler) {
    list($routeMethod, $routePath) = explode('|', $route);

    if ($routeMethod !== $method) continue;

    $params = deliveryMatchRoute($routePath, $path);

    if ($params !== false) {
        $matched = true;
        
        // Incluir manualmente para evitar problemas de namespace si no está en composer
        $controllerFile = __DIR__ . '/controllers/' . $handler[0] . '.php';
        if (file_exists($controllerFile)) {
            require_once $controllerFile;
            $controllerName = 'Delivery\\Controllers\\' . $handler[0];
            
            if (class_exists($controllerName)) {
                $controller = new $controllerName();
                if (method_exists($controller, $handler[1])) {
                    call_user_func_array([$controller, $handler[1]], $params);
                } else {
                    die("Method not found: " . $handler[1]);
                }
            } else {
                die("Class not found: " . $controllerName);
            }
        } else {
            die("Controller file not found: " . $controllerFile);
        }
        break;
    }
}

if (!$matched) {
    http_response_code(404);
    echo "404 - Not Found (Delivery App)";
}
