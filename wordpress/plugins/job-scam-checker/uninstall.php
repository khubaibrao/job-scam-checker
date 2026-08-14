<?php
/**
 * Preserve content by default when the plugin is removed.
 *
 * Phase 1 stores only version and installed-page references. Deliberately avoid
 * deleting pages because they may have been edited by a site administrator.
 *
 * @package JobScamChecker
 */

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
    exit;
}

delete_option( 'jsc_version' );
delete_option( 'jsc_installed_pages' );
