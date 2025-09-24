<?php

use SIPAN\App;

?>

<style>
  .carousel-fade .carousel-inner {
    position: relative;
    height: 100%;
  }

  .carousel-fade .carousel-item {
    position: absolute;
    top: 0;
    left: 0;
    height: 100%;
    width: 100%;
    opacity: 0;
    transition: opacity 1s ease-in-out;
  }

  .carousel-fade .carousel-item.active {
    position: relative;
    opacity: 1;
  }

  .hero-img {
    object-fit: contain;
    height: auto;
    max-height: 500px;
    /* Ajusta esto según tus necesidades */
  }

  /* Asegura que no haya saltos durante la transición */
  .carousel-inner>.carousel-item {
    display: block;
    transform: none;
  }

  /* Optimización para evitar parpadeos */
  .carousel {
    transform: translateZ(0);
    backface-visibility: hidden;
  }
</style>

<?php App::renderComponent('landing-hero') ?>
<?php App::renderComponent('landing-services') ?>
<?php App::renderComponent('landing-destinations') ?>
<?php App::renderComponent('landing-booking') ?>
<?php App::renderComponent('landing-testimonials') ?>
