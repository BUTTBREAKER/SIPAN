<?php

declare(strict_types=1);

?>

<div class="table-responsive">
  <table class="table table-borderless table-hover table-striped text-nowrap" data-bs-theme="dark">
    <thead>
      <tr>
        <th>ID</th>
        <th>Nombre</th>
        <th>Stock</th>
        <th>Precio</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($products as $product) : ?>
        <tr>
          <td><?= $product['id'] ?></td>
          <td><?= $product['nombre'] ?></td>
          <td><?= $product['stock_actual'] ?></td>
          <td><?= $product['precio_actual'] ?></td>
        </tr>
      <?php endforeach ?>
    </tbody>
  </table>
</div>
