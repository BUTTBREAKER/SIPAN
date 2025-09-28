<?php

use SIPAN\App;

$navbarId = uniqid();

?>

<!DOCTYPE html>
<html>

<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width" />
  <title><?= $title ?></title>
  <base href="<?= str_replace('index.php', '', $_SERVER['SCRIPT_NAME']) ?>" />
  <link rel="icon" href="./assets/img/favicon.png" />
  <link rel="stylesheet" href="./assets/dist/layouts/landing.css" />
</head>

<body>
  <?php App::renderComponent('landing-navbar', compact('navbarId')) ?>
  <div data-bs-spy="scroll" data-bs-target="#<?= $navbarId ?>">
    <?= $page ?>
  </div>
  <?php App::renderComponent('landing-footer') ?>

  <script src="./assets/dist/layouts/landing.js"></script>
</body>

</html>
