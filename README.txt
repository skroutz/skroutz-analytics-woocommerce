=== WooCommerce Skroutz Analytics ===
Contributors: skroutz
Tags: skroutz, alve, scrooge, analytics, woocommerce
Requires at least: 4.0
Tested up to: 5.3
Stable tag: 1.4.1
Requires PHP: 5.4
License: GPL-2.0
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Integrate Skroutz Analytics to your WooCommerce enabled Wordpress site.

== Description ==

This plugin provides the integration between [Skroutz Analytics](http://developer.skroutz.gr/analytics/) and the [WooCommerce plugin](https://wordpress.org/plugins/woocommerce/).

Details:

* Integrates the analytics tracking script to all your frontend pages
* Integrates the ecommerce data (transactions and revenue) generated during an order.

Contributing: [Github](https://github.com/skroutz/skroutz-analytics-woocommerce)
== Installation ==

= Manual installation =
1. Download the plugin file to your computer and unzip it.
2. Using an FTP program, or your hosting control panel, upload the unzipped plugin folder to your WordPress installation’s `wp-content/plugins/` directory.
3. Activate the plugin through the Plugins menu in WordPress admin.
4. Set the `Shop Account ID` to the plugin's settings.

= Automatic installation =
1. Go to your wordpress `admin panel > Plugins > Add New`.
2. Search this plugin by its name.
3. Install the plugin.
4. Activate the plugin.
5. Set the `Shop Account ID` to the plugin's settings.

== Frequently Asked Questions ==

= Which sites do you support? =
We support Skrouz.gr, Alve.com and Scrooge.co.uk.

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

