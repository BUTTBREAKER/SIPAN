## 2025-05-14 - [Hash Map Optimization for Date Gap Filling]
**Learning:** Replaced $O(N \times M)$ nested loops with $O(N + M)$ hash map lookups in `Venta` model's reporting methods. This pattern is common in the codebase when filling missing dates in reports. Using `array_column($result, 'value', 'key')` provides a clean and efficient way to create a lookup table in PHP.
**Action:** Always look for nested loops in reporting logic that fill gaps or merge datasets, and replace them with associative array lookups for $O(1)$ access.

## 2025-05-15 - [Static Metadata Caching in BaseModel]
**Learning:** `BaseModel::hasColumn` was performing a `SHOW COLUMNS` query on every call to `all()`, `paginate()`, and `count()` when a sucursal ID was provided. Caching this metadata statically in the model significantly reduces query volume.
**Action:** Use static properties to cache expensive metadata lookups that don't change during the request lifecycle. Normalize database-derived metadata (e.g., column names) to lowercase to ensure case-insensitive matching in PHP.
