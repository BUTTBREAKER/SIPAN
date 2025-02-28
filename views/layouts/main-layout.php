<?php
// Datos simulados
$salesData = [
  ['name' => 'Lun', 'ventas' => 4000, 'pedidos' => 150],
  ['name' => 'Mar', 'ventas' => 3000, 'pedidos' => 120],
  ['name' => 'Mié', 'ventas' => 2000, 'pedidos' => 180],
  ['name' => 'Jue', 'ventas' => 2780, 'pedidos' => 200],
  ['name' => 'Vie', 'ventas' => 1890, 'pedidos' => 160],
  ['name' => 'Sáb', 'ventas' => 2390, 'pedidos' => 220],
  ['name' => 'Dom', 'ventas' => 3490, 'pedidos' => 170],
];

$topProducts = [
  ['name' => 'Pan Francés', 'cantidad' => 500, 'tendencia' => 'up'],
  ['name' => 'Croissant', 'cantidad' => 350, 'tendencia' => 'up'],
  ['name' => 'Pan de Molde', 'cantidad' => 300, 'tendencia' => 'down'],
  ['name' => 'Baguette', 'cantidad' => 250, 'tendencia' => 'up'],
  ['name' => 'Donas', 'cantidad' => 200, 'tendencia' => 'stable'],
];

$notifications = [
  ['type' => 'warning', 'message' => 'Stock bajo de harina - 25kg restantes', 'time' => '5m'],
  ['type' => 'success', 'message' => 'Pedido #2234 completado', 'time' => '10m'],
  ['type' => 'info', 'message' => 'Nuevo pedido recibido #2235', 'time' => '15m'],
  ['type' => 'danger', 'message' => 'Error en transacción #445', 'time' => '30m'],
];
?>

<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>SIPAN - Sistema de Gestión de Panadería</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css" rel="stylesheet">
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
          <a href="#" class="nav-link active">
            <i class="bi bi-house-door"></i> Dashboard
          </a>
        </li>
        <li class="nav-item">
          <a href="#" class="nav-link">
            <i class="bi bi-cart"></i> Ventas
          </a>
        </li>
        <li class="nav-item">
          <a href="#" class="nav-link">
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
      <h1 class="h3">Dashboard</h1>
      <div class="d-flex align-items-center">
        <div class="me-3">
          <i class="bi bi-bell text-warning"></i>
        </div>
        <div class="d-flex align-items-center">
          <img src="https://via.placeholder.com/40" class="rounded-circle" alt="Usuario">
          <div class="ms-3">
            <h6 class="mb-0">Juan Panadero</h6>
            <small class="text-muted">Administrador</small>
          </div>
        </div>
      </div>
    </div>

    <!-- Stats Cards -->
    <div class="row g-4 mb-4">
      <div class="col-12 col-md-6 col-lg-3">
        <div class="card stats-card h-100">
          <div class="card-body">
            <div class="d-flex justify-content-between mb-3">
              <div class="text-secondary">Ventas Diarias</div>
              <i class="bi bi-currency-dollar fs-4 text-warning"></i>
            </div>
            <h3 class="mb-2">$4,589.00</h3>
            <div class="text-success">
              <i class="bi bi-arrow-up"></i> 12.5%
              <small class="text-secondary ms-1">vs ayer</small>
            </div>
          </div>
        </div>
      </div>
      <div class="col-12 col-md-6 col-lg-3">
        <div class="card stats-card h-100">
          <div class="card-body">
            <div class="d-flex justify-content-between mb-3">
              <div class="text-secondary">Pedidos</div>
              <i class="bi bi-bag-check fs-4 text-info"></i>
            </div>
            <h3 class="mb-2">156</h3>
            <div class="text-success">
              <i class="bi bi-arrow-up"></i> 8.2%
              <small class="text-secondary ms-1">vs ayer</small>
            </div>
          </div>
        </div>
      </div>
      <div class="col-12 col-md-6 col-lg-3">
        <div class="card stats-card h-100">
          <div class="card-body">
            <div class="d-flex justify-content-between mb-3">
              <div class="text-secondary">Productos</div>
              <i class="bi bi-box-seam fs-4 text-success"></i>
            </div>
            <h3 class="mb-2">2,345</h3>
            <div class="text-danger">
              <i class="bi bi-arrow-down"></i> 3.1%
              <small class="text-secondary ms-1">vs ayer</small>
            </div>
          </div>
        </div>
      </div>
      <div class="col-12 col-md-6 col-lg-3">
        <div class="card stats-card h-100">
          <div class="card-body">
            <div class="d-flex justify-content-between mb-3">
              <div class="text-secondary">Clientes</div>
              <i class="bi bi-people fs-4 text-primary"></i>
            </div>
            <h3 class="mb-2">48</h3>
            <div class="text-success">
              <i class="bi bi-arrow-up"></i> 4.5%
              <small class="text-secondary ms-1">vs ayer</small>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="row g-4">
      <!-- Gráfico de ventas -->
      <div class="col-12 col-lg-8">
        <div class="card h-100">
          <div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-4">
              <h5 class="card-title">Ventas y Pedidos</h5>
              <select class="form-select form-select-sm w-auto">
                <option>Última Semana</option>
                <option>Último Mes</option>
                <option>Último Año</option>
              </select>
            </div>
            <div style="height: 300px; max-height: 300px; overflow: hidden;">
              <canvas id="salesChart"></canvas>
            </div>
          </div>
        </div>
      </div>


      <!-- Productos más vendidos y notificaciones -->
      <div class="col-12 col-lg-4">
        <div class="card h-100">
          <div class="card-body">
            <h5 class="card-title mb-4">Productos más vendidos</h5>
            <?php foreach ($topProducts as $product): ?>
              <div class="d-flex justify-content-between align-items-center mb-3">
                <div>
                  <h6 class="mb-0"><?php echo $product['name']; ?></h6>
                  <small class="text-secondary"><?php echo $product['cantidad']; ?> unidades</small>
                </div>
                <i class="bi bi-arrow-<?php echo $product['tendencia']; ?>
                                   trend-<?php echo $product['tendencia']; ?>"></i>
              </div>
            <?php endforeach; ?>

            <h5 class="card-title mt-5 mb-4">Notificaciones</h5>
            <?php foreach ($notifications as $notification): ?>
              <div class="notification <?php echo $notification['type']; ?>">
                <div class="d-flex justify-content-between">
                  <div>
                    <p class="mb-0"><?php echo $notification['message']; ?></p>
                    <small class="text-secondary"><?php echo $notification['time']; ?> atrás</small>
                  </div>
                  <i class="bi bi-three-dots-vertical"></i>
                </div>
              </div>
            <?php endforeach; ?>
          </div>
        </div>
      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <script>
    // Toggle sidebar
    document.getElementById('sidebarToggle').addEventListener('click', function() {
      document.getElementById('sidebar').classList.toggle('active');
    });

    // Gráfico de ventas
    const ctx = document.getElementById('salesChart').getContext('2d');
    new Chart(ctx, {
      type: 'line',
      data: {
        labels: <?php echo json_encode(array_column($salesData, 'name')); ?>,
        datasets: [{
            label: 'Ventas',
            data: <?php echo json_encode(array_column($salesData, 'ventas')); ?>,
            borderColor: '#ffa047',
            backgroundColor: 'rgba(255, 160, 71, 0.1)',
            tension: 0.4,
            fill: true
          },
          {
            label: 'Pedidos',
            data: <?php echo json_encode(array_column($salesData, 'pedidos')); ?>,
            borderColor: '#3b82f6',
            backgroundColor: 'rgba(59, 130, 246, 0.1)',
            tension: 0.4,
            fill: true
          }
        ]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false, // 🔹 Evita que el canvas se expanda indefinidamente
        plugins: {
          legend: {
            position: 'top',
            labels: {
              color: '#94a3b8'
            }
          }
        },
        scales: {
          y: {
            beginAtZero: true,
            grid: {
              color: 'rgba(255, 255, 255, 0.1)'
            },
            ticks: {
              color: '#94a3b8'
            }
          },
          x: {
            grid: {
              color: 'rgba(255, 255, 255, 0.1)'
            },
            ticks: {
              color: '#94a3b8'
            }
          }
        }
      }
    });

    // Animación de las cards al cargar
    document.addEventListener('DOMContentLoaded', function() {
      const cards = document.querySelectorAll('.stats-card');
      cards.forEach((card, index) => {
        card.style.animationDelay = `${index * 0.1}s`;
        card.classList.add('animated-card');
      });
    });
  </script>
</body>

</html>
