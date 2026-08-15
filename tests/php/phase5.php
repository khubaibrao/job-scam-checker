<?php
/** Phase 5 anonymous aggregate, trend, search, and privacy tests. */
require __DIR__ . '/bootstrap.php';
$tests = 0; $failed = 0;
function jsc_phase5_test( $description, $condition, $detail = '' ) { global $tests, $failed; ++$tests; if ( $condition ) { echo "PASS: {$description}\n"; return; } ++$failed; echo "FAIL: {$description}" . ( $detail ? " ({$detail})" : '' ) . "\n"; }

class JSC_Phase5_DB {
    public $prefix = 'wp_'; public $queries = array(); public $stats = array();
    public function prepare( $query, $args = array() ) {
        if ( ! is_array( $args ) ) { $args = array_slice( func_get_args(), 1 ); }
        foreach ( $args as $arg ) { $query = preg_replace( '/%[sd]/', "'" . str_replace( "'", "''", (string) $arg ) . "'", $query, 1 ); }
        return $query;
    }
    public function query( $sql ) {
        $this->queries[] = $sql;
        if ( preg_match_all( "/\('([0-9-]+)', '([a-z_]+)', '([a-z0-9_-]+)', 1\)/", $sql, $matches, PREG_SET_ORDER ) ) { foreach ( $matches as $match ) { $key = $match[1] . '|' . $match[2] . '|' . $match[3]; $this->stats[ $key ] = ( $this->stats[ $key ] ?? 0 ) + 1; } }
        return 1;
    }
    public function get_results( $sql ) {
        preg_match( "/metric = '([^']+)' AND stat_date BETWEEN '([^']+)' AND '([^']+)'/", $sql, $match ); $out = array();
        foreach ( $this->stats as $combined => $count ) { list( $date, $metric, $key ) = explode( '|', $combined ); if ( $match && $metric === $match[1] && $date >= $match[2] && $date <= $match[3] ) { $out[ $key ] = ( $out[ $key ] ?? 0 ) + $count; } }
        $rows = array(); foreach ( $out as $key => $count ) { $rows[] = array( 'stat_key' => $key, 'total' => $count ); } return $rows;
    }
}

$GLOBALS['wpdb'] = new JSC_Phase5_DB();
$service = new JSC_Statistics();
$GLOBALS['jsc_test_options']['jsc_statistics_enabled'] = '0';
$token = $service->record_analysis( array( 'level' => array( 'key' => 'high' ), 'detections' => array( array( 'slug' => 'task-deposit' ) ), 'message' => 'PRIVATE MESSAGE' ) );
jsc_phase5_test( 'anonymous statistics disabled produces no writes', '' === $token && empty( $GLOBALS['wpdb']->queries ) );

$GLOBALS['jsc_test_options']['jsc_statistics_enabled'] = '1'; $GLOBALS['jsc_test_options']['jsc_follow_up_enabled'] = '1';
$token = $service->record_analysis( array( 'level' => array( 'key' => 'high' ), 'detections' => array( array( 'slug' => 'task-deposit' ), array( 'slug' => 'cryptocurrency-request' ) ), 'message' => 'PRIVATE MESSAGE' ) );
$stored = json_encode( array( $GLOBALS['wpdb']->queries, $GLOBALS['jsc_test_transients'] ) );
jsc_phase5_test( 'statistics enabled records total checks', isset( $GLOBALS['wpdb']->stats['2026-08-15|checks|total'] ) );
jsc_phase5_test( 'risk-level aggregation records high risk', isset( $GLOBALS['wpdb']->stats['2026-08-15|risk_level|high'] ) );
jsc_phase5_test( 'rule detection aggregation records safe slugs', isset( $GLOBALS['wpdb']->stats['2026-08-15|detection|task-deposit'] ) && isset( $GLOBALS['wpdb']->stats['2026-08-15|detection|cryptocurrency-request'] ) );
jsc_phase5_test( 'aggregate storage never includes pasted message', false === strpos( $stored, 'PRIVATE MESSAGE' ) );

$follow = $service->record_follow_up( $token, 'telegram', 'yes', 'task_deposit' );
jsc_phase5_test( 'valid follow-up is accepted', true === $follow );
jsc_phase5_test( 'channel aggregation records Telegram', isset( $GLOBALS['wpdb']->stats['2026-08-15|channel|telegram'] ) );
jsc_phase5_test( 'money-request aggregation records yes', isset( $GLOBALS['wpdb']->stats['2026-08-15|money_request|yes'] ) );
jsc_phase5_test( 'payment-purpose aggregation records task deposit', isset( $GLOBALS['wpdb']->stats['2026-08-15|payment_purpose|task_deposit'] ) );
$duplicate = $service->record_follow_up( $token, 'telegram', 'yes', 'task_deposit' );
jsc_phase5_test( 'one-use token prevents duplicate manipulation', $duplicate instanceof WP_Error && 'jsc_feedback_duplicate' === $duplicate->code );
$invalid_token = str_repeat( 'i', 32 ); set_transient( 'jsc_feedback_' . hash( 'sha256', $invalid_token ), 1 );
$invalid = $service->record_follow_up( $invalid_token, 'carrier_pigeon', 'yes', 'cash' );
jsc_phase5_test( 'invalid follow-up selections are rejected', $invalid instanceof WP_Error && 'jsc_invalid_feedback' === $invalid->code );
jsc_phase5_test( 'invalid submission does not consume token', false !== get_transient( 'jsc_feedback_' . hash( 'sha256', $invalid_token ) ) );

class JSC_Phase5_Trend_Repository { public $data; public function __construct( $data ) { $this->data = $data; } public function counts( $metric, $start, $end ) { $period = $start >= '2026-08-02' ? 'current' : 'prior'; return $this->data[ $period ][ $metric ] ?? array(); } }
$empty = new JSC_Trend_Provider( new JSC_Phase5_Trend_Repository( array() ) );
jsc_phase5_test( 'trend provider returns honest empty state without samples', array() === $empty->get_trends( '2026-08-15' ) );
$trend_data = array( 'current' => array( 'checks' => array( 'total' => 20 ), 'channel' => array( 'telegram' => 8 ) ), 'prior' => array( 'checks' => array( 'total' => 20 ), 'channel' => array( 'telegram' => 3 ) ) );
$trends = ( new JSC_Trend_Provider( new JSC_Phase5_Trend_Repository( $trend_data ) ) )->get_trends( '2026-08-15' );
jsc_phase5_test( 'trend displays only with sufficient real comparison data', 1 === count( $trends ) && 'Telegram job offers' === $trends[0]['label'] );
$public_source = file_get_contents( JSC_PLUGIN_DIR . 'public/class-jsc-public.php' );
jsc_phase5_test( 'trend UI contains required honest empty-state copy', false !== strpos( $public_source, 'Not enough real data yet to show a trend.' ) );

$settings = new JSC_Settings();
jsc_phase5_test( 'privacy checkbox sanitization fails closed', '1' === $settings->checkbox( '1' ) && '0' === $settings->checkbox( 'yes' ) );
jsc_phase5_test( 'retention setting is bounded', 30 === $settings->retention( 1 ) && 730 === $settings->retention( 5000 ) );
$search_source = file_get_contents( dirname( __DIR__, 2 ) . '/wordpress/themes/job-scam-checker-theme/inc/search.php' );
$search_template = file_get_contents( dirname( __DIR__, 2 ) . '/wordpress/themes/job-scam-checker-theme/search.php' );
jsc_phase5_test( 'native search is restricted to curated pages', false !== strpos( $search_source, "'post_type', 'page'" ) );
jsc_phase5_test( 'search results use meaningful SEO excerpts', false !== strpos( $search_source, '_jsc_seo_description' ) );
jsc_phase5_test( 'search supports sanitized content filtering', false !== strpos( $search_source, "'scam_article'" ) && false !== strpos( $search_source, "'guide'" ) );
jsc_phase5_test( 'search template includes typed results', false !== strpos( $search_template, 'jsc_theme_content_type_label' ) );
jsc_phase5_test( 'no-results search guides visitors to checker', false !== strpos( $search_template, 'No matching pages found' ) && false !== strpos( $search_template, '/job-scam-checker/' ) );
$privacy = JSC_Content_Installer::get_page_definitions()['privacy'];
jsc_phase5_test( 'privacy page describes exact anonymous aggregates', false !== strpos( $privacy['content'], 'risk levels and detected warning-rule identifiers' ) && false !== strpos( $privacy['content'], 'no pasted text' ) );
jsc_phase5_test( 'unchanged Phase 4 privacy page has safe upgrade path', ! empty( $privacy['upgrade_from'] ) );
$schema_source = file_get_contents( JSC_PLUGIN_DIR . 'includes/class-jsc-schema.php' );
jsc_phase5_test( 'schema uses one aggregate row per date metric and key', false !== strpos( $schema_source, 'PRIMARY KEY  (stat_date, metric, stat_key)' ) );
$js = file_get_contents( JSC_PLUGIN_DIR . 'assets/js/checker.js' );
jsc_phase5_test( 'follow-up sends selections and token but never message text', false !== strpos( $js, 'payment_purpose: purpose.value' ) && 1 === substr_count( $js, 'message: textarea.value' ) );

echo "\n{$tests} tests, {$failed} failures.\n"; exit( $failed > 0 ? 1 : 0 );
