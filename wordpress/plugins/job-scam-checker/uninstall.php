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
delete_option( 'jsc_installed_pages' );

global $wpdb;
$table_name = $wpdb->prefix . 'jsc_rules';
$wpdb->query( "DROP TABLE IF EXISTS {$table_name}" ); // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- Controlled WordPress-prefixed table name during explicit uninstall.
