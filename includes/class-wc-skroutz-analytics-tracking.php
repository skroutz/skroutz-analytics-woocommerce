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

	/**
	* The flavor (is the site) provided by the admin settings
	* @var string
	*/
	private $flavor;

	/**
	* The shop account id provided by the admin settings
	* @var string
	*/
	private $shop_account_id;

	/**
	* The items product id options provided by the admin settings
	*
	* id: id|sku
	* parent_id_enabled: yes|no
	* @var array
	*/
	private $items_product_id_settings;

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
	public function __construct( $flavor, $shop_account_id, $items_product_id_settings, $global_object_name_settings ) {
		$this->flavor = $flavor;
		$this->shop_account_id = $shop_account_id;
		$this->items_product_id_settings = $items_product_id_settings;
		$this->global_object_name = $this->get_global_object_name($global_object_name_settings);

		// Page tracking script
		add_action( 'wp_print_footer_scripts', array( $this, 'output_analytics_tracking_script' ) );

		// Ecommerce tracking
		add_action( 'woocommerce_thankyou', array( $this, 'load_ecommerce_analytics' ) );
	}

	public function output_analytics_tracking_script() {
		$analytics_url = constant("WC_Skroutz_Analytics_Flavors::$this->flavor"."_analytics_url");
		$analytics_object = constant("WC_Skroutz_Analytics_Flavors::$this->flavor"."_analytics_object");

		$analytics_script = "
		<!-- Skroutz Analytics WooCommerce plugin - v".WC_Skroutz_Analytics::PLUGIN_VERSION." -->
		<script data-cfasync='false' type='text/javascript'>
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
		return "{$this->global_object_name}('session', 'connect', '$this->shop_account_id');";
	}

	/**
	* Builds an Analytics Ecommerce addOrder action.
	*
	* @param array $order The completed order to report.
	* @return string The JavaScript representation of an Analytics Ecommerce addOrder action.
	*/
	private function prepare_order_data() {
		$data = array(
			'order_id' => $this->order->get_order_number(),
			'revenue'  => $this->calculate_order_revenue(),
			'shipping' => $this->order->get_total_shipping() + $this->order->get_shipping_tax(),
			'tax'      => $this->calculate_order_tax(),
		);

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
		$product = $this->order->get_product_from_item( $item );

		$data = array(
			'order_id'    => $this->order->get_order_number(),
			'product_id'  => $this->sa_product_id( $product ),
			'name'        => $product->get_title(),
			'price'       => $this->order->get_item_total( $item, true ),
			'quantity'    => (int)$item['qty'],
		);

		return json_encode($data);
	}

	private function create_action( $action, $data ) {
		return "{$this->global_object_name}('ecommerce', '$action', JSON.stringify({$data}));";
	}

	/**
	* Returns the product id that should be reported to Analytics based on
	* product id admin settings.
	*
	* @param WC_Product $product The purchased WC product
	* @return string|integer  The product id that should be reported to Analytics
	*
	* @since    1.0.6
	* @access   private
	*/
	private function sa_product_id( $product ) {
		$parent_or_variation = $product;

		if($this->items_product_id_settings['parent_id_enabled'] == 'yes' && $product->is_type( 'variation' ) ) {
			$parent_or_variation = $this->get_parent_product($product);
		}

		$product_id = $this->get_custom_product_id($parent_or_variation);

		if($product_id) {
			return $product_id; // return the custom_id from postmeta table
		} elseif($this->items_product_id_settings['id'] == 'sku') {
			$product_id = $parent_or_variation->get_sku();
		} else {
			$product_id = $parent_or_variation->get_id();
		}

		return $product_id ? $product_id : "wc-sa-{$product->get_id()}";
	}

	/**
	* Get the custom postmeta id if exists, based on admin settings
	*
	* @return NULL|string|integer The custom postmeta id
	*
	* @since    1.2.0
	* @access   private
	*/
	private function get_custom_product_id( $product ) {
		$product_id = null;

		if ($this->items_product_id_settings['custom_id_enabled'] == 'yes' && $this->items_product_id_settings['custom_id']) {
			$product_id = get_post_meta(
				$product->get_id(),
				$this->items_product_id_settings['custom_id'],
				true
			);
		}

		return $product_id;
	}

	/**
	* Get the parent product of a variation product.
	*
	* @param WC_Product $product The purchased WC product
	* @return WC_Product|null|false The parent product
	*
	* @since    1.3.1
	* @access   private
	*/
	private function get_parent_product( $product ) {
		// TODO Use only get_parent_id when we drop support for WooCommerce < 3.0
		if (method_exists( $product, 'get_parent_id' )) {
			$parent_id = $product->get_parent_id();
		} else {
			$parent_id = wp_get_post_parent_id($product->get_id());
		}

		return wc_get_product($parent_id);
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
	private function get_global_object_name( $settings ) {
		$default_object_name = constant("WC_Skroutz_Analytics_Flavors::$this->flavor"."_global_object_name");

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
	* @since    1.0.0
	* @access   private
	*/
	private function calculate_order_revenue() {
		return $this->order->get_total() - array_sum($this->calculate_order_fees());
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
				$this->calculate_order_revenue(),
				constant("WC_Skroutz_Analytics_Flavors::$this->flavor"."_default_tax_rate")
			);
		}

		$order_fees = $this->calculate_order_fees();

		return round ($this->order->get_total_tax() - $order_fees['fees_tax'], 2 );
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

}
