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
class Simply_Media_Folders_Items_Filter {

    /**
	 * The WordPress database object.
	 *
	 * @since    0.1.0
	 * @access   private
	 * @var      array    $db    The $wpdb wordpress object.
	 */
    private $db;

    /**
	 * Whether the plugin filter is enabled.
	 *
	 * @since    0.1.0
	 * @access   private
	 * @var      array    $enabled    Whether the plugin filter is enabled.
	 */
    private $enabled;
    
    /**
	 * The generated name of the table to store folder items in.
	 *
	 * @since    0.1.0
	 * @access   private
	 * @var      array    $tableName    The generated name of the table to store folder items in.
	 */
    private $tableName;

    /**
	 * Initialize the Items Filter.
	 *
	 * @since    0.1.0
     * @return   void
	 */
    public function __construct() {
        global $wpdb;
        $this->db = $wpdb;
        $this->tableName = $this->db->prefix . "simply_media_folder_items";
        $this->enabled = boolval(get_option('simply_media_folders_filter_enabled'));
    }

    /**
	 * Filter used to sort media attachments based on a selected folder when in grid mode.
	 *
	 * @since    0.1.0
     * @return   array
	 */
    public function filterGrid($query) {
        if(!$this->enabled) {
            return $query;
        }
        if(isset($_POST['query']) && isset($_POST['query']['folder_id'])) {
            $folderId = sanitize_text_field(wp_unslash($_POST["query"]["folder_id"]));
            if (!isset($query['post__in'])) {
                $query['post__in'] = array();
                unset($query["post__not_in"]);
            }
            $query['post__in'] += $this->itemsInFolder($folderId);
            if (count($query['post__in']) === 0) {
                $query['post__in'] += array(-1);
            }
        } else {
            if (!isset($query['post__not_in'])) {
                $query['post__not_in'] = array();
                unset($query["post__in"]);
            }
            $query['post__not_in'] = $this->allAttachedItems();
        }
        return $query;
    }

    /**
	 * Filter used to sort media attachments based on a selected folder when in list mode.
	 *
	 * @since    0.1.0
     * @return   array
	 */
    public function filterList($clauses, $query) {
        if(
            !$this->enabled ||
            $query->get('post_type') !== "attachment" ||
            get_user_option('media_library_mode', get_current_user_id()) !== "list"
        ) {
            return $clauses;
        }
        $folderId = isset($_GET['folder_id']) ? sanitize_text_field(wp_unslash($_GET['folder_id'])) : $query->get("folder_id");
        if($folderId !== '') {
            $ids = $this->itemsInFolder($folderId);
            if(count($ids) > 1) {
                $clauses['where'] .= "AND id IN (" . implode(',', $ids) . ")";
            } else if(count($ids) === 1) {
                $clauses['where'] .= "AND id = " . $ids[0];
            } else {
                $clauses['where'] .= "AND id = NULL";
            }
        } else {
            $ids = $this->allAttachedItems();
            if(count($ids) > 1) {
                $clauses['where'] .= "AND id NOT IN (" . implode(',', $ids) . ")";
            } else if(count($ids) === 1) {
                $clauses['where'] .= "AND id <> " . $ids[0];
            }
        }
        return $clauses;
    }

    /**
	 * Returns all items in any folder.
	 *
	 * @since    0.1.0
     * @return   array
	 */
    private function allAttachedItems() {
        $sql = $this->db->prepare('SELECT media_id FROM `%1$s`;', $this->tableName);
        return $this->db->get_col($sql);
    }

    /**
	 * Returns all items in a specific folder.
	 *
	 * @since    0.1.0
     * @return   array
	 */
    private function itemsInFolder($folderId) {
        $sql = 'SELECT media_id FROM %1$s WHERE folder_id = %2$d;';
        $sql = $this->db->prepare($sql, $this->tableName, $folderId);
        return $this->db->get_col($sql);
    }
}