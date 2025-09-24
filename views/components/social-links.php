<?php

declare(strict_types=1);

$socialLinks = [
  ['icon' => 'fab fa-facebook-f', 'href' => '#!'],
  ['icon' => 'fab fa-instagram', 'href' => '#!'],
  ['icon' => 'fab fa-twitter', 'href' => '#!'],
];

?>

<nav class="nav gap-5 justify-content-between">
  <?php foreach ($socialLinks as $link): ?>
    <a
      class="nav-link shadow-lg rounded-pill link-danger"
      href="<?= $link['href'] ?>">
      <i class="<?= $link['icon'] ?>"></i>
    </a>
  <?php endforeach ?>
</nav>
