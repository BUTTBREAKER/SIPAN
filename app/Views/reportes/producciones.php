<?php 
$pageTitle = 'Reporte de Producciones';
$currentPage = 'reportes';
require_once __DIR__ . '/../layouts/header.php';
?>

<div class="page-header">
    <div>
        <h2 class="page-title">Reporte de Producciones</h2>
        <p class="page-subtitle">Historial completo</p>
    </div>
    <div class="d-flex gap-2">
        <a href="/reportes/producciones?formato=pdf" class="btn btn-danger" target="_blank">
            <i class="fas fa-file-pdf"></i> Exportar PDF
        </a>
        <a href="/reportes/producciones?formato=excel" class="btn btn-success" target="_blank">
            <i class="fas fa-file-excel"></i> Exportar Excel
        </a>
        <a href="/reportes" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Volver
        </a>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Fecha</th>
                        <th>Producto</th>
                        <th>Cantidad</th>
                        <th>Responsable</th>
                        <th>Estado</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($producciones as $p): ?>
                    <tr>
                        <td><?= date('d/m/Y H:i', strtotime($p['fecha_produccion'])) ?></td>
                        <td><?= htmlspecialchars($p['producto_nombre']) ?></td>
                        <td><strong><?= $p['cantidad_producida'] ?></strong></td>
                        <td><?= htmlspecialchars($p['primer_nombre'] . ' ' . $p['apellido_paterno']) ?></td>
                        <td>
                            <span class="badge bg-<?= $p['estado'] === 'completado' ? 'success' : 'secondary' ?>">
                                <?= ucfirst($p['estado']) ?>
                            </span>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
