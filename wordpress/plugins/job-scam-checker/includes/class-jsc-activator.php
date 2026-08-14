<?php
/**
 * Plugin activation tasks.
 *
 * @package JobScamChecker
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class JSC_Activator {
    /**
     * Install the Phase 1 content foundation and version marker.
     */
    public static function activate() {
        JSC_Content_Installer::install();
        update_option( 'jsc_version', JSC_VERSION, false );
        flush_rewrite_rules();
    }
}
