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
class Simply_Media_Folders_Options {

    /**
	 * Update a folders values.
	 *
	 * @since    0.1.0
     * @return   never
	 */
    public function update() {
        if(isset($_POST['nonce'])) {
            wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['nonce'])), "smf-nonce");
            if(isset($_POST['filter_enabled'])) {
                $val = get_option('simply_media_folders_filter_enabled');
                $this->respond(update_option('simply_media_folders_filter_enabled', !$val));
            }
            if(isset($_POST["auto_open"])) {
                $len = intval(sanitize_text_field(wp_unslash($_POST['auto_open'])));
                $opt = update_option('simply_media_folders_auto_open', $len);
                $this->respond($opt);
            }
            if(isset($_POST['default_color'])) {
                $len = strval(sanitize_text_field(wp_unslash($_POST['default_color'])));
                $opt = update_option('simply_media_folders_default_color', $len);
                $this->respond($opt);
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