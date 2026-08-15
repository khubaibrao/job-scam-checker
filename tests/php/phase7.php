<?php
/** Phase 7 security, privacy, accessibility and performance tests. */
require __DIR__ . '/bootstrap.php';
$tests = 0; $failed = 0;
function jsc_phase7_test( $description, $condition ) { global $tests, $failed; ++$tests; if ( $condition ) { echo "PASS: {$description}\n"; } else { ++$failed; echo "FAIL: {$description}\n"; } }

$rules = require JSC_PLUGIN_DIR . 'data/default-rules.php';
$engine = new JSC_Rule_Engine();
$payload = '<img src=x onerror=alert(1)><script>location="https://evil.test"</script> Send your password.';
$result = $engine->analyze( $payload, $rules );
$encoded = wp_json_encode( $result );
jsc_phase7_test( 'malicious pasted HTML is never returned as executable markup', false === strpos( $encoded, '<script' ) && false === strpos( $encoded, 'onerror=' ) );
jsc_phase7_test( 'pasted XSS is still analyzed as inert text', in_array( 'password-request', array_column( $result['detections'], 'slug' ), true ) );
$url_result = $engine->analyze( 'Open javascript:alert(1) or https://bit.ly/a?next=%22%3E%3Cscript%3E.', $rules );
jsc_phase7_test( 'suspicious URL output exposes a safe domain only', false !== strpos( wp_json_encode( $url_result['suspicious_links'] ), 'bit.ly' ) && false === strpos( wp_json_encode( $url_result['suspicious_links'] ), 'next=' ) );

class JSC_Phase7_Request {
    private $nonce; private $params;
    public function __construct( $nonce, array $params = array() ) { $this->nonce = $nonce; $this->params = $params; }
    public function get_header( $name ) { return 'X-JSC-Nonce' === $name ? $this->nonce : ''; }
    public function get_param( $name ) { return $this->params[ $name ] ?? null; }
}
$controller = new JSC_REST_Controller();
$denied = $controller->permission_check( new JSC_Phase7_Request( 'forged' ) );
jsc_phase7_test( 'REST permission callback rejects CSRF nonce', $denied instanceof WP_Error && 'jsc_invalid_nonce' === $denied->code );
jsc_phase7_test( 'REST permission callback accepts issued nonce', true === $controller->permission_check( new JSC_Phase7_Request( 'valid-test-nonce' ) ) );
$rest_source = file_get_contents( JSC_PLUGIN_DIR . 'public/class-jsc-rest-controller.php' );
jsc_phase7_test( 'both REST routes use explicit permission checks', 2 === substr_count( $rest_source, "'permission_callback' => array( \$this, 'permission_check' )" ) );
$oversized = $controller->analyze( new JSC_Phase7_Request( 'valid-test-nonce', array( 'message' => str_repeat( 'x', JSC_REST_Controller::MAX_BYTES + 1 ) ) ) );
jsc_phase7_test( 'oversized checker input fails before analysis', $oversized instanceof WP_Error && 'jsc_message_too_long' === $oversized->code );

$_SERVER['REMOTE_ADDR'] = '198.51.100.20';
$limiter = new JSC_Rate_Limiter();
for ( $i = 0; $i < 2; ++$i ) { $limiter->consume( 'phase7_test', 2, 60 ); }
jsc_phase7_test( 'scoped limiter blocks configured excess', false === $limiter->consume( 'phase7_test', 2, 60 ) );
jsc_phase7_test( 'separate limiter scopes do not block legitimate checks', true === $limiter->consume( 'phase7_other', 2, 60 ) );
jsc_phase7_test( 'rate-limit state contains neither IP nor message', false === strpos( wp_json_encode( $GLOBALS['jsc_test_transients'] ), '198.51.100.20' ) && false === strpos( wp_json_encode( $GLOBALS['jsc_test_transients'] ), 'Send your password' ) );

$valid = array( 'name'=>'Regex', 'slug'=>'regex', 'match_type'=>'regex', 'pattern'=>'/(a+)+$/', 'category'=>'payment', 'score_group'=>'regex', 'weight'=>'1', 'priority'=>'1', 'explanation'=>'Safe.', 'recommendation'=>'Verify.' );
$validator = new JSC_Rule_Validator();
jsc_phase7_test( 'nested-quantifier regex configuration is rejected', is_wp_error( $validator->validate( $valid ) ) );
jsc_phase7_test( 'backreference regex configuration is rejected', is_wp_error( $validator->validate( array_merge( $valid, array( 'pattern'=>'/(a)\\1/' ) ) ) ) );

$GLOBALS['jsc_test_options']['jsc_statistics_enabled'] = '0';
class JSC_Phase7_DB { public $prefix = 'wp_'; public $queries = array(); public function query( $q ) { $this->queries[]=$q; return 1; } public function prepare( $q ) { return $q; } }
$GLOBALS['wpdb'] = new JSC_Phase7_DB();
$private = 'PRIVATE-PHASE7-MESSAGE';
$token = ( new JSC_Statistics() )->record_analysis( array( 'message'=>$private, 'level'=>array( 'key'=>'high' ), 'detections'=>array() ) );
jsc_phase7_test( 'disabled statistics produces no token or database write', '' === $token && empty( $GLOBALS['wpdb']->queries ) );
jsc_phase7_test( 'statistics and transient state retain no pasted message', false === strpos( wp_json_encode( array( $GLOBALS['wpdb']->queries, $GLOBALS['jsc_test_transients'] ) ), $private ) );

$markup = ( new JSC_Public() )->render_checker();
jsc_phase7_test( 'checker has labels, live result status and alert semantics', false !== strpos( $markup, '<label for=') && false !== strpos( $markup, 'aria-live="polite"') && false !== strpos( $markup, 'role="alert"') );
jsc_phase7_test( 'checker disables browser text retention helpers', false !== strpos( $markup, 'autocomplete="off"') && false !== strpos( $markup, 'spellcheck="false"') );
$js = file_get_contents( JSC_PLUGIN_DIR . 'assets/js/checker.js' );
jsc_phase7_test( 'all dynamic visitor-facing output uses DOM text APIs', false === strpos( $js, 'innerHTML' ) && false !== strpos( $js, 'textContent' ) );
$admin = file_get_contents( JSC_PLUGIN_DIR . 'admin/class-jsc-admin.php' );
jsc_phase7_test( 'admin views and mutations enforce capability checks', false !== strpos( $admin, "const CAPABILITY = 'manage_options'") && substr_count( $admin, 'current_user_can') >= 2 );
jsc_phase7_test( 'admin mutations enforce WordPress nonces and safe redirects', false !== strpos( $admin, 'check_admin_referer') && false === strpos( $admin, 'wp_redirect(') && false !== strpos( $admin, 'wp_safe_redirect') );
jsc_phase7_test( 'admin assets remain scoped to plugin screens', false !== strpos( $admin, "strpos( (string) \$hook, 'jsc-' )") );
$search = file_get_contents( dirname( __DIR__, 2 ) . '/wordpress/themes/job-scam-checker-theme/inc/search.php' );
jsc_phase7_test( 'public search is page-only, allow-listed and length bounded', false !== strpos( $search, "'post_type', 'page'") && false !== strpos( $search, 'strlen( $term ) > 100') && false !== strpos( $search, 'in_array( $filter, $allowed, true )') );
$schema = file_get_contents( JSC_PLUGIN_DIR . 'includes/class-jsc-schema.php' );
jsc_phase7_test( 'schema supports checker and aggregate query paths with indexes', false !== strpos( $schema, 'KEY enabled_priority') && false !== strpos( $schema, 'KEY metric_date') && false !== strpos( $schema, 'KEY metric_key') );
$repo = file_get_contents( JSC_PLUGIN_DIR . 'includes/class-jsc-rule-repository.php' );
jsc_phase7_test( 'enabled rules are cached and invalidated after mutations', false !== strpos( $repo, 'get_transient( self::CACHE_KEY )') && substr_count( $repo, 'delete_transient( self::CACHE_KEY )') >= 4 );
$theme_css = file_get_contents( dirname( __DIR__, 2 ) . '/wordpress/themes/job-scam-checker-theme/assets/css/base.css' );
jsc_phase7_test( 'frontend retains focus and reduced-motion accommodations', false !== strpos( $theme_css, ':focus-visible') && false !== strpos( $theme_css, 'prefers-reduced-motion: reduce') );

echo "\n{$tests} tests, {$failed} failures.\n"; exit( $failed > 0 ? 1 : 0 );
