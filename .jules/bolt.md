# Bolt's Journal - Critical Performance Learnings

## 2025-01-24 - Hash Map Optimization for Date-Based Reports
**Learning:** The codebase used nested loops ($O(N \times M)$) to fill in gaps for days without sales in report methods like `getVentasUltimosDias` and `getVentasPorPeriodo`. While $N$ is typically small (7-30), this is an inefficient pattern that scales poorly if a larger range is requested.
**Action:** Always use PHP's associative arrays (hash maps) with `array_column()` to index database results by a key (like date) for $O(1)$ lookups during gap filling.
