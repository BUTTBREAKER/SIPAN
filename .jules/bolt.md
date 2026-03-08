## 2025-05-14 - [Hash Map Optimization for Date Gap Filling]
**Learning:** Replaced $O(N \times M)$ nested loops with $O(N + M)$ hash map lookups in `Venta` model's reporting methods. This pattern is common in the codebase when filling missing dates in reports. Using `array_column($result, 'value', 'key')` provides a clean and efficient way to create a lookup table in PHP.
**Action:** Always look for nested loops in reporting logic that fill gaps or merge datasets, and replace them with associative array lookups for $O(1)$ access.

## 2025-05-14 - [Redundant Stock Updates vs Database Triggers]
**Learning:** Found that `Venta::createWithProducts` was manually updating product stock in a loop, while a database trigger `tr_actualizar_stock_venta` was already doing the same. This caused double-subtraction and unnecessary database round-trips.
**Action:** Before implementing manual stock or balance updates, check `database/migrations/` for triggers. Rely on triggers for ACID compliance and performance, and remove redundant application-level updates.

## 2025-05-14 - [Batch SQL Operations for High-Volume Transactions]
**Learning:** Optimized a sale creation process from $O(N)$ queries to $O(1)$ using `WHERE IN` for validation and multi-row `INSERT` for details. Grouping quantities by ID before validation is crucial to handle duplicate items in the same transaction correctly.
**Action:** Use `array_reduce` or a loop to aggregate quantities by ID before batch validation. Use `implode(',', array_fill(0, count($ids), '(?, ?, ...)'))` to construct batch inserts.
