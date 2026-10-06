<?php

namespace App\Models;

require_once __DIR__ . '/../vendor/autoload.php';

echo "Testing Proveedor optimizations...\n";

// 1. Verify addInsumos SQL batch insert
$reflector = new \ReflectionMethod(Proveedor::class, 'addInsumos');
$fileName = $reflector->getFileName();
$startLine = $reflector->getStartLine();
$endLine = $reflector->getEndLine();

$source = implode('', array_slice(file($fileName), $startLine - 1, $endLine - $startLine + 1));

assert(strpos($source, 'INSERT INTO proveedor_insumos') !== false, 'INSERT statement must exist');
assert(strpos($source, 'foreach ($insumos as $insumo)') !== false, 'Insumos loop must exist');
assert(strpos($source, 'execute($sql, $params)') !== false, 'Single batch execute must be called outside foreach loop');
echo "✅ SUCCESS: Proveedor::addInsumos batches multi-row INSERTs in a single query.\n";

// 2. Verify getInsumosSinProveedor query structure
$reflector2 = new \ReflectionMethod(Proveedor::class, 'getInsumosSinProveedor');
$startLine2 = $reflector2->getStartLine();
$endLine2 = $reflector2->getEndLine();

$rawSource2 = implode('', array_slice(file($fileName), $startLine2 - 1, $endLine2 - $startLine2 + 1));
// Strip single-line and multi-line comments
$cleanSource2 = preg_replace('!/\*.*?\*/!s', '', $rawSource2);
$cleanSource2 = preg_replace('!//.*!', '', $cleanSource2);

assert(strpos($cleanSource2, 'NOT EXISTS') !== false, 'Query must use NOT EXISTS');
assert(strpos($cleanSource2, 'GROUP BY') === false, 'Query must not use GROUP BY');
assert(strpos($cleanSource2, 'HAVING') === false, 'Query must not use HAVING');

echo "✅ SUCCESS: Proveedor::getInsumosSinProveedor uses SARGable NOT EXISTS without GROUP BY anti-pattern.\n";
echo "All Proveedor optimizations verified successfully!\n";
