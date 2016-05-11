<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              www.skroutz.gr
 * @since             1.0.0
 * @package           WC_Skroutz_Analytics
 *
 * @wordpress-plugin
 * Plugin Name:       WooCommerce Skroutz Analytics
 * Plugin URI:        https://github.com/skroutz/wc-skroutz-analytics
 * Description:       Integrate skroutz analytics to your WooCommerce enabled Wordpress site
 * Version:           1.0.0
 * Author:            Skroutz
 * Author URI:        www.skroutz.gr
 * License:           GPL-2.0
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       wc-skroutz-analytics
 * Domain Path:       /languages
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
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_wc_skroutz_analytics() {
	if ( ! meets_prerequisites() ) {
		return;
	}

	$plugin = new WC_Skroutz_Analytics();
}
add_action( 'plugins_loaded', 'run_wc_skroutz_analytics' );
// TODO: improve this! Maybe check if woocommerce plugin is enabled.
// then wait for the specific plugin to load (if it is possible) and then run our plugin

/**
 * Checks if it is ok to run the Skroutz Analytics plugin
 *
 * @return boolean true if it meets the prerequisites, otherwise false
 */
function meets_prerequisites() {
	// Check if woocommerce plugin is active
	if ( ! in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
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
