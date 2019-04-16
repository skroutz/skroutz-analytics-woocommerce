<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Given a WC_Product and the plugin's settings the class
 * provides methods for finding the proper product id
 * that should be used across the plugin.
 *
 * @package    WC_Skroutz_Analytics_Product
 * @subpackage WC_Skroutz_Analytics_Product/includes
 * @author     Skroutz SA <analytics@skroutz.gr>
 */
class WC_Skroutz_Analytics_Product {

	/**
	 * The product
	 *
	 * @var WC_Product
	 */
	private $product;

	/**
	 * The items product id options provided by the admin settings
	 *
	 * @var array
	 */
	private $items_product_id_settings;

	/**
	 * Set the product and product id settings
	 *
	 * @param WC_Product $product The WC Product
	 * @param array $items_product_id_settings The product id options provided by the admin settings
	 *
	 * @since    1.4.0
	 */
	public function __construct( $product, $items_product_id_settings ) {
		$this->product = $product;
		$this->items_product_id_settings = $items_product_id_settings;
	}

	/**
	 * Returns the product id that should be reported to Analytics based on
	 * product id admin settings.
	 *
	 * @return string|integer  The product id that should be reported to Analytics
	 *
	 * @since    1.4.0
	 */
	public function get_id() {
		$parent_or_variation = $this->product;

		if ( $this->items_product_id_settings['parent_id_enabled'] == 'yes' && $this->product->is_type( 'variation' ) ) {
			$parent_or_variation = $this->get_parent_product();
		}

		$product_id = $this->get_custom_product_id($parent_or_variation);

		if ( $product_id ) {
			return $product_id; // return the custom_id from postmeta table
		} elseif ( $this->items_product_id_settings['id'] == 'sku' ) {
			$product_id = $parent_or_variation->get_sku();
		} else {
			$product_id = $parent_or_variation->get_id();
		}

		return $product_id ? $product_id : "wc-sa-{$this->product->get_id()}";
	}

	/**
	 * Get the custom postmeta id if exists, based on admin settings
	 *
	 * @param WC_Product $product The WC Product
	 * @return NULL|string|integer The custom postmeta id
	 *
	 * @since    1.4.0
	 * @access   private
	 */
	private function get_custom_product_id( $product ) {
		$product_id = null;

		if ( $this->items_product_id_settings['custom_id_enabled'] == 'yes' && $this->items_product_id_settings['custom_id'] ) {
			$product_id = get_post_meta(
				$product->get_id(),
				$this->items_product_id_settings['custom_id'],
				true
			);
		}

		return $product_id;
	}

	/**
	 * Get the parent product of a variation product.
	 *
	 * @return WC_Product|null|false The parent product
	 *
	 * @since    1.4.0
	 * @access   private
	 */
	private function get_parent_product() {
		// TODO Use only get_parent_id when we drop support for WooCommerce < 3.0
		if ( method_exists( $this->product, 'get_parent_id' ) ) {
			$parent_id = $this->product->get_parent_id();
		} else {
			$parent_id = wp_get_post_parent_id( $this->product->get_id() );
		}

		return wc_get_product( $parent_id );
	}
}
