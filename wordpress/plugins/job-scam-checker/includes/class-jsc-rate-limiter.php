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
    public function consume() {
        $address = isset( $_SERVER['REMOTE_ADDR'] ) ? (string) $_SERVER['REMOTE_ADDR'] : 'unknown';
        $key     = 'jsc_rate_' . hash_hmac( 'sha256', $address, wp_salt( 'nonce' ) );
        $count   = (int) get_transient( $key );

        if ( $count >= self::LIMIT ) {
            return false;
        }

        set_transient( $key, $count + 1, self::WINDOW );
        return true;
    }
}
