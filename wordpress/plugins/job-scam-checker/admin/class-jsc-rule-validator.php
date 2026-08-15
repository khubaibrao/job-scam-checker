<?php
/** Validation boundary for administrator-managed rule configuration. */

if ( ! defined( 'ABSPATH' ) ) { exit; }

class JSC_Rule_Validator {
    const MAX_PATTERN_LENGTH = 500;

    public static function match_types() {
        return array( 'phrase', 'regex', 'contextual', 'shortened_url', 'messaging_url', 'free_hosting_url', 'suspicious_tld', 'punycode_url', 'ip_url' );
    }

    public static function categories() {
        $defaults = array( 'payment', 'cryptocurrency', 'gift_cards', 'fake_check', 'equipment', 'task_scam', 'impersonation', 'communication', 'pressure', 'credentials', 'compensation', 'links', 'channel', 'hiring', 'identity', 'job_type' );
        $saved = get_option( 'jsc_rule_categories', array() );
        return array_values( array_unique( array_merge( $defaults, is_array( $saved ) ? array_map( 'sanitize_key', $saved ) : array() ) ) );
    }

    /** @return array<string,mixed>|WP_Error */
    public function validate( array $input, $existing_id = 0 ) {
        $name = sanitize_text_field( $input['name'] ?? '' );
        $slug = sanitize_key( $input['slug'] ?? '' );
        $type = sanitize_key( $input['match_type'] ?? '' );
        $category = sanitize_key( $input['category'] ?? '' );
        $group = sanitize_key( $input['score_group'] ?? '' );
        $pattern = trim( (string) ( $input['pattern'] ?? '' ) );
        $explanation = sanitize_textarea_field( $input['explanation'] ?? '' );
        $recommendation = sanitize_textarea_field( $input['recommendation'] ?? '' );
        $weight = filter_var( $input['weight'] ?? null, FILTER_VALIDATE_INT );
        $priority = filter_var( $input['priority'] ?? 100, FILTER_VALIDATE_INT );

        if ( '' === $name || strlen( $name ) > 190 || '' === $slug || strlen( $slug ) > 190 ) { return new WP_Error( 'jsc_invalid_identity', __( 'A rule needs a valid name and slug.', 'job-scam-checker' ) ); }
        if ( ! in_array( $type, self::match_types(), true ) ) { return new WP_Error( 'jsc_invalid_type', __( 'Choose a supported detection type.', 'job-scam-checker' ) ); }
        if ( ! in_array( $category, self::categories(), true ) || '' === $group || strlen( $group ) > 64 ) { return new WP_Error( 'jsc_invalid_category', __( 'Choose a valid category and scoring group.', 'job-scam-checker' ) ); }
        if ( false === $weight || $weight < 0 || $weight > 100 ) { return new WP_Error( 'jsc_invalid_weight', __( 'Risk weight must be a whole number from 0 to 100.', 'job-scam-checker' ) ); }
        if ( false === $priority || $priority < 0 || $priority > 9999 ) { return new WP_Error( 'jsc_invalid_priority', __( 'Priority must be a whole number from 0 to 9999.', 'job-scam-checker' ) ); }
        if ( '' === $explanation || '' === $recommendation ) { return new WP_Error( 'jsc_missing_guidance', __( 'Explanation and recommendation text are required.', 'job-scam-checker' ) ); }
        if ( strlen( $pattern ) > self::MAX_PATTERN_LENGTH ) { return new WP_Error( 'jsc_pattern_long', __( 'Detection configuration is too long.', 'job-scam-checker' ) ); }

        $automatic = in_array( $type, array( 'shortened_url', 'messaging_url', 'free_hosting_url', 'suspicious_tld', 'punycode_url', 'ip_url' ), true );
        if ( ! $automatic && '' === $pattern ) { return new WP_Error( 'jsc_pattern_empty', __( 'This detection type requires configuration.', 'job-scam-checker' ) ); }
        if ( 'phrase' === $type ) {
            $decoded = json_decode( $pattern, true );
            if ( null !== $decoded && ( ! is_array( $decoded ) || ! $this->string_list( $decoded ) ) ) { return new WP_Error( 'jsc_invalid_phrases', __( 'Phrase JSON must be a non-empty list of text phrases.', 'job-scam-checker' ) ); }
        }
        if ( 'contextual' === $type ) {
            $groups = json_decode( $pattern, true );
            if ( ! is_array( $groups ) || count( $groups ) < 2 ) { return new WP_Error( 'jsc_invalid_context', __( 'Contextual configuration needs at least two JSON phrase groups.', 'job-scam-checker' ) ); }
            foreach ( $groups as $group_items ) { if ( ! is_array( $group_items ) || ! $this->string_list( $group_items ) ) { return new WP_Error( 'jsc_invalid_context', __( 'Every contextual group must contain text phrases.', 'job-scam-checker' ) ); } }
        }
        if ( 'regex' === $type && ! $this->valid_regex( $pattern ) ) { return new WP_Error( 'jsc_invalid_regex', __( 'The regular expression is invalid or uses an unsafe construct.', 'job-scam-checker' ) ); }

        return array( 'name' => $name, 'slug' => $slug, 'match_type' => $type, 'pattern' => $automatic ? '' : $pattern, 'category' => $category, 'score_group' => $group, 'weight' => (int) $weight, 'explanation' => $explanation, 'recommendation' => $recommendation, 'enabled' => empty( $input['enabled'] ) ? 0 : 1, 'priority' => (int) $priority );
    }

    private function string_list( array $items ) {
        if ( empty( $items ) || count( $items ) > 50 ) { return false; }
        foreach ( $items as $item ) { if ( ! is_string( $item ) || '' === trim( $item ) || strlen( $item ) > 160 ) { return false; } }
        return true;
    }

    private function valid_regex( $pattern ) {
        if ( '' === $pattern || false !== strpos( $pattern, '(?{' ) || false !== strpos( $pattern, '(??{' ) || false !== strpos( $pattern, '\\C' ) ) { return false; }
        return false !== @preg_match( $pattern, '' ); // phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged -- Validation must fail closed.
    }
}
