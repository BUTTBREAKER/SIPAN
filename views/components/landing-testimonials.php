<?php

declare(strict_types=1);

$testimonials = [
  [
    'text' => 'SIPAN transformó completamente la gestión de mi panadería. Ahora tengo control total sobre el inventario y las ventas.',
    'author' => 'Carlos Ramírez',
    'business' => 'Panadería La Espiga Dorada',
    'image' => './assets/img/testimonial/author.png'
  ],
  [
    'text' => 'El sistema es muy intuitivo y el soporte técnico es excelente. Ha mejorado significativamente nuestra productividad.',
    'author' => 'María González',
    'business' => 'Panificadora San José',
    'image' => './assets/img/testimonial/author2.png'
  ],
  [
    'text' => 'La implementación fue rápida y la capacitación muy completa. SIPAN es exactamente lo que necesitábamos.',
    'author' => 'Roberto Sánchez',
    'business' => 'Panadería El Horno Feliz',
    'image' => './assets/img/testimonial/author3.png'
  ],
];

$carouselId = uniqid();

?>

<section id="testimonial" class="row row-cols-1 row-cols-xl-2 row-gap-3">
  <div class="col">
    <h2>Testimonios</h2>
    <h3 class="display-4 font-serif m-0">Lo que dicen nuestros clientes</h3>
  </div>
  <div class="col">
    <div
      class="carousel slide overflow-x-hidden card card-body shadow-lg"
      id="<?= $carouselId ?>"
      data-bs-ride="carousel">
      <div class="carousel-inner">
        <?php foreach ($testimonials as $index => $testimonial) : ?>
          <div class="carousel-item <?= $index !== 0 ?: 'active' ?>">
            <figure class="float-start me-3 text-center">
              <img
                class="rounded-circle"
                src="<?= $testimonial['image'] ?>"
                height="65"
                width="65" />
              <figcaption><?= $testimonial['author'] ?></figcaption>
            </figure>
            <p>"<?= $testimonial['text'] ?>"</p>
            <em class="d-block text-end"><?= $testimonial['business'] ?></em>
          </div>
        <?php endforeach ?>
      </div>
    </div>
  </div>
</section>
