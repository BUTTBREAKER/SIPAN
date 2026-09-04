-- =============================================
-- SIPAN - Chat Interno entre Sucursales
-- Migración: Crear tablas de mensajería
-- =============================================

-- Conversaciones (pueden ser directas o grupales)
CREATE TABLE IF NOT EXISTS chat_conversaciones (
    id INT AUTO_INCREMENT PRIMARY KEY,
    tipo ENUM('directa', 'grupo') NOT NULL DEFAULT 'directa',
    nombre VARCHAR(100) NULL,
    id_sucursal INT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_sucursal (id_sucursal),
    INDEX idx_updated (updated_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Participantes de cada conversación
CREATE TABLE IF NOT EXISTS chat_participantes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_conversacion INT NOT NULL,
    id_usuario INT NOT NULL,
    ultimo_leido TIMESTAMP NULL,
    joined_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_conversacion) REFERENCES chat_conversaciones(id) ON DELETE CASCADE,
    FOREIGN KEY (id_usuario) REFERENCES usuarios(id) ON DELETE CASCADE,
    UNIQUE KEY uq_conv_user (id_conversacion, id_usuario),
    INDEX idx_usuario (id_usuario)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Mensajes
CREATE TABLE IF NOT EXISTS chat_mensajes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_conversacion INT NOT NULL,
    id_usuario INT NOT NULL,
    mensaje TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_conversacion) REFERENCES chat_conversaciones(id) ON DELETE CASCADE,
    FOREIGN KEY (id_usuario) REFERENCES usuarios(id) ON DELETE CASCADE,
    INDEX idx_conv_created (id_conversacion, created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
