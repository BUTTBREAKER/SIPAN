<?php

namespace App\Core;

class Database
{
    private static $instance = null;
    public $lastSql = '';
    public $lastParams = [];

    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function fetchAll($sql, $params = [])
    {
        $this->lastSql = $sql;
        $this->lastParams = $params;
        return [];
    }

    public function fetchOne($sql, $params = [])
    {
        $this->lastSql = $sql;
        $this->lastParams = $params;
        return [];
    }

    public function execute($sql, $params = [])
    {
        $this->lastSql = $sql;
        $this->lastParams = $params;
        return 0;
    }
}

namespace Tests;

require_once __DIR__ . '/../app/Models/ChatMensaje.php';

use App\Models\ChatMensaje;
use App\Core\Database;

$chatModel = new ChatMensaje();
$db = Database::getInstance();

echo "--- Verificando ChatMensaje::getConversaciones ---\n";
$chatModel->getConversaciones(1);
$sql1 = $db->lastSql;
echo "SQL: " . $sql1 . "\n";

// Detectar subconsultas correlacionadas (SELECT dentro de SELECT o JOIN m.id = (SELECT...))
$hasCorrelatedInSelect = preg_match('/SELECT\s*\(.*SELECT/is', $sql1) || strpos($sql1, 'm.id = (') !== false;
if ($hasCorrelatedInSelect) {
    echo "❌ ERROR: Se detectó una subconsulta correlacionada en el SELECT o JOIN.\n";
} else {
    echo "✅ OK: No se detectaron subconsultas correlacionadas.\n";
}

echo "\n--- Verificando ChatMensaje::contarNoLeidos ---\n";
$chatModel->contarNoLeidos(1);
$sql2 = $db->lastSql;
echo "SQL: " . $sql2 . "\n";

// Buscar patrones de subconsulta correlacionada: SELECT (SELECT COUNT(*) FROM chat_mensajes ... WHERE ... = cp.id_conversacion)
$hasCorrelatedInContar = preg_match('/SELECT\s*\(.*SELECT\s*COUNT\s*\(\*\)\s*FROM\s*chat_mensajes/is', $sql2);
if ($hasCorrelatedInContar) {
    echo "❌ ERROR: Se detectó una subconsulta correlacionada en contarNoLeidos.\n";
} else {
    echo "✅ OK: No se detectaron subconsultas correlacionadas.\n";
}

if ($hasCorrelatedInSelect || $hasCorrelatedInContar) {
    exit(1);
}

echo "\n✨ ¡Optimización verificada exitosamente!\n";
exit(0);
