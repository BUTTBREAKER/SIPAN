<?php

declare(strict_types=1);

$benefits = [
  [
    'title' => 'Mayor Eficiencia',
    'description' => 'Reduce tiempos y optimiza procesos',
    'image' => './assets/img/dest/dest1.jpg',
    'alt' => 'Eficiencia'
  ],
  [
    'title' => 'Control Total',
    'description' => 'Gestión integral de tu negocio',
    'image' => './assets/img/dest/dest2.jpg',
    'alt' => 'Control'
  ],
  [
    'title' => 'Impulsa el Crecimiento',
    'description' => 'Decisiones basadas en datos reales',
    'image' => './assets/img/dest/dest3.jpg',
    'alt' => 'Crecimiento'
  ],
];

?>

<section class="text-center" id="destination">
  <img src="./assets/img/dest/shape.svg" class="position-absolute bottom-0 end-0" />
  <h2>Beneficios</h2>
  <h3 class="display-1 font-cursive mb-5">Por qué elegir SIPAN</h3>
  <div class="row row-cols-1 row-cols-md-3 row-gap-3">
    <?php foreach ($benefits as $benefit) : ?>
      <div class="col">
        <div class="card shadow-lg">
          <img class="card-img-top" src="<?= $benefit['image'] ?>" />
          <div class="card-body">
            <h4 class="card-title"><?= $benefit['title'] ?></h4>
            <p class="card-text"><?= $benefit['description'] ?></p>
          </div>
        </div>
      </div>
    <?php endforeach ?>
  </div>
</section>
