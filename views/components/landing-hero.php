<?php

declare(strict_types=1);

use SIPAN\App;

?>

<section style="background: url('./assets/img/hero/hero-bg.svg') center/cover">
  <div class="container py-5">
    <div class="row row-gap-3 align-items-center">
      <div class="col-md-7">
        <h1 class="text-danger">Sistema Integral para Panaderías</h1>
        <p class="display-4 font-serif">Optimiza, controla y haz crecer tu panadería con SIPAN</p>
        <p>
          Automatiza tus procesos, controla tu inventario y aumenta tus ventas
          con nuestro sistema integral diseñado específicamente para panaderías.
        </p>
        <div class="btn-group btn-group-lg gap-3 w-100">
          <a class="btn btn-danger" href="#demo">Solicitar Demo</a>
          <a class="btn btn-outline-danger" href="#features">
            Ver Características
          </a>
        </div>
      </div>
      <div class="col-md">
        <?php App::renderComponent('hero-carousel') ?>
      </div>
    </div>
  </div>
</section>
