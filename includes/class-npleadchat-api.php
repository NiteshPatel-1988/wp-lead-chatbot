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
        $nonce = isset($_SERVER['HTTP_X_WP_NONCE'])? sanitize_text_field( wp_unslash( $_SERVER['HTTP_X_WP_NONCE'] ) ) : '';
        return wp_verify_nonce( $nonce, 'wp_rest' );
    }

    public static function npleadchat_handle_lead( $request ) {
        $params = $request->get_json_params();

        $name = isset( $params['name'] ) ? sanitize_text_field( $params['name'] ) : '';
        $email = isset( $params['email'] ) ? sanitize_email( $params['email'] ) : '';
        $phone = isset( $params['phone'] ) ? sanitize_text_field( $params['phone'] ) : '';
        $message = isset( $params['message'] ) ? sanitize_textarea_field( $params['message'] ) : '';

        if ( empty( $name ) || empty( $email ) ) {
            return rest_ensure_response( array( 'success' => false, 'message' => __( 'Name and email are required.', 'np-lead-chatbot' ) ) );
        }

        $data = array(
            'name' => $name,
            'email' => $email,
            'phone' => $phone,
            'message' => $message,
            'date' => current_time( 'mysql' ),
        );

        $id = NPLEADCHAT_DB::npleadchat_insert_lead( $data );

        if ( $id ) {
            return rest_ensure_response( array( 'success' => true, 'message' => __( 'Lead saved', 'np-lead-chatbot' ), 'id' => $id ) );
        }

        return rest_ensure_response( array( 'success' => false, 'message' => __( 'Could not save lead', 'np-lead-chatbot' ) ) );
    }
}