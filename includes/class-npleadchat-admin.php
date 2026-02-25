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
        add_action( 'admin_post_npleadchat_export_leads', array( __CLASS__, 'npleadchat_export_csv' ) );
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

        <h1 class="wp-heading-inline">
            <?php esc_html_e( 'Leads', 'np-lead-chatbot' ); ?>
        </h1>

        <form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" style="display:inline;">
            <input type="hidden" name="action" value="npleadchat_export_leads" />
            <?php wp_nonce_field( 'npleadchat_export_nonce', 'npleadchat_export_nonce_field' ); ?>
            <input type="submit" class="page-title-action" value="<?php esc_attr_e( 'Export Leads', 'np-lead-chatbot' ); ?>" />
        </form>

        <hr class="wp-header-end">

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

    public static function npleadchat_export_csv() {

        if ( ! current_user_can( 'manage_options' ) ) {
            wp_die( esc_html__( 'Unauthorized access', 'np-lead-chatbot' ) );
        }

        if (
            ! isset( $_POST['npleadchat_export_nonce_field'] ) ||
            ! wp_verify_nonce(
                sanitize_text_field( wp_unslash( $_POST['npleadchat_export_nonce_field'] ) ),
                'npleadchat_export_nonce'
            )
        ) {
            wp_die( esc_html__( 'Nonce verification failed', 'np-lead-chatbot' ) );
        }

        global $wpdb;

        $table_name = $wpdb->prefix . 'npleadchat_leads';

        $leads = $wpdb->get_results( "SELECT * FROM $table_name ORDER BY id ASC", ARRAY_A );

        if ( empty( $leads ) ) {
            wp_die( esc_html__( 'No leads found.', 'np-lead-chatbot' ) );
        }

        // Set headers for CSV download
        header( 'Content-Type: text/csv; charset=utf-8' );
        header( 'Content-Disposition: attachment; filename=npleadchat-leads-' . date( 'Y-m-d' ) . '.csv' );
        header( 'Pragma: no-cache' );
        header( 'Expires: 0' );

        $output = fopen( 'php://output', 'w' );

        // Add column headers
        fputcsv( $output, array_keys( $leads[0] ) );

        foreach ( $leads as $lead ) {
            fputcsv( $output, $lead );
        }

        fclose( $output );
        exit;
    }
}