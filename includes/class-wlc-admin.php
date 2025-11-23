<?php
/**
 * Admin class for NP Lead Chatbot.
 *
 * @package NP_Lead_Chatbot
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class WLC_Admin {
    public static function init() {
        add_action( 'admin_menu', array( __CLASS__, 'admin_menu' ) );
        add_action( 'admin_enqueue_scripts', array( __CLASS__, 'enqueue' ) );
    }

    public static function enqueue( $hook ) {
        wp_enqueue_style( 'wlc-admin', WLC_URL . 'assets/css/chatbot.css', array(), WLC_VERSION );
    }

    public static function admin_menu() {
        add_menu_page( esc_html__( 'NP Lead Chatbot', 'np-lead-chatbot' ), esc_html__( 'NP Lead Chatbot', 'np-lead-chatbot' ), 'manage_options', 'wlc-leads', array( __CLASS__, 'render_page' ), 'dashicons-format-chat' );
    }

    public static function render_page() {
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_die( esc_html__( 'Insufficient permissions', 'np-lead-chatbot' ) );
        }

        $leads = WLC_DB::get_leads();
        ?>
        <div class="wrap">
            <h1><?php echo esc_html__( 'Leads', 'np-lead-chatbot' ); ?></h1>
            <table class="widefat fixed striped">
                <thead>
                    <tr>
                        <th><?php echo esc_html__( 'ID', 'np-lead-chatbot' ); ?></th>
                        <th><?php echo esc_html__( 'Name', 'np-lead-chatbot' ); ?></th>
                        <th><?php echo esc_html__( 'Email', 'np-lead-chatbot' ); ?></th>
                        <th><?php echo esc_html__( 'Phone', 'np-lead-chatbot' ); ?></th>
                        <th><?php echo esc_html__( 'Message', 'np-lead-chatbot' ); ?></th>
                        <th><?php echo esc_html__( 'Date', 'np-lead-chatbot' ); ?></th>
                    </tr>
                </thead>
                <tbody>
                <?php if ( ! empty( $leads ) ) : ?>
                    <?php foreach ( $leads as $lead ) : ?>
                        <tr>
                            <td><?php echo esc_html( $lead->id ); ?></td>
                            <td><?php echo esc_html( $lead->name ); ?></td>
                            <td><?php echo esc_html( $lead->email ); ?></td>
                            <td><?php echo esc_html( $lead->phone ); ?></td>
                            <td><?php echo esc_html( $lead->message ); ?></td>
                            <td><?php echo esc_html( $lead->date ); ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else : ?>
                    <tr><td colspan="6"><?php echo esc_html__( 'No leads found.', 'np-lead-chatbot' ); ?></td></tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
        <?php
    }
}