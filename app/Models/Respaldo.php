<?php

namespace App\Models;

class Respaldo extends BaseModel
{
    protected $table = 'respaldos';

    public function crear($usuario_id, $nombre_archivo, $ruta_archivo, $tamano)
    {
        $sql = "INSERT INTO {$this->table} (id_usuario, nombre_archivo, ruta_archivo, tamano_bytes, tipo)
                VALUES (?, ?, ?, ?, 'manual')";
        return $this->db->execute($sql, [$usuario_id, $nombre_archivo, $ruta_archivo, $tamano]);
    }

    public function getAll()
    {
        $sql = "SELECT r.*, 
                CONCAT_WS(' ', u.primer_nombre, u.segundo_nombre, u.apellido_paterno, u.apellido_materno) as usuario_nombre
                FROM {$this->table} r
                INNER JOIN usuarios u ON r.id_usuario = u.id
                ORDER BY r.fecha_creacion DESC";
        return $this->db->fetchAll($sql);
    }

    public function generarRespaldo($usuario_id)
    {
        $config = require __DIR__ . '/../../config/config.php';

        $fecha = date('Y-m-d_H-i-s');
        $nombre_archivo = "sipan_backup_{$fecha}.sql";
        $ruta_completa = $config['base_path'] . "/backups/{$nombre_archivo}";

        // Crear directorio de respaldos si no existe
        $backup_dir = $config['base_path'] . "/backups";
        if (!is_dir($backup_dir)) {
            mkdir($backup_dir, 0755, true);
        }

        // Detectar la ruta de mysqldump
        $mysqldump_path = $this->findMysqldump();

        if (!$mysqldump_path) {
            return ['success' => false, 'error' => 'No se encontró mysqldump. Verifica que MySQL esté instalado correctamente.'];
        }

        // Comando mysqldump (sin espacios después de -p)
        $comando = sprintf(
            '"%s" --host=%s --user=%s --password=%s --no-tablespaces %s > "%s" 2>&1',
            $mysqldump_path,
            $config['db_host'],
            $config['db_user'],
            $config['db_pass'],
            $config['db_name'],
            $ruta_completa
        );

        exec($comando, $output, $return_var);

        if ($return_var === 0 && file_exists($ruta_completa) && filesize($ruta_completa) > 0) {
            $tamano = filesize($ruta_completa);
            $this->crear($usuario_id, $nombre_archivo, $ruta_completa, $tamano);
            return ['success' => true, 'archivo' => $nombre_archivo];
        }

        // Si falla, intentar eliminar el archivo creado (puede estar vacío o corrupto)
        if (file_exists($ruta_completa)) {
            unlink($ruta_completa);
        }

        $error_msg = !empty($output) ? implode("\n", $output) : 'Error desconocido';
        return ['success' => false, 'error' => 'Error al generar respaldo: ' . $error_msg];
    }

    public function restaurarRespaldo($id)
    {
        $respaldo = $this->find($id);

        if (!$respaldo || !file_exists($respaldo['ruta_archivo'])) {
            return ['success' => false, 'error' => 'Archivo de respaldo no encontrado'];
        }

        $config = require __DIR__ . '/../../config/config.php';

        // Detectar la ruta de mysql
        $mysql_path = $this->findMysql();

        if (!$mysql_path) {
            return ['success' => false, 'error' => 'No se encontró mysql. Verifica que MySQL esté instalado correctamente.'];
        }

        // Comando mysql (sin espacios después de -p)
        $comando = sprintf(
            '"%s" --host=%s --user=%s --password=%s %s < "%s" 2>&1',
            $mysql_path,
            $config['db_host'],
            $config['db_user'],
            $config['db_pass'],
            $config['db_name'],
            $respaldo['ruta_archivo']
        );

        exec($comando, $output, $return_var);

        if ($return_var === 0) {
            return ['success' => true, 'mensaje' => 'Respaldo restaurado correctamente'];
        }

        $error_msg = !empty($output) ? implode("\n", $output) : 'Error desconocido';
        return ['success' => false, 'error' => 'Error al restaurar: ' . $error_msg];
    }

    /**
     * Buscar la ruta de mysqldump en el sistema
     */
    private function findMysqldump()
    {
        // Rutas comunes en Windows (XAMPP, WAMP, etc.)
        $possible_paths = [
            'C:/xampp/mysql/bin/mysqldump.exe',
            'C:/wamp64/bin/mysql/mysql8.0.*/bin/mysqldump.exe',
            'C:/Program Files/MySQL/MySQL Server 8.0/bin/mysqldump.exe',
            'C:/Program Files/MySQL/MySQL Server 5.7/bin/mysqldump.exe',
        ];

        // Intentar con el PATH del sistema primero
        exec('where mysqldump 2>nul', $output, $return_var);
        if ($return_var === 0 && !empty($output[0])) {
            return trim($output[0]);
        }

        // Buscar en rutas comunes
        foreach ($possible_paths as $path) {
            // Manejar wildcards para versiones
            if (strpos($path, '*') !== false) {
                $matches = glob($path);
                if (!empty($matches) && file_exists($matches[0])) {
                    return $matches[0];
                }
            } elseif (file_exists($path)) {
                return $path;
            }
        }

        // En Linux/Mac, intentar con which
        exec('which mysqldump 2>/dev/null', $output, $return_var);
        if ($return_var === 0 && !empty($output[0])) {
            return trim($output[0]);
        }

        return false;
    }

    /**
     * Buscar la ruta de mysql en el sistema
     */
    private function findMysql()
    {
        // Rutas comunes en Windows
        $possible_paths = [
            'C:/xampp/mysql/bin/mysql.exe',
            'C:/wamp64/bin/mysql/mysql8.0.*/bin/mysql.exe',
            'C:/Program Files/MySQL/MySQL Server 8.0/bin/mysql.exe',
            'C:/Program Files/MySQL/MySQL Server 5.7/bin/mysql.exe',
        ];

        // Intentar con el PATH del sistema primero
        exec('where mysql 2>nul', $output, $return_var);
        if ($return_var === 0 && !empty($output[0])) {
            return trim($output[0]);
        }

        // Buscar en rutas comunes
        foreach ($possible_paths as $path) {
            if (strpos($path, '*') !== false) {
                $matches = glob($path);
                if (!empty($matches) && file_exists($matches[0])) {
                    return $matches[0];
                }
            } elseif (file_exists($path)) {
                return $path;
            }
        }

        // En Linux/Mac
        exec('which mysql 2>/dev/null', $output, $return_var);
        if ($return_var === 0 && !empty($output[0])) {
            return trim($output[0]);
        }

        return false;
    }
}
