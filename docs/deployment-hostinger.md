# Manual Hostinger deployment guide

This guide installs Job Scam Checker MVP 1.0.0 without SSH, Composer, Node.js,
paid plugins, or an automatic deployment. Button names in hPanel can change
slightly; Hostinger's WordPress installer and WordPress Admin are sufficient.

## Before installation

1. In Hostinger hPanel, open **Websites**, choose the domain, and use **Add
   Website** or the WordPress auto-installer. Select WordPress, set a strong
   administrator password, choose the correct domain, and finish setup. Do not
   enable optional paid extras merely for this project.
2. In hPanel's PHP configuration, use PHP 8.2 when available. The supported
   minimum is PHP 7.4 and WordPress 6.4. Keep `memory_limit` at 128 MB or more,
   `post_max_size` and `upload_max_filesize` above 10 MB, and leave dangerous
   display of production errors off. The release ZIPs are much smaller than
   these limits.
3. Update WordPress from **Dashboard → Updates**. Remove unused sample plugins
   or themes only if you are sure they are not needed.
4. Before changing an existing site, create a Hostinger backup in hPanel's
   **Backups** area (files and database). If the plan does not provide an
   on-demand backup, download both through Hostinger's available backup tools.
   Record the backup date. Do not continue until a restorable backup exists.
5. In hPanel, enable the free SSL certificate for the domain. Open the site at
   `https://` and confirm the browser shows no certificate warning. In WordPress
   **Settings → General**, both site URLs should use `https://`.

## Install the release ZIPs

1. Sign in at `https://YOUR-DOMAIN/wp-admin/`.
2. Go to **Plugins → Add New → Upload Plugin**. Choose
   `job-scam-checker-1.0.0.zip` from this repository's `release` directory,
   select **Install Now**, then **Activate Plugin**.
3. Activation creates two local database tables, seeds the default rule library,
   creates the 30 curated Home/tool/article/guide/trust/legal pages when their
   paths do not already exist, selects the Home page as the static homepage,
   sets safe defaults, and schedules daily aggregate-retention cleanup.
   Existing matching pages are preserved. Anonymous statistics are off by
   default, and pasted messages are never permanently stored.
4. Go to **Appearance → Themes → Add New → Upload Theme**. Choose
   `job-scam-checker-theme-1.0.0.zip`, select **Install Now**, then **Activate**.
5. If WordPress reports that a destination folder already exists during an
   upgrade, use WordPress's replace-current-version option when offered. Back up
   first. Do not unzip one package inside the other.

## Required WordPress settings

1. Open **Settings → Permalinks**, select **Post name** (recommended), and click
   **Save Changes**, even if already selected.
2. Open **Settings → Reading**. Confirm **A static page** is selected and the
   Homepage is **Home**. Leave search engine visibility discouraged only while
   staging; clear that checkbox when the public site is ready.
3. Open **Settings → General** and confirm site title, tagline, administrator
   email, timezone, language, and HTTPS URLs are accurate.
4. Optionally assign Primary and Footer menus under **Appearance → Menus** (or
   the navigation screen provided by WordPress). The theme includes fallbacks.
5. Open every generated page, especially Contact, Privacy Policy, Terms of Use,
   and Disclaimer. Add only truthful owner/contact details. Have the legal text
   reviewed for the operator's location and practices before launch.
6. Open **Job Scam Checker → Settings**. Confirm checker and desired display
   controls are enabled. Anonymous aggregate statistics are disabled by default;
   enable them only after deciding they match the published privacy notice.
   Choose 30–730 days of aggregate retention if enabled.
7. Open **Job Scam Checker → Rules** and confirm the default rules appear. Built-in
   rules can be edited or disabled but not deleted. Test custom changes on staging.

## HTTPS, caching, and privacy

- Use Hostinger's free SSL and force HTTPS through the hosting controls. Check
  several pages for mixed-content browser warnings.
- Hostinger's normal page caching is compatible. Exclude REST POST requests under
  `/wp-json/job-scam-checker/v1/` from any extra cache/CDN rule. Do not cache
  checker POST responses. Clear page cache after activation or content changes.
- No Redis, external database, server daemon, paid API, production Composer,
  production Node.js, or manual cron/SSH task is required. WordPress pseudo-cron
  handles the small daily cleanup.
- Ensure hosting/security logs do not retain REST request bodies. Logs are outside
  the plugin and must match the site's privacy policy.

## Confirm the site works

1. Visit the homepage in a private browser window and on a phone. Confirm layout,
   menu, checker, footer, and HTTPS work.
2. Paste this legitimate sample and select **CHECK NOW**:

   > Thank you for applying through our official careers page. We would like to
   > schedule a video interview with the hiring manager next Tuesday.

   Expect **Low Risk Indicators**. This does not prove legitimacy; independently
   verify every offer.
3. Reset the checker, then paste this fictional scam sample:

   > Contact our hiring manager on Telegram. Deposit USDT to unlock product tasks,
   > recharge your account to continue, and act now because places are limited.

   Expect strong/high or very-high warning indicators, including payment/task
   pressure. Never send money based on this test.
4. Search for `fake check`, try a nonsense query to see the no-results path, and
   open a nonexistent URL to verify the helpful 404 page.
5. In **Job Scam Checker**, review Overview, Statistics, Rules, Settings, Privacy,
   and Content. Test rule editing/duplication and restore the desired setting.
6. View page source for an article and confirm its description, Open Graph tags,
   and Article/Breadcrumb JSON-LD. Confirm headings and breadcrumbs look correct.
7. Open `https://YOUR-DOMAIN/wp-sitemap.xml` and
   `https://YOUR-DOMAIN/robots.txt`. The sitemap must load, and robots.txt should
   advertise it. Search and 404 pages should not be indexed.

## Optional services later

- **Search Console:** after launch, add the domain or URL-prefix property in
  Google Search Console, complete Google's ownership verification using a DNS or
  supported HTML method, and submit `/wp-sitemap.xml`. It is not required to run
  the site.
- **Analytics:** only after choosing a privacy-respecting configuration and
  updating the privacy/cookie disclosures, add the owner's chosen analytics
  integration. The MVP includes no analytics tracker.
- **AdSense:** apply directly to AdSense only after the site has original public
  content and the legal/contact pages are final. After approval, add code using
  Google's current instructions and update consent/privacy disclosures. Existing
  ad slots are intentionally empty and hidden; the release shows no fake ads.

## Rollback

If the plugin causes a problem, deactivate **Job Scam Checker** under Plugins.
If wp-admin is unavailable, use Hostinger File Manager to rename
`wp-content/plugins/job-scam-checker` to `job-scam-checker-disabled`; this is a
reversible emergency action. Deactivation preserves tables, settings, pages, and
aggregates. Do not select Delete unless intentional: uninstall removes plugin
tables/settings but deliberately preserves edited pages.

If the theme causes a problem, activate a known working default theme in
**Appearance → Themes**. If wp-admin is unavailable, use Hostinger support/File
Manager to rename `wp-content/themes/job-scam-checker-theme`, allowing WordPress
to fall back to another installed theme. Then clear caches. Restore the recorded
Hostinger files-and-database backup if deactivation does not resolve the issue.
