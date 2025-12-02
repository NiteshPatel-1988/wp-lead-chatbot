<?php
/**
 * Database helper for NP Lead Chatbot.
 *
 * @package NP_Lead_Chatbot
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class NPLEADCHAT_DB {
    public static function npleadchat_create_table() {
        global $wpdb;
        $table = $wpdb->prefix . 'npleadchat_leads';
        $charset_collate = $wpdb->get_charset_collate();
        $sql = "";
        $sql .= "CREATE TABLE {$table} (";
        $sql .= "id mediumint(9) NOT NULL AUTO_INCREMENT,";
        $sql .= "name varchar(191) NOT NULL,";
        $sql .= "email varchar(191) NOT NULL,";
        $sql .= "phone varchar(50) NOT NULL,";
        $sql .= "message text NOT NULL,";
        $sql .= "date datetime NOT NULL,";
        $sql .= "PRIMARY KEY  (id)";
        $sql .= ") {$charset_collate};";

        require_once ABSPATH . 'wp-admin/includes/upgrade.php';
        dbDelta( $sql );
    }

    public static function npleadchat_insert_lead( $data = array() ) {
        global $wpdb;
        $table = esc_sql( $wpdb->prefix . 'npleadchat_leads' );
        $wpdb->insert( $table, $data );
        return $wpdb->insert_id;
    }

    public static function npleadchat_get_leads() {
        global $wpdb;
        $table = $wpdb->prefix . 'npleadchat_leads'; 
        $table = esc_sql( $table ); // sanitize table name
        $sql = "SELECT * FROM {$table} ORDER BY id DESC";
        return $wpdb->get_results( $sql ); 
    }
}
