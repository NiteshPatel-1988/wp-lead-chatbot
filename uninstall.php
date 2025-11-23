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
$table = $wpdb->prefix . 'wlc_leads';
$sql = $wpdb->prepare(
    "DROP TABLE IF EXISTS `%i`",
    $table
);
$wpdb->query( $sql );