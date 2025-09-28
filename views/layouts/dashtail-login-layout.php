<?php

use SIPAN\App;

?>

<!doctype html>
<html>

<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width" />
  <title>SIPAN</title>
  <base href="<?= str_replace('index.php', '', $_SERVER['SCRIPT_NAME']) ?>" />

  <!-- Favicon CSS -->
  <link rel="icon" href="./assets/img/logo.png" />

  <!-- Fonts CSS -->
  <link
    rel="stylesheet"
    href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" />

  <style>
    [x-cloak] {
      display: none !important;
    }
  </style>

  <!-- App CSS -->
  <link rel="stylesheet" href="./assets/css/app.css" />

  <!-- Alpine JS -->
  <script defer src="./assets/js/alpine.js"></script>

  <!-- App JS -->
  <script src="./assets/js/app.js"></script>
</head>

<body
  class="font-inter dash-tail-app"
  x-data
  :dir="$store.app.direction"
  :class="{
    'dark': $store.app.isDark,
    ['theme-' + $store.app.theme]: true,
  }">
  <?php App::renderComponent('loader') ?>

  <div
    class="flex min-h-svh w-full flex-col bg-[#EEF1F9] dark:bg-background"
    x-cloak
    x-show="!$store.app.loading">
    <?= $page ?>
  </div>
</body>

</html>
