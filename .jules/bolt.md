## 2025-05-15 - [Database Metadata Caching]
**Learning:** Calling `SHOW COLUMNS` repeatedly in models that inherit from a base class with automatic filtering (like `BaseModel::all`) creates significant overhead in large reports or lists.
**Action:** Implement static caching for table metadata in the base model to ensure schema lookups happen only once per request lifecycle.

## 2025-05-15 - [Batch Retrieval for Reports]
**Learning:** N+1 query patterns are the primary bottleneck in nested report generation (e.g., fetching payments per sale or stats per client).
**Action:** Use batch retrieval with `WHERE IN` for related records or `LEFT JOIN` with `GROUP BY` for aggregates to achieve $O(1)$ database round-trips.

## 2025-05-15 - [PHPCBF Behavior]
**Learning:** `composer format` (phpcbf) can exit with code 2 even when successfully fixing issues, and it may apply extraneous formatting to unrelated files.
**Action:** Always check `git status` after formatting and revert changes to files outside the PR scope to maintain focus.
