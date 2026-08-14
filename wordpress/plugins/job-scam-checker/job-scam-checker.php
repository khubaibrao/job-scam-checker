<?php
/**
 * Plugin Name:       Job Scam Checker
 * Plugin URI:        https://example.com/job-scam-checker
 * Description:       Privacy-first, rule-based job scam checking tools and site foundations.
 * Version:           0.3.0
 * Requires at least: 6.4
 * Requires PHP:      7.4
 * Author:            Job Scam Checker
 * License:           GPL-2.0-or-later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       job-scam-checker
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

define( 'JSC_VERSION', '0.3.0' );
define( 'JSC_DB_VERSION', '1.0.0' );
define( 'JSC_PLUGIN_FILE', __FILE__ );
define( 'JSC_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'JSC_PLUGIN_URL', plugin_dir_url( __FILE__ ) );

require_once JSC_PLUGIN_DIR . 'includes/class-jsc-content-installer.php';
require_once JSC_PLUGIN_DIR . 'includes/class-jsc-schema.php';
require_once JSC_PLUGIN_DIR . 'includes/class-jsc-rule-repository.php';
require_once JSC_PLUGIN_DIR . 'includes/class-jsc-link-analyzer.php';
require_once JSC_PLUGIN_DIR . 'includes/class-jsc-risk-calculator.php';
require_once JSC_PLUGIN_DIR . 'includes/class-jsc-recommendation-provider.php';
require_once JSC_PLUGIN_DIR . 'includes/class-jsc-rule-engine.php';
require_once JSC_PLUGIN_DIR . 'includes/class-jsc-rate-limiter.php';
require_once JSC_PLUGIN_DIR . 'includes/class-jsc-activator.php';
require_once JSC_PLUGIN_DIR . 'includes/class-jsc-plugin.php';
require_once JSC_PLUGIN_DIR . 'public/class-jsc-public.php';
require_once JSC_PLUGIN_DIR . 'public/class-jsc-rest-controller.php';

register_activation_hook( __FILE__, array( 'JSC_Activator', 'activate' ) );

/**
 * Start the plugin after all active plugins are loaded.
 */
function jsc_run_plugin() {
    $plugin = new JSC_Plugin();
    $plugin->run();
}

add_action( 'plugins_loaded', 'jsc_run_plugin' );
