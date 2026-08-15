# Hostinger deployment

The project can run on an ordinary Hostinger WordPress plan without a VPS or another
service.

1. Back up the existing WordPress site and database.
2. Copy `wordpress/plugins/job-scam-checker` to
   `wp-content/plugins/job-scam-checker`.
3. Copy `wordpress/themes/job-scam-checker-theme` to
   `wp-content/themes/job-scam-checker-theme`.
4. In WordPress Admin, activate **Job Scam Checker** under Plugins.
5. Activate **Job Scam Checker** under Appearance > Themes.
6. Visit Settings > Permalinks and save the preferred clean URL structure.
7. Confirm that Home is selected under Settings > Reading.
8. Assign Primary and Footer menus under Appearance > Menus if desired. The
   theme supplies a small fallback primary menu until one is assigned.
9. Review all generated editorial and legal pages in WordPress. Configure an
   accurate contact channel and jurisdiction-appropriate operator details before
   public launch; the project deliberately invents none.
10. Confirm `/wp-sitemap.xml` and `/robots.txt`, then submit the sitemap to Search
    Console only if desired.
11. Review **Job Scam Checker → Settings**. Anonymous statistics are disabled by
    default. Choose whether to enable them and the optional follow-up questions,
    then select aggregate retention from 30 to 730 days.
12. Open **Job Scam Checker → Rules** and confirm the default library is present.
    Shipped rules can be edited or disabled but cannot be deleted. Review the
    Overview, Statistics and Content shortcuts before launch.

The plugin activation routine creates `/home/` and `/job-scam-checker/` only when
those slugs do not already exist. The Phase 4 content release also creates the
curated hubs, articles, guides and trust pages. Existing pages are preserved, and
only exact untouched foundation content is eligible for automatic upgrading.

## Checker behavior

Pressing **CHECK NOW** sends the message only to the site’s own WordPress REST
endpoint. The PHP rule engine returns a score, level, warning signs, explanations,
and suspicious domain findings. The submitted message is not stored.

No production Node.js process, Composer packages, cron worker, external API,
Redis instance, separate database, visitor account, or paid service is needed.
Phase 5 uses normal WordPress pseudo-cron for a small daily retention task and
one indexed aggregate table. It needs no Hostinger cron configuration.

Updating from Phase 5 runs a normal WordPress `dbDelta` migration that adds the
protected-default marker to the existing rules table. It preserves existing
rules, aggregate statistics, options, and content. No manual SQL is required.

Updating to Phase 7 runs `dbDelta` once for database version 3.1.0 and adds the
non-unique `metric_key (metric, stat_key)` statistics index. It does not delete or
rewrite rules, counters, settings or content. Back up first as for any update.

## Recommended production settings

Use supported PHP, force HTTPS, enable WordPress core security updates, and keep
plugins/themes current. Use strong unique admin passwords and 2FA if available.
Consider `define( 'DISALLOW_FILE_EDIT', true );` in `wp-config.php`, and keep
`WP_DEBUG_DISPLAY` off in production.

Ensure Hostinger, proxy and security-plugin logs do not capture REST request
bodies. Set log retention to match the site privacy policy. Confirm caching does
not cache REST POST responses and WordPress cron runs the daily cleanup. For
sustained distributed abuse, use available host WAF/bot controls; the plugin uses
a lightweight transient limiter intended for ordinary traffic.

Before launch, test keyboard use, a screen reader, 200%/400% zoom, mobile reflow,
contrast, checker errors/results, search, metadata, structured data, breadcrumbs,
sitemap and robots behavior on staging. These controls cannot safely be imposed
by the plugin itself.
