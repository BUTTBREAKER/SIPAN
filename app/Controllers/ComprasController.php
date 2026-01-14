<?php

namespace App\Controllers;

use App\Models\Compra;
use App\Models\Insumo;
use App\Models\Proveedor;
use App\Middlewares\AuthMiddleware;

class ComprasController
{
    private $compraModel;
    private $insumoModel;
    private $proveedorModel;

    public function __construct()
    {
        $this->compraModel = new Compra();
        $this->insumoModel = new Insumo();
        $this->proveedorModel = new Proveedor();
    }

    public function index()
    {
        AuthMiddleware::check();
        $user = AuthMiddleware::getUser();
        $compras = $this->compraModel->getWithProveedor($user['sucursal_id']);
        require_once __DIR__ . '/../Views/compras/index.php';
    }

    public function create()
    {
        AuthMiddleware::checkRole(['administrador', 'empleado']); // Cajero no compra
        $user = AuthMiddleware::getUser();

        $proveedores = $this->proveedorModel->getAllBySucursal($user['sucursal_id']);
        // Cargamos insumos para el autocompletado/selección
        $insumos = $this->insumoModel->all($user['sucursal_id']);

        require_once __DIR__ . '/../Views/compras/create.php';
    }

    public function store()
    {
        AuthMiddleware::checkRole(['administrador', 'empleado']);
        header('Content-Type: application/json');

        $user = AuthMiddleware::getUser();

        // Validación básica
        if (empty($_POST['detalles']) || empty($_POST['id_proveedor'])) {
            echo json_encode(['success' => false, 'message' => 'Datos incompletos']);
            exit;
        }

        $detalles = json_decode($_POST['detalles'], true);

        $compraData = [
            'id_sucursal' => $user['sucursal_id'],
            'id_usuario' => $user['id'],
            'id_proveedor' => $_POST['id_proveedor'],
            'fecha_compra' => $_POST['fecha_compra'] ?? date('Y-m-d H:i:s'),
            'numero_comprobante' => $_POST['numero_comprobante'] ?? '',
            'total' => $_POST['total'] ?? 0,
            'estado' => 'completada'
        ];

        try {
            $id = $this->compraModel->createWithDetails($compraData, $detalles);
            echo json_encode(['success' => true, 'message' => 'Compra registrada correctamente', 'id' => $id]);
        } catch (\Exception $e) {
            echo json_encode(['success' => false, 'message' => 'Error al registrar compra: ' . $e->getMessage()]);
        }
    }

    public function show($id)
    {
        AuthMiddleware::check();
        $user = AuthMiddleware::getUser();

        $compra = $this->compraModel->getById($id);

        if (!$compra || $compra['id_sucursal'] != $user['sucursal_id']) {
            header('Location: /compras');
            exit;
        }

        $detalles = $this->compraModel->getDetalles($id);

        require_once __DIR__ . '/../Views/compras/show.php';
    }
}
