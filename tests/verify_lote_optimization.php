<?php

require_once __DIR__ . '/../vendor/autoload.php';

use App\Core\Database;
use App\Models\Lote;

class MockPDOStatement extends PDOStatement
{
    public array $mockData = [];
    public static array $executedQueries = [];

    public function fetchAll(int $mode = PDO::FETCH_DEFAULT, mixed ...$args): array
    {
        return $this->mockData;
    }

    public function execute(?array $params = null): bool
    {
        // Update the last query entry with params
        if (!empty(self::$executedQueries)) {
            $lastIndex = count(self::$executedQueries) - 1;
            self::$executedQueries[$lastIndex]['params'] = $params;
        }
        return true;
    }

    public function rowCount(): int
    {
        return 1;
    }
}

class MockPDO extends PDO
{
    public string $lastSql = '';
    public array $mockData = [];

    public function __construct()
    {
        // Don't call parent constructor
    }

    public function prepare(string $query, array $options = []): PDOStatement|false
    {
        $this->lastSql = $query;
        $stmt = new MockPDOStatement();
        $stmt->mockData = $this->mockData;
        MockPDOStatement::$executedQueries[] = [
            'sql' => $query,
            'params' => null
        ];
        return $stmt;
    }
}

class TestLoteOptimization
{
    private Lote $loteModel;
    private MockPDO $mockPdo;

    public function __construct()
    {
        $this->mockPdo = new MockPDO();

        $dbReflection = new ReflectionClass(Database::class);
        $dbInstance = $dbReflection->newInstanceWithoutConstructor();

        $connProperty = $dbReflection->getProperty('connection');
        $connProperty->setAccessible(true);
        $connProperty->setValue($dbInstance, $this->mockPdo);

        $instanceProperty = $dbReflection->getProperty('instance');
        $instanceProperty->setAccessible(true);
        $instanceProperty->setValue(null, $dbInstance);

        $this->loteModel = new Lote();
    }

    public function testSingleLotDeduction()
    {
        echo "Testing single lot deduction...\n";
        MockPDOStatement::$executedQueries = [];
        $this->mockPdo->mockData = [
            ['id' => 10, 'cantidad_actual' => 50.0]
        ];

        $pendiente = $this->loteModel->descontarStock('insumo', 1, 20.0, 1);

        assert($pendiente === 0.0, "Expected 0 remaining stock pending");

        // Check SELECT query
        $selectQuery = MockPDOStatement::$executedQueries[0]['sql'] ?? '';
        assert(str_contains($selectQuery, "SELECT id, cantidad_actual FROM lotes"), "Expected SELECT id, cantidad_actual");
        assert(!str_contains($selectQuery, "SELECT *"), "SELECT * should not be present");

        // Check UPDATE query
        $updateQuery = MockPDOStatement::$executedQueries[1]['sql'] ?? '';
        assert(str_contains($updateQuery, "UPDATE lotes SET cantidad_actual = ?, estado = ? WHERE id = ?"), "Expected single update query");

        $params = MockPDOStatement::$executedQueries[1]['params'] ?? [];
        assert($params === [30.0, 'activo', 10], "Expected params [30.0, 'activo', 10], got " . json_encode($params));

        echo "PASSED: Single lot deduction.\n";
    }

    public function testMultiLotDeductionBatchUpdate()
    {
        echo "Testing multi lot deduction batch update...\n";
        MockPDOStatement::$executedQueries = [];
        $this->mockPdo->mockData = [
            ['id' => 10, 'cantidad_actual' => 15.0],
            ['id' => 11, 'cantidad_actual' => 20.0],
        ];

        $pendiente = $this->loteModel->descontarStock('insumo', 1, 25.0, 1);

        assert($pendiente === 0.0, "Expected 0 remaining stock pending");

        // Check batch UPDATE query
        $batchUpdateSql = MockPDOStatement::$executedQueries[1]['sql'] ?? '';
        assert(str_contains($batchUpdateSql, "UPDATE lotes SET"), "Expected UPDATE lotes SET");
        assert(str_contains($batchUpdateSql, "cantidad_actual = CASE WHEN id = ? THEN ? WHEN id = ? THEN ? END"), "Expected CASE WHEN for cantidad_actual");
        assert(str_contains($batchUpdateSql, "estado = CASE WHEN id = ? THEN ? WHEN id = ? THEN ? END"), "Expected CASE WHEN for estado");
        assert(str_contains($batchUpdateSql, "WHERE id IN (?,?)"), "Expected WHERE id IN (?,?)");

        $params = MockPDOStatement::$executedQueries[1]['params'] ?? [];
        $expectedParams = [10, 0.0, 11, 10.0, 10, 'agotado', 11, 'activo', 10, 11];
        assert($params === $expectedParams, "Batch update params do not match expected order. Got: " . json_encode($params));

        echo "PASSED: Multi lot deduction batch update.\n";
    }
}

$tester = new TestLoteOptimization();
$tester->testSingleLotDeduction();
$tester->testMultiLotDeductionBatchUpdate();

echo "All Lote optimization tests passed successfully!\n";
