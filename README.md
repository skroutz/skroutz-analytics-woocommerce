# Skroutz Analytics WooCommerce Plugin
Integrate skroutz analytics to your WooCommerce enabled Wordpress site

This plugin provides the integration between [Skroutz Analytics][1] and the [WooCommerce plugin][2].

* Integrates the analytics tracking script to all your frontend pages.
* Integrates the ecommerce data (transactions and revenue) generated during an order.

## Installation

The plugin is available from the [Wordpress plugin repo][11].

#### Wordpress (recommended)

1. Login to your wordpress admin panel
2. Navigate to `Plugins > Add New`
3. Search for `skroutz analytics woocommerce`
4. Install the plugin, authored by skroutz
5. Activate the plugin through the Plugins menu in WordPress admin.
6. Set the `Shop Account ID` to the plugin's settings.

#### Manual

1. Download the plugin file to your computer and unzip it.
2. Upload the unzipped plugin folder to your WordPress installation’s `wp-content/plugins/` directory.
3. Activate the plugin through the Plugins menu in WordPress admin.
4. Set the `Shop Account ID` to the plugin's settings.

## Filters

### Ecommerce data

The plugin provides [filters][12] that allows you to customize the fields that will be reported to analytics:

#### wc_skroutz_analytics_tracking_order_id_filter

Customize the order ID that we send to analytics.

* `$order_id` string The order ID
* `$order` WC_Order The order object

Usage example:

```
function my_order_id( $order_id, $order ) {
    return $order_id; // Replace with custom order ID
}
add_action( 'wc_skroutz_analytics_tracking_order_id_filter', 'my_order_id', 10, 2);
```

#### wc_skroutz_analytics_tracking_order_revenue_filter

Customize the order revenue that we send to analytics.

* `$order_revenue` float The order revenue
* `$order` WC_Order The order object

Usage example:

```
function my_order_revenue( $order_revenue, $order ) {
    return $order_revenue; // Replace with custom order revenue
}
add_action( 'wc_skroutz_analytics_tracking_order_revenue_filter', 'my_order_revenue', 10, 2);
```

#### wc_skroutz_analytics_tracking_order_shipping_filter

Customize the order shipping that we send to analytics.

* `$order_shipping` float The order shipping
* `$order` WC_Order The order object

Usage example:

```
function my_order_shipping( $order_shipping, $order ) {
    return $order_shipping; // Replace with custom order shipping
}
add_action( 'wc_skroutz_analytics_tracking_order_shipping_filter', 'my_order_shipping', 10, 2);
```

#### wc_skroutz_analytics_tracking_order_tax_filter

Customize the order tax that we send to analytics.

* `$order_tax` float The order tax
* `$order` WC_Order The order object

Usage example:

```
function my_order_tax( $order_tax, $order ) {
    return $order_tax; // Replace with custom order tax
}
add_action( 'wc_skroutz_analytics_tracking_order_tax_filter', 'my_order_tax', 10, 2);
```

#### wc_skroutz_analytics_tracking_order_paid_by_filter

Customize the order paid by that we send to analytics.

* `$order_paid_by` string The paid by
* `$order` WC_Order The order object
* `$payment_gateway` WC_Payment_Gateway The payment gateway object

Usage example:

```
function my_order_paid_by( $order_paid_by, $order, $payment_gateway ) {
    return $order_paid_by; // Replace with custom order paid by
}
add_action( 'wc_skroutz_analytics_tracking_order_paid_by_filter', 'my_order_paid_by', 10, 3);
```

#### wc_skroutz_analytics_tracking_order_paid_by_descr_filter

Customize the order paid by description that we send to analytics.

* `$order_paid_by_descr` string The paid by description
* `$order` WC_Order The order object
* `$payment_gateway` WC_Payment_Gateway The payment gateway object

Usage example:

```
function my_order_paid_by_descr( $order_paid_by_descr, $order, $payment_gateway ) {
    return $order_paid_by_descr; // Replace with custom order paid by descr
}
add_action( 'wc_skroutz_analytics_tracking_order_paid_by_descr_filter', 'my_order_paid_by_descr', 10, 3);
```

#### wc_skroutz_analytics_product_id_filter

Customize the Unique ID of each product that will be reported in an order.

* `$id` string The generated Unique ID
* `$product` WC_Product | WC_Product_Variable The product object

Usage example:

```
function my_product_id( $id, $product ) {
    return "my-{$id}-custom-{$product->get_sku()}"; // Replace with custom id
}
add_action( 'wc_skroutz_analytics_product_id_filter', 'my_product_id', 10, 2 );
```

#### wc_skroutz_analytics_tracking_item_name_filter

Customize the item name that we send to analytics.

* `$item_name` string The line item name
* `$item` WC_Order_Item The line item object
* `$product` WC_Product The product object

Usage example:

```
function my_item_name( $item_name, $item, $product ) {
    return $item_name; // Replace with custom order line item name
}
add_action( 'wc_skroutz_analytics_tracking_item_name_filter', 'my_item_name', 10, 3);
```

#### wc_skroutz_analytics_tracking_item_price_filter

Customize the item price that we send to analytics.

* `$item_price` float The line item price
* `$item` WC_Order_Item The line item object
* `$product` WC_Product The product object

Usage example:

```
function my_item_total( $item_price, $item, $product ) {
    return $item_price; // Replace with custom order line item price
}
add_action( 'wc_skroutz_analytics_tracking_item_price_filter', 'my_item_total', 10, 3);
```

#### wc_skroutz_analytics_tracking_item_quantity_filter

Customize the item quantity that we send to analytics.

* `$item_quantity` integer The line item quantity
* `$item` WC_Order_Item The line item object
* `$product` WC_Product The product object

Usage example:

```
function my_item_quantity( $item_quantity, $item, $product ) {
    return $item_quantity; // Replace with custom order line item quantity
}
add_action( 'wc_skroutz_analytics_tracking_item_quantity_filter', 'my_item_quantity', 10, 3);
```

### Widgets

#### wc_skroutz_analytics_product_reviews_widget_id_filter

Customize the ID of the product review widgets.

* `$id` string The generated ID
* `$product` WC_Product | WC_Product_Variable The product object

Usage example:

```
function my_product_reviews_widget_id( $id, $product ) {
    return "my-{$id}-custom"; // Replace with custom id
}
add_action( 'wc_skroutz_analytics_product_reviews_widget_id_filter', 'my_product_reviews_widget_id', 10, 2);
```

## FAQ

##### Which sites do you support?
We support Skroutz.gr

##### Where can I find the plugin's settings?
This plugin will add the settings to the WooCommerce Integration tab `WooCommerce > Settings > Integration > Skroutz Analytics`

##### Where can I find the `Shop Account ID`?
Visit skroutz for [merchants page][10], and navigate to the Skroutz Analytics section. Otherwise you may contact your account manager.

##### I don't see the code on my site. Where is it?
Make sure you have set your Skroutz Analytics `Shop Account ID` in the plugin settings, otherwise the tracking won't work.

##### How can I test if Skroutz Analytics is working?
Skroutz provides you a temporary `verification page` during the testing phase of the skroutz analytics integration. You can visit skroutz for [merchants page][10] or contact your account manager.

##### Which pages do you track?
This plugin does not track any admin pages, only frontend pages.

##### My code is there, but does not report any ecommece data. Why?
Duplicate Skroutz Analytics code causes a conflict in tracking. Remove any other Skroutz Analytics plugins or code from your site to avoid duplication and conflicts in tracking.

##### The order tax seems to be wrong. Why?
The plugin uses the WooCommerce tax rates you have configured in the settings. If the `Enable Taxes` option is disabled, or there are no `Tax Rates` configured, a default tax rate based on the flavor/country will be used to manually the calculate the order tax from the order revenue. So to avoid that, you need to properly setup your tax rules:

* Make sure you have enabled the `Enabled Taxes` option under `WooCommerce > Settings > Tax > Tax Options`
* And you have added **at least one** `Standard Tax Rate` under `WooCommerce > Settings > Tax > Standard Rates`
* Finally the shipping tax should be included in the order tax.
    - Make sure the `Shipping checkbox` is checked in the Tax Rates table (see above)
    - Also the `Tax Status` under `WooCommerce > Settings > Shipping > Flat Rate` must be set to `Taxable`. Note that the `Cost` value should be set excluding tax, as the tax will be automatically applied by WooCommerce. For example if you want the shipping cost to be 5 euro, you should set the cost to 4.03, given a 24% rate tax. The same applies for all the shipping methods that are enabled for your eshop.

##### I don't use neither product ID nor SKU in XML, but a custom postmeta id
The option to use a custom postmeta id is supported. You have to check the `Use custom postmeta id` option in the plugin settings, and then specify the custom id key used in the postmeta table. Note that if the custom id is not found for a product the product id/sku will be used instead.

##### I have variable products with multiple attributes that must be grouped by an attribute (e.g. color) in XML. What can I do?
Select the option `Send parent Unique ID combined with specified variation attribute term IDs` in the plugin settings and specify in the select box the grouping attribute (e.g. color). You can set more that one grouping attributes. But note that the order in which the attributes are set in the settings also affects the generated Unique ID.

The Unique ID that will be generated is a combination of the parent product ID and the specified grouping attribute terms:
`<parent product ID>-<grouping attribute term1 ID>-...-<grouping attribute termN ID>`.

The following rules apply:

* If a product is not a variant the `<product ID>` is used.
* If a product is a variant and does not have one of the declared color attributes the `<parent product ID>` is used.
* If a product is a variant and has one of the specified grouping attributes the `<parent product ID>-<attribute term ID>` is used.
* If a product is a variant and has two of the specified grouping attributes the `<parent product ID>-<attribute1 termA ID>-<attribute2 termB ID>` is used.

##### Global object name `skroutz_analytics` is already being used, can I change it?
The option to use a custom global object name is supported. You have to check the `Use custom global object name` option in the plugin settings, and then specify a name in the text field.

##### How can I integrate the Product Reviews service?
The plugin provides two WordPress Widgets, that you can easily add:

* [Product Reviews Inline Widget](https://developer.skroutz.gr/partner_sku_reviews/#inline-widget)
* [Product Reviews Extended Widget](https://developer.skroutz.gr/partner_sku_reviews/#extended-widget)

You can find detailed documentation on how to integrate the widgets [here](https://developer.skroutz.gr/partner_sku_reviews/wordpress_widgets/).

## Contributing
If you discover issues, or have any ideas for improvements and features, please report them to the [issue tracker][3] of the repository, or submit a pull request.

### Issue reporting
* Check that the issue has not already been reported.
* Check that the issue has not already been fixed in the latest code
  (a.k.a. `master`).
* Be clear, concise and precise in your description of the problem.
* Open an issue with a descriptive title and a summary in grammatically correct, complete sentences.

### Pull requests

* Read [how to properly contribute to open source projects on Github][4].
* Fork the project.
* Use a topic/feature branch to easily amend a pull request later, if necessary.
* Write [good commit messages][5].
* Use the same coding conventions as the rest of the project [Wordpress][6].
* Commit and push until you are happy with your contribution.
* [Squash related commits together][7].
* Open a [pull request][8] that relates to *only* one subject with a clear title and description in grammatically correct, complete sentences.

## License
Skroutz Analytics WooCommerce Plugin is licensed under the [GNU Public License v2][9].

[1]: http://developer.skroutz.gr/analytics/
[2]: https://wordpress.org/plugins/woocommerce/
[3]: https://github.com/skroutz/skroutz-analytics-woocommerce/issues
[4]: http://gun.io/blog/how-to-github-fork-branch-and-pull-request
[5]: http://tbaggery.com/2008/04/19/a-note-about-git-commit-messages.html
[6]: https://make.wordpress.org/core/handbook/best-practices/coding-standards/
[7]: http://gitready.com/advanced/2009/02/10/squashing-commits-with-rebase.htmlphp/
[8]: https://help.github.com/articles/using-pull-requests
[9]: LICENSE.txt
[10]: https://merchants.skroutz.gr/merchants/account/settings/analytics
[11]: https://wordpress.org/plugins/skroutz-analytics-woocommerce/
[12]: https://developer.wordpress.org/plugins/hooks/filters/
