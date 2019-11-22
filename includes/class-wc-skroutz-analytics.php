<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

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
 * @author     Skroutz SA <analytics@skroutz.gr>
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
	const PLUGIN_VERSION = '1.5.0';

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Load the dependencies, define the locale, and set the admin hook for the plugin
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
	 * - WC_Skroutz_Analytics_Flavors. Defines the different supported flavors/sites.
	 * - WC_Skroutz_Analytics_i18n. Defines internationalization functionality.
	 * - WC_Skroutz_Analytics_Integration. Defines the woocommerce integration functionality.
	 * - WC_Skroutz_Analytics_Tracking. Defines the skroutz analytics tracking functionality.
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
		 * The class responsible for fetching the plugin settings.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-wc-skroutz-analytics-settings.php';

		/**
		 * The class responsible for providing the proper product id based on admin settings.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-wc-skroutz-analytics-product.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-wc-skroutz-analytics-integration.php';

		/**
		 * The product reviews inline widget
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/widgets/wc-skroutz-analytics-product-reviews-inline-widget.php';

		/**
		 * The product reviews extended widget
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/widgets/wc-skroutz-analytics-product-reviews-extended-widget.php';

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

		$plugin_i18n->load_plugin_textdomain();
	}

	/**
	 * Add a new integration to WooCommerce.
	 *
	 * @since    1.0.0
	 */
	public function add_integration( $integrations ) {
		$integrations[] = 'WC_Skroutz_Analytics_Integration';

		return $integrations;
	}

}
