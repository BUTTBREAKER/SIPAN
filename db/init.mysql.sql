-- --------------------------------------------
-- 🧠 BASE DE DATOS: SIPAN - SISTEMA PARA PANADERÍAS
-- Optimizado con soporte para multi-sucursal, auditoría, producción, ventas y predicciones
-- --------------------------------------------

CREATE DATABASE IF NOT EXISTS sipan CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE sipan;

-- -------------------------------
-- 🏢 NEGOCIOS Y SUCURSALES
-- -------------------------------

CREATE TABLE negocios (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nombre VARCHAR(255) NOT NULL,
  direccion TEXT,
  telefono VARCHAR(20) NOT NULL CHECK (telefono LIKE '+%'),
  correo VARCHAR(255) NOT NULL CHECK (correo LIKE '%@%'),
  es_principal BOOLEAN DEFAULT FALSE,
  fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE sucursales (
  id INT AUTO_INCREMENT PRIMARY KEY,
  negocio_id INT NOT NULL,
  nombre VARCHAR(255) NOT NULL,
  direccion TEXT,
  telefono VARCHAR(20),
  fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (negocio_id) REFERENCES negocios(id)
);

-- -------------------------------
-- 👥 USUARIOS Y ROLES
-- -------------------------------

CREATE TABLE usuarios (
  id INT AUTO_INCREMENT PRIMARY KEY,
  sucursal_id INT NOT NULL,
  primer_nombre VARCHAR(100) NOT NULL,
  segundo_nombre VARCHAR(100),
  primer_apellido VARCHAR(100) NOT NULL,
  segundo_apellido VARCHAR(100),
  correo VARCHAR(255) UNIQUE NOT NULL CHECK (correo LIKE '%@%'),
  clave VARCHAR(255) NOT NULL,
  rol ENUM('Administrador', 'Empleado', 'Cajero') NOT NULL,
  activo BOOLEAN DEFAULT TRUE,
  fecha_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (sucursal_id) REFERENCES sucursales(id)
);

-- -------------------------------
-- 📦 INVENTARIO
-- -------------------------------

CREATE TABLE productos (
  id INT AUTO_INCREMENT PRIMARY KEY,
  sucursal_id INT NOT NULL,
  nombre VARCHAR(255) NOT NULL,
  tipo ENUM('Producto', 'Insumo') NOT NULL,
  stock_actual DECIMAL(10,2) DEFAULT 0,
  stock_minimo DECIMAL(10,2) DEFAULT 0,
  precio_actual DECIMAL(10,2),
  fecha_actualizacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (sucursal_id) REFERENCES sucursales(id)
);

-- -------------------------------
-- 💰 VENTAS
-- -------------------------------

CREATE TABLE ventas (
  id INT AUTO_INCREMENT PRIMARY KEY,
  sucursal_id INT NOT NULL,
  usuario_id INT NOT NULL,
  metodo_pago ENUM('Efectivo','Tarjeta','Transferencia','Crédito') NOT NULL,
  total DECIMAL(10,2) NOT NULL,
  fecha DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (sucursal_id) REFERENCES sucursales(id),
  FOREIGN KEY (usuario_id) REFERENCES usuarios(id)
);

CREATE TABLE detalles_venta (
  id INT AUTO_INCREMENT PRIMARY KEY,
  venta_id INT NOT NULL,
  producto_id INT NOT NULL,
  cantidad DECIMAL(10,2) NOT NULL,
  precio_unitario DECIMAL(10,2) NOT NULL,
  FOREIGN KEY (venta_id) REFERENCES ventas(id),
  FOREIGN KEY (producto_id) REFERENCES productos(id)
);

-- -------------------------------
-- 👨‍🍳 PRODUCCIÓN
-- -------------------------------

CREATE TABLE produccion (
  id INT AUTO_INCREMENT PRIMARY KEY,
  sucursal_id INT NOT NULL,
  usuario_id INT NOT NULL,
  fecha DATE NOT NULL,
  estado ENUM('En proceso','Finalizado') DEFAULT 'En proceso',
  FOREIGN KEY (sucursal_id) REFERENCES sucursales(id),
  FOREIGN KEY (usuario_id) REFERENCES usuarios(id)
);

CREATE TABLE detalle_produccion (
  id INT AUTO_INCREMENT PRIMARY KEY,
  produccion_id INT NOT NULL,
  producto_id INT NOT NULL,
  cantidad_usada DECIMAL(10,2),
  cantidad_generada DECIMAL(10,2),
  FOREIGN KEY (produccion_id) REFERENCES produccion(id),
  FOREIGN KEY (producto_id) REFERENCES productos(id)
);

-- -------------------------------
-- 🔁 TRANSFERENCIAS ENTRE SUCURSALES
-- -------------------------------

CREATE TABLE transferencias (
  id INT AUTO_INCREMENT PRIMARY KEY,
  sucursal_origen INT NOT NULL,
  sucursal_destino INT NOT NULL,
  producto_id INT NOT NULL,
  cantidad DECIMAL(10,2) NOT NULL,
  estado ENUM('Pendiente','Aprobada','Rechazada') DEFAULT 'Pendiente',
  fecha_solicitud TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (sucursal_origen) REFERENCES sucursales(id),
  FOREIGN KEY (sucursal_destino) REFERENCES sucursales(id),
  FOREIGN KEY (producto_id) REFERENCES productos(id)
);

-- -------------------------------
-- 📈 PREDICCIONES
-- -------------------------------

CREATE TABLE predicciones (
  id INT AUTO_INCREMENT PRIMARY KEY,
  sucursal_id INT NOT NULL,
  tipo ENUM('Producción','Ventas','Abastecimiento') NOT NULL,
  estado ENUM('Activa','Archivada') DEFAULT 'Activa',
  fecha_inicio DATE,
  fecha_fin DATE,
  descripcion TEXT,
  FOREIGN KEY (sucursal_id) REFERENCES sucursales(id)
);

CREATE TABLE detalles_prediccion (
  id INT AUTO_INCREMENT PRIMARY KEY,
  prediccion_id INT NOT NULL,
  producto_id INT NOT NULL,
  cantidad DECIMAL(10,2) NOT NULL,
  sugerencia TEXT,
  FOREIGN KEY (prediccion_id) REFERENCES predicciones(id),
  FOREIGN KEY (producto_id) REFERENCES productos(id)
);

CREATE TABLE sugerencias (
  id INT AUTO_INCREMENT PRIMARY KEY,
  prediccion_id INT NOT NULL,
  descripcion TEXT NOT NULL,
  implementada BOOLEAN DEFAULT FALSE,
  fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (prediccion_id) REFERENCES predicciones(id)
);

-- -------------------------------
-- 📋 AUDITORÍA / LOGS
-- -------------------------------

CREATE TABLE logs (
  id INT AUTO_INCREMENT PRIMARY KEY,
  usuario_id INT,
  accion VARCHAR(100) NOT NULL,
  descripcion TEXT,
  fecha TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (usuario_id) REFERENCES usuarios(id)
);
