<?php

declare(strict_types=1);

$carouselImages = [
  './assets/img/hero/hero-img.png',
  './assets/img/hero/hero-img2.png',
  './assets/img/hero/hero-img3.png',
];

?>

<div class="carousel slide carousel-fade" data-bs-ride="carousel">
  <div class="carousel-inner">
    <?php foreach ($carouselImages as $index => $imagePath): ?>
      <div class="carousel-item <?= $index !== 0 ?: 'active' ?>">
        <img src="<?= $imagePath ?>" height="500" width="100%" class="object-fit-contain" />
      </div>
    <?php endforeach; ?>
  </div>
</div>
