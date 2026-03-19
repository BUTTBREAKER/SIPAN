-- ============================================
-- Migración para la app SIPAN Delivery (Repartidores)
-- ============================================

-- 1. Agregar el rol 'repartidor' al ENUM de la tabla usuarios
ALTER TABLE usuarios MODIFY rol ENUM('administrador', 'empleado', 'cajero', 'repartidor') DEFAULT 'empleado';

-- 2. Agregar el campo id_repartidor a la tabla pedidos
ALTER TABLE pedidos ADD COLUMN id_repartidor INT NULL AFTER id_usuario;

-- 3. Crear relación (llave foránea) entre pedidos.id_repartidor y usuarios.id
ALTER TABLE pedidos ADD CONSTRAINT fk_pedido_repartidor FOREIGN KEY (id_repartidor) REFERENCES usuarios(id) ON DELETE SET NULL;

-- 4. Crear índice para optimizar búsquedas por repartidor
CREATE INDEX idx_repartidor ON pedidos(id_repartidor);
