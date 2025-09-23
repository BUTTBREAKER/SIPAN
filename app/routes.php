<?php

use SIPAN\App;
use SIPAN\Controllers\DashboardController;
use SIPAN\Controllers\LandingController;
use SIPAN\Controllers\ProfileController;
use SIPAN\Middlewares\EnsureUserIsNotLoggedMiddleware;

App::route('GET /', [LandingController::class, 'showLanding']);

App::group('/api', static function (): void {
  App::route('POST /ingresar', static function (): void {
    App::halt(401);
  });

  App::route('POST /registrarse', static function (): void {
    App::halt(409);
  });

  App::group('/productos', static function (): void {
    App::route('GET /', static function (): void {
      App::json([]);
    });
  });
});

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
