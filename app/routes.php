<?php

use SIPAN\App;
use SIPAN\Controllers\LandingController;
use SIPAN\Controllers\AuthController;
use SIPAN\Middlewares\EnsureUserIsLoggedMiddleware;
use SIPAN\Middlewares\EnsureUserIsNotLoggedMiddleware;

// 📌 Ruta principal (Landing Page)
App::route('GET /', [LandingController::class, 'showLanding']);

// 📌 Rutas de autenticación
App::group('/ingresar', function (): void {
    App::route('GET /', function () { App::renderPage('login', 'Iniciar Sesión', 'auth-layout'); });
    App::route('POST /', [AuthController::class, 'login']);
}, [EnsureUserIsNotLoggedMiddleware::class]);

App::group('/registrarse', function (): void {
    App::route('GET /', function () { App::renderPage('register', 'Registro', 'auth-layout'); });
    App::route('POST /', [AuthController::class, 'register']);
}, [EnsureUserIsNotLoggedMiddleware::class]);

// 📌 Rutas protegidas con autenticación
App::group('/app', function (): void {
    App::route('POST /logout', [AuthController::class, 'logout']);
    App::route('GET /perfil', function () { App::renderPage('perfil', 'Perfil', 'main-layout'); });
}, [EnsureUserIsLoggedMiddleware::class]);
