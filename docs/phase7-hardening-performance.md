# Phase 7 hardening and performance

Phase 7 prepares the Phase 1–6 application for a normal public WordPress launch
without changing its local rule-based model or requiring infrastructure beyond
shared PHP/MySQL hosting.

## Security and abuse boundaries

- Both checker REST routes use an explicit permission callback that verifies the
  short-lived, same-origin WordPress nonce. Callbacks verify it again as defense
  in depth.
- Requests over 24,000 bytes or 12,000 characters are rejected. The engine strips
  tags and the browser creates result nodes with `textContent`; pasted markup is
  never inserted into the DOM as HTML.
- Suspicious links are never clickable or returned with paths, queries or
  fragments. Results expose only a validated hostname and safe reason labels.
- Checker requests remain limited to 10 per minute per salted one-way network
  identifier. Follow-ups have a separate 30-per-minute scope. Raw addresses and
  message text are not stored in limiter state.
- Administrator pages require `manage_options`; mutations use action-specific
  nonces, validated identifiers, prepared database operations and safe redirects.
- Regex rules remain length/syntax bounded and now reject recursion,
  backreferences and nested quantified groups associated with excessive work.
- Native search stays page-only with allow-listed filters, a 100-byte term cap
  and 12 results per page.

The transient limiter is deliberately modest and does not replace a host WAF.
Concurrent transient updates may occasionally permit a small burst; hard request
limits and validation still apply.

## Privacy and retention

The pasted message exists only in the HTTPS request, local PHP memory during
analysis and the visitor's browser. It is unset after analysis and is not sent to
an external API, inserted into content, written to plugin tables, placed in URLs,
indexed or intentionally logged. Browser autocomplete and spellcheck are disabled
on the field. Operators must still prevent host/security tools from logging bodies.

Statistics remain disabled by default. When disabled, analysis creates no rows
or follow-up token. When enabled, storage contains only UTC date, allow-listed
metric/key and count. Follow-up tokens are random, hashed in transient keys,
one-use and expire after 15 minutes. Aggregate rows are cleaned daily after the
configured 30–730 days. Follow-ups can be disabled independently.

## Accessibility

The audit retained explicit labels, fieldset/legend grouping, semantic progress,
polite atomic announcements, alert errors, result focus, keyboard controls,
meaningful text and heading structure. Risk includes text and a numeric score,
not color alone. Focus remains visible and scrolling honors reduced motion.

Automated assertions are regression checks, not WCAG certification. Perform
keyboard, screen-reader, zoom/reflow and contrast checks on staging.

## Shared-hosting performance

- Enabled rules are cached for five minutes and invalidated after every mutation.
- Statistics adds `metric_key (metric, stat_key)` for grouped admin totals;
  existing primary and `metric_date` indexes remain.
- Admin detection labels load rules once per page, avoiding one query per row.
- Checker assets remain shortcode-scoped and admin CSS plugin-screen-scoped.
- Existing trend caching, batched writes and bounded pagination remain intact.

`dbDelta` applies database version 3.1.0, adding one non-unique index without
removing or rewriting data.

## SEO and compatibility

Phase 7 does not alter titles, canonical WordPress URLs, metadata, structured
data, breadcrumbs, sitemap, robots directives, links or content inventory. The
project remains compatible with WordPress 6.4+, PHP 7.4+ and ordinary Hostinger
hosting, without Node.js production services, Redis, daemons, workers, a VPS,
CDN or paid service.

## Operational recommendations

These controls belong to WordPress/Hostinger and are not forced by the plugin:

- use supported WordPress/PHP, automatic security updates and HTTPS;
- test backups, use unique admin accounts, strong passwords and 2FA if available;
- protect `wp-config.php`, set `DISALLOW_FILE_EDIT`, and disable debug display in
  production while retaining privacy-reviewed monitoring;
- ensure host/security logs do not capture request bodies and set suitable
  retention;
- use Hostinger/WAF bot controls if distributed abuse exceeds the limiter; and
- exclude REST POSTs from page caches and verify WordPress cron cleanup runs.

No security certification or claim of perfect security is made.
