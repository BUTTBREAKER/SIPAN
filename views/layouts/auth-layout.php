<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= $title ?? 'SIPAN - Autenticación' ?></title>
  <link rel="icon" href="./assets/img/favicon.png" />
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&amp;family=Volkhov:wght@700&amp;display=swap">
  <link rel="stylesheet" href="./assets/css/theme.min.css">
  <style>
    .auth-section {
      padding: 4rem 0;
      min-height: 100vh;
      display: flex;
      align-items: center;
      background: linear-gradient(150deg, #f8f9fa 0%, #ffffff 100%);
    }

    .auth-card {
      background: white;
      border-radius: 1.5rem;
      box-shadow: 0 1rem 2rem rgba(0, 0, 0, 0.1);
      padding: 3rem;
      width: 100%;
      max-width: 800px;
      margin: 0 auto;
      position: relative;
      overflow: hidden;
    }

    .auth-card::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      width: 100%;
      height: 0.4rem;
      background: linear-gradient(90deg, #4b6cb7 0%, #182848 100%);
    }

    .auth-logo {
      width: 180px;
      margin: 0 auto 2.5rem;
      display: block;
      transition: transform 0.3s ease;
    }

    .auth-logo:hover {
      transform: scale(1.05);
    }

    .auth-form .form-control {
      border-radius: 0.75rem;
      padding: 1rem 1.5rem;
      font-size: 1.1rem;
      border: 2px solid #e9ecef;
      transition: all 0.3s ease;
    }

    .auth-form .form-control:focus {
      border-color: #4b6cb7;
      box-shadow: 0 0 0 0.25rem rgba(75, 108, 183, 0.25);
    }

    .auth-form .btn {
      padding: 1rem 2rem;
      border-radius: 0.75rem;
      font-weight: 600;
      letter-spacing: 0.05em;
      transition: all 0.3s ease;
      font-size: 1.1rem;
    }

    .auth-form .btn-primary {
      background: linear-gradient(90deg, #4b6cb7 0%, #182848 100%);
      border: none;
    }

    .auth-form .btn-primary:hover {
      transform: translateY(-2px);
      box-shadow: 0 0.5rem 1rem rgba(75, 108, 183, 0.3);
    }

    .auth-links {
      text-align: center;
      margin-top: 2rem;
      font-size: 0.95rem;
    }

    .auth-links a {
      color: #4b6cb7;
      text-decoration: none;
      font-weight: 500;
      position: relative;
    }

    .auth-links a::after {
      content: '';
      position: absolute;
      bottom: -2px;
      left: 0;
      width: 0;
      height: 2px;
      background: #182848;
      transition: width 0.3s ease;
    }

    .auth-links a:hover::after {
      width: 100%;
    }

    .password-toggle {
      position: absolute;
      right: 1.5rem;
      top: 50%;
      transform: translateY(-50%);
      cursor: pointer;
      color: #6c757d;
    }

    @media (max-width: 768px) {
      .auth-card {
        padding: 2rem;
        margin: 0 1rem;
      }

      .auth-logo {
        width: 140px;
      }
    }
  </style>
</head>

<body>
  <main class="main" id="top">
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-light sticky-top">
      <div class="container">
        <a class="navbar-brand" href="./">
          <img src="assets/img/logo.png" height="64" alt="SIPAN Logo">
        </a>
        <div class="d-flex align-items-center gap-3">
          <a href="./ingresar" class="btn btn-outline-dark">Iniciar Sesión</a>
          <a href="./registrarse" class="btn btn-dark">Registrarse</a>
        </div>
      </div>
    </nav>

    <!-- Sección de Autenticación -->
    <section class="auth-section">
      <div class="container">
        <div class="row justify-content-center">
          <div class="col-xl-8 col-lg-10">
            <div class="auth-card">
              <img src="./assets/img/logo.png" alt="SIPAN" class="auth-logo">
              <h1 class="text-center mb-4 display-5 fw-bold"><?= $title ?? 'Autenticación' ?></h1>

              <?= $page ?>

              <div class="auth-links">
                <?php if (strpos($_SERVER['REQUEST_URI'], 'registrarse') !== false): ?>
                  <p>¿Ya tienes cuenta? <a href="./ingresar">Inicia sesión aquí</a></p>
                <?php else: ?>
                  <div class="d-flex flex-column gap-2">
                    <p>¿No tienes cuenta? <a href="./registrarse">Crea una cuenta</a></p>
                    <p><a href="./recuperar-contrasena">¿Olvidaste tu contraseña?</a></p>
                  </div>
                <?php endif; ?>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

  </main>

  <!-- Scripts -->
  <script src="./assets/vendors/bootstrap/bootstrap.bundle.min.js"></script>
  <script src="./assets/vendors/fontawesome/all.min.js"></script>
  <script>
    // Script para alternar visibilidad de contraseña
    document.querySelectorAll('.password-toggle').forEach(toggle => {
      toggle.addEventListener('click', (e) => {
        const input = e.target.previousElementSibling;
        const type = input.getAttribute('type') === 'password' ? 'text' : 'password';
        input.setAttribute('type', type);
        e.target.classList.toggle('fa-eye-slash');
      });
    });

    // Inicializar tooltips
    document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach(node => {
      new bootstrap.Tooltip(node);
    });
  </script>
</body>

</html>
