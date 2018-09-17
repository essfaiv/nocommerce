<?php
/**
 * Load assets
 *
 * @package F9nocommerce/Admin
 * @version 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'F9nocommerce_Admin_Assets', false ) ) :

	/**
	 * F9nocommerce_Admin_Assets Class.
	 */
	class F9nocommerce_Admin_Assets {

		/**
		 * Hook in tabs.
		 */
		public function __construct() {
			add_action( 'admin_enqueue_scripts', array( $this, 'admin_styles' ) );
		}

		/**
		 * Enqueue styles.
		 */
		public function admin_styles() {

			// Register admin styles.
			wp_register_style( 'f9nocommerce_admin_menu_styles', f9nocommerce()->plugin_url() . '/assets/css/menu.css', array(), F9NOCOMMERCE_VERSION );
		}
	}

endif;

return new F9nocommerce_Admin_Assets();
