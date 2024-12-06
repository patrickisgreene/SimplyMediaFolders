<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @since      0.1.0
 *
 * @package    Simply_Media_Folders
 * @subpackage Simply_Media_Folders/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Simply_Media_Folders
 * @subpackage Simply_Media_Folders/admin
 * @author     Patrick Greene <patrickisgreene@gmail.com>
 */
class Simply_Media_Folders_Admin {
    /**
	 * The WordPress database object.
	 *
	 * @since    0.1.0
	 * @access   private
	 * @var      array    $db    The $wpdb wordpress object.
	 */
	private $db;
	/**
	 * The ID of this plugin.
	 *
	 * @since    0.1.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    0.1.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since      0.1.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 * @return     void
	 */
	public function __construct( $plugin_name, $version ) {
        global $wpdb;
		$this->db = $wpdb;
		$this->plugin_name = $plugin_name;
		$this->version = $version;
	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    0.1.0
	 * @return   void
	 */
	public function enqueue_styles($hook) {
		$this->enqueue_style('jqtree', [], 'all');
		$this->enqueue_style('lib', array($this->plugin_name . '-jqtree'));
		
		$deps = array($this->plugin_name . '-lib', $this->plugin_name . '-jqtree');
		if($hook === "upload.php") {
			$this->enqueue_style('upload', $deps);
		} else {
			$this->enqueue_style('modal', $deps);
		}
	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    0.1.0
	 * @return   void
	 */
	public function enqueue_scripts(string $hook) {
		$this->enqueue_script('jqtree');
		$this->enqueue_script('lib', array($this->plugin_name . '-jqtree'));
		$this->localize_lib_script();

		if($hook === "upload.php") {
			$this->enqueue_script('upload', array($this->plugin_name . '-jqtree', 'jquery-ui-draggable', 'jquery-ui-droppable'));
		} else {
			$this->enqueue_script('modal', array($this->plugin_name . '-jqtree'));
		}
	}

	/**
	 * New media item uploaded callback.
	 *
	 * @since    0.1.0
	 * @return   void
	 */
	public function item_uploaded($item_id) {
        if(isset($_POST['nonce']) && isset($_REQUEST['folder_id'])) {
			wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['nonce'])), "smf-nonce");
            $this->db->insert(
				$this->db->prefix . 'simply_media_folder_items',
				array(
					'media_id' => $item_id,
					'folder_id' => sanitize_text_field(wp_unslash($_REQUEST['folder_id']))
				)
			);
		}
	}

	/**
	 * Helper function to enqueue a javascript file.
	 *
	 * @since    0.1.0
	 * @param    $entry    The file name (minus .js) to include from the admin js directory.
	 * @param    $deps     Dependencies to load. (jquery, wp-element) are included automatically.
	 * @param    $version   wp_enqueue_script version argument.
	 * @return   void
	 */
	private function enqueue_script($entry, $args = [], $version = false) {
		$deps = array('jquery', 'wp-element');
		wp_enqueue_script(
			$this->plugin_name . '-' . $entry,
			plugins_url() . '/simply-media-folders/js/' . $entry . '.js',
			array_merge($deps, $args),
			$this->version,
			$version
		);
	}

	/**
	 * Helper function to enqueue a Less file.
	 *
	 * @since    0.1.0
	 * @param    $entry    The file name (minus .less) to include from the admin js directory.
	 * @param    $deps     Dependencies to load.
	 * @return   void
	 */
	private function enqueue_style($entry, $args = []) {
		wp_enqueue_style(
			$this->plugin_name . '-' . $entry,
			plugins_url() . '/simply-media-folders/css/' . $entry . '.css',
			$args,
			$this->version,
			'all'
		);
	}

	/**
	 * Add the javascript localization object.
	 *
	 * @since    0.1.0
	 * @return   void
	 */
	private function localize_lib_script() {
		wp_localize_script( $this->plugin_name . '-lib', 'simplyMediaFoldersConfig',
	        [
				'selected' => null,
				'nonce' => wp_create_nonce("smf-nonce"),
				'auto_open' => get_option('simply_media_folders_auto_open'),
				'filter_enabled' => get_option('simply_media_folders_filter_enabled'),
				'default_color' => get_option('simply_media_folders_default_color'),
				'library_mode' => get_user_option('media_library_mode', get_current_user_id())
		    ]
		);
	}

}
