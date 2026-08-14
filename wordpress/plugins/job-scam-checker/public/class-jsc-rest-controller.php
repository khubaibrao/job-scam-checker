<?php
/**
 * Same-origin checker API.
 *
 * @package JobScamChecker
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class JSC_REST_Controller {
    const MAX_CHARACTERS = 12000;
    const MAX_BYTES      = 24000;

    /**
     * Register endpoint on the REST API.
     */
    public function register_hooks() {
        add_action( 'rest_api_init', array( $this, 'register_routes' ) );
    }

    public function register_routes() {
        register_rest_route(
            'job-scam-checker/v1',
            '/analyze',
            array(
                'methods'             => WP_REST_Server::CREATABLE,
                'callback'            => array( $this, 'analyze' ),
                'permission_callback' => '__return_true',
            )
        );
    }

    /**
     * Validate, analyze and immediately discard the submitted message.
     *
     * @param WP_REST_Request $request Request object.
     * @return WP_REST_Response|WP_Error
     */
    public function analyze( $request ) {
        $nonce = $request->get_header( 'X-JSC-Nonce' );
        if ( ! $nonce || ! wp_verify_nonce( $nonce, 'jsc_analyze_message' ) ) {
            return new WP_Error( 'jsc_invalid_nonce', __( 'Security check failed. Refresh the page and try again.', 'job-scam-checker' ), array( 'status' => 403 ) );
        }

        $content_length = (int) $request->get_header( 'Content-Length' );
        if ( $content_length > self::MAX_BYTES + 2048 ) {
            return new WP_Error( 'jsc_request_too_large', __( 'The submitted message is too large.', 'job-scam-checker' ), array( 'status' => 413 ) );
        }

        $message = $request->get_param( 'message' );
        if ( ! is_string( $message ) ) {
            return new WP_Error( 'jsc_invalid_message', __( 'Enter a message to check.', 'job-scam-checker' ), array( 'status' => 400 ) );
        }
        $message = wp_unslash( $message );

        $character_count = function_exists( 'mb_strlen' ) ? mb_strlen( $message, 'UTF-8' ) : strlen( $message );
        if ( strlen( $message ) > self::MAX_BYTES || $character_count > self::MAX_CHARACTERS ) {
            return new WP_Error( 'jsc_message_too_long', __( 'Keep the message under 12,000 characters.', 'job-scam-checker' ), array( 'status' => 413 ) );
        }
        if ( $character_count < 10 || '' === trim( wp_strip_all_tags( $message ) ) ) {
            return new WP_Error( 'jsc_message_too_short', __( 'Enter at least 10 characters so the checker has enough context.', 'job-scam-checker' ), array( 'status' => 400 ) );
        }

        $limiter = new JSC_Rate_Limiter();
        if ( ! $limiter->consume() ) {
            return new WP_Error( 'jsc_rate_limited', __( 'Too many checks were requested. Wait a minute and try again.', 'job-scam-checker' ), array( 'status' => 429 ) );
        }

        global $wpdb;
        $repository = new JSC_Rule_Repository( $wpdb );
        $engine     = new JSC_Rule_Engine();
        $result     = $engine->analyze( $message, $repository->get_enabled_rules() );

        unset( $message );
        nocache_headers();
        return new WP_REST_Response( $result, 200 );
    }
}
