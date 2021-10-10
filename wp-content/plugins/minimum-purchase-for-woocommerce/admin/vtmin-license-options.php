<?php
/*
 *  ALL LICENSING FUNCTIONS ARE PRO-ONLY
 *  AND DO NOT RUN WEHN ONLY THE FREE VERSION
 *  HOSTED AT WORDPRESS.ORG IS INSTALLED
 *  
 *  Installation and activation of tHE PURCHASABLE PRO VERSION ACTIVATES ALL LICENSING CODE 
 *  
 *  New File with version 1.09.90    

*/


//********************************************
// also look for $new_combined_options['url']  when testing staging.
//********************************************

  //define( 'VTMIN_STORE_URL', 'https://stage.varktech.com/' ); // you should use your own CONSTANT name, and be sure to replace it throughout this file
  define( 'VTMIN_STORE_URL', 'https://www.varktech.com/' );  // you should use your own CONSTANT name, and be sure to replace it throughout this file
 
 
//***********************************************************
//CHANGE TO specific receiving file???
// http://code.tutsplus.com/tutorials/a-look-at-the-wordpress-http-api-a-practical-example-of-wp_remote_post--wp-32425
//HOST receiving file: http://code.tutsplus.com/tutorials/a-look-at-the-wordpress-http-api-saving-data-from-wp_remote_post--wp-32505
//***********************************************************
//define( 'VTMIN_STORE_URL', 'http://http://www.varktech.com/wp-remote-receiver.php' ); // you should use your own CONSTANT name, and be sure to replace it throughout this file

// the name of your product. This should match the download name in VT exactly
define( 'VTMIN_ITEM_NAME', 'Minimum Purchase Pro for WooCommerce Plugin' );
define( 'VTMIN_ITEM_ID', '200' ); // ITEM ID from home STORE -> POST ID of save product
define( 'VTMIN_PRO_SLUG', 'minimum-purchase-pro-for-woocommerce' );  
//define( 'VTMIN_PRO_PLUGIN_ADDRESS', 'minimum-purchase-pro-for-woocommerce/vt-minimum-purchase-pro.php' ); 
define( 'VTMIN_PRO_LAST_PRELICENSE_VERSION', '1.09.81' );

   
//Set up and run license screen 
class VTMIN_License_Options_screen { 
	
	public function __construct(){ 
  
    add_action( 'admin_init',            array(&$this, 'vtmin_initialize_options' ) );
    add_action( 'admin_menu',            array(&$this, 'vtmin_add_admin_menu_setup_items' ), 99  ); //99 puts it at the bottom of the list
    add_action( "admin_enqueue_scripts", array(&$this, 'vtmin_enqueue_setup_scripts') );
  } 

function vtmin_add_admin_menu_setup_items() {
 // add items to the Pricing Deals custom post type menu structure
  global $vtmin_license_options;
  
  $settingsLocation = 'edit.php?post_type=vtmin-rule';
  
   
	add_submenu_page(
		$settingsLocation,	// The ID of the top-level menu page to which this submenu item belongs
		__( 'Register Pro License', 'vtmin' ), // The value used to populate the browser's title bar when the menu page is active                           
		__( 'Register Pro License', 'vtmin' ),					// The label of this submenu item displayed in the menu
		'administrator',					// What roles are able to access this submenu item
		'vtmin_license_options_page',	// The slug used to represent this submenu item
		array( &$this, 'vtmin_license_options_cntl' ) 				// The callback function used to render the options for this submenu item
	);
  /* 
	add_submenu_page(
		$settingsLocation,	// The ID of the top-level menu page to which this submenu item belongs
		__( 'System Info', 'vtmin' ), // The value used to populate the browser's title bar when the menu page is active                           
		__( 'System Info', 'vtmin' ),					// The label of this submenu item displayed in the menu
		'administrator',					// What roles are able to access this submenu item
		'vtmin_license_options_page',	// The slug used to represent this submenu item
		array( &$this, 'vtmin_system_info_cntl' ) 				// The callback function used to render the options for this submenu item
	); 
  */
} 

/**
 * Renders a simple page to display for the menu item added above.
 */
function vtmin_license_options_cntl() {
  
  
    //v1.1.6.3 begin
    // From a URL anywhere in the site
    // looks for 'action=force_plugin_updates_check', 
    //  which executes a function which clears the TS and transfers to  'plugins.php' (v1.1.8.2 used to be'update-core.php')
    $action     = isset( $_GET['action'] ) ? strtolower( $_GET['action'] )  : false;
    //error_log( print_r(  'vtmin_license_options_cntl, action= ' .$action, true ) );
    if ($action == 'force_plugin_updates_check') {
      vtmin_force_plugin_updates_check();
    }  
    //v1.1.6.3 end
  
  
  //add help tab to this screen...
  //$vtmin_backbone->vtmin_add_help_tab ();
    $content = '<br><a  href="' . VTMIN_DOCUMENTATION_PATH . '"  title="Access Plugin Documentation">Access Plugin Documentation</a>';
    $screen = get_current_screen();
    $screen->add_help_tab( array( 
       'id' => 'vtmin-help-options',            //unique id for the tab
       'title' => 'Pricing Deals Settings Help',      //unique visible title for the tab
       'content' => $content  //actual help text
      ) );

   global $vtmin_license_options; 
    
   if( !$vtmin_license_options )  {
     $vtmin_license_options = get_option( 'vtmin_license_options' );
   }
   

   //***********************************************
   //***********************************************
   //IF SUSPENDED 
   //***********************************************
/*
    NOW DONE IN MAIN PLUGIN FILE   
   if ($vtmin_license_options['state'] == 'suspended-by-vendor') { 
      vtmin_deactivate_pro_plugin();
      add_action( 'admin_notices', 'vtmin_license_error_notice' );
   }
 */   
   //***********************************************
   //***********************************************   
   
    
  ?>
   <style type="text/css">
      #system-buttons {margin-top:0;}
       #system-info-textarea {
          width: 800px;
          height: 400px;
          font-family: Menlo,Monaco,monospace;
          background: 0 0;
          white-space: pre;
          overflow: auto;
          display: block;
      }
      .green {
        color: green;
        font-size: 18px;
      }
      .red {
        color: red;
        font-size: 14px;
        background-color: rgb(255, 235, 232) !important;
        margin: 5px 0 15px;
        border: 1px solid red;
        border-left: 4px solid red;
        box-shadow: 0 1px 1px 0 rgba(0,0,0,.1);
        padding: 10px 12px 20px 12px;
      } 
      .yellow {
          color: black;
          font-size: 14px;
          margin: 5px 0 15px;
          border: 4px solid yellow;
          border-left: 4px solid yellow;
          box-shadow: 0 1px 1px 0 rgba(0,0,0,.1);
          padding: 10px 12px 20px 12px;
          background-color: RGB(255, 255, 180) !important;
      }
      .smallGreyText {
          color: gray;
          font-size: 12px;
      }              
       .sub-label {
        color: grey;
        font-size: 11px !important;
      }  
       .grey {
        color: grey;
        font-size: 11px !important;
      } 
       .black {
        color: black;
        font-size: 11px !important;
      }
      .hidden-button {
        color:rgb(241, 241, 241) !important;
        margin-left: 50px;
      }
      .hidden-button:hover {
        color:white;
      }
      #activate-button, #deactivate-button {
        font-size:18px;
        background-color:white;
      }      
      #reset-button .system-buttons-h4 {
        color:#F1F1F1; /*matches background*/
      }
      #reset-button .system-buttons-h4:hover {
        color:#gray; 
      }      
      #reset-button .nuke_buttons, 
      #reset-button input{
        color:#F1F1F1; /*matches background*/
        box-sizing: none;
        border: none;
        float: right;
        margin-right:200px;        
      }  
      #reset-button .nuke_buttons:hover,
      #reset-button input :hover {
        color:red;
        box-sizing: border-box; 
        border: 1px solid black;
      }
      #show-info-button {margin-left:20px; padding:10px; text-decoration::none; border:1px solid gray; cursor: pointer; cursor: hand;  font-weight:bold; font-size:16px;}
      #show-info-button:hover {color:red;}
      #show-licensing-info {display:none} 
      #license-status-msg a {padding:5px; border:1px solid gray;}  
      
      /*Hide/show tips*/
      .title-anchor, .example-details, .pricing-deal-example, #bufferP {
          clear: left;
      }
      .example-details {
        margin-left: 5%;
        line-height: 22px;
        border: 1px solid green;
        padding: 0px 20px 20px 20px;
        background-color: white;
        border-radius: 5px; 
        margin-bottom: 15px;        
      }
      .pricing-deal-example {
        margin-left: 2%;
      }
      .title-anchor-plus, .title-anchor-minus, .example-details {
          float: left;          
      }
      .title-anchor-plus, .title-anchor-minus {
          text-decoration: none !important;
          font-weight: bold;
      }
      .title-anchor-plus:hover, .title-anchor-minus:hover {
          text-decoration: underline !important;
      }
      .title-text, .example-details {
        font-size: 16px;
      }
      .title-anchor-minus {
          display: none;
      } 
      .plus-icon, .minus-icon {
          background-color: #82C201;
          border-radius: 3px 3px 3px 3px;
          color: #FFFFFF;
          font-size: 14px;
          font-weight: bold;
          margin-left: 10px;
          margin-top: 3px;
          opacity: 0.6;
          padding: 0 5px;
          text-decoration: none;
      }
      .minus-icon {
          font-size: 18px;
      } 
      .hideMe {display:none;}                
  </style> 
  
  <script type="text/javascript">
     jQuery(document).ready(function($) {
            
         
            //****************************
            // Show Discount Where
            //****************************  
            
                          //first time in
                          screen_init_Control();
                          
                          //on CHANGE
                          $("#radio-prod").click(function(){ //use 'change' rather than 'click' 
                               $(".production_url_for_test").hide("slow");                           
                           });     
                          $("#radio-demo").click(function(){ //use 'change' rather than 'click' 
                               $(".production_url_for_test").hide("slow");                           
                           });     
                          $("#radio-test").click(function(){ //use 'change' rather than 'click' 
                               $(".production_url_for_test").show("slow");                           
                           }); 
                           
                          $("#show-info-button").click(function(){
                              $("#show-licensing-info").show("slow");                             
                          });                              
                                                        
                                   
                          function screen_init_Control() {                     
                            
                            if($('#radio-prod').is(':checked')){ //use 'change' rather than 'click' 
                                 $(".production_url_for_test").hide();                           
                             };     
                            if($('#radio-demo').is(':checked')){ //use 'change' rather than 'click' 
                                 $(".production_url_for_test").hide();                           
                             };     
                            if($('#radio-test').is(':checked')){ //use 'change' rather than 'click' 
                                 $(".production_url_for_test").show("slow");                           
                             };
                             
                            $("#show-licensing-info").hide();  
                                                       
                          }; 
                                      
           	 $("#title-anchor-plus-1").click(function(){ 
                    $("#title-anchor-plus-1").hide();
                    $("#title-anchor-minus-1").show();
                    $("#example-details-1").show("slow");    
             });        
           	 $("#title-anchor-minus-1").click(function(){ 
                    $("#title-anchor-minus-1").hide();
                    $("#title-anchor-plus-1").show();
                    $("#example-details-1").hide("slow");    
             }); 
           	 $("#title-anchor-plus-2").click(function(){ 
                    $("#title-anchor-plus-2").hide();
                    $("#title-anchor-minus-2").show();
                    $("#example-details-2").show("slow");    
             });        
           	 $("#title-anchor-minus-2").click(function(){ 
                    $("#title-anchor-minus-2").hide();
                    $("#title-anchor-plus-2").show();
                    $("#example-details-2").hide("slow");    
             });                                              
                        
      }); 
  
  
  </script>
  
  
  
	<div class="wrap">
		<div id="icon-themes" class="icon32"></div>
 
       <?php 
          //v1.1.6.7 shifted message here - admin_notices can be blocked by some plugins, now shown DIRECTLY!!
          // works in conjunction with new PRO plugin update message on the plugins page, using after-nag logic 

          if ( ($vtmin_license_options['pro_plugin_version_status'] == 'Pro Version Error') &&
               ($vtmin_license_options['status'] == 'valid') && //v1.1.8.2 
               ($vtmin_license_options['state']  == 'active') ) { //v1.1.8.2 
            //FULL message for rego screen
            $message  =  '<strong>' . __('Pro Plugin  ** Update Required ** ' , 'vtmin') .'</strong>' ;
            $message .=  "<span style='color:grey !important;'><em>&nbsp;&nbsp;&nbsp; (pro plugin will not discount until updated)</em></span>" ;
            
            $message .=  '<br><br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;' . __('Your Pro Version = ' , 'vtmin') .$vtmin_license_options['pro_version'] .'&nbsp;&nbsp;<strong>' . __(' Required Pro Version = ' , 'vtmin') .VTMIN_MINIMUM_PRO_VERSION .'</strong>'; 
            
           // $message .=  '<br><br><em>'  .VTMIN_PRO_PLUGIN_NAME . '&nbsp;&nbsp;' . ' will not give discounts until this is resolved.' .'</em>' ;  
            //v1.1.8.2 begin
            if ($vtmin_license_options['prod_or_test'] == 'test') { 
              $message .=  '<br><br>&nbsp;&nbsp;&nbsp;&nbsp;  &nbsp;&nbsp;<em>' . __('In a TEST environment, only MANUAL updates are available:'  , 'vtmin') .'</em>'; //v1.1.8.2                               
            } else {
              $message .=  '<br><br>&nbsp;&nbsp; 1. &nbsp;&nbsp;<em>' . __('For Plugin update, CLICK HERE: '  , 'vtmin') .'</em>'; //v1.1.8.2
              $message .=  '&nbsp;&nbsp;<a style="text-decoration: underline;font-size:18px;" href="'.VTMIN_ADMIN_URL.'edit.php?post_type=vtmin-rule&page=vtmin_license_options_page&action=force_plugin_updates_check">' . __('Check for Plugin Updates', 'vtmin'). '</a>'; //v1.1.8.2 - removed home_url, also bounces to license page, which then sets the transient and goes on to the plugins page.
              $message .=  '<br><br>&nbsp;&nbsp; 2. &nbsp;&nbsp;<em>' . __('If no Plugin update is available, please update manually:'  , 'vtmin') .'</em>'; //v1.1.8.2
            }
            //v1.1.8.2 end
            // $message .=  '&nbsp;&nbsp;<a style="text-decoration: underline;font-size:18px;" href="'.VTMIN_ADMIN_URL.'edit.php?post_type=vtmin-rule&page=vtmin_license_options_page&action=force_plugin_updates_check">' . __('Check for Plugin Updates', 'vtmin'). '</a>'; //v1.1.8.2  bounces to license page, which then sets the transient and goes on to the plugins page.
                   
          //  $message .=  '<br><br>&nbsp;&nbsp; 1. &nbsp;&nbsp;<em>' . __('IF your PRO plugin is currently registered, '  , 'vtmin').'</em>';
          //  $message .=  '<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<strong>' . __('You should see an update prompt on your '  , 'vtmin');
          //  $message .=     '<a class="ab-item" href="'.VTMIN_ADMIN_URL.'plugins.php?plugin_status=all&paged=1&s">' . __('Plugins Page', 'vtmin') . '</a>';
          //  $message .=     __(' for a PRO Plugin automated update'  , 'vtmin') .'</strong>';           
            
         //   $message .=  '<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&bull;&nbsp;&nbsp;' . __('If no Pro Plugin update nag, Click Here: '  , 'vtmin');
         //   $message .=  '<a href="'.VTMIN_ADMIN_URL.'edit.php?post_type=vtmin-rule&page=vtmin_license_options_page&action=force_plugin_updates_check">' . __('Check for Plugin Updates', 'vtmin'). '</a>';
           // $message .=  '<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&bull;&nbsp;&nbsp;' . __('Then return to your '  , 'vtmin');
           // $message .=     '<a class="ab-item" href="'.VTMIN_ADMIN_URL.'plugins.php?plugin_status=all&paged=1&s">' . __('Plugins Page', 'vtmin') . '</a>';
           // $message .=     __(' to apply the PRO Plugin automated update'  , 'vtmin') .'</strong>';
            
                 
            //$message .=  '<br><br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&bull;&nbsp;&nbsp;' . __('Use the login credentials emailed to you at purchase time, and'  , 'vtmin');
            $message .=  '<br><br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&bull;&nbsp;&nbsp;' . __('Go to Varktech ', 'vtmin');
            $message .=  '<a target="_blank" href="https://www.varktech.com/your-account/your-login/">Your Login</a>';
            $message .=   __(', and log into your account.', 'vtmin');
            
            $message .=  '<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;' . __('(your Login Username = your purchasing email address)', 'vtmin');
            
            $message .=  '<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&bull;&nbsp;&nbsp;' . __('After logging in, return to the ', 'vtmin'); 
            $message .=  '<a target="_blank" href="https://www.varktech.com/">Varktech.com</a>';
            $message .=   __(' home page.', 'vtmin');
            $message .=  '<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&bull;&nbsp;&nbsp;' . __('In the Varktech.com navigation bar, hover over ', 'vtmin');
            $message .=  '<a target="_blank" href="https://www.varktech.com/your-account/your-login/">Your Login</a>';
            $message .=   __(' and click on ', 'vtmin');               
            $message .=  '<a target="_blank" href="https://www.varktech.com/checkout/purchase-history/">Purchase History</a>';
            $message .=  '<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&bull;&nbsp;&nbsp;' . __('Download the current zip file version of the Pro Plugin from the Purchase History page. ', 'vtmin');
            $message .=  '<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&bull;&nbsp;&nbsp;' . __('In your website Dashboard Plugins Page, delete the old version of the Pro Plugin (no settings will be lost). ', 'vtmin');
            $message .=  '<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&bull;&nbsp;&nbsp;<strong>' . __('<em>Use the new zip file downloaded from Varktech, and Install and Activate</em> the Pro Plugin on your Dashboard Plugins Page. ', 'vtmin') .'</strong>';
            $message .=  '</strong>';
            $message .=  "<span style='color:grey !important;'><br><br><em>&nbsp;&nbsp;&nbsp; (This message displays when the Pro version is installed, regardless of whether it's active)</em></span>" ;
            $admin_notices = '<div id="message" class="error fade" style="background-color: #FFEBE8 !important;"><p>' . $message . ' </p></div>';            
            echo $admin_notices;
          }
   
      ?> 
 
    
		<h2>
      <?php 

          esc_attr_e('Pricing Deals Pro License Registration', 'vtmin'); 
   
      ?>    
    </h2>
    
    <?php 
    
       if ($vtmin_license_options['prod_or_test'] == 'demo') {
         $item_name = VTMIN_ITEM_NAME . ' Demo';
       } else {
         $item_name = VTMIN_ITEM_NAME;
       }
    
      if ($vtmin_license_options['expires'] > ' ') {
        if ($vtmin_license_options['expires'] == 'lifetime') {
          if ($vtmin_license_options['prod_or_test'] == 'demo') {
            ?> <p id="license-expiry-msg"><?php echo $item_name; ?> - 3-Day License </p> <?php
          } else {
            ?> <p id="license-expiry-msg"><?php echo $item_name; ?> - Lifetime License </p> <?php 
          }             
        } else {
          ?> <h2 id="license-expiry-msg" style="font-size: 1.5em;"><em><?php echo $item_name; ?> - License Expires::  <?php echo $vtmin_license_options['expires']; ?></em> </h2> <?php
        }                
      } 
      /* else {
        if ( ($vtmin_license_options['status'] == 'valid') &&
             ($vtmin_license_options['state']  == 'active') ) {
            //Lifetime license message from above 
        }       
      } */
    ?>  
    <?php        
		       
     settings_errors(); //shows errors entered with "add_settings_error"   
     //valid status ONLY allows active or deactivated

      vtmin_maybe_license_state_message();                 
 
    /*if ( isset( $_GET['settings-updated'] ) ) {
         echo "<div class='updated'><p>Theme settings updated successfully.</p></div>";
    } */
    ?>
    
 
    
		
		<form method="post" action="options.php">
			<?php
          //WP functions to execute the registered settings!
					settings_fields( 'vtmin_license_options_group' );     //activates the field settings setup below
					do_settings_sections( 'vtmin_license_options_page' );   //activates the section settings setup below 
          
          
       /*
       3 buttons
          Activate
          Deactivate

          Save Licensing Report as TXT file  ==>> straight to text file...
       */     
       

      // **********************************************************
      // STATUS: valid / invalid / unregistered (default)
      // STATE:  active (only if valid) / deactivated (only if valid) / pending (error but not yet suspended) / suspended-by-vendor / unregistered (default)
      // **********************************************************       
     
          
			?>	

       <p id="system-buttons">

         <?php //v1.1.6 begin   if refactored 
              //valid status ONLY allows active or deactivated
            //VTMIN_PRO_VERSION only exists if PRO version is installed and active
          if (defined('VTMIN_PRO_VERSION')) { //if PRO is ACTIVE 
            if ($vtmin_license_options['status'] =='valid') {   
                switch ( $vtmin_license_options['state'] ) { 
                  case 'active' : 
                      $this->vtmin_show_deactivate_button();
                    break;
                  case 'deactivated' : 
                      $this->vtmin_show_activate_button();
                    break;                    

                  default:                   
                      //can't be any other state!!
                    break;
                }
            } else { //'invalid' OR 'unregistered' path ==>> can't have a state of active or deactivated
                switch ( $vtmin_license_options['state'] ) {  
                  case 'unregistered' : 
                      $this->vtmin_show_activate_button();
                    break;
                  case 'pending' :
                      switch ( $vtmin_license_options['last_action'] ) {  
                          case 'activate_license' :
                          case 'check_license' :
                          case ' ' :
                          case '' :
                              $this->vtmin_show_activate_button();
                             break;
                          case 'deactivate_license' :
                              $this->vtmin_show_deactivate_button();
                             break;                             
                      }
                    break; 
                    
                 case 'suspended-by-vendor' :
                      //carry on, no activate/deactivate showing
                    break;                   
                                   

                  default:                   
                      //show suspended-by-vendor message
                    break;
                }            
            
            }
          } else {  //PRO not currently ACTIVE
            if ($vtmin_license_options['state'] != 'suspended-by-vendor') { //suspended is handled elsewhere...
              $pro_plugin_is_installed = vtmin_check_pro_plugin_installed();
              if ($pro_plugin_is_installed) {
               // $url = bloginfo('url'); v1.1.6 does crazy stuff in wp-admin!!
        ?>
               <br><br><br>
               <h3 class="red">
                    <strong> <?php 
                    _e(' - Activate the  &nbsp;&nbsp;<em>', 'vtmin');
                    echo $item_name; 
                    _e('</em> &nbsp;&nbsp;  on the &nbsp;&nbsp; 
                    <a href="'.VTMIN_ADMIN_URL.'plugins.php">Plugins page</a> &nbsp;&nbsp; 
                    - to show a Registration Button here.', 'vtmin'); // v1.1.8.2  removed home_url ?> </strong>
                </h3> 
        <?php 
              } else {  //PRO not currently installed
        ?>
               <br><br><br>
               <h3 class="yellow">
                    <?php 
                    _e(' - You have installed and activated the FREE version of the plugin only.  <br><br>Please &nbsp;<strong>also</strong>&nbsp; Install and Activate the  &nbsp;&nbsp;<em>', 'vtmin');  //v1.1.7.2 wording changed
                    echo $item_name;  
                    _e('</em> &nbsp;&nbsp; to show a Registration Button here.', 'vtmin'); ?>
                </h3> 
        <?php               
              }
            } 
          }  
          //v1.1.6 end
          
          //v1.1.7.2 begin
          // make clear button conditional - makes 'deactivate' only button after activation!!
          if ( $vtmin_license_options['state'] != 'active' ) {   
            $this->vtmin_show_clear_button();
          }
          //v1.1.7.2 end
        ?> 

		</form>
    
    
       
     <?php //v1.1.8.2  re-coded begin  ?>
    
          
      <br><br>
        <div class="pricing-deal-example group" id="pricing-deal-example-2">                     
          <p class="title">                                                                      
            <a class="title-anchor title-anchor-plus"  id="title-anchor-plus-2"   href="javascript:void(0);"> 
              <span class="title-text">How to Transfer PROD or TEST registration from one site to another
              </span>
              <span class="plus-icon">+
              </span> </a>            
            <a class="title-anchor title-anchor-minus" id="title-anchor-minus-2"  href="javascript:void(0);"> 
              <span class="title-text">How to Transfer PROD or TEST  registration from one site to another
              </span>
              <span class="minus-icon">-
              </span> </a>          
          </p>               
          <span class="example-details  hideMe" id="example-details-2">            
            <br>&nbsp;&nbsp; 1. *Deactivate* the license in the First &nbsp;(currently active)&nbsp; Site &nbsp;&nbsp;&nbsp; <em>(use the DEACTIVATE button)</em>
        <br><br>&nbsp;&nbsp; 2. *Activate* the license in the Second Site
          </span>        
        </div> 
         
         <div class="pricing-deal-example group" id="pricing-deal-example-1">                     
          <p class="title">                                                                      
            <a class="title-anchor title-anchor-plus"  id="title-anchor-plus-1"   href="javascript:void(0);"> 
              <span class="title-text">Test Site Registration OPTIONS
              </span>
              <span class="plus-icon">+
              </span> </a>            
            <a class="title-anchor title-anchor-minus" id="title-anchor-minus-1"  href="javascript:void(0);"> 
              <span class="title-text">Test Site Registration OPTIONS
              </span>
              <span class="minus-icon">-
              </span> </a>          
          </p>               
          <span class="example-details  hideMe" id="example-details-1">            
              <h2>   Test Site Naming Requirements</h2> 
              1. <b>Use the 'test' selection option above</b>
             <br>&nbsp;&nbsp;&nbsp;&nbsp; Your test site's Wordpress Home URL setting must contain ONE of the following strings (ignore the "|"):
               <br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;   development  |  testing  |  demonstration  |  staging   |    
               <br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;   test.   |  .test   |  test-   |  -test   |  /test   |  
               <br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;   stage.  |  .stage  |  stage-  |  -stage  |  /stage  |  
               <br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;   dev.    |  .dev    |  dev-    |  -dev    |  /dev    |   
               <br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;   demo.   |  .demo   |  demo-   |  -demo   |  /demo   |   
               <br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;   beta.   |  .beta   |  beta-   |  -beta   |  /beta   |  
               <br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;   stg.    |  .stg    |  stg-    |  -stg    |  /stg    | 
          </span>        
        </div>  
          <?php /* WHOLE LIST:
                development  |  testing  |  demonstration  |  staging   |    
               test.   |  .test   |  test-   |  -test   |  /test   |  
               stage.  |  .stage  |  stage-  |  -stage  |  /stage  |  
               dev.    |  .dev    |  dev-    |  -dev    |  /dev    |   
               demo.   |  .demo   |  demo-   |  -demo   |  /demo   |   
               beta.   |  .beta   |  beta-   |  -beta   |  /beta   |  
               stg.    |  .stg    |  stg-    |  -stg    |  /stg    | 
                */
           ?>        
     <?php //v1.1.8.2 end ?>    
    
    
              
        <p id="bufferP">&nbsp;</p>

        <h3 class="title"><?php esc_attr_e('System Information', 'vtmin'); ?></h3>
      
        <br><br><br>
        
        <h4 class="system-buttons-h4"><?php esc_attr_e('Show Licensing Info', 'vtmin'); ?></h4>
        <br>
        <a id="show-info-button" href="javascript:void(0);" >
        <span> <?php esc_attr_e('Show Licensing Info', 'vtmin'); ?> </span></a>
        <br><br>
  
        <div id="show-licensing-info">
          <p>To copy the system info, click below then press Ctrl + C &nbsp;&nbsp; (or Cmd + C for a Mac).</p>
          <?php
          	
   
            if ($vtmin_license_options['prod_or_test'] == 'demo') {
              $item_name = VTMIN_ITEM_NAME . ' Demo';
            } else {
              $item_name = VTMIN_ITEM_NAME;
            }

            if ($vtmin_license_options['updater_action_reduced_frequency']) {
              $updater_action_reduced_frequency = 'yes';
            } else {
              $updater_action_reduced_frequency = 'no';
            }
                   
           ?>  
           <textarea readonly="readonly" onclick="this.focus(); this.select()" id="system-info-textarea" 
           name="edd-sysinfo" title="To copy the system info, click below then press Ctrl + C (PC) or Cmd + C (Mac)."><?php echo vtmin_return_license_info(); ?></textarea>
         

          <?php  global $vtmin_license_options;
     //test    if ( ($vtmin_setup_options['allow_license_info_reset'] == 'yes')  &&
     //          ($vtmin_license_options['state'] == 'suspended-by-vendor') ) { ?> 
            <span id="reset-button">         
            <h4 class="system-buttons-h4"><?php esc_attr_e('Reset Licensing Fatal Counter', 'vtmin'); ?></h4>
            <input id="nuke-info-button"    name="vtmin_license_options[reset_fatal_counter]"        type="submit" class="buttons button-third"      value="<?php esc_attr_e('Reset Licensing Fatal Counter', 'vtmin'); ?>" />
            </span>
          <?php //test } ?>             

        </div><!-- /#show-licensing-info -->  
                          
      </p>      


  
  
	</div><!-- /.wrap -->

<?php
} // end vtmin_display  


function vtmin_show_activate_button() {
   ?>
    <span id="how-activate-button">
    <h4 class="system-buttons-h4"><?php esc_attr_e('Activate License', 'vtmin'); ?></h4>        
    <input id="activate-button"   name="vtmin_license_options[activate]"    type="submit" class="nuke_buttons button-first"     value="<?php esc_attr_e('Activate License', 'vtmin'); ?>" /> 
    </span>
    <?php wp_nonce_field( 'vtmin_nonce', 'vtmin_nonce' ); ?>
  <?php 
}


function vtmin_show_deactivate_button() {
  ?>
    <h4 class="system-buttons-h4"><?php esc_attr_e('Deactivate License', 'vtmin'); ?></h4>
    <input id="deactivate-button"  name="vtmin_license_options[deactivate]"      type="submit" class="nuke_buttons button-second"      value="<?php esc_attr_e('Deactivate License', 'vtmin'); ?>" />
    <?php wp_nonce_field( 'vtmin_nonce', 'vtmin_nonce' ); ?>           
  <?php    
}

function vtmin_show_clear_button() {
   ?>  
    <br><br>      
    <h4 class="system-buttons-h4"><?php esc_attr_e('Clear Licensing Info', 'vtmin'); ?></h4>
    <input id="nuke-info-button"    name="vtmin_license_options[nuke-info]"       type="submit" class="nuke_buttons button-third"      value="<?php esc_attr_e('Clear Licensing Info', 'vtmin'); ?>" />
    <?php wp_nonce_field( 'vtmin_nonce', 'vtmin_nonce' ); ?>
     
  <?php 
}


function vtmin_system_info_cntl() {
    require_once  ( VTMIN_DIRNAME . '/admin/vtmin-system-info.php' ); 
}


/* ------------------------------------------------------------------------ *
 * Setting Registration
 * ------------------------------------------------------------------------ */ 

/**
 * Initializes the theme's Discount Reporting Options page by registering the Sections,
 * Fields, and Settings.
 *
 * This function is registered with the 'admin_init' hook.
 */ 

function vtmin_initialize_options() {

	// If the theme options don't exist, create them.
	if( false == get_option( 'vtmin_license_options' ) ) {
		add_option( 'vtmin_license_options', $this->vtmin_set_default_options() );  //add the option into the table based on the default values in the function.
	} // end if
  
/*      //v1.1.6 begin
      global $vtmin_license_options;
      $vtmin_license_options = get_option( 'vtmin_license_options' );
   //error_log( print_r(  'Begin License Options, vtmin_license_options= ', true ) );  
   //error_log( var_export($vtmin_license_options, true ) );
      if (!$vtmin_license_options) {
        $vtmin_license_options = $this->vtmin_set_default_options();
        update_option( 'vtmin_license_options', $vtmin_license_options);
      } 

  //v1.1.6 end
*/
	add_settings_section(
		'license_activation_section',			// ID used to identify this section and with which to register options
		__( 'Activate Pro License', 'vtmin' ),	// Title to be displayed on the administration page
   /* .'&nbsp;&nbsp; => &nbsp;&nbsp;'.
    __( 'for Production or Test site', 'vtmin' ),*/
		array(&$this, 'vtmin_license_section_callback'),	// Callback used to render the description of the section
		'vtmin_license_options_page'		// Page on which to add this section of options
	);
   
          
    add_settings_field(	       
		'key',						// ID used to identify the field throughout the theme
		__( 'License Key', 'vtmin' )    
    .'<br>'.
    __( '<span class="sub-label">&nbsp;&nbsp;<em>(you may use old SessionID - License Key is returned)</em></span>', 'vtmin' ), // The label to the left of the option interface element
		array(&$this, 'vtmin_key_callback'), // The name of the function responsible for rendering the option interface
		'vtmin_license_options_page',	// The page on which this option will be displayed
		'license_activation_section',			// The name of the section to which this field belongs
		array(								// The array of arguments to pass to the callback. In this case, just a description.
			 __( 'Pro Plugin License Key', 'vtmin' )
		)
	);
          
          
    add_settings_field(	       
		'email',						// ID used to identify the field throughout the theme
		__( 'License email', 'vtmin' )
    .'<br>'.
    __( '<span class="sub-label">&nbsp;&nbsp;<em>(email address supplied with Purchase)</em></span>', 'vtmin' ), // The label to the left of the option interface element
		array(&$this, 'vtmin_email_callback'), // The name of the function responsible for rendering the option interface
		'vtmin_license_options_page',	// The page on which this option will be displayed
		'license_activation_section',			// The name of the section to which this field belongs
		array(								// The array of arguments to pass to the callback. In this case, just a description.
			 __( 'Pro Plugin License email', 'vtmin' )
		)
	);

          
    add_settings_field(	       
		'prod_or_test',						// ID used to identify the field throughout the theme
		'<br>' .  __( 'Activation Type', 'vtmin' ), // The label to the left of the option interface element
		array(&$this, 'vtmin_prod_or_test_callback'), // The name of the function responsible for rendering the option interface
		'vtmin_license_options_page',	// The page on which this option will be displayed
		'license_activation_section',			// The name of the section to which this field belongs
		array(								// The array of arguments to pass to the callback. In this case, just a description.
			 __( 'Pro Plugin License prod_or_test', 'vtmin' )
		)
	);

/* v1.1.8.2 removed
     //v1.1.6.8- change workding        
    add_settings_field(	       
		'prod_url_supplied_for_test_site',						// ID used to identify the field throughout the theme
    '<span class="production_url_for_test">'.   
    		__( 'PRODUCTION Site URL', 'vtmin' )

    .'</span>',   // The label to the left of the option interface element
		array(&$this, 'vtmin_prod_url_callback'), // The name of the function responsible for rendering the option interface
		'vtmin_license_options_page',	// The page on which this option will be displayed
		'license_activation_section',			// The name of the section to which this field belongs
		array(								// The array of arguments to pass to the callback. In this case, just a description.
			 __( 'Pro Plugin License prod_url', 'vtmin' )
		)
	);
  */

  	
	// Finally, we register the fields with WordPress
	register_setting(
		'vtmin_license_options_group',
		'vtmin_license_options' ,
    array(&$this, 'vtmin_validate_setup_input')
	);
  
  /*
  //Licensing Conversion Warning!!
    //VTMIN_PRO_VERSION only exists if PRO version is installed and active
  if ( (VTMIN_VERSION == '1.1.5') && 
       (defined('VTMIN_PRO_VERSION')) ) {
    global $pagenow;
    if ( 'plugins.php' === $pagenow ) {
      add_action( 'in_plugin_update_message-' . VTMIN_PLUGIN_SLUG, 'vtmin_update_notice' );    
    }
  }
  */
	
} // end vtmin_initialize_options

 
  
   
  //****************************
  //  DEFAULT OPTIONS INITIALIZATION
  //****************************
function vtmin_set_default_options() {
   
       //error_log( print_r(  'License-Options.php, Begin vtmin_set_default_options', true ) ); 
    
     $url = home_url(); //SET URL     
     $url_no_http = $this->vtmin_strip_out_http($url); //v1.1.6.1 
     
    //TEST TEST TEST TEST TEST TEST TEST TEST TEST TEST TEST TEST v1.1.8.2
    //$url_no_http  = 'dev.anystore.com';

      //VTMIN_PRO_VERSION only exists if PRO version is installed and active
     if (defined('VTMIN_PRO_VERSION')) {  //changed v1.1.6
       $version = VTMIN_PRO_VERSION;
     } else {
       $version = null;
       /* at this point, $vtmin_setup_options['current_pro_version'] may not be loaded.  will be updated in admin_init Overhead function
       //v1.1.6.3 Refactored - switched to array value for PRO version, to allow for DEACTIVATED plugin
       global $vtmin_setup_options;
       $version =   $vtmin_setup_options['current_pro_version'];
       */
     } 
      /*
      //v1.1.8.2
      'url' = Registering site URL , may be a PROD or a TEST name
      'prod_or_test' = the type of license the site/user thinks it is apply to.
      - if this is PROD request
      - if PROD rego already exists
      - if 'url' is TEST site name type
      - try to register as a TEST site
      */       
     $options = array(           
          //screen fields
          'key'=> '',  //opt1 
          'email' => '',  //opt1 
          'prod_or_test' => 'prod',  //opt1
          'prod_url_supplied_for_test_site' => '',  //v1.1.8.2  NO LONGER USED, but remains for historical structure
          //not screen fields
          'url' =>  $url_no_http,  //v1.1.6.1   
          'status' => 'unregistered',    //  'valid'/'invalid'/'unregistered'
          'state' => 'unregistered',     // active / deactivated / pending (error but not yet suspended) / suspended-by-vendor  / unregistered      
          'msg' => '', //opt3  code for both valid and invalid license - invalid goes to admin notices, valid goes onscreen
          'strikes' => '', //opt3  code for both valid and invalid license - invalid goes to admin notices, valid goes onscreen
          'error_try_count' => 0,  //opt4
          'last_action' => '',  // 'activate_license', 'deactivate_license' , 'check_license'
          'last_failed_rego_ts' => '',  
          'last_failed_rego_date_time' => '',   
          'last_successful_rego_ts' => '', //opt6
          'last_successful_rego_date_time' => '',
          'last_response_from_host' => '',
          'last_check_date_in_seconds' => '',
          'params_sent_to_host' => '',
          'expires' => '',
          'diagnostic_msg' => '',
          'strikes_possible' => 3,
          'plugin_current_version' => $version,  //used by plugin-updater exclusively
          'plugin_new_version' => $version,
          'pro_plugin_version_status' => '',
          'pro_version' => '',    //used by main plugin file
          'pro_minimum_free_version' => '',
          'pro_deactivate' => '', //used as a switch to allow the deactivate to happen in admin_init in main plugin file 
          'localhost_warning_done' => '', //if > null, warning has been produced once          
          'user_role_editor_warning_done' => '', //v1.1.6 
          'older_wordpress_warning_done' => '',  //v1.1.6 
          'home_url' => $url,  //v1.1.6.1
          'rego_done' => ''  //v1.1.6.1   used with rego_clock   
     );
     return $options;
}

function vtmin_processing_options_callback () {
    ?>
    <h4 id="vtmin-processing-options"><?php esc_attr_e('These options apply to general discount processing.', 'vtmin'); ?></h4>
    <?php                                                                                                                                                                                      
}



function vtmin_license_section_callback () {
    global $vtmin_license_options; 
    $vtmin_license_options = get_option( 'vtmin_license_options' );

    //error_log( print_r(  'activation callback $vtmin_license_options', true ) );
    //error_log( var_export($vtmin_license_options, true ) ); 
      // **********************************************************
      // STATUS: valid / invalid / unregistered (default)
      // STATE:  active (only if valid) / deactivated (only if valid) / pending (error but not yet suspended) / suspended-by-vendor / unregistered (default)
      // **********************************************************       
    switch ( $vtmin_license_options['status'] ) { 
      //new license display
      case ''  :
      case ' ' :
      case 'unregistered' :
          if ( $vtmin_license_options['last_action'] <= ' '  ) {
          ?>                                   
            <h4 id="vtmin-license-messaging">
                  <strong><?php _e('Pro Plugin License Activation', 'vtmin'); ?></strong> 
            </h4> 
          <?php 
          } else {
          //v1.1.6  Busy message edited below
          ?>                                   
            <h4 id="vtmin-license-messaging">
                  <strong><?php _e('Varktech Registration Process busy.  Please try again. If it does not work a second time, please click on "Clear Licensing Info" and try again.', 'vtmin'); ?></strong> 
            </h4> 
          <?php           
          } 
        break; 
        
      //activation/deactivation successful
      case 'valid'  :     
          //v1.1.6.3 IF the return is an object, it came from inside Software Licensing, and this test IS NOT NECESSARY!
          if ( ( isset($vtmin_license_options['last_response_from_host']) ) &&
               (!is_object($vtmin_license_options['last_response_from_host']) ) ) {            
              //if I've had to ban them this way, or it's a real security issue, it'll get picked up here
              //v1.1.6.2 - IF refactored
              if ( ( isset($vtmin_license_options['last_response_from_host']['response']['code']) )
                             &&
                  ( ($vtmin_license_options['last_response_from_host']['response']['code'] == '403' ) ||
                   ($vtmin_license_options['last_response_from_host']['response']['code'] == '500' ) ) ) {
                $vtmin_license_options['msg'] = 'Activation request temporarily blocked by Varktech security. Please click "Show Licensing Info", copy info. Contact <a href="https://www.varktech.com/support"  title="Support">VarkTech Support</a>, paste copied information into email.';       
              }  
           }        
          ?>                                   
            <h2 id="vtmin-license-messaging" style="color: green !important;" >
                  <strong><?php echo $vtmin_license_options['msg']; ?></strong> 
            </h2> 
          <?php  
        break;

      
      //activation/deactivation successful
      case 'invalid'  :
          //v1.1.6.3 IF the return is an object, it came from inside Software Licensing, and this test IS NOT NECESSARY!
          if ( ( isset($vtmin_license_options['last_response_from_host']) ) &&
               (!is_object($vtmin_license_options['last_response_from_host']) ) ) {   
              //if I've had to ban them this way, or it's a real security issue, it'll get picked up here
              //v1.1.6.2 - IF refactored
              if ( ( isset($vtmin_license_options['last_response_from_host']['response']['code']) )
                             &&
                  ( ($vtmin_license_options['last_response_from_host']['response']['code'] == '403' ) ||
                   ($vtmin_license_options['last_response_from_host']['response']['code'] == '500' ) ) ) {
                $vtmin_license_options['msg'] = 'Activation request temporarily blocked by Varktech security. Please click "Show Licensing Info", copy info. Contact <a href="https://www.varktech.com/support"  title="Support">VarkTech Support</a>, paste copied information into email.';       
              }
          }
          switch ( $vtmin_license_options['state'] ) { 
          
              case ($vtmin_license_options['state'] == 'pending')  :
                 //if license expired, no 'tries' left!
                 if ($vtmin_license_options['diagnostic_msg'] == 'demo_license_expired') {
                    ?>                                   
                      <h2 id="vtmin-license-messaging" style="color: red !important;" >
                            <strong><?php echo $vtmin_license_options['msg']; ?></strong>
                      </h2> 
                      <h2 id="vtmin-license-messaging" style="color: red !important;" >
                            <strong>Please  <a  href=" <?php echo VTMIN_PURCHASE_PRO_VERSION_BY_PARENT ; ?> "  title="Please purchase a full license!"><?php _e('Purchase an Unlimited Pro License', 'vtmin'); ?></a> ! </strong>
                      </h2>                      
                    <?php                  
                 
                    return;
                 }
                 
                 if ($vtmin_license_options['strikes'] > 0) {
                    $tries_left = $vtmin_license_options['strikes_possible'] - $vtmin_license_options['strikes'];
                    if ($tries_left == 1) {
                      $tries_left_msg = ' try remaining';
                    } else {
                      $tries_left_msg = ' tries remaining';                    
                    }
                    ?>                                   
                      <h2 id="vtmin-license-messaging" style="color: red !important;" >
                            <strong><?php echo $vtmin_license_options['msg']; ?></strong>
                            <br><br> 
                            <strong><?php echo 'You have ' .$tries_left. $tries_left_msg ; ?></strong>
                      </h2> 
                    <?php 
                    if ($tries_left == 1) {
                      ?>                                   
                        <h2 class="red" >
                              <strong><?php echo 'If you make a mistake with your last try, the License will be Suspended for ALL SITES using this license.'; ?></strong>
                        </h2> 
                      <?php 
                    }
                  } else {
                    ?>                                   
                      <h2 id="vtmin-license-messaging" style="color: red !important;" >
                            <strong><?php echo $vtmin_license_options['msg']; ?></strong>
                      </h2> 
                    <?php 
                  }
                break;                 
              case ($vtmin_license_options['state'] == 'suspended-by-vendor')  :
                  ?>                                   
                    <h2 id="vtmin-license-messaging" style="color: red !important;" >
                          <strong><?php echo $vtmin_license_options['msg']; ?></strong> 
                    </h2> 
                  <?php  
                break;            
          }
          
        break;
               
                 
    } 
                    

    return;      
    
}


  
  
  function vtmin_key_callback() {    //opt4
  	$options = get_option( 'vtmin_license_options' );	
    $html = '<textarea type="text" id="key"  rows="1" cols="60" name="vtmin_license_options[key]">' . $options['key'] . '</textarea>';  	
  	echo $html;
    return;
  }
  
  function vtmin_email_callback() {    //opt4
  	$options = get_option( 'vtmin_license_options' );	
    $html = '<textarea type="text" id="email"  rows="1" cols="60" name="vtmin_license_options[email]">' . $options['email'] . '</textarea>';  
  	echo $html;
    return;
  }
  
  
  function vtmin_prod_or_test_callback() {   
  	$options = get_option( 'vtmin_license_options' );	
    
    //error_log( print_r(  'showing prod or test radio buttons, $vtmin_license_options= ' , true ) );
    //error_log( var_export($options, true ) );    
    

    $checked = 'checked="checked"';
    
    if ($options['prod_or_test'] == 'prod') {$prod_checked = $checked;} else {$prod_checked = null;}
    if ($options['prod_or_test'] == 'test') {$test_checked = $checked;} else {$test_checked = null;}
  //  if ($options['prod_or_test'] == 'demo') {$demo_checked = $checked;} else {$demo_checked = null;}   //removed 1.1.6.7
    
    $html  = '<ul style="
              padding: 15px;
              background: white none repeat scroll 0% 0%;
              width: 350px;
              border: 1px solid rgb(221, 221, 221);
               " >';			
       
    $html .= '<li> <input id="radio-prod" class="" name="prod_or_test" value="prod" type="radio" '  . $prod_checked . '><span>'    . __("Production Site", "vtmin") .  '</span> </li>';
    $html .= '<li> <input id="radio-test" class="" name="prod_or_test" value="test" type="radio" '  . $test_checked . '><span>'    . __("Test Site / Staging Site / Development Site", "vtmin") .  '</span> </li>';  //v1.1.7.2 wording changed
  //removed 1.1.6.7
 //   $html .= '<li> <input id="radio-demo" class="" name="prod_or_test" value="demo" type="radio" '  . $demo_checked . '><span>'    . __("3-Day Full Free Demo License", "vtmin") .  '</span> </li>';     
    $html .= '</ul>';	
  	echo $html;    
    return;
  }
  
  

 /*
  function vtmin_prod_url_callback() {    //opt4
  	$options = get_option( 'vtmin_license_options' );	
    $html = '<div class="production_url_for_test">';
    $html .= '<textarea type="text" id="prod_url_supplied_for_test_site"  rows="1" cols="60" name="vtmin_license_options[prod_url_supplied_for_test_site]">' . $options['prod_url_supplied_for_test_site'] . '</textarea>';
     
    $html .= '<br><br><p><em>';
    //v1.1.8.2 changed names list
    $html .= '<strong>The TEST site URL **must Include** </strong> one of the following strings (ignore the "|"):';
    $html .= '&nbsp;&nbsp;   development  |  testing  |  demonstration  |  staging   | ';   
    $html .= '&nbsp;&nbsp;   test.   |  .test   |  test-   |  -test   |  /test   | '; 
    $html .= '&nbsp;&nbsp;   stage.  |  .stage  |  stage-  |  -stage  |  /stage  | '; 
    $html .= '&nbsp;&nbsp;   dev.    |  .dev    |  dev-    |  -dev    |  /dev    | ';  
    $html .= '&nbsp;&nbsp;   demo.   |  .demo   |  demo-   |  -demo   |  /demo   | ';  
    $html .= '&nbsp;&nbsp;   beta.   |  .beta   |  beta-   |  -beta   |  /beta   | '; 
    $html .= '&nbsp;&nbsp;   stg.    |  .stg    |  stg-    |  -stg    |  /stg    | '; 
    $html .=  '</em></p>'; 
    $html .= '<br>';
    $html .= '<strong>For Example:</strong>';
    $html .= '<br>&nbsp;&nbsp; Production URL: &nbsp;&nbsp;&nbsp; www.sellyourstuff.com';
    $html .= '<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; Test URL: &nbsp;&nbsp;&nbsp; www<strong><em>.test.</em></strong>sellyourstuff.com';
    $html .= '<br><br>';
    $html .=  '</div>'; 
      	
  	echo $html;
    return;
  }
  */

  //*****************************************************************************************************
  //THIS function is a PRO-ONLY function, and runs ONLY when the PRO plugin is INSTALLED AND ACTIVE
  //*****************************************************************************************************
  function vtmin_license_phone_home($input, $action, $skip_admin_check=null) {
  //$skip_admin_check is for phone_home exec from function vtmin_maybe_recheck_license_activation()
    
    //error_log( print_r(  'Begin vtmin_license_phone_home' , true ) );
    
    //v1.1.6.3  begin - is Woocommerce installed
    if ( ! class_exists( 'WooCommerce' ) )  {
      return;
    }
    //v1.1.6.3  end
      
     global $vtmin_license_options;
   /*
   verify basic stuff:
    license supplied, email supplied
    if test selected, PRO is supplied
    if test selected, enforce the '.test. etc node requirement'
        IN THE ERROR msg for node requirement, explain that 
          if TEST is 1st installation (no Prod yet), you can register the test as PROD
          and then Deactivate the test and re-register as PROD
          
    COUNT LICENSE NOT FOUND ==>> ONLY error NOT counted at the Server
    3 STRIKES you're out...


  ********************************************************
  *  IF TEST
  *  popup field PROD URL ==>> put into the regular URL field
  *  grab the current URL which is the test URL in this case
  *  put in into TEST_URL      
  *********************************************************    

   */

		// run a quick security check
	 	 if ($skip_admin_check == 'yes') {
        $carry_on = true;  
     } else {
       if  ( ! check_admin_referer( 'vtmin_nonce', 'vtmin_nonce' ) ) {	
          return; // get out if we didn't click the Activate button
        }
     }

    //v1.1.6.7 begin
    //strip out leading/trailing spaces
		// retrieve the license from the database
		$license        = trim( $input['key'] );
    //move trimmed value back
    $input['key']   = $license; 
    
    $email          = trim( $input['email'] ); 
    //move trimmed value back
    $input['email'] = $email; 
    //v1.1.6.7 end
    
    $prod_or_test  = $input['prod_or_test'];
    
    //********************************************
    //$url USAGE CHANGED - now it's ALWAYA the current url
    //********************************************  
    $url = $input['url'];


//********************************************************** 
//TEST TEST TEST TEST TEST TEST TEST TEST TEST TEST TEST TEST v1.1.8.2
//$url = 'anystore2.com';
//$test_url = 'test.staging.wpengine.anything.com';
// $ip_address = '1.2.3.4';
//**********************************************************   
      
    $item_name = VTMIN_ITEM_NAME;
    $item_id   = VTMIN_ITEM_ID;

    
    /*
    //**************************
    //* Begin GET IP - somehwnat complex logic to get Host's IP address!!!!!!!!!!
    //**************************
    //get host IP, from http://stackoverflow.com/questions/5800927/how-to-identify-server-ip-address-in-php
    $host = gethostname();
    
    // from http://stackoverflow.com/questions/4305604/get-ip-from-dns-without-using-gethostbyname
    $ip = $this->vtmin_getAddrByHost($host);  //returns $host if IP not found
    if ($ip == $host) {  //if the address did not resolve, then use gethostbyname
      $ip = gethostbyname($host);
    }
    */
    //the definitive solution!!!!!!!!!!!
    //$ip = vtmin_get_ip(); ==>> now in vtmin_get_ip_address
    //end GET IP
    
      
    // data to send in our API request
		$api_params = array(
			'edd_action'   => $action,
			'license' 	   => $license,
			'item_name'    => urlencode( $item_name ), // the name of our product in VTMIN
      'item_id'      => $item_id , // the ID of our product in VTMIN
			'url'          => urlencode( $url ),
      'prod_or_test' => $prod_or_test,
      //'test_url'     => urlencode( $test_url ), v1.1.8.2 NO LONGER USED
      'email'        => urlencode($email),            
      'ip_address'   => vtmin_get_ip_address() 

      
      
      
      
      // from http://stackoverflow.com/questions/5800927/how-to-identify-server-ip-address-in-php
      // 'ip_address'   =>  $_SERVER["SERVER_ADDR"]  - don't use this either!! 
      //'ip_address' = $ip  
		);

		// Call the custom API.
    //https://wordpress.org/support/topic/wp_remote_post-and-timeout ==>> adjust timeout for efficiency during testing
    /*
    compress was introduced in WordPress 2.6 and allows you to send the body of the request in a compressed format. This will be outside the scope of our future articles.
    decompress is similar to compress except that it's on our end - if compressed data is received, this will allow us to decompress the content before doing any further work or processing on it.
    */
     $remote_data = array( 'timeout' => 15, 'sslverify' => false, 'body' => $api_params ) ;
 
  //v1.1.6 begin 
	//	$response = wp_remote_post( VTMIN_STORE_URL, array( 'timeout' => 15, 'sslverify' => false, 'body' => $api_params ) );

      //v1.1.6.1 begin
      if ($prod_or_test == 'demo') {
        $show_demo = ';Demo';      
      } else {
        $show_demo = null;  
      }
      //v1.1.6.1 end
      
      global $vtmin_setup_options; //v1.1.6.3
      
      $response = wp_remote_post( VTMIN_STORE_URL, array(
    			'method' => 'POST',
    			'timeout' => 45,
    			'redirection' => 5,
    			'httpversion' => '1.0',
          //v1.1.6.3 switched to array value for PRO version, to allow for DEACTIVATED plugin
    			'headers' => array( 'user-agent' => 'Aardvark/Register.Check/vtmin/Free/V' . VTMIN_VERSION . '/Pro' .$show_demo. '/V' . $vtmin_setup_options['current_pro_version'] .';'. $input['url'] ),  //v1.1.6.3 
          //'headers' => array( 'user-agent' => 'Aardvark/Register.Check/vtmin/Free/V' . VTMIN_VERSION . '/Pro' .$show_demo. '/V' . VTMIN_PRO_VERSION .';'. $input['url'] ),
    			'body' => $api_params,
    			'sslverify' => false
    			) );
      /*
      from woothemes-updater/classes/class-woothemes-update-checker.php	
      	$request = wp_remote_post( ( $api == 'info' ) ? $this->api_url : $this->update_check_url, array(
      			'method' => 'POST',
      			'timeout' => 45,
      			'redirection' => 5,
      			'httpversion' => '1.0',
      			'headers' => array( 'user-agent' => 'WooThemesUpdater/' . $this->version ),
      			'body' => $args,
      			'sslverify' => false
      			) );
      
      */    
  //v1.1.6 end   
    
    
    
    $input['params_sent_to_host'] = $api_params;
    $input['last_response_from_host'] = $response;


		// make sure the response came back okay
		if ( is_wp_error( $response ) ) {
			//no change to input, just send back
      $input['msg']     =  "License activation function was temporarily busy, please try again!";
      return $input;    
    }


		// decode the license data
		$license_data = json_decode( wp_remote_retrieve_body( $response ) );
    
    //COMES BACK VALID OR INVALID
    if (isset($license_data->status)) {
      $input['status']      =  $license_data->status;
    }
    if (isset($license_data->state)) {
      $input['state']      =  $license_data->state;
    }
    if (isset($license_data->msg)) {
      $input['msg']         =  $license_data->msg;
    }
    
    //v1.1.6 begin  refactored
    if (isset($license_data->diagnostic_msg)) {
      $input['diagnostic_msg'] =  $license_data->diagnostic_msg;
    } else {
      if (isset($license_data->verify_response)) {
        $input['diagnostic_msg'] =  $license_data->verify_response;
      }       
    }
    //v1.1.6 end
 
    if (isset($license_data->strikes)) {
      $input['strikes']     =  $license_data->strikes;
    }             

    $input['last_action'] =  $action;
    
    if (isset($license_data->expires)) {
      $input['expires']  =  $license_data->expires;
    }

    //*******************************************
    //v1.1.8.2 begin - ** new data coming back **
    /*
      'url' = Registering site URL , may be a PROD or a TEST name
      'prod_or_test' = the type of license the site/user thinks it is apply to.
      - if this is PROD request
      - if PROD rego already exists
      - if 'url' is TEST site name type
      - try to register as a TEST site 
     */   
    if (isset($license_data->prod_or_test)) {
      $input['prod_or_test'] = $license_data->prod_or_test; 
    }

    //v1.1.8.2 end
    //************

      // **********************************************************
      // STATUS: valid / invalid / unregistered (default)
      // STATE:  active / deactivated / pending (error but not yet suspended) / suspended-by-vendor
      // **********************************************************   
    
    // v1.1.6 pulled out of here, put in mainline - now picked up when 'vtmin_license_suspended' is processed
    /*
    if ($license_data->state == 'suspended-by-vendor') {
      //deactivate PRO plugin     
      vtmin_deactivate_pro_plugin();
      //vtmin_increment_license_count();
    }
    */
    //  

    If ($input['status'] == 'valid') {
      $input['last_successful_rego_ts'] = time(); 
      $input['last_successful_rego_date_time'] = date("Y-m-d H:i:s"); 
    } else {
      $input['last_failed_rego_ts'] = time(); 
      $input['last_failed_rego_date_time'] = date("Y-m-d H:i:s");     
    }
    
    //in case the USEr used the old SESSIONID, move the returned LICENSE KEY back into the License field.    
    if ( ($license_data->key > ' ') &&
         ($license_data->key != $input['key']) ) {
      $input['key'] = $license_data->key;    
    }

 
     //error_log( print_r(  'PHONE Home at BOTTOM,  $vtmin_license_options= ', true ) );
    //error_log( var_export($vtmin_license_options, true ) );  
 
   return $input;
  } 


  public function vtmin_enqueue_setup_scripts($hook_suffix) {
    switch( $hook_suffix) {        //weird but true
      case 'vtmin-rule_page_vtmin_license_options_page':                
        wp_register_style('vtmin-admin-style', VTMIN_URL.'/admin/css/vtmin-admin-style-' .VTMIN_ADMIN_CSS_FILE_VERSION. '.css' );  //v1.1.0.7
        wp_enqueue_style ('vtmin-admin-style');
        wp_register_style('vtmin-admin-settings-style', VTMIN_URL.'/admin/css/vtmin-admin-settings-style.css' );  
        wp_enqueue_style ('vtmin-admin-settings-style');
      break;
    }
  }    

  
  function vtmin_validate_setup_input( $input ) {
     //error_log( print_r(  'Begin  vtmin_validate_setup_input' , true ) ); 
    //Get the existing settings!
    
    
    $existing_license_options = get_option( 'vtmin_license_options' );
  
/*  COMMENTED FOR TESTING ONLY!  
    //*********************************
    // BAIL if suspended 
    //*********************************
    if ($existing_license_options['state'] == 'suspended-by-vendor') {
      $admin_errorMsg = $existing_license_options['msg'] . ' - no action possible without contacting Vendor';
      $admin_errorMsgTitle = 'License Key Suspended';
      add_settings_error( 'vtmin Options', $admin_errorMsgTitle , $admin_errorMsg , 'error' );
      return $existing_license_options;  
    }
    //***************************************
 */ 
  
    $new_combined_options = array_merge($existing_license_options, $input);
  
  
    //did this come from on of the secondary buttons?
    $activate     = ( ! empty($input['activate']) ? true : false );
    $deactivate   = ( ! empty($input['deactivate']) ? true : false );
    $nuke_info    = ( ! empty($input['nuke-info']) ? true : false ); 
    $reset_fatal_counter    = ( ! empty($input['reset_fatal_counter']) ? true : false );
  
    //global $vtmin_license_options; 
  
    if ($nuke_info) {
      $license_options = $this->vtmin_set_default_options();
      update_option('vtmin_last_license_check_ts', null);
      return $license_options;
    }
     
    if ($reset_fatal_counter) {      
      //if an unlicensed customer needs to clear the error state after license purchase
      update_option('vtmin_license_count', 0 );
      delete_option('vtmin_rego_clock');
      $license_options = $this->vtmin_set_default_options();    
      return $license_options;
    }
    
    
    $settings_error = false;
  
    if ( (isset($new_combined_options['key'])) &&
         ($new_combined_options['key'] > ' ') ) {
      $carry_on = true;    
    } else {
      $admin_errorMsg = 'License Key required';
      $admin_errorMsgTitle = 'License Key required';
      add_settings_error( 'vtmin Options', $admin_errorMsgTitle , $admin_errorMsg , 'error' );
      $settings_error = true;    
    }
    
    if ( (isset($new_combined_options['email'])) &&
         ($new_combined_options['email'] > ' ') ) {
      $carry_on = true;    
    } else {
      $admin_errorMsg = 'Registered purchaser email address required';
      $admin_errorMsgTitle = 'Registered purchaser email address';
      add_settings_error( 'vtmin Options', $admin_errorMsgTitle , $admin_errorMsg , 'error' );
      $settings_error = true;     
    }


    //Pick up RADIO button
    $new_combined_options['prod_or_test'] = $_REQUEST['prod_or_test'];
    
    //v1.1.6.7 begin
    switch( true ) { 
      case $activate        === true : 
          $new_combined_options['last_action'] = 'activate_license';
        break;
      case $deactivate       === true : 
          $new_combined_options['last_action'] = 'deactivate_license';   
        break;
      default:   //standard update button hit...                 
        break;
    }              
   //v1.1.6.7 end

//$new_combined_options['url'] = 'test.staging.wpengine.anything.com';

    /* defaults to PROD, not necessary
    if ( (isset($new_combined_options['prod_or_test'])) &&
    */
    if ($new_combined_options['prod_or_test'] == 'test')  {
        
        //v1.1.8.2 RECODED!!
    
          //v1.1.6.3  staging[number].prod url need for SITEGROUND  
          //  needs to be added to   vark-software-licenses.php  !!!
      
        if ( (strpos($new_combined_options['url'],'test.') !== false)  ||
             (strpos($new_combined_options['url'],'.test') !== false)  ||
             (strpos($new_combined_options['url'],'test-') !== false)  ||
             (strpos($new_combined_options['url'],'-test') !== false)  ||
             (strpos($new_combined_options['url'],'/test') !== false)  ||   
             
             (strpos($new_combined_options['url'],'dev.') !== false)  ||
             (strpos($new_combined_options['url'],'.dev') !== false)  ||
             (strpos($new_combined_options['url'],'dev-') !== false)  ||
             (strpos($new_combined_options['url'],'-dev') !== false)  ||
             (strpos($new_combined_options['url'],'/dev') !== false)  ||  
              
             (strpos($new_combined_options['url'],'demo.') !== false)  ||
             (strpos($new_combined_options['url'],'.demo') !== false)  ||
             (strpos($new_combined_options['url'],'demo-') !== false)  ||
             (strpos($new_combined_options['url'],'-demo') !== false)  ||
             (strpos($new_combined_options['url'],'/demo') !== false)  ||
             
             (strpos($new_combined_options['url'],'beta.') !== false)  ||
             (strpos($new_combined_options['url'],'.beta') !== false)  ||
             (strpos($new_combined_options['url'],'beta-') !== false)  ||
             (strpos($new_combined_options['url'],'-beta') !== false)  ||
             (strpos($new_combined_options['url'],'/beta') !== false)  ||

             (strpos($new_combined_options['url'],'stage.') !== false)  ||
             (strpos($new_combined_options['url'],'.stage') !== false)  ||
             (strpos($new_combined_options['url'],'stage-') !== false)  ||
             (strpos($new_combined_options['url'],'-stage') !== false)  ||
             (strpos($new_combined_options['url'],'/stage') !== false)  ||

             (strpos($new_combined_options['url'],'stg.') !== false)  ||
             (strpos($new_combined_options['url'],'.stg') !== false)  ||
             (strpos($new_combined_options['url'],'stg-') !== false)  ||
             (strpos($new_combined_options['url'],'-stg') !== false)  ||
             (strpos($new_combined_options['url'],'/stg') !== false)  ||
               
             (strpos($new_combined_options['url'],'development') !== false)  ||  
             (strpos($new_combined_options['url'],'testing') !== false)  || 
             (strpos($new_combined_options['url'],'demonstration') !== false)  ||              
             (strpos($new_combined_options['url'],'staging') !== false) ) {            
           $carry_on = true; 
          
        } else {
          //v1.1.8.2 new error msg
          $admin_errorMsg = '"Activation Type = TEST"
              <br><br>This Internal Site Name 
              <br><br>&nbsp;&nbsp; ==> &nbsp;&nbsp;' .$new_combined_options['url']. '&nbsp;&nbsp; <==
               <br><br>Does NOT meet TEST naming requirements
               <br>&nbsp;&nbsp;  TEST NAME Must contain ONE of the following strings (ignore the "|"):
               <br><br>&nbsp;&nbsp;   development  |  testing  |  demonstration  |  staging   |    
               <br>&nbsp;&nbsp;   test.   |  .test   |  test-   |  -test   |  /test   |  
               <br>&nbsp;&nbsp;   stage.  |  .stage  |  stage-  |  -stage  |  /stage  |  
               <br>&nbsp;&nbsp;   dev.    |  .dev    |  dev-    |  -dev    |  /dev    |   
               <br>&nbsp;&nbsp;   demo.   |  .demo   |  demo-   |  -demo   |  /demo   |   
               <br>&nbsp;&nbsp;   beta.   |  .beta   |  beta-   |  -beta   |  /beta   |  
               <br>&nbsp;&nbsp;   stg.    |  .stg    |  stg-    |  -stg    |  /stg    | 
              <br><br>(Internal site name = Wordpress Home URL)'; 

          $admin_errorMsgTitle = 'TEST site registration';
          add_settings_error( 'vtmin Options', $admin_errorMsgTitle , $admin_errorMsg , 'error' );
          $settings_error = true;                
        }   
    }
       
    if ($settings_error) {
      $new_combined_options['status'] = 'invalid';
      $new_combined_options['state']  = 'pending';
      $new_combined_options['msg']    = $admin_errorMsg;
      return $new_combined_options;    
    }
    

    switch( true ) { 
      case $activate        === true : 
          //if already active, no action required
          if ( $new_combined_options['state'] == 'active') { 
            return $new_combined_options;   
          }
          
          $action = 'activate_license';
          
          //Only possible when the PRO PLUGIN is INSTALLED AND ACTIVE
          $new_combined_options = $this->vtmin_license_phone_home($new_combined_options, $action);

//TEST    $new_combined_options['msg'] = 'License Activated';
          
          /*
          //clear out plugin version fields for new activation:
          $new_combined_options['pro_plugin_version_status'] = null;
          $new_combined_options['pro_version'] = null;
          */          
          //*****
          // MESSAGING handled in main plugin file, in function vtmin_maybe_pro_license_error
          //*****
          
          /*  INVALID  done in main file
          If ($new_combined_options['status'] == 'invalid') {
            $vtmin_license_options = $new_combined_options; //OVERWRITE temporarily so that admin_notices can pick up the text          
            add_action( 'admin_notices', 'vtmin_license_error_notice' );        
          }
          */
          /*
          If ($new_combined_options['status'] == 'valid') {
            //MESSAGE, built-in perhaps ===>>> always displays status
          }
          */
   
        break;
      case $deactivate       === true :  
          //if already deactivated, no action required
          if ( $new_combined_options['state'] == 'deactivated') { 
            return $new_combined_options;   
          }
          //Only possible when the PRO PLUGIN is INSTALLED AND ACTIVE
          $action = 'deactivate_license';
          $new_combined_options = $this->vtmin_license_phone_home($new_combined_options, $action);

          /*
          //clear out plugin version fields for new activation:
          $new_combined_options['pro_plugin_version_status'] = null;
          $new_combined_options['pro_version'] = null;
          $new_combined_options['msg'] = 'License Deactivated';
          */
          
          //*****
          // MESSAGING handled in main plugin file, in function vtmin_maybe_pro_license_error
          //*****
                  
          /* INVALID done in main file
          add_action( 'admin_notices', 'vtmin_license_error_notice' );
          */ 
        break;

      default:   //standard update button hit...                 
      
        break;
    }
 

         
      /*
      CLIENT tracks these statuses:
      
          $failure_msg = 'License Not Found'
          $failure_msg = 'Email Not Supplied';
          $failure_msg = 'Prod_or_Test value Not Supplied';
          $failure_msg = 'Test URL Not Supplied';
          $failure_msg = 'IP Address Not Supplied';
          $failure_msg = 'Prod URL not supplied for Test URL registration';
          
          $vark_args['verify_response'] = 'test_name_invalid'
          $vark_args['verify_response'] = 'license_invalid'
          $vark_args['verify_response'] = 'email_mismatch'
          $vark_args['verify_response'] = 'test_already_activated'; //info only, not a strike
          $vark_args['verify_response'] = 'prod_already_activated'; //info only, not a strike
      
      */
    

    //SUSPEND LOCALLY ONLY for THESE issues
    if ($new_combined_options['status'] == 'invalid') {
      if ( ($new_combined_options['msg'] == 'Email supplied does not match email address for License') ||
           ($new_combined_options['msg'] == 'License Not Found') ||
           ($new_combined_options['msg'] == 'Email Not Supplied') ||
           ($new_combined_options['msg'] == 'Prod_or_Test value Not Supplied') ||
           ($new_combined_options['msg'] == 'Test URL Not Supplied') ||
           ($new_combined_options['msg'] == 'IP Address Not Supplied') ||
           ($new_combined_options['msg'] == 'Prod URL not supplied for Test URL registration') ||
           ($new_combined_options['diagnostic_msg'] == 'different_test_site_already_registered' ) ||
           ($new_combined_options['diagnostic_msg'] == 'test_name_invalid' ) ||
           ($new_combined_options['diagnostic_msg'] == 'license_invalid' ) ||
           ($new_combined_options['diagnostic_msg'] == 'item_name_mismatch' ) ||
           ($new_combined_options['diagnostic_msg'] == 'email_mismatch' ) ||
           ($new_combined_options['diagnostic_msg'] == 'demo_license_expired')  ) {
      
        //strikes are NOT increased at the HOST, only HERE  

        if ($existing_license_options['strikes'] > $new_combined_options['strikes'] ) {
          $new_combined_options['strikes'] = $existing_license_options['strikes'];
        }        
        $new_combined_options['strikes']++;

        if ($new_combined_options['strikes'] >= 10) { //v1.1.6.7 upgraded the strike count to 10
           $new_combined_options['state']  = 'suspended-by-vendor';
           $new_combined_options['status'] = 'invalid'; 
           $new_combined_options['diagnostic_msg'] = 'suspended after 10 strikes!';
           //vtmin_deactivate_pro_plugin(); v1.09.90 removed temporarily
           vtmin_increment_license_count();           
    			 //not needed, taken care of elsewhere here...
           //$new_combined_options['msg']    = 'License Suspended by Vendor.  Please contact <a href="https://www.varktech.com/support"  title="Support">VarkTech Support</a> for more Information.';     
        }
        $new_combined_options['strikes_possible'] = 10; //v1.1.6.7 upgraded the strike count to 10
      } else {
        $new_combined_options['strikes_possible'] = 3;      
      }
    } 


    return $new_combined_options;                       
  } 



  //from http://stackoverflow.com/questions/15699101/get-client-ip-address-using-php
  public  function  vtmin_strip_out_http($url) {
      $url = str_replace( 'https://', '', $url  ) ; 
      $url = str_replace( 'http://', '', $url  ) ; 
      $url = rtrim($url, "/" ); //remove trailing slash
      //$url = str_replace( 'www.', '', $url  ) ; //v1.1.8.2 strip out WWW
      return $url;
  }


  /*
  // from http://stackoverflow.com/questions/4305604/get-ip-from-dns-without-using-gethostbyname
  public function vtmin_getAddrByHost($host, $timeout = 3) {
   $query = `nslookup -timeout=$timeout -retry=1 $host`;
   if(preg_match('/\nAddress: (.*)\n/', $query, $matches))
      return trim($matches[1]);
   return $host;
  }
  */
  


} //end class

$vtmin_license_options_screen = new VTMIN_License_Options_screen;


/*
PLUGIN UPDATER
*/

    /* V1.1.6.3   TURN OFF PLUGIN UPDATER WITH "FALSE" (if too heavy in wp-admin)
      add_filter('vtmin_do_plugin_updater', 'do_plugin_updater', 0); //0 priority = do FIRST
      function do_plugin_updater() { return FALSE; }
    */  
    $do_plugin_updater = apply_filters('vtmin_do_plugin_updater',TRUE ); //V1.1.6.3 ALLOW UPDATER SHUTOFF

      
  //VTMIN_PRO_VERSION only exists if PRO version is installed and active
  //v1.1.6.3 can't test for pro_version, may not be ACTIVE!
  //if (defined('VTMIN_PRO_VERSION')) { //v1.1.6.1
    if ( ( !class_exists( 'VTMIN_Plugin_Updater' ) ) &&
         ($do_plugin_updater) ) {  //V1.1.6.3
    	// load our custom updater
      include ( VTMIN_DIRNAME . '/admin/vtmin-plugin-updater.php'); 
    
    }
 // }

  
//v1.1.6  THis NEEDS to be in admin_init!!!
//v1.1.6.3  Activated!! Now set to run a max of 2x per day, ecept when manually demanded via vtmin_force_plugin_updates_check
   //VTMIN_PRO_VERSION only exists if PRO version is installed and active 
  /*
  //checks for PRO later
  if ( (defined('VTMIN_PRO_VERSION')) &&
       (class_exists( 'WooCommerce'))  ) { //v1.1.6.3
  */     
  if ( (class_exists( 'WooCommerce')) &&
       ($do_plugin_updater) && 
       (get_option('vtmin_do_pro_plugin_update') !== FALSE  ) ) {   //v1.1.6.7  plugin updater now runs *only* when a plugin mismatch is detected in the free version - so there must always be paired updates!!     
    add_action( 'admin_init', 'vtmin_maybe_exec_plugin_updater', 0 );  
  }

  //***************************
  // ONLY RUN when PRO PLUGIN is Installed and ACTIVE
  //***************************
  /*
  //v1.1.6.3 REFACTORED
  
  There are two check_ts:
    vtmin_last_updater_check_ts
    vtmin_last_license_check_ts
    
  IF updater_check passes and phone_home passes,
  BOTH TS get updated!!!!!!!!!!  
  
  (vtmin_last_license_check_ts also gets updated in the plugin-updater itself.)
  */
  function vtmin_maybe_exec_plugin_updater() {
  
      //error_log( print_r(  'BEGIN vtmin_maybe_exec_plugin_updater' , true ) );
  
  
      global $vtmin_license_options, $vtmin_setup_options;
      $vtmin_license_options = get_option( 'vtmin_license_options' );
    
    
      //demo licenses are NEVER updated
      if ($vtmin_license_options['prod_or_test'] == 'demo') {  
          //error_log( print_r(  'vtmin_maybe_exec_plugin_updater exit001' , true ) ); 
        return;
      } 
      
     /* 
      
      only allow valid and active???  what about folks who try to get an unregistered update?
      it will catch them, but only on the return trip, and they can do it again and again!
      
      */
      
      //allows through PENDING status, such as Version Mismatch!
      if ( ($vtmin_license_options['state'] == 'suspended-by-vendor') ||
           ($vtmin_license_options['state'] == 'unregistered') ||
           ($vtmin_license_options['state'] == null) ||  //v1.1.6.3
           ($vtmin_license_options['state'] == 'deactivated') ) { 
          //error_log( print_r(  'vtmin_maybe_exec_plugin_updater exit002' , true ) );       
        return;
      }
      
        //v1.1.6.3 begin 
      //vtmin_host_has_new_version stored only in plugin-updater, when new version found!
      //usually takes three rounds of access to successfully bring a new version across...
      $new_version_access_count = get_option( 'vtmin_new_version_access_count' );
      
      //error_log( print_r(  '$host_has_new_version= ' .$new_version_access_count , true ) );
  
      //only allow this through $new_version_access_count times, decrementing as you go.  Set in plugin-updater!
      if ($new_version_access_count) {
        if ($new_version_access_count > 1) {
          $new_version_access_count-- ;
          update_option('vtmin_new_version_access_count', $new_version_access_count);
        } else {
          //at 1, instead of decrementing, just get rid of option
          delete_option('vtmin_new_version_access_count');
        }
      }     

      
      //reduce timing to 2x per day ONLY
      $last_updater_check_ts = get_option( 'vtmin_last_updater_check_ts' );
      $today = time(); 
          
      if (!is_numeric($last_updater_check_ts)) {  
         update_option('vtmin_last_updater_check_ts', $today);  //just update the TS if not there, 1st-time activation will be in progress!!!!!!! 
          //error_log( print_r(  'vtmin_maybe_exec_plugin_updater exit003' , true ) );          
         return;
      }

  // $subbie = ($today - $last_updater_check_ts);
      //error_log( print_r(  '$last_check= ' .$last_updater_check_ts, true ) );
      //error_log( print_r(  'Updater date difference (looking for > 39600) = ' . ($today - $last_updater_check_ts) , true ) ); 
  
      
  
       
        //***********************************************
        //only allow check every 11 hours (unless overridden by a button)
        //***********************************************
        if ( (($today - $last_updater_check_ts) > 39600 ) || //if last test was > 11 hours ago (or overridden)
              ($new_version_access_count) ) { 
          $carry_on = true;
        } else {
          //error_log( print_r(  'vtmin_maybe_exec_plugin_updater exit004' , true ) );         
          return;
        }

        //v1.1.6.3 end
        
        
          //VTMIN_PRO_VERSION only exists if PRO version is installed and active  
        //v1.1.6.3 Refactored - switched to array value for PRO version, to allow for DEACTIVATED plugin
        
        if (defined('VTMIN_PRO_VERSION')) {
          $pro_plugin_is_installed = true;
          $version = VTMIN_PRO_VERSION;   //v1.1.6.3 
        } else {
          $pro_plugin_is_installed = true;
          $version = $vtmin_setup_options['current_pro_version'];
        /* TEST TEST TEST
         //error_log( print_r(  'Pro plugin not active, get pro version from if-installed list' , true ) );  
          $pro_plugin_is_installed = vtmin_check_pro_plugin_installed();
          if ( $pro_plugin_is_installed ) {
            global $vtmin_setup_options;
            if (!$vtmin_setup_options) {
              get_option( 'vtmin_setup_options' );
            }
            $version = $pro_plugin_is_installed;
            $vtmin_setup_options['current_pro_version'] = $pro_plugin_is_installed;
          } 
          */        
        }     
      
      
        if ($pro_plugin_is_installed) {
           //error_log( print_r(  'vtmin_maybe_exec_plugin_updater Pro plugin installed, above updater check, VERSION = ' .$version, true ) );        
            //error_log( print_r(  'vtmin_maybe_exec_plugin_updater RUNNING UPDATER' , true ) );     
        	// setup the updater
        	$edd_updater = new VTMIN_Plugin_Updater( VTMIN_STORE_URL, __FILE__, array(
              //v1.1.6.3 allow for DEACTIVATED plugin
        			'version' 	=> $version, 				// current version number
            //'version' 	=> VTMIN_PRO_VERSION, 				// current version number
        		//gotten directly later
            //	'license' 	=> $license_key, 		// license key (used get_option above to retrieve from DB)
        			'item_name' => urlencode( VTMIN_ITEM_NAME ), 	// name of this plugin
        			'author' 	=> 'Vark'  // author of this plugin
        		)
        	); 
          update_option('vtmin_last_updater_check_ts', $today);  //v1.1.6.3  mark license check TS as  done   
        }
  
          //error_log( print_r(  'vtmin_maybe_exec_plugin_updater exit at BOTTOM' , true ) ); 
        
    return;  
  }


       
  /* ************************************************
  **   Admin - v1.1.5 new function
  *************************************************** */ 

  function vtmin_check_pro_plugin_installed() {
     
    // Check if get_plugins() function exists. This is required on the front end of the
    // site, since it is in a file that is normally only loaded in the admin.
    if ( ! function_exists( 'get_plugins' ) ) {
    	require_once ABSPATH . 'wp-admin/includes/plugin.php';
    }
    
    $all_plugins = get_plugins();

    foreach ($all_plugins as $key => $data) { 
      if ($key == VTMIN_PRO_PLUGIN_FOLDER.'/'.VTMIN_PRO_PLUGIN_FILE) {
        //v1.1.6.3 refactored
        //return true; 
        if ( (isset($data['Version'])) &&
             ($data['Version'] > null) ) {
          return  $data['Version']; //v1.1.6.3  handles when PRO plugin installed but NOT ACTIVE     
        } else {
          return true;
        }
        
      } 
    } 
    
    return false;  
 
  }
  
     
  //****************************
  //  suspended PRO plugin is DEACTIVATED
  //****************************
  function vtmin_deactivate_pro_plugin() {
    //deactivate the PRO plugin, having FAILED licensing
    $plugin = VTMIN_PRO_PLUGIN_SLUG;
    if( is_plugin_active($plugin) ) {
	    deactivate_plugins( $plugin );
      vtmin_increment_license_count();
    }
  }
  
  
  function  vtmin_maybe_license_state_message() { 
     //error_log( print_r(  'Begin vtmin_maybe_license_state_message', true ) );
      global $vtmin_license_options;
      
      $pro_plugin_is_installed = vtmin_check_pro_plugin_installed();
      if (!$pro_plugin_is_installed) {
        if ($vtmin_license_options['state'] == 'suspended-by-vendor') {
          ?> <p class="yellow" id="license-status-msg"><strong> <?php // echo VTMIN_ITEM_NAME; ?> Pro Plugin ** not installed **, no action required.  <br><br> However, license was previously suspended by vendor </strong></p> 
          <br><br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
          <a  href=" <?php echo VTMIN_PURCHASE_PRO_VERSION_BY_PARENT ; ?> "  title="Purchase a full Pro license"><?php _e('Purchase a full Pro license', 'vtmin'); ?></a>
          <?php
        
        } else {
          ?> <p class="green" id="license-status-msg"><strong> <?php // echo VTMIN_ITEM_NAME; ?> Pro Plugin not installed, no action required. 
          <br><br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
          <a  href=" <?php echo VTMIN_PURCHASE_PRO_VERSION_BY_PARENT ; ?> "  title="Purchase a full Pro license"><?php _e('Purchase a full Pro license', 'vtmin'); ?></a>

          </strong></p><br> <?php 
        }
  //removed 1.1.6.7
  /*
            &nbsp; or &nbsp; 
          Get a 3-Day Full Free Demo License
  */       
        return; 
        
      }

      //plugin is installed, if not active, and always gets the suspended message      
      if ($vtmin_license_options['state'] == 'suspended-by-vendor') {
        vtmin_license_suspended_message();
        return;
      }
 
      if ($vtmin_license_options['diagnostic_msg'] == 'demo_license_expired') {
        //vtmin_deactivate_pro_plugin();
        vtmin_demo_license_expired_message();
        return;
      }     
      
        //VTMIN_PRO_VERSION only exists if PRO version is installed and active
      //if not active, no other messages are displayed here.
      if (!defined('VTMIN_PRO_VERSION')) {  // changed v1.1.6
        return;
      }
      
      //**********************
      //FORBIDDEN - SECURITY message cutout
      //**********************
      
  // //error_log( print_r(  '$vtmin_license_options = ' , true ) );
  // //error_log( var_export($vtmin_license_options, true ) );        
      
      //v1.1.6.3 IF the return is an object, it came from inside Software Licensing, and this test IS NOT NECESSARY!
      if ( ( isset($vtmin_license_options['last_response_from_host']) ) &&
           (!is_object($vtmin_license_options['last_response_from_host']) ) ) {
          //v1.1.6.2 - IF refactored
        if ( ( isset($vtmin_license_options['last_response_from_host']['response']['code']) )
                       &&
            ( ($vtmin_license_options['last_response_from_host']['response']['code'] == '403' ) ||
             ($vtmin_license_options['last_response_from_host']['response']['code'] == '500' ) ) ) {
          ?> <p class="red" id="license-status-msg"><strong>Activation request temporarily blocked by Varktech security. Please click "Show Licensing Info", copy info. Contact <a href="https://www.varktech.com/support"  title="Support">VarkTech Support</a>, paste copied information into email.</strong></p> <?php
          return;
        } 
      }
              
      switch ( $vtmin_license_options['prod_or_test'] ) { 
            case 'prod' : 
                $prod_or_test = ' - Production Site - ';
              break;
            case 'test' : 
                $prod_or_test = ' - Test Site - ';
              break;      
            case 'demo' : 
                $prod_or_test = ' - 3-Day Full Free Demo License - ';
              break;                
      }

     
      if ($vtmin_license_options['status'] == 'valid') {   
          switch ( $vtmin_license_options['state'] ) { 
            case 'active' : 
                if ( ($vtmin_license_options['last_action'] == 'activate_license') ||
                     ($vtmin_license_options['last_action'] == 'check_license') ) {
                  ?> <p class="green" id="license-status-msg"><strong> <?php // echo VTMIN_ITEM_NAME . $prod_or_test; ?> <em> Activated Successfully! </em> </strong></p> <?php    
                  
                 // v1.1.6 ON HOLD vtmin_show_update_check_button(); //v1.1.6
                  
                } else { //tried to deactivate
                  ?> <p class="red" id="license-status-msg"><strong> <?php // echo VTMIN_ITEM_NAME . $prod_or_test; ?> Deactivation failed, please try again! </strong></p> <?php 
                }
                
              break;
            case 'deactivated' : 
                if ( ($vtmin_license_options['last_action'] == 'deactivate_license') ||
                     ($vtmin_license_options['last_action'] == 'check_license') ) {
                  ?> <p class="green" id="license-status-msg"><strong> <?php // echo VTMIN_ITEM_NAME . $prod_or_test; ?> <em> Deactivated Successfully! </em> </strong>
                     </p>                  
                  <?php    
                } else { //tried to activate
                  ?> <p class="green" id="license-status-msg"><strong> <?php // echo VTMIN_ITEM_NAME . $prod_or_test; ?> Activation failed, please try again! </strong></p> <?php 
                }
              break;                    

            default:                   
                //can't be any other state!!
              break;
          }
      } else { //'invalid' OR 'unregistered' path ==>> can't have a state of active or deactivated    
         switch ( $vtmin_license_options['state'] ) {  
          case 'unregistered' : 
              if ($vtmin_license_options['last_action'] > ' ') {
                ?> <p class="red" id="license-status-msg"><strong> <?php // echo VTMIN_ITEM_NAME . $prod_or_test; ?> Failed to Connect to Host.  Please Try Again. </strong></p> <?php
              } else { //no action taken yet
                if ($vtmin_license_options['pro_plugin_version_status'] == 'valid') {  //version status test is done IN ADVANCE of registration!
                ?> <p class="yellow" id="license-status-msg"><strong>License key registration required -  Pro Plugin will not function until registered. </strong>
                
                    <?php                     
                      $message =  '<strong>';       
                      $message .=  '<br><br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;' . __('- Register with a License Key ', 'vtmin') ;
                      $message .=  '<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'     . __('- OR with a SessionID.', 'vtmin') ;
                      $message .=  '</strong>';
                      $message .=  '<br><br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;' . __('- If you do not have either ID, Go to <a href="https://www.varktech.com">Varktech.com</a>', 'vtmin') ;
                      $message .=  '<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'     . __('- Log In and get your License Key to Register.', 'vtmin') ;
                      $message .=  '<br><br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;' . __('- OR for older purchases, <em>where a SessionID was furnished</em>,', 'vtmin') ; 
                      $message .=  '<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'     . __('- by Name and Email Address', 'vtmin') .'&nbsp;&nbsp;&nbsp; <a href="https://www.varktech.com/your-account/license-lookup/">License Key Lookup by Name and Email</a>' ;                                           
                      echo $message;                
                     ?>            
                  </p> <?php
                }
              }
              
            break;
          case 'pending' :
              switch ( $vtmin_license_options['last_action'] ) {  
                  case 'activate_license' :
                  case 'check_license' :
                      ?> <p class="yellow" id="license-status-msg" style="font-size:12px;"><strong> <?php echo VTMIN_ITEM_NAME . $prod_or_test; ?> is in a Pending Activation state. <br><br> Please edit the Licensing Information and then activate. </strong></p> <?php
                     break;
                  case 'deactivate_license' :
                      ?> <p class="yellow" id="license-status-msg" style="font-size:12px;"><strong> <?php echo VTMIN_ITEM_NAME . $prod_or_test; ?> is in a Pending Deactivation state. <br><br> Please edit the Licensing Information and then deactivate. </strong></p> <?php
                     break;                             
              }
            break;                    
          case 'suspended-by-vendor' :
              vtmin_license_suspended_message();
            break;                    
    
        }  
    }
    
    return;
  }
 

  function  vtmin_demo_license_expired_message() { 
       //error_log( print_r(  'Begin vtmin_demo_license_expired_message', true ) );
    
    global $vtmin_license_options;
    
    ?> <p class="yellow" id="license-status-msg"><strong>

        &nbsp;&nbsp;&nbsp;   3-Day Demo license has expired. 

        <br><br>&nbsp;&nbsp;&nbsp;
         <a  href=" <?php echo VTMIN_PURCHASE_PRO_VERSION_BY_PARENT ; ?> "  title="Purchase a full Pro license:"><?php _e('Purchase a full Pro license', 'vtmin'); ?></a>

        <span style="color:black !important; font-size:14px; ">
         
          and then:
          
        <br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;  1.&nbsp; Just enter the new Pro License Key and purchasing email address
        <br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;  2.&nbsp; Select "Activation Type" of "Production Site" (or "Test Site", if desired)
        <br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;  3.&nbsp; Click 'Activate License'

        </span>
        </strong></p> <?php      
    return;
  } 
  
  function  vtmin_license_suspended_message() { 
        //error_log( print_r(  'Begin vtmin_license_suspended_message', true ) );
    
    global $vtmin_license_options;
    
    ?> <p class="red" id="license-status-msg"><strong>
        <span style="color:black !important;">
        <?php echo '<br>&nbsp;&nbsp;&nbsp; ' .VTMIN_ITEM_NAME;  ?> 
        </span>
        
        <br><br>&nbsp;&nbsp;&nbsp;  <em>*** License Suspended by Vendor due to a breach of Licensing Rules. ***</em>

        <br><br>&nbsp;&nbsp;&nbsp;  <em>*** Pro Plugin Deactivated ***</em> 
        
        <span style="color:black !important; font-size:14px; "> 
 
        <br><br>&nbsp;&nbsp;&nbsp; License suspended because of too many License activations, or a number of failed attempts at License activation:

        <br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;  1. Multiple failed attempts at a <em>Single Site</em> &nbsp;&nbsp; (or a 3-day Demo license has expired)
        <br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;  - OR -
        <br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;  2. <em>Multiple production/test sites</em> have attempted to <em>register with the same single-site license key</em>.
        
        <span style="color:grey !important;"> 
        <br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;  (with a Single-Site License, you are allowed ONE Production and One Test Site Registration.)
        </span>
        
        <span style="background-color: RGB(255, 255, 180) !important;"> 
        <br><br>&nbsp;&nbsp;&nbsp; For Assistance, Please contact <a  href="https://www.varktech.com/support"  title="Support">www.varktech.com/support</a> and supply the following Information: 
        </span>
        <br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;  1. Licensing info - copy using Button "Show licensing Info" below -
        <br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;  Follow the copy directions, and paste the info into your email to varktech.
        <br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;  2. License purchaser name and address, as supplied at purchase time.
        
        <span style="color:grey !important;"> 
        <br><br><em>&nbsp;&nbsp;&nbsp; (This message displays when the Pro version is installed, regardless of whether it's active)</em>
        </span>

        </span>
        <br><br></strong></p> <?php      
    return;
  }

  //*****************************************
	//CLEAR data on PRO plugin DELETE, to get rid of stored status used in VERION Comparison
  //*****************************************
  function vtmin_maybe_delete_pro_plugin_action() {
   
      $pageURL = $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"]; 
      //error_log( print_r(  'Begin vtmin_maybe_delete_pro_plugin_action, URL= ' .$pageURL , true ) );
  
      if ( (strpos($pageURL,'plugins.php') !== false)  &&
           (strpos($pageURL,'delete-selected') !== false) &&
           (strpos($pageURL,VTMIN_PRO_PLUGIN_FILE) !== false) ) {
          $carry_on = true;
      } else {
     //error_log( print_r(  'vtmin_maybe_delete_pro_plugin_action ENTRY exit =  ' , true ) );        
        return;
    	}    
    	//if( ! current_user_can( 'install_plugins' ) ) {      //v1.1.6
      if ( !current_user_can( 'edit_posts', 'vtmin-rule' ) ) { //v1.1.6
      //error_log( print_r(  'vtmin_maybe_delete_pro_plugin_action permission exit =  ' , true ) );      
    		return;
    	}    

      
      //this check prevents a recurrence, as this is executed twice during a delete action...
      $vtmin_license_options = get_option( 'vtmin_license_options' );
      if ($vtmin_license_options['pro_version'] > null) {
        update_option('vtmin_pro_plugin_deleted', 'yes');
      }

      /*  this update yields inaccurate results.  Moved to main plugin file.    
      $vtmin_license_options['pro_version'] = null;      
      $vtmin_license_options['pro_plugin_version_status'] = null;
      $vtmin_license_options['pro_minimum_free_version'] = null;
      */
       
  
   return;
  }  
  
  //increment license count when SUSPENDED
  function vtmin_increment_license_count() { 
      $vtmin_license_count = get_option( 'vtmin_license_count');
      if (!$vtmin_license_count) {  
        $vtmin_license_count = 0;
      }
      $vtmin_license_count++;
      update_option('vtmin_license_count', $vtmin_license_count);      
   return;
  }
  
  //increment license count when SUSPENDED
  function vtmin_update_notice() {
  	$info = '<br>After this update, PRO version will require Registration. <br>Please have your License ID/Session ID and Purchaser Email ready.';
  	echo '<span class="spam">' . strip_tags( $info, '<br><a><b><i><span>' ) . '</span>';      
   return;
  }

  //*********************************************
  //v1.1.6 new function
  //v1.1.6.3 refactored
  //*********************************************
  function vtmin_force_plugin_updates_check() { 
      //error_log( print_r(  'Begin vtmin_force_plugin_updates_check ', true ) );
     
     //v1.1.8.2 begin
     // only allow auto updates in PROD sites
     global $vtmin_license_options;
     if (!$vtmin_license_options) {
        $vtmin_license_options = get_option( 'vtmin_license_options' ); 
     }
     if ($vtmin_license_options['prod_or_test'] == 'prod') {
     //v1.1.8.2 end
         
          update_option('vtmin_last_updater_check_ts', 1435870883 );   //set older date = 1435870883 (july 2015) to allow immediate plugin update check          
    
          //from plugin "Force Plugin Updates Check", hook in main plugin file
        	set_site_transient( 'update_plugins', null ); 
     
     } //v1.1.8.2

    //error_log( print_r(  'would have gone to update-core ', true ) ); 
     wp_safe_redirect( network_admin_url( 'plugins.php' ) ); //v1.1.8.2 changed to bounce right back to the plugins page!    
  	//wp_safe_redirect( network_admin_url( 'update-core.php' ) );
     
    exit; 

  }     

 

  /* ************************************************
  **   Admin - v1.1.5 new function, run at admin init
  ***    refactored v1.1.6, move HERE from main file, to allow access from Cron job 
  * //only runs if PRO version is installed and active  
  * //v1.1.6.3  REFACTORED  
  * removed the 'maybe' function, combined the two functions
  * now *both* admin call and cron call act as backup to plugin update checks!      
  *************************************************** */ 
	function vtmin_recheck_license_activation() {
       //error_log( print_r(  'Begin vtmin_recheck_license_activation' , true ) ); 
   
    //- is Woocommerce installed
    if ( ! class_exists( 'WooCommerce' ) )  {
      return;
    }

    //VTMIN_PRO_VERSION only exists if PRO version is installed and active
    if ((!defined('VTMIN_PRO_VERSION')) )  { 
          //error_log( print_r(  'Begin vtmin_maybe_recheck_license_activation exit001' , true ) );          
      return;
    }

    
      //error_log( print_r(  'Begin vtmin_maybe_admin_recheck_license_activation ADMIN RECHECK' , true ) );  
   
     global $vtmin_license_options;
     if (!$vtmin_license_options) {
        $vtmin_license_options = get_option( 'vtmin_license_options' ); 
     }
            
     if ( ($vtmin_license_options['status'] == 'valid') && 
          ($vtmin_license_options['state']  == 'active') && //if license is deactivated, pro is not loaded!!
          ($vtmin_license_options['pro_plugin_version_status'] == 'valid')  )  {                          
       $carry_on = true; 
      } else {
      //error_log( print_r(  'Begin vtmin_maybe_recheck_license_activation exit002' , true ) );        
        return;
      }
   
      $last_check = get_option( 'vtmin_last_license_check_ts' );
      
      $today = time(); 
      
      //run the check first time, then every 12 thereafter
      if (!is_numeric($last_check)) {  
         update_option('vtmin_last_license_check_ts', $today);  //v1.1.6.2 just update the TS if not there, 1st-time activation will be in progress!!!!!!!
      //error_log( print_r(  'vtmin_maybe_recheck_license_activation exit003' , true ) );  
         return;
      }
      
  //$last_check=1465603200;
  //$subbie = ($today - $last_check);
      //error_log( print_r(  '$last_check= ' .$last_check, true ) );
      //error_log( print_r(  'Begin vtmin_maybe_recheck_license_activation date difference= ' .$subbie , true ) );  
      
      
      
      //***********************************************
      //check every 13 hours
      // vtmin_last_license_check_ts is updated with *each execution* of  vtmin_recheck_license_activation below
      // SO the following actually only runs if the cron scheduler which executes vtmin_recheck_license_activation everry 8-12hrs DOES NOT RUN
      // It's a fallback, Luke!
      //***********************************************
      if (($today - $last_check) < 46800 )  { //13 hours...
      //error_log( print_r(  'vtmin_maybe_recheck_license_activation difference greater than 13 hours' , true ) );        
        return;
      } 
      
      
       update_option('vtmin_last_license_check_ts', $today);  //v1.1.6.7 moved here

  
        //PHONE HOME and UPDATE 
  
       $vtmin_license_options_screen = new VTMIN_License_Options_screen;
  
       $skip_admin_check = 'yes';    
       
       //***********************************
       //v1.1.6 begin
       //makes sure the TS is updated after phone_home
       //Only possible when the PRO PLUGIN is INSTALLED AND ACTIVE
 
       $vtmin_license_options = $vtmin_license_options_screen->vtmin_license_phone_home($vtmin_license_options, 'check_license', $skip_admin_check);
   
 
       if ($vtmin_license_options['status'] == 'invalid') {
          //Can't update vtmin_license_options here, things explode!! Store for update in main plugin php file
         update_option('vtmin_license_suspended', $vtmin_license_options);
              //error_log( print_r(  'created vtmin_license_suspended', true ) );       
       }  

      //v1.1.6.7 moved above
      // update_option('vtmin_last_license_check_ts', $today);

     
      //error_log( print_r(  'Begin vtmin_maybe_recheck_license_activation at BOTTOM' , true ) ); 
  
   return;
  } 

  //*********************************************
  //v1.1.8.2 new function
  //*********************************************
  function vtmin_return_license_info() { 
        global $vtmin_license_options;    
        $send_data  = '### Begin Licensing Info ###' . "\n\n";

      	$send_data .= 'Home URL:                 ' . $vtmin_license_options['url'] . "\n";
        $send_data .= 'Plugin Name:              ' . VTMIN_ITEM_NAME . "\n";
        $send_data .= 'Status:                   ' . $vtmin_license_options['status'] . "\n";
        $send_data .= 'State:                    ' . $vtmin_license_options['state'] . "\n";
        $send_data .= 'Message:                  ' . $vtmin_license_options['msg'] . "\n";
      	$send_data .= 'Key:                      ' . $vtmin_license_options['key'] . "\n";
        $send_data .= 'Email:                    ' . $vtmin_license_options['email'] . "\n";
        $send_data .= 'Activation Type:          ' . $vtmin_license_options['prod_or_test'] . "\n";
        //$send_data .= 'Prod Site URL (if test):  ' . $vtmin_license_options['prod_url_supplied_for_test_site'] . "\n"; v1.1.8.2 removed
        $send_data .= 'Strikes:                  ' . $vtmin_license_options['strikes'] . "\n"; 
        $send_data .= 'Last Action:              ' . $vtmin_license_options['last_action'] . "\n"; 
        $send_data .= 'Last good attempt:        ' . $vtmin_license_options['last_successful_rego_date_time'] . "\n";
        $send_data .= 'Last failed attempt:      ' . $vtmin_license_options['last_failed_rego_date_time'] . "\n"; 
        $send_data .= 'Expires:                  ' . $vtmin_license_options['expires'] . "\n"; 
        $send_data .= 'Plugin Item ID:           ' . VTMIN_ITEM_ID . "\n";
       // $send_data .= 'Plugin Item ID Demo:      ' . VTMIN_ITEM_ID_DEMO . "\n";  //removed 1.1.6.7
        $send_data .= 'Registering To:           ' . VTMIN_STORE_URL . "\n"; 
        $send_data .= 'Diagnostic Message:       ' . $vtmin_license_options['diagnostic_msg'] . "\n";
                      
        $send_data .= 'Pro Current Version:      ' . $vtmin_license_options['plugin_current_version'] . "\n";  //used by plugin updater only
        $send_data .= 'Pro New Version:          ' . $vtmin_license_options['plugin_new_version'] . "\n";
        
        $send_data .= 'Pro Version:              ' . $vtmin_license_options['pro_version'] . "\n";
        $send_data .= 'Pro Required Version:     ' . VTMIN_MINIMUM_PRO_VERSION . "\n";
        $send_data .= 'Pro Version Status:       ' . $vtmin_license_options['pro_plugin_version_status'] . "\n";
        
        $send_data .= 'Free Current Version:     ' . VTMIN_VERSION . "\n";
        $send_data .= 'Free Required Version:    ' . $vtmin_license_options['pro_minimum_free_version'] . "\n";
        
        $count = get_option( 'vtmin_license_count');
        $send_data .= 'License Count:            ' . $count . "\n";
        $send_data .= 'Pro Deactivate Flag:      ' . $vtmin_license_options['pro_deactivate'] . "\n";
        $send_data .= 'IP Address:               ' . vtmin_get_ip_address() . "\n"; //v1.1.8.2
        
       // $send_data .= 'Updater Reduced Frequency:' . ' ' . $updater_action_reduced_frequency . "\n";
        
        $last_check = get_option('vtmin_last_license_check_ts');
        $last_check_formatted   = is_numeric( $last_check ) ? date( 'Y-m-d H:i:s', $last_check ) : $last_check; 
         
        $send_data .= 'Last Check_License TS:    ' . $last_check_formatted . "\n";              
         
        $send_data .= 'Home URL (for anchors):   ' . $vtmin_license_options['home_url'] . "\n"; //v1.1.6.1
        $send_data .= 'Rego Done Flag:           ' . $vtmin_license_options['rego_done'] . "\n"; //v1.1.6.1

        $send_data .= "\n \n \n";
        
        $send_data .= 'Last Response from Host: <pre>'.print_r($vtmin_license_options['last_response_from_host'], true).'</pre>'  . "\n" ;     

        $send_data .= "\n \n \n";
        
        $send_data .= 'Last Parameters sent to Host: <pre>'.print_r($vtmin_license_options['params_sent_to_host'], true).'</pre>'  . "\n" ;   
   return $send_data;                          
  }     

  
 
  /*
  //from https://code.garyjones.co.uk/get-wordpress-plugin-version
   function vtmin_get_pro_plugin_version() {
    	if ( ! function_exists( 'get_plugins' ) )
    		require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
    	$plugin_folder = get_plugins( '/' .VTMIN_PRO_PLUGIN_FOLDER);
    	return $plugin_folder[VTMIN_PRO_PLUGIN_FILE]['Version'];
  }
  */
