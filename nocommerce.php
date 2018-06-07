<?php
/**
 * Plugin Name: NoCommerce
 * Description: Disable commerce features to use WooCommerce as product catalog.
 * Version: 1.0.0
 * Author: Fervidum
 * Text Domain: f9nocommerce
 * Domain Path: /languages/
 *
 * @package F9nocommerce
 */

defined( 'ABSPATH' ) || exit;

// Define F9NOCOMMERCE_PLUGIN_FILE.
if ( ! defined( 'F9NOCOMMERCE_PLUGIN_FILE' ) ) {
	define( 'F9NOCOMMERCE_PLUGIN_FILE', __FILE__ );
}

// Include the main F9nocommerce class.
if ( ! class_exists( 'F9nocommerce' ) ) {
	include_once dirname( F9NOCOMMERCE_PLUGIN_FILE ) . '/includes/class-f9nocommerce.php';
}

/**
 * Main instance of F9nocommerce.
 *
 * Returns the main instance of F9nocommerce to prevent the need to use globals.
 *
 * @since 1.0
 * @return F9nocommerce
 */
function f9nocommerce() {
	return F9nocommerce::instance();
}

f9nocommerce();
