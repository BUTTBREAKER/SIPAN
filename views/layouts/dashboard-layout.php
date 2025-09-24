<?php

use SIPAN\App;

$url = App::request()->url;

?>

<!DOCTYPE html>
<html data-bs-theme="dark">

<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width" />
  <meta name="color-scheme" content="only dark" />
  <title>SIPAN - Sistema de Gestión de Panadería</title>
  <base href="<?= str_replace('index.php', '', $_SERVER['SCRIPT_NAME']) ?>" />
  <link rel="stylesheet" href="./assets/dist/dashboard.css" />
  <style>
    :root {
      --primary-bg: #1a1f2d;
      --secondary-bg: #232838;
      --accent-color: #ffa047;
      --text-primary: #ffffff;
      --text-secondary: #94a3b8;
      --card-bg: #2a303f;
      --success-color: #22c55e;
      --warning-color: #fbbf24;
      --danger-color: #ef4444;
      --info-color: #3b82f6;
    }

    body {
      background-color: var(--primary-bg);
      color: var(--text-primary);
      font-family: 'Inter', sans-serif;
    }

    #sidebar {
      background-color: var(--secondary-bg);
      width: 280px;
      min-height: 100vh;
      position: fixed;
      transition: all 0.3s;
    }

    .nav-link {
      color: var(--text-secondary);
      padding: 0.75rem 1.25rem;
      margin: 0.25rem 1rem;
      border-radius: 0.5rem;
      display: flex;
      align-items: center;
      transition: all 0.3s;
    }

    .nav-link:hover,
    .nav-link.active {
      background-color: var(--accent-color);
      color: var(--primary-bg);
    }

    .nav-link i {
      margin-right: 0.75rem;
      font-size: 1.25rem;
    }

    .content-wrapper {
      margin-left: 280px;
      padding: 2rem;
      transition: all 0.3s;
    }

    .card {
      background-color: var(--card-bg);
      border: none;
      border-radius: 1rem;
      box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
    }

    .stats-card {
      background: linear-gradient(145deg, var(--card-bg) 0%, var(--secondary-bg) 100%);
      transition: transform 0.3s;
    }

    .stats-card:hover {
      transform: translateY(-5px);
    }

    .notification {
      padding: 1rem;
      border-radius: 0.5rem;
      margin-bottom: 0.5rem;
      background-color: var(--secondary-bg);
    }

    .notification.warning {
      border-left: 4px solid var(--warning-color);
    }

    .notification.success {
      border-left: 4px solid var(--success-color);
    }

    .notification.info {
      border-left: 4px solid var(--info-color);
    }

    .notification.danger {
      border-left: 4px solid var(--danger-color);
    }

    .trend-up {
      color: var(--success-color);
    }

    .trend-down {
      color: var(--danger-color);
    }

    .trend-stable {
      color: var(--warning-color);
    }

    #sidebarToggle {
      display: none;
      position: fixed;
      top: 1rem;
      left: 1rem;
      z-index: 1000;
      background-color: var(--accent-color);
      border: none;
      padding: 0.5rem;
      border-radius: 0.5rem;
    }

    @media (max-width: 768px) {
      #sidebar {
        margin-left: -280px;
      }

      #sidebar.active {
        margin-left: 0;
      }

      .content-wrapper {
        margin-left: 0;
      }

      #sidebarToggle {
        display: block;
      }
    }

    /* Estilos personalizados para el scrollbar */
    ::-webkit-scrollbar {
      width: 8px;
    }

    ::-webkit-scrollbar-track {
      background: var(--primary-bg);
    }

    ::-webkit-scrollbar-thumb {
      background: var(--accent-color);
      border-radius: 4px;
    }

    /* Animaciones para las cards */
    @keyframes fadeIn {
      from {
        opacity: 0;
        transform: translateY(20px);
      }

      to {
        opacity: 1;
        transform: translateY(0);
      }
    }

    .animated-card {
      animation: fadeIn 0.5s ease-out forwards;
    }
  </style>
</head>

<body>
  <button id="sidebarToggle" class="btn">
    <i class="bi bi-list text-white"></i>
  </button>

  <!-- Sidebar -->
  <nav id="sidebar">
    <div class="py-4 px-3">
      <div class="d-flex align-items-center mb-4 px-3">
        <i class="bi bi-box-seam text-warning fs-2 me-2"></i>
        <h2 class="m-0">SIPAN</h2>
      </div>
      <ul class="nav flex-column">
        <li class="nav-item">
          <a href="./administracion" class="nav-link <?= $url !== '/administracion' ?: 'active' ?>">
            <i class="bi bi-house-door"></i> Dashboard
          </a>
        </li>
        <li class="nav-item">
          <a href="#" class="nav-link">
            <i class="bi bi-cart"></i> Ventas
          </a>
        </li>
        <li class="nav-item">
          <a href="./administracion/productos" class="nav-link <?= $url !== '/administracion/productos' ?: 'active' ?>">
            <i class="bi bi-box"></i> Inventario
          </a>
        </li>
        <li class="nav-item">
          <a href="#" class="nav-link">
            <i class="bi bi-people"></i> Clientes
          </a>
        </li>
        <li class="nav-item">
          <a href="#" class="nav-link">
            <i class="bi bi-truck"></i> Proveedores
          </a>
        </li>
        <li class="nav-item">
          <a href="#" class="nav-link">
            <i class="bi bi-graph-up"></i> Reportes
          </a>
        </li>
        <li class="nav-item mt-4">
          <a href="#" class="nav-link">
            <i class="bi bi-gear"></i> Configuración
          </a>
        </li>
      </ul>
    </div>
  </nav>

  <!-- Contenido principal -->
  <div class="content-wrapper">
    <div class="d-flex justify-content-between align-items-center mb-4">
      <h1 class="h3"><?= $title ?></h1>
      <div class="d-flex align-items-center">
        <div class="me-3">
          <i class="bi bi-bell text-warning"></i>
        </div>
        <div class="d-flex align-items-center">
          <img src="" class="rounded-circle" alt="Usuario">
          <div class="ms-3">
            <h6 class="mb-0">Juan Panadero</h6>
            <small class="text-muted">Administrador</small>
          </div>
        </div>
      </div>
    </div>

    <?= $page ?>
  </div>

  <script src="./assets/dist/dashboard.js"></script>
  <script>
    // Toggle sidebar
    document.getElementById('sidebarToggle').addEventListener('click', function() {
      document.getElementById('sidebar').classList.toggle('active');
    });
  </script>
</body>

</html>
