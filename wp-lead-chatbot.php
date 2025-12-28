<?php
/**
 * Plugin Name: NP Lead Chatbot
 * Description: A lead generation chatbot plugin.
 * Version: 1.1.0
 * Author: NitsPatel
 * Author URI: https://github.com/NiteshPatel-1988
 * Text Domain: np-lead-chatbot
 * Domain Path: /languages
 * Requires PHP: 7.4
 * Requires at least: 6.0
 * Tested up to: 6.8
 * License: GPLv2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * 
 * @package NP_Lead_Chatbot
 */

/* Exit if accessed directly */
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

define( 'NPLEADCHAT_PLUGIN_FILE', __FILE__ );
define( 'NPLEADCHAT_VERSION', '1.1.0' );
define( 'NPLEADCHAT_DIR', plugin_dir_path( __FILE__ ) );
define( 'NPLEADCHAT_URL', plugin_dir_url( __FILE__ ) );

// Include core files
require_once NPLEADCHAT_DIR . 'includes/class-npleadchat-activator.php';
require_once NPLEADCHAT_DIR . 'includes/class-npleadchat-deactivator.php';
require_once NPLEADCHAT_DIR . 'includes/class-npleadchat-admin.php';
require_once NPLEADCHAT_DIR . 'includes/class-npleadchat-frontend.php';
require_once NPLEADCHAT_DIR . 'includes/class-npleadchat-api.php';
require_once NPLEADCHAT_DIR . 'includes/class-npleadchat-db.php';

// Activation/Deactivation hooks
register_activation_hook( __FILE__, array( 'NPLEADCHAT_Activator', 'npleadchat_activate' ) );
register_deactivation_hook( __FILE__, array( 'NPLEADCHAT_Deactivator', 'npleadchat_deactivate' ) );

// Initialize admin and frontend
add_action( 'init', function() {
    // REST route registration handled in class-npleadchat-api on init
    if ( is_admin() ) {
        NPLEADCHAT_Admin::npleadchat_init();
    }
    NPLEADCHAT_Frontend::npleadchat_init();
    NPLEADCHAT_API::npleadchat_init();
} );