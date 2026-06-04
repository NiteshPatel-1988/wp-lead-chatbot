<?php
/**
 * Uninstall script for NP Lead Chatbot
 *
 * @package NP_Lead_Chatbot
 */

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
    exit;
}

global $wpdb;
$table = $wpdb->prefix . 'npleadchat_leads';
$wpdb->query( "DROP TABLE IF EXISTS `" . esc_sql( $table ) . "`" ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared

delete_option( 'npleadchat_options' );
delete_option( 'npleadchat_db_version' );
