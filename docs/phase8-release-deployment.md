# Phase 8: final QA and MVP release

Release version: **1.0.0**. Database schema version: **3.1.0**. Content release
version: **5.0.0**. Supported minimums are WordPress 6.4 and PHP 7.4; PHP 8.2 is
recommended for a normal Hostinger shared-hosting installation.

## Final audit result

The plugin, theme, checker flow, deterministic rules/scoring, results UI, 30-page
content release, aggregate trends, native search, administration, privacy,
security, accessibility, performance, SEO, installation, upgrade, and uninstall
paths were reviewed. The placeholder package URLs were removed, all package/theme
versions were aligned to 1.0.0, and the complete Phase 1–8 suite passes.

The production trees contain no TODO/FIXME markers, bundled secrets, development
tests, remote runtime calls, paid integrations, or production package-manager
dependencies. PHP syntax is clean. Input limits, REST nonces, scoped rate limits,
capability/nonces for administration, prepared database operations, safe DOM text
rendering, output escaping, rule-regex validation, aggregate-only statistics,
and explicit no-message-storage regressions are covered by the suite.

Intentional extension interfaces (rule types/categories, content-version upgrades,
display controls, aggregate trends, empty ad targets, SEO-plugin handoff, and
optional integrations) remain. No dead production file or broken production
reference was identified. Public assets are local and scoped; admin assets load
only on plugin screens. Enabled rules are cached and invalidated on mutation.
Aggregate writes use a compact indexed daily table, and search uses bounded,
page-only WordPress queries.

## SEO, content, and legal review

The release has unique curated titles and descriptions, one visible H1 supplied
by templates, hierarchical H2/H3 article bodies, breadcrumbs, curated internal
links, honest Article/Breadcrumb structured data, baseline Open Graph metadata,
WordPress canonical handling, native sitemap integration, and noindex handling
for search/404 pages. It creates no mass-generated pages or review/rating schema.

No fake address, team member, partnership, certification, statistic, testimonial,
or advertisement is shipped. Fictional scam examples are identified as such.
The checker consistently describes indicators and does not claim to prove fraud
or provide legal/professional advice. The Contact page deliberately invents no
contact method.

Before launch, the owner must personally verify all Privacy Policy, Terms of Use,
Disclaimer, About, and Contact text; add only accurate operator/contact details;
and obtain jurisdiction-specific professional review where appropriate. If
analytics or advertising is added later, disclosures/consent must be reviewed
again. This project review is a product/content check, not legal advice.

## Privacy and runtime guarantees

Pasted messages are analyzed in request memory, unset after analysis, and are not
inserted into WordPress content, options, transients, or custom tables. Optional
statistics are disabled by default and store only daily aggregate counters with
bounded keys. A one-use token supports optional aggregate follow-up selections;
it contains no pasted message. Hosting, proxy, firewall, or security-plugin logs
are outside the plugin and must be configured not to retain request bodies.

Normal use requires no Composer, Node.js, npm, Redis, daemon, worker, SSH, shell,
external database, external/paid API, visitor account, or paid WordPress plugin.
WordPress and its ordinary MySQL/MariaDB database are the only platform runtime.

## Release artifacts

`tools/build-release.sh` copies only the two production source trees into a
temporary staging directory, creates reproducible-layout WordPress ZIPs, and
writes SHA-256 hashes. `tools/test-phase8.sh` verifies archive integrity, expected
single top-level folders and entry files, excluded development paths, checksums,
installation contracts, risk examples, privacy/security boundaries, metadata,
search, sitemap/robots, upgrade, and uninstall behavior.

The `release` directory contains only:

- `job-scam-checker-1.0.0.zip`
- `job-scam-checker-theme-1.0.0.zip`
- `SHA256SUMS`
- `RELEASE-NOTES.md`

Follow `docs/deployment-hostinger.md` for manual installation and rollback, and
complete `docs/release-checklist.md` before making the site public.

## Known limitations

- Risk analysis is deterministic pattern matching, not proof of fraud; novel,
  obfuscated, multilingual, image-only, or context-dependent scams may be missed,
  and legitimate text can contain warning phrases.
- The transient rate limiter is appropriate for ordinary shared hosting but is
  not a distributed WAF. Use the host's free security controls during an attack.
- The suite is dependency-free and extensive but does not replace final testing
  on the owner's exact Hostinger WordPress/PHP/database/cache configuration.
- No contact endpoint, analytics, consent platform, Search Console verification,
  advertisement, or SEO-plugin integration is configured automatically.
- Social share images are not included, so Open Graph provides text/URL metadata
  but no site-specific `og:image` until the owner supplies an appropriate image.
- WordPress uninstall preserves generated/edited pages intentionally to avoid
  destroying owner content; the owner may remove them manually afterward.
