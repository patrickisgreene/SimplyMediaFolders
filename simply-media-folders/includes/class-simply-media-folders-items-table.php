<?php

/**
 * Utility class for managing the folders table.
 *
 * @since      0.1.0
 *
 * @package    Simply_Media_Folders
 * @subpackage Simply_Media_Folders/includes
 */

/**
 * Utility class for managing the folders table.
 *
 * @since      0.1.0
 * @package    Simply_Media_Folders
 * @subpackage Simply_Media_Folders/includes
 * @author     Patrick Greene <patrickisgreene@gmail.com>
 */
class Simply_Media_Folders_Items_Table {

    /**
	 * The WordPress database object.
	 *
	 * @since    0.1.0
	 * @access   private
	 * @var      array    $db    The $wpdb wordpress object.
	 */
    private $db;

    /**
	 * The generated name of the table to store folder items in.
	 *
	 * @since    0.1.0
	 * @access   private
	 * @var      array    $tableName    The generated name of the table to store folder items in.
	 */
    private $tableName;

    /**
	 * Initialize the Items Table helper.
	 *
	 * @since    0.1.0
     * @return   void
	 */
    public function __construct() {
        global $wpdb;
        $this->db = $wpdb;
        $this->tableName = $this->db->prefix . "simply_media_folder_items";
    }

    /**
	 * Attach media to a folder.
	 *
	 * @since    0.1.0
     * @return   void
	 */
    public function attach() {
        if(isset($_POST['nonce'])) {
            wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['nonce'])), "smf-nonce");
            $args = [
                'media_id' => isset($_POST['media_id']) ? sanitize_text_field(wp_unslash($_POST['media_id'])) : null,
                'folder_id' => isset($_POST['folder_id']) ? sanitize_text_field(wp_unslash($_POST['folder_id'])) : null
            ];
            $this->db->delete(
                $this->tableName,
                array('media_id' => $args['media_id'])
            );
            if($args['folder_id'] !== '') {
                $this->db->insert(
                    $this->tableName,
                    $args
                );
                $this->respond($this->db->insert_id);
            } else {
                $this->respond(1);
            }
        }
    }

    /**
	 * Returns a JSON response and then dies.
	 *
	 * @since    0.1.0
     * @return   array
	 */
    private function respond($data) {
        wp_send_json($data);
        die();
    }
}