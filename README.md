# Skroutz Analytics WooCommerce Plugin
Integrate skroutz analytics to your WooCommerce enabled Wordpress site

This plugin provides the integration between [Skroutz Analytics][1] and the [WooCommerce plugin][2]. 

* Integrates the analytics tracking script to all your frontend pages.
* Integrates the ecommerce data (transactions and revenue) generated during an order.

## Installation
1. Download the plugin file to your computer and unzip it.
2. Upload the unzipped plugin folder to your WordPress installation’s `wp-content/plugins/` directory.
3. Activate the plugin through the Plugins menu in WordPress admin.
4. Set the `Shop Account ID` to the plugin's settings.

## FAQ

##### Which sites do you support?
We support Skrouz.gr, Alve.com and Scrooge.co.uk.

##### Where can I find the plugin's settings?
This plugin will add the settings to the WooCommerce Integration tab `WooCommerce > Settings > Integration > Skroutz Analytics`

##### I don't see the code on my site. Where is it?
Make sure you have set your Skroutz Analytics `Shop Account ID` in the plugin settings, otherwise the tracking won't work.

##### Which pages do you track?
This plugin does not track any admin pages, only frontend pages.

##### My code is there, but does not report any ecommece data. Why?
Duplicate Skroutz Analytics code causes a conflict in tracking. Remove any other Skroutz Analytics plugins or code from your site to avoid duplication and conflicts in tracking.

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

