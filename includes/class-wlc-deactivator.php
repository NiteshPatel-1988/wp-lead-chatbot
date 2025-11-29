<?php
/**
 * Deactivator class for NP Lead Chatbot.
 *
 * @package NP_Lead_Chatbot
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class NPLEADCHAT_Deactivator {
    public static function npleadchat_deactivate() {
        // No transient cleanup required currently.
    }
}
