<?php

/**
 * The tracking functionality of the plugin.
 *
 * @link       www.skroutz.gr
 * @since      1.0.0
 *
 * @package    WC_Skroutz_Analytics_Tracking
 * @subpackage WC_Skroutz_Analytics_Tracking/includes
 */

/**
 * The tracking functionality of the plugin.
 *
 * @package    WC_Skroutz_Analytics_Tracking
 * @subpackage WC_Skroutz_Analytics_Tracking/includes
 * @author     Skroutz <info@skroutz.gr>
 */
class WC_Skroutz_Analytics_Tracking {

	/**
	* The shop account id provided by the admin settings
	* @var string
	*/
	private $shop_account_id;

	/**
	* Define the core functionality of the plugin.
	*
	* Set the plugin name and the plugin version that can be used throughout the plugin.
	* Load the dependencies, define the locale, and set the hooks for the admin area and
	* the public-facing side of the site.
	*
	* @since    1.0.0
	*/
	public function __construct( $shop_account_id ) {
		$this->shop_account_id = $shop_account_id;

	  	// Page tracking script
	    add_action( 'wp_enqueue_scripts', array( $this, 'load_analytics_tracking_script' ) );
	}

	public function load_analytics_tracking_script() {
		wp_register_script(
			'sa_tracking',
		  	plugin_dir_url(dirname(__FILE__)) . 'assets/js/skroutz-analytics-tracking.js',
		  	'',
		  	WC_Skroutz_Analytics::PLUGIN_VERSION
		);

		wp_localize_script(
			'sa_tracking',
			WC_Skroutz_Analytics::PLUGIN_ID,
			array( 'shop_account_id' => $this->shop_account_id )
		);

		wp_enqueue_script( 'sa_tracking' );
	}
}
