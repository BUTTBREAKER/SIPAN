## 2025-01-24 - [Date Gap Filling Optimization]
**Learning:** Filling date gaps in time-series results using nested loops (O(N*M)) is a common anti-pattern in this codebase. Using `array_column` to create an associative array (hash map) allows for O(N + M) performance, which is significantly better as the period or number of sales increases.
**Action:** Always look for nested loops over database results when generating reports or chart data and replace them with hash map lookups.

## 2025-01-24 - [Batch Processing and In-Memory Calculations]
**Learning:** When optimizing O(N) loops that perform database-dependent calculations (like weighted average price), pre-fetching all required metadata into a hash map and maintaining local state within the loop allows for O(1) database round-trips while preserving calculation correctness. Hard-coded 'new Class()' calls in models hinder mockability; using null-coalescing model properties facilitates easier testing in environments without a live DB.
**Action:** Use 'WHERE IN' to pre-fetch metadata before loops and refactor internal model instantiations to support dependency injection/mocking.

## 2025-01-24 - [SARGability and Efficient Aggregation in Dashboards]
**Learning:** Fetching all records for a branch only to filter or count them in PHP is a major memory and bandwidth bottleneck as data grows. Aggregating in the database using `GROUP BY` and status-specific counts (e.g., `getCountsBySucursal`) is significantly faster. Additionally, using `DATE(col) = CURDATE()` prevents index usage; range comparisons like `col >= CURDATE()` are SARGable and enable the database to utilize existing indexes on date/time columns.
**Action:** Always prefer SQL-level aggregation (`COUNT`, `GROUP BY`) and ensure `WHERE` clauses remain SARGable by avoiding functions on indexed columns.

## 2025-01-24 - [Dashboard Metric Consolidation]
**Learning:** Fetching multiple aggregate metrics (today, week, month) via separate queries is inefficient. Fetching a single daily dataset for the longest period (e.g., last 31 days) and aggregating in PHP reduces database round-trips by 75% and provides consistent results across different dashboard widgets.
**Action:** Consolidate related aggregate queries into a single daily time-series fetch and process the sub-periods in-memory.

## 2025-01-24 - [Pruning Unused Aggregations in High-Volume Queries]
**Learning:** Performing a many-to-one `JOIN` and `GROUP BY` just to return a count (e.g., `total_productos` in a sales list) is a significant performance drain when that data isn't actually consumed by the frontend. Removing these redundant joins reduces database CPU, memory usage, and execution time, especially as history grows.
**Action:** Before optimizing a query with a join/count, verify if the resulting field is actually used in the view or controller. If not, prune it.

## 2025-01-24 - [Request-Level Configuration Caching]
**Learning:** Global configuration values (like BCV exchange rate) are often accessed multiple times across different components (header, sales creation, reports) during a single request. Implementing a simple in-memory static cache in the model prevents redundant database queries and expensive external API calls without the risk of stale data between requests in standard PHP-FPM environments.
**Action:** Identify frequently accessed configuration keys and implement static properties for request-level caching in the `Configuracion` model or similar utility classes.
