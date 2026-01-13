<?php 
$pageTitle = 'Predicciones de Venta';
$currentPage = 'predicciones';
require_once __DIR__ . '/../layouts/header.php';
?>

<div class="page-header">
    <h2 class="page-title">Predicciones Inteligentes</h2>
    <p class="page-subtitle">Proyección de demanda basada en análisis histórico</p>
</div>

<div class="row g-4" x-data="prediccionesApp()">
    <!-- Card Principal Gráfico -->
    <div class="col-md-9">
        <div class="card h-100">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h3 class="card-title">
                    <i class="fas fa-chart-line"></i> Proyección de Ventas (Próximos 7 Días)
                </h3>
                <span class="badge bg-info">Modelo: Regresión Lineal Simple</span>
            </div>
            <div class="card-body">
                <div style="position: relative; height: 400px;">
                    <canvas id="prediccionChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Panel Lateral: Métricas del Modelo -->
    <div class="col-md-3">
        <div class="card mb-3">
            <div class="card-header">
                <h3 class="card-title">Tendencia</h3>
            </div>
            <div class="card-body text-center">
                <div class="mb-3">
                    <i class="fas fa-3x" 
                       :class="{
                           'fa-arrow-trend-up text-success': tendencia === 'creciente',
                           'fa-arrow-trend-down text-danger': tendencia === 'decreciente',
                           'fa-minus text-muted': tendencia === 'estable'
                       }"></i>
                </div>
                <h4 class="mb-1" x-text="tendencia.charAt(0).toUpperCase() + tendencia.slice(1)">Calculando...</h4>
                <p class="text-muted small">Basado en los últimos 30 días</p>
            </div>
        </div>

        <div class="card mb-3">
            <div class="card-header">
                <h3 class="card-title">Detalles del Modelo</h3>
            </div>
            <div class="card-body">
                <ul class="list-group list-group-flush small">
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        Pendiente (m)
                        <span class="badge bg-secondary rounded-pill" x-text="modelo.pendiente">0</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        Intersección (b)
                        <span class="badge bg-secondary rounded-pill" x-text="modelo.interseccion">0</span>
                    </li>
                </ul>
                <div class="alert alert-warning mt-3 mb-0" style="font-size: 0.8rem;">
                    <i class="fas fa-lightbulb"></i> La proyección asume condiciones de mercado estables. Eventos externos pueden alterar resultados.
                </div>
            </div>
        </div>
        
        <div class="d-grid">
             <a href="/sugerencias" class="btn btn-warning">
                <i class="fas fa-shopping-basket"></i> Ver Sugerencias de Compra
             </a>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
function prediccionesApp() {
    return {
        chart: null,
        tendencia: 'Calculando...',
        modelo: { pendiente: 0, interseccion: 0 },

        async init() {
            await this.cargarDatos();
        },

        async cargarDatos() {
            try {
                const response = await fetch('/predicciones/data');
                const data = await response.json();

                if (data.success) {
                    this.tendencia = data.tendencia;
                    this.modelo = data.info_modelo;
                    this.renderChart(data);
                } else {
                    SIPAN.error(data.message || 'Error al calcular predicciones');
                }
            } catch (error) {
                console.error('Error:', error);
                SIPAN.error('Error de conexión al obtener predicciones');
            }
        },

        renderChart(data) {
            const ctx = document.getElementById('prediccionChart').getContext('2d');
            
            // Unificar datos históricos y proyecciones
            // Historico
            const labels = data.historico.map(d => formatDate(d.fecha));
            const ventasReales = data.historico.map(d => d.valor);
            const mediaMovil = data.historico.map(d => d.media_movil);
            
            // Proyecciones (agregamos al final)
            const labelsFuturos = data.prediccion.map(d => formatDate(d.fecha) + ' (Est)');
            const valoresFuturos = data.prediccion.map(d => d.valor);

            // Rellenar arrays para el gráfico
            // Para "Ventas Reales", los futuros son null
            const datasetReales = [...ventasReales, ...Array(data.prediccion.length).fill(null)];
            
            // Para "Proyección", los pasados son null (o el último real para conectar línea)
            const datasetProyeccion = [...Array(data.historico.length - 1).fill(null), ventasReales[ventasReales.length - 1], ...valoresFuturos];

            // Media móvil (solo histórico)
            const datasetSMA = [...mediaMovil, ...Array(data.prediccion.length).fill(null)];

            this.chart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: [...labels, ...labelsFuturos],
                    datasets: [
                        {
                            label: 'Ventas Reales',
                            data: datasetReales,
                            borderColor: '#D4A574', // Color primario
                            backgroundColor: 'rgba(212, 165, 116, 0.2)',
                            borderWidth: 2,
                            tension: 0.3,
                            fill: true
                        },
                        {
                            label: 'Tendencia (Media Móvil)',
                            data: datasetSMA,
                            borderColor: '#17a2b8', // Azul info
                            borderDash: [5, 5],
                            borderWidth: 2,
                            pointRadius: 0,
                            tension: 0.3,
                            fill: false
                        },
                        {
                            label: 'Proyección Futura',
                            data: datasetProyeccion,
                            borderColor: '#28a745', // Verde éxito
                            backgroundColor: 'rgba(40, 167, 69, 0.1)',
                            borderWidth: 2,
                            borderDash: [2, 2],
                            tension: 0.1,
                            pointStyle: 'rectRot',
                            pointRadius: 6,
                            fill: false
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    interaction: {
                        mode: 'index',
                        intersect: false,
                    },
                    plugins: {
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    let label = context.dataset.label || '';
                                    if (label) {
                                        label += ': ';
                                    }
                                    if (context.parsed.y !== null) {
                                        label += '$ ' + context.parsed.y.toFixed(2);
                                    }
                                    return label;
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            title: {
                                display: true,
                                text: 'Monto de Venta ($)'
                            }
                        }
                    }
                }
            });
        }
    };
}

function formatDate(dateStr) {
    const d = new Date(dateStr);
    return `${d.getDate()}/${d.getMonth() + 1}`;
}
</script>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
