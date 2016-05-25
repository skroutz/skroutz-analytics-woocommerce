=== WooCommerce Skroutz Analytics ===
Contributors: Skroutz SA
Tags: skroutz, alve, scrooge, analytics, woocommerce
Requires at least: 4.1
Tested up to: 4.5.2
Stable tag: 1.0.1
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

= I don't see the code on my site. Where is it? =
Make sure you have set your Skroutz Analytics `Shop Account ID` in the plugin settings, otherwise tracking won't work.

= Which pages do you track? =
This plugin does not track any admin pages, only frontend pages.

= My code is there, but does not report any ecommece data. Why? =
Duplicate Skroutz Analytics code causes a conflict in tracking. Remove any other Skroutz Analytics plugins or code from your site to avoid duplication and conflicts in tracking.

== Screenshots ==

1. Skroutz Analytics settings panel.
2. Skroutz Analytics statistics in the Skroutz for merchants.

== Changelog ==

= 1.0.1 =
Project restructure

= 1.0.0 =
Initial release

