## 2025-05-14 - [Hash Map Optimization for Date Gap Filling]
**Learning:** Replaced $O(N \times M)$ nested loops with $O(N + M)$ hash map lookups in `Venta` model's reporting methods. This pattern is common in the codebase when filling missing dates in reports. Using `array_column($result, 'value', 'key')` provides a clean and efficient way to create a lookup table in PHP.
**Action:** Always look for nested loops in reporting logic that fill gaps or merge datasets, and replace them with associative array lookups for $O(1)$ access.

## 2025-05-15 - [N+1 Query Optimization via Batch Fetching]
**Learning:** Found an N+1 query problem in `ReportesController::ventas` where each sale triggered a separate query to `venta_pagos`. Replaced this with a single batch query using `WHERE IN` and an in-memory associative array lookup. This reduced database queries from $1+N$ to $1+1$.
**Action:** Check loops that perform database queries for related records. Implement `getBy*Ids` methods in models to support batch fetching and group results in-memory.
