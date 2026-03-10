## 2025-05-14 - [Hash Map Optimization for Date Gap Filling]
**Learning:** Replaced $O(N \times M)$ nested loops with $O(N + M)$ hash map lookups in `Venta` model's reporting methods. This pattern is common in the codebase when filling missing dates in reports. Using `array_column($result, 'value', 'key')` provides a clean and efficient way to create a lookup table in PHP.
**Action:** Always look for nested loops in reporting logic that fill gaps or merge datasets, and replace them with associative array lookups for $O(1)$ access.

## 2025-05-15 - [Database Metadata Caching]
**Learning:** The 'BaseModel::hasColumn' method was performing a 'SHOW COLUMNS' query on every call to 'all()', 'paginate()', or 'count()'. In a single request, this could result in dozens of redundant metadata queries.
**Action:** Implemented a static '$columnCache' in 'BaseModel' to store table schema information for the duration of the request. This reduces database overhead for all model-based operations.

## 2025-05-15 - [Batch Retrieval for Complex Reports]
**Learning:** Identified N+1 query patterns in reports where details (like payments per sale or stats per client) were fetched in loops.
**Action:** For payments, used 'WHERE IN' batch fetching with in-memory mapping. For client stats, merged the statistics into the main 'SELECT' using 'LEFT JOIN' and 'GROUP BY'. Both techniques reduce +N$ queries to (1)$.
