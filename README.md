# SIPAN - Sistema Integral para Panaderías (v2.8)

## Descripción

SIPAN es un sistema web robusto y moderno diseñado para la gestión integral de panaderías y negocios de repostería. Permite un control total sobre las ventas, inventarios (insumos y productos), compras, recetas, producción y análisis predictivo.

## Características Principales

*   **Punto de Venta (POS):** Ventas rápidas con múltiples métodos de pago, soporte para USD y Bolívares (VES) con conversión automática basada en la tasa BCV del día.
*   **Gestión de Inventario:** Control detallado de stock para productos terminados e insumos, con alertas de stock bajo y trazabilidad por lotes/vencimientos.
*   **Producción y Recetas:** Creación de recetas con cálculo automático de costos y módulo de producción que descuenta insumos automáticamente.
*   **Predicciones y Sugerencias:** Motor de análisis que sugiere compras de insumos basadas en el consumo histórico y stock actual.
*   **Gestión de Proveedores:** Base de datos de proveedores vinculada a las compras y deudas pendientes.
*   **Reportes Avanzados:** Estadísticas visuales de ventas, compras, productos más vendidos y rendimiento por sucursal.
*   **Seguridad y Auditoría:** Sistema de roles (Admin, Empleado, Cajero) y registro detallado de todas las acciones con opción de "Deshacer" (Undo).
*   **Onboarding:** Tour interactivo guiado para nuevos usuarios.

## Tecnologías Utilizadas

*   **Backend:** PHP 7.4+ (Arquitectura MVC personalizada)
*   **Base de Datos:** MySQL 5.7+ / MariaDB
*   **Frontend:** Vanilla JS, Alpine.js, Grid.js, Chart.js, Tailwind CSS (en componentes específicos)
*   **Estilos:** CSS3 Moderno con efectos Glassmorphism.

## Estructura del Proyecto

*   `app/`: Lógica de la aplicación (Controllers, Models, Middlewares).
*   `config/`: Archivos de configuración de base de datos y sistema.
*   `public/`: Punto de entrada (`index.php`) y recursos públicos (CSS, JS, imágenes).
*   `database.sql`: Esquema completo y consolidado de la base de datos.

## Últimos Avances (v2.8)

*   Implementación de tasa BCV automatizada.
*   Sistema de sugerencias de compra inteligente.
*   Corrección de seguridad y manejo de datos NULL en insumos.
*   Interfaz modernizada con diseño Premium.

## Soporte

Para más detalles, consulte el archivo `INSTALACION.md`.

---
**Versión:** 2.8.0  
**Estado:** Estable / Producción
