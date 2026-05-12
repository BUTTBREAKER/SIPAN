<?php

require_once __DIR__ . '/../app/Models/BaseModel.php';
require_once __DIR__ . '/../app/Models/Configuracion.php';

class MockDB {
    public $queryCount = 0;
    public $lastSql = '';
    public $data = [
        'tasa_bcv' => ['valor' => '55.50', 'updated_at' => '2025-01-01 12:00:00'],
        'sitio_nombre' => ['valor' => 'SIPAN Test']
    ];

    public function fetchOne($sql, $params = []) {
        $this->queryCount++;
        $this->lastSql = $sql;
        $key = $params[0];

        // Simular lógica de SELECT 1 para existencia
        if (strpos($sql, 'SELECT 1') !== false) {
            return isset($this->data[$key]);
        }

        return $this->data[$key] ?? null;
    }

    public function execute($sql, $params = []) {
        $this->queryCount++;
        $this->lastSql = $sql;
        $key = $params[count($params) - 1];
        $val = $params[0];

        if (strpos($sql, 'INSERT') !== false) {
             $key = $params[0];
             $val = $params[1];
        }

        $this->data[$key] = ['valor' => $val, 'updated_at' => date('Y-m-d H:i:s')];
        return true;
    }
}

class MockConfiguracion extends \App\Models\Configuracion {
    public function __construct($db) {
        $this->db = $db;
    }
}

function runTest() {
    echo "--- Iniciando Test de Cache de Configuracion Refacturado ---\n";

    $mockDb = new MockDB();
    $config = new MockConfiguracion($mockDb);

    // Test 1: Primera llamada a get()
    echo "Test 1: Primera llamada a get('sitio_nombre')...\n";
    $val1 = $config->get('sitio_nombre');
    assert($val1 === 'SIPAN Test');
    assert($mockDb->queryCount === 1);
    echo "OK: Valor recuperado y query realizada.\n";

    // Test 2: Segunda llamada a get() (debe usar cache)
    echo "Test 2: Segunda llamada a get('sitio_nombre')...\n";
    $val2 = $config->get('sitio_nombre');
    assert($val2 === 'SIPAN Test');
    assert($mockDb->queryCount === 1); // No debe aumentar
    echo "OK: Valor recuperado de cache (0 queries adicionales).\n";

    // Test 3: getTasaBCV() primera llamada DESPUÉS de un get('tasa_bcv')
    echo "Test 3: get('tasa_bcv') seguido de getTasaBCV()...\n";
    $config->get('tasa_bcv');
    assert($mockDb->queryCount === 2);

    $tasa1 = $config->getTasaBCV();
    assert($tasa1 === 55.50);
    assert($mockDb->queryCount === 3); // DEBE realizar una query para verificar updated_at
    echo "OK: getTasaBCV() realizó verificación a pesar de estar en cache general.\n";

    // Test 4: Segunda llamada a getTasaBCV() (debe usar cache específico)
    echo "Test 4: Segunda llamada a getTasaBCV()...\n";
    $tasa2 = $config->getTasaBCV();
    assert($tasa2 === 55.50);
    assert($mockDb->queryCount === 3); // No debe aumentar
    echo "OK: Valor recuperado de cache específico de tasa (0 queries adicionales).\n";

    // Test 5: set() para una clave nueva (debe ser INSERT)
    echo "Test 5: set() para una clave nueva...\n";
    $config->set('nueva_clave', 'valor_nuevo');
    assert(strpos($mockDb->lastSql, 'INSERT') !== false);
    assert($config->get('nueva_clave') === 'valor_nuevo');
    echo "OK: Clave nueva insertada correctamente.\n";

    // Test 6: set() para una clave existente (debe ser UPDATE)
    echo "Test 6: set() para una clave existente...\n";
    $config->set('sitio_nombre', 'Nuevo SIPAN');
    assert(strpos($mockDb->lastSql, 'UPDATE') !== false);
    assert($config->get('sitio_nombre') === 'Nuevo SIPAN');
    echo "OK: Clave existente actualizada correctamente.\n";

    echo "--- Todos los tests pasaron exitosamente ---\n";
}

try {
    runTest();
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    echo "Trace: " . $e->getTraceAsString() . "\n";
    exit(1);
}
