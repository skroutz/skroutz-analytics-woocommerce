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
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       wc-skroutz-analytics
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-wc-skroutz-analytics-activator.php
 */
function activate_wc_skroutz_analytics() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-wc-skroutz-analytics-activator.php';
	WC_Skroutz_Analytics_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-wc-skroutz-analytics-deactivator.php
 */
function deactivate_wc_skroutz_analytics() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-wc-skroutz-analytics-deactivator.php';
	WC_Skroutz_Analytics_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_wc_skroutz_analytics' );
register_deactivation_hook( __FILE__, 'deactivate_wc_skroutz_analytics' );

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

	$plugin = new WC_Skroutz_Analytics();
	$plugin->run();

}
run_wc_skroutz_analytics();
