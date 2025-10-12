<?php 
$pageTitle = 'Recetas';
$currentPage = 'recetas';
require_once __DIR__ . '/../layouts/header.php';
?>

<div class="page-header d-flex justify-between align-center">
    <div>
        <h2 class="page-title">Recetas</h2>
        <p class="page-subtitle">Gestión de recetas de producción</p>
    </div>
    <a href="./recetas/create" class="btn btn-primary">
        <i class="fas fa-plus"></i> Nueva Receta
    </a>
</div>

<div class="card">
    <div class="card-header">
        <h3 class="card-title">Listado de Recetas</h3>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table datatable">
                <thead>
                    <tr>
                        <th>Producto</th>
                        <th>Rendimiento</th>
                        <th>Insumos</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($recetas)): ?>
                    <tr>
                        <td colspan="5" class="text-center">No hay recetas registradas</td>
                    </tr>
                    <?php else: ?>
                    <?php foreach ($recetas as $receta): ?>
                    <tr>
                        <td><strong><?= htmlspecialchars($receta['producto_nombre'] ?? 'Producto #' . $receta['id_producto']) ?></strong></td>
                        <td><?= $receta['rendimiento'] ?> unidades</td>
                        <td>
                            <?php
                            // Obtener insumos de la receta
                            require_once __DIR__ . '/../../Models/Receta.php';
                            $recetaModel = new \App\Models\Receta();
                            $insumos = $recetaModel->getInsumos($receta['id']);
                            echo count($insumos) . ' insumo(s)';
                            ?>
                        </td>
                        <td>
                            <div class="d-flex gap-1">
                                <a href="./recetas/edit/<?= $receta['id'] ?>" class="btn btn-sm btn-info" title="Ver/Editar">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <?php if ($_SESSION['user_rol'] === 'administrador'): ?>
                                <button onclick="eliminarReceta(<?= $receta['id'] ?>)" class="btn btn-sm btn-danger" title="Eliminar">
                                    <i class="fas fa-trash"></i>
                                </button>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
async function eliminarReceta(id) {
    const confirmed = await SIPAN.confirm('¿Eliminar esta receta?', '¿Estás seguro?');
    if (!confirmed) return;
    
    try {
        const response = await fetch(`/recetas/delete/${id}`, {
            method: 'POST'
        });
        
        const data = await response.json();
        
        if (data.success) {
            SIPAN.success(data.message);
            setTimeout(() => window.location.reload(), 1500);
        } else {
            SIPAN.error(data.message);
        }
    } catch (error) {
        SIPAN.error('Error al eliminar receta');
    }
}
</script>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
