<?php

use SIPAN\App;

if (!file_exists('vendor/autoload.php')) {
  require_once __DIR__ . '/views/pages/uninitialized.php';

  exit;
}

require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/app/configs.php';
require_once __DIR__ . '/app/routes.php';

App::start();
