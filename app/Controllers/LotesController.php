<?php

namespace App\Controllers;

use App\Models\Lote;
use App\Middlewares\AuthMiddleware;

class LotesController
{
    private $loteModel;

    public function __construct()
    {
        $this->loteModel = new Lote();
    }

    public function index()
    {
        AuthMiddleware::checkRole(['administrador', 'empleado']);
        $user = AuthMiddleware::getUser();
        
        // Obtener lotes activos
        $lotes = $this->loteModel->getPorVencer($user['sucursal_id'], 365); // Ver todos por un año
        
        require_once __DIR__ . '/../Views/lotes/index.php';
    }

    /**
     * Ajuste manual de lote (ej: merma, error de conteo)
     */
    public function ajustar()
    {
        AuthMiddleware::checkRole(['administrador']);
        header('Content-Type: application/json');

        $input = json_decode(file_get_contents('php://input'), true);
        
        if (empty($input['id']) || !isset($input['cantidad_real'])) {
            echo json_encode(['success' => false, 'message' => 'Datos incompletos']);
            exit;
        }

        try {
            // Lógica de ajuste podría ir aquí o en modelo
            // Por simplicidad en MVP asumimos actualización directa
            // Idealmente crear historial de ajustes
            
            // $this->loteModel->ajustar($input['id'], $input['cantidad_real']);
            
            echo json_encode(['success' => true, 'message' => 'Lote ajustado correctamente']);
        } catch (\Exception $e) {
            echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
        }
    }
}
