<?php

namespace App\Models;

class Sucursal extends BaseModel {
    protected $table = 'sucursales';
    
    /**
     * Obtener todas las sucursales de un negocio
     */
    public function getByNegocio($negocio_id) {
        // Por ahora sin filtro de negocio hasta que corrijas la BD
        $sql = "SELECT * FROM {$this->table} ORDER BY nombre ASC";
        return $this->db->fetchAll($sql);
    }
    
    /**
     * Obtener solo sucursales activas
     */
    public function getActivas($negocio_id = null) {
        $sql = "SELECT * FROM {$this->table} WHERE estado = 'activa' ORDER BY nombre";
        return $this->db->fetchAll($sql);
    }
    
    /**
     * Obtener nombre de sucursal por ID
     */
    public function getNombre($id) {
        $sql = "SELECT nombre FROM {$this->table} WHERE id = ?";
        $result = $this->db->fetchOne($sql, [$id]);
        return $result['nombre'] ?? '';
    }
    
    /**
     * Buscar sucursal por clave
     */
    public function findByClave($clave) {
        $sql = "SELECT * FROM {$this->table} WHERE clave_sucursal = ? AND estado = 'activa' LIMIT 1";
        return $this->db->fetchOne($sql, [$clave]);
    }
    
    /**
     * Obtener estadísticas de una sucursal
     */
    public function getStats($sucursal_id) {
        $sql = "SELECT 
                    COUNT(*) as total_empleados,
                    SUM(CASE WHEN estado = 'activo' THEN 1 ELSE 0 END) as empleados_activos,
                    SUM(CASE WHEN estado = 'inactivo' THEN 1 ELSE 0 END) as empleados_inactivos
                FROM usuarios 
                WHERE id_sucursal = ?";
        
        $result = $this->db->fetchOne($sql, [$sucursal_id]);
        
        return [
            'total_empleados' => (int)($result['total_empleados'] ?? 0),
            'empleados_activos' => (int)($result['empleados_activos'] ?? 0),
            'empleados_inactivos' => (int)($result['empleados_inactivos'] ?? 0)
        ];
    }
    
    /**
     * Obtener empleados de una sucursal
     * Concatena los nombres completos según la estructura real de la tabla
     */
    public function getEmpleados($sucursal_id) {
        $sql = "SELECT 
                    u.id,
                    CONCAT_WS(' ', u.primer_nombre, u.segundo_nombre, u.apellido_paterno, u.apellido_materno) as nombre,
                    u.correo,
                    u.rol,
                    u.estado 
                FROM usuarios u
                WHERE u.id_sucursal = ? 
                ORDER BY u.primer_nombre, u.apellido_paterno ASC";
        
        return $this->db->fetchAll($sql, [$sucursal_id]) ?? [];
    }

    
}