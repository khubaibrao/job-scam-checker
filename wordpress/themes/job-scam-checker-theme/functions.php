<?php
/**
 * Theme bootstrap.
 *
 * @package JobScamCheckerTheme
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

define( 'JSC_THEME_VERSION', '0.4.0' );

require_once get_template_directory() . '/inc/setup.php';
require_once get_template_directory() . '/inc/assets.php';
require_once get_template_directory() . '/inc/metadata.php';
require_once get_template_directory() . '/inc/content-navigation.php';
require_once get_template_directory() . '/inc/structured-data.php';
