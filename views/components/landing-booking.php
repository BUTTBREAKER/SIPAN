<?php

declare(strict_types=1);

$steps = [
  [
    'icon' => 'selection.svg',
    'title' => 'Solicita una Demo',
    'description' => 'Conoce todas las funcionalidades de SIPAN con una demostración personalizada.',
    'bg' => 'bg-primary'
  ],
  [
    'icon' => 'water-sport.svg',
    'title' => 'Configuración Inicial',
    'description' => 'Configuramos el sistema según las necesidades específicas de tu panadería.',
    'bg' => 'bg-danger'
  ],
  [
    'icon' => 'taxi.svg',
    'title' => 'Capacitación y Soporte',
    'description' => 'Te acompañamos en la implementación con capacitación completa y soporte continuo.',
    'bg' => 'bg-info'
  ],
];

?>

<section id="booking" class="row row-cols-1 row-cols-lg-2 row-gap-3">
  <div class="col">
    <h3>Simple y Rápido</h3>
    <h2 class="display-4 font-serif mb-5">Comienza a usar SIPAN en 3 pasos</h2>
    <ul class="list-group shadow-lg">
      <?php foreach ($steps as $step) : ?>
        <li class="list-group-item d-flex align-items-start gap-3">
          <picture class="<?= $step['bg'] ?> p-3 rounded-1 m-0">
            <img src="./assets/img/steps/<?= $step['icon'] ?>" class="img-fluid" />
          </picture>
          <div class="flex-1">
            <h4><?= $step['title'] ?></h4>
            <p class="m-0"><?= $step['description'] ?></p>
          </div>
        </li>
      <?php endforeach ?>
    </ul>
  </div>
  <div class="col">
    <article class="card shadow-lg">
      <img class="card-img-top" src="./assets/img/steps/booking-img.jpg" />
      <div class="card-body">
        <h3>Demo Gratuita</h3>
        <p>Conoce SIPAN en acción</p>
        <button class="btn btn-danger">Solicitar Ahora</button>
      </div>
    </article>
  </div>
</section>
