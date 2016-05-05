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
    $this->shop_account_id  = $this->get_option( 'sa_shop_account_id' );

    $this->register_admin_hooks();

    // Check if account id is set, else don't do any tracking
    if( ! $this->shop_account_id ) {
      return;
    }

    $this->tracking = new WC_Skroutz_Analytics_Tracking( $this->shop_account_id );
  }

  public function init_form_fields() {
        $this->form_fields = array(
            'sa_shop_account_id' => array(
                'title'       => __( 'Shop Account ID', 'wc-skroutz-analytics' ),
                'type'        => 'text',
                'description' => __( 'The shop account ID is provided by Skroutz', 'wc-skroutz-analytics' ),
                'placeholder' => 'SA-XXXX-YYYY',
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
      $message =  sprintf(__('An error occured. %s', 'wc-skroutz-analytics') ,$value );

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

  private function is_valid_skroutz_analytics_code( $code ) {
    return preg_match('/^sa-\d{4}-\d{4}$/i', $code ) ? true : false;
  }

  private function register_admin_hooks() {
    add_action( 'woocommerce_update_options_integration_' . $this->id, array( $this, 'process_admin_options' ) );
        add_filter( 'woocommerce_settings_api_sanitized_fields_' . $this->id, array( $this, 'sanitize_settings' ) );
  }

}
