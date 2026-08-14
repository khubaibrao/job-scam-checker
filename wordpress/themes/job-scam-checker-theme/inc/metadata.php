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
    } elseif ( is_singular() && has_excerpt() ) {
        $description = get_the_excerpt();
    }

    $description = wp_strip_all_tags( $description );
    if ( '' === $description ) {
        return;
    }
    ?>
    <meta name="description" content="<?php echo esc_attr( $description ); ?>">
    <meta property="og:title" content="<?php echo esc_attr( $title ); ?>">
    <meta property="og:description" content="<?php echo esc_attr( $description ); ?>">
    <meta property="og:type" content="<?php echo is_singular( 'post' ) ? 'article' : 'website'; ?>">
    <meta property="og:site_name" content="<?php echo esc_attr( $site_name ); ?>">
    <meta property="og:url" content="<?php echo esc_url( jsc_theme_current_url() ); ?>">
    <?php
}
add_action( 'wp_head', 'jsc_theme_metadata', 2 );

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
