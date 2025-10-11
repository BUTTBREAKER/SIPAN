<?php

namespace SIPAN\Controllers;

use SIPAN\App;

final readonly class LandingController
{
  static function showLanding(): void
  {
    App::renderPage('landing', 'SIPAN', 'landing-layout');
  }
}
