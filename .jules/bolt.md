## 2025-05-14 - [Hash Map Optimization for Date Gap Filling]
**Learning:** Replaced $O(N \times M)$ nested loops with $O(N + M)$ hash map lookups in `Venta` model's reporting methods. This pattern is common in the codebase when filling missing dates in reports. Using `array_column($result, 'value', 'key')` provides a clean and efficient way to create a lookup table in PHP.
**Action:** Always look for nested loops in reporting logic that fill gaps or merge datasets, and replace them with associative array lookups for $O(1)$ access.

## 2025-05-15 - [Batch Retrieval to Fix N+1 Query in Reports]
**Learning:** Resolved an N+1 query problem in the sales report by replacing iterative database calls with a single batch query () and in-memory grouping. This is a crucial pattern for reports that join multiple one-to-many relationships.
**Action:** When a report iterates over a main dataset (e.g., Sales) and needs to fetch related data (e.g., Payments), implement a `getBatchXByIds` method in the model and group the results in the controller using associative arrays.

## 2025-05-15 - [Batch Retrieval to Fix N+1 Query in Reports]
**Learning:** Resolved an N+1 query problem in the sales report by replacing iterative database calls with a single batch query (`WHERE IN`) and in-memory grouping. This is a crucial pattern for reports that join multiple one-to-many relationships.
**Action:** When a report iterates over a main dataset (e.g., Sales) and needs to fetch related data (e.g., Payments), implement a `getBatchXByIds` method in the model and group the results in the controller using associative arrays.
