<?php

namespace App\Controllers;

use App\Middlewares\AuthMiddleware;
use App\Models\Receta;
use App\Models\Insumo;

class CalculoInsumosController
{
    private $recetaModel;
    private $insumoModel;

    public function __construct()
    {
        AuthMiddleware::checkAuth();
        $this->recetaModel = new Receta();
        $this->insumoModel = new Insumo();
    }

    public function calcularInsumos()
    {
        if (ob_get_length()) {
            ob_clean();
        }
        header('Content-Type: application/json');

        $data = json_decode(file_get_contents('php://input'), true);
        $id_receta = $data['id_receta'] ?? null;
        $cantidad_producir = $data['cantidad_producir'] ?? 0;

        if (!$id_receta || $cantidad_producir <= 0) {
            echo json_encode([
                'success' => false,
                'message' => 'Datos inválidos'
            ]);
            exit;
        }

        // Obtener receta
        $receta = $this->recetaModel->find($id_receta);

        if (!$receta) {
            echo json_encode([
                'success' => false,
                'message' => 'Receta no encontrada'
            ]);
            exit;
        }

        // Obtener insumos de la receta
        $insumos_receta = $this->recetaModel->getInsumosByReceta($id_receta);

        if (empty($insumos_receta)) {
            echo json_encode([
                'success' => false,
                'message' => 'La receta no tiene insumos configurados'
            ]);
            exit;
        }

        // Calcular factor de multiplicación
        $factor = $cantidad_producir / $receta['rendimiento'];

        $insumos_necesarios = [];
        $insumos_faltantes = [];
        $puede_producir = true;

        foreach ($insumos_receta as $insumo_receta) {
            $cantidad_necesaria = $insumo_receta['cantidad'] * $factor;

            // Obtener stock actual del insumo
            $insumo = $this->insumoModel->find($insumo_receta['id_insumo']);
            $stock_actual = $insumo['stock_actual'];

            $suficiente = $stock_actual >= $cantidad_necesaria;

            if (!$suficiente) {
                $puede_producir = false;
                $insumos_faltantes[] = [
                    'nombre' => $insumo['nombre'],
                    'necesario' => $cantidad_necesaria,
                    'disponible' => $stock_actual,
                    'faltante' => $cantidad_necesaria - $stock_actual,
                    'unidad' => $insumo['unidad_medida']
                ];
            }

            $insumos_necesarios[] = [
                'id_insumo' => $insumo['id'],
                'nombre' => $insumo['nombre'],
                'cantidad_necesaria' => $cantidad_necesaria,
                'stock_actual' => $stock_actual,
                'unidad_medida' => $insumo['unidad_medida'],
                'precio_unitario' => $insumo['precio_unitario'],
                'costo_total' => $cantidad_necesaria * $insumo['precio_unitario'],
                'suficiente' => $suficiente
            ];
        }

        // Calcular costo total de producción
        $costo_total = array_sum(array_column($insumos_necesarios, 'costo_total'));
        $costo_por_unidad = $costo_total / $cantidad_producir;

        echo json_encode([
            'success' => true,
            'puede_producir' => $puede_producir,
            'receta' => [
                'nombre' => $receta['nombre'],
                'rendimiento' => $receta['rendimiento']
            ],
            'cantidad_producir' => $cantidad_producir,
            'factor' => $factor,
            'insumos_necesarios' => $insumos_necesarios,
            'insumos_faltantes' => $insumos_faltantes,
            'costo_total' => $costo_total,
            'costo_por_unidad' => $costo_por_unidad
        ]);
        exit;
    }

    public function verificarDisponibilidad()
    {
        header('Content-Type: application/json');

        $data = json_decode(file_get_contents('php://input'), true);
        $id_producto = $data['id_producto'] ?? null;
        $cantidad = $data['cantidad'] ?? 0;

        if (!$id_producto || $cantidad <= 0) {
            echo json_encode([
                'success' => false,
                'message' => 'Datos inválidos'
            ]);
            exit;
        }

        // Buscar receta del producto
        $receta = $this->recetaModel->findByProducto($id_producto);

        if (!$receta) {
            echo json_encode([
                'success' => false,
                'message' => 'No hay receta configurada para este producto'
            ]);
            exit;
        }

        // Calcular insumos necesarios
        $data_calculo = [
            'id_receta' => $receta['id'],
            'cantidad_producir' => $cantidad
        ];

        // Reutilizar la lógica de calcularInsumos
        $this->calcularInsumos();
    }
}
