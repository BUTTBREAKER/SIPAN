<?php

declare(strict_types=1);

namespace SIPAN\Controllers;

use SIPAN\App;

final readonly class DashboardController
{
  static function showDashboard(): void
  {
    App::renderPage('dashboard', 'Administración', 'dashboard-layout');
  }
}
