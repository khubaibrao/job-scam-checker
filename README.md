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

## Tests

```bash
bash tools/test-all.sh
```

See `docs/deployment-hostinger.md` for installation details.
