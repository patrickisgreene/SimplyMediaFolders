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
 * @subpackage Simply_Media_Folders/includes
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
 * @subpackage Simply_Media_Folders/includes
 * @author     Patrick Greene <patrickisgreene@gmail.com>
 */
class Simply_Media_Folders {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    0.1.0
	 * @access   protected
	 * @var      Simply_Media_Folders_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    0.1.0
	 * @access   protected
	 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    0.1.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    0.1.0
	 */
	public function __construct() {
		if ( defined( 'SIMPLY_MEDIA_FOLDERS_VERSION' ) ) {
			$this->version = SIMPLY_MEDIA_FOLDERS_VERSION;
		} else {
			$this->version = '0.1.0';
		}
		$this->plugin_name = 'simply-media-folders';
        
		$this->load_dependencies();
		$this->define_admin_hooks();
	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - Simply_Media_Folders_Loader. Orchestrates the hooks of the plugin.
	 * - Simply_Media_Folders_Admin. Defines all hooks for the admin area.
	 * - Simply_Media_Folders_Folders_Table. Defines logic for manipulating the folders table.
	 * - Simply_Media_Folders_Items_Filter. Defines the filters applied to the ajax media calls.
	 * - Simply_Media_Folders_Items_Table. Defines logic for the manipulating the folder items table.
	 * - Simply_Media_Folders_Options. Defines logic for changing option values.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    0.1.0
	 * @access   private
	 */
	private function load_dependencies() {

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-simply-media-folders-loader.php';

		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-simply-media-folders-folders-table.php';

		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-simply-media-folders-items-table.php';

		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-simply-media-folders-options.php';

		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-simply-media-folders-items-filter.php';

		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-simply-media-folders-admin.php';

		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'integrations/class-simply-media-folders-elementor-website-builder.php';

		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'integrations/class-simply-media-folders-beaver-builder.php';

		$this->loader = new Simply_Media_Folders_Loader();

	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    0.1.0
	 * @access   private
	 */
	private function define_admin_hooks() {

		$plugin_admin = new Simply_Media_Folders_Admin($this->get_plugin_name(), $this->get_version());
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );
		$this->loader->add_action( 'add_attachment', $plugin_admin, 'item_uploaded');
		
		$folders_table = new Simply_Media_Folders_Folders_Table();
		$this->loader->add_action( 'wp_ajax_simply_media_folders_list', $folders_table, 'list' );
		$this->loader->add_action( 'wp_ajax_simply_media_folders_create', $folders_table, 'create');
		$this->loader->add_action( 'wp_ajax_simply_media_folders_delete', $folders_table, 'delete');
		$this->loader->add_action( 'wp_ajax_simply_media_folders_update', $folders_table, 'update');
		
		$items_table = new Simply_Media_Folders_Items_Table();
		$this->loader->add_action( 'wp_ajax_simply_media_folders_attach', $items_table, 'attach');
		
		$options = new Simply_Media_Folders_Options();
		$this->loader->add_action( 'wp_ajax_simply_media_folders_options', $options, 'update');
		
		$filter = new Simply_Media_Folders_Items_Filter();
        $this->loader->add_filter( 'ajax_query_attachments_args', $filter, 'filterGrid', 20);
		$this->loader->add_filter( 'posts_clauses', $filter, 'filterList', 10, 2);
		
		new Simply_Media_Folders_Elementor_Website_Builder($this->loader, $plugin_admin);

		new Simply_Media_Folders_Beaver_Builder($this->loader, $plugin_admin);
	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    0.1.0
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     0.1.0
	 * @return    string    The name of the plugin.
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     0.1.0
	 * @return    Simply_Media_Folders_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     0.1.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}
}
