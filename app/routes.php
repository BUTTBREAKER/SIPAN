<?php

use SIPAN\App;
use SIPAN\Controllers\DashboardController;
use SIPAN\Controllers\LandingController;
use SIPAN\Controllers\ProfileController;
use SIPAN\Middlewares\EnsureUserIsNotLoggedMiddleware;

App::route('GET /', [LandingController::class, 'showLanding']);

// 📌 Rutas de autenticación
App::group('/ingresar', static function (): void {
  App::route('GET /', [ProfileController::class, 'showLogin']);
  App::route('POST /', [ProfileController::class, 'handleLogin']);
}, [EnsureUserIsNotLoggedMiddleware::class]);

App::group('/registrarse', static function (): void {
  App::route('GET /', [ProfileController::class, 'showRegister']);
  App::route('POST /', [ProfileController::class, 'handleRegister']);
}, [EnsureUserIsNotLoggedMiddleware::class]);

// 📌 Rutas protegidas con autenticación
App::group('/administracion', static function (): void {
  App::route('GET /', [DashboardController::class, 'showDashboard']);
  App::route('POST /salir', [ProfileController::class, 'handleLogout']);
  App::route('GET /perfil', [ProfileController::class, 'showProfile']);
}, [/*EnsureUserIsLoggedMiddleware::class*/]);
