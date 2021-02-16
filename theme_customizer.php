<?php
/**
 * Contains methods for customizing the theme customization screen.
 *
 * @link http://codex.wordpress.org/Theme_Customization_API
 * @since DustySunTheme 1.0
 */
class DustySunTheme_Customize {
   /**
    * This hooks into 'customize_register' (available as of WP 3.4) and allows
    * you to add new sections and controls to the Theme Customize screen.
    *
    * Note: To enable instant preview, we have to actually write a bit of custom
    * javascript. See live_preview() for more.
    *
    * @see add_action('customize_register',$func)
    * @param \WP_Customize_Manager $wp_customize
    * @link http://ottopress.com/2012/how-to-leverage-the-theme-customizer-in-your-own-themes/
    * @since MyTheme 1.0
    */
   public static function mindmissions_register ( $wp_customize ) {
     /* ========================================================== */
     //    MAIN PANEL
     /* ========================================================== */


     $wp_customize->add_panel( 'mindmissions_child_theme_customizations_option', array(
       'priority' => 1,
       'capability' => 'edit_theme_options',
       'title' => __('Mind Missions Custom Options', 'mindmissions_child_theme'),
     ));

     /* ========================================================== */
     // GENERAL OPTIONS PANEL
     /* ========================================================== */
     $wp_customize->add_section('mindmissions_general_section', array(
       'priority' => 5,
       'title' => __('WooCommerce Options', 'mindmissions_child_theme'),
       'panel' => 'mindmissions_child_theme_customizations_option',
       'description' => __('Customize checkout options and coming soon options.', 'mindmissions_child_theme'),
     ));

      // WooCommerce Shop Coming Soon Description
      $wp_customize->add_setting('mindmissions_custom_wc_coming_soon_shop_title_suffix', array(
        'default' => ' – COMING SOON',
        'type' => 'option'
      ));

      $wp_customize->add_control('mindmissions_custom_wc_coming_soon_shop_title_suffix', array(
        'label' => __('Coming soon product title suffix', 'mindmissions_child_theme'),
        'section' => 'mindmissions_general_section',
        'type' => 'text',
        'priority' => 10,
        'settings' => 'mindmissions_custom_wc_coming_soon_shop_title_suffix'
      ));

      // WooCommerce Shop Coming Soon Description
      $wp_customize->add_setting('mindmissions_custom_wc_coming_soon_shop_text', array(
        'default' => 'This option is not yet available, but click below to find out more.',
        'type' => 'option'
      ));

      $wp_customize->add_control('mindmissions_custom_wc_coming_soon_shop_text', array(
        'label' => __('Coming soon product text in the shop', 'mindmissions_child_theme'),
        'section' => 'mindmissions_general_section',
        'type' => 'textarea',
        'priority' => 20,
        'settings' => 'mindmissions_custom_wc_coming_soon_shop_text'
      ));


      // WooCommerce Shop Coming Soon Button Text
      $wp_customize->add_setting('mindmissions_custom_wc_coming_soon_shop_button_text', array(
        'default' => 'See More',
        'type' => 'option'
      ));

      $wp_customize->add_control('mindmissions_custom_wc_coming_soon_shop_button_text', array(
        'label' => __('Coming soon product button text in the shop', 'mindmissions_child_theme'),
        'section' => 'mindmissions_general_section',
        'type' => 'text',
        'priority' => 30,
        'settings' => 'mindmissions_custom_wc_coming_soon_shop_button_text'
      ));

      // WooCommerce Checkout Digital Content Agreement checkmark text
      $wp_customize->add_setting('mindmissions_custom_wc_checkout_digital_content_agreement_text', array(
        'default' => 'I\'ve read and accept the Mind Missions <a href="https://mindmissions.com/" target="_blank">Digital Content Agreement</a>',
        'type' => 'option'
      ));

      $wp_customize->add_control('mindmissions_custom_wc_checkout_digital_content_agreement_text', array(
        'label' => __('Checkout page Digital Content Agreement checkmark text (include links as HTML)', 'mindmissions_child_theme'),
        'section' => 'mindmissions_general_section',
        'type' => 'textarea',
        'priority' => 40,
        'settings' => 'mindmissions_custom_wc_checkout_digital_content_agreement_text'
      ));

      // WooCommerce Checkout Digital Content Agreement checkmark not checked text
      $wp_customize->add_setting('mindmissions_custom_wc_checkout_digital_content_agreement_reminder_text', array(
        'default' => 'You must accept the Mind Missions Digital Content Agreement',
        'type' => 'option'
      ));

      $wp_customize->add_control('mindmissions_custom_wc_checkout_digital_content_agreement_reminder_text', array(
        'label' => __('Reminder notice if they did not agree to the Digital Content Agreement', 'mindmissions_child_theme'),
        'section' => 'mindmissions_general_section',
        'type' => 'textarea',
        'priority' => 50,
        'settings' => 'mindmissions_custom_wc_checkout_digital_content_agreement_reminder_text'
      ));

    
  } //end public static function mindmissions_register  

} //end class

// Setup the Theme Customizer settings and controls...
add_action( 'customize_register' , array( 'DustySunTheme_Customize' , 'mindmissions_register' ) );
