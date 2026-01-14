-- ============================================
-- SIPAN - Estructura completa Consolidad (v2.8)
-- ============================================

-- Crear base de datos si no existe
CREATE DATABASE IF NOT EXISTS sipan CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE sipan;

-- ============================================
-- TABLA: sucursales
-- ============================================
CREATE TABLE IF NOT EXISTS sucursales (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    direccion TEXT,
    telefono VARCHAR(20),
    correo VARCHAR(100),
    estado ENUM('activa', 'inactiva') DEFAULT 'activa',
    fecha_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_estado (estado)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- TABLA: usuarios
-- ============================================
CREATE TABLE IF NOT EXISTS usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_sucursal INT DEFAULT NULL,
    primer_nombre VARCHAR(50) NOT NULL,
    segundo_nombre VARCHAR(50) DEFAULT NULL,
    apellido_paterno VARCHAR(50) NOT NULL,
    apellido_materno VARCHAR(50) DEFAULT NULL,
    correo VARCHAR(100) UNIQUE NOT NULL,
    clave VARCHAR(255) NOT NULL,
    rol ENUM('administrador', 'empleado', 'cajero') DEFAULT 'empleado',
    estado ENUM('activo', 'inactivo') DEFAULT 'activo',
    fecha_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_sucursal) REFERENCES sucursales(id) ON DELETE SET NULL,
    INDEX idx_correo (correo),
    INDEX idx_rol (rol),
    INDEX idx_sucursal (id_sucursal)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- TABLA: proveedores
-- ============================================
CREATE TABLE IF NOT EXISTS proveedores (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_sucursal INT NOT NULL,
    nombre VARCHAR(100) NOT NULL,
    ruc VARCHAR(20),
    telefono VARCHAR(20),
    direccion TEXT,
    correo VARCHAR(100),
    contacto_nombre VARCHAR(100),
    fecha_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_sucursal) REFERENCES sucursales(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- TABLA: negocios
-- ============================================
CREATE TABLE IF NOT EXISTS negocios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_sucursal INT NOT NULL,
    nombre VARCHAR(100) NOT NULL,
    direccion TEXT,
    telefono VARCHAR(20),
    correo VARCHAR(100),
    logo VARCHAR(255),
    fecha_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_sucursal) REFERENCES sucursales(id) ON DELETE CASCADE,
    INDEX idx_sucursal (id_sucursal)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- TABLA: productos
-- ============================================
CREATE TABLE IF NOT EXISTS productos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_negocio INT NOT NULL,
    id_sucursal INT NOT NULL,
    id_usuario INT NOT NULL,
    nombre VARCHAR(100) NOT NULL,
    categoria VARCHAR(50) DEFAULT 'Otro',
    descripcion TEXT,
    stock_actual INT DEFAULT 0,
    stock_minimo INT DEFAULT 0,
    precio_actual DECIMAL(10, 2) DEFAULT 0.00,
    imagen VARCHAR(255),
    fecha_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_negocio) REFERENCES negocios(id) ON DELETE CASCADE,
    FOREIGN KEY (id_sucursal) REFERENCES sucursales(id) ON DELETE CASCADE,
    FOREIGN KEY (id_usuario) REFERENCES usuarios(id) ON DELETE CASCADE,
    INDEX idx_negocio (id_negocio),
    INDEX idx_sucursal (id_sucursal),
    INDEX idx_categoria (categoria),
    INDEX idx_stock (stock_actual)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- TABLA: insumos
-- ============================================
CREATE TABLE IF NOT EXISTS insumos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_negocio INT NOT NULL,
    id_sucursal INT NOT NULL,
    id_usuario INT NOT NULL,
    id_proveedor INT DEFAULT NULL,
    codigo VARCHAR(50) NULL UNIQUE,
    nombre VARCHAR(100) NOT NULL,
    descripcion TEXT,
    unidad_medida VARCHAR(20) NOT NULL DEFAULT 'kg',
    stock_actual DECIMAL(10, 2) NOT NULL DEFAULT 0.00,
    stock_minimo DECIMAL(10, 2) NOT NULL DEFAULT 0.00,
    precio_unitario DECIMAL(10, 2) NOT NULL DEFAULT 0.00,
    costo_unitario DECIMAL(10, 2) DEFAULT 0.00,
    fecha_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_negocio) REFERENCES negocios(id) ON DELETE CASCADE,
    FOREIGN KEY (id_sucursal) REFERENCES sucursales(id) ON DELETE CASCADE,
    FOREIGN KEY (id_usuario) REFERENCES usuarios(id) ON DELETE CASCADE,
    FOREIGN KEY (id_proveedor) REFERENCES proveedores(id) ON DELETE SET NULL,
    INDEX idx_negocio (id_negocio),
    INDEX idx_sucursal (id_sucursal),
    INDEX idx_stock (stock_actual)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- TABLA: recetas
-- ============================================
CREATE TABLE IF NOT EXISTS recetas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_producto INT NOT NULL,
    id_sucursal INT NOT NULL,
    nombre VARCHAR(100) NOT NULL,
    descripcion TEXT,
    rendimiento INT DEFAULT 1 COMMENT 'Cantidad de productos que genera la receta',
    tiempo_preparacion INT DEFAULT 0 COMMENT 'Tiempo en minutos',
    instrucciones TEXT,
    fecha_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_producto) REFERENCES productos(id) ON DELETE CASCADE,
    FOREIGN KEY (id_sucursal) REFERENCES sucursales(id) ON DELETE CASCADE,
    INDEX idx_producto (id_producto),
    INDEX idx_sucursal (id_sucursal)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- TABLA: receta_insumos (relación muchos-a-muchos)
-- ============================================
CREATE TABLE IF NOT EXISTS receta_insumos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_receta INT NOT NULL,
    id_insumo INT NOT NULL,
    cantidad DECIMAL(10, 2) NOT NULL,
    unidad_medida VARCHAR(20) DEFAULT 'kg',
    FOREIGN KEY (id_receta) REFERENCES recetas(id) ON DELETE CASCADE,
    FOREIGN KEY (id_insumo) REFERENCES insumos(id) ON DELETE CASCADE,
    INDEX idx_receta (id_receta),
    INDEX idx_insumo (id_insumo)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- TABLA: ventas
-- ============================================
CREATE TABLE IF NOT EXISTS ventas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_negocio INT NOT NULL,
    id_sucursal INT NOT NULL,
    id_usuario INT NOT NULL,
    total DECIMAL(10, 2) DEFAULT 0.00,
    metodo_pago VARCHAR(50) DEFAULT 'efectivo_usd',
    estado ENUM('pendiente', 'completada', 'cancelada') DEFAULT 'completada',
    fecha_venta TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_negocio) REFERENCES negocios(id) ON DELETE CASCADE,
    FOREIGN KEY (id_sucursal) REFERENCES sucursales(id) ON DELETE CASCADE,
    FOREIGN KEY (id_usuario) REFERENCES usuarios(id) ON DELETE CASCADE,
    INDEX idx_negocio (id_negocio),
    INDEX idx_sucursal (id_sucursal),
    INDEX idx_fecha (fecha_venta)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- TABLA: venta_productos
-- ============================================
CREATE TABLE IF NOT EXISTS venta_productos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_venta INT NOT NULL,
    id_producto INT NOT NULL,
    cantidad INT NOT NULL,
    precio_unitario DECIMAL(10, 2) NOT NULL,
    subtotal DECIMAL(10, 2) NOT NULL,
    FOREIGN KEY (id_venta) REFERENCES ventas(id) ON DELETE CASCADE,
    FOREIGN KEY (id_producto) REFERENCES productos(id) ON DELETE CASCADE,
    INDEX idx_venta (id_venta),
    INDEX idx_producto (id_producto)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- TABLA: venta_pagos
-- ============================================
CREATE TABLE IF NOT EXISTS venta_pagos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_venta INT NOT NULL,
    metodo_pago VARCHAR(50) NOT NULL,
    monto DECIMAL(10,2) NOT NULL,
    referencia VARCHAR(100) NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_venta) REFERENCES ventas(id) ON DELETE CASCADE,
    INDEX idx_venta (id_venta)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- TABLA: producciones
-- ============================================
CREATE TABLE IF NOT EXISTS producciones (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_negocio INT NOT NULL,
    id_sucursal INT NOT NULL,
    id_usuario INT NOT NULL,
    id_producto INT NOT NULL,
    cantidad_producida INT NOT NULL,
    costo_total DECIMAL(10, 2) DEFAULT 0.00,
    fecha_produccion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_negocio) REFERENCES negocios(id) ON DELETE CASCADE,
    FOREIGN KEY (id_sucursal) REFERENCES sucursales(id) ON DELETE CASCADE,
    FOREIGN KEY (id_usuario) REFERENCES usuarios(id) ON DELETE CASCADE,
    FOREIGN KEY (id_producto) REFERENCES productos(id) ON DELETE CASCADE,
    INDEX idx_negocio (id_negocio),
    INDEX idx_sucursal (id_sucursal),
    INDEX idx_producto (id_producto),
    INDEX idx_fecha (fecha_produccion)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- TABLA: produccion_insumos
-- ============================================
CREATE TABLE IF NOT EXISTS produccion_insumos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_produccion INT NOT NULL,
    id_insumo INT NOT NULL,
    cantidad_utilizada DECIMAL(10, 2) NOT NULL,
    FOREIGN KEY (id_produccion) REFERENCES producciones(id) ON DELETE CASCADE,
    FOREIGN KEY (id_insumo) REFERENCES insumos(id) ON DELETE CASCADE,
    INDEX idx_produccion (id_produccion),
    INDEX idx_insumo (id_insumo)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- TABLA: compras
-- ============================================
CREATE TABLE IF NOT EXISTS compras (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_sucursal INT NOT NULL,
    id_usuario INT NOT NULL,
    id_proveedor INT NULL,
    fecha_compra DATETIME DEFAULT CURRENT_TIMESTAMP,
    numero_comprobante VARCHAR(50) NULL,
    total DECIMAL(10,2) NOT NULL DEFAULT 0,
    estado ENUM('pendiente', 'completada', 'anulada') DEFAULT 'completada',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_proveedor) REFERENCES proveedores(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- TABLA: compra_detalles
-- ============================================
CREATE TABLE IF NOT EXISTS compra_detalles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_compra INT NOT NULL,
    tipo_item ENUM('insumo', 'producto') NOT NULL,
    id_item INT NOT NULL,
    cantidad DECIMAL(10,2) NOT NULL,
    costo_unitario DECIMAL(10,2) NOT NULL,
    subtotal DECIMAL(10,2) NOT NULL,
    lote_codigo VARCHAR(50) NULL,
    fecha_vencimiento DATE NULL,
    FOREIGN KEY (id_compra) REFERENCES compras(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- TABLA: lotes
-- ============================================
CREATE TABLE IF NOT EXISTS lotes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_sucursal INT NOT NULL,
    tipo ENUM('insumo', 'producto') NOT NULL,
    id_item INT NOT NULL,
    codigo_lote VARCHAR(50) NOT NULL,
    fecha_entrada DATE NOT NULL,
    fecha_vencimiento DATE NULL,
    cantidad_inicial DECIMAL(10,2) NOT NULL,
    cantidad_actual DECIMAL(10,2) NOT NULL,
    costo_unitario DECIMAL(10,2) NOT NULL DEFAULT 0,
    estado ENUM('activo', 'agotado', 'vencido') DEFAULT 'activo',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_item (tipo, id_item),
    INDEX idx_sucursal (id_sucursal),
    INDEX idx_vencimiento (fecha_vencimiento)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- TABLA: notificaciones
-- ============================================
CREATE TABLE IF NOT EXISTS notificaciones (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_usuario INT DEFAULT NULL,
    id_sucursal INT DEFAULT NULL,
    tipo ENUM('stock_bajo_producto', 'stock_bajo_insumo', 'sugerencia_compra', 'venta_importante', 'sistema') DEFAULT 'sistema',
    titulo VARCHAR(200) NOT NULL,
    mensaje TEXT NOT NULL,
    referencia_tipo VARCHAR(50) COMMENT 'producto, insumo, venta, etc',
    referencia_id INT COMMENT 'ID del elemento referenciado',
    leida BOOLEAN DEFAULT FALSE,
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_usuario) REFERENCES usuarios(id) ON DELETE CASCADE,
    FOREIGN KEY (id_sucursal) REFERENCES sucursales(id) ON DELETE CASCADE,
    INDEX idx_usuario (id_usuario),
    INDEX idx_sucursal (id_sucursal),
    INDEX idx_leida (leida),
    INDEX idx_fecha (fecha_creacion)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- TABLA: sugerencias_compra
-- ============================================
CREATE TABLE IF NOT EXISTS sugerencias_compra (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_sucursal INT NOT NULL,
    id_insumo INT NOT NULL,
    cantidad_sugerida DECIMAL(10, 2) NOT NULL,
    cantidad_actual DECIMAL(10, 2) NOT NULL,
    razon TEXT COMMENT 'Explicación de por qué se sugiere la compra',
    prioridad ENUM('baja', 'media', 'alta') DEFAULT 'media',
    estado ENUM('pendiente', 'aprobada', 'rechazada', 'completada') DEFAULT 'pendiente',
    fecha_sugerencia TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    fecha_actualizacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (id_sucursal) REFERENCES sucursales(id) ON DELETE CASCADE,
    FOREIGN KEY (id_insumo) REFERENCES insumos(id) ON DELETE CASCADE,
    INDEX idx_sucursal (id_sucursal),
    INDEX idx_insumo (id_insumo),
    INDEX idx_estado (estado),
    INDEX idx_prioridad (prioridad)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- TABLA: clientes
-- ============================================
CREATE TABLE IF NOT EXISTS clientes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_sucursal INT DEFAULT NULL,
    nombre VARCHAR(100) NOT NULL,
    apellido VARCHAR(100) DEFAULT NULL,
    documento_tipo ENUM('DNI', 'RUC', 'CE', 'Pasaporte') DEFAULT 'DNI',
    documento_numero VARCHAR(20) DEFAULT NULL,
    telefono VARCHAR(20) DEFAULT NULL,
    correo VARCHAR(100) DEFAULT NULL,
    direccion TEXT DEFAULT NULL,
    estado ENUM('activo', 'inactivo') DEFAULT 'activo',
    fecha_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_sucursal) REFERENCES sucursales(id) ON DELETE SET NULL,
    INDEX idx_documento (documento_numero),
    INDEX idx_estado (estado),
    INDEX idx_sucursal (id_sucursal)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- TABLA: pedidos
-- ============================================
CREATE TABLE IF NOT EXISTS pedidos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_cliente INT NOT NULL,
    id_sucursal INT DEFAULT NULL,
    id_usuario INT DEFAULT NULL,
    numero_pedido VARCHAR(20) UNIQUE NOT NULL,
    fecha_pedido TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    fecha_entrega DATE DEFAULT NULL,
    estado_pedido ENUM('pendiente', 'en_proceso', 'completado', 'entregado', 'cancelado') DEFAULT 'pendiente',
    estado_pago ENUM('pendiente', 'abonado', 'pagado') DEFAULT 'pendiente',
    subtotal DECIMAL(10, 2) DEFAULT 0.00,
    descuento DECIMAL(10, 2) DEFAULT 0.00,
    total DECIMAL(10, 2) NOT NULL,
    monto_pagado DECIMAL(10, 2) DEFAULT 0.00,
    monto_deuda DECIMAL(10, 2) DEFAULT 0.00,
    observaciones TEXT DEFAULT NULL,
    FOREIGN KEY (id_cliente) REFERENCES clientes(id) ON DELETE CASCADE,
    FOREIGN KEY (id_sucursal) REFERENCES sucursales(id) ON DELETE SET NULL,
    FOREIGN KEY (id_usuario) REFERENCES usuarios(id) ON DELETE SET NULL,
    INDEX idx_cliente (id_cliente),
    INDEX idx_sucursal (id_sucursal),
    INDEX idx_estado_pedido (estado_pedido),
    INDEX idx_estado_pago (estado_pago),
    INDEX idx_fecha_pedido (fecha_pedido),
    INDEX idx_numero_pedido (numero_pedido)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- TABLA: pedido_productos
-- ============================================
CREATE TABLE IF NOT EXISTS pedido_productos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_pedido INT NOT NULL,
    id_producto INT NOT NULL,
    cantidad INT NOT NULL,
    precio_unitario DECIMAL(10, 2) NOT NULL,
    subtotal DECIMAL(10, 2) NOT NULL,
    FOREIGN KEY (id_pedido) REFERENCES pedidos(id) ON DELETE CASCADE,
    FOREIGN KEY (id_producto) REFERENCES productos(id) ON DELETE CASCADE,
    INDEX idx_pedido (id_pedido),
    INDEX idx_producto (id_producto)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- TABLA: pedido_pagos
-- ============================================
CREATE TABLE IF NOT EXISTS pedido_pagos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_pedido INT NOT NULL,
    id_usuario INT DEFAULT NULL,
    monto DECIMAL(10, 2) NOT NULL,
    metodo_pago ENUM('efectivo', 'tarjeta', 'transferencia', 'yape', 'plin', 'otro') DEFAULT 'efectivo',
    referencia VARCHAR(100) DEFAULT NULL,
    fecha_pago TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    observaciones TEXT DEFAULT NULL,
    FOREIGN KEY (id_pedido) REFERENCES pedidos(id) ON DELETE CASCADE,
    FOREIGN KEY (id_usuario) REFERENCES usuarios(id) ON DELETE SET NULL,
    INDEX idx_pedido (id_pedido),
    INDEX idx_fecha_pago (fecha_pago)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- TABLA: configuracion
-- ============================================
CREATE TABLE IF NOT EXISTS configuracion (
    id INT AUTO_INCREMENT PRIMARY KEY,
    clave VARCHAR(50) UNIQUE NOT NULL,
    valor TEXT,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- ============================================
-- TABLA: auditoria
-- ============================================
CREATE TABLE IF NOT EXISTS auditoria (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_usuario INT NOT NULL,
    id_sucursal INT DEFAULT NULL,
    tabla VARCHAR(50) NOT NULL,
    accion ENUM('INSERT', 'UPDATE', 'DELETE') NOT NULL,
    registro_id INT NOT NULL,
    datos_anteriores JSON DEFAULT NULL,
    datos_nuevos JSON DEFAULT NULL,
    ip_address VARCHAR(45) DEFAULT NULL,
    user_agent TEXT DEFAULT NULL,
    fecha_accion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    deshacer BOOLEAN DEFAULT FALSE,
    fecha_deshacer TIMESTAMP NULL DEFAULT NULL,
    usuario_deshacer INT DEFAULT NULL,
    FOREIGN KEY (id_usuario) REFERENCES usuarios(id) ON DELETE CASCADE,
    FOREIGN KEY (id_sucursal) REFERENCES sucursales(id) ON DELETE SET NULL,
    FOREIGN KEY (usuario_deshacer) REFERENCES usuarios(id) ON DELETE SET NULL,
    INDEX idx_usuario (id_usuario),
    INDEX idx_sucursal (id_sucursal),
    INDEX idx_tabla (tabla),
    INDEX idx_accion (accion),
    INDEX idx_fecha (fecha_accion),
    INDEX idx_registro (tabla, registro_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- TABLA: respaldos
-- ============================================
CREATE TABLE IF NOT EXISTS respaldos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_usuario INT NOT NULL,
    nombre_archivo VARCHAR(255) NOT NULL,
    ruta_archivo VARCHAR(500) NOT NULL,
    tamano_bytes BIGINT DEFAULT 0,
    tipo ENUM('manual', 'automatico') DEFAULT 'manual',
    descripcion TEXT DEFAULT NULL,
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_usuario) REFERENCES usuarios(id) ON DELETE CASCADE,
    INDEX idx_usuario (id_usuario),
    INDEX idx_tipo (tipo),
    INDEX idx_fecha (fecha_creacion)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- DATOS INICIALES
-- ============================================
INSERT INTO configuracion (clave, valor) VALUES ('tasa_bcv', '50.00'), ('moneda_principal', 'USD');
INSERT INTO proveedores (id, id_sucursal, nombre, ruc, telefono, direccion) VALUES (1, 1, 'Proveedor General', '20000000001', '999999999', 'Direccion General');

-- ============================================
-- VISTAS
-- ============================================
CREATE OR REPLACE VIEW v_productos_stock_bajo AS
SELECT p.id, p.nombre, p.categoria, p.stock_actual, p.stock_minimo, s.nombre AS sucursal, n.nombre AS negocio, ROUND((p.stock_actual / NULLIF(p.stock_minimo,0)) * 100, 2) AS porcentaje_stock
FROM productos p INNER JOIN sucursales s ON p.id_sucursal = s.id INNER JOIN negocios n ON p.id_negocio = n.id
WHERE p.stock_minimo > 0 AND p.stock_actual <= p.stock_minimo;

CREATE OR REPLACE VIEW v_insumos_stock_bajo AS
SELECT i.id, i.nombre, i.stock_actual, i.stock_minimo, i.unidad_medida, s.nombre AS sucursal, n.nombre AS negocio, ROUND((i.stock_actual / NULLIF(i.stock_minimo,0)) * 100, 2) AS porcentaje_stock
FROM insumos i INNER JOIN sucursales s ON i.id_sucursal = s.id INNER JOIN negocios n ON i.id_negocio = n.id
WHERE i.stock_minimo > 0 AND i.stock_actual <= i.stock_minimo;

-- ============================================
-- PROCEDIMIENTOS ALMACENADOS
-- ============================================
DELIMITER //

CREATE PROCEDURE sp_calcular_insumos_produccion(IN p_id_producto INT, IN p_cantidad INT)
BEGIN
    SELECT i.id AS insumo_id, i.nombre AS insumo_nombre, ri.cantidad * p_cantidad AS cantidad_necesaria, ri.unidad_medida, i.stock_actual,
    CASE WHEN i.stock_actual >= (ri.cantidad * p_cantidad) THEN 'Suficiente' ELSE 'Insuficiente' END AS disponibilidad
    FROM recetas r INNER JOIN receta_insumos ri ON r.id = ri.id_receta INNER JOIN insumos i ON ri.id_insumo = i.id
    WHERE r.id_producto = p_id_producto;
END //

CREATE PROCEDURE sp_generar_sugerencias_compra(IN p_id_sucursal INT)
BEGIN
    INSERT INTO sugerencias_compra (id_sucursal, id_insumo, cantidad_sugerida, cantidad_actual, razon, prioridad, estado)
    SELECT p_id_sucursal, i.id, COALESCE(i.stock_minimo, 0) * 2, COALESCE(i.stock_actual, 0),
    CONCAT('Stock actual (', COALESCE(i.stock_actual, 0), ' ', COALESCE(i.unidad_medida, 'unidades'), ') por debajo del minimo (', COALESCE(i.stock_minimo, 0), ' ', COALESCE(i.unidad_medida, 'unidades'), ')'),
    CASE WHEN COALESCE(i.stock_actual, 0) < (COALESCE(i.stock_minimo, 0) * 0.5) THEN 'alta' WHEN COALESCE(i.stock_actual, 0) < COALESCE(i.stock_minimo, 0) THEN 'media' ELSE 'baja' END, 'pendiente'
    FROM insumos i WHERE i.id_sucursal = p_id_sucursal AND COALESCE(i.stock_actual, 0) <= COALESCE(i.stock_minimo, 0)
    AND NOT EXISTS (SELECT 1 FROM sugerencias_compra sc WHERE sc.id_insumo = i.id AND sc.estado = 'pendiente');
    SELECT ROW_COUNT() AS sugerencias_generadas;
END //

CREATE PROCEDURE sp_verificar_stock_bajo()
BEGIN
    INSERT INTO notificaciones (id_sucursal, tipo, titulo, mensaje, referencia_tipo, referencia_id, leida)
    SELECT p.id_sucursal, 'stock_bajo_producto', CONCAT('Stock Bajo: ', p.nombre), CONCAT('El producto ', p.nombre, ' tiene un stock de ', p.stock_actual, ' unidades'), 'producto', p.id, FALSE
    FROM productos p WHERE p.stock_minimo > 0 AND p.stock_actual <= p.stock_minimo
    AND NOT EXISTS (SELECT 1 FROM notificaciones n WHERE n.referencia_tipo = 'producto' AND n.referencia_id = p.id AND n.leida = FALSE AND DATE(n.fecha_creacion) = CURDATE());
END //

DELIMITER ;

-- ============================================
-- TRIGGERS
-- ============================================
DELIMITER //

CREATE TRIGGER tr_actualizar_stock_venta AFTER INSERT ON venta_productos FOR EACH ROW BEGIN UPDATE productos SET stock_actual = stock_actual - NEW.cantidad WHERE id = NEW.id_producto; END //
CREATE TRIGGER tr_actualizar_stock_produccion AFTER INSERT ON producciones FOR EACH ROW BEGIN UPDATE productos SET stock_actual = stock_actual + NEW.cantidad_producida WHERE id = NEW.id_producto; END //
CREATE TRIGGER tr_descontar_insumos_produccion AFTER INSERT ON produccion_insumos FOR EACH ROW BEGIN UPDATE insumos SET stock_actual = stock_actual - NEW.cantidad_utilizada WHERE id = NEW.id_insumo; END //

DELIMITER ;
