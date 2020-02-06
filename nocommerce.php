<?php
/**
 * Plugin Name: NoCommerce
 * Description: Disable commerce features to use WooCommerce as product catalog.
 * Version: 1.0.0-alpha
 * Author: Fervidum
 * Author URI: https://fervidum.github.io/
 * Text Domain: f9nocommerce
 * Domain Path: /languages/
 *
 * Directory: https://fervidum.github.io/nocommerce
 *
 * @package F9nocommerce
 */

// If this file is called directly, abort.
defined( 'ABSPATH' ) || exit;

// Define F9NOCOMMERCE_PLUGIN_FILE.
if ( ! defined( 'F9NOCOMMERCE_PLUGIN_FILE' ) ) {
	define( 'F9NOCOMMERCE_PLUGIN_FILE', __FILE__ );
}

// Include the main F9nocommerce class.
if ( ! class_exists( 'F9nocommerce', false ) ) {
	include_once dirname( __FILE__ ) . '/includes/class-f9nocommerce.php';
}

/**
 * Returns the main instance of F9nocommerce.
 *
 * @return F9nocommerce
 */
function f9nocommerce() {
	return F9nocommerce::instance();
}

f9nocommerce();
