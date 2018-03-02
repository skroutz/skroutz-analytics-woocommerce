<?php

/**
 * Fired when the plugin is uninstalled.
 *
 * @link       www.skroutz.gr
 * @since      1.0.0
 *
 * @package    WC_Skroutz_Analytics
 */

// If uninstall not called from WordPress, then exit.
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

$skroutz_analytics_option = 'woocommerce_wc_skroutz_analytics_settings';
delete_option( $skroutz_analytics_option );
