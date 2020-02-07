<?php
/**
 * F9nocommerce Admin Settings Class
 *
 * @package F9nocommerce/Admin
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'F9nocommerce_Admin_Settings', false ) ) :

	/**
	 * F9nocommerce_Admin_Settings Class.
	 */
	class F9nocommerce_Admin_Settings {

		/**
		 * Include the settings page classes.
		 */
		public static function settings_pages( $settings_wc ) {
			$settings = array();

			$settings[] = include 'settings/class-f9nocommerce-settings-products.php';

			$settings = apply_filters( 'f9nocommerce_get_settings_pages', $settings );

			return array_merge(
				$settings_wc,
				$settings
			);
		}
	}

endif;
