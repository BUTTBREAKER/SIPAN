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

  <!-- Alpine JS -->
  <script defer src="./assets/js/alpine.js"></script>

  <!-- App JS -->
  <script src="./assets/js/app.js"></script>

  <link rel="stylesheet" href="./assets/dist/layouts/dashtail-login.css" />
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
    <div class="min-h-screen bg-background flex items-center overflow-hidden w-full">
      <div class="min-h-screen basis-full flex flex-wrap w-full justify-center overflow-y-auto">
        <div class="basis-1/2 bg-primary w-full relative hidden xl:flex justify-center items-center bg-gradient-to-br from-primary-600 via-primary-400 to-primary-600">
          <img
            src="./assets/images/auth/line.png"
            class="absolute top-0 left-0 w-full h-full" />
          <div class="relative z-10 backdrop-blur bg-primary-foreground/40 py-14 px-16 2xl:py-[84px] 2xl:pl-[50px] 2xl:pr-[136px] rounded max-w-[640px]">
            <div>
              <button class="bg-transparent hover:bg-transparent h-fit w-fit p-0">
                <span class="icon-[heroicons--play-solid] text-primary-foreground h-[78px] w-[78px] -ms-2"></span>
              </button>
              <div class="text-4xl leading-[50px] 2xl:text-6xl 2xl:leading-[72px] font-semibold mt-2.5">
                <span class="text-default-600 dark:text-default-300">
                  Unlock <br />
                  Your Project <br />
                </span>
                <span class="text-default-900 dark:text-default-50">Performance</span>
              </div>
              <div class="mt-5 2xl:mt-8 text-default-900 dark:text-default-200 text-2xl font-medium">
                You will never know everything. <br />
                But you will know more...
              </div>
            </div>
          </div>
        </div>
        <div class="h-screen overflow-y-auto basis-full md:basis-1/2 w-full px-4 py-5 flex justify-center items-center">
          <div class="lg:w-[480px]">
            <div x-twmerge="{ '': true }">
              <?= $page ?>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <script src="./assets/dist/layouts/dashtail-login.js"></script>
</body>

</html>
