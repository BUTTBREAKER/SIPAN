-- ============================================
-- Migración: Estados adicionales para SIPAN Delivery
-- ============================================

-- 1. Agregar 'en_camino' y 'no_entregado' al ENUM de estado_pedido
ALTER TABLE pedidos 
  MODIFY estado_pedido ENUM('pendiente', 'en_proceso', 'en_camino', 'completado', 'entregado', 'no_entregado', 'cancelado') 
  DEFAULT 'pendiente';

-- 2. Cambiar fecha_entrega de DATE a DATETIME para registrar hora exacta
ALTER TABLE pedidos MODIFY fecha_entrega DATETIME DEFAULT NULL;
