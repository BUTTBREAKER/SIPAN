<?php

namespace App\Controllers;

use App\Models\SugerenciaCompra;
use App\Middlewares\AuthMiddleware;

class SugerenciasController
{
    private $sugerenciaModel;

    public function __construct()
    {
        $this->sugerenciaModel = new SugerenciaCompra();
    }

    public function index()
    {
        AuthMiddleware::check();

        $user = AuthMiddleware::getUser();
        $sucursal_id = $user['sucursal_id'];

        $estado = $_GET['estado'] ?? null;

        $sugerencias = $this->sugerenciaModel->getWithDetails($sucursal_id, $estado);

        require_once __DIR__ . '/../Views/sugerencias/index.php';
    }

    public function generar()
    {
        AuthMiddleware::checkRole(['administrador', 'empleado']);

        header('Content-Type: application/json');

        $user = AuthMiddleware::getUser();
        $sucursal_id = $user['sucursal_id'];

        try {
            $resultado = $this->sugerenciaModel->generar($sucursal_id);
            echo json_encode(['success' => true, 'message' => 'Sugerencias generadas correctamente', 'total' => $resultado['sugerencias_generadas']]);
        } catch (\Exception $e) {
            echo json_encode(['success' => false, 'message' => 'Error al generar sugerencias: ' . $e->getMessage()]);
        }
        exit;
    }

    public function aprobar()
    {
        AuthMiddleware::checkRole(['administrador', 'empleado']);

        header('Content-Type: application/json');

        $id = $_POST['id'] ?? 0;

        if (!$id) {
            echo json_encode(['success' => false, 'message' => 'ID no válido']);
            exit;
        }

        try {
            $this->sugerenciaModel->aprobar($id);
            echo json_encode(['success' => true, 'message' => 'Sugerencia aprobada']);
        } catch (\Exception $e) {
            echo json_encode(['success' => false, 'message' => 'Error al aprobar sugerencia: ' . $e->getMessage()]);
        }
        exit;
    }

    public function rechazar()
    {
        AuthMiddleware::checkRole(['administrador', 'empleado']);

        header('Content-Type: application/json');

        $id = $_POST['id'] ?? 0;

        if (!$id) {
            echo json_encode(['success' => false, 'message' => 'ID no válido']);
            exit;
        }

        try {
            $this->sugerenciaModel->rechazar($id);
            echo json_encode(['success' => true, 'message' => 'Sugerencia rechazada']);
        } catch (\Exception $e) {
            echo json_encode(['success' => false, 'message' => 'Error al rechazar sugerencia: ' . $e->getMessage()]);
        }
        exit;
    }

    public function completar()
    {
        AuthMiddleware::checkRole(['administrador', 'empleado']);

        header('Content-Type: application/json');

        $id = $_POST['id'] ?? 0;

        if (!$id) {
            echo json_encode(['success' => false, 'message' => 'ID no válido']);
            exit;
        }

        try {
            $this->sugerenciaModel->completar($id);
            echo json_encode(['success' => true, 'message' => 'Sugerencia marcada como completada']);
        } catch (\Exception $e) {
            echo json_encode(['success' => false, 'message' => 'Error al completar sugerencia: ' . $e->getMessage()]);
        }
        exit;
    }
}
