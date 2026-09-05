<?php

declare(strict_types=1);

namespace App\Models;

use Exception;
use OutOfBoundsException;
use PDOException;

final class Auditoria extends BaseModel
{
    protected string $table = 'auditoria';

    /**
     * Obtener auditorías por usuario
     * @return list<array{
     *   id: int,
     *   id_usuario: int,
     *   id_sucursal: ?int,
     *   tabla: string,
     *   accion: 'INSERT'|'UPDATE'|'DELETE'|'UNDO',
     *   registro_id: int,
     *   datos_anteriores: ?string,
     *   datos_nuevos: ?string,
     *   ip_address: ?string,
     *   user_agent: ?string,
     *   fecha_accion: string,
     *   deshacer: null|0|1,
     *   fecha_deshacer: ?string,
     *   usuario_deshacer: ?int,
     *   usuario_nombre: string,
     * }>
     * @throws PDOException
     */
    public function getByUsuario(int $usuario_id, int $limit = 20): array
    {
        $sql = "
            SELECT a.*,
            CONCAT_WS(
                ' ',
                u.primer_nombre,
                u.segundo_nombre,
                u.apellido_paterno,
                u.apellido_materno
            ) usuario_nombre
            FROM $this->table a
            LEFT JOIN usuarios u ON a.id_usuario = u.id
            WHERE a.id_usuario = ?
            ORDER BY a.fecha_accion DESC
            LIMIT ?
        ";

        return $this->db->fetchAll($sql, [$usuario_id, $limit]);
    }

    /**
     * @param 'INSERT'|'UPDATE'|'DELETE'|'UNDO' $accion
     * @param ?array{auditoria_id: int, accion_original: 'INSERT'|'UPDATE'|'DELETE'|'UNDO'} $datos_anteriores
     * @param ?array{deshecho: true} $datos_nuevos
     * @throws PDOException
     */
    private function registrar(
        int $usuario_id,
        int $sucursal_id,
        string $tabla,
        string $accion,
        int $registro_id,
        ?array $datos_anteriores = null,
        ?array $datos_nuevos = null,
    ): int {
        $sql = "
            INSERT INTO $this->table
            (
                id_usuario,
                id_sucursal,
                tabla,
                accion,
                registro_id,
                datos_anteriores,
                datos_nuevos,
                ip_address,
                user_agent
            )
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
        ";

        $ip = $_SERVER['REMOTE_ADDR'] ?? null;
        $user_agent = $_SERVER['HTTP_USER_AGENT'] ?? null;

        return $this->db->execute($sql, [
            $usuario_id,
            $sucursal_id,
            $tabla,
            $accion,
            $registro_id,
            $datos_anteriores ? json_encode($datos_anteriores) : null,
            $datos_nuevos ? json_encode($datos_nuevos) : null,
            $ip,
            $user_agent,
        ]);
    }

    /**
     * Obtener auditorías con detalles de usuario y sucursal.
     * Optimización Bolt: Soporta filtrado por acción y estado (deshacer) en SQL y reduce el límite por defecto.
     * @param null|'INSERT'|'UPDATE'|'DELETE'|'UNDO' $accion
     * @param null|'activo'|'deshecho' $estado
     * @return list<array{
     *   id: int,
     *   id_usuario: int,
     *   id_sucursal: ?int,
     *   tabla: string,
     *   accion: 'INSERT'|'UPDATE'|'DELETE'|'UNDO',
     *   registro_id: int,
     *   datos_anteriores: ?string,
     *   datos_nuevos: ?string,
     *   ip_address: ?string,
     *   user_agent: ?string,
     *   fecha_accion: string,
     *   deshacer: null|0|1,
     *   fecha_deshacer: ?string,
     *   usuario_deshacer: ?int,
     *   usuario_nombre: string,
     *   sucursal_nombre: string,
     * }>
     * @throws PDOException
     */
    public function getWithDetails(
        ?int $sucursal_id = null,
        ?string $tabla = null,
        ?int $usuario_id = null,
        ?string $accion = null,
        ?string $estado = null,
        int $limit = 100,
    ): array {
        $sql = "
            SELECT a.*,
            CONCAT_WS(
                ' ',
                u.primer_nombre,
                u.segundo_nombre,
                u.apellido_paterno,
                u.apellido_materno
            ) usuario_nombre,
            s.nombre sucursal_nombre
            FROM $this->table a
            LEFT JOIN usuarios u ON a.id_usuario = u.id
            LEFT JOIN sucursales s ON a.id_sucursal = s.id
            WHERE 1 = 1
        ";

        $params = [];

        if ($sucursal_id) {
            $sql .= ' AND a.id_sucursal = ?';
            $params[] = $sucursal_id;
        }

        if ($tabla) {
            $sql .= ' AND a.tabla = ?';
            $params[] = $tabla;
        }

        if ($usuario_id) {
            $sql .= ' AND a.id_usuario = ?';
            $params[] = $usuario_id;
        }

        if ($accion) {
            $sql .= ' AND a.accion = ?';
            $params[] = $accion;
        }

        if ($estado) {
            if ($estado === 'deshecho') {
                $sql .= ' AND a.deshacer = 1';
            } elseif ($estado === 'activo') {
                $sql .= ' AND a.deshacer = 0';
            }
        }

        $sql .= ' ORDER BY a.fecha_accion DESC LIMIT ?';
        $params[] = $limit;

        return $this->db->fetchAll($sql, $params);
    }

    /** @return array{success: bool, message: ?string} */
    public function deshacer(int $auditoria_id, int $usuario_id): array
    {
        try {
            $this->db->beginTransaction();

            // Obtener registro de auditoría
            $auditoria = $this->find($auditoria_id);

            if (!$auditoria) {
                throw new OutOfBoundsException('Registro de auditoría no encontrado');
            }

            if ($auditoria['deshacer'] === 1) {
                throw new Exception('Esta acción ya fue deshecha');
            }

            // Verificar que la acción sea reversible
            if (!in_array($auditoria['accion'], ['INSERT', 'UPDATE', 'DELETE'])) {
                throw new Exception('Esta acción no es reversible');
            }

            // Restaurar datos anteriores según el tipo de acción
            if ($auditoria['accion'] === 'DELETE') {
                // Reinsertar el registro eliminado
                $datos = json_decode($auditoria['datos_anteriores'], true);

                if (!$datos) {
                    throw new Exception('No hay datos anteriores para restaurar');
                }

                $this->restaurarRegistro($auditoria['tabla'], $datos);
                $mensaje = 'Registro restaurado correctamente';
            } elseif ($auditoria['accion'] === 'UPDATE') {
                // Actualizar con datos anteriores
                $datos = json_decode($auditoria['datos_anteriores'], true);

                if (!$datos) {
                    throw new Exception('No hay datos anteriores para restaurar');
                }

                $this->restaurarRegistro($auditoria['tabla'], $datos, $auditoria['registro_id']);
                $mensaje = 'Cambios revertidos correctamente';
            } elseif ($auditoria['accion'] === 'INSERT') {
                // Eliminar el registro insertado
                $this->eliminarRegistro($auditoria['tabla'], $auditoria['registro_id']);
                $mensaje = 'Registro eliminado correctamente';
            }

            // Marcar como deshecho
            $sql = "
                UPDATE $this->table}
                SET deshacer = 1, fecha_deshacer = NOW(), usuario_deshacer = ?
                WHERE id = ?
            ";

            $this->db->execute($sql, [$usuario_id, $auditoria_id]);

            // Registrar la acción de deshacer en auditoría
            $this->registrar(
                $usuario_id,
                $auditoria['id_sucursal'],
                'auditoria',
                'UNDO',
                $auditoria_id,
                ['auditoria_id' => $auditoria_id, 'accion_original' => $auditoria['accion']],
                ['deshecho' => true]
            );

            $this->db->commit();

            return ['success' => true, 'message' => $mensaje ?? null];
        } catch (Exception $exception) {
            $this->db->rollback();

            return ['success' => false, 'message' => $exception->getMessage()];
        }
    }

    /** @throws PDOException */
    public function puedeDeshacer(int $auditoria_id): bool
    {
        $auditoria = $this->find($auditoria_id);

        if (!$auditoria) {
            return false;
        }

        // No se puede deshacer si ya fue deshecho
        if ($auditoria['deshacer'] === 1) {
            return false;
        }

        // Solo se pueden deshacer INSERT, UPDATE y DELETE
        if (!in_array($auditoria['accion'], ['INSERT', 'UPDATE', 'DELETE'])) {
            return false;
        }

        // Verificar que no haya pasado más de 24 horas (opcional)
        $fecha_accion = strtotime($auditoria['fecha_accion']);
        $ahora = time();
        $horas_transcurridas = ($ahora - $fecha_accion) / 3600;

        return $horas_transcurridas <= 24;
    }

    /**
     * @param array<string, null|scalar> $datos
     * @throws PDOException
     */
    private function restaurarRegistro(string $tabla, array $datos, ?int $id = null): int
    {
        if ($id) {
            // UPDATE
            $sets = [];
            $params = [];

            foreach ($datos as $key => $value) {
                if ($key !== 'id') {
                    $sets[] = "$key = ?";
                    $params[] = $value;
                }
            }

            $params[] = $id;
            $sql = "UPDATE $tabla SET " . implode(', ', $sets) . ' WHERE id = ?';
        } else {
            // INSERT
            $columns = array_keys($datos);
            $placeholders = array_fill(0, count($columns), '?');

            $sql = "
                INSERT INTO $tabla (" . implode(', ', $columns) . ')
                VALUES (' . implode(', ', $placeholders) . ')
            ';

            $params = array_values($datos);
        }

        return $this->db->execute($sql, $params);
    }

    /** @throws PDOException */
    private function eliminarRegistro(string $tabla, int $id): int
    {
        $sql = "DELETE FROM $tabla WHERE id = ?";

        return $this->db->execute($sql, [$id]);
    }

    /**
     * @return list<array{
     *   tabla: string,
     *   accion: 'INSERT'|'UPDATE'|'DELETE'|'UNDO',
     *   total: int,
     *   fecha: string,
     * }>
     * @throws PDOException
     */
    public function getEstadisticas(?string $sucursal_id = null): array
    {
        if ($sucursal_id) {
            $sql = "
                SELECT tabla, accion, COUNT(*) total, DATE(fecha_accion) fecha
                FROM $this->table
                WHERE id_sucursal = ?
                GROUP BY tabla, accion, DATE(fecha_accion)
                ORDER BY fecha DESC, total DESC
                LIMIT 100
            ";

            return $this->db->fetchAll($sql, [$sucursal_id]);
        }

        $sql = "
            SELECT tabla, accion, COUNT(*) as total, DATE(fecha_accion) as fecha
            FROM $this->table
            GROUP BY tabla, accion, DATE(fecha_accion)
            ORDER BY fecha DESC, total DESC
            LIMIT 100
        ";

        return $this->db->fetchAll($sql);
    }
}
