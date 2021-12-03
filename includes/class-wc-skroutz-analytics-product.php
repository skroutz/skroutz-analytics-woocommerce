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

		if ( $this->product->is_type( 'variation' ) && $this->items_product_id_settings['parent_id_enabled'] != 'no' ) {
			$parent_or_variation = $this->get_parent_product();
		}

		$product_id = $this->get_custom_product_id($parent_or_variation);

		if ( ! $product_id ) {
			$product_id = $this->items_product_id_settings['id'] == 'sku'
				? $parent_or_variation->get_sku()
				: $parent_or_variation->get_id();
		}

		if ( ! $product_id ) {
			// If product sku is not found set a default id. Analytics does not accept line items without a product id.
			$product_id = "wc-sa-{$this->product->get_id()}";
		}

		if ( $this->product->is_type( 'variation' ) && $this->items_product_id_settings['parent_id_enabled'] == 'parent_id_term_id' ) {
			$product_id .= $this->get_product_terms_suffix();
		}

		return apply_filters( 'wc_skroutz_analytics_product_id_filter', $product_id, $this->product );
	}

	/**
	 * Returns the variable product id concatenated with first term id. This id will help us to show sku reviews widget
	 * for variable products.
	 *
	 * @return string|integer  The product id concatenated with first term id
	 *
	 * @since    1.7.0
	 */
	public function get_reviews_widget_product_id() {
		if ( $this->product->is_type( 'variable' ) && $this->items_product_id_settings['parent_id_enabled'] == 'no' ) {
			// TODO Use only get_visible_children when we drop support for WooCommerce < 3.0
			$variation_ids = method_exists( $this->product, 'get_visible_children' ) ?
				$this->product->get_visible_children() :
				$this->product->get_children(true);

			// Verify there is at least one visible variation otherwise use the parent product
			if ( isset($variation_ids[0]) ) {
				$this->product = wc_get_product($variation_ids[0]);
			}
		}

		$product_id = $this->get_custom_product_id($this->product);

		if ( ! $product_id && $this->items_product_id_settings['id'] == 'sku' ) {
			$product_id = $this->product->get_sku();
		}

		if ( ! $product_id ) {
			$product_id = $this->product->get_id();
		}

		if ( $this->product->is_type( 'variable' ) && $this->items_product_id_settings['parent_id_enabled'] == 'parent_id_term_id' ) {
			$product_id .= $this->get_product_first_term_suffix();
		}

		return apply_filters( 'wc_skroutz_analytics_product_reviews_widget_id_filter', $product_id, $this->product );
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

	/**
	 * Get the suffix of the product id based on the grouped attributes settings.
	 * Format: -product_term_id_1-product_term_id_2...
	 *
	 * @return NULL|string The product id suffix. If no product attribute matches any attribute in settings returns null.
	 *
	 * @since    1.5.0
	 * @access   private
	 */
	private function get_product_terms_suffix() {
		$product_attributes = $this->product->get_variation_attributes();

		$terms = null;
		foreach ($this->items_product_id_settings['grouping_attributes'] as $attribute_name) {

			$taxonomy_name = wc_attribute_taxonomy_name($attribute_name);
			// TODO: Use wc_variation_attribute_name() when we drop support for WooCommerce < 2.6
			$variation_attribute_name = 'attribute_' . sanitize_title( $taxonomy_name );

			if ( isset($product_attributes[$variation_attribute_name]) ) {
				$term = get_term_by( 'slug', $product_attributes[$variation_attribute_name], $taxonomy_name );
				if ( $term ) {
					$terms .= "-{$term->term_id}";
				}
			}
		}

		return $terms;
	}

	/**
	 * Get the suffix of the id that we use to sku reviews widget. The suffix is the first term of every
	 * grouped attribute in settings
	 * Format: -variation_id-first_term_id
	 *
	 * @return NULL|string The variable product id suffix. If no product attribute matches any attribute in
	 * settings return null.
	 *
	 * @since    1.7.0
	 * @access   private
	 */
	private function get_product_first_term_suffix() {
		$product_attributes = $this->product->get_variation_attributes();

		$terms = null;
		foreach ($this->items_product_id_settings['grouping_attributes'] as $attribute_name) {

			$taxonomy_name = wc_attribute_taxonomy_name($attribute_name);

			if ( isset($product_attributes[$taxonomy_name][0]) ) {
				$term = get_term_by( 'slug', $product_attributes[$taxonomy_name][0], $taxonomy_name );

				if ( $term ) {
					$terms .= "-{$term->term_id}";
				}
			}
		}

		return $terms;
	}
}
