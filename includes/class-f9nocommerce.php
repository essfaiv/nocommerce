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
		$this->includes();
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

	/**
	 * What type of request is this?
	 *
	 * @param  string $type admin, ajax, cron or frontend.
	 * @return bool
	 */
	private function is_request( $type ) {
		switch ( $type ) {
			case 'admin':
				return is_admin();
			case 'ajax':
				return defined( 'DOING_AJAX' );
			case 'cron':
				return defined( 'DOING_CRON' );
			case 'frontend':
				return ( ! is_admin() || defined( 'DOING_AJAX' ) ) && ! defined( 'DOING_CRON' );
		}
	}

	/**
	 * Include required core files used in admin and on the frontend.
	 */
	public function includes() {
		if ( $this->is_request( 'admin' ) ) {
			include_once F9NOCOMMERCE_ABSPATH . 'includes/admin/class-f9nocommerce-admin.php';
		}
	}

	private function is_woocommerce_activated() {
		return class_exists( 'WooCommerce' );
	}

	public function init() {
		$this->load_plugin_textdomain();

		add_filter( 'woocommerce_create_pages', array( $this, 'create_pages' ) );
		add_filter( 'woocommerce_register_post_type_product', array( $this, 'post_type_product' ) );
		add_action( 'wp_dashboard_setup', array( $this, 'remove_wc_dashboard_status' ), 11 );
		add_filter( 'woocommerce_show_admin_bar_visit_store', '__return_false' );
		add_filter( 'request', array( $this, 'remove_product_cat_base' ) );
		add_filter( 'term_link', array( $this, 'product_cat_link' ), 10, 3 );
		add_filter( 'post_type_link', array( $this, 'product_permalink' ), 10, 3 );
		add_action( 'generate_rewrite_rules', array( $this, 'product_rewrite_rule' ) );
	}

	public function load_plugin_textdomain() {
		$locale = is_admin() && function_exists( 'get_user_locale' ) ? get_user_locale() : get_locale();
		$locale = apply_filters( 'plugin_locale', $locale, 'f9nocommerce' );

		load_textdomain( 'f9nocommerce', dirname( F9NOCOMMERCE_PLUGIN_FILE ) . '/languages/' . $locale . '.mo' );
	}

	/**
	 * Get the plugin url.
	 *
	 * @return string
	 */
	public function plugin_url() {
		return untrailingslashit( plugins_url( '/', F9NOCOMMERCE_PLUGIN_FILE ) );
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
		$args['menu_icon'] = 'dashicons-building';
		$args['labels']['name'] = __( 'Apartamentos' );
		$args['labels']['menu_name'] = __( 'Apartamentos' );
		$args['labels']['all_items'] = __( 'Todos os apartamentos' );
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

	public function remove_wc_dashboard_status() {
		remove_meta_box( 'woocommerce_dashboard_status', 'dashboard', 'normal' );
	}

	public function remove_product_cat_base( $vars ) {
		global $wpdb;
		$name = '';
		if ( isset( $vars['name'] ) ) {
			$name = $vars['name'];
		}
		if ( ! apply_filters( 'f9nocommerce_remove_product_cat_base_request', false, $name ) ) {
			return $vars;
		}
		if ( ! empty( $vars['pagename'] ) || ! empty( $vars['category_name'] ) || ! empty( $vars['name'] ) || ! empty( $vars['attachment'] ) ) {
			$slug = ! empty( $vars['pagename'] ) ? $vars['pagename'] : ( ! empty( $vars['name'] ) ? $vars['name'] : ( ! empty( $vars['category_name'] ) ? $vars['category_name'] : $vars['attachment'] ) );
			$exists = $wpdb->get_var( $wpdb->prepare( "SELECT t.term_id FROM $wpdb->terms t LEFT JOIN $wpdb->term_taxonomy tt ON tt.term_id = t.term_id WHERE tt.taxonomy = 'product_cat' AND t.slug = %s", array( $slug ) ) );
			if ( $exists ) {
				$old_vars = $vars;
				$vars = array( 'product_cat' => $slug );
				if ( ! empty( $old_vars['paged'] ) || ! empty( $old_vars['page'] ) ) {
					$vars['paged'] = ! empty( $old_vars['paged'] ) ? $old_vars['paged'] : $old_vars['page'];
				}
				if ( ! empty( $old_vars['orderby'] ) ) {
					$vars['orderby'] = $old_vars['orderby'];
					if ( ! empty( $old_vars['order'] ) ) {
						$vars['order'] = $old_vars['order'];
					}
				}
			}
		}
		return $vars;
	}

	public function product_cat_link( $url, $term, $taxonomy ) {
		if ( apply_filters( 'f9nocommerce_remove_product_cat_base_link', false, $url, $term, $taxonomy ) ) {
			$permalinks = wc_get_permalink_structure();
			$url = str_replace( "/{$permalinks['category_rewrite_slug']}/", '/', $url );
		}
		return $url;
	}

	public function product_permalink( $permalink, $post, $leavename ) {
		if ( 'product' != $post->post_type ) {
			return $permalink;
		}
		// Get the categories for the product
		$categories = wp_get_post_terms( $post->ID, 'product_cat', array( 'fields' => 'slugs' ) );
		if ( ! empty( $categories ) && in_array( 'imoveis-de-terceiros', $categories ) ) {
			$permalinks = wc_get_permalink_structure();
			$permalink = str_replace( "{$permalinks['product_rewrite_slug']}", '/imovel-de-terceiros', $permalink );
		}
		return $permalink;
	}

	public function product_rewrite_rule( $wp_rewrite ) {
		$permalinks = wc_get_permalink_structure();
		// This rule will will match the post id in imovel-de-terceiro/%postname% struture
		$new_rules = array();
		$slug = trim( $permalinks['product_rewrite_slug'], '/' );
		$new_rules[ "^({$slug}|imovel-de-terceiros)/([^/]*)/?" ] = 'index.php?product=$matches[2]';
		$wp_rewrite->rules = array_merge( $new_rules, $wp_rewrite->rules );
		return $wp_rewrite;
	}
}
