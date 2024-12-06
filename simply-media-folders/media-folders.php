<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://github.com/Wendivoid/SimplyMediaFolders
 * @since             0.1.0
 * @package           Simply_Media_Folders
 *
 * @wordpress-plugin
 * Plugin Name:       Simply Media Folders
 * Plugin URI:        https://github.com/Wendivoid/SimplyMediaFolders
 * Description:       Simply adds folders to the WordPress media library.
 * Version:           0.1.0
 * Author:            Patrick Greene
 * Author URI:        https://github.com/Wendivoid
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       simply-media-folders
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Currently plugin version.
 */
define( 'SIMPLY_MEDIA_FOLDERS_VERSION', '0.1.0' );

/**
 * The code that runs during plugin activation.
 */
function activate_simply_media_folders() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-simply-media-folders-activator.php';
	Simply_Media_Folders_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 */
function uninstall_simply_media_folders() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-simply-media-folders-uninstaller.php';
	Simply_Media_Folders_Uninstaller::uninstall();
}

register_activation_hook( __FILE__, 'activate_simply_media_folders' );
register_uninstall_hook( __FILE__, 'uninstall_simply_media_folders' );

/**
 * The core plugin class that is used to define admin-specific hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-simply-media-folders.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    0.1.0
 */
function run_simply_media_folders() {

	$plugin = new Simply_Media_Folders();
	$plugin->run();

}
run_simply_media_folders();
