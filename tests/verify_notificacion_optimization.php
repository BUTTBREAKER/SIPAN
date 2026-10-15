<?php

namespace App\Core;

if (!class_exists(\App\Core\Database::class, false)) {
    class Database
    {
        private static $instance = null;
        public array $queries = [];

        public static function getInstance()
        {
            if (self::$instance === null) {
                self::$instance = new self();
            }
            return self::$instance;
        }

        public function fetchAll($sql, $params = [])
        {
            $this->queries[] = ['sql' => $sql, 'params' => $params];
            return [];
        }

        public function fetchOne($sql, $params = [])
        {
            $this->queries[] = ['sql' => $sql, 'params' => $params];
            return [];
        }

        public function execute($sql, $params = [])
        {
            $this->queries[] = ['sql' => $sql, 'params' => $params];
            return true;
        }
    }
}

namespace TestRunner;

require_once __DIR__ . '/../app/Models/BaseModel.php';
require_once __DIR__ . '/../app/Models/Notificacion.php';

use App\Models\Notificacion;
use App\Core\Database;

function verify_notificacion_optimization()
{
    $db = Database::getInstance();
    $notifModel = new Notificacion();

    echo "Testing Notificacion::getNoLeidas without usuario_id...\n";
    $notifModel->getNoLeidas(1);
    $lastQuery = end($db->queries);

    assert(str_contains($lastQuery['sql'], 'SELECT id, tipo, titulo, mensaje, referencia_tipo, referencia_id, fecha_creacion'), 'Query should explicitly select required columns');
    assert(!str_contains($lastQuery['sql'], 'SELECT *'), 'Query should not use SELECT *');
    assert(str_contains($lastQuery['sql'], 'LIMIT 50'), 'Query should contain LIMIT 50 clause');
    assert(count($lastQuery['params']) === 1, 'Parameters count should be 1 (sucursal_id)');
    assert($lastQuery['params'][0] === 1, 'First parameter should be sucursal_id');

    echo "Testing Notificacion::getNoLeidas with usuario_id and custom limit...\n";
    $notifModel->getNoLeidas(1, 5, 20);
    $lastQuery = end($db->queries);

    assert(str_contains($lastQuery['sql'], 'LIMIT 20'), 'Query should contain custom LIMIT 20 clause');
    assert(count($lastQuery['params']) === 2, 'Parameters count should be 2 (sucursal_id, usuario_id)');
    assert($lastQuery['params'][0] === 1, 'First parameter should be sucursal_id');
    assert($lastQuery['params'][1] === 5, 'Second parameter should be usuario_id');

    echo "✅ All Notificacion::getNoLeidas optimization assertions passed successfully!\n";
}

verify_notificacion_optimization();
