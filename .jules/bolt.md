## 2025-05-14 - [Hash Map Optimization for Date Gap Filling]
**Learning:** Replaced $O(N \times M)$ nested loops with $O(N + M)$ hash map lookups in `Venta` model's reporting methods. This pattern is common in the codebase when filling missing dates in reports. Using `array_column($result, 'value', 'key')` provides a clean and efficient way to create a lookup table in PHP.
**Action:** Always look for nested loops in reporting logic that fill gaps or merge datasets, and replace them with associative array lookups for $O(1)$ access.

## 2025-05-15 - [N+1 Query Optimization in Sales Reporting]
**Learning:** Identified an N+1 query bottleneck in `ReportesController::ventas` where payments were fetched individually for each sale. Replaced it with a batch-fetching strategy using `WHERE IN` and an in-memory hash map for (1)$ lookup. This is particularly effective for reports with large datasets where database round-trips significantly impact performance.
**Action:** When iterating over a collection and performing dependent database lookups (like sales -> payments), always batch the lookups using `WHERE IN` and group the results in-memory using an associative array indexed by the parent's ID.
