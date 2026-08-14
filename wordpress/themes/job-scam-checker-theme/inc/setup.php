<?php
/**
 * Theme setup.
 *
 * @package JobScamCheckerTheme
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Register supported WordPress features and menus.
 */
function jsc_theme_setup() {
    load_theme_textdomain( 'job-scam-checker-theme', get_template_directory() . '/languages' );
    add_theme_support( 'title-tag' );
    add_theme_support( 'post-thumbnails' );
    add_theme_support( 'responsive-embeds' );
    add_theme_support( 'wp-block-styles' );
    add_theme_support( 'editor-styles' );
    add_theme_support(
        'html5',
        array( 'search-form', 'comment-form', 'comment-list', 'gallery', 'caption', 'style', 'script' )
    );
    add_theme_support(
        'custom-logo',
        array(
            'height'      => 48,
            'width'       => 240,
            'flex-height' => true,
            'flex-width'  => true,
        )
    );

    register_nav_menus(
        array(
            'primary' => __( 'Primary navigation', 'job-scam-checker-theme' ),
            'footer'  => __( 'Footer navigation', 'job-scam-checker-theme' ),
        )
    );
}
add_action( 'after_setup_theme', 'jsc_theme_setup' );

/**
 * Set a readable content width for embeds and media.
 */
function jsc_theme_content_width() {
    $GLOBALS['content_width'] = 760;
}
add_action( 'after_setup_theme', 'jsc_theme_content_width', 0 );
