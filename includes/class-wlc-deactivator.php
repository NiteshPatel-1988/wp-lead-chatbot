<?php
/**
 * Deactivator class for NP Lead Chatbot.
 *
 * @package NP_Lead_Chatbot
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class WLC_Deactivator {
    public static function deactivate() {
        // No transient cleanup required currently.
    }
}
