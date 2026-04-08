<?php
/**
 * Frontend class for NP Lead Chatbot.
 *
 * @package NP_Lead_Chatbot
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class NPLEADCHAT_Frontend {
    public static function npleadchat_init() {
        add_shortcode( 'npleadchat', array( __CLASS__, 'npleadchat_bot_ui' ) );
        add_action( 'wp_enqueue_scripts', array( __CLASS__, 'npleadchat_enqueue' ) );
        add_action( 'wp_footer', array( __CLASS__, 'npleadfloating_widget' ) );
    }

    public static function npleadchat_enqueue() {
        wp_enqueue_style(
            'npleadchat-frontend',
            NPLEADCHAT_URL . 'assets/css/chatbot.css',
            array(),
            NPLEADCHAT_VERSION
        );
        wp_enqueue_script(
            'npleadchat-frontend-js',
            NPLEADCHAT_URL . 'assets/js/chatbot.js',
            array( 'jquery' ),
            NPLEADCHAT_VERSION,
            true
        );
        wp_localize_script( 'npleadchat-frontend-js', 'npleadchat_api', array(
            'url'   => esc_url_raw( rest_url( 'npleadchat/v1/lead' ) ),
            'nonce' => wp_create_nonce( 'wp_rest' ),
        ) );
    }

    /**
     * Shortcode output — inline embed with its own header.
     */
    public static function npleadchat_bot_ui() {
        ob_start(); ?>

        <div id="wlc-chatbot" class="nlc-inline-mode">

            <div class="nlc-inline-header">
                <h3><?php esc_html_e( 'Chat With Us', 'np-lead-chatbot' ); ?></h3>
                <p><?php esc_html_e( 'Fill in the form and we\'ll get back to you shortly.', 'np-lead-chatbot' ); ?></p>
            </div>

            <div class="nlc-form-body">
                <?php echo self::npleadchat_form_fields(); ?>
            </div>

        </div>

        <?php return ob_get_clean();
    }

    /**
     * Floating widget injected into footer.
     */
    public static function npleadfloating_widget() {
        /* Floating trigger button */
        echo '<div id="wlc-floating-btn" role="button" aria-label="' . esc_attr__( 'Open chat', 'np-lead-chatbot' ) . '" tabindex="0">';

        /* Chat icon */
        echo '<svg class="nlc-icon nlc-icon-chat" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">';
        echo '<path d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>';
        echo '</svg>';

        /* Close icon */
        echo '<svg class="nlc-icon nlc-icon-close" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">';
        echo '<path d="M6 18L18 6M6 6l12 12" stroke="white" stroke-width="2.5" stroke-linecap="round"/>';
        echo '</svg>';

        echo '</div>';

        /* Popup */
        echo '<div id="wlc-chat-popup" role="dialog" aria-modal="true" aria-label="' . esc_attr__( 'Contact form', 'np-lead-chatbot' ) . '">';

            /* Gradient header */
            echo '<div class="nlc-popup-header">';
                echo '<div class="nlc-header-top">';

                    /* Avatar */
                    echo '<div class="nlc-header-avatar">';
                    echo '<svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">';
                    echo '<path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2M12 11a4 4 0 100-8 4 4 0 000 8z" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>';
                    echo '</svg>';
                    echo '</div>';

                    /* Title + status */
                    echo '<div class="nlc-header-info">';
                    echo '<h3 class="nlc-header-title">' . esc_html__( 'Chat With Us', 'np-lead-chatbot' ) . '</h3>';
                    echo '<div class="nlc-header-status">';
                    echo '<span class="nlc-status-dot"></span>';
                    echo '<span class="nlc-status-text">' . esc_html__( 'We\'re online', 'np-lead-chatbot' ) . '</span>';
                    echo '</div>';
                    echo '</div>';

                    /* Close button */
                    echo '<button id="wlc-chat-close" aria-label="' . esc_attr__( 'Close chat', 'np-lead-chatbot' ) . '">&times;</button>';

                echo '</div>';
            echo '</div>';

            /* Form */
            echo '<div id="wlc-chatbot">';
            echo self::npleadchat_form_fields();
            echo '</div>';

            /* Footer */
            echo '<div class="nlc-popup-footer">' . esc_html__( 'Powered by NP Lead Chatbot', 'np-lead-chatbot' ) . '</div>';

        echo '</div>';
    }

    /**
     * Shared form fields HTML (used in both shortcode and floating widget).
     */
    private static function npleadchat_form_fields() {
        ob_start(); ?>

        <div class="nlc-field">
            <label class="nlc-label" for="wlc-name"><?php esc_html_e( 'Your Name', 'np-lead-chatbot' ); ?></label>
            <input type="text" id="wlc-name" placeholder="<?php esc_attr_e( 'Jane Smith', 'np-lead-chatbot' ); ?>" autocomplete="name">
            <span class="wlc-error" id="wlc-name-error" role="alert"></span>
        </div>

        <div class="nlc-field">
            <label class="nlc-label" for="wlc-email"><?php esc_html_e( 'Email Address', 'np-lead-chatbot' ); ?></label>
            <input type="email" id="wlc-email" placeholder="<?php esc_attr_e( 'jane@example.com', 'np-lead-chatbot' ); ?>" autocomplete="email">
            <span class="wlc-error" id="wlc-email-error" role="alert"></span>
        </div>

        <div class="nlc-field">
            <label class="nlc-label" for="wlc-phone"><?php esc_html_e( 'Phone Number', 'np-lead-chatbot' ); ?></label>
            <input type="text" id="wlc-phone" placeholder="<?php esc_attr_e( '+1 (555) 000-0000', 'np-lead-chatbot' ); ?>" autocomplete="tel">
            <span class="wlc-error" id="wlc-phone-error" role="alert"></span>
        </div>

        <div class="nlc-field">
            <label class="nlc-label" for="wlc-message"><?php esc_html_e( 'Message', 'np-lead-chatbot' ); ?></label>
            <textarea id="wlc-message" placeholder="<?php esc_attr_e( 'How can we help you?', 'np-lead-chatbot' ); ?>"></textarea>
            <span class="wlc-error" id="wlc-message-error" role="alert"></span>
        </div>

        <button id="wlc-submit">
            <span class="nlc-btn-text">
                <svg width="15" height="15" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true" style="flex-shrink:0">
                    <path d="M3 10l14-7-7 14-2-5-5-2z" fill="white"/>
                </svg>
                <?php esc_html_e( 'Send Message', 'np-lead-chatbot' ); ?>
            </span>
            <span class="nlc-spinner" aria-hidden="true"></span>
        </button>

        <div id="wlc-response" role="status" aria-live="polite"></div>

        <?php return ob_get_clean();
    }
}
