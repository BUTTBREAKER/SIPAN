<?php
// Router para PHP Built-in Server

$uri = urldecode(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));

// Si es un archivo estático, servirlo directamente
if ($uri !== '/' && file_exists(__DIR__ . $uri)) {
    return false;
}

// Redirigir todo a index.php
$_SERVER['SCRIPT_NAME'] = '/index.php';
require __DIR__ . '/index.php';
