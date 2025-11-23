<?php
/**
 * Activator class for NP Lead Chatbot.
 *
 * @package NP_Lead_Chatbot
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class WLC_Activator {
    public static function activate() {
        require_once WLC_DIR . 'includes/class-wlc-db.php';
        WLC_DB::create_table();
    }
}