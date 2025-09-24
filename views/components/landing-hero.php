<?php

declare(strict_types=1);

use SIPAN\App;

?>

<section style="background: url('./assets/img/hero/hero-bg.svg') center/cover">
  <div class="container">
    <div class="row align-items-center">
      <div class="col-md-7">
        <h4 class="text-danger">Sistema Integral para Panaderías</h4>
        <h1 class="display-1 font-cursive">Optimiza, controla y haz crecer tu panadería con SIPAN</h1>
        <p>
          Automatiza tus procesos, controla tu inventario y aumenta tus ventas
          con nuestro sistema integral diseñado específicamente para panaderías.
        </p>
        <div class="btn-group btn-group-lg gap-3">
          <a class="btn btn-primary" href="#demo">
            Solicitar Demo
          </a>
          <a class="btn btn-outline-primary" href="#features">
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
