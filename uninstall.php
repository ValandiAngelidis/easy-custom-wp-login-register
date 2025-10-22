<?php
/**
 * Uninstall handler for Easy Custom WP Login Register
 *
 * This file is executed when the plugin is uninstalled via the WordPress UI.
 */

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
    exit;
}

// Remove saved plugin settings
delete_option( 'ecwpr_settings' );

// If network-activated, also remove the site option
if ( function_exists( 'is_multisite' ) && is_multisite() ) {
    delete_site_option( 'ecwpr_settings' );
}

// No usermeta keys to delete.
