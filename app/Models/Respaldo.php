<?php

namespace SIPAN\Models;

class Respaldo extends BaseModel
{
  protected $table = 'respaldos';

  public function crear($usuario_id, $nombre_archivo, $ruta_archivo, $tamano)
  {
    $sql = "INSERT INTO {$this->table} (id_usuario, nombre_archivo, ruta_archivo, tamano)
                VALUES (?, ?, ?, ?)";
    return $this->db->execute($sql, [$usuario_id, $nombre_archivo, $ruta_archivo, $tamano]);
  }

  public function getAll()
  {
    $sql = "SELECT r.*, u.nombre as usuario_nombre
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

    // Comando mysqldump
    $comando = sprintf(
      'mysqldump -h %s -u %s -p%s %s > %s',
      escapeshellarg($config['db_host']),
      escapeshellarg($config['db_user']),
      escapeshellarg($config['db_pass']),
      escapeshellarg($config['db_name']),
      escapeshellarg($ruta_completa)
    );

    exec($comando, $output, $return_var);

    if ($return_var === 0 && file_exists($ruta_completa)) {
      $tamano = filesize($ruta_completa);
      $this->crear($usuario_id, $nombre_archivo, $ruta_completa, $tamano);
      return ['success' => true, 'archivo' => $nombre_archivo];
    }

    return ['success' => false, 'error' => 'Error al generar respaldo'];
  }

  public function restaurarRespaldo($id)
  {
    $respaldo = $this->find($id);

    if (!$respaldo || !file_exists($respaldo['ruta_archivo'])) {
      return ['success' => false, 'error' => 'Archivo de respaldo no encontrado'];
    }

    $config = require __DIR__ . '/../../config/config.php';

    $comando = sprintf(
      'mysql -h %s -u %s -p%s %s < %s',
      escapeshellarg($config['db_host']),
      escapeshellarg($config['db_user']),
      escapeshellarg($config['db_pass']),
      escapeshellarg($config['db_name']),
      escapeshellarg($respaldo['ruta_archivo'])
    );

    exec($comando, $output, $return_var);

    if ($return_var === 0) {
      return ['success' => true, 'mensaje' => 'Respaldo restaurado correctamente'];
    }

    return ['success' => false, 'error' => 'Error al restaurar respaldo'];
  }
}
