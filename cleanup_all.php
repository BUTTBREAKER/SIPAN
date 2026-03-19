<?php
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
    "enum_output.txt"
];

foreach ($files as $file) {
    if (file_exists($file)) {
        if (unlink($file)) {
            echo "Eliminado: $file\n";
        } else {
            echo "Error al eliminar: $file\n";
        }
    } else {
        echo "No existe: $file\n";
    }
}

// También limpiar en public/
$public_files = [
    'public/debug_status.php',
    'public/web_fix_status.php',
    'public/diag.php'
];

foreach ($public_files as $file) {
    if (file_exists($file)) {
        if (unlink($file)) {
            echo "Eliminado: $file\n";
        } else {
            echo "Error al eliminar: $file\n";
        }
    }
}
?>
