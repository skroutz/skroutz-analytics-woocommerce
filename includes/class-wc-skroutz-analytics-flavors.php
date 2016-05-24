<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Enum class for the analytics settings for each flavor/site
 *
 * @package    WC_Skroutz_Analytics_Flavors
 * @subpackage WC_Skroutz_Analytics_Flavors/includes
 * @author     Skroutz SA <analytics@skroutz.gr>
 */
class WC_Skroutz_Analytics_Flavors
{
	const skroutz_analytics_url = 'https://analytics.skroutz.gr/analytics.min.js';
	const skroutz_merchants_url = 'https://merchants.skroutz.gr/merchants/account/settings/analytics';
  const skroutz_default_tax_rate = 24;

	const alve_analytics_url = 'https://analytics.alve.com/analytics.min.js';
	const alve_merchants_url = 'https://merchants.alve.com/merchants/account/settings/analytics';
  const alve_default_tax_rate = 18;

	const scrooge_analytics_url = 'https://analytics.scrooge.co.uk/analytics.min.js';
	const scrooge_merchants_url = 'https://merchants.scrooge.co.uk/merchants/account/settings/analytics';
  const scrooge_default_tax_rate = 20;
}
