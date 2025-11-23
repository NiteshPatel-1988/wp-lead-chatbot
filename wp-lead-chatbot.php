<?php
/**
 * Plugin Name: NP Lead Chatbot
 * Description: A lead generation chatbot plugin.
 * Version: 1.0.0
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

define( 'WLC_PLUGIN_FILE', __FILE__ );
define( 'WLC_VERSION', '1.0.0' );
define( 'WLC_DIR', plugin_dir_path( __FILE__ ) );
define( 'WLC_URL', plugin_dir_url( __FILE__ ) );

// Include core files
require_once WLC_DIR . 'includes/class-wlc-activator.php';
require_once WLC_DIR . 'includes/class-wlc-deactivator.php';
require_once WLC_DIR . 'includes/class-wlc-admin.php';
require_once WLC_DIR . 'includes/class-wlc-frontend.php';
require_once WLC_DIR . 'includes/class-wlc-api.php';
require_once WLC_DIR . 'includes/class-wlc-db.php';

// Activation/Deactivation hooks
register_activation_hook( __FILE__, array( 'WLC_Activator', 'activate' ) );
register_deactivation_hook( __FILE__, array( 'WLC_Deactivator', 'deactivate' ) );

// Initialize admin and frontend
add_action( 'init', function() {
    // REST route registration handled in class-wlc-api on init
    if ( is_admin() ) {
        WLC_Admin::init();
    }
    WLC_Frontend::init();
    WLC_API::init();
} );