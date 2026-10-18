<?php

require_once __DIR__ . '/../vendor/autoload.php';

use App\Models\Insumo;
use App\Core\Database;

$dbReflection = new ReflectionClass(Database::class);
$mockDb = $dbReflection->newInstanceWithoutConstructor();

// Replace Database instance
$instanceProp = $dbReflection->getProperty('instance');
$instanceProp->setAccessible(true);
$instanceProp->setValue(null, $mockDb);

class PDOMock extends PDO {
    public static string $lastSql = '';
    public static array $lastParams = [];

    public function __construct() {}

    public function prepare($query, $options = null): PDOStatement|false {
        self::$lastSql = $query;
        $stmt = new class extends PDOStatement {
            public function __construct() {}
            public function execute($params = null): bool {
                PDOMock::$lastParams = $params ?? [];
                return true;
            }
            public function fetchAll($mode = PDO::FETCH_DEFAULT, mixed ...$args): array {
                return [];
            }
        };
        return $stmt;
    }
}

$pdoMock = new PDOMock();

$connProp = $dbReflection->getProperty('connection');
$connProp->setAccessible(true);
$connProp->setValue($mockDb, $pdoMock);

$insumoModel = new Insumo();

echo "Testing Insumo::getAllBySucursal SQL...\n";
$insumoModel->getAllBySucursal(1);
$sqlBySucursal = PDOMock::$lastSql;
$paramsBySucursal = PDOMock::$lastParams;

echo "Captured SQL:\n" . $sqlBySucursal . "\n";
echo "Captured Params:\n" . json_encode($paramsBySucursal) . "\n\n";

if (preg_match('/SELECT\s+i\.\*,\s*GROUP_CONCAT.*?\(\s*SELECT/i', $sqlBySucursal)) {
    echo "FAIL: Correlated scalar subquery detected in SELECT list!\n";
    exit(1);
}

if (!str_contains($sqlBySucursal, 'LEFT JOIN (') || !str_contains($sqlBySucursal, 'SUBSTRING_INDEX')) {
    echo "FAIL: Derived table JOIN with SUBSTRING_INDEX expected in query!\n";
    exit(1);
}

if (count($paramsBySucursal) !== 2 || $paramsBySucursal[0] !== 1 || $paramsBySucursal[1] !== 1) {
    echo "FAIL: Expected 2 sucursal_id parameters bound to query!\n";
    exit(1);
}

echo "Testing Insumo::all (global) SQL...\n";
$insumoModel->all(null);
$sqlGlobal = PDOMock::$lastSql;

echo "Captured SQL:\n" . $sqlGlobal . "\n";

if (preg_match('/SELECT\s+i\.\*,\s*GROUP_CONCAT.*?\(\s*SELECT/i', $sqlGlobal)) {
    echo "FAIL: Correlated scalar subquery detected in global SELECT list!\n";
    exit(1);
}

if (!str_contains($sqlGlobal, 'LEFT JOIN (') || !str_contains($sqlGlobal, 'SUBSTRING_INDEX')) {
    echo "FAIL: Derived table JOIN with SUBSTRING_INDEX expected in global query!\n";
    exit(1);
}

echo "SUCCESS: Insumo model SQL queries successfully optimized without correlated scalar subqueries!\n";
