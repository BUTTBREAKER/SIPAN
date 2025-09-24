<?php

declare(strict_types=1);

$navbarCollapseId = uniqid();

$navLinks = [
  ['href' => '#service', 'label' => 'Service'],
  ['href' => '#destination', 'label' => 'Destination'],
  ['href' => '#booking', 'label' => 'Booking'],
  ['href' => '#testimonial', 'label' => 'Testimonial'],
];

?>

<nav id="<?= $navbarId ?>" class="navbar navbar-expand-xl sticky-top">
  <div class="container">
    <a
      href="./"
      data-bs-toggle="tooltip"
      title="Sistema Integral para Panaderías">
      <img src="./assets/img/logo.png" height="64" />
    </a>
    <button
      class="navbar-toggler"
      data-bs-toggle="collapse"
      data-bs-target="#<?= $navbarCollapseId ?>">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div
      class="collapse navbar-collapse"
      id="<?= $navbarCollapseId ?>">
      <hr class="d-xl-none" />
      <ul class="navbar-nav ms-auto gap-3 align-items-xl-center">
        <?php foreach ($navLinks as $link) : ?>
          <li>
            <a class="nav-link" href="<?= $link['href'] ?>">
              <?= $link['label'] ?>
            </a>
          </li>
        <?php endforeach ?>

        <li class="btn-group">
          <a
            class="btn btn-danger"
            href="./ingresar">
            Iniciar sesión
          </a>
          <a
            class="btn btn-danger"
            href="./registrarse">
            Registrarse
          </a>
        </li>
      </ul>
    </div>
  </div>
</nav>
