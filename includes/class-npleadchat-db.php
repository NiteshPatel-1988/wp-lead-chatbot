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
        $table           = $wpdb->prefix . 'npleadchat_leads';
        $charset_collate = $wpdb->get_charset_collate();

        $sql = "CREATE TABLE {$table} (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            name varchar(191) NOT NULL,
            email varchar(191) NOT NULL,
            phone varchar(50) NOT NULL,
            message text NOT NULL,
            date datetime NOT NULL,
            PRIMARY KEY  (id)
        ) {$charset_collate};";

        require_once ABSPATH . 'wp-admin/includes/upgrade.php';
        dbDelta( $sql );
    }

    public static function npleadchat_insert_lead( $data = array() ) {
        global $wpdb;
        $table = $wpdb->prefix . 'npleadchat_leads';
        $wpdb->insert(
            $table,
            $data,
            array( '%s', '%s', '%s', '%s', '%s' )
        );
        return $wpdb->insert_id;
    }

    public static function npleadchat_get_leads( $orderby = 'date', $order = 'DESC', $search = '' ) {
        global $wpdb;

        $allowed_orderby = array( 'name', 'date' );
        if ( ! in_array( $orderby, $allowed_orderby, true ) ) {
            $orderby = 'date';
        }
        $order = ( 'ASC' === strtoupper( $order ) ) ? 'ASC' : 'DESC';

        $table = $wpdb->prefix . 'npleadchat_leads';

        if ( ! empty( $search ) ) {
            $like = '%' . $wpdb->esc_like( $search ) . '%';
            // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
            return $wpdb->get_results(
                $wpdb->prepare(
                    "SELECT * FROM `{$table}` WHERE name LIKE %s OR email LIKE %s OR phone LIKE %s OR message LIKE %s ORDER BY {$orderby} {$order}", // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
                    $like,
                    $like,
                    $like,
                    $like
                )
            );
        }

        // No search — orderby/order are already validated above.
        // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
        return $wpdb->get_results(
            "SELECT * FROM `{$table}` ORDER BY {$orderby} {$order}" // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
        );
    }

    public static function npleadchat_delete_leads( array $ids ) {
        global $wpdb;

        if ( empty( $ids ) ) {
            return;
        }

        // All IDs are already cast to int by the caller — build safe placeholders.
        $ids          = array_map( 'absint', $ids );
        $placeholders = implode( ',', array_fill( 0, count( $ids ), '%d' ) );
        $table        = $wpdb->prefix . 'npleadchat_leads';

        $wpdb->query( // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
            $wpdb->prepare(
                "DELETE FROM `{$table}` WHERE id IN ({$placeholders})", // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
                ...$ids
            )
        );
    }
}
