<?php

if ( ! defined( 'ABSPATH' ) ) {
  exit;
}

/**
 * A singleton providing the settings of the plugin.
 *
 * @package    WC_Skroutz_Analytics_Product
 * @subpackage WC_Skroutz_Analytics_Product/includes
 * @author     Skroutz SA <analytics@skroutz.gr>
 */
class WC_Skroutz_Analytics_Settings {

	const DEFAULT_FLAVOR = 'skroutz';
	const DEFAULT_SHOP_ACCOUNT_ID = null;
	const DEFAULT_ITEMS_PRODUCT_ID = 'sku';
	const DEFAULT_ITEMS_PRODUCT_PARENT_ID_ENABLED = 'no';
	const DEFAULT_ITEM_GROUPING_ATTRIBUTES = array();
	const DEFAULT_ITEMS_CUSTOM_ID_ENABLED = 'no';
	const DEFAULT_ITEMS_CUSTOM_ID = null;
	const DEFAULT_CUSTOM_GLOBAL_OBJECT_NAME_ENABLED = 'no';
	const DEFAULT_CUSTOM_GLOBAL_OBJECT_NAME = null;

	/**
	 * The singleton instance
	 * @var string
	 */
	private static $instance;

	/**
	 * The flavor (is the site) provided by the admin settings
	 * @var string
	 */
	private $flavor;

	/**
	 * The shop account id provided by the admin settings
	 * @var string
	 */
	private $shop_account_id;

	/**
	 * The items product id options provided by the admin settings
	 * @var array
	 */
	private $items_product_id_settings;

	/**
	 * The global object name provided by the admin settings
	 * @var array
	 */
	private $global_object_name_settings;

	/**
	 * The public function to call in order to get the only instance
	 * of the class. On first call a new instance will be created.
	 *
	 * @since 1.4.0
	 */
	public static function get_instance() {
		if ( ! isset( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Initialize the class and set the settings of the plugin.
	 *
	 * @since    1.4.0
	 * @access   private
	 */
	private function __construct() {
		$this->set_settings();
	}

	/**
	 * Get the flavor set in the plugin or the default if is not set
	 *
	 * @since 1.4.0
	 */
	public function get_flavor() {
		return $this->flavor;
	}

	/**
	 * Get the shop account id set in the plugin
	 *
	 * @since 1.4.0
	 */
	public function get_shop_account_id() {
		return $this->shop_account_id;
	}

	/**
	 * Get the product id settings set in the plugin or the default if is not set
	 *
	 * @since 1.4.0
	 */
	public function get_product_id_settings() {
		return $this->items_product_id_settings;
	}

	/**
	 * Get the global object name settings set in the plugin or the default if is not set
	 *
	 * @since 1.4.0
	 */
	public function get_global_object_name_settings() {
		return $this->global_object_name_settings;
	}

	/**
	 * Get the stored plugin settings. Set the defaults if not available.
	 *
	 * @since    1.4.0
	 * @access   private
	 */
	private function set_settings() {
		$settings = get_option( 'woocommerce_wc_skroutz_analytics_settings', [] );

		$this->flavor = isset( $settings['sa_flavor'] ) ? $settings['sa_flavor'] : self::DEFAULT_FLAVOR;
		$this->shop_account_id  = isset( $settings['sa_shop_account_id'] )
			? $settings['sa_shop_account_id']
			: self::DEFAULT_SHOP_ACCOUNT_ID;
		$this->items_product_id_settings = array(
			'id' => isset( $settings['sa_items_product_id'] )
				? $settings['sa_items_product_id']
				: self::DEFAULT_ITEMS_PRODUCT_ID,
			'parent_id_enabled' => isset( $settings['sa_items_product_parent_id_enabled'] )
				? $settings['sa_items_product_parent_id_enabled']
				: self::DEFAULT_ITEMS_PRODUCT_PARENT_ID_ENABLED,
			'grouping_attributes' => $this->grouping_attributes( $settings ),
			'custom_id_enabled' => isset( $settings['sa_items_custom_id_enabled'] )
				? $settings['sa_items_custom_id_enabled']
				: self::DEFAULT_ITEMS_CUSTOM_ID_ENABLED,
			'custom_id' => isset( $settings['sa_items_custom_id'] )
				? $settings['sa_items_custom_id']
				: self::DEFAULT_ITEMS_CUSTOM_ID,
		);
		$this->global_object_name_settings = array(
			'enabled' => isset( $settings['sa_custom_global_object_name_enabled'] )
				? $settings['sa_custom_global_object_name_enabled']
				: self::DEFAULT_CUSTOM_GLOBAL_OBJECT_NAME_ENABLED,
			'name' => isset( $settings['sa_custom_global_object_name'] )
				? $settings['sa_custom_global_object_name']
				: self::DEFAULT_CUSTOM_GLOBAL_OBJECT_NAME,
		);
	}

	/**
	 * Returns the WooCommerce attributes that are selected for product ID grouping, in the order they were selected.
	 * @param array $settings The fetched plugin settings
	 * @return array [attribute_id => attribute_name] ordered mapping of the selected attributes
	 *
	 * @since  1.5.0
	 * @access private
	 */
	private function grouping_attributes( $settings ) {
		$attributes_id_name = wp_list_pluck( wc_get_attribute_taxonomies(), 'attribute_name', 'attribute_id' );

		$selected_attributes = isset( $settings['sa_items_grouping_attributes'] ) && is_array( $settings['sa_items_grouping_attributes'] )
			? array_flip( $settings['sa_items_grouping_attributes'] )
			: self::DEFAULT_ITEM_GROUPING_ATTRIBUTES;

		// Handle the case when a previously added attribute has been deleted from WooCommerce but not from our settings.
		$selected_attributes = array_intersect_key( $selected_attributes, $attributes_id_name );

		// set the names of the selected attributes
		array_walk(
			$selected_attributes,
			function ( &$value, $key ) use ( $attributes_id_name ) {
				$value = $attributes_id_name[ $key ];
			}
		);

		return $selected_attributes;
	}
}
