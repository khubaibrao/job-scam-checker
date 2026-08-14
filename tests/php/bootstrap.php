<?php
/**
 * Minimal WordPress stubs for dependency-free Phase 1 tests.
 */

define( 'ABSPATH', dirname( __DIR__, 2 ) . '/' );
define( 'OBJECT', 'OBJECT' );
define( 'JSC_VERSION', '0.1.0' );
define( 'JSC_PLUGIN_DIR', dirname( __DIR__, 2 ) . '/wordpress/plugins/job-scam-checker/' );
define( 'JSC_PLUGIN_URL', 'https://example.test/wp-content/plugins/job-scam-checker/' );

$GLOBALS['jsc_test_pages']    = array();
$GLOBALS['jsc_test_options']  = array();
$GLOBALS['jsc_test_styles']   = array();
$GLOBALS['jsc_test_scripts']  = array();
$GLOBALS['jsc_test_shortcodes'] = array();

class WP_Post {
    public $ID;
    public $post_name;

    public function __construct( $id, $slug ) {
        $this->ID        = $id;
        $this->post_name = $slug;
    }
}

class WP_Error {}

function __( $text ) { return $text; }
function esc_html__( $text ) { return htmlspecialchars( $text, ENT_QUOTES, 'UTF-8' ); }
function esc_attr__( $text ) { return htmlspecialchars( $text, ENT_QUOTES, 'UTF-8' ); }
function esc_html_e( $text ) { echo esc_html__( $text ); }
function esc_attr_e( $text ) { echo esc_attr__( $text ); }
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
require_once JSC_PLUGIN_DIR . 'public/class-jsc-public.php';
