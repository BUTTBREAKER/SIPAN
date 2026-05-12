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

<<<<<<< bolt-config-caching-884154159979286291
## 2025-01-24 - [Request-Level Configuration Caching]
**Learning:** Global configuration values (like BCV exchange rate) are often accessed multiple times across different components (header, sales creation, reports) during a single request. Implementing a simple in-memory static cache in the model prevents redundant database queries and expensive external API calls without the risk of stale data between requests in standard PHP-FPM environments.
**Action:** Identify frequently accessed configuration keys and implement static properties for request-level caching in the `Configuracion` model or similar utility classes.
=======
## 2026-05-02 - [Request-Level Caching for Configuration]
**Learning:** Configuration settings (especially the BCV exchange rate) are often accessed multiple times during a single request (e.g., in the header and again in specific controllers or views). Implementing request-level in-memory caching using static properties safely reduces redundant database queries and expensive API logic without the complexity of persistent caching or the risk of stale data across different requests.
**Action:** Use static properties to cache frequently accessed model-level data that is unlikely to change during a single request lifecycle, especially for global settings or metadata used in headers.
## 2025-01-24 - [Request-level Configuration Caching]
**Learning:** Global configuration settings and the exchange rate (BCV) are often accessed multiple times in a single request (e.g., once in `header.php` and again in specific views or controllers). Implementing a static cache within the `Configuracion` model prevents redundant database round-trips and ensures that expensive operations like API-based rate expiration checks are performed only once per request.
**Action:** Use static properties for in-memory caching of frequently accessed key-value pairs and state flags to optimize high-traffic global metadata.
## 2026-04-19 - [Request-Level Caching with Defaults]
**Learning:** When implementing request-level caching for methods that allow a fallback default (like `get($key, $default)`), it is critical to cache only the raw result (or `null`) from the database. Caching the provided default value can lead to logic errors in subsequent operations, such as a `set()` method incorrectly performing an `UPDATE` on a non-existent row because it believes the key exists based on a previously cached default.
**Action:** Always decouple the cached database result from the method's return-level default handling to ensure internal state (existence checks) remains accurate.
## 2026-04-18 - [Request-Level Configuration Cache]
**Learning:** Repetitive database queries for global configuration (like exchange rates in a header) on every page load create unnecessary database overhead. A simple request-level in-memory cache using a static property can eliminate these redundant queries. However, special care must be taken for methods with internal expiration logic (like `getTasaBCV`) and methods that depend on database existence checks (like `set`) to avoid bypassing critical checks or breaking `INSERT` vs `UPDATE` logic.
**Action:** Implement request-level caching for configuration models, ensuring a "checked" flag or similar mechanism is used for values that require periodic refreshes, and always bypass the cache for database existence checks in `set()` operations.
## 2026-04-16 - [Static Configuration Caching and Null Handling]
**Learning:** Redundant database queries for global settings (like exchange rates) on every page load create unnecessary overhead. Using `isset()` for cache checks fails to optimize keys that are `null` or missing, as it returns `false` and triggers a re-query. Additionally, caching a caller-provided `$default` value instead of the database result can lead to inconsistent returns if the same key is requested elsewhere with a different default.
**Action:** Use a static array for request-level caching in configuration models, verify existence with `array_key_exists()` to support `null` values, and only cache the raw database result to maintain default value integrity.
## 2025-01-24 - [Unused Controller Fetch and MVC Compliance]
**Learning:** Fetching a full data catalog (e.g., `Producto::all()`) in a controller action when the view performs its own AJAX-based searches is a significant performance drain. Additionally, instantiating models and fetching data directly within views violates MVC patterns and hinders testability.
**Action:** Audit controller-view pairs to ensure all data fetched in the controller is consumed by the view. If the view performs asynchronous searches for the same data, remove the redundant initial fetch. Always refactor in-view model logic into the appropriate controller action.
## 2025-01-24 - [In-Memory Caching for Global Configuration]
**Learning:** Frequent retrieval of global configuration values (like exchange rates) that are used across multiple components (header, controllers, views) can lead to redundant database queries within a single request. Implementing a static in-memory cache at the model level eliminates these extra queries.
**Action:** Use static properties to cache frequently accessed, request-constant configuration values in models to reduce database load. Ensure that 'setter' methods also update this cache to maintain consistency.
>>>>>>> main
