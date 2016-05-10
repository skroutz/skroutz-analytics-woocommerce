<?php

if ( ! defined( 'ABSPATH' ) ) {
  exit;
}

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       www.skroutz.gr
 * @since      1.0.0
 *
 * @package    WC_Skroutz_Analytics
 * @subpackage WC_Skroutz_Analytics/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    WC_Skroutz_Analytics
 * @subpackage WC_Skroutz_Analytics/admin
 * @author     Skroutz <info@skroutz.gr>
 */
class WC_Skroutz_Analytics_Integration extends WC_Integration {

  /**
   * The tracking is responsible for all the tracking actions in the plugin.
   *
   * @since    1.0.0
   * @access   protected
   * @var      WC_Skroutz_Analytics_Tracking $tracking Defines all the tracking actions.
   */
  private $tracking;

  /**
   * Initialize the class and set its properties.
   *
   * @since    1.0.0
   */
  public function __construct() {
    $this->id = WC_Skroutz_Analytics::PLUGIN_ID;
    $this->method_title = __( 'Skroutz Analytics', 'wc-skroutz-analytics' );
    $this->method_description    = __( 'Skroutz Analytics is a free service that gives you the ability to generate statistics about your shop visitors and products.', 'wc-skroutz-analytics' );

    // Load the settings
    $this->init_form_fields();
    $this->init_settings();

    //Skroutz Analytics admin settings
    $this->flavor = $this->get_option( 'sa_flavor' );
    $this->shop_account_id  = $this->get_option( 'sa_shop_account_id' );
    $this->items_product_id  = $this->get_option( 'sa_items_product_id' );

    $this->register_admin_hooks();

    // Check if account id is set, else don't do any tracking
    if( ! $this->shop_account_id ) {
      return;
    }

    $this->tracking = new WC_Skroutz_Analytics_Tracking( $this->flavor, $this->shop_account_id, $this->items_product_id );
  }

  	public function init_form_fields() {
  		$merchants_link = "<a id='merchants_link' href='' target='_blank'></a>"; //will be populated by the client

        $this->form_fields = array(
            'sa_flavor' => array(
                'title'       => __( 'Site', 'wc-skroutz-analytics' ),
                'type'        => 'select',
                'description' => __( 'Specify the site your eshop reports to.', 'wc-skroutz-analytics' ),
                'options' => array( 'Skroutz' => 'Skroutz', 'Alve' => 'Alve', 'Scrooge' => 'Scrooge' ),
                'default' => 'Skroutz',
            ),
            'sa_shop_account_id' => array(
                'title'       => __( 'Shop Account ID', 'wc-skroutz-analytics' ),
                'type'        => 'text',
                'description' => sprintf(__('The shop account ID is provided by %s', 'wc-skroutz-analytics'), $merchants_link ),
                'placeholder' => 'SA-XXXX-YYYY',
            ),
            'sa_items_product_id' => array(
                'title'       => __( 'Product ID', 'wc-skroutz-analytics' ),
                'type'        => 'select',
                'description' => __( 'Specify the product ID that should be sent to analytics.', 'wc-skroutz-analytics' ),
                'options' => array( 'sku' => 'Product SKU', 'id' => 'Product ID' ),
                'default' => 'sku',
                'desc_tip' => __( 'It must the same product ID used in the XML feed provided to Skroutz.', 'wc-skroutz-analytics' ),
            ),
        );
    }

    /**
   * Validate the shop account id
   *
   * @param string key The key to be validated
   * @return string The value that was submitted
   */
  public function validate_sa_shop_account_id_field( $key ) {
    $value = $_POST[ $this->plugin_id . $this->id . '_' . $key ];

    if ( isset( $value ) && !$this->is_valid_skroutz_analytics_code( $value ) ) {
      $this->errors[$key] = sprintf(__('Shop Account ID is invalid', 'wc-skroutz-analytics') );
    }

    return $value;
  }

  /**
   * Display errors by overriding the display_errors() method
   */
  public function display_errors() {
    foreach ( $this->errors as $key => $value ) {
      $class = 'notice notice-error';
      $message =  sprintf(__('An error occurred. %s', 'wc-skroutz-analytics') ,$value );

      printf( '<div class="%s"><p>%s</p></div>', $class, $message );
    }
  }

  /**
   * Sanitize admin settings
   */
  public function sanitize_settings( $settings ) {
    if ( isset( $settings['sa_shop_account_id'] ) ) {
      $settings['sa_shop_account_id'] = strtoupper( $settings['sa_shop_account_id'] );
    }

    return $settings;
  }

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

  private function is_valid_skroutz_analytics_code( $code ) {
    return preg_match('/^sa-\d{4}-\d{4}$/i', $code ) ? true : false;
  }

  	private function register_admin_hooks() {
  		add_action( 'admin_enqueue_scripts', array( $this, 'load_admin_assets') );

    	add_action( 'woocommerce_update_options_integration_' . $this->id, array( $this, 'process_admin_options' ) );
        add_filter( 'woocommerce_settings_api_sanitized_fields_' . $this->id, array( $this, 'sanitize_settings' ) );
  	}

}
