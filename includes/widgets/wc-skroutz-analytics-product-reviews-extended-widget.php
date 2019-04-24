<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * The Product Reviews Extended Widget
 *
 * @package    WC_Skroutz_Analytics_Product_Reviews_Extended_Widget
 * @subpackage WC_Skroutz_Analytics_Product_Reviews_Extended_Widget/includes/widgets
 * @author     Skroutz SA <analytics@skroutz.gr>
 */
class WC_Skroutz_Analytics_Product_Reviews_Extended_Widget extends WP_Widget {

	private $settings;

	/**
	 * Sets up the widgets name, settings etc
	 */
	public function __construct() {
		$this->settings = WC_Skroutz_Analytics_Settings::get_instance();

		$widget_ops = array(
			'classname' => 'skroutz-product-reviews-extended-widget',
			'description' => sprintf(
				__( 'Display the extended widget with product ratings and reviews from %s', 'wc-skroutz-analytics' ),
				ucfirst( $this->settings->get_flavor() )
			),
		);

		parent::__construct(
			'WC_Skroutz_Analytics_Product_Reviews_Extended_Widget',
			sprintf( __( 'Extended Product Reviews %s Widget', 'wc-skroutz-analytics' ), ucfirst( $this->settings->get_flavor() ) ),
			$widget_ops
		);
	}

	/**
	 * Outputs the content of the widget
	 *
	 * @param array $args
	 * @param array $instance
	 */
	public function widget( $args, $instance ) {
		if ( ! is_product() ) {
			return;
		}

		$sa_product = new WC_Skroutz_Analytics_Product(wc_get_product(), $this->settings->get_product_id_settings());

		echo $args['before_widget'];
		echo "<!-- Skroutz Analytics WooCommerce plugin - Extended Widget - v".WC_Skroutz_Analytics::PLUGIN_VERSION." -->";
		echo "<div id='{$this->div_id()}' data-product-id='{$sa_product->get_id()}'>{$this->preview_placeholder()}</div>";
		echo $args['after_widget'];
	}

	private function div_id() {
		return "{$this->settings->get_flavor()}-product-reviews-extended";
	}

	private function preview_placeholder() {
		return is_customize_preview() ? "{$this->settings->get_flavor()} extended widget placeholder" : '';
	}
}
