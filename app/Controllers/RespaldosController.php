<?php

namespace App\Controllers;

use App\Models\Respaldo;
use App\Middlewares\AuthMiddleware;

class RespaldosController
{
    private $respaldoModel;

    public function __construct()
    {
        $this->respaldoModel = new Respaldo();
    }

    public function index()
    {
        AuthMiddleware::checkRole(['administrador']);

        $respaldos = $this->respaldoModel->getAll();

        require_once __DIR__ . '/../Views/respaldos/index.php';
    }

    public function generar()
    {
        AuthMiddleware::checkRole(['administrador']);

        header('Content-Type: application/json');

        $user = AuthMiddleware::getUser();

        try {
            $resultado = $this->respaldoModel->generarRespaldo($user['id']);

            if ($resultado['success']) {
                echo json_encode([
                    'success' => true,
                    'message' => 'Respaldo generado correctamente',
                    'archivo' => $resultado['archivo']
                ]);
            } else {
                echo json_encode([
                    'success' => false,
                    'message' => $resultado['error']
                ]);
            }
        } catch (\Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => 'Error al generar respaldo: ' . $e->getMessage()
            ]);
        }
        exit;
    }

    public function restaurar()
    {
        AuthMiddleware::checkRole(['administrador']);

        header('Content-Type: application/json');

        // Leer del body JSON en lugar de $_POST
        $input = json_decode(file_get_contents('php://input'), true);
        $respaldo_id = $input['id'] ?? $_POST['respaldo_id'] ?? 0;

        if (!$respaldo_id) {
            echo json_encode(['success' => false, 'message' => 'ID de respaldo no válido']);
            exit;
        }

        try {
            $resultado = $this->respaldoModel->restaurarRespaldo($respaldo_id);

            if ($resultado['success']) {
                echo json_encode(['success' => true, 'message' => $resultado['mensaje']]);
            } else {
                echo json_encode(['success' => false, 'message' => $resultado['error']]);
            }
        } catch (\Exception $e) {
            echo json_encode(['success' => false, 'message' => 'Error al restaurar respaldo: ' . $e->getMessage()]);
        }
        exit;
    }

    public function descargar($id)
    {
        AuthMiddleware::checkRole(['administrador']);

        $respaldo = $this->respaldoModel->find($id);

        if (!$respaldo || !file_exists($respaldo['ruta_archivo'])) {
            header('Location: /respaldos');
            exit;
        }

        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . $respaldo['nombre_archivo'] . '"');
        header('Content-Length: ' . filesize($respaldo['ruta_archivo']));

        readfile($respaldo['ruta_archivo']);
        exit;
    }
}
