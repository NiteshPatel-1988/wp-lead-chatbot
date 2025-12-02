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
        wp_enqueue_style( 'npleadchat-frontend', NPLEADCHAT_URL . 'assets/css/chatbot.css', array(), NPLEADCHAT_VERSION );
        wp_enqueue_script( 'npleadchat-frontend-js', NPLEADCHAT_URL . 'assets/js/chatbot.js', array(), NPLEADCHAT_VERSION, true );
        wp_localize_script( 'npleadchat-frontend-js', 'npleadchat_api', array(
            'url'   => esc_url_raw( rest_url( 'npleadchat/v1/lead' ) ),
            'nonce' => wp_create_nonce( 'wp_rest' ),
        ) );
    }

    public static function npleadchat_bot_ui() {
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

    public static function npleadfloating_widget() {
        echo '<div id="wlc-floating-btn">💬</div>';
        echo '<div id="wlc-chat-popup">';
            echo '<span id="wlc-chat-close">×</span>';
            echo do_shortcode("[npleadchat]");
        echo '</div>';
    }
}