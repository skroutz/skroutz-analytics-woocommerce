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

  var $customIdCheckbox = $('#woocommerce_wc_skroutz_analytics_sa_items_custom_id_enabled'),
  $customId = $('#woocommerce_wc_skroutz_analytics_sa_items_custom_id').closest('tr'),
  showHideCustomId = function() { $customIdCheckbox.is(':checked') ? $customId.show() : $customId.hide(); };

  var $objectNameCheckbox = $('#woocommerce_wc_skroutz_analytics_sa_custom_global_object_name_enabled'),
  $objectName = $('#woocommerce_wc_skroutz_analytics_sa_custom_global_object_name').closest('tr'),
  showHideGlobalObjectName = function() { $objectNameCheckbox.is(':checked') ? $objectName.show() : $objectName.hide(); };

  var $variationIDRadios = $('input[name="woocommerce_wc_skroutz_analytics_sa_items_product_parent_id_enabled"]'),
      $termGroupingRadio = $('#woocommerce_wc_skroutz_analytics_sa_items_product_parent_id_term_id'),
      $groupingAttributes = $('#woocommerce_wc_skroutz_analytics_sa_items_grouping_attributes').closest('tr'),
      showHideGroupingAttributes = function() { $termGroupingRadio.is(':checked') ? $groupingAttributes.show() : $groupingAttributes.hide(); };

  // Initialize
  showHideCustomId();
  showHideGlobalObjectName();
  showHideGroupingAttributes();

  // Listen for checkbox changes
  $customIdCheckbox.on('change', function() { showHideCustomId(); });
  $objectNameCheckbox.on('change', function() { showHideGlobalObjectName(); });
  $variationIDRadios.change(showHideGroupingAttributes);

  // initialize select2
  var multiselects = $('select[multiple]');
  multiselects.select2();

  // Make select2 respect selection order
  // `select2-selecting` is the proper event for version 3, while `select2:select` is
  // the event for version 4. To get the value of the selected option, in version 3 we
  // have to use `e.val`, while in version 4 `e.params.data.id`.
  // Select2 was updated to version 4 in WooCommerce version 3.0.
  multiselects.on('select2:select select2-selecting', function(e){
    var id = e.val || e.params.data.id;
    var option = $(e.target).children('[value='+id+']');
    option.detach();
    $(e.target).append(option).change();
  });
});
