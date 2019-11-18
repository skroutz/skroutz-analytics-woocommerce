<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * The admin-specific functionality of the plugin.
 *
 * Integration with the WooCommerce plugin.
 * Define the plugin settings, validation and sanitization.
 * Instantiate the tracking functionality if able
 * Register the widgets if able
 *
 * @package    WC_Skroutz_Analytics
 * @subpackage WC_Skroutz_Analytics/admin
 * @author     Skroutz SA <analytics@skroutz.gr>
 */
class WC_Skroutz_Analytics_Integration extends WC_Integration {

	/**
	* The tracking is responsible for all the tracking actions in the plugin.
	*
	* @since    1.0.0
	* @access   private
	* @var      WC_Skroutz_Analytics_Tracking $tracking Defines all the tracking actions.
	*/
	private $tracking;

	/**
	 * The plugin's settigns.
	 *
	 * @since    1.4.0
	 * @access   private
	 * @var      WC_Skroutz_Analytics_Settings $sa_settings The plugin's settigns.
	 */
	private $sa_settings;

	/**
	* Initialize the class and set its properties.
	*
	* @since    1.0.0
	*/
	public function __construct() {
		$this->id = WC_Skroutz_Analytics::PLUGIN_ID;
		$this->method_title = __( 'Skroutz Analytics', 'wc-skroutz-analytics' );
		$this->method_description    = __( 'Skroutz Analytics is a free service that gives you the ability to generate statistics about your shop visitors and products.', 'wc-skroutz-analytics' );

		//Add settings action link to plugins listing
		add_filter( 'plugin_action_links_' . SA_PLUGIN_BASENAME, array( $this, 'add_action_links' ));

		$this->sa_settings = WC_Skroutz_Analytics_Settings::get_instance();
		$this->init_form_fields();
		$this->init_settings();

		$this->register_admin_hooks();

		// Check if account id is set, else don't do any tracking
		if ( ! $this->sa_settings->get_shop_account_id() ) {
			return;
		}

		$this->tracking = new WC_Skroutz_Analytics_Tracking();

		$this->register_widgets();
	}

	/**
	* Add settings action link to plugins listing
	*
	* @param array links The existing action links
	* @return array The final action links to be displayed
	*
	* @since    1.0.0
	*/
	public function add_action_links ( $links ) {
		$action_links = array(
			'settings' => sprintf(
				'<a href="%s" title="%s"> %s </a>',
				admin_url( 'admin.php?page=wc-settings&tab=integration&section=wc_skroutz_analytics'),
				__( 'View Skroutz Analytics Settings', 'wc-skroutz-analytics'),
				__( 'Settings', 'wc-skroutz-analytics' )
			),
		);

		return array_merge( $links, $action_links );
	}

	/**
	* Define the plugin form fields and settings
	*
	* @since    1.0.0
	*/
	public function init_form_fields() {
		$merchants_link = "<a id='merchants_link' href='' target='_blank'></a>"; //will be populated by the client
		$default_object_name = constant( "WC_Skroutz_Analytics_Flavors::".$this->sa_settings->get_flavor()."_global_object_name" );

		$this->form_fields = array(
			'sa_flavor' => array(
				'title'       => __( 'Site', 'wc-skroutz-analytics' ),
				'type'        => 'select',
				'description' => __( 'Specify the site your eshop reports to.', 'wc-skroutz-analytics' ),
				'options'     => array( 'skroutz' => 'Skroutz', 'alve' => 'Alve', 'scrooge' => 'Scrooge' ),
				'default'     => WC_Skroutz_Analytics_Settings::DEFAULT_FLAVOR,
			),
			'sa_shop_account_id' => array(
				'title'       => __( 'Shop Account ID', 'wc-skroutz-analytics' ),
				'type'        => 'text',
				'description' => sprintf(__('The shop account ID is provided by %s', 'wc-skroutz-analytics'), $merchants_link ),
				'placeholder' => 'SA-XXXX-YYYY',
				'default'     => WC_Skroutz_Analytics_Settings::DEFAULT_SHOP_ACCOUNT_ID,
			),
			'sa_items_product_id' => array(
				'title'       => __( 'Unique ID', 'wc-skroutz-analytics' ),
				'type'        => 'select',
				'description' => __( 'Specify the Unique ID that should be sent to analytics.', 'wc-skroutz-analytics' ),
				'options'     => array( 'sku' => 'Product SKU', 'id' => 'Product ID' ),
				'default'     => WC_Skroutz_Analytics_Settings::DEFAULT_ITEMS_PRODUCT_ID,
				'desc_tip'    => __( 'It must have the same Unique ID used in the XML feed provided to Skroutz.', 'wc-skroutz-analytics' ),
			),
			'sa_items_custom_id_enabled' => array(
				'type'    => 'checkbox',
				'label'   => __( 'Use custom postmeta ID', 'wc-skroutz-analytics' ),
				'default' => WC_Skroutz_Analytics_Settings::DEFAULT_ITEMS_CUSTOM_ID_ENABLED,
			),
			'sa_items_custom_id' => array(
				'type'        => 'text',
				'description' => __( 'Specify a custom ID key that will be used to retrieve the Unique ID from postmeta table.', 'wc-skroutz-analytics' ),
				'placeholder' => 'custom_id',
				'desc_tip'    => __( 'If custom ID key is not found the Product ID/SKU will be used.', 'wc-skroutz-analytics' ),
				'default'     => WC_Skroutz_Analytics_Settings::DEFAULT_ITEMS_CUSTOM_ID,
			),
			'sa_items_product_parent_id_enabled' => array(
				'type'   => 'multiradio',
				'radios' => array(
					'sa_items_product_variation_id' => array(
						'title' => __( 'Variation Unique IDs', 'wc-skroutz-analytics' ),
						'label' => __( 'Send variation Unique ID', 'wc-skroutz-analytics' ),
						'description' => sprintf(__( 'e.g. Given parent product with %1$sID: 1%2$s %1$s&amp;%2$s variation (%1$sred color%2$s) with %1$sID: 2%2$s ➔ will send %1$sID: 2%2$s', 'wc-skroutz-analytics' ), '<strong>', '</strong>'),
						'value' => 'no',
					),
					'sa_items_product_parent_id' => array(
						'label'       => __( 'Send parent Unique ID', 'wc-skroutz-analytics' ),
						'description' => sprintf(__( 'e.g. Given parent product with %1$sID: 1%2$s %1$s&amp;%2$s variation (%1$sred color%2$s) with %1$sID: 2%2$s ➔ will send %1$sID: 1%2$s', 'wc-skroutz-analytics' ), '<strong>', '</strong>'),
						'value'       => 'yes',
					),
					'sa_items_product_parent_id_term_id' => array(
						'label' => __( 'Send parent Unique ID combined with specified variation attribute term IDs', 'wc-skroutz-analytics' ),
						'description' => sprintf(__( 'e.g. Given parent product with %1$sID: 1%2$s %1$s&amp;%2$s variation (%1$sred color%2$s) with %1$sID: 2%2$s %1$s&amp;%2$s red term with %1$sID: 73%2$s ➔ will send %1$sID: 1-73%2$s', 'wc-skroutz-analytics' ), '<strong>', '</strong>'),
						'value' => 'parent_id_term_id',
					),
				),
				'default' => WC_Skroutz_Analytics_Settings::DEFAULT_ITEMS_PRODUCT_PARENT_ID_ENABLED,
			),
			'sa_items_grouping_attributes' => array(
				'type'        => 'ordered_multiselect',
				'description' => __( 'Specify the attributes that will be used to group product variations (e.g. color).', 'wc-skroutz-analytics' ),
				'options'     => $this->attribute_taxonomies(),
				'default'     => WC_Skroutz_Analytics_Settings::DEFAULT_ITEM_GROUPING_ATTRIBUTES,
			),
			'sa_custom_global_object_name_enabled' => array(
				'title'    => __( 'Global Object Name', 'wc-skroutz-analytics' ),
				'type'     => 'checkbox',
				'label'    => __( 'Use custom global object name', 'wc-skroutz-analytics' ),
				'desc_tip' => __( 'Change global object name when there is a conflict with the default value.', 'wc-skroutz-analytics' ),
				'default'  => WC_Skroutz_Analytics_Settings::DEFAULT_CUSTOM_GLOBAL_OBJECT_NAME_ENABLED,
			),
			'sa_custom_global_object_name' => array(
				'type'        => 'text',
				'description' => __( 'Specify a custom global object name that will be used in the analytics tracking code.', 'wc-skroutz-analytics' ),
				'placeholder' => $default_object_name,
				'default'     => WC_Skroutz_Analytics_Settings::DEFAULT_CUSTOM_GLOBAL_OBJECT_NAME,
			),
		);
	}

	/**
	* Validate the shop account id
	*
	* @param string key The key to be validated
	* @return string The value that was submitted
	*
	* @since    1.0.0
	*/
	public function validate_sa_shop_account_id_field( $key ) {
		// TODO: Add second argument ($value) and remove current assignment, when we drop support for WooCommerce < 2.6
		// https://docs.woocommerce.com/wc-apidocs/source-class-WC_Settings_API.html#137
		$value = $_POST[ $this->plugin_id . $this->id . '_' . $key ];

		if ( isset( $value ) && !$this->is_valid_skroutz_analytics_code( $value ) ) {
			WC_Admin_Settings::add_error( __('Shop Account ID is invalid', 'wc-skroutz-analytics') );
		}

		return $value;
	}

	/**
	* Sanitize admin settings
	*
	* @since    1.0.0
	*/
	public function sanitize_settings( $settings ) {
		if ( isset( $settings['sa_shop_account_id'] ) ) {
			$settings['sa_shop_account_id'] = strtoupper( $settings['sa_shop_account_id'] );
		}

		return $settings;
	}

	/**
	* Load the admin assets only when on the settings view of the plugin
	*
	* @since    1.0.0
	*/
	public function load_admin_assets( $hook ) {
		//load the assets only if we are in the settings tab of our plugin
		if ( $hook !== 'woocommerce_page_wc-settings' ) {
			return;
		}

		if ( isset($_GET['tab']) && $_GET['tab'] !== 'integration' ) {
  			return;
		}

		if ( isset($_GET['section']) && $_GET['section'] !== 'wc_skroutz_analytics' ) {
			return;
		}

		wp_register_script(
			'sa_admin_settings',
			plugin_dir_url(dirname(__FILE__)) . 'assets/js/skroutz-analytics-admin-settings.js',
			array( 'jquery', 'select2' ),
			WC_Skroutz_Analytics::PLUGIN_VERSION
		);

		$flavors_class = new ReflectionClass('WC_Skroutz_Analytics_Flavors');
		$flavors = $flavors_class->getConstants();

		wp_localize_script(
			'sa_admin_settings',
			WC_Skroutz_Analytics::PLUGIN_ID,
			array( 'flavors' => $flavors )
		);

		wp_enqueue_script( 'sa_admin_settings' );
	}

	/**
	 * Register the inline widget
	 *
	 * @since    1.4.0
	 */
	public function register_skroutz_product_reviews_inline_widget() {
		register_widget( 'WC_Skroutz_Analytics_Product_Reviews_Inline_Widget' );
	}

	/**
	 * Register the extended widget
	 *
	 * @since    1.4.0
	 */
	public function register_skroutz_product_reviews_extended_widget() {
		register_widget( 'WC_Skroutz_Analytics_Product_Reviews_Extended_Widget' );
	}

	/**
	* Analytics code validation rule
	*
	* @since    1.0.0
	* @access   private
	*/
	private function is_valid_skroutz_analytics_code( $code ) {
		return preg_match('/^sa-\d{4}-\d{4}$/i', $code ) ? true : false;
	}

	/**
	* Register the admin hooks for the asset loading, validation and sanitization
	*
	* @since    1.0.0
	* @access   private
	*/
	private function register_admin_hooks() {
		add_action( 'admin_enqueue_scripts', array( $this, 'load_admin_assets') );

		add_action( 'woocommerce_update_options_integration_' . $this->id, array( $this, 'process_admin_options' ) );
		add_filter( 'woocommerce_settings_api_sanitized_fields_' . $this->id, array( $this, 'sanitize_settings' ) );
	}

	/**
	 * Register the inline and extended widgets on widgets init
	 *
	 * @since 1.4.0
	 * @access   private
	 */
	private function register_widgets() {
		add_action( 'widgets_init', array( $this, 'register_skroutz_product_reviews_inline_widget') );
		add_action( 'widgets_init', array( $this, 'register_skroutz_product_reviews_extended_widget') );
	}

	/**
	 * Fetches IDs and names of WooCommerce attribute taxonomies.
	 * @return array [attribute_id => attribute_name] mapping
	 *
	 * @since  1.5.0
	 * @access private
	 */
	private function attribute_taxonomies() {
		return wp_list_pluck( wc_get_attribute_taxonomies(), 'attribute_name', 'attribute_id' );
	}

	/**
	 * Generates radio button HTML.
	 * The `name` attribute of the control should be passed in $data as `name`.
	 * The currently selected value of the corresponding radio group should be passed in $data as `current_value`.
	 * @param string $key Field key
	 * @param array  $data Field data containing the `name` and `current_value` keys
	 * @return string
	 *
	 * @since  1.5.0
	 * @access private
	 */
	private function generate_radio_html( $key, $data ) {
		$field_key = $this->get_field_key( $key );
		$defaults  = array(
			'title'             => '',
			'label'             => '',
			'name'              => '',
			'value'             => '',
			'current_value'     => '',
			'disabled'          => false,
			'class'             => '',
			'css'               => '',
			'desc_tip'          => false,
			'description'       => '',
			'custom_attributes' => array()
		);

		$data = wp_parse_args( $data, $defaults );

		if ( ! $data['label'] ) {
			$data['label'] = $data['title'];
		}

		ob_start();
		?>
		<tr valign="top">
			<th scope="row" class="titledesc">
				<label for="<?php echo esc_attr( $field_key ); ?>">
					<?php echo wp_kses_post( $data['title'] ); ?>
					<?php echo $this->get_tooltip_html( $data ); // WPCS: XSS ok. ?>
				</label>
			</th>
			<td class="forminp">
				<fieldset>
					<legend class="screen-reader-text"><span><?php echo wp_kses_post( $data['title'] ); ?></span></legend>
					<label for="<?php echo esc_attr( $field_key ); ?>">
						<input
							id="<?php echo esc_attr( $field_key ); ?>"
							name="<?php echo esc_attr( $data['name'] ); ?>"
							value="<?php echo esc_attr( $data['value'] ); ?>"
							type="radio"
							class="<?php echo esc_attr( $data['class'] ); ?>"
							style="<?php echo esc_attr( $data['css'] ); ?>"
							<?php checked( $data['current_value'], $data['value'] ); ?>
							<?php disabled( $data['disabled'], true ); ?>
							<?php echo $this->get_custom_attribute_html( $data ); // WPCS: XSS ok. ?>
						/>
						<?php echo wp_kses_post( $data['label'] ); ?>
					</label>
					<br/>
					<?php echo $this->get_description_html( $data ); // WPCS: XSS ok. ?>
				</fieldset>
			</td>
		</tr>
		<?php

		return ob_get_clean();
	}

	/**
	 * Generates HTML for multiple radio buttons allowing the user to select an one-of-many option.
	 * Each radio accepts options such as `title`, `label` and `description` independently,
	 * however they will all share the same `name` and `current_value`, thus forming a radio group.
	 * Options for each radio should be passed in $data as `radios` in the form of a [radio key => radio data] array.
	 * @param string $key Field key
	 * @param array $data Field data containing the `radios` key
	 * @return string
	 *
	 * @since  1.5.0
	 * @see WC_Skroutz_Analytics_Integration::generate_radio_html()
	 */
	protected function generate_multiradio_html( $key, $data ) {
		$field_key = $this->get_field_key( $key );
		$defaults = array(
			'radios'  => array(),
		);

		$data = wp_parse_args( $data, $defaults );

		$current_value = $this->get_option( $key, $data['default'] );

		foreach ( $data['radios'] as &$radio ) {
			$radio['current_value'] = $current_value;
			$radio['name'] = $field_key;
		}

		return implode( "\n",
			array_map(
				array( $this, 'generate_radio_html' ),
				array_keys( $data['radios'] ), array_values( $data['radios'] )
			)
		);
	}

	/**
	 * Generates Ordered Multiselect HTML.
	 * A multiselect that renders the options by preserving selection order.
	 * @param string $key Field key
	 * @param array  $data Field data
	 * @return string
	 *
	 * @since  1.5.0
	 * @see WC_Settings_API::generate_multiselect_html()
	 */
	protected function generate_ordered_multiselect_html( $key, $data ) {
		$current_selection = $this->get_option( $key, $data['default'] );
		$selected_order = array_flip( $current_selection ); // position becomes the value
		$options_count = count( $data['options'] );

		$sort_by_select_order = function ( $a, $b ) use ( $selected_order, $options_count ) {
			// we place non selected options at the bottom
			$order_a = isset( $selected_order[ $a ] ) ? $selected_order[ $a ] : $options_count;
			$order_b = isset( $selected_order[ $b ] ) ? $selected_order[ $b ] : $options_count;

			return $order_a - $order_b;
		};

		uksort( $data['options'], $sort_by_select_order );

		return $this->generate_multiselect_html( $key, $data );
	}

	/**
	 * Validates Ordered Multiselect Field.
	 * @param  string $key Field key
	 * @return string|array
	 *
	 * @since  1.5.0
	 * @see WC_Settings_API::validate_multiselect_field()
	 */
	protected function validate_ordered_multiselect_field() {
		// TODO: Add arguments ($key, $value) and replace the function body with
		// $this->validate_multiselect_field( $key, $value ); when we drop support for WooCommerce < 2.6
		return call_user_func_array( array( $this, 'validate_multiselect_field' ), func_get_args() );
	}
}
