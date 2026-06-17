<?php

namespace App\Models;

use App\Core\Database;

class ChatMensaje
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    /**
     * Obtener conversaciones del usuario con último mensaje y conteo no leídos
     */
    public function getConversaciones($userId)
    {
        // Bolt Optimization: Refactored correlated subqueries into derived table joins.
        // This reduces query complexity from O(N*M) to O(N+M), where N is conversations and M is messages.
        $sql = "SELECT 
                    c.id,
                    c.tipo,
                    c.nombre AS grupo_nombre,
                    c.id_sucursal,
                    -- Último mensaje
                    m.mensaje AS ultimo_mensaje,
                    m.created_at AS ultimo_mensaje_fecha,
                    m_user.primer_nombre AS ultimo_mensaje_autor,
                    -- Conteo no leídos
                    COALESCE(unread.count, 0) AS no_leidos,
                    -- Info del otro participante (para directas)
                    other_user.id AS otro_usuario_id,
                    CONCAT_WS(' ', other_user.primer_nombre, other_user.apellido_paterno) AS otro_usuario_nombre,
                    other_user.rol AS otro_usuario_rol,
                    -- Sucursal del otro usuario
                    s.nombre AS otro_usuario_sucursal
                FROM chat_participantes cp
                INNER JOIN chat_conversaciones c ON c.id = cp.id_conversacion
                -- Último mensaje (Join con MAX(id) para evitar correlated subquery O(N*M))
                LEFT JOIN (
                    SELECT m2.id_conversacion, MAX(m2.id) as last_id
                    FROM chat_mensajes m2
                    GROUP BY m2.id_conversacion
                ) last_msg ON last_msg.id_conversacion = c.id
                LEFT JOIN chat_mensajes m ON m.id = last_msg.last_id
                LEFT JOIN usuarios m_user ON m_user.id = m.id_usuario
                -- Conteo no leídos (Join con conteos agregados por conversación)
                LEFT JOIN (
                    SELECT cm2.id_conversacion, COUNT(*) as count
                    FROM chat_mensajes cm2
                    INNER JOIN chat_participantes cp2 ON cm2.id_conversacion = cp2.id_conversacion
                    WHERE cp2.id_usuario = ?
                      AND cm2.created_at > COALESCE(cp2.ultimo_leido, '1970-01-01')
                      AND cm2.id_usuario != ?
                    GROUP BY cm2.id_conversacion
                ) unread ON unread.id_conversacion = c.id
                -- Otro participante (para conversaciones directas)
                LEFT JOIN chat_participantes cp_other ON cp_other.id_conversacion = c.id
                    AND cp_other.id_usuario != ? AND c.tipo = 'directa'
                LEFT JOIN usuarios other_user ON other_user.id = cp_other.id_usuario
                LEFT JOIN sucursales s ON s.id = other_user.id_sucursal
                WHERE cp.id_usuario = ?
                ORDER BY COALESCE(m.created_at, c.created_at) DESC";

        return $this->db->fetchAll($sql, [$userId, $userId, $userId, $userId]);
    }

    /**
     * Obtener o crear conversación directa entre dos usuarios
     */
    public function getOrCreateDirecta($userId1, $userId2)
    {
        // Buscar si ya existe
        $sql = "SELECT cp1.id_conversacion
                FROM chat_participantes cp1
                INNER JOIN chat_participantes cp2 ON cp1.id_conversacion = cp2.id_conversacion
                INNER JOIN chat_conversaciones c ON c.id = cp1.id_conversacion
                WHERE cp1.id_usuario = ? AND cp2.id_usuario = ? AND c.tipo = 'directa'
                LIMIT 1";

        $existing = $this->db->fetchOne($sql, [$userId1, $userId2]);

        if ($existing) {
            return $existing['id_conversacion'];
        }

        // Crear nueva conversación directa
        $this->db->execute(
            "INSERT INTO chat_conversaciones (tipo) VALUES ('directa')"
        );
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
     */
    public function getMensajes($convId, $limit = 50, $beforeId = null)
    {
        $params = [$convId];
        $sql = "SELECT 
                    m.id, m.mensaje, m.created_at, m.id_usuario,
                    CONCAT_WS(' ', u.primer_nombre, u.apellido_paterno) AS autor_nombre,
                    u.rol AS autor_rol
                FROM chat_mensajes m
                INNER JOIN usuarios u ON u.id = m.id_usuario
                WHERE m.id_conversacion = ?";

        if ($beforeId) {
            $sql .= " AND m.id < ?";
            $params[] = $beforeId;
        }

        $sql .= " ORDER BY m.created_at DESC LIMIT " . (int)$limit;

        $mensajes = $this->db->fetchAll($sql, $params);
        return array_reverse($mensajes); // Devolver en orden cronológico
    }

    /**
     * Enviar un mensaje
     */
    public function enviar($convId, $userId, $mensaje)
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
     */
    public function marcarLeida($convId, $userId)
    {
        $this->db->execute(
            "UPDATE chat_participantes SET ultimo_leido = NOW() WHERE id_conversacion = ? AND id_usuario = ?",
            [$convId, $userId]
        );
    }

    /**
     * Verificar que el usuario es participante de la conversación
     */
    public function esParticipante($convId, $userId)
    {
        $result = $this->db->fetchOne(
            "SELECT id FROM chat_participantes WHERE id_conversacion = ? AND id_usuario = ?",
            [$convId, $userId]
        );
        return !empty($result);
    }

    /**
     * Contar total de mensajes no leídos del usuario (para sidebar badge)
     */
    public function contarNoLeidos($userId)
    {
        // Bolt Optimization: Replaced nested subqueries with a single INNER JOIN.
        // This improves efficiency by allowing the DB to aggregate unread messages across all
        // participating conversations in a single pass (O(N+M)).
        $sql = "SELECT COUNT(*) as total
                FROM chat_mensajes cm
                INNER JOIN chat_participantes cp ON cm.id_conversacion = cp.id_conversacion
                WHERE cp.id_usuario = ?
                  AND cm.created_at > COALESCE(cp.ultimo_leido, '1970-01-01')
                  AND cm.id_usuario != ?";

        $result = $this->db->fetchOne($sql, [$userId, $userId]);
        return (int)($result['total'] ?? 0);
    }

    /**
     * Obtener usuarios disponibles para chatear (agrupados por sucursal)
     */
    public function getUsuariosDisponibles($currentUserId)
    {
        $sql = "SELECT 
                    u.id,
                    CONCAT_WS(' ', u.primer_nombre, u.apellido_paterno) AS nombre,
                    u.rol,
                    u.id_sucursal,
                    s.nombre AS sucursal_nombre
                FROM usuarios u
                LEFT JOIN sucursales s ON s.id = u.id_sucursal
                WHERE u.id != ? AND u.estado = 'activo'
                ORDER BY s.nombre ASC, u.primer_nombre ASC";

        return $this->db->fetchAll($sql, [$currentUserId]);
    }

    /**
     * Obtener timestamp del último mensaje relevante para el usuario (para polling)
     */
    public function getUltimaActividad($userId)
    {
        $sql = "SELECT MAX(m.created_at) AS ultima
                FROM chat_mensajes m
                INNER JOIN chat_participantes cp ON cp.id_conversacion = m.id_conversacion
                WHERE cp.id_usuario = ?";

        $result = $this->db->fetchOne($sql, [$userId]);
        return $result['ultima'] ?? null;
    }

    /**
     * Obtener info de una conversación
     */
    public function getConversacion($convId)
    {
        return $this->db->fetchOne(
            "SELECT * FROM chat_conversaciones WHERE id = ?",
            [$convId]
        );
    }
}
