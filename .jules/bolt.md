## 2025-05-14 - [Hash Map Optimization for Date Gap Filling]
**Learning:** Replaced $O(N \times M)$ nested loops with $O(N + M)$ hash map lookups in `Venta` model's reporting methods. This pattern is common in the codebase when filling missing dates in reports. Using `array_column($result, 'value', 'key')` provides a clean and efficient way to create a lookup table in PHP.
**Action:** Always look for nested loops in reporting logic that fill gaps or merge datasets, and replace them with associative array lookups for $O(1)$ access.

## 2025-05-15 - [Solving N+1 Queries in Reports]
**Learning:** Identified high-impact N+1 bottlenecks in `ReportesController`. Optimized by (1) using batch retrieval with `WHERE IN` for related entities (Sales -> Payments) and (2) using `LEFT JOIN` with `GROUP BY` for aggregated statistics (Clients -> Sales Stats). Both patterns reduce database complexity from $O(N)$ to $O(1)$ queries relative to the main dataset size.
**Action:** Prioritize batch retrieval for related data and JOINs for statistics in all reporting modules to minimize database round-trips.
