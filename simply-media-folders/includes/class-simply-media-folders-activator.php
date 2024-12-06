<?php

/**
 * Fired during plugin activation
 *
 * @since      0.1.0
 *
 * @package    Simply_Media_Folders
 * @subpackage Simply_Media_Folders/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      0.1.0
 * @package    Simply_Media_Folders
 * @subpackage Simply_Media_Folders/includes
 * @author     Patrick Greene <patrickisgreene@gmail.com>
 */
class Simply_Media_Folders_Activator {
	private $charsetCollate;

	/**
	 * The generated name of the table to store folders in.
	 *
	 * @since    0.1.0
	 * @access   private
	 * @var      array    $tableName    The generated name of the table to store folders in.
	 */
	private $tableName;

	/**
	 * The generated name of the table to store folder items in.
	 *
	 * @since    0.1.0
	 * @access   private
	 * @var      array    $mapTableName    The generated name of the table to store folder items in.
	 */
	private $mapTableName;

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    0.1.0
	 * @return   void
	 */
	public function __construct() {
		global $wpdb;
		$this->charsetCollate = $wpdb->get_charset_collate();
		$this->tableName = $wpdb->prefix . "simply_media_folders";
		$this->mapTableName = $wpdb->prefix . "simply_media_folder_items";
	}

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    0.1.0
	 * @return   void
	 */
	public static function activate() {
		$self = new Self();
		$self->createItemsTable();
        $self->createFoldersTable();
		$self->addOptions();
	}

	/**
	 * Create the folders table.
	 *
	 * @since    0.1.0
	 * @return   void
	 */
	public function createFoldersTable() {
		$tableName = $this->tableName;
		$charsetCollate = $this->charsetCollate;
        $sql = "CREATE TABLE $tableName (";
		$sql .= ' id mediumint(9) NOT NULL AUTO_INCREMENT PRIMARY KEY,';
		$sql .= ' name varchar(24) NOT NULL,';
		$sql .= ' color varchar(24),';
		$sql .= ' parent_id mediumint(9)';
		$sql .= ") $charsetCollate;";
		require_once ABSPATH . 'wp-admin/includes/upgrade.php';
		dbDelta( $sql );
	}

	/**
	 * Create the folder items table.
	 *
	 * @since    0.1.0
	 * @return   void
	 */
	public function createItemsTable() {
		$tableName = $this->mapTableName;
		$charsetCollate = $this->charsetCollate;
        $sql = "CREATE TABLE " . $tableName . " (";
		$sql .= "media_id mediumint(9) NOT NULL,";
		$sql .= "folder_id mediumint(9) NOT NULL";
		$sql .= ") $charsetCollate;";
		require_once ABSPATH . 'wp-admin/includes/upgrade.php';
		dbDelta( $sql );
	}

	/**
	 * Add the packages options.
	 *
	 * @since    0.1.0
	 * @return   void
	 */
	public function addOptions() {
		add_option(
			'simply_media_folders_filter_enabled',
			true,
			'',
			'no'
		);
		add_option(
			'simply_media_folders_default_color',
			"#276cb8",
			'',
			'no'
		);
		add_option(
			'simply_media_folders_auto_open',
			"1",
			'',
			'no'
		);
	}

}
