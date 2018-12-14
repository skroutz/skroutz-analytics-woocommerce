<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * and defines a function that starts the plugin.
 *
 * @link              www.skroutz.gr
 * @since             1.0.0
 * @package           WC_Skroutz_Analytics
 *
 * @wordpress-plugin
 * Plugin Name:       WooCommerce Skroutz Analytics
 * Plugin URI:        https://github.com/skroutz/wc-skroutz-analytics
 * Description:       Integrate skroutz analytics to your WooCommerce enabled Wordpress site
 * Version:           1.3.1
 * Author:            Skroutz
 * Author URI:        www.skroutz.gr
 * License:           GPL-2.0
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       wc-skroutz-analytics
 * Domain Path:       /languages
 * WC requires at least: 2.5.0
 * WC tested up to: 3.5.2
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-wc-skroutz-analytics.php';

/**
 * Begins execution of the plugin.
 *
 * When the plugins have been loaded we check if we can run our plugin.
 *
 * @since    1.0.0
 */
function run_wc_skroutz_analytics() {
	if ( ! meets_prerequisites() ) {
		return;
	}

	define('SA_PLUGIN_BASENAME', plugin_basename( __FILE__ ));

	$plugin = new WC_Skroutz_Analytics();
}
add_action( 'plugins_loaded', 'run_wc_skroutz_analytics' );

/**
 * Checks if it is ok to run the Skroutz Analytics plugin
 *
 * @return boolean true if it meets the prerequisites, otherwise false
 */
function meets_prerequisites() {
	// Check if woocommerce plugin is active, by checking if the woocommerce class that we will extend exists
	if( ! class_exists( 'WC_Integration' )) {
		add_action( 'admin_notices', 'woocommerce_missing_notice' );
		return false;
	}

	return true;
}

/**
 * WooCommerce not installed notice
 *
 * @return string
 */
function woocommerce_missing_notice() {
	$class = 'notice error';
	$message = 'WooCommerce Skroutz Analytics plugin requires the WooCommerce plugin';

	printf( '<div class="%s"><p>%s</p></div>', $class, $message );
}
