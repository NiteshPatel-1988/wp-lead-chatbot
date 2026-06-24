<?php
/**
 * GDPR Privacy Integration for NP Lead Chatbot
 *
 * This file implements WordPress privacy hooks for GDPR compliance.
 * Add this to your main plugin file or include it in the initialization.
 *
 * @package NP_Lead_Chatbot
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class NPLEADCHAT_Privacy {

    /**
     * Initialize privacy hooks
     */
    public static function npleadchat_init_privacy() {
        add_filter( 'wp_privacy_personal_data_exporters', array( __CLASS__, 'npleadchat_register_exporter' ) );
        add_filter( 'wp_privacy_personal_data_erasers', array( __CLASS__, 'npleadchat_register_eraser' ) );
    }

    /**
     * Register data exporter for privacy requests
     *
     * @param array $exporters Array of registered exporters.
     * @return array
     */
    public static function npleadchat_register_exporter( $exporters ) {
        $exporters['npleadchat'] = array(
            'exporter_friendly_name' => __( 'NP Lead Chatbot', 'np-lead-chatbot' ),
            'callback'               => array( __CLASS__, 'npleadchat_exporter' ),
        );
        return $exporters;
    }

    /**
     * Register data eraser for privacy requests
     *
     * @param array $erasers Array of registered erasers.
     * @return array
     */
    public static function npleadchat_register_eraser( $erasers ) {
        $erasers['npleadchat'] = array(
            'eraser_friendly_name' => __( 'NP Lead Chatbot', 'np-lead-chatbot' ),
            'callback'             => array( __CLASS__, 'npleadchat_eraser' ),
        );
        return $erasers;
    }

    /**
     * Export personal data for a user
     *
     * @param string $email_address The user's email address.
     * @param int    $page         Current page number.
     * @return array
     */
    public static function npleadchat_exporter( $email_address = '', $page = 1 ) {
        $export_items = array();

        if ( empty( $email_address ) ) {
            return array(
                'data' => $export_items,
                'done' => true,
            );
        }

        global $wpdb;
        $table = $wpdb->prefix . 'npleadchat_leads';

        // Get leads associated with this email
        $leads = $wpdb->get_results(
            $wpdb->prepare(
                "SELECT * FROM {$table} WHERE email = %s ORDER BY date DESC",
                $email_address
            )
        );

        if ( ! empty( $leads ) ) {
            $group_id = 0;

            foreach ( $leads as $lead ) {
                $group_id++;

                $export_items[] = array(
                    'group_id'    => 'npleadchat-leads-' . $group_id,
                    'group_label' => sprintf(
                        /* translators: %s: Lead capture date */
                        __( 'Lead Captured: %s', 'np-lead-chatbot' ),
                        $lead->date
                    ),
                    'item_id'     => 'lead-' . $lead->id,
                    'data'        => array(
                        array(
                            'name'  => __( 'Name', 'np-lead-chatbot' ),
                            'value' => $lead->name,
                        ),
                        array(
                            'name'  => __( 'Email', 'np-lead-chatbot' ),
                            'value' => $lead->email,
                        ),
                        array(
                            'name'  => __( 'Phone', 'np-lead-chatbot' ),
                            'value' => $lead->phone,
                        ),
                        array(
                            'name'  => __( 'Message', 'np-lead-chatbot' ),
                            'value' => $lead->message,
                        ),
                        array(
                            'name'  => __( 'Source Page', 'np-lead-chatbot' ),
                            'value' => $lead->source_url,
                        ),
                        array(
                            'name'  => __( 'Submission Date', 'np-lead-chatbot' ),
                            'value' => $lead->date,
                        ),
                    ),
                );
            }
        }

        return array(
            'data' => $export_items,
            'done' => true,
        );
    }

    /**
     * Erase personal data for a user
     *
     * @param string $email_address The user's email address.
     * @param int    $page         Current page number.
     * @return array
     */
    public static function npleadchat_eraser( $email_address = '', $page = 1 ) {
        if ( empty( $email_address ) ) {
            return array(
                'items_removed'  => false,
                'items_retained' => false,
                'messages'       => array(),
                'done'           => true,
            );
        }

        global $wpdb;
        $table = $wpdb->prefix . 'npleadchat_leads';

        // Get all leads for this email
        $lead_ids = $wpdb->get_col(
            $wpdb->prepare(
                "SELECT id FROM {$table} WHERE email = %s",
                $email_address
            )
        );

        if ( empty( $lead_ids ) ) {
            return array(
                'items_removed'  => false,
                'items_retained' => false,
                'messages'       => array(),
                'done'           => true,
            );
        }

        // Delete the leads
        $deleted = 0;
        foreach ( $lead_ids as $lead_id ) {
            $result = $wpdb->query(
                $wpdb->prepare(
                    "DELETE FROM {$table} WHERE id = %d",
                    $lead_id
                )
            );
            if ( $result ) {
                $deleted++;
            }
        }

        return array(
            'items_removed'  => $deleted > 0,
            'items_retained' => false,
            'messages'       => array(),
            'done'           => true,
        );
    }
}

// Initialize privacy hooks when WordPress loads
add_action( 'init', array( 'NPLEADCHAT_Privacy', 'npleadchat_init_privacy' ) );
