-- Migración: Implementación de Caja Chica (Cierre de Caja) con Soporte Multi-moneda
-- Fecha: 2026-01-13

CREATE TABLE IF NOT EXISTS cajas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_sucursal INT NOT NULL,
    id_usuario_apertura INT NOT NULL,
    id_usuario_cierre INT DEFAULT NULL,
    monto_apertura DECIMAL(10, 2) NOT NULL DEFAULT 0.00,
    monto_apertura_usd DECIMAL(10, 2) NOT NULL DEFAULT 0.00,
    monto_apertura_bs DECIMAL(15, 2) NOT NULL DEFAULT 0.00,
    monto_cierre DECIMAL(10, 2) DEFAULT NULL,
    monto_cierre_usd DECIMAL(10, 2) DEFAULT NULL,
    monto_cierre_bs DECIMAL(15, 2) DEFAULT NULL,
    monto_esperado DECIMAL(10, 2) DEFAULT NULL,
    monto_esperado_usd DECIMAL(10, 2) DEFAULT NULL,
    monto_esperado_bs DECIMAL(15, 2) DEFAULT NULL,
    estado ENUM('abierta', 'cerrada') DEFAULT 'abierta',
    fecha_apertura DATETIME DEFAULT CURRENT_TIMESTAMP,
    fecha_cierre DATETIME DEFAULT NULL,
    observaciones TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (id_sucursal) REFERENCES sucursales(id),
    FOREIGN KEY (id_usuario_apertura) REFERENCES usuarios(id),
    FOREIGN KEY (id_usuario_cierre) REFERENCES usuarios(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS caja_movimientos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_caja INT NOT NULL,
    tipo ENUM('ingreso', 'egreso') NOT NULL,
    monto DECIMAL(10, 2) NOT NULL,
    descripcion VARCHAR(255) NOT NULL,
    metodo_pago VARCHAR(50) DEFAULT 'efectivo',
    id_venta INT DEFAULT NULL,
    fecha DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_caja) REFERENCES cajas(id) ON DELETE CASCADE,
    FOREIGN KEY (id_venta) REFERENCES ventas(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
