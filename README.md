# Job Scam Checker

A lightweight, privacy-first WordPress MVP for checking suspicious employment
messages. The production package uses PHP, WordPress, CSS, and a small amount of
vanilla JavaScript. It has no paid API, AI, external database, or Node.js runtime
dependency.

## Repository layout

- `wordpress/plugins/job-scam-checker` — checker application plugin
- `wordpress/themes/job-scam-checker-theme` — public-facing custom theme
- `tests` — dependency-free Phase 1 smoke and integration tests
- `docs` — architecture and Hostinger deployment notes

## Working checker

Install and activate the plugin and theme in WordPress. Plugin activation creates
the Home and Job Scam Checker pages if they do not exist, assigns the homepage,
and seeds the local rule library. The checker performs private, same-origin PHP
analysis with no AI or external service.

Results provide an accessible score and risk level, detailed warning explanations,
safe domain findings, and practical next steps. See
`docs/phase3-results-experience.md` for the presentation and privacy contract.

The curated SEO release contains 30 intentional pages with hierarchical scam and
guide hubs, original educational articles, breadcrumbs, related reading, unique
metadata and honest structured data. See `docs/phase4-content-seo.md`.

Phase 5 adds administrator-controlled anonymous daily aggregate statistics,
optional post-result channel/payment questions, threshold-gated real-data trends,
native filtered WordPress search, and broader related-content paths. Statistics
are disabled by default and never store pasted messages or visitor profiles. See
`docs/phase5-trends-search.md`.

Phase 6 adds a dedicated native WordPress administration area with real-data
overview and statistics screens, validated rule management, protected default
rules, organized checker/privacy/display settings, a tightly scoped aggregate
reset, and links into normal WordPress page editing. See
`docs/phase6-admin-management.md`.

Phase 7 hardens REST permissions, abuse controls, hostile HTML/URL handling,
regex validation, search bounds and privacy regressions. It also caches enabled
rules, removes repeated admin lookups, adds an aggregate-query index, and audits
accessibility/shared-hosting deployment. Pasted messages are still never stored.
See `docs/phase7-hardening-performance.md`.

## Tests

```bash
bash tools/test-all.sh
```

See `docs/deployment-hostinger.md` for installation details. Review **Job Scam
Checker → Settings** before choosing whether to enable anonymous statistics.
