# Hostinger deployment

Phase 1 can run on an ordinary Hostinger WordPress plan without a VPS or another
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

The plugin activation routine creates `/home/` and `/job-scam-checker/` only when
those slugs do not already exist. Existing pages are preserved.

## Checker behavior

Pressing **CHECK NOW** sends the message only to the site’s own WordPress REST
endpoint. The PHP rule engine returns a score, level, warning signs, explanations,
and suspicious domain findings. The submitted message is not stored.

No production Node.js process, Composer packages, cron worker, external API,
Redis instance, separate database, visitor account, or paid service is needed.
