=== Skroutz Analytics for WooCommerce ===
Contributors: skroutz
Tags: skroutz, analytics, woocommerce
Requires at least: 4.0
Tested up to: 5.8
Stable tag: 1.6.9
Requires PHP: 5.4
License: GPL-2.0
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Integrate Skroutz Analytics to your WooCommerce enabled Wordpress site.

== Description ==

This plugin provides the integration between [Skroutz Analytics](http://developer.skroutz.gr/analytics/) and the [WooCommerce plugin](https://wordpress.org/plugins/woocommerce/).

* Integrates the analytics tracking script to all your frontend pages
* Integrates the ecommerce data (transactions and revenue) generated during an order.

=== Documentation ===

Visit documentation for [Skroutz Analytics Woocommerce Plugin](https://github.com/skroutz/skroutz-analytics-woocommerce).

=== Filters ===

The plugin provides [filters](https://developer.wordpress.org/plugins/hooks/filters/) that allows you to customize the fields that will be reported to analytics:
* [wc_skroutz_analytics_tracking_order_id_filter](https://github.com/skroutz/skroutz-analytics-woocommerce#wc_skroutz_analytics_tracking_order_id_filter)
* [wc_skroutz_analytics_tracking_order_revenue_filter](https://github.com/skroutz/skroutz-analytics-woocommerce#wc_skroutz_analytics_tracking_order_revenue_filter)
* [wc_skroutz_analytics_tracking_order_shipping_filter](https://github.com/skroutz/skroutz-analytics-woocommerce#wc_skroutz_analytics_tracking_order_shipping_filter)
* [wc_skroutz_analytics_tracking_order_tax_filter](https://github.com/skroutz/skroutz-analytics-woocommerce#wc_skroutz_analytics_tracking_order_tax_filter)
* [wc_skroutz_analytics_tracking_order_paid_by_filter](https://github.com/skroutz/skroutz-analytics-woocommerce#wc_skroutz_analytics_tracking_order_paid_by_filter)
* [wc_skroutz_analytics_tracking_order_paid_by_descr_filter](https://github.com/skroutz/skroutz-analytics-woocommerce#wc_skroutz_analytics_tracking_order_paid_by_descr_filter)
* [wc_skroutz_analytics_product_id_filter](https://github.com/skroutz/skroutz-analytics-woocommerce#wc_skroutz_analytics_product_id_filter)
* [wc_skroutz_analytics_tracking_item_name_filter](https://github.com/skroutz/skroutz-analytics-woocommerce#wc_skroutz_analytics_tracking_item_name_filter)
* [wc_skroutz_analytics_tracking_item_price_filter](https://github.com/skroutz/skroutz-analytics-woocommerce#wc_skroutz_analytics_tracking_item_price_filter)
* [wc_skroutz_analytics_tracking_item_quantity_filter](https://github.com/skroutz/skroutz-analytics-woocommerce#wc_skroutz_analytics_tracking_item_quantity_filter)
* [wc_skroutz_analytics_product_reviews_widget_id_filter](https://github.com/skroutz/skroutz-analytics-woocommerce#wc_skroutz_analytics_product_reviews_widget_id_filter)

== Installation ==

= Automatic installation =
1. Go to your wordpress `admin panel > Plugins > Add New`.
2. Search this plugin by its name.
3. Install the plugin.
4. Activate the plugin.
5. Set the `Shop Account ID` to the plugin's settings.

= Manual installation =
1. Download the plugin file to your computer and unzip it.
2. Using an FTP program, or your hosting control panel, upload the unzipped plugin folder to your WordPress installation’s `wp-content/plugins/` directory.
3. Activate the plugin through the Plugins menu in WordPress admin.
4. Set the `Shop Account ID` to the plugin's settings.

== Frequently Asked Questions ==

= Which sites do you support? =
We support Skroutz.gr

= Where can I find the plugin's settings? =
This plugin will add the settings to the WooCommerce Integration tab.
`WooCommerce > Settings > Integration > Skroutz Analytics`

= Where can I find the `Shop Account ID`? =
Visit skroutz for [merchants page](https://merchants.skroutz.gr/merchants/account/settings/analytics), and navigate to the Skroutz Analytics section. Otherwise you may contact your account manager.

= I don't see the code on my site. Where is it? =
Make sure you have set your Skroutz Analytics `Shop Account ID` in the plugin settings, otherwise tracking won't work.

= How can I test if Skroutz Analytics is working? =
Skroutz provides you a temporary `verification page` during the testing phase of the skroutz analytics integration. You can visit skroutz for [merchants page](https://merchants.skroutz.gr/merchants/account/settings/analytics) or contact your account manager.

= Which pages do you track? =
This plugin does not track any admin pages, only frontend pages.

= My code is there, but does not report any ecommece data. Why? =
Duplicate Skroutz Analytics code causes a conflict in tracking. Remove any other Skroutz Analytics plugins or code from your site to avoid duplication and conflicts in tracking.

= The order tax seems to be wrong. Why? =
The plugin uses the WooCommerce tax rates you have configured in the settings. If the `Enable Taxes` option is disabled, or there are no `Tax Rates` configured, a default tax rate based on the flavor/country will be used to manually the calculate the order tax from the order revenue. So to avoid that, you need to properly setup your tax rules:

* Make sure you have enabled the `Enabled Taxes` option under `WooCommerce > Settings > Tax > Tax Options`
* And you have added **at least one** `Standard Tax Rate` under `WooCommerce > Settings > Tax > Standard Rates`
* Finally the shipping tax should be included in the order tax.
    - Make sure the `Shipping checkbox` is checked in the Tax Rates table (see above)
    - Also the `Tax Status` under `WooCommerce > Settings > Shipping > Flat Rate` must be set to `Taxable`. Note that the `Cost` value should be set excluding tax, as the tax will be automatically applied by WooCommerce. For example if you want the shipping cost to be 5 euro, you should set the cost to 4.03, given a 24% rate tax. The same applies for all the shipping methods that are enabled for your eshop.

= I don't use neither product ID nor SKU in XML, but a custom postmeta id =
The option to use a custom postmeta id is supported. You have to check the `Use custom postmeta id` option in the plugin settings, and then specify the custom id key used in the postmeta table. Note that if the custom id is not found for a product the product id/sku will be used instead.

= I have variable products with multiple attributes that must be grouped by an attribute (e.g. color) in XML. What can I do? =
Select the option `Send parent Unique ID combined with specified variation attribute term IDs` in the plugin settings and specify in the select box the grouping attribute (e.g. color). You can set more that one grouping attributes. But note that the order in which the attributes are set in the settings also affects the generated Unique ID.

The Unique ID that will be generated is a combination of the parent product ID and the specified grouping attribute terms `<parent product ID>-<attribute1 termA ID>-<attributeN termY ID>`.

The following rules apply:

* If a product is not a variant the `<product ID>` is used.
* If a product is a variant and does not have one of the declared color attributes the `<parent product ID>` is used.
* If a product is a variant and has one of the specified grouping attributes the `<parent product ID>-<attribute term ID>` is used.
* If a product is a variant and has two of the specified grouping attributes the `<parent product ID>-<attribute1 termA ID>-<attribute2 termB ID>` is used.

= Global object name `skroutz_analytics` is already being used, can I change it? =
The option to use a custom global object name is supported. You have to check the `Use custom global object name` option in the plugin settings, and then specify a name in the text field.

= How can I add the Product Reviews Widgets? =
The plugin provides two Wordpress Widgets, that you can easily add:

* [Product Reviews Inline Widget](https://developer.skroutz.gr/partner_sku_reviews/#inline-widget)
* [Product Reviews Extended Widget](https://developer.skroutz.gr/partner_sku_reviews/#extended-widget)

Detailed documentation on how to integrate the widgets can be found [here](https://developer.skroutz.gr/partner_sku_reviews/wordpress_widgets/).

== Screenshots ==

1. Skroutz Analytics settings panel.
2. Skroutz Analytics statistics in the Skroutz for merchants.

== Changelog ==

= 1.6.9 =
* Replace Skroutz Analytics URL

= 1.6.8 =
* Bump Wordpress tested up to version 5.8
* Bump WooCommerce tested up to version 5.6

= 1.6.7 =
* Rename README.txt to readme.txt
* Update Skroutz logos in assets

= 1.6.6 =
* Modify plugin name to Skroutz Analytics for WooCommerce

= 1.6.5 =
* Bump Wordpress tested up to version 5.6
* Bump WooCommerce tested up to version 4.8
* Fix deprecation warning for WC_Abstract_Legacy_Order::get_product_from_item

= 1.6.4 =
* Fix Shop Account ID validation on admin settings when second part is more than 4 digits

= 1.6.3 =
* Bump Wordpress tested up to version 5.5
* Bump WooCommerce tested up to version 4.3

= 1.6.2 =
* Fix substr encoding issue on payment method description that was preventing order reporting

= 1.6.1 =
* Bump Wordpress tested up to version 5.4
* Bump WooCommerce tested up to version 4.0

= 1.6.0 =
* Add payment method to addOrder
* Bump WooCommerce tested up to version 3.9

= 1.5.1 =
* Stop reporting to Analytics orders with `failed` status.

= 1.5.0 =
* Add a new Unique ID setting that combines the parent product ID and the specified grouping attribute terms.
* Add filter `wc_skroutz_analytics_product_id_filter` for allowing the customization of the generated Unique ID.
* Add the plugin version as a data attribute in the script tag of the analytics tracking script, to easily extract the version.
* Camelize flavor in description of Shop Account ID.
* Remove JSON.stringify() as is no longer required [skroutz/analytics.js](https://github.com/skroutz/analytics.js/pull/51)
* Bump Wordpress Tested up version to 5.3
* Bump WooCommerce Tested up version to 3.8

= 1.4.1 =
* Bump WooCommerce tested up to version 3.7

= 1.4.0 =
* Add Product Reviews Inline & Extended Widgets [docs](https://developer.skroutz.gr/partner_sku_reviews/wordpress_widgets/)

= 1.3.2 =
* Remove `deploy` directory that was falsely included in version 1.3.1.

= 1.3.1 =
* Fix `product->id`, `product->get_variation_id`, `product->parent` deprecation notices thrown in newest WooCommerce versions.

= 1.3.0 =
* Update Analytics tracking script to latest version. [docs](http://developer.skroutz.gr/analytics/#step-1-integrate-the-analytics-tracking-script)
* Add ability to use a custom global object name. [docs](http://developer.skroutz.gr/analytics/settings/#renaming-the-skroutzanalytics-object)
* Modify the default global object name from `sa` to `{site}_analytics`

= 1.2.0 =
* Add option to use a custom product id from postmeta table.
* Fix display validation errors on admin settings.

= 1.1.1 =
* Add WooCommerce `requires at least: 2.5.0` and `tested up to: 3.3.0`
* Fix settings cleanup on plugin uninstall

= 1.1.0 =
* Add an extra option to always send the parent product id/sku even if it is a variation product.
* Modify the reported product id based on the new option.
* Modify the behaviour of the product ID option. We used to send only the parent product id even if it was a variation product. We now check the type of the product and send the variation id instead if applicable.

= 1.0.6 =
Add fallback mechanism when product SKU is not set.
Generate custom product id when admin product id setting is set to SKU and the product SKU field is not set.

= 1.0.5 =
Disable cloudflare rocket loader for tracking scripts

= 1.0.4 =
* In order shipping include the shipping tax if it is setup in WooCommerce
* Fallback mechanism for order tax, if it is not properly setup in WooCommerce. A default tax rate will be used based on the flavor/country.

= 1.0.3 =
* Update readme FAQ
* Support Wordpress version > 4.0

= 1.0.2 =
Fix item price to always include the tax

= 1.0.1 =
Project restructure

= 1.0.0 =
Initial release
