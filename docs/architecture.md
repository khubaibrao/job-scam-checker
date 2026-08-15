# Architecture

Job Scam Checker is split into a portable WordPress plugin and a custom theme.

The plugin owns application behavior, installation, checker markup, and—beginning
in Phase 2—the rule engine. The theme owns layout, navigation, typography,
metadata, and editorial templates. Production has no build service or package
manager requirement.

The browser sends checker requests only to a nonce-protected, same-origin
WordPress REST endpoint. Local PHP rules analyze the message in memory. The
message is discarded after the response and is not written to WordPress data,
aggregate statistics, logs, transients, URLs, or browser-generated result markup.

## Compatibility target

- WordPress 6.4 or newer
- PHP 7.4 or newer
- MySQL/MariaDB supplied by WordPress hosting
- Apache or LiteSpeed with normal WordPress permalinks

## Content installation

Plugin activation calls an idempotent page installer. It creates only missing
pages, never overwrites an existing matching slug, stores the resulting IDs, and
assigns the generated Home page as the static front page. Later phases can append
page definitions to this framework.

## Application data

The plugin version, schema version, and generated page IDs are stored in options.
Rule definitions live in the custom WordPress rules table. Rate limits store a
short-lived counter under a salted one-way network identifier. No pasted
messages, analytics cookies, or identifying visitor content are stored by the
custom code.

Phase 5 can optionally store daily counters in `jsc_daily_stats`. Its composite
date/metric/key primary key increments aggregate rows rather than creating
visitor events. Safe keys cover totals, risk levels, detection-rule slugs and
allow-listed follow-up selections. Messages and visitor identifiers never cross
the aggregate repository boundary. Collection is disabled by default.
