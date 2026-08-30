<?php

declare(strict_types=1);

use flight\Container;
use Leaf\Http\Session;
use Psr\Http\Message\ServerRequestInterface;

require_once __DIR__ . '/../bootstrap/app.php';

// Habilitar buffering de salida
ob_start();

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
$path = $request->getUri()->getPath();
$path = str_replace('/delivery', '', $path);
$path = rtrim($path, '/') ?: '/';
$method = $request->getMethod();

// Función simple de enrutamiento
$deliveryMatchRoute = function (string $routePath, string $requestPath) {
    $pattern = preg_replace('/\{([a-zA-Z0-9_]+)\}/', '([^/]+)', $routePath);
    $pattern = "@^{$pattern}$@D";

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
    [$routeMethod, $routePath] = explode('|', $route);

    if ($routeMethod !== $method) {
        continue;
    }

    $params = $deliveryMatchRoute($routePath, $path);

    if ($params === false) {
        continue;
    }

    $matched = true;

    // Incluir manualmente para evitar problemas de namespace si no está en composer
    $controllerFile = __DIR__ . "/controllers/$handler[0].php";

    if (!file_exists($controllerFile)) {
        exit("Controller file not found: $controllerFile");
    }

    $controllerName = "\\Delivery\\Controllers\\$handler[0]";

    if (!class_exists($controllerName)) {
        exit("Class not found: $controllerName");
    }

    $controller = new $controllerName();

    if (!method_exists($controller, $handler[1])) {
        exit("Method not found: $handler[1]");
    }

    call_user_func_array([$controller, $handler[1]], $params);

    break;
}

if (!$matched) {
    http_response_code(404);
    echo "404 - Not Found (Delivery App)";
}
