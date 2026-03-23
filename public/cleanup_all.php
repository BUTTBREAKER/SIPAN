<?php

$root = dirname(__DIR__);
$files = [
    'check_db_schema.php',
    'check_db_v2.php',
    'check_enum.php',
    'db_fix_status.php',
    'db_repair_all.php',
    'diag.php',
    'diag_log.txt',
    'fix_db.php',
    'test_backup.php',
    "fetchAll('DESCRIBE",
    "fetchAll('SELECT",
    "generarRespaldo(1))",
    "getMessage()",
    "fix_output.txt",
    "enum_output.txt",
    "repair_output.txt",
    "backup_test_output.txt",
    "db_final_check.txt",
    "hello.txt",
    "final_db_check.txt",
    "dir_output.txt",
    "enum_output.txt",
    "cleanup_all.php"
];

header('Content-Type: text/plain');
echo "INICIANDO LIMPIEZA DESDE: $root\n";

foreach ($files as $file) {
    $path = $root . '/' . $file;
    if (file_exists($path)) {
        if (unlink($path)) {
            echo "Eliminado: $file\n";
        } else {
            echo "Error al eliminar: $file\n";
        }
    } else {
        echo "No existe: $file\n";
    }
}

// Limpiar archivos en public/ (excepto este)
$public_files = [
    'debug_status.php',
    'web_fix_status.php',
    'diag.php',
    'cleanup_delivery.php',
    'test_tool.txt'
];

foreach ($public_files as $file) {
    $path = __DIR__ . '/' . $file;
    if (file_exists($path)) {
        if (unlink($path)) {
            echo "Eliminado public/: $file\n";
        } else {
            echo "Error al eliminar public/: $file\n";
        }
    }
}

echo "PROCESO COMPLETADO.\n";
