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

<footer class="container">
  <div class="row row-gap-4">
    <div class="col-12 col-md-7 col-lg-3 order-first">
      <img src="./assets/img/logo.png" width="150" class="img-fluid mb-4" />
      <p class="text-secondary">
        Book your trip in minute, get full Control for much longer.
      </p>
    </div>
    <?php foreach ($linksLists as $linksList): ?>
      <div class="col-lg-2 col-md-4 order-last">
        <h4 class="mb-4"><?= $linksList['title'] ?></h4>
        <ul class="list-unstyled d-grid gap-3 m-0">
          <?php foreach ($linksList['links'] as $link): ?>
            <li>
              <a class="link-900" href="<?= $link['href'] ?>">
                <?= $link['label'] ?>
              </a>
            </li>
          <?php endforeach ?>
        </ul>
      </div>
    <?php endforeach ?>

    <div class="col-12 col-md-5 col-lg-3 order-lg-last order-md-1">
      <?php App::renderComponent('social-links') ?>
      <h4 class="my-4">Discover our app</h4>
      <?php App::renderComponent('apps-links') ?>
    </div>
  </div>
  <p class="my-5 text-center">UPTM © <?= date('Y') ?></p>
</footer>
