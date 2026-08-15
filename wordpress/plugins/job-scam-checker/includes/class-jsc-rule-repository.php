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
    const CACHE_KEY = 'jsc_enabled_rules_v1';
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
        $cached = get_transient( self::CACHE_KEY );
        if ( is_array( $cached ) ) {
            return $cached;
        }
        $query = "SELECT id, name, slug, match_type, pattern, category, score_group, weight, explanation, recommendation, priority
            FROM {$this->table}
            WHERE enabled = 1
            ORDER BY priority ASC, id ASC";

        $rules = $this->wpdb->get_results( $query, ARRAY_A ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared -- Static table query with no user input.
        $rules = is_array( $rules ) ? $rules : array();
        set_transient( self::CACHE_KEY, $rules, 5 * MINUTE_IN_SECONDS );
        return $rules;
    }

    /** Return rules for administration, optionally filtered by a safe search term/category/status. */
    public function get_rules( $search = '', $category = '', $status = '' ) {
        $where = array( '1=1' );
        $args  = array();
        if ( '' !== $search ) {
            $like    = '%' . $this->wpdb->esc_like( $search ) . '%';
            $where[] = '(name LIKE %s OR slug LIKE %s OR explanation LIKE %s)';
            array_push( $args, $like, $like, $like );
        }
        if ( '' !== $category ) { $where[] = 'category = %s'; $args[] = sanitize_key( $category ); }
        if ( in_array( $status, array( 'enabled', 'disabled' ), true ) ) { $where[] = 'enabled = %d'; $args[] = 'enabled' === $status ? 1 : 0; }
        $sql = "SELECT * FROM {$this->table} WHERE " . implode( ' AND ', $where ) . ' ORDER BY priority ASC, id ASC';
        if ( $args ) { $sql = $this->wpdb->prepare( $sql, $args ); }
        $rows = $this->wpdb->get_results( $sql, ARRAY_A );
        return is_array( $rows ) ? $rows : array();
    }

    public function get_rule( $id ) {
        $row = $this->wpdb->get_row( $this->wpdb->prepare( "SELECT * FROM {$this->table} WHERE id = %d LIMIT 1", (int) $id ), ARRAY_A );
        return is_array( $row ) ? $row : null;
    }

    public function slug_exists( $slug, $exclude_id = 0 ) {
        $sql = "SELECT id FROM {$this->table} WHERE slug = %s";
        $args = array( $slug );
        if ( $exclude_id ) { $sql .= ' AND id != %d'; $args[] = (int) $exclude_id; }
        return (bool) $this->wpdb->get_var( $this->wpdb->prepare( $sql . ' LIMIT 1', $args ) );
    }

    /** Insert a validated custom rule. */
    public function create( array $rule ) {
        $now = current_time( 'mysql', true );
        $data = array_merge( $rule, array( 'is_default' => 0, 'created_at' => $now, 'updated_at' => $now ) );
        $id = $this->wpdb->insert( $this->table, $data, self::formats() ) ? (int) $this->wpdb->insert_id : 0;
        if ( $id ) { delete_transient( self::CACHE_KEY ); }
        return $id;
    }

    /** Update only administrator-editable fields. */
    public function update( $id, array $rule ) {
        $rule['updated_at'] = current_time( 'mysql', true );
        $updated = false !== $this->wpdb->update( $this->table, $rule, array( 'id' => (int) $id ), array( '%s', '%s', '%s', '%s', '%s', '%s', '%d', '%s', '%s', '%d', '%d', '%s' ), array( '%d' ) );
        if ( $updated ) { delete_transient( self::CACHE_KEY ); }
        return $updated;
    }

    public function set_enabled( $id, $enabled ) {
        $updated = false !== $this->wpdb->update( $this->table, array( 'enabled' => $enabled ? 1 : 0, 'updated_at' => current_time( 'mysql', true ) ), array( 'id' => (int) $id ), array( '%d', '%s' ), array( '%d' ) );
        if ( $updated ) { delete_transient( self::CACHE_KEY ); }
        return $updated;
    }

    /** Delete custom rules only; defaults fail closed. */
    public function delete_custom( $id ) {
        $deleted = false !== $this->wpdb->query( $this->wpdb->prepare( "DELETE FROM {$this->table} WHERE id = %d AND is_default = 0", (int) $id ) );
        if ( $deleted ) { delete_transient( self::CACHE_KEY ); }
        return $deleted;
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

            $rule['is_default'] = 1;
            $rule['created_at'] = $now;
            $rule['updated_at'] = $now;
            $this->wpdb->insert( $this->table, $rule, self::formats() );
            delete_transient( self::CACHE_KEY );
        }
    }

    /**
     * @return array<int,string>
     */
    private static function formats() {
        return array( '%s', '%s', '%s', '%s', '%s', '%s', '%d', '%s', '%s', '%d', '%d', '%d', '%s', '%s' );
    }
}
