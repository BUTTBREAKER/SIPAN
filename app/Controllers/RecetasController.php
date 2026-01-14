<?php

namespace App\Controllers;

use App\Models\Receta;
use App\Models\Producto;
use App\Models\Insumo;
use App\Middlewares\AuthMiddleware;

class RecetasController
{
    private $recetaModel;
    private $productoModel;
    private $insumoModel;

    public function __construct()
    {
        $this->recetaModel = new Receta();
        $this->productoModel = new Producto();
        $this->insumoModel = new Insumo();
    }

    public function index()
    {
        AuthMiddleware::check();

        $user = AuthMiddleware::getUser();
        $sucursal_id = $user['sucursal_id'];

        $recetas = $this->recetaModel->getWithDetails($sucursal_id);

        require_once __DIR__ . '/../Views/recetas/index.php';
    }

    public function create()
    {
        AuthMiddleware::checkRole(['administrador', 'empleado']);

        $user = AuthMiddleware::getUser();
        $sucursal_id = $user['sucursal_id'];

        $productos = $this->productoModel->all($sucursal_id);
        $insumos = $this->insumoModel->all($sucursal_id);

        require_once __DIR__ . '/../Views/recetas/create.php';
    }

    public function store()
    {
        AuthMiddleware::checkRole(['administrador', 'empleado']);
        header('Content-Type: application/json');

        $id_producto = $_POST['id_producto'] ?? null;
        $rendimiento = $_POST['rendimiento'] ?? null;
        $instrucciones = $_POST['instrucciones'] ?? '';
        $insumos = json_decode($_POST['insumos'] ?? '[]', true);
        $user = AuthMiddleware::getUser();
        $sucursal_id = $user['sucursal_id'];

        if (!$id_producto || !$rendimiento || empty($insumos)) {
            echo json_encode(['success' => false, 'message' => 'Datos incompletos']);
            return;
        }

        try {
            // ✅ El nombre de la receta será el del producto
            $producto = $this->productoModel->find($id_producto);
            if (!$producto) {
                echo json_encode(['success' => false, 'message' => 'Producto no encontrado']);
                return;
            }

            $this->recetaModel->createWithInsumos($id_producto, $rendimiento, $instrucciones, $sucursal_id, $insumos);
            echo json_encode(['success' => true, 'message' => 'Receta guardada correctamente']);
        } catch (\Exception $e) {
            echo json_encode(['success' => false, 'message' => 'Error al guardar receta: ' . $e->getMessage()]);
        }
    }

    public function show($id)
    {
        AuthMiddleware::checkRole(['administrador', 'empleado']);

        $user = AuthMiddleware::getUser();
        $sucursal_id = $user['sucursal_id'];

        $receta = $this->recetaModel->find($id);
        if (!$receta) {
            header('Location: /recetas');
            exit;
        }

        // Obtener producto asociado para ver precio venta
        $producto = $this->productoModel->find($receta['id_producto']);

        // Obtener insumos con costos
        // IMPORTANTE: Aseguramos que getInsumos traiga costo promedio/unitario del insumo
        // Si no, lo traemos manualmente buscando cada uno.
        $insumos = $this->recetaModel->getInsumos($id);

        // Calcular Costos
        $costo_total_receta = 0;
        $detalles_insumos = [];

        foreach ($insumos as $insumo) {
            // Asumimos que getInsumos ya trae datos básicos, pero necesitamos el costo actual del insumo
            // Si el modelo Receta no hace join con costo, buscamos el insumo
            $insumoData = $this->insumoModel->find($insumo['id_insumo']);
            $costo_unitario = $insumoData['costo_unitario'] ?? 0; // Campo debe existir en insumos o lotes (promedio)

            // Si no existe costo_unitario en insumos, usar promedio ponderado de lotes (pendiente),
            // por ahora usamos un fallback si no está implementado

            $subtotal = $insumo['cantidad'] * $costo_unitario;
            $costo_total_receta += $subtotal;

            $insumo['costo_unitario'] = $costo_unitario;
            $insumo['subtotal_costo'] = $subtotal;
            $detalles_insumos[] = $insumo;
        }

        $costo_unitario_produccion = ($receta['rendimiento'] > 0) ? ($costo_total_receta / $receta['rendimiento']) : 0;
        $precio_venta = $producto['precio_actual'] ?? 0;
        $margen_unitario = $precio_venta - $costo_unitario_produccion;
        $margen_porcentaje = ($precio_venta > 0) ? (($margen_unitario / $precio_venta) * 100) : 0;

        require_once __DIR__ . '/../Views/recetas/show.php';
    }

    public function edit($id)
    {
        AuthMiddleware::checkRole(['administrador', 'empleado']);

        $user = AuthMiddleware::getUser();
        $sucursal_id = $user['sucursal_id'];

        $receta = $this->recetaModel->find($id);
        if (!$receta) {
            header('Location: /recetas');
            exit;
        }

        // ✅ Aquí obtenemos los insumos con nombre incluido
        $receta_insumos = $this->recetaModel->getInsumos($id);
        $productos = $this->productoModel->all($sucursal_id);
        $insumos_disponibles = $this->insumoModel->all($sucursal_id);

        require_once __DIR__ . '/../Views/recetas/edit.php';
    }

    public function update($id)
    {
        AuthMiddleware::checkRole(['administrador', 'empleado']);
        header('Content-Type: application/json');

        // Soporta tanto $_POST como JSON raw
        $json = file_get_contents('php://input');
        $raw_data = json_decode($json, true);

        $data = [
            'nombre' => $raw_data['nombre'] ?? ($_POST['nombre'] ?? null),
            'rendimiento' => $raw_data['rendimiento'] ?? ($_POST['rendimiento'] ?? 1),
            'instrucciones' => $raw_data['instrucciones'] ?? ($_POST['instrucciones'] ?? '')
        ];

        try {
            // Actualizar datos básicos
            $this->recetaModel->update($id, array_filter($data, fn($v) => $v !== null));

            // Actualizar insumos si vienen en el JSON
            $insumos = $raw_data['insumos'] ?? ($_POST['insumos'] ?? null);
            if ($insumos !== null) {
                if (is_string($insumos)) {
                    $insumos = json_decode($insumos, true);
                }
                $this->recetaModel->updateInsumos($id, $insumos);
            }

            echo json_encode(['success' => true, 'message' => 'Receta actualizada correctamente']);
        } catch (\Exception $e) {
            echo json_encode(['success' => false, 'message' => 'Error al actualizar receta: ' . $e->getMessage()]);
        }
        exit;
    }

    public function delete($id)
    {
        AuthMiddleware::checkRole(['administrador']);
        header('Content-Type: application/json');

        try {
            $this->recetaModel->delete($id);
            echo json_encode(['success' => true, 'message' => 'Receta eliminada correctamente']);
        } catch (\Exception $e) {
            echo json_encode(['success' => false, 'message' => 'Error al eliminar receta: ' . $e->getMessage()]);
        }
        exit;
    }

    public function calcular()
    {
        AuthMiddleware::check();
        header('Content-Type: application/json');

    // Leer params (soporta GET y POST)
        $producto_id = $_GET['id_producto'] ?? $_POST['producto_id'] ?? 0;
        $cantidad = $_GET['cantidad'] ?? $_POST['cantidad'] ?? null;

        if (!$producto_id) {
            echo json_encode(['success' => false, 'message' => 'Producto no especificado']);
            exit;
        }

        try {
            // Si no vienen cantidad => solo preguntan si existe receta (para mostrar "receta_cargada")
            if ($cantidad === null || $cantidad === '') {
                // Buscar receta asociada al producto
                $receta = $this->recetaModel->findByProducto($producto_id);

                if (!$receta) {
                    echo json_encode(['success' => true, 'receta' => null]); // indica "sin receta"
                    exit;
                }

                // Retornar datos básicos de la receta (rendimiento, tiempo, instrucciones...)
                $resp = [
                'success' => true,
                'receta' => [
                    'id' => $receta['id'],
                    'rendimiento' => $receta['rendimiento'] ?? 0,
                    'tiempo_preparacion' => $receta['tiempo_preparacion'] ?? 0,
                    'instrucciones' => $receta['instrucciones'] ?? ''
                ]
                ];
                echo json_encode($resp);
                exit;
            }

            // Si llega cantidad -> calcular insumos necesarios
            $cantidad = (float) $cantidad;
            $insumosRaw = $this->recetaModel->calcularInsumos($producto_id, $cantidad);

            // Transformar filas para la vista y decidir si puede producir
            $insumos = [];
            $puede_producir = true;

            foreach ($insumosRaw as $r) {
                // soportar distintos nombres de columnas que puedan venir del SP o query
                $nombre = $r['insumo_nombre'] ?? $r['nombre'] ?? $r['insumo'] ?? null;
                $cantidad_necesaria = $r['cantidad_necesaria'] ?? $r['cantidad'] ?? $r['cantidad_necesaria'] ?? 0;
                $unidad = $r['unidad_medida'] ?? $r['unidad'] ?? $r['unidad_insumo'] ?? '';
                $stock_actual = isset($r['stock_actual']) ? (float)$r['stock_actual'] : (
                            isset($r['stock_disponible']) ? (float)$r['stock_disponible'] : 0
                          );

                // Si el procedimiento devolviera 'faltante' o 'disponibilidad', también podríamos usarlo
                $suficiente = ($stock_actual >= (float)$cantidad_necesaria);
                if (!$suficiente) {
                    $puede_producir = false;
                }

                $insumos[] = [
                    'id' => $r['insumo_id'] ?? $r['id_insumo'] ?? $r['id'] ?? null,
                    'nombre' => $nombre,
                    'cantidad_necesaria' => (float)$cantidad_necesaria,
                    'unidad' => $unidad,
                    'stock_disponible' => $stock_actual,
                    'suficiente' => $suficiente
                ];
            }

            echo json_encode([
            'success' => true,
            'insumos' => $insumos,
            'puede_producir' => $puede_producir
            ]);
        } catch (\Exception $e) {
            echo json_encode(['success' => false, 'message' => 'Error al calcular insumos: ' . $e->getMessage()]);
        }
        exit;
    }


    public function addInsumo()
    {
        AuthMiddleware::checkRole(['administrador', 'empleado']);
        header('Content-Type: application/json');

        $receta_id = $_POST['receta_id'] ?? 0;
        $insumo_id = $_POST['insumo_id'] ?? 0;
        $cantidad = $_POST['cantidad'] ?? 0;
        $unidad_medida = $_POST['unidad_medida'] ?? 'kg';

        try {
            $this->recetaModel->addInsumo($receta_id, $insumo_id, $cantidad, $unidad_medida);
            echo json_encode(['success' => true, 'message' => 'Insumo agregado a la receta']);
        } catch (\Exception $e) {
            echo json_encode(['success' => false, 'message' => 'Error al agregar insumo: ' . $e->getMessage()]);
        }
        exit;
    }

    public function removeInsumo()
    {
        AuthMiddleware::checkRole(['administrador', 'empleado']);
        header('Content-Type: application/json');

        $receta_id = $_POST['receta_id'] ?? 0;
        $insumo_id = $_POST['insumo_id'] ?? 0;

        try {
            $this->recetaModel->removeInsumo($receta_id, $insumo_id);
            echo json_encode(['success' => true, 'message' => 'Insumo eliminado de la receta']);
        } catch (\Exception $e) {
            echo json_encode(['success' => false, 'message' => 'Error al eliminar insumo: ' . $e->getMessage()]);
        }
        exit;
    }

    public function list()
    {
        AuthMiddleware::check();
        header('Content-Type: application/json');

        $user = AuthMiddleware::getUser();
        $sucursal_id = $user['sucursal_id'];

        try {
            $recetas = $this->recetaModel->all($sucursal_id);
            echo json_encode(['success' => true, 'recetas' => $recetas]);
        } catch (\Exception $e) {
            echo json_encode(['success' => false, 'message' => 'Error al obtener recetas: ' . $e->getMessage()]);
        }
        exit;
    }
}
