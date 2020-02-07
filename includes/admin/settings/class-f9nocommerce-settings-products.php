<?php
/**
 * F9nocommerce Product Settings
 *
 * @package F9nocommerce/Admin
 */

defined( 'ABSPATH' ) || exit;

if ( class_exists( 'F9nocommerce_Settings_Products', false ) ) {
	return new F9nocommerce_Settings_Products();
}

/**
 * F9nocommerce_Settings_Products.
 */
class F9nocommerce_Settings_Products extends WC_Settings_Products {

	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->id = 'products';
		add_filter( 'woocommerce_get_sections_' . $this->id, array( $this, 'sections' ) );
		add_filter( 'woocommerce_get_settings_' . $this->id, array( $this, 'settings' ), 10, 2 );
	}

	/**
	 * Get sections.
	 *
	 * @return array
	 */
	public function sections( $sections ) {
		$sections['labels'] = __( 'Labels', 'f9nocommerce' );

		return $sections;
	}

	/**
	 * Get settings array.
	 *
	 * @param array $settings_wc Settings.
	 * @param string $current_section Current section name.
	 * @return array
	 */
	public function settings( $settings_wc, $current_section = '' ) {
		$settings = array();
		if ( 'labels' === $current_section ) {
			$settings = apply_filters(
				'f9nocommerce_labels_settings',
				array(
					array(
						'title' => __( 'Labels', 'f9nocommerce' ),
						'type'  => 'title',
						'desc'  => '',
						'id'    => 'f9nocommerce_wc_product_labels',
					),

					array(
						'title'    => __( 'Plural label', 'f9nocommerce' ),
						'id'       => 'f9nocommerce_wc_product_label',
						'type'     => 'text',
						'default'  => __( 'Products', 'woocommerce' ),
						'css'      => 'width: 250px;',
						'autoload' => false,
					),

					array(
						'title'    => __( 'Singular label', 'f9nocommerce' ),
						'id'       => 'f9nocommerce_wc_product_singular_label',
						'type'     => 'text',
						'default'  => __( 'Product', 'woocommerce' ),
						'css'      => 'width: 250px;',
						'autoload' => false,
					),

					array(
						'title'    => __( 'All Items', 'f9nocommerce' ),
						'id'       => 'f9nocommerce_wc_product_all_items',
						'type'     => 'text',
						'default'  => __( 'All Products', 'woocommerce' ),
						'css'      => 'width: 250px;',
						'autoload' => false,
					),

					array(
						'type' => 'sectionend',
						'id'   => 'f9nocommerce_wc_product_labels',
					),

				)
			);
		} else {
			$settings = array_merge( $settings_wc, $settings );
		}

		return $settings;
	}
}

return new F9nocommerce_Settings_Products();
