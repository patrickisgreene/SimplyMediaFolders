<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @since      0.1.0
 *
 * @package    Simply_Media_Folders
 * @subpackage Simply_Media_Folders/integrations
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      0.1.0
 * @package    Simply_Media_Folders
 * @subpackage Simply_Media_Folders/integrations
 * @author     Patrick Greene <patrickisgreene@gmail.com>
 */
class Simply_Media_Folders_Beaver_Builder {

	/**
	 * Define the core functionality of the plugin.
	 *
	 * @since    0.1.0
	 */
	public function __construct($loader, $admin_plugin) {
		if(class_exists('FLBuilderLoader')) {
    		Self::registerForBeaverBuilder($loader, $admin_plugin);
		}
	}

	/**
	 * Register hooks for the beaver page builder.
	 *
	 * @since    0.1.0
     * @param    Simply_Media_Folders_Loader
	 * @param    Simply_Media_Folders_Admin   $admin_plugin   The name of the WordPress action that is being registered.
	 * @return   void
	 */
	private static function registerForBeaverBuilder($loader, $admin_plugin) {
		$loader->add_action( 'fl_before_sortable_enqueue', $admin_plugin, 'enqueue_scripts' );
		$loader->add_action( 'fl_before_sortable_enqueue', $admin_plugin, 'enqueue_styles' );
	}

}
