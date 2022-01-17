<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * The tracking functionality of the plugin.
 *
 * @package    WC_Skroutz_Analytics_Tracking
 * @subpackage WC_Skroutz_Analytics_Tracking/includes
 * @author     Skroutz SA <analytics@skroutz.gr>
 */
class WC_Skroutz_Analytics_Tracking {

	const PAID_BY_DESCR_MAX_LENGTH = 50;

	/**
	 * The days before we don't send addOrder and addItem
	 */
	const OBSOLETE_ORDER_DAYS = 30;

	/**
	* The global object name provided by the admin settings
	* @var string
	*/
	private $global_object_name;

	/**
	* The current order to be submitted
	* @var string
	*/
	private $order;

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
		$this->settings = WC_Skroutz_Analytics_Settings::get_instance();
		$this->global_object_name = $this->get_global_object_name();

		// Page tracking script
		add_action( 'wp_print_footer_scripts', array( $this, 'output_analytics_tracking_script' ) );

		// Ecommerce tracking
		add_action( 'woocommerce_thankyou', array( $this, 'load_ecommerce_analytics' ) );
	}

	public function output_analytics_tracking_script() {
		$analytics_url = constant( "WC_Skroutz_Analytics_Flavors::".$this->settings->get_flavor()."_analytics_url" );
		$analytics_object = constant( "WC_Skroutz_Analytics_Flavors::".$this->settings->get_flavor()."_analytics_object" );
		$plugin_version = WC_Skroutz_Analytics::PLUGIN_VERSION;

		$analytics_script = "
		<!-- Skroutz Analytics WooCommerce plugin - v{$plugin_version} -->
		<script data-cfasync='false' data-wc-skroutz-analytics-plugin-version='$plugin_version' type='text/javascript'>
			(function(a,b,c,d,e,f,g){a['$analytics_object']=e;a[e]= a[e] || function(){
			(a[e].q = a[e].q || []).push(arguments);};f=b.createElement(c);f.async=true;
			f.src=d;g=b.getElementsByTagName(c)[0];g.parentNode.insertBefore(f,g);
			})(window,document,'script','$analytics_url','$this->global_object_name');

			{$this->create_connect()}
		</script>
		";

		echo $analytics_script;
	}

	public function load_ecommerce_analytics( $order_id ) {
		$this->order = new WC_Order( $order_id );

		// Do not report an order to analytics when the order status is failed.
		if ( $this->order->has_status( 'failed' ) ) {
			return;
		}

		// Do not report an order to analytics when the order is prior to 30 days ago.
		if ( $this->order_is_obsolete() ) {
			return;
		}

		add_action( 'wp_print_footer_scripts', array( $this, 'output_ecommerce_analytics_script' ) );
	}

	public function output_ecommerce_analytics_script() {
		$analytics_script = "<script data-cfasync='false' type='text/javascript'> \n";

		$analytics_script .= $this->create_action( 'addOrder', $this->prepare_order_data() ) . "\n";

		foreach ( $this->order->get_items() as $item ) {
			$analytics_script .= $this->create_action( 'addItem', $this->prepare_item_data( $item ) ) . "\n";
		}

		$analytics_script .= "</script> \n";

		echo $analytics_script;
	}

	/**
	* Builds the connect command based on global object name and shop account id.
	*
	* @return string The connect command
	*
	* @since    1.3.0
	* @access   private
	*/
	private function create_connect() {
		return "{$this->global_object_name}('session', 'connect', '{$this->settings->get_shop_account_id()}');";
	}

	/**
	* Builds an Analytics Ecommerce addOrder action.
	*
	* @param array $order The completed order to report.
	* @return string The JavaScript representation of an Analytics Ecommerce addOrder action.
	*/
	private function prepare_order_data() {
		$data = array(
			'order_id' => $this->get_order_id(),
			'revenue'  => $this->get_order_revenue(),
			'shipping' => $this->get_order_shipping(),
			'tax'      => $this->get_order_tax(),
		);

		$payment_gateway = wc_get_payment_gateway_by_order( $this->order );
		if ( $payment_gateway ) {
			$data['paid_by'] = $payment_gateway->id;
			$data['paid_by_descr'] = mb_substr( $payment_gateway->get_title(), 0, self::PAID_BY_DESCR_MAX_LENGTH );
		}

		return json_encode($data);
	}

	/**
	* Builds an Analytics Ecommerce addItem action.
	*
	* @param array $order The completed order to report.
	* @param array $item The purchesed product to report, part of this order.
	* @return string The JavaScript representation of an Analytics Ecommerce addItem action.
	*/
	private function prepare_item_data( $item ) {
		// WC_Abstract_Legacy_Order::get_product_from_item is deprecated since version 4.4.0
		// TODO Use only WC_Order_Item_Product::get_product when we drop support for WooCommerce < 3.0
		if ( method_exists( $item, 'get_product' ) ) {
			$product = $item->get_product();
		} else {
			$product = $this->order->get_product_from_item( $item );
		}

		$sa_product = new WC_Skroutz_Analytics_Product( $product, $this->settings->get_product_id_settings() );

		$data = array(
			'order_id'    => $this->get_order_id(),
			'product_id'  => $sa_product->get_id(),
			'name'        => $product->get_title(),
			'price'       => $this->order->get_item_total( $item, true ),
			'quantity'    => (int)$item['qty'],
		);

		return json_encode($data);
	}

	private function create_action( $action, $data ) {
		return "{$this->global_object_name}('ecommerce', '$action', {$data});";
	}

	/**
	 * Returns the order id that should be reported to Analytics
	 *
	 * @return string  The order id that should be reported to Analytics
	 *
	 * @since    1.7.0
	 * @access   private
	 */
	private function get_order_id() {
		return apply_filters( 'wc_skroutz_analytics_tracking_order_id_filter', $this->order->get_order_number(), $this->order );
	}

	/**
	* Get the global object name, based on admin settings.
	* If custom name is not enabled or not provided the default global object name will be returned.
	*
	* @return array The global object name
	*
	* @since    1.3.0
	* @access   private
	*/
	private function get_global_object_name() {
		$default_object_name = constant( "WC_Skroutz_Analytics_Flavors::".$this->settings->get_flavor()."_global_object_name" );

		$settings = $this->settings->get_global_object_name_settings();

		return ($settings['enabled'] == 'yes' && $settings['name']) ? $settings['name'] : $default_object_name;
	}

	/**
	* Calculates the total fees of the order.
	*
	* @return array The total fees excluding tax (fees_excl_tax) and the the total tax of the fees (fees_tax)
	*
	* @since    1.0.0
	* @access   private
	*/
	private function calculate_order_fees() {
		$fees_excl_tax = 0;
		$fees_tax = 0;
  		foreach ($this->order->get_fees() as $fee) {
			$fees_excl_tax += $fee['line_total'];
			$fees_tax += $fee['line_tax'];
		}

		return array(
			'fees_excl_tax' => round($fees_excl_tax, 2),
			'fees_tax' => round($fees_tax, 2),
		);
	}

	/**
	* Calculates the revenue of the order excluding the fees
	*
	* @return float Order revenue
	*
	* @since    1.7.0
	* @access   private
	*/
	private function get_order_revenue() {
		$order_revenue = $this->order->get_total() - array_sum( $this->calculate_order_fees() );
		return apply_filters( 'wc_skroutz_analytics_tracking_order_revenue_filter', $order_revenue, $this->order );
	}

	/**
	 * Get the tax of the order
	 *
	 * @return float Order tax
	 *
	 * @since    1.7.0
	 * @access   private
	 */
	private function get_order_tax() {
		$order_tax = $this->calculate_order_tax();
		return apply_filters( 'wc_skroutz_analytics_tracking_order_tax_filter', $order_tax, $this->order );
	}

	/**
	* Calculates the tax of the order excluding the tax fees
	*
	* @return float Order tax
	*
	* @since    1.0.0
	* @access   private
	*/
	private function calculate_order_tax() {
		// Fallback tax calculation mechanism if WC does not return any taxes (No tax rules, or taxes are disabled)
		// Manually calculate the tax based on an the default country tax rate that we have configured
		if ( $this->order->get_total_tax() == 0 ) {
			return $this->calculate_tax_from_total(
				$this->get_order_revenue(),
				constant( "WC_Skroutz_Analytics_Flavors::".$this->settings->get_flavor()."_default_tax_rate" )
			);
		}

		$order_fees = $this->calculate_order_fees();

		return round ($this->order->get_total_tax() - $order_fees['fees_tax'], 2 );
	}

	/**
	 * Calculates the shipping of the order
	 *
	 * @return float Order shipping
	 *
	 * @since    1.7.0
	 * @access   private
	 */
	private function get_order_shipping() {
		$order_shipping = $this->order->get_total_shipping() + $this->order->get_shipping_tax();
		return apply_filters( 'wc_skroutz_analytics_tracking_order_shipping_filter', $order_shipping, $this->order );
	}

	/**
	* Calculates the tax given a total amount and a tax rate
	*
	* @param float The total amount (that should include the tax)
	* @param int The tax rate
	* @return float The tax
	*
	* @since    1.0.4
	* @access   private
	*/
	private function calculate_tax_from_total($total, $tax_rate) {
		return round( $total - $total / ( 1 + $tax_rate/100 ), 2 );
	}

	/**
	 * Checks if order is prior to 30 days ago
	 *
	 * @return boolean
	 *
	 * @since    1.7.0
	 * @access   private
	 */
	private function order_is_obsolete() {
		$timezone = WC_Skroutz_Analytics_Helpers::getTimezone();
		$thirty_days_ago = new DateTime('- '.self::OBSOLETE_ORDER_DAYS.' days', $timezone);

		// TODO Use only WC_Order::get_date_created when we drop support for WooCommerce < 3.0
		if ( method_exists( $this->order, 'get_date_created' ) ) {
			if ( is_null( $this->order->get_date_created() ) ) {
				return false;
			}

			$order_is_obsolete = ( $thirty_days_ago > $this->order->get_date_created() );
		}
		else {
			if ( empty( $this->order->order_date ) ) {
				return false;
			}

			$order_is_obsolete = ( $thirty_days_ago > new DateTime( $this->order->order_date, $timezone ) );
		}

		return $order_is_obsolete;
	}
}
