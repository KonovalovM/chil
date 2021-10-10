<?php
/*
VarkTech Minimum Purchase for WooCommerce
Woo-specific functions
Parent Plugin Integration
*/


class VTMIN_Parent_Definitions {
	
	public function __construct(){
    
    define('VTMIN_PARENT_PLUGIN_NAME',                      'WooCommerce');
    define('VTMIN_EARLIEST_ALLOWED_PARENT_VERSION',         '2.1.0');  //v1.0.9.5  plugin now uses WOO messaging to send messages to screen
    define('VTMIN_TESTED_UP_TO_PARENT_VERSION',             '1.6.6');
    define('VTMIN_DOCUMENTATION_PATH_PRO_BY_PARENT',        'http://www.varktech.com/woocommerce/minimum-purchase-pro-for-woocommerce/?active_tab=tutorial');                                                                                                     //***
    define('VTMIN_DOCUMENTATION_PATH_FREE_BY_PARENT',       'http://www.varktech.com/woocommerce/minimum-purchase-for-woocommerce/?active_tab=tutorial');      
    define('VTMIN_INSTALLATION_INSTRUCTIONS_BY_PARENT',     'http://www.varktech.com/woocommerce/minimum-purchase-for-woocommerce/?active_tab=instructions');
    define('VTMIN_PRO_INSTALLATION_INSTRUCTIONS_BY_PARENT', 'http://www.varktech.com/woocommerce/minimum-purchase-pro-for-woocommerce/?active_tab=instructions');
    define('VTMIN_PURCHASE_PRO_VERSION_BY_PARENT',          'http://www.varktech.com/woocommerce/minimum-purchase-pro-for-woocommerce/');
    define('VTMIN_DOWNLOAD_FREE_VERSION_BY_PARENT',         'http://wordpress.org/extend/plugins/minimum-purchase-for-woocommerce/');
    
    //html default selector locations in checkout where error message will display before.
    define('VTMIN_CHECKOUT_PRODUCTS_SELECTOR_BY_PARENT',    '.shop_table');        // PRODUCTS TABLE on BOTH cart page and checkout page
    define('VTMIN_CHECKOUT_ADDRESS_SELECTOR_BY_PARENT',     '#customer_details');      //  address area on checkout page    default = on
        
    error_reporting(E_ALL ^ E_NOTICE ^ E_WARNING ^ E_DEPRECATED ); // v1.0.9

    global $vtmin_info;      
    $default_full_msg   =  __('Enter Custom Message (optional)', 'vtmin');   //v1.08 fixed v1.09
    $default_msg_title =  __('Minimum Purchase Error', 'vtmin');   //v1.09.91.2  
    $vtmin_info = array(                                                                    
      	'parent_plugin' => 'woo',
      	'parent_plugin_taxonomy' => 'product_cat',
        'parent_plugin_taxonomy_name' => 'Product Categories',
        'parent_plugin_cpt' => 'product',
        'applies_to_post_types' => 'product', //rule cat only needs to be registered to product, not rule as well...
        'rulecat_taxonomy' => 'vtmin_rule_category',
        'rulecat_taxonomy_name' => 'Minimum Purchase Rules',
        
        //elements used in vtmin-apply-rules.php at the ruleset level
        'error_message_needed' => 'no',
        'cart_grp_info' => '',
          /*  cart_grp_info will contain the following:
            array(
              'qty'    => '',
              'price'    => ''
            )
          */
        'cart_color_cnt' => '',
        'rule_id_list' => '',
        'line_cnt' => 0,
        'action_cnt'  => 0,
        'bold_the_error_amt_on_detail_line'  => 'no',
        'currPageURL'  => '',
        'woo_cart_url'  => '',
        'woo_checkout_url'  => '',
        'woo_pay_url'  => '',
        'default_full_msg'  => $default_full_msg, //v1.08
        'default_msg_title'  => $default_msg_title //v1.09.91.2
      );

	}

} //end class
$vtmin_parent_definitions = new VTMIN_Parent_Definitions;

  //v2.0.0 BEGIN 
  //NEEDS TO BE HERE

  function  vtmin_get_ip_address() {
    
    /* 
        //IF YOU MUST OVERRIDE THE IP ADDRESS ON A PERMANENT BASIS
        //USE SOMETHING LIKE https://www.site24x7.com/find-ip-address-of-web-site.html to find your website IP address (**NOT** your CLIENT ip address)
        //copy code begin
        add_filter('vtmin_override_with_supplied_ip_address', 'override_with_supplied_ip_address', 10 );        
        function override_with_supplied_ip_address() {  return 'YOUR IP ADDRESS HERE'; }
        //copy code end                
    */
    if (apply_filters('vtmin_override_with_supplied_ip_address',FALSE) ) {
      return apply_filters('vtmin_override_with_supplied_ip_address');
    }
    
    
    /*  // IP address license check can fail if you have copied your whole site with options table from one IP address to another
        // ==>>>>> only ever do this with a SINGLE RULE SCREEN ACCESS, 
        // then remove from your theme functions.php file ==>>>>> heavy server resource cost if executed constantly!!!!!!!
        //copy code begin
        add_filter('vtmin_force_new_ip_address', 'force_new_ip_address', 10 );        
        function force_new_ip_address() {  return 'yes'; } 
        //copy code end
    */
    if (apply_filters('vtmin_force_new_ip_address',FALSE) ) {
      $skip_this = true;
    } else {
      $vtmin_ip_address = get_option( 'vtmin_ip_address' );
      if ($vtmin_ip_address) {
        return $vtmin_ip_address;
      }    
    }

    
    //THIS ONLY OCCURS WHEN THE PLUGIN IS FIRST INSTALLED!
    // from http://stackoverflow.com/questions/4305604/get-ip-from-dns-without-using-gethostbyname
    
    //v1.1.6.3 refactored, put in test for php version
    $php_version = phpversion();
    if ( version_compare( $php_version, '5.3.1', '<' ) ) {
      $vtmin_ip_address = $_SERVER['SERVER_ADDR'];
    } else {    
      $host = gethostname();
      $query = `nslookup -timeout=$timeout -retry=1 $host`;
      if(preg_match('/\nAddress: (.*)\n/', $query, $matches)) {
        $vtmin_ip_address =  trim($matches[1]);
      } else {
        $vtmin_ip_address = gethostbyname($host);
      }    
    }	

    
    update_option( 'vtmin_ip_address', $vtmin_ip_address );
    
    return $vtmin_ip_address;

  }
  //v2.0.0 END
