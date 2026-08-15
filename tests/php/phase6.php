<?php
/** Phase 6 administration and management tests. */
require __DIR__ . '/bootstrap.php';

$tests = 0; $failed = 0;
function jsc_phase6_test( $description, $condition ) { global $tests, $failed; ++$tests; if ( $condition ) { echo "PASS: {$description}\n"; } else { ++$failed; echo "FAIL: {$description}\n"; } }

$valid = array( 'name'=>'Custom payment language', 'slug'=>'custom-payment-language', 'match_type'=>'phrase', 'pattern'=>'["pay the recruiter"]', 'category'=>'payment', 'score_group'=>'custom_payment', 'weight'=>'17', 'priority'=>'100', 'explanation'=>'Payment before hiring is dangerous.', 'recommendation'=>'Do not pay; verify independently.', 'enabled'=>'1' );
$validator = new JSC_Rule_Validator();
$result = $validator->validate( $valid );
jsc_phase6_test( 'valid custom rule is accepted', is_array( $result ) && 17 === $result['weight'] && 1 === $result['enabled'] );
jsc_phase6_test( 'rule text is sanitized', is_array( $validator->validate( array_merge( $valid, array( 'name'=>'<b>Safe title</b>' ) ) ) ) && 'Safe title' === $validator->validate( array_merge( $valid, array( 'name'=>'<b>Safe title</b>' ) ) )['name'] );
jsc_phase6_test( 'negative rule weight is rejected', is_wp_error( $validator->validate( array_merge( $valid, array( 'weight'=>'-1' ) ) ) ) );
jsc_phase6_test( 'over-100 rule weight is rejected', is_wp_error( $validator->validate( array_merge( $valid, array( 'weight'=>'101' ) ) ) ) );
jsc_phase6_test( 'non-numeric rule weight is rejected', is_wp_error( $validator->validate( array_merge( $valid, array( 'weight'=>'ten' ) ) ) ) );
jsc_phase6_test( 'invalid match type is rejected', is_wp_error( $validator->validate( array_merge( $valid, array( 'match_type'=>'php' ) ) ) ) );
jsc_phase6_test( 'empty phrase pattern is rejected', is_wp_error( $validator->validate( array_merge( $valid, array( 'pattern'=>'' ) ) ) ) );
jsc_phase6_test( 'malformed contextual rule is rejected', is_wp_error( $validator->validate( array_merge( $valid, array( 'match_type'=>'contextual', 'pattern'=>'[["job"]]' ) ) ) ) );
jsc_phase6_test( 'invalid regular expression is rejected', is_wp_error( $validator->validate( array_merge( $valid, array( 'match_type'=>'regex', 'pattern'=>'/(unclosed/' ) ) ) ) );
jsc_phase6_test( 'regex code execution construct is rejected', is_wp_error( $validator->validate( array_merge( $valid, array( 'match_type'=>'regex', 'pattern'=>'/(?{phpinfo();})/' ) ) ) ) );
jsc_phase6_test( 'required management categories exist', 0 === count( array_diff( array( 'payment','cryptocurrency','gift_cards','fake_check','equipment','task_scam','impersonation','communication','pressure','credentials','compensation','links' ), JSC_Rule_Validator::categories() ) ) );

$settings = new JSC_Settings();
jsc_phase6_test( 'privacy statistics checkbox sanitizes', '1' === $settings->checkbox( '1' ) && '0' === $settings->checkbox( 'yes' ) );
jsc_phase6_test( 'retention setting remains bounded', 30 === $settings->retention( 1 ) && 730 === $settings->retention( 900 ) && 365 === $settings->retention( 365 ) );
jsc_phase6_test( 'custom rule categories are sanitized and bounded', array( 'paymentrequests', 'custom', 'bad' ) === $settings->categories( 'Payment Requests, custom, <bad>' ) );
$GLOBALS['jsc_test_options']['jsc_checker_enabled'] = '0';
jsc_phase6_test( 'checker display setting disables checker safely', false !== strpos( ( new JSC_Public() )->render_checker(), 'temporarily unavailable' ) );
jsc_phase6_test( 'disabled checker is rejected by the API', 'jsc_checker_disabled' === ( new JSC_REST_Controller() )->analyze( null )->code );
$GLOBALS['jsc_test_options']['jsc_trends_visible'] = '0';
jsc_phase6_test( 'trend visibility setting suppresses component', '' === ( new JSC_Public() )->render_trends() );

$admin_source = file_get_contents( JSC_PLUGIN_DIR . 'admin/class-jsc-admin.php' );
$repo_source = file_get_contents( JSC_PLUGIN_DIR . 'includes/class-jsc-rule-repository.php' );
$schema_source = file_get_contents( JSC_PLUGIN_DIR . 'includes/class-jsc-schema.php' );
jsc_phase6_test( 'admin access requires manage_options', false !== strpos( $admin_source, "const CAPABILITY = 'manage_options'" ) && false !== strpos( $admin_source, 'current_user_can' ) );
jsc_phase6_test( 'state changes require nonce verification', substr_count( $admin_source, 'check_admin_referer' ) >= 1 && false !== strpos( $admin_source, "'jsc_save_rule_'" ) && false !== strpos( $admin_source, "'jsc_reset_statistics'" ) );
jsc_phase6_test( 'settings use WordPress Settings API', false !== strpos( $admin_source, "settings_fields( 'jsc_management' )" ) );
jsc_phase6_test( 'rule create edit enable disable and duplicate actions exist', false !== strpos( $repo_source, 'function create' ) && false !== strpos( $repo_source, 'function update' ) && false !== strpos( $repo_source, 'function set_enabled' ) && false !== strpos( $admin_source, "'duplicate' === \$action" ) );
jsc_phase6_test( 'default-rule schema protection is durable', false !== strpos( $schema_source, 'is_default tinyint' ) && false !== strpos( $repo_source, 'is_default = 0' ) );
jsc_phase6_test( 'default deletion is rejected by controller', false !== strpos( $admin_source, 'Default rules cannot be deleted' ) );
jsc_phase6_test( 'statistics dashboard uses existing aggregate metrics', false !== strpos( $admin_source, "totals( 'risk_level' )" ) && false !== strpos( $admin_source, "'payment_purpose'") && false !== strpos( $admin_source, 'daily_totals' ) );
jsc_phase6_test( 'statistics reset requires authorization nonce and explicit confirmation', false !== strpos( $admin_source, "authorize( 'jsc_reset_statistics' )" ) && false !== strpos( $admin_source, "'yes' !== ( \$_POST['confirm_reset']") );
jsc_phase6_test( 'statistics reset repository targets only aggregate table', false !== strpos( file_get_contents( JSC_PLUGIN_DIR . 'includes/class-jsc-statistics-repository.php' ), 'DELETE FROM {$this->table}' ) );
jsc_phase6_test( 'privacy screen states exact non-collection boundary', false !== strpos( $admin_source, 'Pasted text and visitor identities are never stored' ) && false !== strpos( $admin_source, 'full URLs, credentials, or per-visitor histories' ) );
jsc_phase6_test( 'admin assets are scoped to plugin screens', false !== strpos( $admin_source, "strpos( (string) \$hook, 'jsc-' )" ) );

// Phase 5 aggregation behavior remains intact through the unchanged service boundary.
class JSC_Phase6_Stats_DB {
    public $prefix = 'wp_'; public $queries = array();
    public function prepare( $sql, $args = null ) { return $sql; }
    public function query( $sql ) { $this->queries[] = $sql; return 4; }
    public function get_results() { return array(); }
}
$fake_db = new JSC_Phase6_Stats_DB();
jsc_phase6_test( 'statistics reset succeeds without touching non-stat content', true === ( new JSC_Statistics_Repository( $fake_db ) )->reset() && 1 === count( $fake_db->queries ) && false !== strpos( $fake_db->queries[0], 'wp_jsc_daily_stats' ) );

echo "\n{$tests} tests, {$failed} failures.\n"; exit( $failed > 0 ? 1 : 0 );
