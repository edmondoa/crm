---
name: Date-only report filters
description: Date range boundaries for SQLite-backed reports and tests.
---

For date-only columns filtered by a user-selected inclusive range, use date-aware comparisons such as `whereDate` rather than comparing date strings with `whereBetween`.

**Why:** SQLite-backed tests can persist Eloquent date casts as `YYYY-MM-DD 00:00:00`. Comparing that value to a string upper bound like `YYYY-MM-DD` can exclude records on the final day.

**How to apply:** When adding report filters for payment, invoice, or other date-only columns, test records on both the start and end dates; use full datetime bounds for timestamp columns.
