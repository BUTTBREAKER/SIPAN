<?php

namespace App\Controllers;

use App\Models\Configuracion;
use App\Middlewares\AuthMiddleware;

class ConfigController
{
    private $configModel;

    public function __construct()
    {
        $this->configModel = new Configuracion();
    }

    public function refreshTasa()
    {
        AuthMiddleware::check(); // Ensure logged in

        header('Content-Type: application/json');
        ob_start(); // Buffer output

        try {
            $newRate = $this->configModel->updateTasaBCV();

            ob_clean(); // Clean buffer

            if ($newRate) {
                echo json_encode([
                    'success' => true,
                    'rate' => $newRate,
                    'message' => 'Tasa actualizada correctamente'
                ]);
            } else {
                echo json_encode([
                    'success' => false,
                    'message' => 'No se pudo obtener la tasa en este momento'
                ]);
            }
        } catch (\Exception $e) {
            ob_clean();
            echo json_encode([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ]);
        }
        exit;
    }
}
