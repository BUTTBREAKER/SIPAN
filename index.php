<?php

use SIPAN\App;

$autoloadPath = __DIR__ . '/vendor/autoload.php';

if (!file_exists($autoloadPath)) {
  require_once __DIR__ . '/views/pages/uninitialized.php';

  exit;
}

require_once $autoloadPath;
require_once __DIR__ . '/app/configs.php';
require_once __DIR__ . '/app/routes.php';

App::start();
