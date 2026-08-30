<?php

declare(strict_types=1);

use flight\Container;
use Leaf\Http\Session;
use Psr\Http\Message\ServerRequestInterface;

require_once __DIR__ . '/../bootstrap/app.php';

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

$request = Container::getInstance()->get(ServerRequestInterface::class);

// Iniciar sesión
$isSecure = (
    strtolower($request->getUri()->getScheme()) === 'https'
    || strtolower($request->getHeaderLine('X_FORWARDED_PROTO')) === 'https'
);

$sessionParams = session_get_cookie_params();

if (session_status() === PHP_SESSION_NONE) {
    $sessionParams = [
        'lifetime' => (int) getenv('session_lifetime'),
        'secure' => $isSecure,
        'httponly' => true,
        'samesite' => 'Strict',
    ] + session_get_cookie_params();

    session_set_cookie_params($sessionParams);

    $baseSessionName = getenv('session_name');
    $finalSessionName = "{$baseSessionName}_DELIVERY";

    session_name($finalSessionName);
    Session::start();
}

// Obtener ruta (relativa a /delivery)
$request_uri = $_SERVER['REQUEST_URI'];
$path = parse_url($request_uri, PHP_URL_PATH);
$path = str_replace('/delivery', '', $path);
$path = rtrim($path, '/') ?: '/';
$method = $_SERVER['REQUEST_METHOD'];

// Función simple de enrutamiento
$deliveryMatchRoute = function (string $routePath, string $requestPath) {
    $pattern = preg_replace('/\{([a-zA-Z0-9_]+)\}/', '([^/]+)', $routePath);
    $pattern = "@^" . $pattern . "$@D";

    if (preg_match($pattern, $requestPath, $matches)) {
        array_shift($matches);
        return $matches;
    }
    return false;
};

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

    if ($routeMethod !== $method) {
        continue;
    }

    $params = $deliveryMatchRoute($routePath, $path);

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
