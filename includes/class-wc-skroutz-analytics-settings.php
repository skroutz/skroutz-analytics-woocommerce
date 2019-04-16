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

		$this->flavor = isset( $settings['sa_flavor'] ) ? $settings['sa_flavor'] : 'skroutz';
		$this->shop_account_id  = isset( $settings['sa_shop_account_id'] )
									? $settings['sa_shop_account_id']
									: null;
		$this->items_product_id_settings = array(
			'id' => isset( $settings['sa_items_product_id'] ) ? $settings['sa_items_product_id'] : 'sku',
			'parent_id_enabled' => isset( $settings['sa_items_product_parent_id_enabled'] )
									? $settings['sa_items_product_parent_id_enabled']
									: 'no',
			'custom_id_enabled' => isset( $settings['sa_items_custom_id_enabled'] )
									? $settings['sa_items_custom_id_enabled']
									: 'no',
			'custom_id' => isset( $settings['sa_items_custom_id'] ) ? $settings['sa_items_custom_id'] : null,
		);
		$this->global_object_name_settings = array(
			'enabled' => isset( $settings['sa_custom_global_object_name_enabled'] )
							? $settings['sa_custom_global_object_name_enabled']
							: 'no',
			'name' => isset( $settings['sa_custom_global_object_name'] )
						? $settings['sa_custom_global_object_name']
						: null,
		);
	}
}
