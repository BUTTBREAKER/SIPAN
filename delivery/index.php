<?php

declare(strict_types=1);

use App\Route;
use flight\Container;
use Leaf\Http\Session;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ServerRequestInterface;

require_once __DIR__ . '/../bootstrap/app.php';

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

/** @var Route[] */
$routes = require __DIR__ . '/../routes/delivery.php';

// Enrutador
foreach ($routes as $route) {
    if (!$route->matchRequestMethod($request)) {
        continue;
    }

    $params = $route->getParamsFromUri($request->getUri()->withPath($path));

    if ($params === false) {
        continue;
    }

    $callable = $route->getCallable();
    ob_start();
    $callable($params);

    $response = Container::getInstance()
        ->get(ResponseFactoryInterface::class)
        ->createResponse();

    $response->getBody()->write(ob_get_clean());

    http_response_code($response->getStatusCode());

    foreach ($response->getHeaders() as $name => $values) {
        foreach ($values as $value) {
            header("$name: $value", false);
        }
    }

    echo $response->getBody();

    return;
}

http_response_code(404);
echo "404 - Not Found (Delivery App)";
