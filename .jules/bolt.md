## 2025-05-14 - [Hash Map Optimization for Date Gap Filling]
**Learning:** Replaced $O(N \times M)$ nested loops with $O(N + M)$ hash map lookups in `Venta` model's reporting methods. This pattern is common in the codebase when filling missing dates in reports. Using `array_column($result, 'value', 'key')` provides a clean and efficient way to create a lookup table in PHP.
**Action:** Always look for nested loops in reporting logic that fill gaps or merge datasets, and replace them with associative array lookups for $O(1)$ access.

## 2025-05-15 - [Database Triggers vs. Application Logic Redundancy]
**Learning:** Found that the database contains triggers (e.g., `tr_actualizar_stock_venta`) that automatically update stock when records are inserted into detail tables. Many models (e.g., `Venta`, `Produccion`) manually execute the same UPDATE queries, leading to double-processing and potential data corruption.
**Action:** Before implementing stock updates in PHP, check if a DB trigger already handles it. Removing redundant application-level updates improves performance and ensures data integrity.
