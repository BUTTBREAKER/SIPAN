# SIPAN - Sistema Integral para Panaderías (v2.8)

SIPAN es un sistema web robusto y moderno diseñado para la gestión integral de panaderías y negocios de repostería. Permite un control total sobre las ventas, inventarios (insumos y productos), compras, recetas, producción y análisis predictivo.

## Características Principales

- **Punto de Venta (POS):** Ventas rápidas con múltiples métodos de pago, soporte para USD y Bolívares (VES) con conversión automática basada en la tasa BCV del día.
- **Gestión de Inventario:** Control detallado de stock para productos terminados e insumos, con alertas de stock bajo y trazabilidad por lotes/vencimientos.
- **Producción y Recetas:** Creación de recetas con cálculo automático de costos y módulo de producción que descuenta insumos automáticamente.
- **Predicciones y Sugerencias:** Motor de análisis que sugiere compras de insumos basadas en el consumo histórico y stock actual.
- **Gestión de Proveedores:** Base de datos de proveedores vinculada a las compras y deudas pendientes.
- **Reportes Avanzados:** Estadísticas visuales de ventas, compras, productos más vendidos y rendimiento por sucursal.
- **Seguridad y Auditoría:** Sistema de roles (Admin, Empleado, Cajero) y registro detallado de todas las acciones con opción de "Deshacer"(Undo).
- **Onboarding:** Tour interactivo guiado para nuevos usuarios.

## Tecnologías Utilizadas

- **Backend:** PHP 7.4+ (Arquitectura MVC personalizada)
- **Base de Datos:** MySQL 5.7+ / MariaDB
- **Frontend:** Vanilla JS, Alpine.js, Grid.js, Chart.js, Tailwind CSS (en componentes específicos)
- **Estilos:** CSS3 Moderno con efectos Glassmorphism.

## Estructura del Proyecto

- `/app`: Lógica de la aplicación (Controllers, Models, Middlewares).
- `/config`: Archivos de configuración de base de datos y sistema.
- `/public`: Punto de entrada (`index.php`) y recursos públicos (CSS, JS, imágenes).
- `database.sql`: Esquema completo y consolidado de la base de datos.

## Últimos Avances (v2.8)

- Implementación de tasa BCV automatizada.
- Sistema de sugerencias de compra inteligente.
- Corrección de seguridad y manejo de datos NULL en insumos.
- Interfaz modernizada con diseño Premium.

# Guia de Instalacion SIPAN v2.8

## Requisitos Previos

- **Servidor Web**: Apache 2.4+ (Recomendado XAMPP en Windows)
- **PHP**: Versión 7.4 o superior
- **MySQL/MariaDB**: MySQL 5.7+ o MariaDB 10.3+
- **Extensiones PHP obligatorias**: `pdo`, `pdo_mysql`, `json`, `mbstring`, `session`, `curl`

## Paso 1: Preparar los Archivos

1. Extrae el contenido del proyecto en tu directorio web (ej. `C:\xampp\htdocs\sipan`).
2. Asegúrate de que el servidor Apache esté configurado para permitir archivos `.htaccess` (AllowOverride All).

## Paso 2: Configurar la Base de Datos

1. Abre tu gestor de base de datos (ej. phpMyAdmin o MySQL Workbench).
2. Crea una base de datos llamada `sipan`.
3. Importa el archivo `database.sql` que contiene la estructura completa y consolidada:
```cmd
C:\xampp\mysql\bin\mysql.exe -u root -p sipan < database.sql
```
_(Si usas XAMPP y no tienes contraseña en root, omite el `-p`)_.

## Paso 3: Configuración del Sistema

1. Renombra o asegúrate de tener el archivo `config/config.php` (usando `config.php.example` como base si es necesario).
2. Ajusta las credenciales de tu base de datos:
```php
return [
    'db_host' => 'localhost',
    'db_name' => 'sipan',
    'db_user' => 'root',
    'db_pass' => '', // Tu contraseña
    // ...
];
```

## Paso 4: Acceso Inicial

1. Accede mediante tu navegador a `http://localhost/sipan/public`.
2. Credenciales por defecto:
- **Email**: `admin@sipan.com`
- **Contraseña**: `admin123`

> [!IMPORTANT]
> Cambia la contraseña inmediatamente después de entrar en la sección de Perfil/Usuarios.

## Solución de Problemas

- **Error 404 en rutas**: Verifica que el módulo `mod_rewrite` de Apache esté activado y que el archivo `.htaccess` exista en la carpeta `public`.
- **Error de Conexión**: Revisa que los datos en `config/config.php` coincidan con tu servidor MySQL local.
- **Caracteres extraños**: El sistema está configurado para `utf8mb4`. Asegúrate de que tu base de datos y conexión usen este charset.

---
**Versión:** 2.8.0  
**Estado:** Estable / Producción
**Fecha**: Enero 2026
