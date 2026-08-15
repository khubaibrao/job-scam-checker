<?php
/**
 * Aggregate-only anonymous checker statistics.
 *
 * @package JobScamChecker
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class JSC_Statistics_Repository {
    /** @var wpdb */
    private $wpdb;

    /** @var string */
    private $table;

    public function __construct( $wpdb ) {
        $this->wpdb  = $wpdb;
        $this->table = $wpdb->prefix . 'jsc_daily_stats';
    }

    /**
     * Increment several aggregate counters in one database query.
     *
     * Values must be pre-validated identifiers. No visitor input or message text
     * is accepted by this persistence boundary.
     *
     * @param array<int,array{metric:string,stat_key:string}> $counters Counters.
     * @param string|null                                     $date UTC date.
     * @return bool
     */
    public function increment_many( array $counters, $date = null ) {
        $date = $date ?: current_time( 'Y-m-d', true );
        if ( ! preg_match( '/^\d{4}-\d{2}-\d{2}$/', $date ) || empty( $counters ) ) {
            return false;
        }

        $values = array();
        $args   = array();
        foreach ( $counters as $counter ) {
            $metric = sanitize_key( $counter['metric'] ?? '' );
            $key    = sanitize_key( $counter['stat_key'] ?? '' );
            if ( '' === $metric || '' === $key || strlen( $metric ) > 32 || strlen( $key ) > 100 ) {
                continue;
            }
            $values[] = '(%s, %s, %s, 1)';
            array_push( $args, $date, $metric, $key );
        }
        if ( empty( $values ) ) {
            return false;
        }

        $sql = "INSERT INTO {$this->table} (stat_date, metric, stat_key, stat_count) VALUES " . implode( ', ', $values ) .
            ' ON DUPLICATE KEY UPDATE stat_count = stat_count + 1';
        $written = false !== $this->wpdb->query( $this->wpdb->prepare( $sql, $args ) ); // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- Controlled prefixed table name; values prepared.
        if ( $written ) { delete_transient( 'jsc_public_trends' ); }
        return $written;
    }

    /**
     * Return grouped counts for a bounded date range.
     *
     * @return array<string,int>
     */
    public function counts( $metric, $start_date, $end_date ) {
        $sql = $this->wpdb->prepare(
            "SELECT stat_key, SUM(stat_count) AS total FROM {$this->table} WHERE metric = %s AND stat_date BETWEEN %s AND %s GROUP BY stat_key ORDER BY total DESC", // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- Controlled prefixed table name.
            sanitize_key( $metric ),
            $start_date,
            $end_date
        );
        $rows = $this->wpdb->get_results( $sql, ARRAY_A );
        $out  = array();
        foreach ( is_array( $rows ) ? $rows : array() as $row ) {
            $out[ $row['stat_key'] ] = (int) $row['total'];
        }
        return $out;
    }

    /** Delete aggregates older than the configured retention window. */
    public function delete_before( $date ) {
        return $this->wpdb->query( $this->wpdb->prepare( "DELETE FROM {$this->table} WHERE stat_date < %s", $date ) ); // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- Controlled prefixed table name.
    }

    /** Return all-time counts for an allow-listed metric. */
    public function totals( $metric ) {
        $allowed = array( 'checks', 'risk_level', 'detection', 'channel', 'money_request', 'payment_purpose' );
        if ( ! in_array( $metric, $allowed, true ) ) { return array(); }
        $rows = $this->wpdb->get_results( $this->wpdb->prepare( "SELECT stat_key, SUM(stat_count) AS total FROM {$this->table} WHERE metric = %s GROUP BY stat_key ORDER BY total DESC", $metric ), ARRAY_A );
        $out = array();
        foreach ( is_array( $rows ) ? $rows : array() as $row ) { $out[ $row['stat_key'] ] = (int) $row['total']; }
        return $out;
    }

    /** Daily total checks, newest first. */
    public function daily_totals( $limit = 30 ) {
        $limit = min( 365, max( 1, (int) $limit ) );
        $rows = $this->wpdb->get_results( $this->wpdb->prepare( "SELECT stat_date, stat_count AS total FROM {$this->table} WHERE metric = %s AND stat_key = %s ORDER BY stat_date DESC LIMIT %d", 'checks', 'total', $limit ), ARRAY_A );
        return is_array( $rows ) ? $rows : array();
    }

    /** Reset aggregate rows only. */
    public function reset() {
        $deleted = $this->wpdb->query( "DELETE FROM {$this->table}" ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared -- Controlled table, intentionally all aggregate rows.
        if ( false !== $deleted ) { delete_transient( 'jsc_public_trends' ); }
        return false !== $deleted;
    }
}
