<?php

namespace App\Controllers;

use App\Models\Venta;
use App\Models\SugerenciaCompra;
use App\Helpers\Predictor;
use App\Middlewares\AuthMiddleware;

class PrediccionesController
{
    private $ventaModel;
    private $sugerenciaModel;

    public function __construct()
    {
        $this->ventaModel = new Venta();
        $this->sugerenciaModel = new SugerenciaCompra();
    }

    public function index()
    {
        AuthMiddleware::check();
        require_once __DIR__ . '/../Views/predicciones/index.php';
    }

    public function getDatosVentas()
    {
        AuthMiddleware::check();
        header('Content-Type: application/json');

        $user = AuthMiddleware::getUser();
        $sucursal_id = $user['sucursal_id'];

        // Obtener historial de ventas (últimos 30 días para entrenar el modelo)
        // Necesitamos un método en VentaModel que devuelva [fecha => total]
        // Por ahora simularemos o adaptaremos uno existente

        // $historial = $this->ventaModel->getVentasDiarias($sucursal_id, 30);

        // Nota: Como getVentasDiarias devuelve array de objetos, lo transformamos
        $raw_data = $this->ventaModel->getVentasPorPeriodo($sucursal_id, 30); // Usando método existente del Dashboard

        $datos_historicos = [];
        foreach ($raw_data as $row) {
            $datos_historicos[$row['fecha']] = floatval($row['total']);
        }

        // Si tenemos pocos datos, no podemos predecir bien
        if (count($datos_historicos) < 5) {
            echo json_encode([
                'success' => false,
                'message' => 'Insuficientes datos históricos para realizar una predicción fiable (mínimo 5 días).'
            ]);
            exit;
        }

        // Calcular Regresión Lineal (Proyectar 7 días)
        $resultado = Predictor::regresionLineal($datos_historicos, 7);

        // Calcular Media Móvil (Periodo 3)
        $media_movil = Predictor::mediaMovil($datos_historicos, 3);

        echo json_encode([
            'success' => true,
            'historico' => array_map(function ($k, $v, $sma) {
                return ['fecha' => $k, 'valor' => $v, 'media_movil' => $sma];
            }, array_keys($datos_historicos), $datos_historicos, $media_movil),
            'prediccion' => $resultado['proyecciones'],
            'tendencia' => $resultado['tendencia'],
            'info_modelo' => [
                'pendiente' => round($resultado['pendiente'], 4),
                'interseccion' => round($resultado['interseccion'], 2)
            ]
        ]);
        exit;
    }

    public function generarSugerenciasAutomaticas()
    {
        AuthMiddleware::checkRole(['administrador', 'empleado']);
        header('Content-Type: application/json');

        $user = AuthMiddleware::getUser();
        $sucursal_id = $user['sucursal_id'];

        try {
            // Utilizamos el modelo existente que llama al SP
            $resultado = $this->sugerenciaModel->generar($sucursal_id);
            echo json_encode([
                'success' => true,
                'message' => 'Sugerencias analizadas y generadas correctamente',
                'total' => $resultado['sugerencias_generadas'] ?? 0
            ]);
        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'message' => 'Error al generar sugerencias: ' . $e->getMessage()
            ]);
        }
        exit;
    }
}
