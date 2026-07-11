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
        // Bolt Optimization: Replaced correlated subqueries with JOINs to derived tables.
        // 1. Unread count is calculated via LEFT JOIN to an aggregated subquery.
        // 2. Latest message is identified via MAX(id) in a JOIN, avoiding ORDER BY/LIMIT per row.
        // This reduces complexity from O(N*M) to O(N+M).
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
                    COALESCE(unr.no_leidos, 0) AS no_leidos,
                    -- Info del otro participante (para directas)
                    other_user.id AS otro_usuario_id,
                    CONCAT_WS(' ', other_user.primer_nombre, other_user.apellido_paterno) AS otro_usuario_nombre,
                    other_user.rol AS otro_usuario_rol,
                    -- Sucursal del otro usuario
                    s.nombre AS otro_usuario_sucursal
                FROM chat_participantes cp
                INNER JOIN chat_conversaciones c ON c.id = cp.id_conversacion
                -- Último mensaje (Optimizado: join con MAX id filtrado por las conversaciones del usuario)
                LEFT JOIN (
                    SELECT cm_inner.id_conversacion, MAX(cm_inner.id) as last_msg_id
                    FROM chat_mensajes cm_inner
                    INNER JOIN chat_participantes cp4 ON cm_inner.id_conversacion = cp4.id_conversacion
                    WHERE cp4.id_usuario = ?
                    GROUP BY cm_inner.id_conversacion
                ) m_last ON m_last.id_conversacion = c.id
                LEFT JOIN chat_mensajes m ON m.id = m_last.last_msg_id
                LEFT JOIN usuarios m_user ON m_user.id = m.id_usuario
                -- Conteo no leídos (Optimizado: join con agregación filtrada por usuario)
                LEFT JOIN (
                    SELECT cm2.id_conversacion, COUNT(*) as no_leidos
                    FROM chat_mensajes cm2
                    INNER JOIN chat_participantes cp3 ON cm2.id_conversacion = cp3.id_conversacion
                    WHERE cp3.id_usuario = ?
                      AND cm2.created_at > COALESCE(cp3.ultimo_leido, '1970-01-01')
                      AND cm2.id_usuario != ?
                    GROUP BY cm2.id_conversacion
                ) unr ON unr.id_conversacion = c.id
                -- Otro participante (para conversaciones directas)
                LEFT JOIN chat_participantes cp2 ON cp2.id_conversacion = c.id 
                    AND cp2.id_usuario != ? AND c.tipo = 'directa'
                LEFT JOIN usuarios other_user ON other_user.id = cp2.id_usuario
                LEFT JOIN sucursales s ON s.id = other_user.id_sucursal
                WHERE cp.id_usuario = ?
                ORDER BY COALESCE(m.created_at, c.created_at) DESC";

        return $this->db->fetchAll($sql, [$userId, $userId, $userId, $userId, $userId]);
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
        // Bolt Optimization: Replaced nested scalar subquery with a single JOIN between chat_mensajes and chat_participantes.
        // This calculates the total in a single pass (O(N+M)) instead of O(N*M).
        $sql = "SELECT COUNT(*) AS total
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
