<?php

declare(strict_types=1);

namespace App\Models;

use PDOException;

final class ChatMensaje extends BaseModel
{
    /**
     * Obtener conversaciones del usuario con último mensaje y conteo no leídos
     * @return list<array{
     *   id: int,
     *   tipo: 'directa'|'grupo',
     *   grupo_nombre: ?string,
     *   id_sucursal: ?int,
     *   ultimo_mensaje: string,
     *   ultimo_mensaje_fecha: string,
     *   ultimo_mensaje_autor: string,
     *   no_leidos: int,
     *   otro_usuario_id: int,
     *   otro_usuario_nombre: string,
     *   otro_usuario_rol: 'administrador'|'empleado'|'cajero'|'repartidor',
     *   otro_usuario_sucursal: string,
     * }>
     * @throws PDOException
     */
    public function getConversaciones(int $userId): array
    {
        // Optimización Bolt: Reemplazo de subconsultas correlacionadas por JOINs a tablas derivadas (O(N+M))
        $sql = "
            SELECT
                c.id,
                c.tipo,
                c.nombre grupo_nombre,
                c.id_sucursal,
                -- Último mensaje
                m.mensaje ultimo_mensaje,
                m.created_at ultimo_mensaje_fecha,
                m_user.primer_nombre ultimo_mensaje_autor,
                -- Conteo no leídos
                COALESCE(unread.unread_count, 0) no_leidos,
                -- Info del otro participante (para directas)
                other_user.id otro_usuario_id,
                CONCAT_WS(' ', other_user.primer_nombre, other_user.apellido_paterno) otro_usuario_nombre,
                other_user.rol otro_usuario_rol,
                -- Sucursal del otro usuario
                s.nombre otro_usuario_sucursal
            FROM chat_participantes cp
            INNER JOIN chat_conversaciones c ON c.id = cp.id_conversacion
            -- Último mensaje (derived table filtrada por conversaciones del usuario)
            LEFT JOIN (
                SELECT cm_max.id_conversacion, MAX(cm_max.id) max_id
                FROM chat_mensajes cm_max
                INNER JOIN chat_participantes cp_max ON cp_max.id_conversacion = cm_max.id_conversacion AND cp_max.id_usuario = ?
                GROUP BY cm_max.id_conversacion
            ) m_last ON m_last.id_conversacion = c.id
            LEFT JOIN chat_mensajes m ON m.id = m_last.max_id
            LEFT JOIN usuarios m_user ON m_user.id = m.id_usuario
            -- Conteo no leídos (derived table filtrada por conversaciones del usuario)
            LEFT JOIN (
                SELECT cm2.id_conversacion, COUNT(*) unread_count
                FROM chat_mensajes cm2
                INNER JOIN chat_participantes cp_unread ON cp_unread.id_conversacion = cm2.id_conversacion AND cp_unread.id_usuario = ?
                WHERE cm2.created_at > COALESCE(cp_unread.ultimo_leido, '1970-01-01')
                  AND cm2.id_usuario != ?
                GROUP BY cm2.id_conversacion
            ) unread ON unread.id_conversacion = c.id
            -- Otro participante (para conversaciones directas)
            LEFT JOIN chat_participantes cp2 ON cp2.id_conversacion = c.id
                AND cp2.id_usuario != ? AND c.tipo = 'directa'
            LEFT JOIN usuarios other_user ON other_user.id = cp2.id_usuario
            LEFT JOIN sucursales s ON s.id = other_user.id_sucursal
            WHERE cp.id_usuario = ?
            ORDER BY COALESCE(m.created_at, c.created_at) DESC
        ";

        return $this->db->fetchAll($sql, [$userId, $userId, $userId, $userId, $userId]);
    }

    /**
     * Obtener o crear conversación directa entre dos usuarios
     * @throws PDOException
     */
    public function getOrCreateDirecta(int $userId1, int $userId2): int|string|false
    {
        // Buscar si ya existe
        $sql = "
            SELECT cp1.id_conversacion
            FROM chat_participantes cp1
            INNER JOIN chat_participantes cp2 ON cp1.id_conversacion = cp2.id_conversacion
            INNER JOIN chat_conversaciones c ON c.id = cp1.id_conversacion
            WHERE cp1.id_usuario = ? AND cp2.id_usuario = ? AND c.tipo = 'directa'
            LIMIT 1
        ";

        $existing = $this->db->fetchOne($sql, [$userId1, $userId2]);

        if ($existing) {
            return $existing['id_conversacion'];
        }

        // Crear nueva conversación directa
        $this->db->execute("INSERT INTO chat_conversaciones (tipo) VALUES ('directa')");
        $convId = $this->db->lastInsertId();

        // Agregar ambos participantes
        $this->db->execute(
            "INSERT INTO chat_participantes (id_conversacion, id_usuario) VALUES (?, ?)",
            [$convId, $userId1]
        );

        $this->db->execute(
            "INSERT INTO chat_participantes (id_conversacion, id_usuario) VALUES (?, ?)",
            [$convId, $userId2]
        );

        return $convId;
    }

    /**
     * Obtener mensajes paginados de una conversación
     * @return list<array{
     *   id: int,
     *   mensaje: string,
     *   created_at: string,
     *   id_usuario: int,
     *   autor_nombre: string,
     *   autor_rol: 'administrador'|'empleado'|'cajero'|'repartidor',
     * }>
     * @throws PDOException
     */
    public function getMensajes(int $convId, int $limit = 50, ?int $beforeId = null): array
    {
        $params = [$convId];

        $sql = "
            SELECT
                m.id, m.mensaje, m.created_at, m.id_usuario,
                CONCAT_WS(' ', u.primer_nombre, u.apellido_paterno) AS autor_nombre,
                u.rol AS autor_rol
            FROM chat_mensajes m
            INNER JOIN usuarios u ON u.id = m.id_usuario
            WHERE m.id_conversacion = ?
        ";

        if ($beforeId) {
            $sql .= ' AND m.id < ?';
            $params[] = $beforeId;
        }

        $sql .= ' ORDER BY m.created_at DESC LIMIT ?';
        $params[] = $limit;
        $mensajes = $this->db->fetchAll($sql, $params);

        return array_reverse($mensajes); // Devolver en orden cronológico
    }

    /**
     * Enviar un mensaje
     * @throws PDOException
     */
    public function enviar(int $convId, int $userId, string $mensaje): string|false
    {
        $this->db->execute(
            "INSERT INTO chat_mensajes (id_conversacion, id_usuario, mensaje) VALUES (?, ?, ?)",
            [$convId, $userId, $mensaje]
        );

        // Actualizar timestamp de la conversación
        $this->db->execute(
            "UPDATE chat_conversaciones SET updated_at = NOW() WHERE id = ?",
            [$convId]
        );

        return $this->db->lastInsertId();
    }

    /**
     * Marcar conversación como leída por el usuario
     * @throws PDOException
     */
    public function marcarLeida(int $convId, int $userId): void
    {
        $this->db->execute(
            "UPDATE chat_participantes SET ultimo_leido = NOW() WHERE id_conversacion = ? AND id_usuario = ?",
            [$convId, $userId]
        );
    }

    /**
     * Verificar que el usuario es participante de la conversación
     * @throws PDOException
     */
    public function esParticipante(int $convId, int $userId): bool
    {
        $result = $this->db->fetchOne(
            "SELECT id FROM chat_participantes WHERE id_conversacion = ? AND id_usuario = ?",
            [$convId, $userId]
        );

        return !empty($result);
    }

    /**
     * Contar total de mensajes no leídos del usuario (para sidebar badge)
     * @throws PDOException
     */
    public function contarNoLeidos(int $userId): int
    {
        // Optimización Bolt: Reemplazo de subconsulta por JOIN directo (O(N+M))
        $sql = "
            SELECT COALESCE(COUNT(cm.id), 0) total
            FROM chat_participantes cp
            INNER JOIN chat_mensajes cm ON cm.id_conversacion = cp.id_conversacion
                AND cm.created_at > COALESCE(cp.ultimo_leido, '1970-01-01')
                AND cm.id_usuario != ?
            WHERE cp.id_usuario = ?
        ";

        $result = $this->db->fetchOne($sql, [$userId, $userId]);
        $total = $result['total'] ?? 0;

        return intval($total);
    }

    /**
     * Obtener usuarios disponibles para chatear (agrupados por sucursal)
     * @return list<array{
     *   id: int,
     *   nombre: string,
     *   rol: 'administrador'|'empleado'|'cajero'|'repartidor',
     *   id_sucursal: ?int,
     *   sucursal_nombre: string,
     * }>
     * @throws PDOException
     */
    public function getUsuariosDisponibles(int $currentUserId): array
    {
        $sql = "
            SELECT
                u.id,
                CONCAT_WS(' ', u.primer_nombre, u.apellido_paterno) nombre,
                u.rol,
                u.id_sucursal,
                s.nombre sucursal_nombre
            FROM usuarios u
            LEFT JOIN sucursales s ON s.id = u.id_sucursal
            WHERE u.id != ? AND u.estado = 'activo'
            ORDER BY s.nombre ASC, u.primer_nombre ASC
        ";

        return $this->db->fetchAll($sql, [$currentUserId]);
    }

    /**
     * Obtener timestamp del último mensaje relevante para el usuario (para polling)
     * @throws PDOException
     */
    public function getUltimaActividad(int $userId): ?string
    {
        $sql = "
            SELECT MAX(m.created_at) ultima
            FROM chat_mensajes m
            INNER JOIN chat_participantes cp ON cp.id_conversacion = m.id_conversacion
            WHERE cp.id_usuario = ?
        ";

        $result = $this->db->fetchOne($sql, [$userId]);

        return $result['ultima'] ?? null;
    }
}