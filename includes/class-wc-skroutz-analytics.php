<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       www.skroutz.gr
 * @since      1.0.0
 *
 * @package    WC_Skroutz_Analytics
 * @subpackage WC_Skroutz_Analytics/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    WC_Skroutz_Analytics
 * @subpackage WC_Skroutz_Analytics/includes
 * @author     Skroutz <info@skroutz.gr>
 */
class WC_Skroutz_Analytics {

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since 	1.0.0
	 * @var 	string 	PLUGIN_ID	The string used to uniquely identify this plugin.
	 */
	const PLUGIN_ID = 'wc_skroutz_analytics';

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @var      string    PLUGIN_VERSION    The current version of the plugin.
	 */
	const PLUGIN_VERSION = '1.0.0';

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {

		$this->load_dependencies();
		$this->set_locale();

		add_filter( 'woocommerce_integrations', array( $this, 'add_integration' ) );
	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - WC_Skroutz_Analytics_Loader. Orchestrates the hooks of the plugin.
	 * - WC_Skroutz_Analytics_i18n. Defines internationalization functionality.
	 * - WC_Skroutz_Analytics_Admin. Defines all hooks for the admin area.
	 * - WC_Skroutz_Analytics_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies() {

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-wc-skroutz-analytics-i18n.php';

		/**
		 * The class responsible flavor settings.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-wc-skroutz-analytics-flavors.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-wc-skroutz-analytics-integration.php';

		/**
		 * The class responsible for all the tracking actions.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-wc-skroutz-analytics-tracking.php';
	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the WC_Skroutz_Analytics_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale() {

		$plugin_i18n = new WC_Skroutz_Analytics_i18n();

		add_action( 'plugins_loaded', array( $plugin_i18n, 'load_plugin_textdomain' ) );
	}

	/**
	 * Add a new integration to WooCommerce.
	 */
	public function add_integration( $integrations ) {
		$integrations[] = 'WC_Skroutz_Analytics_Integration';

		return $integrations;
	}

}
