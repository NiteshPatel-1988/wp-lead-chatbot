<?php
/**
 * REST API class for NP Lead Chatbot.
 *
 * @package NP_Lead_Chatbot
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class NPLEADCHAT_API {
    const MIN_SUBMIT_SECONDS = 3;
    const DUPLICATE_WINDOW   = 10 * MINUTE_IN_SECONDS;

    public static function npleadchat_init() {
        add_action( 'rest_api_init', array( __CLASS__, 'npleadchat_register_routes' ) );
    }

    public static function npleadchat_register_routes() {
        register_rest_route( 'npleadchat/v1', '/lead', array(
            'methods'  => 'POST',
            'callback' => array( __CLASS__, 'npleadchat_handle_lead' ),
            'permission_callback' => array( __CLASS__, 'npleadchat_permission_check' ),
        ) );
    }

    public static function npleadchat_permission_check( $request ) {
        $nonce = sanitize_text_field( $request->get_header( 'x_wp_nonce' ) );

        return wp_verify_nonce( $nonce, 'wp_rest' );
    }

    private static function npleadchat_success_message() {
        $options = NPLEADCHAT_Admin::npleadchat_get_options();

        return $options['success_message'];
    }

    private static function npleadchat_get_visitor_ip() {
        if ( empty( $_SERVER['REMOTE_ADDR'] ) ) {
            return 'unknown';
        }

        return sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) );
    }

    private static function npleadchat_is_too_fast( $form_started_at ) {
        $form_started_at = absint( $form_started_at );

        if ( empty( $form_started_at ) ) {
            return true;
        }

        return ( time() - $form_started_at ) < self::MIN_SUBMIT_SECONDS;
    }

    private static function npleadchat_rate_limit_key( $email ) {
        return 'npleadchat_rate_' . md5( self::npleadchat_get_visitor_ip() . '|' . strtolower( $email ) );
    }

    private static function npleadchat_duplicate_key( array $data ) {
        return 'npleadchat_dup_' . md5(
            strtolower( $data['email'] ) . '|' .
            strtolower( $data['name'] ) . '|' .
            strtolower( $data['message'] ) . '|' .
            strtolower( $data['source_url'] )
        );
    }

    private static function npleadchat_send_notification( array $data, $lead_id ) {
        $options = NPLEADCHAT_Admin::npleadchat_get_options();

        if ( empty( $options['enable_notifications'] ) || empty( $options['notification_email'] ) || ! is_email( $options['notification_email'] ) ) {
            return;
        }

        $site_name = wp_specialchars_decode( get_bloginfo( 'name' ), ENT_QUOTES );
        $subject   = sprintf(
            /* translators: %s: site name */
            __( 'New lead from %s', 'np-lead-chatbot' ),
            $site_name
        );

        $body = sprintf(
            "%s\n\n%s\n%s\n%s\n%s\n%s\n%s\n",
            __( 'A new lead was captured by Lead Capture Chat.', 'np-lead-chatbot' ),
            sprintf(
                /* translators: %s: lead name */
                __( 'Name: %s', 'np-lead-chatbot' ),
                $data['name']
            ),
            sprintf(
                /* translators: %s: lead email */
                __( 'Email: %s', 'np-lead-chatbot' ),
                $data['email']
            ),
            sprintf(
                /* translators: %s: lead phone */
                __( 'Phone: %s', 'np-lead-chatbot' ),
                $data['phone']
            ),
            sprintf(
                /* translators: %s: lead message */
                __( 'Message: %s', 'np-lead-chatbot' ),
                $data['message']
            ),
            sprintf(
                /* translators: %s: source URL */
                __( 'Source: %s', 'np-lead-chatbot' ),
                $data['source_url']
            ),
            sprintf(
                /* translators: %s: admin leads URL */
                __( 'View lead: %s', 'np-lead-chatbot' ),
                admin_url( 'admin.php?page=npleadchat-leads&lead_id=' . absint( $lead_id ) )
            )
        );

        wp_mail( sanitize_email( $options['notification_email'] ), $subject, $body );
    }

    public static function npleadchat_handle_lead( $request ) {
        $params = $request->get_json_params();
        $params = is_array( $params ) ? $params : array();

        $name = isset( $params['name'] ) ? sanitize_text_field( $params['name'] ) : '';
        $email = isset( $params['email'] ) ? sanitize_email( $params['email'] ) : '';
        $phone = isset( $params['phone'] ) ? sanitize_text_field( $params['phone'] ) : '';
        $message = isset( $params['message'] ) ? sanitize_textarea_field( $params['message'] ) : '';
        $source_url = isset( $params['source_url'] ) ? esc_url_raw( $params['source_url'] ) : '';
        $honeypot = isset( $params['website'] ) ? sanitize_text_field( $params['website'] ) : '';
        $form_started_at = isset( $params['form_started_at'] ) ? absint( $params['form_started_at'] ) : 0;
        $options = NPLEADCHAT_Admin::npleadchat_get_options();

        if ( ! empty( $honeypot ) ) {
            return rest_ensure_response( array( 'success' => true, 'message' => self::npleadchat_success_message() ) );
        }

        if ( self::npleadchat_is_too_fast( $form_started_at ) ) {
            return rest_ensure_response( array( 'success' => true, 'message' => self::npleadchat_success_message() ) );
        }

        if ( empty( $name ) || empty( $email ) ) {
            return rest_ensure_response( array( 'success' => false, 'message' => __( 'Name and email are required.', 'np-lead-chatbot' ) ) );
        }

        if ( ! is_email( $email ) ) {
            return rest_ensure_response( array( 'success' => false, 'message' => __( 'Please enter a valid email address.', 'np-lead-chatbot' ) ) );
        }

        $rate_limit_key = self::npleadchat_rate_limit_key( $email );
        if ( get_transient( $rate_limit_key ) ) {
            return rest_ensure_response( array( 'success' => false, 'message' => __( 'Please wait a moment before sending another message.', 'np-lead-chatbot' ) ) );
        }

        $data = array(
            'name' => $name,
            'email' => $email,
            'phone' => $phone,
            'message' => $message,
            'source_url' => $source_url,
            'date' => current_time( 'mysql' ),
        );

        $duplicate_key = self::npleadchat_duplicate_key( $data );
        if ( get_transient( $duplicate_key ) ) {
            return rest_ensure_response( array( 'success' => true, 'message' => self::npleadchat_success_message() ) );
        }

        $id = NPLEADCHAT_DB::npleadchat_insert_lead( $data );

        if ( $id ) {
            set_transient( $rate_limit_key, 1, max( 10, absint( $options['rate_limit_seconds'] ) ) );
            set_transient( $duplicate_key, 1, self::DUPLICATE_WINDOW );
            self::npleadchat_send_notification( $data, $id );

            return rest_ensure_response( array( 'success' => true, 'message' => self::npleadchat_success_message(), 'id' => $id ) );
        }

        return rest_ensure_response( array( 'success' => false, 'message' => __( 'Could not save lead', 'np-lead-chatbot' ) ) );
    }
}
