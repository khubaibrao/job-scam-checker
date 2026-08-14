<?php
/**
 * Local rule-based message analysis.
 *
 * @package JobScamChecker
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class JSC_Rule_Engine {
    /** @var JSC_Link_Analyzer */
    private $link_analyzer;

    /** @var JSC_Risk_Calculator */
    private $calculator;

    public function __construct( JSC_Link_Analyzer $link_analyzer = null, JSC_Risk_Calculator $calculator = null ) {
        $this->link_analyzer = $link_analyzer ?: new JSC_Link_Analyzer();
        $this->calculator    = $calculator ?: new JSC_Risk_Calculator();
    }

    /**
     * Analyze text against enabled rules. Text is never persisted.
     *
     * @param string                          $message Plain visitor message.
     * @param array<int,array<string,mixed>> $rules Enabled rule definitions.
     * @return array<string,mixed>
     */
    public function analyze( $message, array $rules ) {
        $plain      = html_entity_decode( wp_strip_all_tags( (string) $message, true ), ENT_QUOTES | ENT_HTML5, 'UTF-8' );
        $normalized = $this->normalize( $plain );
        $links      = $this->link_analyzer->extract( $plain );
        $matches    = array();

        foreach ( $rules as $rule ) {
            $evidence = $this->match_rule( $rule, $plain, $normalized, $links );
            if ( false === $evidence ) {
                continue;
            }

            $matches[] = array(
                'id'             => isset( $rule['id'] ) ? (int) $rule['id'] : 0,
                'slug'           => sanitize_key( $rule['slug'] ),
                'name'           => sanitize_text_field( $rule['name'] ),
                'category'       => sanitize_key( $rule['category'] ),
                'score_group'    => sanitize_key( $rule['score_group'] ),
                'weight'         => min( 100, max( 0, (int) $rule['weight'] ) ),
                'explanation'    => sanitize_text_field( $rule['explanation'] ),
                'recommendation' => sanitize_text_field( $rule['recommendation'] ),
                'evidence'       => sanitize_text_field( $evidence ),
            );
        }

        $result                    = $this->calculator->calculate( $matches );
        $result['warning_count']   = count( $result['detections'] );
        $result['suspicious_links'] = $this->public_link_findings( $links );
        $result['disclaimer']      = __( 'This automated check cannot confirm whether an offer is legitimate or fraudulent. Verify independently before proceeding.', 'job-scam-checker' );

        return $result;
    }

    /**
     * @return string|false Short safe evidence label or false.
     */
    private function match_rule( array $rule, $plain, $normalized, array $links ) {
        $type    = $rule['match_type'];
        $pattern = $rule['pattern'];

        if ( 'phrase' === $type ) {
            foreach ( $this->decode_list( $pattern ) as $phrase ) {
                $normalized_phrase = $this->normalize( $phrase );
                if ( $this->contains_phrase( $normalized, $normalized_phrase ) && ! $this->is_safety_statement( $normalized, $normalized_phrase, $rule['category'] ) ) {
                    return $phrase;
                }
            }
        }

        if ( 'regex' === $type && $this->safe_regex_match( $pattern, $plain, $regex_match ) ) {
            return $regex_match;
        }

        if ( 'contextual' === $type ) {
            $groups = json_decode( $pattern, true );
            if ( is_array( $groups ) && $this->context_groups_match( $normalized, $groups ) ) {
                return __( 'Multiple related phrases appeared together', 'job-scam-checker' );
            }
        }

        if ( in_array( $type, array( 'shortened_url', 'messaging_url', 'free_hosting_url', 'suspicious_tld', 'punycode_url', 'ip_url' ), true ) ) {
            $property = array(
                'shortened_url' => 'shortened',
                'messaging_url' => 'messaging',
                'free_hosting_url' => 'free_hosting',
                'suspicious_tld' => 'suspicious_tld',
                'punycode_url' => 'punycode',
                'ip_url' => 'ip_address',
            )[ $type ];
            foreach ( $links as $link ) {
                if ( ! empty( $link[ $property ] ) ) {
                    return $link['domain'];
                }
            }
        }

        return false;
    }

    private function normalize( $text ) {
        $text = function_exists( 'mb_strtolower' ) ? mb_strtolower( $text, 'UTF-8' ) : strtolower( $text );
        $text = preg_replace( '/[^\p{L}\p{N}@.$%+\/-]+/u', ' ', $text );
        return trim( preg_replace( '/\s+/u', ' ', $text ) );
    }

    /**
     * @return array<int,string>
     */
    private function decode_list( $pattern ) {
        $decoded = json_decode( $pattern, true );
        return is_array( $decoded ) ? array_values( array_filter( $decoded, 'is_string' ) ) : array( (string) $pattern );
    }

    private function contains_phrase( $text, $phrase ) {
        if ( '' === $phrase ) {
            return false;
        }
        return (bool) preg_match( '/(?<![\p{L}\p{N}])' . preg_quote( $phrase, '/' ) . '(?![\p{L}\p{N}])/u', $text );
    }

    /**
     * Avoid scoring common employer warnings such as "we will never ask you to
     * pay a fee" as though the message requested the prohibited action.
     */
    private function is_safety_statement( $text, $phrase, $category ) {
        if ( ! in_array( $category, array( 'payment', 'credentials', 'identity' ), true ) ) {
            return false;
        }
        $position = strpos( $text, $phrase );
        if ( false === $position ) {
            return false;
        }
        $prefix = substr( $text, max( 0, $position - 200 ), min( 200, $position ) );
        return (bool) preg_match( '/(?:never|do not|does not|will not|won.t|should not|no legitimate employer)[^.!?\n]{0,180}$/u', $prefix );
    }

    /**
     * Context JSON is an array of groups. At least one phrase from every group
     * must occur, allowing expressive combinations without executable patterns.
     */
    private function context_groups_match( $text, array $groups ) {
        foreach ( $groups as $alternatives ) {
            if ( ! is_array( $alternatives ) ) {
                return false;
            }
            $matched = false;
            foreach ( $alternatives as $phrase ) {
                if ( is_string( $phrase ) && $this->contains_phrase( $text, $this->normalize( $phrase ) ) ) {
                    $matched = true;
                    break;
                }
            }
            if ( ! $matched ) {
                return false;
            }
        }
        return ! empty( $groups );
    }

    /**
     * Run only bounded, internally/admin-defined regex strings.
     */
    private function safe_regex_match( $pattern, $text, &$evidence ) {
        $evidence = '';
        if ( ! is_string( $pattern ) || strlen( $pattern ) > 500 || strlen( $text ) > 24000 ) {
            return false;
        }
        $matched = @preg_match( $pattern, $text, $matches ); // phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged -- Invalid admin regex must fail closed.
        if ( 1 !== $matched ) {
            return false;
        }
        $evidence = substr( wp_strip_all_tags( $matches[0] ), 0, 120 );
        return true;
    }

    /**
     * Return domains and flags, never active clickable URLs or query strings.
     */
    private function public_link_findings( array $links ) {
        $findings = array();
        foreach ( $links as $link ) {
            if ( ! $link['shortened'] && ! $link['messaging'] && ! $link['free_hosting'] && ! $link['suspicious_tld'] && ! $link['punycode'] && ! $link['ip_address'] ) {
                continue;
            }
            $findings[] = array(
                'domain'         => $link['domain'],
                'shortened'      => $link['shortened'],
                'messaging'      => $link['messaging'],
                'free_hosting'   => $link['free_hosting'],
                'suspicious_tld' => $link['suspicious_tld'],
                'punycode'       => $link['punycode'],
                'ip_address'     => $link['ip_address'],
            );
        }
        return $findings;
    }
}
