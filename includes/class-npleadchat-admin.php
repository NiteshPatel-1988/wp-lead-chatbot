<?php
/**
 * Admin class for NP Lead Chatbot.
 *
 * @package NP_Lead_Chatbot
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class NPLEADCHAT_Admin {
    public static function npleadchat_init() {
        add_action( 'admin_menu', array( __CLASS__, 'npleadchat_admin_menu' ) );
        add_action( 'admin_enqueue_scripts', array( __CLASS__, 'npleadchat_enqueue' ) );
    }

    public static function npleadchat_enqueue( $hook ) {
        wp_enqueue_style( 'npleadchat-admin', NPLEADCHAT_URL . 'assets/css/chatbot.css', array(), NPLEADCHAT_VERSION );
    }

    public static function npleadchat_admin_menu() {
        add_menu_page( esc_html__( 'NP Lead Chatbot', 'np-lead-chatbot' ), esc_html__( 'NP Lead Chatbot', 'np-lead-chatbot' ), 'manage_options', 'npleadchat-leads', array( __CLASS__, 'npleadchat_render_page' ), 'dashicons-format-chat' );
    }

    public static function npleadchat_render_page() {
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_die( esc_html__( 'Insufficient permissions', 'np-lead-chatbot' ) );
        }

        require_once NPLEADCHAT_DIR . 'includes/class-npleadchat-leads-table.php';

    $table = new NPLEADCHAT_Leads_Table();
    $table->prepare_items();
    ?>

    <div class="wrap">
        <h1><?php esc_html_e( 'Leads', 'np-lead-chatbot' ); ?></h1>

        <form method="post">
            <input type="hidden" name="page" value="npleadchat-leads" />

            <?php
            wp_nonce_field( 'bulk-' . $table->_args['plural'] );

            $table->search_box( __( 'Search Leads', 'np-lead-chatbot' ), 'npleadchat-search' );
            $table->display();
            ?>
        </form>
      
    </div>

    <?php
    }
}