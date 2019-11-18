<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * The admin-specific functionality of the plugin.
 *
 * Integration with the WooCommerce plugin.
 * Define the plugin settings, validation and sanitization.
 * Instantiate the tracking functionality if able
 * Register the widgets if able
 *
 * @package    WC_Skroutz_Analytics
 * @subpackage WC_Skroutz_Analytics/admin
 * @author     Skroutz SA <analytics@skroutz.gr>
 */
class WC_Skroutz_Analytics_Integration extends WC_Integration {

	/**
	* The tracking is responsible for all the tracking actions in the plugin.
	*
	* @since    1.0.0
	* @access   private
	* @var      WC_Skroutz_Analytics_Tracking $tracking Defines all the tracking actions.
	*/
	private $tracking;

	/**
	 * The plugin's settigns.
	 *
	 * @since    1.4.0
	 * @access   private
	 * @var      WC_Skroutz_Analytics_Settings $sa_settings The plugin's settigns.
	 */
	private $sa_settings;

	/**
	* Initialize the class and set its properties.
	*
	* @since    1.0.0
	*/
	public function __construct() {
		$this->id = WC_Skroutz_Analytics::PLUGIN_ID;
		$this->method_title = __( 'Skroutz Analytics', 'wc-skroutz-analytics' );
		$this->method_description    = __( 'Skroutz Analytics is a free service that gives you the ability to generate statistics about your shop visitors and products.', 'wc-skroutz-analytics' );

		//Add settings action link to plugins listing
		add_filter( 'plugin_action_links_' . SA_PLUGIN_BASENAME, array( $this, 'add_action_links' ));

		$this->sa_settings = WC_Skroutz_Analytics_Settings::get_instance();
		$this->init_form_fields();
		$this->init_settings();

		$this->register_admin_hooks();

		// Check if account id is set, else don't do any tracking
		if ( ! $this->sa_settings->get_shop_account_id() ) {
			return;
		}

		$this->tracking = new WC_Skroutz_Analytics_Tracking();

		$this->register_widgets();
	}

	/**
	* Add settings action link to plugins listing
	*
	* @param array links The existing action links
	* @return array The final action links to be displayed
	*
	* @since    1.0.0
	*/
	public function add_action_links ( $links ) {
		$action_links = array(
			'settings' => sprintf(
				'<a href="%s" title="%s"> %s </a>',
				admin_url( 'admin.php?page=wc-settings&tab=integration&section=wc_skroutz_analytics'),
				__( 'View Skroutz Analytics Settings', 'wc-skroutz-analytics'),
				__( 'Settings', 'wc-skroutz-analytics' )
			),
		);

		return array_merge( $links, $action_links );
	}

	/**
	* Define the plugin form fields and settings
	*
	* @since    1.0.0
	*/
	public function init_form_fields() {
		$merchants_link = "<a id='merchants_link' href='' target='_blank'></a>"; //will be populated by the client
		$default_object_name = constant( "WC_Skroutz_Analytics_Flavors::".$this->sa_settings->get_flavor()."_global_object_name" );

		$this->form_fields = array(
			'sa_flavor' => array(
				'title'       => __( 'Site', 'wc-skroutz-analytics' ),
				'type'        => 'select',
				'description' => __( 'Specify the site your eshop reports to.', 'wc-skroutz-analytics' ),
				'options'     => array( 'skroutz' => 'Skroutz', 'alve' => 'Alve', 'scrooge' => 'Scrooge' ),
				'default'     => WC_Skroutz_Analytics_Settings::DEFAULT_FLAVOR,
			),
			'sa_shop_account_id' => array(
				'title'       => __( 'Shop Account ID', 'wc-skroutz-analytics' ),
				'type'        => 'text',
				'description' => sprintf(__('The shop account ID is provided by %s', 'wc-skroutz-analytics'), $merchants_link ),
				'placeholder' => 'SA-XXXX-YYYY',
				'default'     => WC_Skroutz_Analytics_Settings::DEFAULT_SHOP_ACCOUNT_ID,
			),
			'sa_items_product_id' => array(
				'title'       => __( 'Product ID', 'wc-skroutz-analytics' ),
				'type'        => 'select',
				'description' => __( 'Specify the product ID that should be sent to analytics.', 'wc-skroutz-analytics' ),
				'options'     => array( 'sku' => 'Product SKU', 'id' => 'Product ID' ),
				'default'     => WC_Skroutz_Analytics_Settings::DEFAULT_ITEMS_PRODUCT_ID,
				'desc_tip'    => __( 'It must have the same product ID used in the XML feed provided to Skroutz.', 'wc-skroutz-analytics' ),
			),
			'sa_items_product_parent_id_enabled' => array(
				'type'    => 'checkbox',
				'label'   => __( 'Always send parent product ID/SKU (variation ids will be ignored)', 'wc-skroutz-analytics' ),
				'default' => WC_Skroutz_Analytics_Settings::DEFAULT_ITEMS_PRODUCT_PARENT_ID_ENABLED,
			),
			'sa_items_custom_id_enabled' => array(
				'type'    => 'checkbox',
				'label'   => __( 'Use custom postmeta id', 'wc-skroutz-analytics' ),
				'default' => WC_Skroutz_Analytics_Settings::DEFAULT_ITEMS_CUSTOM_ID_ENABLED,
			),
			'sa_items_custom_id' => array(
				'type'        => 'text',
				'description' => __( 'Specify a custom id key that will be used to retrieve the product id from postmeta table.', 'wc-skroutz-analytics' ),
				'placeholder' => 'custom_id',
				'desc_tip'    => __( 'If custom id key is not found the Product ID/SKU will be used.', 'wc-skroutz-analytics' ),
				'default'     => WC_Skroutz_Analytics_Settings::DEFAULT_ITEMS_CUSTOM_ID,
			),
			'sa_custom_global_object_name_enabled' => array(
				'title'    => __( 'Global Object Name', 'wc-skroutz-analytics' ),
				'type'     => 'checkbox',
				'label'    => __( 'Use custom global object name', 'wc-skroutz-analytics' ),
				'desc_tip' => __( 'Change global object name when there is a conflict with the default value.', 'wc-skroutz-analytics' ),
				'default'  => WC_Skroutz_Analytics_Settings::DEFAULT_CUSTOM_GLOBAL_OBJECT_NAME_ENABLED,
			),
			'sa_custom_global_object_name' => array(
				'type'        => 'text',
				'description' => __( 'Specify a custom global object name that will be used in the analytics tracking code.', 'wc-skroutz-analytics' ),
				'placeholder' => $default_object_name,
				'default'     => WC_Skroutz_Analytics_Settings::DEFAULT_CUSTOM_GLOBAL_OBJECT_NAME,
			),
		);
	}

	/**
	* Validate the shop account id
	*
	* @param string key The key to be validated
	* @return string The value that was submitted
	*
	* @since    1.0.0
	*/
	public function validate_sa_shop_account_id_field( $key ) {
		// TODO: Add second argument ($value) and remove current assignment, when we drop support for WooCommerce < 2.6
		// https://docs.woocommerce.com/wc-apidocs/source-class-WC_Settings_API.html#137
		$value = $_POST[ $this->plugin_id . $this->id . '_' . $key ];

		if ( isset( $value ) && !$this->is_valid_skroutz_analytics_code( $value ) ) {
			WC_Admin_Settings::add_error( __('Shop Account ID is invalid', 'wc-skroutz-analytics') );
		}

		return $value;
	}

	/**
	* Sanitize admin settings
	*
	* @since    1.0.0
	*/
	public function sanitize_settings( $settings ) {
		if ( isset( $settings['sa_shop_account_id'] ) ) {
			$settings['sa_shop_account_id'] = strtoupper( $settings['sa_shop_account_id'] );
		}

		return $settings;
	}

	/**
	* Load the admin assets only when on the settings view of the plugin
	*
	* @since    1.0.0
	*/
	public function load_admin_assets( $hook ) {
		//load the assets only if we are in the settings tab of our plugin
		if ( $hook !== 'woocommerce_page_wc-settings' ) {
			return;
		}

		if ( isset($_GET['tab']) && $_GET['tab'] !== 'integration' ) {
  			return;
		}

		if ( isset($_GET['section']) && $_GET['section'] !== 'wc_skroutz_analytics' ) {
			return;
		}

		wp_register_script(
			'sa_admin_settings',
			plugin_dir_url(dirname(__FILE__)) . 'assets/js/skroutz-analytics-admin-settings.js',
			'',
			WC_Skroutz_Analytics::PLUGIN_VERSION
		);

		$flavors_class = new ReflectionClass('WC_Skroutz_Analytics_Flavors');
		$flavors = $flavors_class->getConstants();

		wp_localize_script(
			'sa_admin_settings',
			WC_Skroutz_Analytics::PLUGIN_ID,
			array( 'flavors' => $flavors )
		);

		wp_enqueue_script( 'sa_admin_settings' );
	}

	/**
	 * Register the inline widget
	 *
	 * @since    1.4.0
	 */
	public function register_skroutz_product_reviews_inline_widget() {
		register_widget( 'WC_Skroutz_Analytics_Product_Reviews_Inline_Widget' );
	}

	/**
	 * Register the extended widget
	 *
	 * @since    1.4.0
	 */
	public function register_skroutz_product_reviews_extended_widget() {
		register_widget( 'WC_Skroutz_Analytics_Product_Reviews_Extended_Widget' );
	}

	/**
	* Analytics code validation rule
	*
	* @since    1.0.0
	* @access   private
	*/
	private function is_valid_skroutz_analytics_code( $code ) {
		return preg_match('/^sa-\d{4}-\d{4}$/i', $code ) ? true : false;
	}

	/**
	* Register the admin hooks for the asset loading, validation and sanitization
	*
	* @since    1.0.0
	* @access   private
	*/
	private function register_admin_hooks() {
		add_action( 'admin_enqueue_scripts', array( $this, 'load_admin_assets') );

		add_action( 'woocommerce_update_options_integration_' . $this->id, array( $this, 'process_admin_options' ) );
		add_filter( 'woocommerce_settings_api_sanitized_fields_' . $this->id, array( $this, 'sanitize_settings' ) );
	}

	/**
	 * Register the inline and extended widgets on widgets init
	 *
	 * @since 1.4.0
	 * @access   private
	 */
	private function register_widgets() {
		add_action( 'widgets_init', array( $this, 'register_skroutz_product_reviews_inline_widget') );
		add_action( 'widgets_init', array( $this, 'register_skroutz_product_reviews_extended_widget') );
	}
}
