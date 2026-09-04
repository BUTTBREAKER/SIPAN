<?php

// Cargar variables de entorno
require_once __DIR__ . '/../app/Helpers/Environment.php';
use App\Helpers\Environment;

Environment::load();

return [
    // Rutas
    'base_path' => dirname(__DIR__),
    'public_path' => dirname(__DIR__) . '/public',
    'upload_path' => dirname(__DIR__) . '/public/assets/images/uploads',

    // Paginación
    'per_page' => (int) Environment::get('PAGINATION_PER_PAGE', 20),
];
