<?php
/**
 * Lightweight baseline metadata.
 *
 * WordPress owns titles and canonical URLs. This adds descriptions and basic
 * Open Graph data without requiring an SEO plugin; established SEO plugins can
 * take over later without duplicated output.
 *
 * @package JobScamCheckerTheme
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Print safe metadata when a known SEO plugin is not active.
 */
function jsc_theme_metadata() {
    if ( defined( 'WPSEO_VERSION' ) || defined( 'RANK_MATH_VERSION' ) ) {
        return;
    }

    $site_name   = get_bloginfo( 'name' );
    $title       = wp_get_document_title();
    $description = get_bloginfo( 'description' );

    if ( is_front_page() ) {
        $description = __( 'Check suspicious job offers and recruiter messages for common scam warning signs. Free, instant and privacy-focused.', 'job-scam-checker-theme' );
    } elseif ( is_singular() ) {
        $custom_description = get_post_meta( get_queried_object_id(), '_jsc_seo_description', true );
        if ( $custom_description ) {
            $description = $custom_description;
        } elseif ( has_excerpt() ) {
            $description = get_the_excerpt();
        }
    }

    $description = wp_strip_all_tags( $description );
    if ( '' === $description ) {
        return;
    }
    ?>
    <meta name="description" content="<?php echo esc_attr( $description ); ?>">
    <meta property="og:title" content="<?php echo esc_attr( $title ); ?>">
    <meta property="og:description" content="<?php echo esc_attr( $description ); ?>">
    <?php $content_type = is_singular() ? get_post_meta( get_queried_object_id(), '_jsc_content_type', true ) : ''; ?>
    <meta property="og:type" content="<?php echo esc_attr( in_array( $content_type, array( 'scam_article', 'guide' ), true ) ? 'article' : 'website' ); ?>">
    <meta property="og:site_name" content="<?php echo esc_attr( $site_name ); ?>">
    <meta property="og:url" content="<?php echo esc_url( jsc_theme_current_url() ); ?>">
    <?php
}
add_action( 'wp_head', 'jsc_theme_metadata', 2 );

/**
 * Apply the curated SEO title without replacing WordPress title handling.
 *
 * @param array<string,string> $parts Document title parts.
 * @return array<string,string>
 */
function jsc_theme_document_title( $parts ) {
    if ( is_singular() ) {
        $custom_title = get_post_meta( get_queried_object_id(), '_jsc_seo_title', true );
        if ( $custom_title ) {
            $parts['title'] = $custom_title;
        }
    }
    return $parts;
}
add_filter( 'document_title_parts', 'jsc_theme_document_title' );

/**
 * Keep utility result pages out of search while allowing public content pages.
 *
 * @param array<string,bool|string> $robots Robot directives.
 * @return array<string,bool|string>
 */
function jsc_theme_robots_directives( $robots ) {
    if ( is_search() || is_404() ) {
        $robots['noindex'] = true;
        unset( $robots['index'] );
    }
    return $robots;
}
add_filter( 'wp_robots', 'jsc_theme_robots_directives' );

/**
 * Advertise the native WordPress sitemap in the virtual robots.txt response.
 *
 * @param string $output Existing robots.txt output.
 * @param bool   $public Whether search visibility is enabled.
 * @return string
 */
function jsc_theme_robots_txt( $output, $public ) {
    if ( $public && false === stripos( $output, 'Sitemap:' ) ) {
        $output .= "\nSitemap: " . home_url( '/wp-sitemap.xml' ) . "\n";
    }
    return $output;
}
add_filter( 'robots_txt', 'jsc_theme_robots_txt', 10, 2 );

/**
 * Build the current canonical-style URL from WordPress APIs.
 *
 * @return string
 */
function jsc_theme_current_url() {
    if ( is_front_page() ) {
        return home_url( '/' );
    }

    if ( is_singular() ) {
        return (string) get_permalink();
    }

    return home_url( add_query_arg( array(), $GLOBALS['wp']->request ?? '' ) );
}
