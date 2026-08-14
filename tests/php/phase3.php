<?php
/**
 * Phase 3 results experience tests.
 */

require __DIR__ . '/bootstrap.php';

$tests = 0;
$failed = 0;

function jsc_phase3_test( $description, $condition, $detail = '' ) {
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

$cases = array(
    'low' => 'Thank you for applying on our careers page. We would like to schedule a panel interview next week.',
    'caution' => 'We found your profile. WhatsApp recruitment for this position. You are already hired.',
    'high' => 'Before the interview, send your bank account details and share the OTP so payroll can verify you.',
    'very_high' => 'No interview required. Deposit USDT to unlock tasks, recharge your account, and send bitcoin today. Buy gift cards and send gift card codes.',
);

foreach ( $cases as $expected_level => $message ) {
    $result = $engine->analyze( $message, $rules );
    jsc_phase3_test( $expected_level . ' example has expected risk level', $expected_level === $result['level']['key'], $result['level']['key'] . ' score ' . $result['score'] );
    jsc_phase3_test( $expected_level . ' example has recommended actions', count( $result['actions'] ) >= 3, (string) count( $result['actions'] ) );
    jsc_phase3_test( $expected_level . ' wording remains probabilistic', false === stripos( $result['level']['message'], 'definitely a scam' ) );
}

$low_actions = array_column( $engine->analyze( $cases['low'], $rules )['actions'], 'id' );
jsc_phase3_test( 'low result still includes money safety guidance', in_array( 'no-money', $low_actions, true ) );
jsc_phase3_test( 'low result still includes password and code safety guidance', in_array( 'protect-codes', $low_actions, true ) );
jsc_phase3_test( 'low result still includes personal-information guidance', in_array( 'protect-identity', $low_actions, true ) );

$payment = $engine->analyze( 'Pay the registration fee with crypto before starting.', $rules );
$action_ids = array_column( $payment['actions'], 'id' );
jsc_phase3_test( 'payment result recommends not sending money', in_array( 'no-money', $action_ids, true ) );
jsc_phase3_test( 'all results recommend independent verification', in_array( 'verify-employer', $action_ids, true ) );
jsc_phase3_test( 'all results recommend official careers page', in_array( 'official-careers-page', $action_ids, true ) );

$credentials = $engine->analyze( 'Send your bank account details and share the OTP immediately.', $rules );
jsc_phase3_test( 'credential result recommends protecting codes', in_array( 'protect-codes', array_column( $credentials['actions'], 'id' ), true ) );

$identity = $engine->analyze( 'Upload a photo of your passport before the interview.', $rules );
jsc_phase3_test( 'identity result recommends limiting personal information', in_array( 'protect-identity', array_column( $identity['actions'], 'id' ), true ) );

$domain = $engine->analyze( 'Register today at https://bit.ly/fakejob or http://192.0.2.4/login.', $rules );
jsc_phase3_test( 'suspicious domain output contains reasons', ! empty( $domain['suspicious_links'][0]['reasons'] ) );
jsc_phase3_test( 'suspicious domain output excludes full URL path', false === strpos( json_encode( $domain['suspicious_links'] ), 'fakejob' ) );

$public = new JSC_Public();
$public->register_assets();
$markup = $public->render_checker();

jsc_phase3_test( 'result region has accessible heading relationship', false !== strpos( $markup, 'aria-labelledby="jsc-checker-1-result-title"' ) );
jsc_phase3_test( 'result region can receive programmatic focus', false !== strpos( $markup, 'data-jsc-result hidden tabindex="-1"' ) );
jsc_phase3_test( 'status announcement is polite and atomic', false !== strpos( $markup, 'role="status" aria-live="polite" aria-atomic="true"' ) );
jsc_phase3_test( 'risk score uses semantic progress output', false !== strpos( $markup, '<progress data-jsc-progress max="100"' ) );
jsc_phase3_test( 'error state is an accessible alert', false !== strpos( $markup, 'data-jsc-error hidden role="alert" tabindex="-1"' ) );
jsc_phase3_test( 'no-JavaScript state confirms message was not sent', false !== strpos( $markup, 'Your message has not been sent or stored' ) );
jsc_phase3_test( 'print control is available', false !== strpos( $markup, 'data-jsc-print' ) );
jsc_phase3_test( 'reset control is available', false !== strpos( $markup, 'data-jsc-reset' ) );
jsc_phase3_test( 'AdSense-ready slot exists without ad content', (bool) preg_match( '/<aside class="jsc-ad-slot"[^>]*hidden[^>]*><\/aside>/', $markup ) );

$script = file_get_contents( JSC_PLUGIN_DIR . 'assets/js/checker.js' );
$style = file_get_contents( JSC_PLUGIN_DIR . 'assets/css/checker.css' );
jsc_phase3_test( 'dynamic result copy uses textContent', false !== strpos( $script, '.textContent =' ) );
jsc_phase3_test( 'dynamic result script avoids innerHTML', false === strpos( $script, 'innerHTML' ) && false === strpos( $script, 'insertAdjacentHTML' ) );
jsc_phase3_test( 'script handles server and malformed response failures', false !== strpos( $script, 'showError' ) && false !== strpos( $script, 'JSON.parse' ) );
jsc_phase3_test( 'script implements print action', false !== strpos( $script, 'window.print()' ) );
jsc_phase3_test( 'script honors reduced-motion preference', false !== strpos( $script, 'prefers-reduced-motion: reduce' ) );
jsc_phase3_test( 'script provides a bounded network failure path', false !== strpos( $script, 'controller.abort()' ) );
jsc_phase3_test( 'styles include phone breakpoint', false !== strpos( $style, '@media (max-width: 30rem)' ) );
jsc_phase3_test( 'styles include mobile breakpoint', false !== strpos( $style, '@media (max-width: 37.5rem)' ) );
jsc_phase3_test( 'styles include print layout', false !== strpos( $style, '@media print' ) );
jsc_phase3_test( 'print layout preserves hidden ungenerated result', false !== strpos( $style, '.jsc-result[hidden]' ) );
jsc_phase3_test( 'styles keep hidden ad slot out of layout', false !== strpos( $style, '.jsc-ad-slot[hidden]' ) );

echo "\n{$tests} tests, {$failed} failures.\n";
exit( $failed > 0 ? 1 : 0 );
