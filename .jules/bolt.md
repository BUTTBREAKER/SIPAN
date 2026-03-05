## 2025-05-14 - [Hash Map Optimization for Date Gap Filling]
**Learning:** Replaced $O(N \times M)$ nested loops with $O(N + M)$ hash map lookups in `Venta` model's reporting methods. This pattern is common in the codebase when filling missing dates in reports. Using `array_column($result, 'value', 'key')` provides a clean and efficient way to create a lookup table in PHP.
**Action:** Always look for nested loops in reporting logic that fill gaps or merge datasets, and replace them with associative array lookups for $O(1)$ access.

## 2025-05-15 - [Database Triggers vs Application Logic]
**Learning:** The database contains triggers (`tr_actualizar_stock_venta`, `tr_descontar_insumos_produccion`) that automatically handle stock management. Manual stock updates in PHP models were redundant, causing extra DB roundtrips and potential data corruption (double-processing).
**Action:** Always verify the existence of database triggers before implementing state-changing logic in models. Remove redundant manual updates to improve performance and data integrity.

## 2025-05-15 - [Batch Retrieval for Reporting]
**Learning:** N+1 query patterns in reports (e.g., fetching payments for each sale in a loop) significantly degrade performance as the dataset grows. Implementing batch retrieval methods (using `WHERE IN`) and grouping data in memory with hash maps reduces $1+N$ queries to $2$.
**Action:** In reporting controllers, identify queries within loops and refactor them to use batch retrieval in the model.
