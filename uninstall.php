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

delete_option( 'woocommerce_wc_skroutz_analytics_settings' );
delete_option( 'widget_wc_skroutz_analytics_product_reviews_inline_widget' );
delete_option( 'widget_wc_skroutz_analytics_product_reviews_extended_widget' );
