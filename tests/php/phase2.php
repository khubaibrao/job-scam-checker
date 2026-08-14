<?php
/**
 * Phase 2 rule engine, scoring, link and privacy-oriented tests.
 */

require __DIR__ . '/bootstrap.php';

$tests = 0;
$failed = 0;

function jsc_phase2_test( $description, $condition, $detail = '' ) {
    global $tests, $failed;
    ++$tests;
    if ( $condition ) {
        echo "PASS: {$description}\n";
        return;
    }
    ++$failed;
    echo "FAIL: {$description}" . ( $detail ? " ({$detail})" : '' ) . "\n";
}

$rules = require JSC_PLUGIN_DIR . 'data/default-rules.php';
$engine = new JSC_Rule_Engine();
$fixtures = json_decode( file_get_contents( dirname( __DIR__ ) . '/fixtures/messages.json' ), true );

jsc_phase2_test( 'initial library contains at least 35 rules', count( $rules ) >= 35, (string) count( $rules ) );
jsc_phase2_test( 'every rule has a unique slug', count( $rules ) === count( array_unique( array_column( $rules, 'slug' ) ) ) );

foreach ( $fixtures as $fixture ) {
    $result = $engine->analyze( $fixture['message'], $rules );
    $slugs = array_column( $result['detections'], 'slug' );

    jsc_phase2_test( $fixture['id'] . ' score stays within 0–100', $result['score'] >= 0 && $result['score'] <= 100, (string) $result['score'] );
    if ( 'legitimate' === $fixture['kind'] ) {
        jsc_phase2_test( $fixture['id'] . ' avoids false-positive threshold', $result['score'] <= $fixture['max_score'], 'score ' . $result['score'] . '; ' . implode( ',', $slugs ) );
    } else {
        jsc_phase2_test( $fixture['id'] . ' reaches expected risk threshold', $result['score'] >= $fixture['min_score'], 'score ' . $result['score'] . '; ' . implode( ',', $slugs ) );
        foreach ( $fixture['required'] as $required_slug ) {
            jsc_phase2_test( $fixture['id'] . ' detects ' . $required_slug, in_array( $required_slug, $slugs, true ), implode( ',', $slugs ) );
        }
    }
}

$overlap = $engine->analyze( 'Pay a training fee and registration fee before you start.', $rules );
$overlap_group = array_filter( $overlap['detections'], static function ( $item ) { return 'employment_fee' === $item['score_group']; } );
jsc_phase2_test( 'overlapping employment fees score once', 1 === count( $overlap_group ) );

$repeated = $engine->analyze( str_repeat( 'Send bitcoin. ', 30 ), $rules );
jsc_phase2_test( 'repeated phrases do not multiply score', 26 === $repeated['score'], (string) $repeated['score'] );

$safe_link = $engine->analyze( 'Apply at https://careers.example.com/jobs/123 after the interview.', $rules );
jsc_phase2_test( 'ordinary HTTPS link is not suspicious', 0 === count( $safe_link['suspicious_links'] ) );

$free_host = $engine->analyze( 'Complete hiring at https://company.wixsite.com/jobs now.', $rules );
jsc_phase2_test( 'free-hosted recruiting domain is identified', in_array( 'free-hosting-domain', array_column( $free_host['detections'], 'slug' ), true ) );

$html = $engine->analyze( '<script>alert(1)</script> Send your password', $rules );
jsc_phase2_test( 'analysis output does not contain pasted script markup', false === strpos( json_encode( $html ), '<script>' ) );

jsc_phase2_test( 'risk 0 maps to low', 'low' === JSC_Risk_Calculator::level_for_score( 0 )['key'] );
jsc_phase2_test( 'risk 25 maps to caution', 'caution' === JSC_Risk_Calculator::level_for_score( 25 )['key'] );
jsc_phase2_test( 'risk 50 maps to high', 'high' === JSC_Risk_Calculator::level_for_score( 50 )['key'] );
jsc_phase2_test( 'risk 75 maps to very high', 'very_high' === JSC_Risk_Calculator::level_for_score( 75 )['key'] );

$_SERVER['REMOTE_ADDR'] = '203.0.113.10';
$limiter = new JSC_Rate_Limiter();
$allowed = true;
for ( $i = 0; $i < JSC_Rate_Limiter::LIMIT; ++$i ) {
    $allowed = $allowed && $limiter->consume();
}
jsc_phase2_test( 'rate limiter allows configured initial requests', $allowed );
jsc_phase2_test( 'rate limiter blocks requests over the limit', false === $limiter->consume() );
$transient_keys = array_keys( $GLOBALS['jsc_test_transients'] );
jsc_phase2_test( 'rate limit key does not store raw IP address', false === strpos( $transient_keys[0], $_SERVER['REMOTE_ADDR'] ) );

$schema_source = file_get_contents( JSC_PLUGIN_DIR . 'includes/class-jsc-schema.php' );
jsc_phase2_test( 'schema creates the custom rules table through dbDelta', false !== strpos( $schema_source, 'dbDelta( $sql )' ) );
jsc_phase2_test( 'schema includes editable rule fields', false !== strpos( $schema_source, 'match_type varchar' ) && false !== strpos( $schema_source, 'explanation text' ) && false !== strpos( $schema_source, 'enabled tinyint' ) );

class JSC_Test_Request {
    private $headers;
    private $message;
    public function __construct( $nonce, $message, $length = 0 ) {
        $this->headers = array( 'X-JSC-Nonce' => $nonce, 'Content-Length' => $length );
        $this->message = $message;
    }
    public function get_header( $name ) { return $this->headers[ $name ] ?? ''; }
    public function get_param() { return $this->message; }
}

$controller = new JSC_REST_Controller();
$bad_nonce = $controller->analyze( new JSC_Test_Request( 'wrong', 'This is a long enough message.' ) );
jsc_phase2_test( 'endpoint rejects an invalid nonce', $bad_nonce instanceof WP_Error && 'jsc_invalid_nonce' === $bad_nonce->code );
$too_large = $controller->analyze( new JSC_Test_Request( 'valid-test-nonce', str_repeat( 'x', 24001 ) ) );
jsc_phase2_test( 'endpoint rejects oversized message bytes', $too_large instanceof WP_Error && 'jsc_message_too_long' === $too_large->code );

class JSC_Test_WPDB {
    public $prefix = 'wp_';
    private $rules;
    public function __construct( $rules ) { $this->rules = $rules; }
    public function get_results() { return $this->rules; }
}

$GLOBALS['wpdb'] = new JSC_Test_WPDB( $rules );
$_SERVER['REMOTE_ADDR'] = '203.0.113.11';
$submitted_message = 'No interview required. Pay the registration fee with crypto today.';
$success = $controller->analyze( new JSC_Test_Request( 'valid-test-nonce', $submitted_message, strlen( $submitted_message ) ) );
jsc_phase2_test( 'endpoint returns a successful structured analysis', $success instanceof WP_REST_Response && 200 === $success->status && $success->data['score'] >= 50 );
jsc_phase2_test( 'successful endpoint response does not echo submitted message', false === strpos( json_encode( $success->data ), $submitted_message ) );
jsc_phase2_test( 'rate-limit storage never contains submitted message', false === strpos( json_encode( $GLOBALS['jsc_test_transients'] ), $submitted_message ) );

echo "\n{$tests} tests, {$failed} failures.\n";
exit( $failed > 0 ? 1 : 0 );
