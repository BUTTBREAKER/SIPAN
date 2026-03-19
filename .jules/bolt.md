## 2025-01-24 - [Date Gap Filling Optimization]
**Learning:** Filling date gaps in time-series results using nested loops (O(N*M)) is a common anti-pattern in this codebase. Using `array_column` to create an associative array (hash map) allows for O(N + M) performance, which is significantly better as the period or number of sales increases.
**Action:** Always look for nested loops over database results when generating reports or chart data and replace them with hash map lookups.

## 2025-01-24 - [Batch Processing and In-Memory Calculations]
**Learning:** When optimizing O(N) loops that perform database-dependent calculations (like weighted average price), pre-fetching all required metadata into a hash map and maintaining local state within the loop allows for O(1) database round-trips while preserving calculation correctness. Hard-coded 'new Class()' calls in models hinder mockability; using null-coalescing model properties facilitates easier testing in environments without a live DB.
**Action:** Use 'WHERE IN' to pre-fetch metadata before loops and refactor internal model instantiations to support dependency injection/mocking.
