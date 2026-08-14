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

## Phase 1 preview

Install and activate the plugin and theme in WordPress. Plugin activation creates
the Home and Job Scam Checker pages if they do not exist and assigns the homepage.
The checker form is an interface shell in Phase 1; analysis is intentionally
reserved for Phase 2.

## Tests

```bash
php tests/php/run.php
```

See `docs/deployment-hostinger.md` for installation details.
