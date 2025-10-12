<?php

namespace SIPAN\Controllers;

use SIPAN\Middlewares\AuthMiddleware;
use SIPAN\Models\Venta;
use SIPAN\Models\Producto;
use SIPAN\Models\Insumo;
use SIPAN\Models\Receta;
use SIPAN\Models\SugerenciaCompra;

class PrediccionesController {
    private $ventaModel;
    private $productoModel;
    private $insumoModel;
    private $recetaModel;
    private $sugerenciaModel;
    
    public function __construct() {
        AuthMiddleware::checkAuth();
        $this->ventaModel = new Venta();
        $this->productoModel = new Producto();
        $this->insumoModel = new Insumo();
        $this->recetaModel = new Receta();
        $this->sugerenciaModel = new SugerenciaCompra();
    }
    
    public function index() {
        $user = AuthMiddleware::getUser();
        $sucursal_id = $user['id_sucursal'];
        
        // Obtener predicciones
        $predicciones_ventas = $this->predecirVentas($sucursal_id);
        $predicciones_produccion = $this->predecirProduccion($sucursal_id);
        $sugerencias_compra = $this->generarSugerenciasCompra($sucursal_id);
        
        require_once __DIR__ . '/../Views/predicciones/index.php';
    }
    
    /**
     * Predecir ventas basadas en histórico
     */
    private function predecirVentas($sucursal_id, $dias_futuro = 7) {
        // Obtener ventas de los últimos 30 días
        $sql = "SELECT DATE(fecha_venta) as fecha, COUNT(*) as cantidad_ventas, SUM(total) as total_ventas
                FROM ventas
                WHERE id_sucursal = ? AND fecha_venta >= DATE_SUB(NOW(), INTERVAL 30 DAY)
                GROUP BY DATE(fecha_venta)
                ORDER BY fecha ASC";
        
        $ventas_historicas = $this->ventaModel->db->fetchAll($sql, [$sucursal_id]);
        
        if (empty($ventas_historicas)) {
            return [];
        }
        
        // Calcular promedio de ventas por día
        $total_ventas = array_sum(array_column($ventas_historicas, 'total_ventas'));
        $dias_con_ventas = count($ventas_historicas);
        $promedio_diario = $total_ventas / $dias_con_ventas;
        
        // Calcular tendencia (regresión lineal simple)
        $tendencia = $this->calcularTendencia($ventas_historicas);
        
        // Generar predicciones
        $predicciones = [];
        for ($i = 1; $i <= $dias_futuro; $i++) {
            $fecha = date('Y-m-d', strtotime("+{$i} days"));
            $prediccion = $promedio_diario + ($tendencia * $i);
            
            $predicciones[] = [
                'fecha' => $fecha,
                'venta_estimada' => max(0, $prediccion), // No puede ser negativo
                'confianza' => $this->calcularConfianza($ventas_historicas)
            ];
        }
        
        return $predicciones;
    }
    
    /**
     * Predecir necesidades de producción
     */
    private function predecirProduccion($sucursal_id) {
        // Obtener productos más vendidos en los últimos 7 días
        $sql = "SELECT p.id, p.nombre, SUM(dv.cantidad) as total_vendido,
                AVG(dv.cantidad) as promedio_diario
                FROM detalle_ventas dv
                INNER JOIN productos p ON dv.id_producto = p.id
                INNER JOIN ventas v ON dv.id_venta = v.id
                WHERE v.id_sucursal = ? AND v.fecha_venta >= DATE_SUB(NOW(), INTERVAL 7 DAY)
                GROUP BY p.id, p.nombre
                ORDER BY total_vendido DESC
                LIMIT 10";
        
        $productos_vendidos = $this->ventaModel->db->fetchAll($sql, [$sucursal_id]);
        
        $predicciones = [];
        
        foreach ($productos_vendidos as $producto) {
            // Obtener stock actual
            $producto_data = $this->productoModel->find($producto['id']);
            $stock_actual = $producto_data['stock_actual'];
            $stock_minimo = $producto_data['stock_minimo'];
            
            // Calcular días de stock restante
            $promedio_diario = $producto['promedio_diario'];
            $dias_stock = $promedio_diario > 0 ? $stock_actual / $promedio_diario : 999;
            
            // Calcular cantidad a producir
            $cantidad_sugerida = 0;
            $prioridad = 'baja';
            
            if ($dias_stock < 2) {
                $prioridad = 'alta';
                $cantidad_sugerida = ceil($promedio_diario * 7); // Producir para 7 días
            } elseif ($dias_stock < 4) {
                $prioridad = 'media';
                $cantidad_sugerida = ceil($promedio_diario * 5); // Producir para 5 días
            } elseif ($stock_actual < $stock_minimo) {
                $prioridad = 'media';
                $cantidad_sugerida = ceil($promedio_diario * 3); // Producir para 3 días
            }
            
            if ($cantidad_sugerida > 0) {
                $predicciones[] = [
                    'producto' => $producto['nombre'],
                    'stock_actual' => $stock_actual,
                    'promedio_diario' => round($promedio_diario, 2),
                    'dias_stock' => round($dias_stock, 1),
                    'cantidad_sugerida' => $cantidad_sugerida,
                    'prioridad' => $prioridad
                ];
            }
        }
        
        return $predicciones;
    }
    
    /**
     * Generar sugerencias de compra automáticas
     */
    private function generarSugerenciasCompra($sucursal_id) {
        // Obtener insumos con stock bajo
        $sql = "SELECT i.*, 
                (i.stock_minimo - i.stock_actual) as cantidad_faltante,
                CASE 
                    WHEN i.stock_actual <= i.stock_minimo * 0.3 THEN 'alta'
                    WHEN i.stock_actual <= i.stock_minimo * 0.6 THEN 'media'
                    ELSE 'baja'
                END as prioridad
                FROM insumos i
                WHERE i.id_sucursal = ? AND i.stock_actual < i.stock_minimo
                ORDER BY prioridad DESC, cantidad_faltante DESC";
        
        $insumos_bajos = $this->insumoModel->db->fetchAll($sql, [$sucursal_id]);
        
        $sugerencias = [];
        
        foreach ($insumos_bajos as $insumo) {
            // Calcular cantidad óptima de compra
            $cantidad_optima = max(
                $insumo['cantidad_faltante'],
                $insumo['stock_minimo'] * 2 // Comprar para tener el doble del mínimo
            );
            
            // Calcular costo estimado
            $costo_estimado = $cantidad_optima * $insumo['precio_unitario'];
            
            $sugerencias[] = [
                'insumo' => $insumo['nombre'],
                'stock_actual' => $insumo['stock_actual'],
                'stock_minimo' => $insumo['stock_minimo'],
                'cantidad_sugerida' => ceil($cantidad_optima),
                'unidad_medida' => $insumo['unidad_medida'],
                'costo_estimado' => $costo_estimado,
                'prioridad' => $insumo['prioridad']
            ];
        }
        
        return $sugerencias;
    }
    
    /**
     * Calcular tendencia de ventas (regresión lineal)
     */
    private function calcularTendencia($datos) {
        $n = count($datos);
        if ($n < 2) return 0;
        
        $sum_x = 0;
        $sum_y = 0;
        $sum_xy = 0;
        $sum_x2 = 0;
        
        foreach ($datos as $i => $dato) {
            $x = $i + 1;
            $y = $dato['total_ventas'];
            
            $sum_x += $x;
            $sum_y += $y;
            $sum_xy += $x * $y;
            $sum_x2 += $x * $x;
        }
        
        // Pendiente de la línea de tendencia
        $pendiente = ($n * $sum_xy - $sum_x * $sum_y) / ($n * $sum_x2 - $sum_x * $sum_x);
        
        return $pendiente;
    }
    
    /**
     * Calcular nivel de confianza de la predicción
     */
    private function calcularConfianza($datos) {
        $n = count($datos);
        
        if ($n < 7) return 'baja';
        if ($n < 15) return 'media';
        return 'alta';
    }
    
    /**
     * Generar sugerencias automáticas (ejecutar periódicamente)
     */
    public function generarSugerenciasAutomaticas() {
        header('Content-Type: application/json');
        
        $user = AuthMiddleware::getUser();
        $sucursal_id = $user['id_sucursal'];
        
        try {
            $sugerencias = $this->generarSugerenciasCompra($sucursal_id);
            
            // Guardar sugerencias en la base de datos
            foreach ($sugerencias as $sugerencia) {
                $this->sugerenciaModel->create([
                    'id_insumo' => $this->insumoModel->findByNombre($sugerencia['insumo'])['id'],
                    'id_sucursal' => $sucursal_id,
                    'cantidad_sugerida' => $sugerencia['cantidad_sugerida'],
                    'prioridad' => $sugerencia['prioridad'],
                    'estado' => 'pendiente'
                ]);
            }
            
            echo json_encode([
                'success' => true,
                'message' => 'Sugerencias generadas correctamente',
                'cantidad' => count($sugerencias)
            ]);
        } catch (\Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => 'Error al generar sugerencias: ' . $e->getMessage()
            ]);
        }
        exit;
    }
}

