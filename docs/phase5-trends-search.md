# Phase 5 anonymous trends and native search

Phase 5 adds opt-in, aggregate-only usage statistics and improves discovery of
the existing curated content. It uses WordPress, PHP, MySQL/MariaDB and local
JavaScript only. It adds no external search, analytics, AI, or paid service.

## Anonymous aggregate collection

Anonymous statistics are disabled by default. An administrator can enable them
under **Settings → Job Scam Checker**, independently disable the optional
follow-up questions, and choose aggregate retention from 30 to 730 days. The
checker continues to analyze messages normally when collection is disabled.

When enabled, a successful check increments daily counters for total checks,
the result level (`low`, `caution`, `high`, or `very_high`), and stable slugs of
warning rules detected by the existing engine. Optional follow-up selections
increment channel, money-request and payment-purpose counters.

The `wp_jsc_daily_stats` table (using the site's configured prefix) has one row
per UTC date, metric and safe key. It never receives pasted text, names, phone
numbers, email addresses, IP addresses, full URLs, identity documents, banking
details, or visitor identifiers. Existing temporary rate limiting uses a salted
one-way network identifier. Duplicate prevention uses a random, short-lived,
one-use token containing no message or identity data.

## Trends

The homepage's **Trending Job Scam Patterns** compares the latest 14 days with
the preceding 14. A pattern appears only when both periods contain at least 10
real checks, the current pattern has at least five occurrences, the prior pattern
has at least three, and its share of checks increased. No invented percentages,
sample data or claims appear. Otherwise it says: “Not enough real data yet to
show a trend.” Results are cached locally for 15 minutes and invalidated by
aggregate writes.

## Search and content discovery

Native WordPress search is accessible from the header and footer. It searches
curated pages, returns 12 results per page, labels content types, uses curated
SEO descriptions as excerpts, and allows filters for tools, scam types, guides
and category hubs. Empty results suggest broader terms and link to the checker.

Related reading retains curated relationships, then adds reverse relationships
and a bounded number of pages sharing the current content type using the existing
installed-page map rather than an unbounded query.

## Security and lifecycle

- Analysis and follow-up requests require the same-origin nonce.
- Analysis retains request-size and rate limits; follow-up fields are enums.
- Follow-up tokens are one-use and expire after 15 minutes.
- Settings use WordPress sanitization and the `manage_options` capability.
- Aggregate values use prepared SQL and a controlled prefixed table name.
- Daily WordPress cron removes counters outside the 30–730 day retention choice.
- Deactivation clears the schedule; uninstall removes aggregate options/table.

The Privacy Policy has an exact-content upgrade path from untouched Phase 4
copy. Administrator-edited privacy pages remain unchanged.
