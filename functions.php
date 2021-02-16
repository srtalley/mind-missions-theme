<?php
// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

// BEGIN ENQUEUE PARENT ACTION
// AUTO GENERATED - Do not modify or remove comment markers above or below:

if ( !function_exists( 'chld_thm_cfg_locale_css' ) ):
    function chld_thm_cfg_locale_css( $uri ){
        if ( empty( $uri ) && is_rtl() && file_exists( get_template_directory() . '/rtl.css' ) )
            $uri = get_template_directory_uri() . '/rtl.css';
        return $uri;
    }
endif;
add_filter( 'locale_stylesheet_uri', 'chld_thm_cfg_locale_css' );

if ( !function_exists( 'chld_thm_cfg_parent_css' ) ):
    function chld_thm_cfg_parent_css() {
        wp_enqueue_style( 'chld_thm_cfg_parent', trailingslashit( get_template_directory_uri() ) . 'style.css', array(  ) );
        wp_enqueue_script( 'main', get_stylesheet_directory_uri() . '/js/main.js', '', '1.0', true );
    }
endif;
add_action( 'wp_enqueue_scripts', 'chld_thm_cfg_parent_css', 10 );

// END ENQUEUE PARENT ACTION
/**
 * Add quantity field on the shop page.
 */
function ds_shop_page_add_quantity_field() {
	/** @var WC_Product $product */
	$product = wc_get_product( get_the_ID() );
	if ( ! $product->is_sold_individually() && 'variable' != $product->get_type() && $product->is_purchasable() ) {
        echo '<div class="woocommerce-archive-quantity"><div class="woocommerce-archive-quantity-label">Quantity: </div>'; 
        woocommerce_quantity_input( array( 'min_value' => 1, 'max_value' => $product->backorders_allowed() ? '' : $product->get_stock_quantity() ) );
        echo '</div>';
	}
}
add_action( 'woocommerce_after_shop_loop_item', 'ds_shop_page_add_quantity_field', 8 );
/**
 * Add required JavaScript.
 */
function ds_shop_page_quantity_add_to_cart_handler() {
	wc_enqueue_js( '
		$(".woocommerce .products").on("click", ".quantity input", function() {
			return false;
		});
		$(".woocommerce .products").on("change input", ".quantity .qty", function() {
			var add_to_cart_button = $(this).parents( ".product" ).find(".add_to_cart_button");
			// For AJAX add-to-cart actions
			add_to_cart_button.data("quantity", $(this).val());
			// For non-AJAX add-to-cart actions
			add_to_cart_button.attr("href", "?add-to-cart=" + add_to_cart_button.attr("data-product_id") + "&quantity=" + $(this).val());
		});
		// Trigger on Enter press
		$(".woocommerce .products").on("keypress", ".quantity .qty", function(e) {
			if ((e.which||e.keyCode) === 13) {
				$( this ).parents(".product").find(".add_to_cart_button").trigger("click");
			}
		});
	' );
}
add_action( 'init', 'ds_shop_page_quantity_add_to_cart_handler' );



/**
 * Make it so the image is on the left and the other items on the right on shop pages
 */
add_action('woocommerce_before_shop_loop_item_title', 'ds_change_shop_layout_open', 100,1);

function ds_change_shop_layout_open($string) {
    echo '</a> <!--close product image tag-->';
    global $product;

    $link = apply_filters( 'woocommerce_loop_product_link', get_the_permalink(), $product );

    echo '<div class="woocommerce-archive-right-col"><a href="' . esc_url( $link ) . '" class="woocommerce-loop-product-title__link">';
}

add_action( 'woocommerce_after_shop_loop_item', 'ds_change_shop_layout_close', 200 );

function ds_change_shop_layout_close($string) {

    echo '</div> <!-- woocommerce-archive-right-col -->';
    
}
/* Add Coming Soon to Coming Soon products */
remove_action( 'woocommerce_shop_loop_item_title','woocommerce_template_loop_product_title', 10 );
add_action('woocommerce_shop_loop_item_title', 'ds_change_coming_soon_products_title', 10 );
function ds_change_coming_soon_products_title() {
    global $product;
    // echo $product->get_id();
    $product_cats_ids = wc_get_product_term_ids( $product->get_id(), 'product_cat' );
    $title_append = '';
    foreach( $product_cats_ids as $cat_id ) {
        $term = get_term_by( 'id', $cat_id, 'product_cat' );
    
        // echo $term->slug;
        if($term->slug == 'coming-soon') {
            $mindmissions_custom_wc_coming_soon_shop_title_suffix = get_option('mindmissions_custom_wc_coming_soon_shop_title_suffix', false);
            if($mindmissions_custom_wc_coming_soon_shop_title_suffix == '') {
                $mindmissions_custom_wc_coming_soon_shop_title_suffix = ' – COMING SOON';
            }
            $title_append = $mindmissions_custom_wc_coming_soon_shop_title_suffix;
        }
    }
    echo '<h2 class="woocommerce-loop-product__title">' . get_the_title() . $title_append . '</h2>';
}

function wl ( $log ) {
    if ( is_array( $log ) || is_object( $log ) ) {
    error_log( print_r( $log, true ) );
    } else {
    error_log( $log );
    }
}


/**
 * Replace add to cart button in the loop.
 */
function ds_change_loop_add_to_cart() {
    remove_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_add_to_cart', 10 );
    add_action( 'woocommerce_after_shop_loop_item', 'ds_template_loop_add_to_cart', 10 );
}
 
add_action( 'init', 'ds_change_loop_add_to_cart', 10 );
 
/**
 * Use single add to cart button for variable products.
 */
function ds_template_loop_add_to_cart() {
    global $product;

    $product_cats_ids = wc_get_product_term_ids( $product->get_id(), 'product_cat' );
    $coming_soon_product = '';
    foreach( $product_cats_ids as $cat_id ) {
        $term = get_term_by( 'id', $cat_id, 'product_cat' );
        if($term->slug == 'coming-soon') {
            $coming_soon_product = true;
        } // endif
    } // end foreach

    if($coming_soon_product) {
        $mindmissions_custom_wc_coming_soon_shop_text = get_option('mindmissions_custom_wc_coming_soon_shop_text', false);
        if($mindmissions_custom_wc_coming_soon_shop_text == '') {
            $mindmissions_custom_wc_coming_soon_shop_text = 'This option is not yet available, but click below to find out more.';
        }
        echo '<div class="wc-coming-soon">' . $mindmissions_custom_wc_coming_soon_shop_text . '</div>';

        $mindmissions_custom_wc_coming_soon_shop_button_text = get_option('mindmissions_custom_wc_coming_soon_shop_button_text', false);
        if($mindmissions_custom_wc_coming_soon_shop_button_text == '') {
            $mindmissions_custom_wc_coming_soon_shop_button_text = 'See More';
        }
        echo '<a href="' . get_the_permalink() . '" class="button add_to_cart_button wc-coming-soon-button">' . $mindmissions_custom_wc_coming_soon_shop_button_text . '</a>';
        return;
    }
    if( !$product->is_type( 'variable' ) ) {
        woocommerce_template_loop_add_to_cart();
        return;
    }

    woocommerce_template_single_add_to_cart();
}
 
/**
 * Customise variable add to cart button for loop.
 *
 * Remove qty selector and simplify.
 */
function ds_loop_variation_add_to_cart_button() {
    global $product;
 
    ?>
    <div class="woocommerce-variation-add-to-cart variations_button">
        <button type="submit" class="single_add_to_cart_button button"><?php echo esc_html( $product->single_add_to_cart_text() ); ?></button>
        <input type="hidden" name="add-to-cart" value="<?php echo absint( $product->get_id() ); ?>" />
        <input type="hidden" name="product_id" value="<?php echo absint( $product->get_id() ); ?>" />
        <input type="hidden" name="variation_id" class="variation_id" value="0" />
    </div>
    <?php
}

/**
 * Replace the home link URL
 */
add_filter( 'woocommerce_breadcrumb_home_url', 'woo_custom_breadrumb_home_url' );
function woo_custom_breadrumb_home_url() {
    return '/curriculum-store';
}

/**
 * Change several of the breadcrumb defaults
 */
add_filter( 'woocommerce_breadcrumb_defaults', 'jk_woocommerce_breadcrumbs' );
function jk_woocommerce_breadcrumbs() {
    return array(
            'delimiter'   => '<span class="woocommerce-breadcrumb-delimiter"> &raquo; </span>',
            'wrap_before' => '<nav class="woocommerce-breadcrumb" itemprop="breadcrumb">',
            'wrap_after'  => '</nav>',
            'before'      => '<span class="woocommerce-breadcrumb-item">',
            'after'       => '</span>',
            'home'        => _x( 'Curriculum Store', 'breadcrumb', 'woocommerce' ),
        );
}

/*
 * Make the Out of Stock items so they can't be selected
 */
add_filter( 'woocommerce_variation_is_active', 'ds_gray_out_variations_out_of_stock', 10, 2 );
 
function ds_gray_out_variations_out_of_stock( $is_active, $variation ) {
    if ( ! $variation->is_in_stock() ) return false;
    return $is_active;
} // end ds_gray_out_variations_out_of_stock

/*
 * Make the variable items price text say starting from
 */
add_filter( 'woocommerce_variable_price_html', 'ds_variation_price_format_min', 9999, 2 );
  
function ds_variation_price_format_min( $price, $product ) {
   $prices = $product->get_variation_prices( true );
   $min_price = current( $prices['price'] );
   $price = sprintf( __( 'From: %1$s', 'woocommerce' ), wc_price( $min_price ) );
   return $price;
}

/*
 * Make the tax exempt checkout field work and actually remove the taxes
 */
add_action( 'woocommerce_checkout_update_order_review', 'ds_taxexempt_checkout_update_order_review');
function ds_taxexempt_checkout_update_order_review( $post_data ) {

  global $woocommerce;

  $woocommerce->customer->set_is_vat_exempt(FALSE);
  parse_str($post_data, $woocommerce_checkout_fields);

  extract($woocommerce_checkout_fields);

  if(isset($woocommerce_checkout_fields['additional_wooccm1']) && $woocommerce_checkout_fields['additional_wooccm1'] == 1) {
    $woocommerce->customer->set_is_vat_exempt(true);                
  }

} // end function function ds_taxexempt_checkout_update_order_review

/*
 * Add a script to handle what happens when the tax exempt button is checked - forces
 * the cart to update and show $0 for tax
 */
add_action('wp_head', 'ds_taxexempt_checkout_js');
function ds_taxexempt_checkout_js() {

  if(is_checkout()) {
    // do the checkout stuff
    ?>
    <script type="text/javascript">
        //v1
        jQuery(function($) {

          $(document).ready(function() {
            
            // detect click on the tax exempt field and then update the checkout
            $('#additional_wooccm1').on('click', function() {
                $(document.body).trigger("update_checkout");
           }); // end select on change

          }); // document ready
        }); // outer wrapper
    </script>
    <?php
  } // end if is_checkout
} // end function ds_taxexempt_checkout_js

/**
 * Modify the error message that is shown when the tax exemption upload is empty
 */
add_filter( 'woocommerce_add_error', 'ds_tax_exempt_documentation_modify_error' );
function ds_tax_exempt_documentation_modify_error( $error ) {

    $fields = WC()->checkout->get_checkout_fields('additional');
    if(isset($fields['additional_wooccm0']['label'])) {
        $tax_exempt_field_label = '<strong>' . $fields['additional_wooccm0']['label'] . '</strong> is a required field.';
    }
     if( $tax_exempt_field_label == $error ) {
         $error = 'You must upload your tax exemption information below.';
     }
     return $error;
 }

/**
 * If the Quote option is chosen, don't require them upload a tax document
 */
add_filter( 'woocommerce_checkout_fields', 'dst_no_required_upload_on_quote', 100,1 );

function dst_no_required_upload_on_quote( $fields ){
    if(isset($_POST['payment_method']) && $_POST['payment_method'] == 'sliced-invoices') {
        unset( $fields['additional']['additional_wooccm0']['required'] );
    }
	return $fields;
}

/*
 * Everything is set to just be authorized by PayPal and not captured by default. This makes
 * all orders go into WooCommerce with an on-hold status. When this happens or when an order is updated,
 * if the order status is on-hold, and the tax exempt box was NOT checked, then the order is automatically
 * moved to processing which will capture the payment from PayPal.
 */
add_action('woocommerce_order_status_changed', 'ds_auto_complete_by_payment_method');
function ds_auto_complete_by_payment_method($order_id) {

    if ( ! $order_id ) {
      return;
    }

    global $product;
    $order = wc_get_order( $order_id );
    
    if ($order->data['status'] == 'on-hold') {

        $tax_exempt_status = get_post_meta($order_id, '_additional_wooccm1');

        if ($tax_exempt_status[0] != 1 ) {
            $order->update_status( 'processing' );
        }
    }
}


/*
 * Change the Place Order button to Request a Quote if Sliced Invoices is chosen
 */
add_filter('woocommerce_available_payment_gateways', 'ds_sliced_invoices_change_place_order_button');
function ds_sliced_invoices_change_place_order_button($gateways) {
    if($gateways['sliced-invoices']) {
        $gateways['sliced-invoices']->order_button_text = 'Request a Quote';
    }
    return $gateways;
} //end function ds_sliced_invoices_change_place_order_button


/**
 * Add the digital content agreement checkbox before the submit payment button
 */
add_action( 'woocommerce_review_order_before_submit', 'dst_add_checkout_digital_content_agreement', 9 );
    
function dst_add_checkout_digital_content_agreement() {

    $show_digital_content_agreement = dst_check_cart_for_digital_products();

    if($show_digital_content_agreement) {
        $mindmissions_custom_wc_checkout_digital_content_agreement_text = get_option('mindmissions_custom_wc_checkout_digital_content_agreement_text', false);
        if($mindmissions_custom_wc_checkout_digital_content_agreement_text == '') {
            $mindmissions_custom_wc_checkout_digital_content_agreement_text = 'I\'ve read and accept the Mind Missions <a href="https://mindmissions.com/" target="_blank">Digital Content Agreement</a>';
        } // end if
        woocommerce_form_field( 'mind_missions_digital_content_agreement', array(
            'type'          => 'checkbox',
            'class'         => array('form-row privacy'),
            'label_class'   => array('woocommerce-form__label woocommerce-form__label-for-checkbox checkbox'),
            'input_class'   => array('woocommerce-form__input woocommerce-form__input-checkbox input-checkbox'),
            'required'      => true,
            'label'         => $mindmissions_custom_wc_checkout_digital_content_agreement_text,
            )); 
    } // end if

   
} //end function dst_add_checkout_digital_content_agreement

/**
 * Show notice if customer does not tick
 */ 
    
add_action( 'woocommerce_checkout_process', 'dst_show_digital_content_agreement_not_approved' );
function dst_show_digital_content_agreement_not_approved() {
    $show_digital_content_agreement = dst_check_cart_for_digital_products();

    if($show_digital_content_agreement) {
        if ( ! (int) isset( $_POST['mind_missions_digital_content_agreement'] ) ) {
            $mindmissions_custom_wc_checkout_digital_content_agreement_reminder_text = get_option('mindmissions_custom_wc_checkout_digital_content_agreement_reminder_text', false);
            if($mindmissions_custom_wc_checkout_digital_content_agreement_reminder_text == '') {
                $mindmissions_custom_wc_checkout_digital_content_agreement_reminder_text = 'You must accept the Mind Missions Digital Content Agreement';
            } // end if
            wc_add_notice( __( $mindmissions_custom_wc_checkout_digital_content_agreement_reminder_text ), 'error' );
        } // end if
    } // end if
} // end function dst_show_digital_content_agreement_not_approved

/**
 * Function to check if there are digital products present in the cart
 */
function dst_check_cart_for_digital_products() {
    $found_digital_product = false;
    global $woocommerce;
    foreach ( $woocommerce->cart->cart_contents as $key => $values ) {
        $category_slugs = array( 'digital' );
        $terms = get_the_terms( $values['product_id'], 'product_cat' );
        foreach ( $terms as $term ) {
            if ( in_array( $term->slug, $category_slugs ) ) {
                $found_digital_product = true;
                break;
            } // end if
        } // end foreach
    }// end foreach
    if($found_digital_product) {
        return true; 
    } else { 
        return false;
    }
} // end function dst_check_cart_for_digital_products

/**
 * Add a return button to the quote shown to the user after submitting the quote
 */
add_action('sliced_quote_after_body', 'ds_sliced_before_quote_display');
function ds_sliced_before_quote_display() {
    if(is_single()) {
       
        if(isset($_SERVER['HTTP_REFERER'])) {
            $referring_url = $_SERVER['HTTP_REFERER'];
        } else {
            $referring_url = site_url();
        }
        echo '<form class="sliced-quote-return" style="margin-left: 20px;">
                <a href="' . $referring_url . '" style="display: inline-block; color: #FFF; background-color: #337ab7; padding: 15px 50px; border-radius: 3px;">< Return</a>
            </form>';
    }
} // end function ds_sliced_before_quote_display

/*
 * If this is a phone order, make sure to create the quote
 */
add_action( 'woocommerce_checkout_phone_order_processed', 'dst_woocommerce_checkout_phone_order_processed', 10, 3);

function dst_woocommerce_checkout_phone_order_processed($order_id, $checkout_data, $order) {

    // Disable sending the quote email
    add_filter( 'woocommerce_email_recipient_customer_quote', 'dcwd_conditionally_send_wc_email', 10, 2 );

    $order_items = $order->get_items();
    sliced_woocommerce_create_quote_or_invoice('quote', $order, $order_items);

} // end function dst_woocommerce_checkout_phone_order_processed

/*
 * Disable the quote email
 */    
function dcwd_conditionally_send_wc_email( $whether_enabled, $object ) {
    return false;
}
/*
 * Remove the WooCommerce Cart, Checkout and My Account pages from the sitemap
 */ 
add_filter( 'wpseo_exclude_from_sitemap_by_post_ids', function () {
    return array( 953, 954, 5725 );
  } );



////////////////////////////////////////////////////
// CHILD THEME CUSTOMIZER OPTIONS
////////////////////////////////////////////////////
require_once('theme_customizer.php');