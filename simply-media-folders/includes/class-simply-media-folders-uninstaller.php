<?php

/**
 * Fired during plugin deactivation
 *
 * @since      0.1.0
 *
 * @package    Simply_Media_Folders
 * @subpackage Simply_Media_Folders/includes
 */

/**
 * Fired during plugin deactivation.
 *
 * This class defines all code necessary to run during the plugin's deactivation.
 *
 * @since      0.1.0
 * @package    Simply_Media_Folders
 * @subpackage Simply_Media_Folders/includes
 * @author     Patrick Greene <patrickisgreene@gmail.com>
 */
class Simply_Media_Folders_Uninstaller {

	/**
	 * The WordPress database object.
	 *
	 * @since    0.1.0
	 * @access   private
	 * @var      array    $db    The $wpdb wordpress object.
	 */
    private $db;

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
		$this->db = $wpdb;
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
	public static function uninstall() {
		$self = new Self();
		$self->dropItemsTable();
        $self->dropFoldersTable();
		$self->removeOptions();
	}

	/**
	 * Delete the folders table.
	 *
	 * @since    0.1.0
	 * @return   void
	 */
	public function dropFoldersTable() {
		$sql = $this->db->prepare("DROP TABLE IF EXISTS %1$s;", $this->tableName);
		$this->db->query($sql);
	}

	/**
	 * Delete the folder items table.
	 *
	 * @since    0.1.0
	 * @return   void
	 */
	public function dropItemsTable() {
		$sql = $this->db->prepare("DROP TABLE IF EXISTS %1$s;", $this->mapTableName);
		$this->db->query($sql);
	}

	public function removeOptions() {
		delete_option('simply_media_folders_filter_enabled');
		delete_option('simply_media_folders_default_color');
		delete_option('simply_media_folders_auto_open');
	}

}
