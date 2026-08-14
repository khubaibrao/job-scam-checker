<?php
/**
 * Rule persistence.
 *
 * @package JobScamChecker
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class JSC_Rule_Repository {
    /** @var wpdb */
    private $wpdb;

    /** @var string */
    private $table;

    /**
     * @param wpdb $wpdb WordPress database connection.
     */
    public function __construct( $wpdb ) {
        $this->wpdb  = $wpdb;
        $this->table = $wpdb->prefix . 'jsc_rules';
    }

    /**
     * Return enabled rules ordered predictably.
     *
     * @return array<int,array<string,mixed>>
     */
    public function get_enabled_rules() {
        $query = "SELECT id, name, slug, match_type, pattern, category, score_group, weight, explanation, recommendation, priority
            FROM {$this->table}
            WHERE enabled = 1
            ORDER BY priority ASC, id ASC";

        $rules = $this->wpdb->get_results( $query, ARRAY_A ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared -- Static table query with no user input.
        return is_array( $rules ) ? $rules : array();
    }

    /**
     * Insert rules that do not already exist by stable slug.
     *
     * @param array<int,array<string,mixed>> $rules Default rule definitions.
     */
    public function seed_missing( array $rules ) {
        $now = current_time( 'mysql', true );

        foreach ( $rules as $rule ) {
            $exists = $this->wpdb->get_var(
                $this->wpdb->prepare( "SELECT id FROM {$this->table} WHERE slug = %s LIMIT 1", $rule['slug'] ) // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- Controlled prefixed table name.
            );

            if ( $exists ) {
                continue;
            }

            $rule['created_at'] = $now;
            $rule['updated_at'] = $now;
            $this->wpdb->insert( $this->table, $rule, self::formats() );
        }
    }

    /**
     * @return array<int,string>
     */
    private static function formats() {
        return array( '%s', '%s', '%s', '%s', '%s', '%s', '%d', '%s', '%s', '%d', '%d', '%s', '%s' );
    }
}
