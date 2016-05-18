jQuery(document).ready(function($) {
  var updateLink = function(link, flavor) {
    link.text(flavor);
    link.attr('href', wc_skroutz_analytics.flavors[flavor+'_merchants_url']);
  },
  $link = $('#merchants_link'),
  $flavorDropdown = $('#woocommerce_wc_skroutz_analytics_sa_flavor'),
  updateMerchantLink = function(flavor) { return updateLink($link, flavor); };

  // Initialize
  updateMerchantLink($flavorDropdown.val());

  // Listen for flavor changes
  $flavorDropdown.on('change', function() { updateMerchantLink(this.value); });
});
