<?php

declare(strict_types=1);

namespace SIPAN\Controllers;

use SIPAN\App;

final readonly class ProductApiController
{
  static function index(): void
  {
    $products = db()->select('productos')->all();

    App::json($products);
  }
}
