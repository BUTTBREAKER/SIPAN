<?php

namespace App\Core {
    class Database {
        private static $instance = null;
        public $queryLog = [];
        public $mockData = [];

        public static function getInstance() {
            if (self::$instance === null) {
                self::$instance = new self();
            }
            return self::$instance;
        }

        public function fetchOne($sql, $params = []) {
            $this->queryLog[] = ['sql' => $sql, 'params' => $params];
            if (strpos($sql, "FROM cajas") !== false && strpos($sql, "LEFT JOIN caja_movimientos") !== false) {
                return $this->mockData['caja_resumen'] ?? ['monto_apertura' => 100, 'ingresos' => 50, 'egresos' => 20];
            }
            if (strpos($sql, "FROM cajas") !== false) {
                return $this->mockData['active_caja'] ?? ['id' => 1, 'id_sucursal' => 1, 'estado' => 'abierta'];
            }
            return null;
        }

        public function fetchAll($sql, $params = []) {
            $this->queryLog[] = ['sql' => $sql, 'params' => $params];
            if (strpos($sql, "FROM sucursales") !== false) {
                return $this->mockData['active_sucursales'] ?? [['id' => 1, 'nombre' => 'Sucursal 1']];
            }
            return [];
        }

        public function execute($sql, $params = []) {
            $this->queryLog[] = ['sql' => $sql, 'params' => $params];
            return true;
        }

        public function create($table, $data) {
            return 1;
        }

        public function lastInsertId() {
            return 1;
        }
    }
}

namespace {
    require_once __DIR__ . '/../app/Models/BaseModel.php';
    require_once __DIR__ . '/../app/Models/Caja.php';
    require_once __DIR__ . '/../app/Models/Sucursal.php';

    use App\Models\Caja;
    use App\Models\Sucursal;
    use App\Core\Database;

    function verifyCaching() {
        $db = Database::getInstance();
        $cajaModel = new Caja();
        $sucursalModel = new Sucursal();

        echo "Testing Caja::getActiva caching...\n";
        $db->queryLog = [];
        $caja1 = $cajaModel->getActiva(1);
        $count1 = count($db->queryLog);

        $caja2 = $cajaModel->getActiva(1);
        $count2 = count($db->queryLog);

        if ($count1 === 1 && $count2 === 1) {
            echo "✅ Caja::getActiva caching works (1 query for 2 calls).\n";
        } else {
            echo "❌ Caja::getActiva caching failed. Queries: $count1, $count2\n";
            exit(1);
        }

        echo "Testing Sucursal::getActivas caching...\n";
        $db->queryLog = [];
        $suc1 = $sucursalModel->getActivas();
        $scount1 = count($db->queryLog);

        $suc2 = $sucursalModel->getActivas();
        $scount2 = count($db->queryLog);

        if ($scount1 === 1 && $scount2 === 1) {
            echo "✅ Sucursal::getActivas caching works (1 query for 2 calls).\n";
        } else {
            echo "❌ Sucursal::getActivas caching failed. Queries: $scount1, $scount2\n";
            exit(1);
        }

        echo "Testing Caja cache invalidation on abrir()...\n";
        $cajaModel->abrir(1, 1, 100, 0, 50);
        $db->queryLog = [];
        $cajaModel->getActiva(1);
        if (count($db->queryLog) === 1) {
            echo "✅ Caja cache invalidated on abrir().\n";
        } else {
            echo "❌ Caja cache NOT invalidated on abrir().\n";
            exit(1);
        }

        echo "Testing Caja cache invalidation on cerrar()...\n";
        $cajaModel->getActiva(1); // refill cache
        $cajaModel->cerrar(1, 1, 100, 0, 50);
        $db->queryLog = [];
        $cajaModel->getActiva(1);
        if (count($db->queryLog) === 1) {
            echo "✅ Caja cache invalidated on cerrar().\n";
        } else {
            echo "❌ Caja cache NOT invalidated on cerrar().\n";
            exit(1);
        }

        echo "Testing consolidated Caja::getResumen query...\n";
        $db->queryLog = [];
        $resumen = $cajaModel->getResumen(1);
        $rLog = $db->queryLog[0]['sql'];
        if (strpos($rLog, "LEFT JOIN caja_movimientos") !== false) {
            echo "✅ Caja::getResumen uses consolidated JOIN query.\n";
            echo "   Data: Apertura: {$resumen['apertura']}, Ingresos: {$resumen['ingresos']}, Egresos: {$resumen['egresos']}, Esperado: {$resumen['esperado']}\n";
        } else {
            echo "❌ Caja::getResumen does NOT use consolidated query.\n";
            exit(1);
        }

        echo "\n🎉 All Caja and Sucursal optimizations verified successfully!\n";
    }

    verifyCaching();
}
