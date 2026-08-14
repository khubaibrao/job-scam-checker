<?php
/**
 * Theme asset loading.
 *
 * @package JobScamCheckerTheme
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Load local assets only. No render-blocking remote font or script requests.
 */
function jsc_theme_enqueue_assets() {
    wp_enqueue_style(
        'jsc-theme-base',
        get_template_directory_uri() . '/assets/css/base.css',
        array(),
        JSC_THEME_VERSION
    );
    wp_enqueue_style(
        'jsc-theme-components',
        get_template_directory_uri() . '/assets/css/components.css',
        array( 'jsc-theme-base' ),
        JSC_THEME_VERSION
    );
    wp_enqueue_script(
        'jsc-theme-navigation',
        get_template_directory_uri() . '/assets/js/navigation.js',
        array(),
        JSC_THEME_VERSION,
        true
    );
}
add_action( 'wp_enqueue_scripts', 'jsc_theme_enqueue_assets' );
