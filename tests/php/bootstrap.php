<?php
/**
 * Minimal WordPress stubs for dependency-free Phase 1 tests.
 */

define( 'ABSPATH', dirname( __DIR__, 2 ) . '/' );
define( 'OBJECT', 'OBJECT' );
define( 'ARRAY_A', 'ARRAY_A' );
define( 'JSC_VERSION', '0.2.0' );
define( 'JSC_DB_VERSION', '1.0.0' );
define( 'JSC_PLUGIN_DIR', dirname( __DIR__, 2 ) . '/wordpress/plugins/job-scam-checker/' );
define( 'JSC_PLUGIN_URL', 'https://example.test/wp-content/plugins/job-scam-checker/' );

$GLOBALS['jsc_test_pages']    = array();
$GLOBALS['jsc_test_options']  = array();
$GLOBALS['jsc_test_styles']   = array();
$GLOBALS['jsc_test_scripts']  = array();
$GLOBALS['jsc_test_shortcodes'] = array();
$GLOBALS['jsc_test_transients'] = array();
$GLOBALS['jsc_test_localized']  = array();

class WP_Post {
    public $ID;
    public $post_name;

    public function __construct( $id, $slug ) {
        $this->ID        = $id;
        $this->post_name = $slug;
    }
}

class WP_Error {
    public $code;
    public $message;
    public $data;
    public function __construct( $code = '', $message = '', $data = array() ) {
        $this->code = $code; $this->message = $message; $this->data = $data;
    }
}

class WP_REST_Response {
    public $data;
    public $status;
    public function __construct( $data, $status = 200 ) { $this->data = $data; $this->status = $status; }
}

function __( $text ) { return $text; }
function esc_html__( $text ) { return htmlspecialchars( $text, ENT_QUOTES, 'UTF-8' ); }
function esc_attr__( $text ) { return htmlspecialchars( $text, ENT_QUOTES, 'UTF-8' ); }
function esc_html_e( $text ) { echo esc_html__( $text ); }
function esc_attr_e( $text ) { echo esc_attr__( $text ); }
function esc_url_raw( $url ) { return filter_var( 0 === strpos( $url, 'www.' ) ? 'https://' . $url : $url, FILTER_SANITIZE_URL ); }
function wp_strip_all_tags( $text ) { return strip_tags( $text ); }
function sanitize_key( $text ) { return preg_replace( '/[^a-z0-9_\-]/', '', strtolower( $text ) ); }
function sanitize_text_field( $text ) { return trim( strip_tags( $text ) ); }
function wp_parse_url( $url, $component = -1 ) { return parse_url( $url, $component ); }
function wp_json_encode( $value ) { return json_encode( $value, JSON_UNESCAPED_SLASHES ); }
function wp_unslash( $value ) { return $value; }
function wp_salt() { return 'test-only-fixed-salt'; }
function wp_create_nonce() { return 'valid-test-nonce'; }
function wp_verify_nonce( $nonce ) { return 'valid-test-nonce' === $nonce; }
function rest_url( $path ) { return 'https://example.test/wp-json/' . $path; }
function nocache_headers() {}
function get_option( $name ) { return $GLOBALS['jsc_test_options'][ $name ] ?? false; }
function get_transient( $key ) { return $GLOBALS['jsc_test_transients'][ $key ] ?? false; }
function set_transient( $key, $value ) { $GLOBALS['jsc_test_transients'][ $key ] = $value; return true; }
function is_wp_error( $value ) { return $value instanceof WP_Error; }
function add_action() {}

function add_shortcode( $name, $callback ) {
    $GLOBALS['jsc_test_shortcodes'][ $name ] = $callback;
}

function wp_register_style( $handle, $src ) {
    $GLOBALS['jsc_test_styles'][ $handle ] = $src;
}

function wp_register_script( $handle, $src ) {
    $GLOBALS['jsc_test_scripts'][ $handle ] = $src;
}

function wp_enqueue_style( $handle ) {
    $GLOBALS['jsc_test_styles'][ $handle ] = $GLOBALS['jsc_test_styles'][ $handle ] ?? true;
}

function wp_enqueue_script( $handle ) {
    $GLOBALS['jsc_test_scripts'][ $handle ] = $GLOBALS['jsc_test_scripts'][ $handle ] ?? true;
}

function wp_localize_script( $handle, $name, $data ) {
    $GLOBALS['jsc_test_localized'][ $name ] = $data;
}

function get_page_by_path( $slug ) {
    return $GLOBALS['jsc_test_pages'][ $slug ] ?? null;
}

function wp_insert_post( $post ) {
    $id = count( $GLOBALS['jsc_test_pages'] ) + 1;
    $GLOBALS['jsc_test_pages'][ $post['post_name'] ] = new WP_Post( $id, $post['post_name'] );
    return $id;
}

function update_option( $name, $value ) {
    $GLOBALS['jsc_test_options'][ $name ] = $value;
    return true;
}

require_once JSC_PLUGIN_DIR . 'includes/class-jsc-content-installer.php';
require_once JSC_PLUGIN_DIR . 'includes/class-jsc-rule-repository.php';
require_once JSC_PLUGIN_DIR . 'includes/class-jsc-link-analyzer.php';
require_once JSC_PLUGIN_DIR . 'includes/class-jsc-risk-calculator.php';
require_once JSC_PLUGIN_DIR . 'includes/class-jsc-rule-engine.php';
require_once JSC_PLUGIN_DIR . 'includes/class-jsc-rate-limiter.php';
require_once JSC_PLUGIN_DIR . 'public/class-jsc-public.php';
require_once JSC_PLUGIN_DIR . 'public/class-jsc-rest-controller.php';
