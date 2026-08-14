<?php
/**
 * Public checker shell.
 *
 * @package JobScamChecker
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class JSC_Public {
    /**
     * Register public hooks.
     */
    public function register_hooks() {
        add_action( 'wp_enqueue_scripts', array( $this, 'register_assets' ) );
        add_shortcode( 'job_scam_checker', array( $this, 'render_checker' ) );
    }

    /**
     * Register assets without loading them on pages that do not use the checker.
     */
    public function register_assets() {
        wp_register_style(
            'jsc-checker',
            JSC_PLUGIN_URL . 'assets/css/checker.css',
            array(),
            JSC_VERSION
        );
        wp_register_script(
            'jsc-checker',
            JSC_PLUGIN_URL . 'assets/js/checker.js',
            array(),
            JSC_VERSION,
            true
        );
    }

    /**
     * Render the Phase 1 checker interface.
     *
     * @return string
     */
    public function render_checker() {
        wp_enqueue_style( 'jsc-checker' );
        wp_enqueue_script( 'jsc-checker' );

        ob_start();
        require JSC_PLUGIN_DIR . 'public/views/checker-form.php';
        return (string) ob_get_clean();
    }
}
