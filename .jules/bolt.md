## 2025-05-14 - [Hash Map Optimization for Date Gap Filling]
**Learning:** Replaced $O(N \times M)$ nested loops with $O(N + M)$ hash map lookups in `Venta` model's reporting methods. This pattern is common in the codebase when filling missing dates in reports. Using `array_column($result, 'value', 'key')` provides a clean and efficient way to create a lookup table in PHP.
**Action:** Always look for nested loops in reporting logic that fill gaps or merge datasets, and replace them with associative array lookups for $O(1)$ access.

## 2025-05-15 - [Redundant Stock Updates vs Database Triggers]
**Learning:** Found that `Venta` and `Produccion` models were manually updating stock levels in PHP code, while the database already had triggers (`tr_actualizar_stock_venta`, `tr_descontar_insumos_produccion`) doing the same. This redundancy caused a double-decrement bug and increased DB round-trips.
**Action:** Check for existing database triggers before implementing state-changing logic in models to avoid duplicate processing and data inconsistency.
