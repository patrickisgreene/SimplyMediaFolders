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
class Simply_Media_Folders_Folders_Table {

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
	 * Initialize the Folders Table helper.
	 *
	 * @since    0.1.0
     * @return   void
	 */
    public function __construct() {
        global $wpdb;
        $this->db = $wpdb;
        $this->tableName = $this->db->prefix . "simply_media_folders";
    }

    /**
	 * List all folders as a nested array of folder objects.
	 *
	 * @since    0.1.0
     * @return   array
	 */
    public function list() {
        $tableName = $this->tableName;
        $sql = $this->db->prepare('SELECT * FROM %1$s;', $this->tableName);
        $res = $this->db->get_results($sql);
        if($res) {
            $output = $this->nestArray($res);
        } else {
            $output = [];
        }
        return $this->respond($output);
    }

    /**
	 * Get a folder given an id.
	 *
	 * @since    0.1.0
	 * @param    int  $id  The id of the folder to fetch.
     * @return   array
	 */
    public function get($id) {
        $sql = 'SELECT * FROM %1$s WHERE id = %2$s;';
        $stmt = $this->db->prepare($sql, $this->tableName, $id);
        return $this->respond($this->db->get_result($stmt));
    }

    /**
	 * Create a new folder.
	 *
	 * @since    0.1.0
     * @return   integer
	 */
    public function create() {
        if(isset($_POST['nonce'])) {
            wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['nonce'])), "smf-nonce");
            $args = [
                'name' => isset($_POST['name']) ? sanitize_text_field(wp_unslash($_POST['name'])) : null,
                'color' => isset($_POST['color']) ? sanitize_text_field(wp_unslash($_POST['color'])) : null,
                'parent_id' => isset($_POST['parent_id']) ? sanitize_text_field(wp_unslash($_POST['parent_id'])) : null
            ];
            $this->db->insert(
                $this->tableName,
                $args
            );
            return $this->respond($this->db->insert_id);
        }
        return $this->respond(null);
    }

    /**
	 * Update a folders values.
	 *
	 * @since    0.1.0
     * @return   integer
	 */
    public function update() {
        if(isset($_POST['nonce']) && isset($_POST['id'])) {
            wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['nonce'])), "smf-nonce");
            $args = [];
            if(isset($_POST['parent_id'])) {
                $parent = sanitize_text_field(wp_unslash($_POST['parent_id']));
                if(strlen($parent) > 0) {
                    $args['parent_id'] = $parent;
                } else {
                    $args['parent_id'] = null;
                }
            }
            if(isset($_POST['name'])) {
                $args['name'] = sanitize_text_field(wp_unslash($_POST['name']));
            }
            if(isset($_POST['color'])) {
                $args['color'] = sanitize_text_field(wp_unslash($_POST['color']));
            }
            $this->respond($this->db->update(
                $this->tableName,
                $args,
                array('id' => intval(sanitize_text_field(wp_unslash($_POST['id']))))
            ));
        }
    }

    /**
	 * Delete a folder.
	 *
	 * @since    0.1.0
     * @return   integer
	 */
    public function delete() {
        if(isset($_POST['nonce']) && isset($_POST['id'])) {
            wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['nonce'])), "smf-nonce");
            $id = sanitize_text_field(wp_unslash($_POST['id']));
            $this->db->delete($this->tableName, array('id' => $id));
            $sql = $this->db->prepare('SELECT parent_id from %1$s WHERE id = %2$s;', $this->tableName, $id);
            $parent_id = $this->db->get_results($sql);
            if($parent_id) {
                $this->db->delete(
                    $this->db->prefix . "simply_media_folder_items",
                    array('folder_id' => $parent_id),
                    array('folder_id' => $id),
                );
                
                $this->db->update(
                    $this->tableName,
                    array('parent_id' => $parent_id),
                    array('parent_id' => $id)
                );
            } else {
                $this->db->delete(
                    $this->db->prefix . "simply_media_folder_items",
                    array('folder_id' => $id)
                );
    
                $this->db->update(
                    $this->tableName,
                    array('parent_id' => null),
                    array('parent_id' => $id)
                );
            }
        }
        $this->respond(null);
    }

    /**
	 * List all folders as a nested array of folder objects.
	 *
	 * @since    0.1.0
     * @return   array
	 */
    private function nestArray($arr, $id = null) {
        $newArr = [];

        foreach($arr as $item) {
            if($item->parent_id === $id) {
                array_push($newArr, [
                    "id" => $item->id,
                    "name" => $item->name,
                    "color" => $item->color,
                    "parent_id" => $item->parent_id,
                    "children" => $this->nestArray($arr, $item->id)
                ]);
            }
        }
        return $newArr;
    }

    /**
	 * Returns a JSON response and then dies.
	 *
	 * @since    0.1.0
     * @return   array
	 */
    private function respond($data) {
        wp_send_json_success($data);
        die();
    }
}