<?php
/** Native page search tuned for the curated Job Scam Checker library. */
if ( ! defined( 'ABSPATH' ) ) { exit; }
function jsc_theme_search_query( $query ) {
    if ( is_admin() || ! $query->is_main_query() || ! $query->is_search() ) { return; }
    $query->set( 'post_type', 'page' );
    $query->set( 'posts_per_page', 12 );
    $filter  = '0' !== (string) get_option( 'jsc_search_filters_enabled', '1' ) && isset( $_GET['content_type'] ) ? sanitize_key( wp_unslash( $_GET['content_type'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Read-only public filter.
    $allowed = array( 'tool', 'hub', 'scam_article', 'guide', 'trust', 'legal' );
    if ( in_array( $filter, $allowed, true ) ) { $query->set( 'meta_query', array( array( 'key' => '_jsc_content_type', 'value' => $filter ) ) ); }
}
add_action( 'pre_get_posts', 'jsc_theme_search_query' );
function jsc_theme_search_excerpt( $excerpt, $post ) {
    if ( is_search() && $post instanceof WP_Post ) { $description = get_post_meta( $post->ID, '_jsc_seo_description', true ); if ( $description ) { return wp_strip_all_tags( $description ); } }
    return $excerpt;
}
add_filter( 'get_the_excerpt', 'jsc_theme_search_excerpt', 10, 2 );
function jsc_theme_content_type_label( $type ) {
    $labels = array( 'tool' => __( 'Checker tool', 'job-scam-checker-theme' ), 'hub' => __( 'Topic hub', 'job-scam-checker-theme' ), 'scam_article' => __( 'Scam type', 'job-scam-checker-theme' ), 'guide' => __( 'Guide', 'job-scam-checker-theme' ), 'trust' => __( 'Site information', 'job-scam-checker-theme' ), 'legal' => __( 'Legal information', 'job-scam-checker-theme' ) );
    return $labels[ $type ] ?? __( 'Information', 'job-scam-checker-theme' );
}
