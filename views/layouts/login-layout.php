<!DOCTYPE html>
<html>

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width">
  <title><?= $title ?? 'SIPAN - Autenticación' ?></title>
  <base href="<?= str_replace('index.php', '', $_SERVER['SCRIPT_NAME']) ?>" />
  <link rel="icon" href="./assets/img/favicon.png" />
  <link rel="stylesheet" href="./assets/dist/layouts/landing.css">
</head>

<body>
  <!-- Navbar -->
  <nav class="navbar navbar-expand-lg sticky-top">
    <div class="container">
      <a href="./" data-bs-toggle="tooltip" title="Sistema Integral para Panaderías">
        <img src="./assets/img/logo.png" height="64" />
      </a>
      <div class="btn-group">
        <a href="./ingresar" class="btn btn-danger">Iniciar sesión</a>
        <a href="./registrarse" class="btn btn-danger">Registrarse</a>
      </div>
    </div>
  </nav>

  <!-- Sección de Autenticación -->
  <section style="background: url('./assets/img/hero/hero-bg.svg')">
    <div class="container py-5">
      <div class="col-xl-8 m-auto">
        <div class="card card-body shadow-lg rounded-top-5 border-5 border-start-0 border-end-0 border-bottom-0 border-danger">
          <figure class="text-center">
            <img src="./assets/img/logo.png" class="w-25">
          </figure>

          <h1 class="text-center card-title font-serif"><?= $title ?></h1>

          <?= $page ?>
        </div>
      </div>
    </div>
  </section>

  <script src="./assets/dist/layouts/landing.js"></script>
</body>

</html>
