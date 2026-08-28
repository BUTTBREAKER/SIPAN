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

## 2025-01-24 - [N+1 Query Elimination in Views]
**Learning:** Instantiating models and performing database queries within a loop inside a View (e.g., fetching ingredient counts for a list of recipes) is a severe N+1 performance bottleneck. Moving the aggregation to the initial SQL query using `LEFT JOIN` and `GROUP BY` reduces database round-trips from O(N) to O(1) and improves architectural separation by removing model dependencies from the View.
**Action:** Always check Views for PHP loops that perform database calls and refactor the underlying Model method to include the required data via SQL joins or aggregations.

## 2025-01-24 - [BaseModel Signature Consistency]
**Learning:** Overriding `BaseModel` methods (like `all()`) with incompatible signatures or ignoring standard parameters (like `$sucursal_id`) creates silent performance bottlenecks. Controllers passing these arguments expect filtering that isn't happening, leading to global data leaks and high memory usage.
**Action:** When overriding `BaseModel` methods, ensure signatures match exactly and honor inherited filtering parameters to prevent performance regressions in a multi-tenant/multi-branch architecture.

## 2025-01-24 - [Unused Join and Aggregation Optimization]
**Learning:** Performing a `LEFT JOIN` and `GROUP BY` to calculate a field that is never displayed in the UI is a common source of database overhead. Removing these redundant operations, especially in many-to-one relationships (like sales to products), drastically reduces query complexity and memory usage as the dataset grows.
**Action:** Before implementing an aggregation in a listing query, verify that the resulting field is actually used in the associated view or controller.

## 2025-01-24 - [Pruning Unused Aggregations in High-Volume Queries]
**Learning:** Performing a many-to-one `JOIN` and `GROUP BY` just to return a count (e.g., `total_productos` in a sales list) is a significant performance drain when that data isn't actually consumed by the frontend. Removing these redundant joins reduces database CPU, memory usage, and execution time, especially as history grows.
**Action:** Before optimizing a query with a join/count, verify if the resulting field is actually used in the view or controller. If not, prune it.

## 2025-01-24 - [Request-Level Configuration Caching]
**Learning:** Global configuration values (like BCV exchange rate) are often accessed multiple times across different components (header, sales creation, reports) during a single request. Implementing a simple in-memory static cache in the model prevents redundant database queries and expensive external API calls without the risk of stale data between requests in standard PHP-FPM environments.
**Action:** Identify frequently accessed configuration keys and implement static properties for request-level caching in the `Configuracion` model or similar utility classes.

## 2025-01-24 - [Unused Controller Fetch and MVC Compliance]
**Learning:** Fetching a full data catalog (e.g., `Producto::all()`) in a controller action when the view performs its own AJAX-based searches is a significant performance drain. Additionally, instantiating models and fetching data directly within views violates MVC patterns and hinders testability.
**Action:** Audit controller-view pairs to ensure all data fetched in the controller is consumed by the view. If the view performs asynchronous searches for the same data, remove the redundant initial fetch. Always refactor in-view model logic into the appropriate controller action.

## 2025-01-24 - [Chat SQL Optimization]
**Learning:** Refactoring scalar subqueries in the SELECT list and correlated subqueries in JOINs into derived table joins reduces complexity from O(N*M) to O(N+M). For unread counts, ensuring context-aware filters (like `id_usuario`) are inside the derived table is critical for performance as the message volume grows. Additionally, using `MAX(id)` in a grouped derived table is more efficient than `ORDER BY ... LIMIT 1` for retrieving the latest record per group in MySQL.
**Action:** Replace scalar subqueries in SELECT lists with LEFT JOINs to derived tables. Use `MAX(id)` for "latest record" patterns and ensure user-specific filters are pushed down into subqueries.
