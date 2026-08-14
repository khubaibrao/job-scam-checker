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
        $public = new JSC_Public();
        $public->register_hooks();

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
