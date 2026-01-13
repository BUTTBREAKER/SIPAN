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
   ```bash
   C:\xampp\mysql\bin\mysql.exe -u root -p sipan < database.sql
   ```
   *(Si usas XAMPP y no tienes contraseña en root, omite el `-p`)*.

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

- **Error 404 en rutas**: Verifica que el módulo `mod_rewrite` de Apache esté activado y que el archivo `.htaccess` exista en la carpeta `public/`.
- **Error de Conexión**: Revisa que los datos en `config/config.php` coincidan con tu servidor MySQL local.
- **Caracteres extraños**: El sistema está configurado para `utf8mb4`. Asegúrate de que tu base de datos y conexión usen este charset.

---
**Versión**: 2.8.0  
**Fecha**: Enero 2026
