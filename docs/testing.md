# Testing

Run all implemented checks from the repository root:

```bash
bash tools/test-all.sh
```

The suite performs PHP syntax checks, exercises the content installer against
WordPress-compatible stubs, confirms repeat activation does not duplicate pages,
renders and inspects the checker markup, verifies required approved copy, and
checks production PHP/CSS/JavaScript for obvious external API/CDN dependencies.

Phase 2 adds mixed legitimate/scam fixtures, rule-library validation, risk
thresholds, overlap and repetition protection, URL/domain classification,
security-warning false positives, XSS-oriented output checks, rate limiting,
schema assertions, nonce rejection, and request-size rejection.

Phase 3 tests Low, Caution, High, and Very High result contracts; contextual and
universal safety actions; suspicious-domain reasons; probabilistic wording;
accessible relationships, announcements, progress, and alerts; no-JavaScript and
malformed-response handling; DOM-safe rendering; phone/mobile breakpoints; print
rules; and the empty hidden advertising integration target.

Phase 4 validates the exact page inventory and type counts, hierarchical path
uniqueness, internal-link resolution, related-key integrity, title/description
uniqueness, minimum article depth, heading hierarchy, required fictional-example
and safety sections, pairwise content similarity, empty ad targets, robots and
sitemap integration, permitted schema types, visible breadcrumbs, accurate
privacy claims, hub completeness, and absence of rating/review claims.

Phase 5 validates enabled and disabled collection, absence of pasted-message
retention, total/risk/rule/channel/money/payment-purpose aggregation, one-use
duplicate protection, invalid follow-up rejection, honest trend thresholds and
empty states, bounded privacy settings, aggregate table keys, native page search,
curated excerpts, allow-listed filters, no-results guidance, and the Privacy
Policy upgrade contract.

Phase 6 validates capability and nonce boundaries, settings sanitization,
checker/display/privacy controls, rule creation/edit/status/duplication paths,
risk-weight and pattern validation, arbitrary-code rejection, custom deletion
and default protection, aggregate dashboard metrics, scoped admin assets, and an
explicitly confirmed reset that targets only the Phase 5 aggregate table.

Phase 7 adds hostile pasted HTML/JavaScript and suspicious-URL tests, explicit
REST permission and CSRF checks, oversized-input and scoped-rate-limit behavior,
malicious regex rejection, disabled-statistics/no-retention assertions, DOM-safe
output, admin capability/nonce/safe-redirect checks, search bounds, accessibility
markup and reduced-motion checks, asset scope, rule-cache invalidation, and all
expected database indexes. The complete suite reruns every Phase 1–6 test first.

Phase 8 adds 44 automated release contracts covering clean activation metadata,
theme readiness, fresh/idempotent content installation, schema/default seeding,
separate schema upgrades, low/caution/high/very-high checker flows, suspicious
links, message non-retention, statistics defaults, REST validation/nonces/rate
limits, search/no-results/404 contracts, metadata/Open Graph, structured data,
sitemap/robots behavior, uninstall safety, platform minimums, absence of external
runtime dependencies/secrets, archive structure, production-only file contents,
ZIP integrity, and SHA-256 verification. Together, Phases 1–8 run **304 tests**.

The full command rebuilds release archives before validating them:

```bash
bash tools/test-all.sh
```

After deployment to a WordPress staging site, manually verify:

- Plugin and theme activation with debug logging enabled
- Homepage at phone, tablet, and desktop widths
- Keyboard navigation, visible focus, skip link, mobile menu, and Escape behavior
- Checker character counter, loading/error states, maximum length, and results
- REST endpoint behavior for low, some, high, and very-high risk examples
- Home/checker page creation and repeat activation
- Permalinks, page title, description, and Open Graph tags
- Theme behavior when the plugin is inactive
- Every screen under Job Scam Checker, including honest empty states
- Rule search/filter, create, edit, duplicate, enable/disable and protected deletion
- Checker, statistics, privacy, display, search and related-content settings
- Aggregate reset and confirmation that content, rules and settings remain intact
- Trend empty state before two real-data periods meet the sample thresholds
- Search queries, content filters, pagination and no-results checker link
- Daily aggregate retention cleanup through WordPress cron
- REST requests without or with an expired checker nonce return 403
- Repeated checker and optional follow-up requests receive scoped 429 responses
- Pasted HTML/JavaScript appears nowhere as active markup or stored content
- Enabled-rule changes invalidate the cache and affect the next check
- Database migration reports version 3.1.0 and includes `metric_key`
- Plugin and theme ZIP upload/activation on the exact Hostinger staging site
- HTTPS, host caching exclusions for REST POSTs, sitemap and robots responses
- Screen-reader announcements, 200%/400% zoom, reflow, focus and contrast
