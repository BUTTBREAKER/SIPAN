<?php

declare(strict_types=1);

$socialLinks = [
  ['id' => 'facebook', 'icon' => 'fab fa-facebook-f', 'href' => '#!'],
  ['id' => 'instagram', 'icon' => 'fab fa-instagram', 'href' => '#!'],
  ['id' => 'twitter', 'icon' => 'fab fa-twitter', 'href' => '#!'],
];

?>

<div class="d-flex gap-3">
  <?php foreach ($socialLinks as $link): ?>
    <a
      class="icon-item shadow-lg"
      id="<?= $link['id'] ?>"
      href="<?= $link['href'] ?>">
      <i class="<?= $link['icon'] ?>"></i>
    </a>
  <?php endforeach ?>
</div>
