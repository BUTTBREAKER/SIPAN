<?php

// Cargar variables de entorno
require_once __DIR__ . '/../app/Helpers/Environment.php';
use App\Helpers\Environment;

Environment::load();

return [
    // Base de datos
    'db_host' => Environment::get('DB_HOST'),
    'db_name' => Environment::get('DB_NAME'),
    'db_user' => Environment::get('DB_USER'),
    'db_pass' => Environment::get('DB_PASS'),
    
    // JWT
    'jwt_secret' => Environment::get('JWT_SECRET'),
    'jwt_expiration' => (int) Environment::get('JWT_EXPIRATION', 86400), // 24 horas
    
    // Aplicación
    'app_name' => Environment::get('APP_NAME', 'SIPAN'),
    'app_url' => Environment::get('APP_URL', 'http://localhost:8000'),
    'app_env' => Environment::get('APP_ENV', 'production'),
    'app_debug' => Environment::get('APP_DEBUG', false),
    'timezone' => Environment::get('TIMEZONE', 'America/Lima'),
    
    // Sesión
    'session_name' => Environment::get('SESSION_NAME', 'SIPAN_SESSION'),
    'session_lifetime' => (int) Environment::get('SESSION_LIFETIME', 86400),
    
    // Rate Limiting
    'rate_limit_login_max_attempts' => (int) Environment::get('RATE_LIMIT_LOGIN_MAX_ATTEMPTS', 5),
    'rate_limit_login_window' => (int) Environment::get('RATE_LIMIT_LOGIN_WINDOW', 300), // 5 minutos
    
    // Rutas
    'base_path' => dirname(__DIR__),
    'public_path' => dirname(__DIR__) . '/public',
    'upload_path' => dirname(__DIR__) . '/public/assets/images/uploads',
    
    // Paginación
    'per_page' => (int) Environment::get('PAGINATION_PER_PAGE', 20),
];
