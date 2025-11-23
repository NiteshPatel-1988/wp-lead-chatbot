<?php
/**
 * Frontend class for NP Lead Chatbot.
 *
 * @package NP_Lead_Chatbot
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class WLC_Frontend {
    public static function init() {
        add_shortcode( 'lead_chatbot', array( __CLASS__, 'chatbot_ui' ) );
        add_action( 'wp_enqueue_scripts', array( __CLASS__, 'enqueue' ) );
        add_action( 'wp_footer', array( __CLASS__, 'floating_widget' ) );
    }

    public static function enqueue() {
        wp_enqueue_style( 'wlc-frontend', WLC_URL . 'assets/css/chatbot.css', array(), WLC_VERSION );
        wp_enqueue_script( 'wlc-frontend-js', WLC_URL . 'assets/js/chatbot.js', array(), WLC_VERSION, true );
        wp_localize_script( 'wlc-frontend-js', 'wlc_api', array(
            'url'   => esc_url_raw( rest_url( 'wlc/v1/lead' ) ),
            'nonce' => wp_create_nonce( 'wp_rest' ),
        ) );
    }

    public static function chatbot_ui() {
        ob_start(); ?>

        <div id="wlc-chatbot">
            <h3><?php echo esc_html__( 'Chat With Us', 'np-lead-chatbot' ); ?></h3>

            <small class="wlc-error" id="wlc-name-error"></small>
            <input type="text" id="wlc-name" placeholder="Your Name">
            
            <small class="wlc-error" id="wlc-email-error"></small> 
            <input type="email" id="wlc-email" placeholder="Your Email">
            
            <small class="wlc-error" id="wlc-phone-error"></small>
            <input type="text" id="wlc-phone" placeholder="Phone Number">
            
            <small class="wlc-error" id="wlc-message-error"></small>
            <textarea id="wlc-message" placeholder="Your Message"></textarea>
            
            <button id="wlc-submit">Send</button>

            <p id="wlc-response"></p>
        </div>

        <?php return ob_get_clean();
    }

    public static function floating_widget() {
        echo '<div id="wlc-floating-btn">💬</div>';
        echo '<div id="wlc-chat-popup">';
            echo '<span id="wlc-chat-close">×</span>';
            echo do_shortcode("[lead_chatbot]");
        echo '</div>';
    }
}