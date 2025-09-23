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

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
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
