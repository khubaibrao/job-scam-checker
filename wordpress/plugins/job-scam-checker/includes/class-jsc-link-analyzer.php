<?php
/**
 * Safe URL extraction and classification.
 *
 * @package JobScamChecker
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class JSC_Link_Analyzer {
    /** @var array<int,string> */
    private $shorteners = array(
        'bit.ly', 'tinyurl.com', 't.co', 'goo.gl', 'is.gd', 'buff.ly',
        'cutt.ly', 'rb.gy', 'rebrand.ly', 'shorturl.at', 'tiny.one', 'ow.ly',
    );

    /** @var array<int,string> */
    private $messaging_domains = array(
        'wa.me', 'api.whatsapp.com', 't.me', 'telegram.me', 'signal.me',
    );

    /** @var array<int,string> */
    private $free_hosting_domains = array(
        '000webhostapp.com', 'infinityfreeapp.com', 'godaddysites.com',
        'wixsite.com', 'weebly.com', 'rf.gd',
    );

    /**
     * Extract URLs without fetching or opening them.
     *
     * @param string $text Message text.
     * @return array<int,array<string,mixed>>
     */
    public function extract( $text ) {
        preg_match_all( '~(?:(?:https?://)|(?:www\.))[a-z0-9][a-z0-9._\~:/?#\[\]@!$&\'()*+,;=%-]*~iu', $text, $matches );
        $links = array();

        foreach ( array_unique( $matches[0] ?? array() ) as $raw_url ) {
            $clean_url = rtrim( $raw_url, ".,;:!?)]}'\"" );
            $parse_url = 0 === stripos( $clean_url, 'www.' ) ? 'https://' . $clean_url : $clean_url;
            $host      = strtolower( (string) wp_parse_url( $parse_url, PHP_URL_HOST ) );
            $host      = preg_replace( '/^www\./', '', $host );

            if ( ! $host || strlen( $host ) > 253 || ! $this->is_valid_host( $host ) ) {
                continue;
            }

            $links[] = array(
                'url'                => esc_url_raw( $clean_url, array( 'http', 'https' ) ),
                'domain'             => $host,
                'shortened'          => $this->domain_matches( $host, $this->shorteners ),
                'messaging'          => $this->domain_matches( $host, $this->messaging_domains ),
                'free_hosting'       => $this->domain_matches( $host, $this->free_hosting_domains ),
                'suspicious_tld'     => (bool) preg_match( '/\.(?:top|xyz|click|work|cam|buzz|monster|quest|rest|gq|tk|ml|cf)$/i', $host ),
                'punycode'           => 0 === strpos( $host, 'xn--' ) || false !== strpos( $host, '.xn--' ),
                'ip_address'         => false !== filter_var( $host, FILTER_VALIDATE_IP ),
            );
        }

        return $links;
    }

    /**
     * @param string            $domain Domain to test.
     * @param array<int,string> $candidates Exact domains and their subdomains.
     * @return bool
     */
    private function domain_matches( $domain, array $candidates ) {
        foreach ( $candidates as $candidate ) {
            if ( $domain === $candidate || self::ends_with( $domain, '.' . $candidate ) ) {
                return true;
            }
        }
        return false;
    }

    /**
     * Reject malformed or control-character hostnames.
     */
    private function is_valid_host( $host ) {
        if ( false !== filter_var( $host, FILTER_VALIDATE_IP ) ) {
            return true;
        }
        return (bool) preg_match( '/^(?:[a-z0-9](?:[a-z0-9-]{0,61}[a-z0-9])?\.)+[a-z0-9][a-z0-9-]{1,62}$/i', $host );
    }

    private static function ends_with( $haystack, $needle ) {
        return '' === $needle || substr( $haystack, -strlen( $needle ) ) === $needle;
    }
}
