CREATE TABLE negocios (
  id INTEGER AUTO_INCREMENT PRIMARY KEY,
  nombre VARCHAR(255) NOT NULL CHECK (LENGTH(nombre) > 0),

  /* registrar un sector, localidad y estado en las tablas correspondientes si
  no existen previamente con los datos del formulario enviados por el usuario,
  luego obtener el id del sector, asignarlo a negocios.id_sector y también
  asignar estados.id_negocio al negocios.id */
  id_sector INTEGER,

  /* el teléfono debe poder repetirse entre negocios del mismo administrador,
  a otro administrador si debe usar teléfonos diferentes */
  telefono VARCHAR(255) NOT NULL CHECK (telefono LIKE '+%'),

  /* mismo caso del teléfono */
  correo VARCHAR(255) NOT NULL CHECK (correo LIKE '%@%'),

  /* de los negocios asignados a un administrador, sólo puede haber un negocio
  principal */
  es_principal BOOLEAN NOT NULL DEFAULT FALSE,

  /* enviar manualmente con PHP para utilizar la zona horaria configurada por
  el desarrollador en las variables de entorno
  (por ejemplo: America/Caracas +04:00) */
  fecha_registro DATETIME NOT NULL
);

CREATE TABLE estados (
  id INTEGER AUTO_INCREMENT PRIMARY KEY,
  nombre VARCHAR(255) NOT NULL UNIQUE CHECK (LENGTH(nombre) > 0),
  id_negocio INTEGER NOT NULL,
  fecha_registro DATETIME NOT NULL

  FOREIGN KEY (id_negocio) REFERENCES negocios(id)
);

CREATE TABLE ciudades (
  id INTEGER AUTO_INCREMENT PRIMARY KEY,
  nombre VARCHAR(255) NOT NULL CHECK (LENGTH(nombre) > 0),
  id_estado INTEGER NOT NULL,
  fecha_registro DATETIME NOT NULL,

  FOREIGN KEY (id_estado) REFERENCES estados(id),
  UNIQUE (nombre, id_estado)
);

CREATE TABLE sectores (
  id INTEGER AUTO_INCREMENT PRIMARY KEY,
  nombre VARCHAR(255) NOT NULL,
  id_ciudad INTEGER NOT NULL,
  fecha_registro DATETIME NOT NULL,

  FOREIGN KEY (id_ciudad) REFERENCES ciudades(id),
  UNIQUE (nombre, id_ciudad)
);

CREATE TABLE usuarios (
  id INTEGER AUTO_INCREMENT PRIMARY KEY,
  primer_nombre VARCHAR(255) NOT NULL CHECK (LENGTH(primer_nombre) > 0),
  segundo_nombre VARCHAR(255) CHECK (LENGTH(segundo_nombre) > 0),
  primer_apellido VARCHAR(255) NOT NULL CHECK (LENGTH(primer_apellido) > 0),
  segundo_apellido VARCHAR(255) CHECK (LENGTH(segundo_apellido) > 0),

  /* sólo el administrador puede usar el mismo correo que el negocio */
  correo VARCHAR(255) UNIQUE NOT NULL CHECK (correo LIKE '%@%'),
  clave VARCHAR(255) NOT NULL CHECK (LENGTH(clave) > 0),
  rol ENUM('Administrador', 'Empleado', 'Cajero') NOT NULL,
  activado BOOLEAN NOT NULL DEFAULT TRUE,
  fecha_registro DATETIME NOT NULL
);

CREATE TABLE asignacion_de_negocios (
  id INTEGER AUTO_INCREMENT PRIMARY KEY,
  id_usuario INTEGER NOT NULL,
  id_negocio INTEGER NOT NULL,
  fecha_registro DATETIME NOT NULL,

  FOREIGN KEY (id_negocio) REFERENCES negocios(id),
  FOREIGN KEY (id_usuario) REFERENCES usuarios(id)
);

/* productos e insumos separados para no vender insumos  */
CREATE TABLE productos (
  id INTEGER AUTO_INCREMENT PRIMARY KEY,
  id_negocio INTEGER NOT NULL,
  id_usuario INTEGER NOT NULL,
  nombre VARCHAR(255) NOT NULL,
  stock_actual DECIMAL(10, 2) NOT NULL DEFAULT 0,
  stock_minimo DECIMAL(10, 2) NOT NULL,
  precio_actual DECIMAL(10, 2) NOT NULL,
  fecha_registro DATETIME NOT NULL,

  FOREIGN KEY (id_negocio) REFERENCES negocios(id).
  FOREIGN KEY (id_usuario) REFERENCES usuarios(id)
);

CREATE TABLE insumos (
  id INTEGER AUTO_INCREMENT PRIMARY KEY,
  id_negocio INTEGER NOT NULL,
  id_usuario INTEGER NOT NULL,
  nombre VARCHAR(255) NOT NULL,
  stock_actual DECIMAL(10, 2) NOT NULL DEFAULT 0,
  stock_minimo DECIMAL(10, 2) NOT NULL DEFAULT 0,
  fecha_registro DATETIME NOT NULL,

  FOREIGN KEY (id_negocio) REFERENCES negocios(id).
  FOREIGN KEY (id_usuario) REFERENCES usuarios(id)
);

CREATE TABLE ventas (
  id INTEGER AUTO_INCREMENT PRIMARY KEY,
  id_usuario INTEGER NOT NULL,
  metodo_pago ENUM('Efectivo', 'Tarjeta', 'Transferencia', 'Crédito') NOT NULL,
  fecha_registro DATETIME NOT NULL,

  FOREIGN KEY (id_usuario) REFERENCES usuarios(id)
);

CREATE TABLE detalles_venta (
  id INTEGER AUTO_INCREMENT PRIMARY KEY,
  id_venta INTEGER NOT NULL,
  id_producto INTEGER NOT NULL,
  precio_unitario_fijo DECIMAL(10, 2) NOT NULL,
  cantidad DECIMAL(10, 2) NOT NULL,
  fecha_registro DATETIME NOT NULL,

  FOREIGN KEY (id_venta) REFERENCES ventas(id),
  FOREIGN KEY (id_producto) REFERENCES productos(id)
);

-- Tabla: Producción
CREATE TABLE produccion (
    id INTEGER AUTO_INCREMENT PRIMARY KEY,
    negocio_id INTEGER NOT NULL,
    usuario_id INTEGER NOT NULL,
    fecha DATE NOT NULL,
    estado ENUM('En Proceso', 'Finalizado') NOT NULL,
    FOREIGN KEY (negocio_id) REFERENCES negocio(id),
    FOREIGN KEY (usuario_id) REFERENCES usuario(id)
);

-- Tabla: Detalles de Producción
CREATE TABLE detalle_produccion (
    id INTEGER AUTO_INCREMENT PRIMARY KEY,
    produccion_id INTEGER NOT NULL,
    inventario_id INTEGER NOT NULL,
    cantidad_usada DECIMAL(10, 2) NOT NULL,
    cantidad_generada DECIMAL(10, 2) NOT NULL,
    FOREIGN KEY (produccion_id) REFERENCES produccion(id),
    FOREIGN KEY (inventario_id) REFERENCES inventario(id)
);

-- Tabla: Predicciones
CREATE TABLE prediccion (
    id INTEGER AUTO_INCREMENT PRIMARY KEY,
    negocio_id INTEGER NOT NULL,
    fecha_inicio DATE NOT NULL,
    fecha_fin DATE NOT NULL,
    tipo ENUM('producción', 'ventas', 'abastecimiento') NOT NULL,
    estado ENUM('Activa', 'Archivada') DEFAULT 'Activa',
    descripcion TEXT,
    FOREIGN KEY (negocio_id) REFERENCES negocio(id)
);

-- Tabla: Detalles de Predicciones
CREATE TABLE detalle_prediccion (
    id INTEGER AUTO_INCREMENT PRIMARY KEY,
    prediccion_id INTEGER NOT NULL,
    inventario_id INTEGER NOT NULL,
    cantidad DECIMAL(10, 2) NOT NULL,
    sugerencia TEXT NOT NULL,
    FOREIGN KEY (prediccion_id) REFERENCES prediccion(id),
    FOREIGN KEY (inventario_id) REFERENCES inventario(id)
);

-- Tabla: Sugerencias
CREATE TABLE sugerencia (
    id INTEGER AUTO_INCREMENT PRIMARY KEY,
    prediccion_id INTEGER NOT NULL,
    descripcion TEXT NOT NULL,
    estado ENUM('Pendiente', 'Implementado') DEFAULT 'Pendiente',
    fecha_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (prediccion_id) REFERENCES prediccion(id)
);
