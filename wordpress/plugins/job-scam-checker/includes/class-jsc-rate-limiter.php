<?php
/**
 * Small transient-backed public endpoint rate limiter.
 *
 * @package JobScamChecker
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class JSC_Rate_Limiter {
    const LIMIT = 10;
    const WINDOW = 60;

    /**
     * Consume one request for the current network address.
     *
     * Stores only a salted one-way identifier and a counter, never message text.
     *
     * @return bool True when allowed.
     */
    public function consume( $scope = 'checker', $limit = self::LIMIT, $window = self::WINDOW ) {
        $address = isset( $_SERVER['REMOTE_ADDR'] ) ? trim( (string) $_SERVER['REMOTE_ADDR'] ) : 'unknown';
        if ( false === filter_var( $address, FILTER_VALIDATE_IP ) ) {
            $address = 'unknown';
        }
        $scope  = sanitize_key( $scope );
        $limit  = min( 120, max( 1, (int) $limit ) );
        $window = min( HOUR_IN_SECONDS, max( 10, (int) $window ) );
        $key    = 'jsc_rate_' . $scope . '_' . hash_hmac( 'sha256', $address, wp_salt( 'nonce' ) );
        $count   = (int) get_transient( $key );

        if ( $count >= $limit ) {
            return false;
        }

        set_transient( $key, $count + 1, $window );
        return true;
    }
}
