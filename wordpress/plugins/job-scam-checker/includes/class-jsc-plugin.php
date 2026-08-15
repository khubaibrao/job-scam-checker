<?php
/**
 * Main plugin coordinator.
 *
 * @package JobScamChecker
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class JSC_Plugin {
    /**
     * Register plugin integrations.
     */
    public function run() {
        JSC_Schema::maybe_upgrade();
        JSC_Content_Installer::maybe_install();

        $public = new JSC_Public();
        $public->register_hooks();

        $rest = new JSC_REST_Controller();
        $rest->register_hooks();

        $settings = new JSC_Settings();
        $settings->register_hooks();

        $admin = new JSC_Admin();
        $admin->register_hooks();

        if ( ! wp_next_scheduled( 'jsc_daily_cleanup' ) ) {
            wp_schedule_event( time() + DAY_IN_SECONDS, 'daily', 'jsc_daily_cleanup' );
        }

        add_action( 'init', array( $this, 'load_textdomain' ) );
    }

    /**
     * Load translations bundled with the plugin.
     */
    public function load_textdomain() {
        load_plugin_textdomain(
            'job-scam-checker',
            false,
            dirname( plugin_basename( JSC_PLUGIN_FILE ) ) . '/languages'
        );
    }
}
