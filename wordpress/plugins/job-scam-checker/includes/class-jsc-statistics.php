<?php
/**
 * Privacy-safe statistics policy and collection service.
 *
 * @package JobScamChecker
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class JSC_Statistics {
    const TOKEN_TTL = 900;

    public static function enabled() {
        return '1' === (string) get_option( 'jsc_statistics_enabled', '0' );
    }

    public static function follow_up_enabled() {
        return self::enabled() && '1' === (string) get_option( 'jsc_follow_up_enabled', '1' );
    }

    public static function retention_days() {
        return min( 730, max( 30, (int) get_option( 'jsc_statistics_retention_days', 365 ) ) );
    }

    /** Record only a result level and safe rule slugs; return a one-use token. */
    public function record_analysis( array $result ) {
        if ( ! self::enabled() ) {
            return '';
        }
        global $wpdb;
        $counters = array(
            array( 'metric' => 'checks', 'stat_key' => 'total' ),
            array( 'metric' => 'risk_level', 'stat_key' => sanitize_key( $result['level']['key'] ?? 'unknown' ) ),
        );
        foreach ( $result['detections'] ?? array() as $detection ) {
            if ( ! empty( $detection['slug'] ) ) {
                $counters[] = array( 'metric' => 'detection', 'stat_key' => sanitize_key( $detection['slug'] ) );
            }
        }
        ( new JSC_Statistics_Repository( $wpdb ) )->increment_many( $counters );

        if ( ! self::follow_up_enabled() ) {
            return '';
        }
        $token = wp_generate_password( 32, false, false );
        set_transient( 'jsc_feedback_' . hash( 'sha256', $token ), 1, self::TOKEN_TTL );
        return $token;
    }

    /** Validate and aggregate a one-use follow-up submission. */
    public function record_follow_up( $token, $channel, $money_requested, $payment_purpose ) {
        if ( ! self::follow_up_enabled() || ! is_string( $token ) || strlen( $token ) < 20 ) {
            return new WP_Error( 'jsc_feedback_disabled', __( 'Anonymous follow-up collection is unavailable.', 'job-scam-checker' ), array( 'status' => 400 ) );
        }
        $token_key = 'jsc_feedback_' . hash( 'sha256', $token );
        if ( ! get_transient( $token_key ) ) {
            return new WP_Error( 'jsc_feedback_duplicate', __( 'This optional follow-up was already submitted or has expired.', 'job-scam-checker' ), array( 'status' => 409 ) );
        }

        $channels = array( 'whatsapp', 'telegram', 'sms', 'email', 'linkedin', 'facebook', 'job_board', 'other' );
        $purposes = array( 'training', 'equipment', 'registration', 'task_deposit', 'cryptocurrency', 'gift_cards', 'other' );
        $channel  = sanitize_key( $channel );
        $money    = sanitize_key( $money_requested );
        $purpose  = sanitize_key( $payment_purpose );
        if ( ! in_array( $channel, $channels, true ) || ! in_array( $money, array( 'yes', 'no' ), true ) || ( 'yes' === $money && ! in_array( $purpose, $purposes, true ) ) || ( 'no' === $money && '' !== $purpose ) ) {
            return new WP_Error( 'jsc_invalid_feedback', __( 'Choose a valid answer for each follow-up question.', 'job-scam-checker' ), array( 'status' => 400 ) );
        }

        delete_transient( $token_key );
        $counters = array(
            array( 'metric' => 'channel', 'stat_key' => $channel ),
            array( 'metric' => 'money_request', 'stat_key' => $money ),
        );
        if ( 'yes' === $money ) {
            $counters[] = array( 'metric' => 'payment_purpose', 'stat_key' => $purpose );
        }
        global $wpdb;
        ( new JSC_Statistics_Repository( $wpdb ) )->increment_many( $counters );
        return true;
    }
}
