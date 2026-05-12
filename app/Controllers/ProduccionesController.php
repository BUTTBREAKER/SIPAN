<?php

namespace App\Controllers;

use App\Models\Produccion;
use App\Models\Producto;
use App\Models\Negocio;
use App\Middlewares\AuthMiddleware;

class ProduccionesController
{
    private $produccionModel;
    private $productoModel;
    private $negocioModel;

    public function __construct()
    {
        $this->produccionModel = new Produccion();
        $this->productoModel = new Producto();
        $this->negocioModel = new Negocio();
    }

    public function index()
    {
        AuthMiddleware::check();

        $user = AuthMiddleware::getUser();
        $sucursal_id = $user['sucursal_id'];

        $producciones = $this->produccionModel->getWithDetails($sucursal_id);

        require_once __DIR__ . '/../Views/producciones/index.php';
    }

    public function create()
    {
        AuthMiddleware::checkRole(['administrador', 'empleado']);

        $user = AuthMiddleware::getUser();
        $sucursal_id = $user['sucursal_id'];

        // Cargar todos los productos de la sucursal
        $productos = $this->productoModel->getBySucursal($sucursal_id);

        require_once __DIR__ . '/../Views/producciones/create.php';
    }


    public function store()
    {
        AuthMiddleware::checkRole(['administrador', 'empleado']);

        header('Content-Type: application/json');

        $user = AuthMiddleware::getUser();
        $sucursal_id = $user['sucursal_id'];

        $negocio = $this->negocioModel->getBySucursal($sucursal_id);

        if (!$negocio) {
            echo json_encode(['success' => false, 'message' => 'No se encontró negocio para esta sucursal']);
            exit;
        }

        $produccion_data = [
        'id_negocio' => $negocio['id'],
        'id_sucursal' => $sucursal_id,
        'id_usuario' => $user['id'],
        'id_producto' => $_POST['id_producto'] ?? 0,
        'cantidad_producida' => $_POST['cantidad_producida'] ?? 0,
        'costo_total' => $_POST['costo_total'] ?? 0
        ];

        $insumos = json_decode($_POST['insumos'] ?? '[]', true);

    // LOG PARA DEBUG
        error_log('=== DEBUG PRODUCCIÓN ===');
        error_log('Datos producción: ' . print_r($produccion_data, true));
        error_log('Insumos recibidos: ' . print_r($insumos, true));

        try {
            if (empty($insumos)) {
                // Producción sin consumo de insumos
                $produccion_id = $this->produccionModel->create($produccion_data);
            } else {
                // Producción con consumo de insumos
                $produccion_id = $this->produccionModel->createWithInsumos($produccion_data, $insumos);
            }

            echo json_encode(['success' => true, 'message' => 'Producción registrada correctamente', 'id' => $produccion_id]);
        } catch (\Exception $e) {
            error_log('Error en producción: ' . $e->getMessage());
            error_log('Stack trace: ' . $e->getTraceAsString());
            echo json_encode(['success' => false, 'message' => 'Error al registrar producción: ' . $e->getMessage()]);
        }
        exit;
    }

    public function show($id)
    {
        AuthMiddleware::check();

        $user = AuthMiddleware::getUser();
        $sucursal_id = $user['sucursal_id'];

        // Bolt Optimization: Retrieve production, product and user info in a single round-trip
        $produccion = $this->produccionModel->getProduccionConDetalles($id, $sucursal_id);

        if (!$produccion) {
            header('Location: /producciones');
            exit;
        }

        $insumos = $this->produccionModel->getInsumos($id);

        require_once __DIR__ . '/../Views/producciones/show.php';
    }
}
