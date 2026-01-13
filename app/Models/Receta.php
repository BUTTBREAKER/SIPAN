<?php

namespace App\Models;

class Receta extends BaseModel {
    protected $table = 'recetas';

    /**
     * Crea una receta y sus insumos en una transacción.
     * Si no se pasa nombre, obtiene el nombre desde la tabla productos y lo usa.
     *
     * $insumos: array of ['id'|'id_insumo' => int, 'cantidad' => float, optionally 'unidad_medida' => string]
     */
    public function createWithInsumos($id_producto, $rendimiento, $instrucciones, $sucursal_id, $insumos) {
        try {
            $this->db->beginTransaction();

            // Obtener nombre del producto para usarlo como nombre de la receta (si existe)
            $producto = $this->db->fetchOne("SELECT nombre FROM productos WHERE id = ?", [$id_producto]);
            $nombre_receta = $producto['nombre'] ?? null;

            // Insertar receta (incluye nombre tomado del producto)
            $sql = "INSERT INTO recetas (id_producto, id_sucursal, nombre, rendimiento, instrucciones) 
                    VALUES (?, ?, ?, ?, ?)";
            $this->db->execute($sql, [$id_producto, $sucursal_id, $nombre_receta, $rendimiento, $instrucciones]);

            $id_receta = $this->db->lastInsertId();

            // Agregar los insumos (acepta tanto 'id' como 'id_insumo' en el array)
            $sqlDetalle = "INSERT INTO receta_insumos (id_receta, id_insumo, cantidad, unidad_medida) VALUES (?, ?, ?, ?)";
            foreach ($insumos as $insumo) {
                $insumoId = $insumo['id'] ?? $insumo['id_insumo'] ?? null;
                $cantidad = isset($insumo['cantidad']) ? $insumo['cantidad'] : 0;
                $unidad = $insumo['unidad_medida'] ?? ($insumo['unidad'] ?? 'kg');

                if (!$insumoId) {
                    // ignorar entradas mal formadas
                    continue;
                }

                $this->db->execute($sqlDetalle, [$id_receta, $insumoId, $cantidad, $unidad]);
            }

            $this->db->commit();
            return $id_receta;
        } catch (\Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    /**
     * Buscar receta por id incluyendo nombre del producto asociado.
     */
    public function find($id) {
        $sql = "SELECT r.*, p.nombre AS producto_nombre
                FROM {$this->table} r
                LEFT JOIN productos p ON r.id_producto = p.id
                WHERE r.id = ?";
        return $this->db->fetchOne($sql, [$id]);
    }

    public function getByProducto($producto_id) {
        $sql = "SELECT * FROM {$this->table} WHERE id_producto = ?";
        return $this->db->fetchOne($sql, [$producto_id]);
    }

    /**
     * Retorna insumos de una receta. Mantengo las columnas previas y agrego nombres de insumo.
     */
    public function getInsumos($receta_id) {
        $sql = "SELECT ri.*, i.nombre, i.unidad_medida as unidad_insumo, i.stock_actual, i.precio_unitario
                FROM receta_insumos ri
                INNER JOIN insumos i ON ri.id_insumo = i.id
                WHERE ri.id_receta = ?";
        return $this->db->fetchAll($sql, [$receta_id]);
    }

    public function addInsumo($receta_id, $insumo_id, $cantidad, $unidad_medida = 'kg') {
        $sql = "INSERT INTO receta_insumos (id_receta, id_insumo, cantidad, unidad_medida) 
                VALUES (?, ?, ?, ?)";
        return $this->db->execute($sql, [$receta_id, $insumo_id, $cantidad, $unidad_medida]);
    }

    public function removeInsumo($receta_id, $insumo_id) {
        $sql = "DELETE FROM receta_insumos WHERE id_receta = ? AND id_insumo = ?";
        return $this->db->execute($sql, [$receta_id, $insumo_id]);
    }

    public function updateInsumo($receta_id, $insumo_id, $cantidad) {
        $sql = "UPDATE receta_insumos SET cantidad = ? WHERE id_receta = ? AND id_insumo = ?";
        return $this->db->execute($sql, [$cantidad, $receta_id, $insumo_id]);
    }

    /**
     * Reemplaza todos los insumos de la receta: borra los actuales y agrega los nuevos.
     * $insumos debe tener elementos con 'id'|'id_insumo', 'cantidad' y opcional 'unidad_medida'.
     */
    public function updateInsumos($receta_id, array $insumos) {
        try {
            $this->db->beginTransaction();

            // Borrar los anteriores
            $sqlDel = "DELETE FROM receta_insumos WHERE id_receta = ?";
            $this->db->execute($sqlDel, [$receta_id]);

            // Insertar de nuevo
            $sqlIns = "INSERT INTO receta_insumos (id_receta, id_insumo, cantidad, unidad_medida)
                       VALUES (?, ?, ?, ?)";
            foreach ($insumos as $i) {
                $insumoId = $i['id'] ?? $i['id_insumo'] ?? null;
                $cantidad = $i['cantidad'] ?? 0;
                $unidad = $i['unidad_medida'] ?? ($i['unidad'] ?? 'kg');

                if (!$insumoId) {
                    continue;
                }

                $this->db->execute($sqlIns, [$receta_id, $insumoId, $cantidad, $unidad]);
            }

            $this->db->commit();
            return true;
        } catch (\Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    public function calcularInsumos($producto_id, $cantidad) {
        $sql = "CALL sp_calcular_insumos_produccion(?, ?)";
        return $this->db->fetchAll($sql, [$producto_id, $cantidad]);
    }

    public function getWithDetails($sucursal_id) {
        $sql = "SELECT r.*, p.nombre as producto_nombre, 
                       COUNT(ri.id) as total_insumos
                FROM {$this->table} r
                INNER JOIN productos p ON r.id_producto = p.id
                LEFT JOIN receta_insumos ri ON r.id = ri.id_receta
                WHERE r.id_sucursal = ?
                GROUP BY r.id
                ORDER BY r.nombre";
        return $this->db->fetchAll($sql, [$sucursal_id]);
    }

    public function all($sucursal_id = null) {
        if ($sucursal_id === null) {
            $sql = "SELECT r.*, p.nombre as producto_nombre
                    FROM {$this->table} r
                    INNER JOIN productos p ON r.id_producto = p.id
                    ORDER BY r.nombre";
            return $this->db->fetchAll($sql);
        }

        $sql = "SELECT r.*, p.nombre as producto_nombre
                FROM {$this->table} r
                INNER JOIN productos p ON r.id_producto = p.id
                WHERE r.id_sucursal = ?
                ORDER BY r.nombre";
        return $this->db->fetchAll($sql, [$sucursal_id]);
    }

    public function getInsumosByReceta($receta_id) {
        $sql = "SELECT ri.*, i.nombre, i.unidad_medida, i.stock_actual, i.precio_unitario
                FROM receta_insumos ri
                INNER JOIN insumos i ON ri.id_insumo = i.id
                WHERE ri.id_receta = ?";
        return $this->db->fetchAll($sql, [$receta_id]);
    }

    public function findByProducto($producto_id) {
        $sql = "SELECT * FROM {$this->table} WHERE id_producto = ? LIMIT 1";
        return $this->db->fetchOne($sql, [$producto_id]);
    }

    public function getInsumosPorProducto($producto_id) {
        $sql = "SELECT ri.*, r.rendimiento as rendimiento_receta
                FROM recetas r
                INNER JOIN receta_insumos ri ON r.id = ri.id_receta
                WHERE r.id_producto = ?";
        return $this->db->fetchAll($sql, [$producto_id]);
    }
}
