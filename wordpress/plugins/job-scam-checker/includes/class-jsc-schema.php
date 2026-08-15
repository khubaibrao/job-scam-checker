<?php
/**
 * Database schema and seed migrations.
 *
 * @package JobScamChecker
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class JSC_Schema {
    /**
     * Create or update application tables and seed initial rules.
     */
    public static function install() {
        global $wpdb;

        $table_name      = $wpdb->prefix . 'jsc_rules';
        $charset_collate = $wpdb->get_charset_collate();
        $sql             = "CREATE TABLE {$table_name} (
            id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            name varchar(190) NOT NULL,
            slug varchar(190) NOT NULL,
            match_type varchar(32) NOT NULL,
            pattern text NOT NULL,
            category varchar(64) NOT NULL,
            score_group varchar(64) NOT NULL,
            weight smallint(5) unsigned NOT NULL DEFAULT 1,
            explanation text NOT NULL,
            recommendation text NOT NULL,
            enabled tinyint(1) unsigned NOT NULL DEFAULT 1,
            priority smallint(5) unsigned NOT NULL DEFAULT 100,
            created_at datetime NOT NULL,
            updated_at datetime NOT NULL,
            PRIMARY KEY  (id),
            UNIQUE KEY slug (slug),
            KEY enabled_priority (enabled, priority),
            KEY category (category)
        ) {$charset_collate};";

        require_once ABSPATH . 'wp-admin/includes/upgrade.php';
        dbDelta( $sql );

        $stats_table = $wpdb->prefix . 'jsc_daily_stats';
        $stats_sql   = "CREATE TABLE {$stats_table} (
            stat_date date NOT NULL,
            metric varchar(32) NOT NULL,
            stat_key varchar(100) NOT NULL,
            stat_count bigint(20) unsigned NOT NULL DEFAULT 0,
            PRIMARY KEY  (stat_date, metric, stat_key),
            KEY metric_date (metric, stat_date)
        ) {$charset_collate};";
        dbDelta( $stats_sql );

        self::seed_default_rules();
        update_option( 'jsc_db_version', JSC_DB_VERSION, false );
    }

    /**
     * Run safe migrations following an ordinary plugin update.
     */
    public static function maybe_upgrade() {
        if ( JSC_DB_VERSION !== get_option( 'jsc_db_version' ) ) {
            self::install();
        }
    }

    /**
     * Insert missing defaults without overwriting administrator edits.
     */
    private static function seed_default_rules() {
        global $wpdb;

        $repository = new JSC_Rule_Repository( $wpdb );
        $repository->seed_missing( require JSC_PLUGIN_DIR . 'data/default-rules.php' );
    }
}
