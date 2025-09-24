<?php

declare(strict_types=1);

use SIPAN\App;

$linksLists = [
  [
    'title' => 'Company',
    'links' => [
      ['href' => '#!', 'label' => 'About'],
      ['href' => '#!', 'label' => 'Careers'],
      ['href' => '#!', 'label' => 'Mobile'],
    ],
  ],
  [
    'title' => 'Contact',
    'links' => [
      ['href' => '#!', 'label' => 'Help/FAQ'],
      ['href' => '#!', 'label' => 'Press'],
      ['href' => '#!', 'label' => 'Affiliate'],
    ],
  ],
  [
    'title' => 'More',
    'links' => [
      ['href' => '#!', 'label' => 'Airlinefees'],
      ['href' => '#!', 'label' => 'Airline'],
      ['href' => '#!', 'label' => 'Low fare tips'],
    ],
  ],
];

?>

<footer class="container text-center">
  <div class="row row-gap-3">
    <div class="col-md-6 col-xl-3 order-first d-flex flex-column justify-content-between gap-3">
      <img src="./assets/img/logo.png" class="w-50 m-auto" />
      <p class="m-0 lead">
        Book your trip in minute, get full Control for much longer.
      </p>
    </div>
    <?php foreach ($linksLists as $linksList) : ?>
      <div class="col-6 col-md-4 col-xl-2 order-last d-flex flex-column justify-content-between gap-3">
        <h2 class="m-0"><?= $linksList['title'] ?></h2>
        <nav class="nav flex-column">
          <?php foreach ($linksList['links'] as $link) : ?>
            <a class="nav-link px-0 link-danger" href="<?= $link['href'] ?>">
              <?= $link['label'] ?>
            </a>
          <?php endforeach ?>
        </nav>
      </div>
    <?php endforeach ?>

    <div class="col-md order-xl-last order-md-first d-flex flex-column justify-content-between gap-3">
      <?php App::renderComponent('social-links') ?>
      <h2 class="m-0">Discover our app</h2>
      <?php App::renderComponent('apps-links') ?>
    </div>
  </div>
  <p class="my-5 text-center">UPTM © <?= date('Y') ?></p>
</footer>
