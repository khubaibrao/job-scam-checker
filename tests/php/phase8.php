<?php
/** Final release, installation, compatibility, and packaging contracts. */

require __DIR__ . '/bootstrap.php';

$tests  = 0;
$failed = 0;

function jsc_phase8_test( $description, $condition ) {
    global $tests, $failed;
    ++$tests;
    if ( $condition ) {
        echo "PASS: {$description}\n";
        return;
    }
    ++$failed;
    echo "FAIL: {$description}\n";
}

$root       = dirname( __DIR__, 2 );
$plugin_dir = $root . '/wordpress/plugins/job-scam-checker';
$theme_dir  = $root . '/wordpress/themes/job-scam-checker-theme';
$plugin     = file_get_contents( $plugin_dir . '/job-scam-checker.php' );
$theme      = file_get_contents( $theme_dir . '/style.css' );
$schema     = file_get_contents( $plugin_dir . '/includes/class-jsc-schema.php' );
$activator  = file_get_contents( $plugin_dir . '/includes/class-jsc-activator.php' );
$uninstall  = file_get_contents( $plugin_dir . '/uninstall.php' );
$rest       = file_get_contents( $plugin_dir . '/public/class-jsc-rest-controller.php' );
$metadata   = file_get_contents( $theme_dir . '/inc/metadata.php' );
$structured = file_get_contents( $theme_dir . '/inc/structured-data.php' );
$search     = file_get_contents( $theme_dir . '/inc/search.php' );

jsc_phase8_test( 'plugin release version is 1.0.0', false !== strpos( $plugin, 'Version:           1.0.0' ) && JSC_VERSION === '1.0.0' );
jsc_phase8_test( 'theme release version is 1.0.0', false !== strpos( $theme, 'Version: 1.0.0' ) );
jsc_phase8_test( 'plugin declares WordPress 6.4 minimum', false !== strpos( $plugin, 'Requires at least: 6.4' ) );
jsc_phase8_test( 'plugin declares PHP 7.4 minimum', false !== strpos( $plugin, 'Requires PHP:      7.4' ) );
jsc_phase8_test( 'theme declares matching platform minimums', false !== strpos( $theme, 'Requires at least: 6.4' ) && false !== strpos( $theme, 'Requires PHP: 7.4' ) );
jsc_phase8_test( 'release headers contain no placeholder URL', false === strpos( $plugin . $theme, 'example.com' ) );
jsc_phase8_test( 'activation hook uses the dedicated activator', false !== strpos( $plugin, "register_activation_hook( __FILE__, array( 'JSC_Activator', 'activate' ) )" ) );
jsc_phase8_test( 'activation installs schema before content', strpos( $activator, 'JSC_Schema::install()' ) < strpos( $activator, 'JSC_Content_Installer::install()' ) );
jsc_phase8_test( 'fresh install disables anonymous statistics', false !== strpos( $activator, "update_option( 'jsc_statistics_enabled', '0'" ) );
jsc_phase8_test( 'fresh install enables the checker', false !== strpos( $activator, "update_option( 'jsc_checker_enabled', '1'" ) );
jsc_phase8_test( 'activation schedules aggregate cleanup', false !== strpos( $activator, "wp_schedule_event" ) && false !== strpos( $activator, "jsc_daily_cleanup" ) );
jsc_phase8_test( 'rules schema has a unique stable slug', false !== strpos( $schema, 'UNIQUE KEY slug (slug)' ) );
jsc_phase8_test( 'statistics schema stores aggregate dimensions only', false !== strpos( $schema, 'stat_date date' ) && false === strpos( $schema, 'message text' ) );
jsc_phase8_test( 'schema records its independent version', false !== strpos( $schema, "update_option( 'jsc_db_version', JSC_DB_VERSION" ) );
jsc_phase8_test( 'upgrade path compares the schema version', false !== strpos( $schema, "JSC_DB_VERSION !== get_option( 'jsc_db_version' )" ) );
jsc_phase8_test( 'default rule seeding preserves administrator edits', false !== strpos( $schema, 'seed_missing' ) );

$GLOBALS['jsc_test_pages'] = $GLOBALS['jsc_test_options'] = $GLOBALS['jsc_test_post_meta'] = array();
$first = JSC_Content_Installer::install();
jsc_phase8_test( 'fresh content install creates all release pages', 30 === count( $first ) );
jsc_phase8_test( 'fresh content install selects a static homepage', 'page' === get_option( 'show_on_front' ) && ! empty( get_option( 'page_on_front' ) ) );
jsc_phase8_test( 'content installer records its version', JSC_CONTENT_VERSION === get_option( 'jsc_content_version' ) );
jsc_phase8_test( 'repeat content installation creates no duplicates', $first === JSC_Content_Installer::install() && 30 === count( $GLOBALS['jsc_test_pages'] ) );

$rules   = require $plugin_dir . '/data/default-rules.php';
$engine  = new JSC_Rule_Engine();
$low     = $engine->analyze( 'Thank you for applying. We would like to arrange a video interview with the hiring manager next week.', $rules );
$medium  = $engine->analyze( 'Work as a package inspector from home. Receive packages, inspect them, repackage and reship using labels we provide. You are hired without an interview.', $rules );
$high    = $engine->analyze( 'WhatsApp recruitment. No interview required. Pay the registration fee with crypto today.', $rules );
$highest = $engine->analyze( 'Contact our hiring manager on Telegram. Complete product optimization tasks. Deposit USDT to unlock tasks and recharge your account to continue. Act now!', $rules );
$link    = $engine->analyze( 'Apply immediately through https://bit.ly/job-offer before the limited slots close.', $rules );
jsc_phase8_test( 'end-to-end legitimate message is low risk', 'low' === $low['level']['key'] );
jsc_phase8_test( 'end-to-end medium message is caution risk', 'caution' === $medium['level']['key'] );
jsc_phase8_test( 'end-to-end high-risk message is high risk', 'high' === $high['level']['key'] );
jsc_phase8_test( 'end-to-end very-high-risk message is very high risk', 'very_high' === $highest['level']['key'] );
jsc_phase8_test( 'suspicious shortened link is reported safely', ! empty( $link['suspicious_links'] ) && false === strpos( wp_json_encode( $link['suspicious_links'] ), '/job-offer' ) );
jsc_phase8_test( 'result never returns the submitted message', false === strpos( wp_json_encode( $highest ), 'Deposit USDT' ) );
jsc_phase8_test( 'REST input requires a string', false !== strpos( $rest, "! is_string( \$message )" ) );
jsc_phase8_test( 'REST input enforces character and byte limits', false !== strpos( $rest, 'MAX_CHARACTERS = 12000' ) && false !== strpos( $rest, 'MAX_BYTES      = 24000' ) );
jsc_phase8_test( 'REST routes require nonce permission callbacks', 2 === substr_count( $rest, "'permission_callback' => array( \$this, 'permission_check' )" ) );
jsc_phase8_test( 'REST responses explicitly prevent caching', 2 === substr_count( $rest, 'nocache_headers()' ) );
jsc_phase8_test( 'search is restricted to pages', false !== strpos( $search, "set( 'post_type', 'page' )" ) );
jsc_phase8_test( 'search input and filters are bounded', false !== strpos( $search, 'strlen( $term ) > 100' ) && false !== strpos( $search, 'sanitize_key' ) );
jsc_phase8_test( 'search and 404 pages are noindex', false !== strpos( $metadata, 'is_search() || is_404()' ) && false !== strpos( $metadata, "\$robots['noindex'] = true" ) );
jsc_phase8_test( 'robots output advertises the WordPress sitemap', false !== strpos( $metadata, "home_url( '/wp-sitemap.xml' )" ) );
jsc_phase8_test( 'metadata contains description and Open Graph output', false !== strpos( $metadata, 'name="description"' ) && 5 <= substr_count( $metadata, 'property="og:' ) );
jsc_phase8_test( 'WordPress retains canonical URL ownership', false !== strpos( $metadata, 'WordPress owns titles and canonical URLs' ) );
jsc_phase8_test( 'structured data is limited to honest editorial types', false !== strpos( $structured, "'@type'            => 'Article'" ) && false !== strpos( $structured, "'@type' => 'BreadcrumbList'" ) && false === strpos( $structured, 'Review' ) );
jsc_phase8_test( 'uninstall requires the WordPress uninstall context', false !== strpos( $uninstall, "defined( 'WP_UNINSTALL_PLUGIN' )" ) );
jsc_phase8_test( 'uninstall removes plugin tables and scheduled cleanup', 2 === substr_count( $uninstall, 'DROP TABLE IF EXISTS' ) && false !== strpos( $uninstall, 'wp_clear_scheduled_hook' ) );
jsc_phase8_test( 'uninstall deliberately preserves edited pages', false === strpos( $uninstall, 'wp_delete_post' ) );

$production = '';
foreach ( new RecursiveIteratorIterator( new RecursiveDirectoryIterator( $root . '/wordpress', FilesystemIterator::SKIP_DOTS ) ) as $file ) {
    if ( $file->isFile() ) { $production .= file_get_contents( $file->getPathname() ); }
}
jsc_phase8_test( 'production source has no TODO or FIXME markers', ! preg_match( '/\b(?:TODO|FIXME)\b/i', $production ) );
jsc_phase8_test( 'production source has no paid or external API dependency', ! preg_match( '/(?:api[_-]?key|\bstripe\b|\bopenai\b|wp_remote_(?:get|post)|curl_exec)/i', $production ) );
jsc_phase8_test( 'production source has no shell execution dependency', ! preg_match( '/\b(?:shell_exec|passthru|proc_open|popen)\s*\(/', $production ) );
jsc_phase8_test( 'production source contains no obvious secret material', ! preg_match( '/-----BEGIN (?:RSA |EC |OPENSSH )?PRIVATE KEY-----|sk-[A-Za-z0-9]{20,}/', $production ) );

echo "\n{$tests} tests, {$failed} failures.\n";
exit( $failed > 0 ? 1 : 0 );
