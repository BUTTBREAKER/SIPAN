<?php

namespace App\Models;

use App\Core\Database;

class OrdenCompra extends BaseModel
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    // Crear la orden y devolver el ID
    public function crear($data)
    {
        $sql = "INSERT INTO ordenes_compra 
                (id_proveedor, id_usuario, sucursal_id, estado, observaciones) 
                VALUES (:id_proveedor, :id_usuario, :sucursal_id, 'pendiente', :observaciones)";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':id_proveedor' => $data['id_proveedor'],
            ':id_usuario' => $data['id_usuario'],
            ':sucursal_id' => $data['sucursal_id'],
            ':observaciones' => $data['observaciones'] ?? null
        ]);

        return $this->db->lastInsertId();
    }

    // Marcar orden como recibida
    public function marcarRecibida($id)
    {
        $sql = "UPDATE ordenes_compra SET estado = 'recibida' WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([':id' => $id]);
    }

    // Obtener orden con proveedor
    public function find($id)
    {
        $sql = "SELECT oc.*, p.nombre AS proveedor
                FROM ordenes_compra oc
                JOIN proveedores p ON p.id = oc.id_proveedor
                WHERE oc.id = :id";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $id]);
        return $stmt->fetch();
    }

    // Listar todas las órdenes de la sucursal
    public function listarPorSucursal($id_sucursal)
    {
        $sql = "SELECT oc.*, p.nombre AS proveedor
                FROM ordenes_compra oc
                JOIN proveedores p ON p.id = oc.id_proveedor
                WHERE oc.sucursal_id = :id_sucursal
                ORDER BY oc.id DESC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id_sucursal' => $id_sucursal]);
        return $stmt->fetchAll();
    }
}
