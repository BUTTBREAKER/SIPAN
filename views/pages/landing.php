<?php

use SIPAN\App;

?>

<?php App::renderComponent('landing-hero') ?>

<div class="d-grid gap-5 my-5 container">
  <?php App::renderComponent('landing-services') ?>
  <?php App::renderComponent('landing-destinations') ?>
  <?php App::renderComponent('landing-booking') ?>
  <?php App::renderComponent('landing-testimonials') ?>
</div>
