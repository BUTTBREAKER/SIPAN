## 2025-05-14 - [Hash Map Optimization for Date Gap Filling]
**Learning:** Replaced $O(N \times M)$ nested loops with $O(N + M)$ hash map lookups in `Venta` model's reporting methods. This pattern is common in the codebase when filling missing dates in reports. Using `array_column($result, 'value', 'key')` provides a clean and efficient way to create a lookup table in PHP.
**Action:** Always look for nested loops in reporting logic that fill gaps or merge datasets, and replace them with associative array lookups for $O(1)$ access.
## 2025-05-15 - [Redundant Stock Management Logic vs Database Triggers]
**Learning:** Found that `Venta::createWithProducts` was manually updating stock levels even though a database trigger (`tr_actualizar_stock_venta`) already handled this. This caused double-subtraction of stock and unnecessary database round-trips. Batching insertions and relying on triggers reduced DB calls by >60%.
**Action:** Before optimizing stock updates, check `database/migrations` for triggers that might already handle inventory changes to avoid data inconsistency and redundant queries.
