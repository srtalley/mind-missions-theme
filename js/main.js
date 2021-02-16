//version: 1.0
jQuery(function($) {
  $(document).ready(function(){
    // Make English be the only option selected
    $('.tax-product_cat:not(.term-texas) select[name="attribute_pa_language"] option[value="english"],.product_cat-state:not(.product_cat-texas) select[name="attribute_pa_language"] option[value="english"]').prop('selected', true);
  });
});
