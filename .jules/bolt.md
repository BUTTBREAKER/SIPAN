## 2025-05-14 - [Hash Map Optimization for Date Gap Filling]
**Learning:** Replaced $O(N \times M)$ nested loops with $O(N + M)$ hash map lookups in `Venta` model's reporting methods. This pattern is common in the codebase when filling missing dates in reports. Using `array_column($result, 'value', 'key')` provides a clean and efficient way to create a lookup table in PHP.
**Action:** Always look for nested loops in reporting logic that fill gaps or merge datasets, and replace them with associative array lookups for $O(1)$ access.

## 2025-05-15 - [Batch Retrieval to Solve N+1 Query Problem]
**Learning:** Found an N+1 query bottleneck in `ReportesController::ventas()` where payments were fetched individually for each sale. Solved this by implementing a batch retrieval method in the `Venta` model (`getPagosByVentaIds`) and grouping results in-memory using an associative array. This reduced database round-trips from $N+1$ to 2.
**Action:** Identify loops that perform database queries and refactor them to fetch all required data in a single batch query (e.g., using `WHERE IN`).
