<?php
/**
 * Activator class for NP Lead Chatbot.
 *
 * @package NP_Lead_Chatbot
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class NPLEADCHAT_Activator {
    public static function npleadchat_activate() {
        require_once NPLEADCHAT_DIR . 'includes/class-npleadchat-db.php';
        NPLEADCHAT_DB::npleadchat_create_table();
        add_option( 'npleadchat_options', NPLEADCHAT_Admin::npleadchat_default_options() );
    }
}
