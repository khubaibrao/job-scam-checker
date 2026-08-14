<?php
/**
 * Phase 1 test runner. Run with: php tests/php/run.php
 */

require __DIR__ . '/bootstrap.php';

$tests  = 0;
$failed = 0;

function jsc_test( $description, $condition ) {
    global $tests, $failed;
    ++$tests;

    if ( $condition ) {
        echo "PASS: {$description}\n";
        return;
    }

    ++$failed;
    echo "FAIL: {$description}\n";
}

$definitions = JSC_Content_Installer::get_page_definitions();
jsc_test( 'installer defines the homepage', isset( $definitions['home'] ) );
jsc_test( 'installer defines the checker page', isset( $definitions['job_scam_checker'] ) );
jsc_test( 'checker page uses the public shortcode', false !== strpos( $definitions['job_scam_checker']['content'], '[job_scam_checker]' ) );

$first_install = JSC_Content_Installer::install();
jsc_test( 'installer creates two Phase 1 pages', 2 === count( $GLOBALS['jsc_test_pages'] ) );
jsc_test( 'installer assigns a static homepage', 'page' === $GLOBALS['jsc_test_options']['show_on_front'] );
jsc_test( 'installer records homepage ID', $first_install['home'] === $GLOBALS['jsc_test_options']['page_on_front'] );

$second_install = JSC_Content_Installer::install();
jsc_test( 'installer is idempotent', 2 === count( $GLOBALS['jsc_test_pages'] ) );
jsc_test( 'existing page IDs remain stable', $first_install === $second_install );

$public = new JSC_Public();
$public->register_hooks();
jsc_test( 'public integration registers shortcode', isset( $GLOBALS['jsc_test_shortcodes']['job_scam_checker'] ) );

$public->register_assets();
jsc_test( 'checker stylesheet is local', 0 === strpos( $GLOBALS['jsc_test_styles']['jsc-checker'], JSC_PLUGIN_URL ) );
jsc_test( 'checker script is local', 0 === strpos( $GLOBALS['jsc_test_scripts']['jsc-checker'], JSC_PLUGIN_URL ) );

$markup = $public->render_checker();
jsc_test( 'checker has an explicit textarea label', (bool) preg_match( '/<label for="([^"]+-message)"/', $markup ) );
jsc_test( 'checker limits message length', false !== strpos( $markup, 'maxlength="12000"' ) );
jsc_test( 'checker includes the sensitive-data warning', false !== strpos( $markup, 'Avoid pasting passwords' ) );
jsc_test( 'checker submits only through controlled JavaScript', false !== strpos( $markup, 'type="button"' ) );
jsc_test( 'checker CTA matches approved copy', false !== strpos( $markup, 'CHECK NOW' ) );
jsc_test( 'checker exposes a same-origin REST endpoint', false !== strpos( $GLOBALS['jsc_test_localized']['JSCCheckerConfig']['endpoint'], '/wp-json/job-scam-checker/v1/analyze' ) );
jsc_test( 'checker exposes a request nonce', 'valid-test-nonce' === $GLOBALS['jsc_test_localized']['JSCCheckerConfig']['nonce'] );

echo "\n{$tests} tests, {$failed} failures.\n";
exit( $failed > 0 ? 1 : 0 );
