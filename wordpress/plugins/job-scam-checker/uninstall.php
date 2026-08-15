<?php
/**
 * Preserve content by default when the plugin is removed.
 *
 * Deliberately avoid deleting pages because they may have been edited by a site
 * administrator. Remove application-owned rule data and version markers.
 *
 * @package JobScamChecker
 */

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
    exit;
}

delete_option( 'jsc_version' );
delete_option( 'jsc_db_version' );
delete_option( 'jsc_content_version' );
delete_option( 'jsc_installed_pages' );
delete_option( 'jsc_statistics_enabled' );
delete_option( 'jsc_follow_up_enabled' );
delete_option( 'jsc_statistics_retention_days' );
wp_clear_scheduled_hook( 'jsc_daily_cleanup' );

global $wpdb;
$table_name = $wpdb->prefix . 'jsc_rules';
$wpdb->query( "DROP TABLE IF EXISTS {$table_name}" ); // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- Controlled WordPress-prefixed table name during explicit uninstall.
$stats_table = $wpdb->prefix . 'jsc_daily_stats';
$wpdb->query( "DROP TABLE IF EXISTS {$stats_table}" ); // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- Controlled WordPress-prefixed table name during explicit uninstall.
