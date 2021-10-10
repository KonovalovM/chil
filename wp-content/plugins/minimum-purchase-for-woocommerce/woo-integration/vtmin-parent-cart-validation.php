<?php
/*
VarkTech Minimum Purchase for WooCommerce
Woo-specific functions
Parent Plugin Integration
*/


class VTMIN_Parent_Cart_Validation {
	
	public function __construct(){
     global $vtmin_info, $woocommerce; //$woocommerce_checkout = $woocommerce->checkout();
     /*  =============+++++++++++++++++++++++++++++++++++++++++++++++++++++++++   
     *        Apply Minimum Amount Rules to ecommerce activity
     *                                                          
     *          WOO-Specific Checkout Logic and triggers 
     *                                               
     *  =============+++++++++++++++++++++++++++++++++++++++++++++++++++++++++   */
                                
    //  add actions for early entry into Woo's 3 shopping cart-related pages, and the "place order" button -

    //if "place order" button hit, this action catches and errors as appropriate
    add_action( 'woocommerce_before_checkout_process', array(&$this, 'vtmin_woo_check_click_to_pay') );  //v1.09.5 

    //v1.09.5  changed to be direct, wp_loaded is correct!! ...
    add_action( 'wp_loaded',                                array(&$this, 'vtmin_woo_apply_checkout_cntl'),99,1 ); //loaded passes no values, but needed for other call!!!
    
    //add_action( 'init',                                array(&$this, 'vtmin_woo_apply_checkout_cntl'),99 ); 
    //add_action( 'woocommerce_init',                                array(&$this, 'vtmin_woo_apply_checkout_cntl'),99 );
    //add_action( 'woocommerce_loaded',                                array(&$this, 'vtmin_woo_apply_checkout_cntl')); 
    
    //NEEDS WORK!!!!!!!
    // deosn't work...  add_action( 'woocommerce_cart_updated',                 array(&$this, 'vtmin_woo_apply_checkout_cntl'),99 );

     /*   Priority of 99 in the action above, to delay add_action execution. The
          priority delays us in the exec sequence until after any quantity change has
          occurred, so we pick up the correct altered state. */                                                                      
	}

     
 /*  =============+++++++++++++++++++++++++++++++++++++++++++++++++++++++++    */
  //**********************************   
  //v1.09.5 refactored
  //**********************************
  public function vtmin_woo_check_click_to_pay() { 

    global $vtmin_cart, $vtmin_cart_item, $vtmin_rules_set, $vtmin_rule, $vtmin_info, $woocommerce;
    vtmin_debug_options();  //v1.09            
     $vtmin_apply_rules = new VTMIN_Apply_Rules;   
    
    //v1.09.8 begin
    if(!isset($_SESSION)){
      session_start();
      header("Cache-Control: no-cache");
      header("Pragma: no-cache");
    }
                   
     //clears old messages first  
     if ( (isset ($_SESSION['error_message_sent'])) &&
         ($_SESSION['error_message_sent']) ) {
       $_SESSION['error_message_sent'] = false;
       wc_clear_notices();   
     }

    //v1.09.8 end
        
    //ERROR Message Path
    if ( sizeof($vtmin_cart->error_messages) > 0 ) {      
      
      //v1.08 changes begin
        switch( $vtmin_cart->error_messages_are_custom ) {  
          case 'all':
               $this->vtmin_display_custom_messages();
            break;
          case 'some':    
               $this->vtmin_display_custom_messages();
               $this->vtmin_display_standard_messages();
    
                //v1.09.5 begin 
                for($i=0; $i < sizeof($vtmin_cart->error_messages); $i++) { 
                 if ($vtmin_cart->error_messages[$i]['msg_is_custom'] != 'yes') {  //v1.08 ==>> don't show custom messages here...             
                    $message = '<div class="vtmin-error" id="line-cnt' . $vtmin_info['line_cnt'] . '"><h3 class="error-title">'  .$vtmin_info['default_msg_title'].   '</h3><p>' . $vtmin_cart->error_messages[$i]['msg_text']. '</p></div>';   //v1.09.91.2
                    wc_add_notice( $message, 'error' );
                    $_SESSION['error_message_sent'] = true;
                  }
                }
                //v1.09.5 begin
                 
                
            break;           
          default:  //'none' / no state set yet
               $this->vtmin_display_standard_messages();
              //v1.09.1 begin
                //v1.09.5 REMOVED
              /*
              $current_version =  WOOCOMMERCE_VERSION;
              if( (version_compare(strval('2.1.0'), strval($current_version), '>') == 1) ) {   //'==1' = 2nd value is lower     
                $woocommerce->add_error(  __('Minimum Purchase error found.', 'vtmin') );  //supplies an error msg and prevents payment from completing 
              } else {
              */
               //added in woo 2.1

             //   wc_add_notice( __('Minimum Purchase error found.', 'vtmin'), 'error' );   //supplies an error msg and prevents payment from completing
                // wc_add_notice( __('Minimum Purchase error found.', 'vtmin'), $notice_type = 'error' );   //supplies an error msg and prevents payment from completing  
 
    
            //v1.09.5 begin 
            for($i=0; $i < sizeof($vtmin_cart->error_messages); $i++) { 
             if ($vtmin_cart->error_messages[$i]['msg_is_custom'] != 'yes') {  //v1.08 ==>> don't show custom messages here...             
                $message = '<div class="vtmin-error" id="line-cnt' . $vtmin_info['line_cnt'] . '"><h3 class="error-title">'  .$vtmin_info['default_msg_title'].   '</h3><p>' . $vtmin_cart->error_messages[$i]['msg_text']. '</p></div>';   //v1.09.91.2
                wc_add_notice( $message, 'error' );
                $_SESSION['error_message_sent'] = true;
              }
            }
            //v1.09.5 end 
               
             // } //v1.09.5 REMOVED
              //v1.09.1  end                
            break;                    
        }

      //v1.08 changes end 
            
    } 

  return;
       
  }                                     
           
  /* ************************************************
  **   Application - Apply Rules at E-Commerce Checkout
  *************************************************** */
	public function vtmin_woo_apply_checkout_cntl(){  //v1.0.9.4  added passed value

        
      //error_log( print_r(  ' ', true ) );
      //error_log( print_r(  '001', true ) );
  
        
    //v1.09.5  begin
/*
     if (function_exists( 'get_current_screen' ))  {   //v1.09.9  
      error_log( print_r(  'get_current_screen found', true ) );           
    }
     if (is_admin() )  {   //v1.09.9  
      error_log( print_r(  'is_admin()  found', true ) );           
    }  
     if ( defined( 'DOING_CRON' ) )  {   //v1.09.9  
      error_log( print_r(  'DOING_CRON  found', true ) );           
    }  
*/            
    //v1.09.5  begin
     //if ( (function_exists( 'get_current_screen' ) ) ||    // get_current_screen ONLY exists in ADMIN!!!  //v1.09.9  REMOVED 1.09.91.2, no longer applies only to ADMIN
     if ( (strpos($_SERVER["REQUEST_URI"],'wp-admin') !== false) ||   //v1.09.91.2 this replaced is_admin
          ( defined( 'DOING_CRON' ) ) ) {   //v1.09.9  
      //error_log( print_r(  '001a RETURN', true ) );           
      return;
    }

          //error_log( print_r(  '002', true ) );  

    //v1.09.5  end
    
    //v1.09.71 begin
    if(!isset($_SESSION)){
      session_start();
      header("Cache-Control: no-cache");
      header("Pragma: no-cache");
    }
    //v1.09.71 end
    
    global $vtmin_cart, $vtmin_cart_item, $vtmin_rules_set, $vtmin_rule, $vtmin_info, $woocommerce, $vtmin_setup_options;
    vtmin_debug_options();  //v1.09    
    //input and output to the apply_rules routine in the global variables.
    //    results are put into $vtmin_cart
 
 
      //error_log( print_r(  '003', true ) ); 
 
    //v1.09.83  begin  - if recoded 
    if ( (isset($woocommerce) ) &&
         (isset($woocommerce->cart)) && //v1.09.91.1
         (sizeof($woocommerce->cart->get_cart())>0) &&
         (isset( $vtmin_setup_options['show_errors_on_more_pages'] )) &&
         ($vtmin_setup_options['show_errors_on_more_pages'] == 'all') ) { 
      $show_messages_on_all_woo_pages = true;
  
      //error_log( print_r(  '003a', true ) );     
    } else {
      //error_log( print_r(  'currPageURL= ' .$vtmin_info['currPageURL'], true ) );
      //error_log( print_r(  'woo_cart_url= ' .$vtmin_info['woo_cart_url'], true ) );
      //error_log( print_r(  'woo_checkout_url= ' .$vtmin_info['woo_checkout_url'], true ) );
      $currPageURL      = $vtmin_info['currPageURL'];
      $woo_cart_url     = $vtmin_info['woo_cart_url'];
      $woo_checkout_url = $vtmin_info['woo_checkout_url'];
      
      // if an ITEM HAS BEEN REMOVED, url is apemnded to (&...) , can't look for equality - look for a substring
      //     (if CUSTOM MESSAGE not used, JS message does NOT come across in the situation where all was good, and then an item is removed)
      if ( (strpos($currPageURL,$woo_cart_url )     !== false) ||  //BOOLEAN == true...
           (strpos($currPageURL,$woo_checkout_url ) !== false) ) {  //BOOLEAN == true...
      //v1.09.5  end     
       $show_messages = true; 
  
      //error_log( print_r(  '003b', true ) );          
       
      } else {  
      
      //error_log( print_r(  '003c RETURN', true ) );     
        return;
      } 
      //v1.0.9.4 end
    }
    //v1.09.83  end

      //error_log( print_r(  '004', true ) );  
        
     //v1.09.8 begin  clears old messages first  
     // wc_clear_notices DOES NOT WORK when a product is deleted!!!!!!! 
     if ( (isset ($_SESSION['error_message_sent'])) &&
         ($_SESSION['error_message_sent']) ) {
       $_SESSION['error_message_sent'] = false;
       wc_clear_notices();   
     }
     //v1.09.8 end

        
      //error_log( print_r(  ' ', true ) );
      //error_log( print_r(  'above APPLY_RULES in parent-cart-validation', true ) );
         
     $vtmin_apply_rules = new VTMIN_Apply_Rules;   
    
    //ERROR Message Path
    if ( sizeof($vtmin_cart->error_messages) > 0 ) {      
      
      //v1.08 changes begin
        switch( $vtmin_cart->error_messages_are_custom ) {  
          case 'all':
               $this->vtmin_display_custom_messages();
               
               //$_SESSION['error_message_sent'] = true; //v1.09.71  removed v1.09.8
               
            break;
          case 'some':    
               $this->vtmin_display_custom_messages();
               $this->vtmin_display_standard_messages();
               
               //$_SESSION['error_message_sent'] = true; //v1.09.71  removed v1.09.8
               
            break;           
          default:  //'none' / no state set yet
               $this->vtmin_display_standard_messages();
              //v1.09.1 begin
              //v1.09.5 REMOVED
              /*
              $current_version =  WOOCOMMERCE_VERSION;
              if( (version_compare(strval('2.1.0'), strval($current_version), '>') == 1) ) {   //'==1' = 2nd value is lower     
                $woocommerce->add_error(  __('Minimum Purchase error found.', 'vtmin') );  //supplies an error msg and prevents payment from completing 
              } else {
              */
               //added in woo 2.1
               
              //  wc_add_notice( __('Minimum Purchase error found.', 'vtmin'), 'error' );   //supplies an error msg and prevents payment from completing
                // wc_add_notice( __('Minimum Purchase error found.', 'vtmin'), $notice_type = 'error' );   //supplies an error msg and prevents payment from completing  
                
              // } //v1.09.5 REMOVED
              //v1.09.1  end                
            break;                    
        }

      //v1.08 changes end 
     
            
    } 
    /* moved above v1.09.8
    else { //v1.09.71 begin
      // CUSTOM messages can hang about after the issue has been cleared, - this takes care of it.
       if ( (isset ($_SESSION['error_message_sent'])) &&
           ($_SESSION['error_message_sent']) ) {
        $_SESSION['error_message_sent'] = false;
        wc_clear_notices();   
       }
    }
    //v1.09.71 end
    */
  } 


  /* ************************************************
  **   v1.08 New Function
  *************************************************** */
  public function vtmin_display_standard_messages() {
    global $vtmin_cart, $vtmin_cart_item, $vtmin_rules_set, $vtmin_rule, $vtmin_info, $woocommerce;
    //insert error messages into checkout page
    add_action( "wp_enqueue_scripts", array($this, 'vtmin_enqueue_error_msg_css') );
    add_action('wp_head', array(&$this, 'vtmin_display_rule_error_msg_at_checkout') );  //JS to insert error msgs 
        
    $vtmin_cart->error_messages_processed = 'yes';
  } 

  /* ************************************************
  **   v1.08 New Function
  *************************************************** */
  public function vtmin_display_custom_messages() {
    global $vtmin_cart, $vtmin_cart_item, $vtmin_rules_set, $vtmin_rule, $vtmin_info, $woocommerce;
            
	//v1.09.8 begin
	if(!isset($_SESSION)){
	  session_start();
	  header("Cache-Control: no-cache");
	  header("Pragma: no-cache");
	}
	//v1.09.8 end
                
    for($i=0; $i < sizeof($vtmin_cart->error_messages); $i++) { 
       if ($vtmin_cart->error_messages[$i]['msg_is_custom'] == 'yes') {  //v1.08 ==>> show custom messages here...
          //v1.09.1 begin
          $current_version =  WOOCOMMERCE_VERSION;
          if( (version_compare(strval('2.1.0'), strval($current_version), '>') == 1) ) {   //'==1' = 2nd value is lower     
            $woocommerce->add_error(  $vtmin_cart->error_messages[$i]['msg_text'] );  //supplies an error msg and prevents payment from completing 
          } else {
           //added in woo 2.1
            wc_add_notice( stripslashes($vtmin_cart->error_messages[$i]['msg_text']), $notice_type = 'error' );   //supplies an error msg and prevents payment from completing             
            $_SESSION['error_message_sent'] = true;  //v1.09.8 end                       
          } 
          //v1.09.1  end          
       } //end if
    }  //end 'for' loop    
  }   
  
  
  /* ************************************************
  **   Application - On Error Display Message on E-Commerce Checkout Screen  
  *  //v1.09.5 REFACTORED  
  *************************************************** */ 
  public function vtmin_display_rule_error_msg_at_checkout(){
    global $vtmin_info, $vtmin_cart, $vtmin_setup_options;
             
        //v1.09.8 begin
        if(!isset($_SESSION)){
          session_start();
          header("Cache-Control: no-cache");
          header("Pragma: no-cache");
        }
        //v1.09.8 end  
          
        for($i=0; $i < sizeof($vtmin_cart->error_messages); $i++) { 
         if ($vtmin_cart->error_messages[$i]['msg_is_custom'] != 'yes') {  //v1.08 ==>> don't show custom messages here...             
            $message = '<div class="vtmin-error" id="line-cnt' . $vtmin_info['line_cnt'] . '"><h3 class="error-title">'  .$vtmin_info['default_msg_title'].   '</h3><p>' . $vtmin_cart->error_messages[$i]['msg_text']. '</p></div>';   //v1.09.91.2
            wc_add_notice( $message, 'error' );
            $_SESSION['error_message_sent'] = true;  //v1.09.8                       
          }
        }

          
     /* ***********************************
        CUSTOM ERROR MSG CSS AT CHECKOUT
        *********************************** */
     if ($vtmin_setup_options[custom_error_msg_css_at_checkout] > ' ' )  {
        echo '<style type="text/css">';
        echo $vtmin_setup_options[custom_error_msg_css_at_checkout];
        echo '</style>';
     }
     
     /*
      Turn off the messages processed switch.  As this function is only executed out
      of wp_head, the switch is only cleared when the next screenful is sent.
     */
     $vtmin_cart->error_messages_processed = 'no';   
 } 
 
   
  /* ************************************************
  **   Application - get current page url
  *************************************************** */ 
 /*
 public  function vtmin_currPageURL() {
     $pageURL = 'http';
     if ($_SERVER["HTTPS"] == "on") {$pageURL .= "s";}
        $pageURL .= "://";
     if ($_SERVER["SERVER_PORT"] != "80") {
        $pageURL .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"];
     } else {
        $pageURL .= $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
     }
     return $pageURL;
  } 
  */
  

  /* ************************************************
  **   Application - On Error enqueue error style
  *************************************************** */
  public function vtmin_enqueue_error_msg_css() {
    wp_register_style( 'vtmin-error-style', VTMIN_URL.'/core/css/vtmin-error-style.css' );  
    wp_enqueue_style('vtmin-error-style');
  } 

 
} //end class
$vtmin_parent_cart_validation = new VTMIN_Parent_Cart_Validation;
