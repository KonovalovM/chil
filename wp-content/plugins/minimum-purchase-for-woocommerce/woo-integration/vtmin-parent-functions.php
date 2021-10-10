<?php
/*
VarkTech Minimum Purchase for WooCommerce
WPSC-specific functions
Parent Plugin Integration
*/

 	
	function vtmin_load_vtmin_cart_for_processing(){
      global $wpdb,  $woocommerce, $vtmin_cart, $vtmin_cart_item, $vtmin_info; 
      
      // from Woocommerce/templates/cart/mini-cart.php  and  Woocommerce/templates/checkout/review-order.php
        
      if (sizeof($woocommerce->cart->get_cart())>0) {
      
					$woocommerce->cart->calculate_totals(); //v1.09.3 calculation includes generating line subtotals, used below
          
          $vtmin_cart = new VTMIN_Cart;  
          foreach ( $woocommerce->cart->get_cart() as $cart_item_key => $cart_item ) {
						$_product = $cart_item['data'];
						if ($_product->exists() && $cart_item['quantity']>0) {
							$vtmin_cart_item                = new VTMIN_Cart_Item;
             
              //the product id does not change in woo if variation purchased.  
              //  Load expected variation id, if there, along with constructed product title.
              $varLabels = ' ';
              if ($cart_item['variation_id'] > ' ') {      
                 
                  // get parent title
                  $parent_post = get_post($cart_item['product_id']);
                  
                  // get variation names to string onto parent title
                  foreach($cart_item['variation'] as $key => $value) {          
                    $varLabels .= $value . '&nbsp;';           
                  }
                  
                  $vtmin_cart_item->product_id    = $cart_item['variation_id'];
                  $vtmin_cart_item->product_name  = $parent_post->post_title . '&nbsp;' . $varLabels ;

              } else { 
                  $vtmin_cart_item->product_id    = $cart_item['product_id'];
                  //$vtmin_cart_item->product_name  = $_product->get_title().$woocommerce->cart->get_item_data( $cart_item );
                  //v1.08.2.1 begin
                  if ( version_compare( WC_VERSION, '3.3.0', '>=' ) ) {
                    $vtmin_cart_item->product_name  = $_product->get_title().wc_get_formatted_cart_item_data($cart_item); 
                  } else {
                    $vtmin_cart_item->product_name  = $_product->get_title().$woocommerce->cart->get_item_data( $cart_item ); 
                  }
                  //v1.08.2.1 end                       
              }
  
              
              $vtmin_cart_item->quantity      = $cart_item['quantity'];
              
              //v1.09.3 commented unit price
              //$vtmin_cart_item->unit_price    = get_option( 'woocommerce_display_cart_prices_excluding_tax' ) == 'yes' || $woocommerce->customer->is_vat_exempt() ? $_product->get_price_excluding_tax() : $_product->get_price();
              
              /*
              $quantity = 1; //v1.08 vat fix
              $vtmin_cart_item->unit_price    = get_option( 'woocommerce_display_cart_prices_excluding_tax' ) == 'yes' || $woocommerce->customer->is_vat_exempt() ? $_product->get_price_excluding_tax() : $_product->get_price_including_tax( $quantity ); //$_product->get_price();   //v1.08 vat fix
              */
              
              //v1.09.3 begin
              //  pick up unit price from line subtotal only - 
              //  will include all taxation and price adjustments from other plugins
              
              //$vtmin_cart_item->total_price   = $vtmin_cart_item->quantity * $vtmin_cart_item->unit_price;
    //          $vtmin_cart_item->total_price = $cart_item['line_subtotal'];
    //          $vtmin_cart_item->unit_price  = $cart_item['line_subtotal'] / $cart_item['quantity'];

              if ( ( get_option( 'woocommerce_calc_taxes' ) == 'no' ) ||
                   ( get_option( 'woocommerce_prices_include_tax' ) == 'no' ) ) {      
                 //NO VAT included in price
                 $vtmin_cart_item->unit_price  =  $cart_item['line_subtotal'] / $cart_item['quantity'];  
                 $vtmin_cart_item->total_price =  $cart_item['line_subtotal'];                                                
              } else {
                 
                 //v1.0.7.4 begin
                 //TAX included in price in DB, and Woo $cart_item pricing **has already subtracted out the TAX **, so restore the TAX
                 //  this price reflects the tax situation of the ORIGINAL price - so if the price was originally entered with tax, this will reflect tax
                 $price           =  $cart_item['line_subtotal']  / $cart_item['quantity'];    
                 $qty = 1;           
                 $_tax  = new WC_Tax();                
                // $product = get_product( $product_id ); 
                 $product = wc_get_product( $vtmin_cart_item->product_id  ); //v1.1.7 replace get_product with wc_get_product
                 $tax_rates  = $_tax->get_rates( $product->get_tax_class() );
        			 	 $taxes      = $_tax->calc_tax( $price  * $qty, $tax_rates, false );
        				 $tax_amount = $_tax->get_tax_total( $taxes );
        				 $vtmin_cart_item->unit_price  = round( $price  * $qty + $tax_amount, absint( get_option( 'woocommerce_price_num_decimals' ) ) ); 
                 $vtmin_cart_item->total_price = ($vtmin_cart_item->unit_price * $cart_item['quantity']);
               }                             
              //v1.09.3 end
              
              /*  *********************************
              ***  JUST the cat *ids* please...
              ************************************ */
              $vtmin_cart_item->prod_cat_list = wp_get_object_terms( $cart_item['product_id'], $vtmin_info['parent_plugin_taxonomy'], $args = array('fields' => 'ids') );
              $vtmin_cart_item->rule_cat_list = wp_get_object_terms( $cart_item['product_id'], $vtmin_info['rulecat_taxonomy'], $args = array('fields' => 'ids') );
        
              //add cart_item to cart array
              $vtmin_cart->cart_items[]       = $vtmin_cart_item;
				    }
        } //	endforeach;
			} 
           
  }      

 
   //  checked_list (o) - selection list from previous iteration of rule selection                                 
    function vtmin_fill_variations_checklist ($tax_class, $checked_list = NULL, $product_ID, $product_variation_IDs) { 
        global $post;
        // *** ------------------------------------------------------------------------------------------------------- ***
        // additional code from:  woocommerce/admin/post-types/writepanels/writepanel-product-type-variable.php
        // *** ------------------------------------------------------------------------------------------------------- ***
        //    woo doesn't keep the variation title in post title of the variation ID post, additional logic constructs the title ...
        
        $parent_post = get_post($product_ID);
        
        $attributes = (array) maybe_unserialize( get_post_meta($product_ID, '_product_attributes', true) );
    
        $parent_post_terms = wp_get_post_terms( $post->ID, $attribute['name'] );
       
        // woo parent product title only carried on parent post
        echo '<h3>' .$parent_post->post_title.    ' - Variations</h3>'; 
        
        foreach ($product_variation_IDs as $product_variation_ID) {     //($product_variation_IDs as $product_variation_ID => $info)
            // $variation_post = get_post($product_variation_ID);
         
            $output  = '<li id='.$product_variation_ID.'>' ;
            $output  .= '<label class="selectit">' ;
            $output  .= '<input id="'.$product_variation_ID.'_'.$tax_class.' " ';
            $output  .= 'type="checkbox" name="tax-input-' .  $tax_class . '[]" ';
            $output  .= 'value="'.$product_variation_ID.'" ';
            if ($checked_list) {
                if (in_array($product_variation_ID, $checked_list)) {   //if variation is in previously checked_list   
                   $output  .= 'checked="checked"';
                }                
            }
            $output  .= '>'; //end input statement
 
            $variation_label = ''; //initialize label
            
            //get the variation names
            foreach ($attributes as $attribute) :

									// Only deal with attributes that are variations
									if ( !$attribute['is_variation'] ) continue;

									// Get current value for variation (if set)
									$variation_selected_value = get_post_meta( $product_variation_ID, 'attribute_' . sanitize_title($attribute['name']), true );

									// Get terms for attribute taxonomy or value if its a custom attribute
									if ($attribute['is_taxonomy']) :
										$post_terms = wp_get_post_terms( $product_ID, $attribute['name'] );
										foreach ($post_terms as $term) :
											if ($variation_selected_value == $term->slug) {
                          $variation_label .= $term->name . '&nbsp;&nbsp;' ;
                      }
										endforeach;
									else :
										$options = explode('|', $attribute['value']);
										foreach ($options as $option) :
											if ($variation_selected_value == $option) {
                        $variation_label .= ucfirst($option) . '&nbsp;&nbsp;' ;
                      }
										endforeach;
									endif;

						endforeach;
                
            $output  .= '&nbsp;&nbsp; #' .$product_variation_ID. '&nbsp;&nbsp; - &nbsp;&nbsp;' .$variation_label;
            $output  .= '</label>';            
            $output  .= '</li>'; 
            echo $output ;             
         }         
        return;   
    }
    

  /* ************************************************
  **   Get all variations for product
  *************************************************** */
  function vtmin_get_variations_list($product_ID) {
        
    //sql from woocommerce/classes/class-wc-product.php
   $variations = get_posts( array(
			'post_parent' 	=> $product_ID,
			'posts_per_page'=> -1,
			'post_type' 	  => 'product_variation',
			'fields' 		    => 'ids',
			'post_status'	  => 'publish',
      'order'         => 'ASC'
	  ));
   if ($variations)  {    
      $product_variations_list = array();
      foreach ( $variations as $variation) {
        $product_variations_list [] = $variation;             
    	}
    } else  {
      $product_variations_list;
    }
    
    return ($product_variations_list);
  } 
  
  
  function vtmin_test_for_variations ($prod_ID) { 
      
     $vartest_response = 'no';
     
     /* Commented => DB access method uses more IO/CPU cycles than array processing below...
     //sql from woocommerce/classes/class-wc-product.php
     $variations = get_posts( array(
    			'post_parent' 	=> $prod_ID,
    			'posts_per_page'=> -1,
    			'post_type' 	=> 'product_variation',
    			'fields' 		=> 'ids',
    			'post_status'	=> 'publish'
    		));
     if ($variations)  {
        $vartest_response = 'yes';
     }  */
     
     // code from:  woocommerce/admin/post-types/writepanels/writepanel-product-type-variable.php
     $attributes = (array) maybe_unserialize( get_post_meta($prod_ID, '_product_attributes', true) );
     foreach ($attributes as $attribute) {
       if ($attribute['is_variation'])  {
          $vartest_response = 'yes';
          break;
       }
     }
     
     return ($vartest_response);   
  }     

  //v1.07 begin
    
   function vtmin_format_money_element($price) { 
      //from woocommerce/woocommerce-core-function.php   function woocommerce_price
    	$return          = '';
    	$num_decimals    = (int) get_option( 'woocommerce_price_num_decimals' );
    	$currency_pos    = get_option( 'woocommerce_currency_pos' );
    	$currency_symbol = get_woocommerce_currency_symbol();
    	$decimal_sep     = wp_specialchars_decode( stripslashes( get_option( 'woocommerce_price_decimal_sep' ) ), ENT_QUOTES );
    	$thousands_sep   = wp_specialchars_decode( stripslashes( get_option( 'woocommerce_price_thousand_sep' ) ), ENT_QUOTES );
    
    	$price           = apply_filters( 'raw_woocommerce_price', (double) $price );
    	$price           = number_format( $price, $num_decimals, $decimal_sep, $thousands_sep );
    
    	if ( get_option( 'woocommerce_price_trim_zeros' ) == 'yes' && $num_decimals > 0 )
    		$price = woocommerce_trim_zeros( $price );
    
    	//$return = '<span class="amount">' . sprintf( get_woocommerce_price_format(), $currency_symbol, $price ) . '</span>'; 

    $current_version =  WOOCOMMERCE_VERSION;
    if( (version_compare(strval('2'), strval($current_version), '>') == 1) ) {   //'==1' = 2nd value is lower     
      $formatted = number_format( $price, $num_decimals, stripslashes( get_option( 'woocommerce_price_decimal_sep' ) ), stripslashes( get_option( 'woocommerce_price_thousand_sep' ) ) );
      $formatted = $currency_symbol . $formatted;
    } else {
      $formatted = sprintf( get_woocommerce_price_format(), $currency_symbol, $price );
    }
          
     return $formatted;
   }
   
   //****************************
   // Gets Currency Symbol from PARENT plugin   - only used in backend UI during rules update
   //****************************   
  function vtmin_get_currency_symbol() {    
    return get_woocommerce_currency_symbol();  
  } 


  function vtmin_debug_options(){   //v1.09 updated function
    global $vtmin_setup_options;
    if ( ( isset( $vtmin_setup_options['debugging_mode_on'] )) &&
         ( $vtmin_setup_options['debugging_mode_on'] == 'yes' ) ) {  
      error_reporting(E_ALL);  
    }  else {
      error_reporting(E_ERROR | E_CORE_ERROR | E_COMPILE_ERROR);    //only allow FATAL error types
    }
     
  }

  //***** v1.0.5  end
  
  //v1.07 end

  //*************************************************************
  //v1.09.8 begin - MOVED HERE from parent-cart-validation
  //*************************************************************
  
  // from woocommerce/classes/class-wc-cart.php 
  function vtmin_woo_get_url ($pageName) {            
     global $woocommerce;
      $checkout_page_id = vtmin_woo_get_page_id($pageName);
  		if ( $checkout_page_id ) {
  			if ( is_ssl() )
  				return str_replace( 'http:', 'https:', get_permalink($checkout_page_id) );
  			else
  				return apply_filters( 'woocommerce_get_checkout_url', get_permalink($checkout_page_id) );
  		}
  }
      
  // from woocommerce/woocommerce-core-functions.php 
  function vtmin_woo_get_page_id ($pageName) { 
    $page = apply_filters('woocommerce_get_' . $pageName . '_page_id', get_option('woocommerce_' . $pageName . '_page_id'));
		return ( $page ) ? $page : -1;
  }    
 /*  =============+++++++++++++++++++++++++++++++++++++++++++++++++++++++++    */
    
 
   //v1.07 begin
  /* ************************************************
  **   Application - get current page url
  *       
  *       The code checking for 'www.' is included since
  *       some server configurations do not respond with the
  *       actual info, as to whether 'www.' is part of the 
  *       URL.  The additional code balances out the currURL,
  *       relative to the Parent Plugin's recorded URLs           
  *************************************************** */ 
 function vtmin_currPageURL() {
     global $vtmin_info;
     $currPageURL = vtmin_get_currPageURL();
     $www = 'www.';
     
     $curr_has_www = 'no';
     if (strpos($currPageURL, $www )) {
         $curr_has_www = 'yes';
     }
     
     //use checkout URL as an example of all setup URLs
     $checkout_has_www = 'no';
     if (strpos($vtmin_info['woo_checkout_url'], $www )) {
         $checkout_has_www = 'yes';
     }     
         
     switch( true ) {
        case ( ($curr_has_www == 'yes') && ($checkout_has_www == 'yes') ):
        case ( ($curr_has_www == 'no')  && ($checkout_has_www == 'no') ): 
            //all good, no action necessary
          break;
        case ( ($curr_has_www == 'no') && ($checkout_has_www == 'yes') ):
            //reconstruct the URL with 'www.' included.
            $currPageURL = vtmin_get_currPageURL($www); 
          break;
        case ( ($curr_has_www == 'yes') && ($checkout_has_www == 'no') ): 
            //all of the woo URLs have no 'www.', and curr has it, so remove the string 
            $currPageURL = str_replace($www, "", $currPageURL);
          break;
     } 
 
     return $currPageURL;
  } 
 function vtmin_get_currPageURL($www = null) {
     global $vtmin_info;
     $pageURL = 'http';
     //if ($_SERVER["HTTPS"] == "on") {$pageURL .= "s";}
     if ( isset( $_SERVER["HTTPS"] ) && strtolower( $_SERVER["HTTPS"] ) == "on" ) { $pageURL .= "s";}
     $pageURL .= "://";
     $pageURL .= $www;   //mostly null, only active rarely, 2nd time through - see above
     
     //NEVER create the URL with the port name!!!!!!!!!!!!!!
     $pageURL .= $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
     /* 
     if ($_SERVER["SERVER_PORT"] != "80") {
        $pageURL .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"];
     } else {
        $pageURL .= $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
     }
     */
     return $pageURL;
  }  
   //v1.07 end    

  //*************************************************************
  //v1.09.8 END - MOVED HERE from parent-cart-validation
  //*************************************************************
  
   //*************************
   // v1.09.91.2 new function
   //*************************
  function vtmin_auto_update_setting_html1 ( $html, $plugin_file, $plugin_data ) {
    if ( 'minimum-purchase-for-woocommerce/vt-minimum-purchase.php' 	   ===  $plugin_file ) {
        $html = __( 'Auto-updates are not available for this plugin.', 'minimum-purchase-for-woocommerce' );
    }
 
    return $html;
  }
  add_filter( 'plugin_auto_update_setting_html', 'vtmin_auto_update_setting_html1', 10, 3 );

    
   //*************************
   // v1.09.91.2 new function
   //*************************
  function vtmin_auto_update_setting_html2 ( $html, $plugin_file, $plugin_data ) {
    if ( 'minimum-purchase-pro-for-woocommerce/vt-minimum-purchase-pro.php' 	   ===  $plugin_file ) {
        $html = __( 'Auto-updates are not available for this plugin.', 'minimum-purchase-pro-for-woocommerce' );
    }
 
    return $html;
  }
  add_filter( 'plugin_auto_update_setting_html', 'vtmin_auto_update_setting_html2', 10, 3 );  
