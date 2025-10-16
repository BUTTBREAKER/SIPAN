<?php

use SIPAN\App;

?>

<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login - SIPAN</title>

  <base href="<?= str_replace('index.php', '', $_SERVER['SCRIPT_NAME']) ?>" />
  <!-- Bootstrap 5 -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

  <!-- Font Awesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

  <!-- Google Fonts -->
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

  <!-- SweetAlert2 -->
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

  <style>
    :root {
      --color-primary: #D4A574;
      --color-primary-dark: #B8935F;
      --color-secondary: #F5E6D3;
      --color-accent: #8B6F47;
      --color-dark: #3E2723;
      --color-light: #FFF8E7;
    }

    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }

    body {
      font-family: 'Inter', sans-serif;
      background: linear-gradient(135deg, var(--color-primary) 0%, var(--color-accent) 100%);
      min-height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 2rem;
    }

    .login-container {
      background: white;
      border-radius: 20px;
      box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
      overflow: hidden;
      max-width: 1000px;
      width: 100%;
      display: grid;
      grid-template-columns: 1fr 1fr;
    }

    .login-left {
      background: linear-gradient(135deg, var(--color-primary) 0%, var(--color-accent) 100%);
      color: white;
      padding: 3rem;
      display: flex;
      flex-direction: column;
      justify-content: center;
      align-items: center;
      text-align: center;
    }

    .login-logo {
      font-size: 4rem;
      margin-bottom: 1rem;
    }

    .login-title {
      font-size: 2.5rem;
      font-weight: 700;
      margin-bottom: 1rem;
    }

    .login-subtitle {
      font-size: 1.1rem;
      opacity: 0.9;
    }

    .login-right {
      padding: 3rem;
      display: flex;
      flex-direction: column;
      justify-content: center;
    }

    .login-form-title {
      font-size: 1.8rem;
      font-weight: 700;
      color: var(--color-dark);
      margin-bottom: 0.5rem;
    }

    .login-form-subtitle {
      color: #6c757d;
      margin-bottom: 2rem;
    }

    .form-group {
      margin-bottom: 1.5rem;
    }

    .form-label {
      display: block;
      margin-bottom: 0.5rem;
      font-weight: 500;
      color: var(--color-dark);
    }

    .form-control {
      width: 100%;
      padding: 0.875rem 1rem;
      border: 2px solid #e0e0e0;
      border-radius: 10px;
      font-size: 1rem;
      transition: all 0.3s ease;
    }

    .form-control:focus {
      outline: none;
      border-color: var(--color-primary);
      box-shadow: 0 0 0 4px rgba(212, 165, 116, 0.1);
    }

    .input-icon {
      position: relative;
    }

    .input-icon i {
      position: absolute;
      left: 1rem;
      top: 50%;
      transform: translateY(-50%);
      color: #6c757d;
    }

    .input-icon .form-control {
      padding-left: 3rem;
    }

    .btn-login {
      width: 100%;
      padding: 1rem;
      background: linear-gradient(135deg, var(--color-primary), var(--color-primary-dark));
      color: white;
      border: none;
      border-radius: 10px;
      font-size: 1rem;
      font-weight: 600;
      cursor: pointer;
      transition: all 0.3s ease;
    }

    .btn-login:hover {
      transform: translateY(-2px);
      box-shadow: 0 8px 20px rgba(212, 165, 116, 0.4);
    }

    @media (max-width: 768px) {
      .login-container {
        grid-template-columns: 1fr;
      }

      .login-left {
        display: none;
      }
    }
  </style>
</head>

<body>
  <div class="login-container">
    <div class="login-left">
      <div class="login-logo">
        <i class="fas fa-bread-slice"></i>
      </div>
      <h1 class="login-title">SIPAN</h1>
      <p class="login-subtitle">Sistema Integral para Panaderías</p>
      <p style="margin-top: 2rem; opacity: 0.8">Gestiona tu panadería de manera eficiente y profesional</p>
    </div>

    <div class="login-right">
      <h2 class="login-form-title">Iniciar Sesión</h2>
      <p class="login-form-subtitle">Ingresa tus credenciales para acceder</p>

      <form id="loginForm" action=".<?= App::getUrl('login.post') ?>" method="POST">
        <div class="form-group">
          <label class="form-label">Correo Electrónico</label>
          <div class="input-icon">
            <i class="fas fa-envelope"></i>
            <input type="email" name="correo" class="form-control" placeholder="tu@correo.com" required>
          </div>
        </div>

        <div class="form-group">
          <label class="form-label">Contraseña</label>
          <div class="input-icon">
            <i class="fas fa-lock"></i>
            <input type="password" name="clave" class="form-control" placeholder="••••••••" required>
          </div>
        </div>

        <button type="submit" class="btn-login">
      </form>

      <div class="text-center mt-4">
        <p class="text-muted">¿No tienes cuenta? <a href=".<?= App::getUrl('register.get') ?>" style="color: #8B6F47; font-weight: 600; text-decoration: none;">Regístrate aquí</a></p>
      </div>
      <i class="fas fa-sign-in-alt"></i> Ingresar
      </button>
      </form>
    </div>
  </div>

  <script>
    document.getElementById('loginForm').addEventListener('submit', async function(e) {
      e.preventDefault();

      const formData = new FormData(this);

      try {
        const response = await fetch(this.action, {
          method: 'POST',
          body: formData
        });

        const data = await response.json();

        if (data.success) {
          Swal.fire({
            icon: 'success',
            title: '¡Bienvenido!',
            text: data.message,
            confirmButtonColor: '#D4A574',
            timer: 2000,
            timerProgressBar: true
          }).then(() => {
            window.location.href = '.<?= App::getUrl('dashboard') ?>';
          });
        } else {
          Swal.fire({
            icon: 'error',
            title: 'Error',
            text: data.message,
            confirmButtonColor: '#D4A574'
          });
        }
      } catch (error) {
        Swal.fire({
          icon: 'error',
          title: 'Error',
          text: 'Error al procesar la solicitud',
          confirmButtonColor: '#D4A574'
        });
        console.error('Error:', error);
      }
    });
  </script>
</body>

</html>
