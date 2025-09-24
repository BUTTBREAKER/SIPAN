<?php

declare(strict_types=1);

$services = [
  [
    'icon' => 'icon1.png',
    'title' => 'Punto de Venta',
    'description' => 'Gestiona ventas, facturas y tickets de manera rápida y eficiente.'
  ],
  [
    'icon' => 'icon2.png',
    'title' => 'Control de Inventario',
    'description' => 'Seguimiento de materias primas y productos terminados en tiempo real.'
  ],
  [
    'icon' => 'icon3.png',
    'title' => 'Gestión de Producción',
    'description' => 'Planifica tu producción diaria y optimiza tus recetas.'
  ],
  [
    'icon' => 'icon4.png',
    'title' => 'Reportes y Análisis',
    'description' => 'Informes detallados de ventas, costos y rentabilidad.'
  ]
];

?>

<section class="text-center" id="service">
  <img src="./assets/img/category/shape.svg" class="position-absolute end-0" />
  <h2>Funcionalidades</h2>
  <h3 class="display-1 font-cursive mb-5">
    Soluciones completas para tu panadería
  </h3>
  <div class="row row-cols-1 row-cols-sm-2 row-cols-xl-4 row-gap-3">
    <?php foreach ($services as $service) : ?>
      <div class="col">
        <article class="card card-body shadow-lg z-0">
          <picture>
            <img src="./assets/img/category/<?= $service['icon'] ?>" />
          </picture>
          <h3 class="card-title"><?= $service['title'] ?></h3>
          <p class="card-text"><?= $service['description'] ?></p>
        </article>
      </div>
    <?php endforeach ?>
  </div>
</section>
