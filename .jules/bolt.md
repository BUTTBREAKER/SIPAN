## 2025-05-14 - [Hash Map Optimization for Date Gap Filling]
**Learning:** Replaced $O(N \times M)$ nested loops with $O(N + M)$ hash map lookups in `Venta` model's reporting methods. This pattern is common in the codebase when filling missing dates in reports. Using `array_column($result, 'value', 'key')` provides a clean and efficient way to create a lookup table in PHP.
**Action:** Always look for nested loops in reporting logic that fill gaps or merge datasets, and replace them with associative array lookups for $O(1)$ access.

## 2025-05-15 - [N+1 Query Optimization in Client Reports]
**Learning:** Identified a significant performance bottleneck in the `ReportesController::clientes` method where statistics were fetched in a loop for each client ( + N$ queries). Consolidating this into a single query using `LEFT JOIN` and `GROUP BY` reduced the database round-trips to $O(1)$.
**Action:** When generating reports that aggregate data from related tables, prioritize using SQL aggregation and joins over application-level loops.
