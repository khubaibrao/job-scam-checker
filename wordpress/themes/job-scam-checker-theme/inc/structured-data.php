<?php
/**
 * Honest structured data for editorial and navigation content.
 *
 * @package JobScamCheckerTheme
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Print Article and BreadcrumbList JSON-LD where supported by page content.
 */
function jsc_theme_structured_data() {
    if ( ! is_singular() || is_front_page() || defined( 'WPSEO_VERSION' ) || defined( 'RANK_MATH_VERSION' ) ) {
        return;
    }

    $post         = get_queried_object();
    $content_type = get_post_meta( $post->ID, '_jsc_content_type', true );
    $description  = get_post_meta( $post->ID, '_jsc_seo_description', true );
    $graphs       = array();

    if ( in_array( $content_type, array( 'scam_article', 'guide' ), true ) ) {
        $graphs[] = array(
            '@type'            => 'Article',
            '@id'              => get_permalink( $post ) . '#article',
            'headline'         => get_the_title( $post ),
            'description'      => wp_strip_all_tags( $description ),
            'datePublished'    => get_the_date( DATE_W3C, $post ),
            'dateModified'     => get_the_modified_date( DATE_W3C, $post ),
            'mainEntityOfPage' => get_permalink( $post ),
            'author'           => array( '@type' => 'Organization', 'name' => get_bloginfo( 'name' ), 'url' => home_url( '/' ) ),
            'publisher'        => array( '@type' => 'Organization', 'name' => get_bloginfo( 'name' ), 'url' => home_url( '/' ) ),
        );
    }

    $breadcrumbs = jsc_theme_breadcrumb_items();
    if ( $breadcrumbs ) {
        $list = array();
        foreach ( $breadcrumbs as $index => $item ) {
            $list[] = array( '@type' => 'ListItem', 'position' => $index + 1, 'name' => $item['label'], 'item' => $item['url'] );
        }
        $graphs[] = array( '@type' => 'BreadcrumbList', 'itemListElement' => $list );
    }

    if ( $graphs ) {
        echo '<script type="application/ld+json">' . wp_json_encode( array( '@context' => 'https://schema.org', '@graph' => $graphs ), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) . '</script>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- JSON encoded controlled schema data.
    }
}
add_action( 'wp_head', 'jsc_theme_structured_data', 20 );
