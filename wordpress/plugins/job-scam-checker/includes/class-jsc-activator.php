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
     * Install the current schema, content release, settings, and version marker.
     */
    public static function activate() {
        JSC_Schema::install();
        JSC_Content_Installer::install();
        update_option( 'jsc_version', JSC_VERSION, false );
        if ( false === get_option( 'jsc_statistics_enabled', false ) ) {
            update_option( 'jsc_statistics_enabled', '0', false );
            update_option( 'jsc_follow_up_enabled', '1', false );
            update_option( 'jsc_statistics_retention_days', 365, false );
        }
        if ( false === get_option( 'jsc_checker_enabled', false ) ) {
            update_option( 'jsc_checker_enabled', '1', false );
            update_option( 'jsc_trends_visible', '1', false );
            update_option( 'jsc_search_filters_enabled', '1', false );
            update_option( 'jsc_related_content_enabled', '1', false );
            update_option( 'jsc_result_focus_enabled', '1', false );
        }
        if ( ! wp_next_scheduled( 'jsc_daily_cleanup' ) ) {
            wp_schedule_event( time() + DAY_IN_SECONDS, 'daily', 'jsc_daily_cleanup' );
        }
        flush_rewrite_rules();
    }
}
