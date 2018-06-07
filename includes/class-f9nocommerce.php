<?php
/**
 * F9nocommerce setup
 *
 * @package F9nocommerce
 * @since 1.0.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Main F9nocommerce Class.
 *
 * @class F9nocommerce
 */
final class F9nocommerce {

	/**
	 * F9nocommerce version.
	 *
	 * @var string
	 */
	public $version = '1.0.0';

	/**
	 * The single instance of the class.
	 *
	 * @var F9nocommerce
	 * @since 1.0
	 */
	protected static $_instance = null;

	/**
	 * Main F9nocommerce Instance.
	 *
	 * Ensures only one instance of F9nocommerce is loaded or can be loaded.
	 *
	 * @since 1.0
	 * @static
	 * @see f9nocommerce()
	 * @return F9nocommerce - Main instance.
	 */
	public static function instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}

	public function __construct() {
		$this->define_constants();
		$this->init_hooks();
	}

	private function init_hooks() {
		add_action( 'init', array( $this, 'init' ), 0 );

		add_action( 'init', array( __CLASS__, 'register_post_types' ), 6 );
	}

	private function define_constants() {
		$this->define( 'F9NOCOMMERCE_ABSPATH', dirname( F9NOCOMMERCE_PLUGIN_FILE ) . '/' );
		$this->define( 'F9NOCOMMERCE_VERSION', $this->version );
	}

	private function define( $name, $value ) {
		if ( ! defined( $name ) ) {
			define( $name, $value );
		}
	}

	private function is_woocommerce_activated() {
		return class_exists( 'WooCommerce' );
	}

	public function init() {
		$this->load_plugin_textdomain();

		add_filter( 'woocommerce_create_pages', array( $this, 'create_pages' ) );
		add_filter( 'woocommerce_register_post_type_product', array( $this, 'post_type_product' ) );
	}

	public function load_plugin_textdomain() {
		$locale = is_admin() && function_exists( 'get_user_locale' ) ? get_user_locale() : get_locale();
		$locale = apply_filters( 'plugin_locale', $locale, 'f9nocommerce' );

		load_textdomain( 'f9nocommerce', dirname( F9NOCOMMERCE_PLUGIN_FILE ) . '/languages/' . $locale . '.mo' );
	}

	public function create_pages( $pages ) {
		$dont_create = array(
			'cart',
			'checkout',
			'myaccount',
		);

		$pages['shop'] = array(
			'name' => _x( 'products', 'Page slug', 'f9nocommerce' ),
			'title' => _x( 'Products', 'Page title', 'f9nocommerce' ),
			'content' => '',
		);

		foreach ( $dont_create as $page ) {
			unset( $pages[ $page ] );
		}

		return $pages;
	}

	public function post_type_product( $args ) {
		$product_slug = apply_filters( 'woocommerce_post_type_product_slug', 'product' );
		if ( 'product' !== $product_slug ) {
			register_post_type(
				$product_slug,
				$args
			);
		}
		return $args;
	}

	public static function register_post_types() {
		$product_slug = apply_filters( 'woocommerce_post_type_product_slug', 'product' );
		if ( 'product' !== $product_slug ) {
			unregister_post_type( 'product' );
		}
	}
}
