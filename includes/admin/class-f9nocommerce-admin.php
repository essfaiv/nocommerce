<?php
/**
 * F9nocommerce Admin
 *
 * @class F9nocommerce_Admin
 * @author Fervidum
 * @category Admin
 * @package F9nocommerce/Admin
 * @version 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * F9nocommerce_Admin class.
 */
class F9nocommerce_Admin {

	/**
	 * Constructor.
	 */
	public function __construct() {
		add_action( 'init', array( $this, 'includes' ) );
	}

	/**
	 * Include any classes we need within admin.
	 */
	public function includes() {
		include_once dirname( __FILE__ ) . '/class-f9nocommerce-admin-assets.php';
	}
}

return new F9nocommerce_Admin();
