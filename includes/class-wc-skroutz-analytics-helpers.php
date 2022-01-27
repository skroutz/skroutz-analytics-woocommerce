<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Implements helper functions for woocommerce plugin.
 *
 * @since      1.7.0
 * @package    WC_Skroutz_Analytics
 * @subpackage WC_Skroutz_Analytics/includes
 * @author     Skroutz SA <analytics@skroutz.gr>
 */
class WC_Skroutz_Analytics_Helpers {

	/**
	 * Get Wordpress Timezone based on settings
	 *
	 * @return DateTimeZone
	 *
	 * @since    1.7.0
	 * @access   private
	 * @see      https://developer.wordpress.org/reference/functions/wp_timezone_string/
	 */
	public static function getTimezone() {
		if ( function_exists( 'wp_timezone' ) ) {
			return wp_timezone();
		}

		$timezone_string = get_option( 'timezone_string' );

		if ( $timezone_string ) {
			return new DateTimeZone( $timezone_string );
		}

		$offset  = (float) get_option( 'gmt_offset' );
		$hours   = (int) $offset;
		$minutes = ( $offset - $hours );

		$sign      = ( $offset < 0 ) ? '-' : '+';
		$abs_hour  = abs( $hours );
		$abs_mins  = abs( $minutes * 60 );
		$tz_offset = sprintf( '%s%02d:%02d', $sign, $abs_hour, $abs_mins );

		return new DateTimeZone( $tz_offset );
	}
}