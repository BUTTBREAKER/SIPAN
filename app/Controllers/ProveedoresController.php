<?php

namespace App\Controllers;

use App\Models\Proveedor;
use App\Models\Insumo;
use App\Middlewares\AuthMiddleware;

class ProveedoresController {
    private $proveedorModel;
    private $insumoModel;

    public function __construct() {
        $this->proveedorModel = new Proveedor();
        $this->insumoModel = new Insumo();
    }

    public function index() {
        AuthMiddleware::check();
        $user = AuthMiddleware::getUser();
        $proveedores = $this->proveedorModel->getAllBySucursal($user['sucursal_id']);
        require_once __DIR__ . '/../Views/proveedores/index.php';
    }

    public function create() {
        AuthMiddleware::checkRole(['administrador']);
        $user = AuthMiddleware::getUser();
        $insumos = $this->insumoModel->getAllBySucursal($user['sucursal_id']);
        require_once __DIR__ . '/../Views/proveedores/create.php';
    }

    public function store() {
        AuthMiddleware::checkRole(['administrador']);
        header('Content-Type: application/json');

        $user = AuthMiddleware::getUser();
        $data = [
            'nombre' => $_POST['nombre'] ?? '',
            'rif' => $_POST['rif'] ?? '',
            'telefono' => $_POST['telefono'] ?? '',
            'correo' => $_POST['correo'] ?? '',
            'direccion' => $_POST['direccion'] ?? '',
            'observaciones' => $_POST['observaciones'] ?? '',
            'sucursal_id' => $user['sucursal_id']
        ];

        $insumos = json_decode($_POST['insumos'] ?? '[]', true);

        try {
            $id = $this->proveedorModel->create($data);
            if (!empty($insumos)) {
                $this->proveedorModel->addInsumos($id, $insumos);
            }
            echo json_encode(['success' => true, 'message' => 'Proveedor registrado correctamente']);
        } catch (\Exception $e) {
            echo json_encode(['success' => false, 'message' => 'Error al registrar proveedor: ' . $e->getMessage()]);
        }
    }

    public function edit($id) {
        AuthMiddleware::checkRole(['administrador']);
        
        $proveedor = $this->proveedorModel->find($id);
        if (!$proveedor) {
            header('Location: /proveedores');
            exit;
        }

        $user = AuthMiddleware::getUser();
        $insumos = $this->insumoModel->getAllBySucursal($user['sucursal_id']);
        
        // Obtener raw data
        $raw_asociados = $this->proveedorModel->getWithInsumos($id);
        
        // Filtrar validos (donde id_insumo no sea null)
        $insumos_asociados = array_filter($raw_asociados, function($row) {
            return !empty($row['id_insumo']);
        });
        
        // Re-indexar array para que sea [0, 1, 2...] y no [0, 5, 8...] al convertir a JSON
        $insumos_asociados = array_values($insumos_asociados);

        require_once __DIR__ . '/../Views/proveedores/edit.php';
    }

    public function update($id) {
        AuthMiddleware::checkRole(['administrador']);
        header('Content-Type: application/json');

        $data = [
            'nombre' => $_POST['nombre'] ?? '',
            'rif' => $_POST['rif'] ?? '',
            'telefono' => $_POST['telefono'] ?? '',
            'correo' => $_POST['correo'] ?? '',
            'direccion' => $_POST['direccion'] ?? '',
            'observaciones' => $_POST['observaciones'] ?? '',
        ];

        $insumos = json_decode($_POST['insumos'] ?? '[]', true);

        try {
            $this->proveedorModel->update($id, $data);
            $this->proveedorModel->addInsumos($id, $insumos);
            echo json_encode(['success' => true, 'message' => 'Proveedor actualizado correctamente']);
        } catch (\Exception $e) {
            echo json_encode(['success' => false, 'message' => 'Error al actualizar proveedor: ' . $e->getMessage()]);
        }
    }

    public function delete($id) {
        AuthMiddleware::checkRole(['administrador']);
        $this->proveedorModel->delete($id);
        header('Location: /proveedores');
    }

public function insumosSinProveedor() {
    AuthMiddleware::check();
    header('Content-Type: application/json');

    $user = AuthMiddleware::getUser();
    $sucursal_id = $user['sucursal_id'];

    try {
        $insumos = $this->proveedorModel->getInsumosSinProveedor($sucursal_id);
        echo json_encode(['success' => true, 'insumos' => $insumos]);
    } catch (\Exception $e) {
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
    exit;
}



    public function show($id) {
        AuthMiddleware::check();
        
        $proveedor = $this->proveedorModel->find($id);
        if (!$proveedor) {
            header('Location: /proveedores');
            exit;
        }

        // Insumos que provee (usando la nueva columna directa)
        $insumos = $this->insumoModel->getByProveedor($id);
        
        // Historial de Compras
        $compraModel = new \App\Models\Compra();
        $compras = $compraModel->getByProveedor($id);
        
        // Calcular deuda total (estado_pago != pagado)
        // Nota: Asumiendo que existe columna 'estado_pago' o 'monto_deuda' en compras
        // Si no existe, usamos total para todas las que no estén pagadas
        $deuda_total = 0;
        foreach ($compras as $compra) {
            if (($compra['estado_pago'] ?? '') !== 'pagado') {
                $deuda_total += ($compra['monto_deuda'] ?? 0); // O saldo_pendiente si existe
            }
        }
        
        require_once __DIR__ . '/../Views/proveedores/show.php';
    }

}
