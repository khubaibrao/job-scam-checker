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

Phase 8 is the production-ready MVP 1.0.0 release. It adds final installation,
security, privacy, SEO, performance, compatibility and archive validation;
reproducible plugin/theme ZIPs; SHA-256 checksums; and a manual Hostinger guide.
See `docs/phase8-release-deployment.md`.

## How to install the MVP

1. Back up WordPress, then confirm it runs WordPress 6.4 or newer and PHP 7.4
   or newer (PHP 8.2 is recommended).
2. In **Plugins → Add New → Upload Plugin**, upload
   `release/job-scam-checker-1.0.0.zip`, install it, and activate it.
3. In **Appearance → Themes → Add New → Upload Theme**, upload
   `release/job-scam-checker-theme-1.0.0.zip`, install it, and activate it.
4. Save **Settings → Permalinks**, verify **Settings → Reading** uses the Home
   page, and review **Job Scam Checker → Settings**.
5. Review all owner, contact, Privacy, Terms, and Disclaimer content before
   launch. Then test the checker, search, `/wp-sitemap.xml`, and `/robots.txt`.

Full non-technical instructions and rollback steps are in
`docs/deployment-hostinger.md`.

## Tests

```bash
bash tools/test-all.sh
```

See `docs/deployment-hostinger.md` for installation details. Review **Job Scam
Checker → Settings** before choosing whether to enable anonymous statistics.
