<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * The flavor settings for the plugin.
 *
 * @link       www.skroutz.gr
 * @since      1.0.0
 *
 * @package    WC_Skroutz_Analytics_Flavors
 * @subpackage WC_Skroutz_Analytics_Flavors/includes
 */

/**
 * Enum class for the analytics settings for each flavor/site
 *
 * @package    WC_Skroutz_Analytics_Flavors
 * @subpackage WC_Skroutz_Analytics_Flavors/includes
 * @author     Skroutz <info@skroutz.gr>
 */
class WC_Skroutz_Analytics_Flavors
{
    const Skroutz = array(
    	'analytics_url' => 'https://analytics.skroutz.gr/analytics.min.js',
    	'merchants_url' => 'https://merchants.skroutz.gr/merchants/account/settings/analytics',
    );

    const Alve = array(
    	'analytics_url' => 'https://analytics.alve.com/analytics.min.js',
    	'merchants_url' => 'https://merchants.alve.com/merchants/account/settings/analytics',
    );

    const Scrooge = array(
    	'analytics_url' => 'https://analytics.scrooge.co.uk/analytics.min.js',
    	'merchants_url' => 'https://merchants.scrooge.co.uk/merchants/account/settings/analytics',
    );
}
