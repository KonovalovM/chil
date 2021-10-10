<?php

/**
* Shortcodes: Don't auto-p wrap shortcodes that stand alone
*/
function themeworks_shortcodes_unautop() {
    add_filter( 'the_content', 'shortcode_unautop' );
}
add_action( 'init', 'themeworks_shortcodes_unautop' );

/**
* Add the shortcodes
*/
function themeworks_shortcodes() {

    add_filter( 'the_content', 'shortcode_unautop' );

    add_shortcode( 'tw_contact_form', 'themeworks_contact_form_shortcode' );
    add_shortcode( 'tw_grid', 'themeworks_grid_shortcode' );
    add_shortcode( 'tw_dropcap', 'themeworks_dropcap_shortcode');
    add_shortcode( 'tw_button', 'themeworks_button_shortcode');
    add_shortcode( 'tw_icon', 'themeworks_icon_shortcode' );
    add_shortcode( 'tw_box', 'themeworks_box_shortcode' );
    add_shortcode( 'tw_highlight', 'themeworks_highlight_shortcode' );
    add_shortcode( 'tw_divider', 'themeworks_divider_shortcode' );
    add_shortcode( 'tw_pricing', 'themeworks_pricing_shortcode' );
    add_shortcode( 'tw_signup', 'themeworks_signup_shortcode' );

    // Long posts should require a higher limit, see http://core.trac.wordpress.org/ticket/8553
    @ini_set( 'pcre.backtrack_limit', 500000 );
}
add_action( 'wp_head', 'themeworks_shortcodes' );

/**
 * Contact Form
 */

function themeworks_contact_form_shortcode() {

    $html = NULL;
    $html .= '<div class="contact-form">' . "\n";
    $html .= "\t" . '<form class="themeworks-contact-form">' . "\n";
    $html .= "\t\t" . '<input type="text" name="contactHide" id="contactHide" class="contactHide hide" value="" />' . "\n";
    $html .= "\t\t" . '<div class="contact-form-input fields">' . "\n";
    $html .= "\t\t\t" . '<input type="text" name="contact-name" class="contact-name required" placeholder="' . __( 'Name', 'themeworks' ) . ' *" />' . "\n";
    $html .= "\t\t" . '</div>' . "\n";
    $html .= "\t\t" . '<div class="contact-form-input fields">' . "\n";
    $html .= "\t\t\t" . '<input type="text" name="contact-email" class="contact-email required" placeholder="' . __( 'Email Address', 'themeworks' ) . ' *" />' . "\n";
    $html .= "\t\t" . '</div>' . "\n";
    $html .= "\t\t" . '<div class="contact-form-textarea fields">' . "\n";
    $html .= "\t\t\t" . '<textarea name="contact-message" class="contact-message required" placeholder="' . __( 'Your Message', 'themeworks' ) . ' *"></textarea>' . "\n";
    $html .= "\t\t" . '</div>' . "\n";
    $html .= "\t\t" . '<div class="contact-form-submit fields">' . "\n";
    $html .= "\t\t\t" . '<p class="contact-all-required contact-error hide">' . __( 'All fields required!', 'themeworks' ) . '</p>' . "\n";
    $html .= "\t\t\t" . '<a id="submitButton" href="#" class="progress-button button" data-loading="' . __( 'Processing...', 'themeworks' ) . '"  data-finished="' . __( 'Your message was sent.', 'themeworks' ) . '!" data-success="' . __( 'Your message was sent.', 'themeworks' ) . '" data-type="background-bar" data-fail="' . __( 'Failed, please try again', 'themeworks' ) . '!">' . __( 'Submit', 'themeworks' ) . '</a>' . "\n";
    $html .= "\t\t" . '</div>' . "\n";
    $html .= "\t" . '</form>' . "\n";
    $html .= '</div>' . "\n";

    return $html;
}

/**
 * Ajax callback to add users to mailchimp
 */
function themeworks_contact_processor() {

    global $theme_options;
    $email      = $_POST['email'];
    $name       = $_POST['name'];
    $msg        = $_POST['message'];

    $site_name = esc_attr( get_bloginfo( 'name' ) );

    if ( isset ( $theme_options['contact_email'] ) && ! empty ( $theme_options['contact_email'] ) ) {
        $send_to = $theme_options['contact_email'];
    } else {
        $send_to = get_option( 'admin_email' );
    }


    $message['headers'] = "From: " . $name . " <" .  $email . ">\r\n";
    $message['headers'] .= "Reply-To: ". $email . "\r\n";
    $message['headers'] .= "MIME-Version: 1.0\r\n";
    $message['headers'] .= "Content-Type: text/html; charset=utf-8\r\n";

    // Send the email
    if ( wp_mail( $send_to, 'Message from '. $site_name, nl2br( $msg ), $message['headers'] ) ) {
        echo "success";
    } else {
        echo "fail";
    }

    die;
}
add_action( 'wp_ajax_themeworks_contact', 'themeworks_contact_processor' );
add_action( 'wp_ajax_nopriv_themeworks_contact', 'themeworks_contact_processor' );

/**
 * Grids
 */
$GLOBALS['cnt'] = 0;
function themeworks_grid_shortcode( $atts, $content = null ) {

    extract( shortcode_atts( array(
        'cols' => '1'
    ), $atts ) );

    if ( 5 == $cols ) {
        $class = 'five-col';
    } elseif ( 4 == $cols ) {
        $class = 'four-col';
    } elseif ( 3 == $cols ){
        $class = 'three-col';
    } elseif ( 2 == $cols ) {
        $class = 'two-col';
    } else {
        $class = 'one-col';
    }

    $html = NULL;
    if ( 0 == $GLOBALS['cnt'] ) {
        $html = '<div class="themeworks-grid-container">';
    }
    $html .= '<div class="col ' . $class . '">';
    $html .= do_shortcode( $content );
    $html .= '</div>';
    $GLOBALS['cnt']++;
    if ( $cols == $GLOBALS['cnt'] ) {
        $html .= '</div>';
        $GLOBALS['cnt'] = 0;
    }

    return $html;
}

/**
 * Dropcaps
 * @since 1.1
 *
 */

if ( !function_exists( 'themeworks_dropcap_shortcode' ) ) {
    function themeworks_dropcap_shortcode( $atts, $content = null ) {
        extract(shortcode_atts(array(
            "size"   => '',
            "color"  => ''
       ), $atts));

        $size = ( $size ) ? 'font-size:' . $size . ';width:' . $size . ';height:' . $size : null;
        $color = ( $color ) ? 'color:' . $color : null;

        return '<span class="dropcap" style="' . $color . $size . '">' . do_shortcode( $content ) . '</span>';

    }
}

/**
 * Buttons
 * @since 1.1
 *
 */

if ( !function_exists( 'themeworks_button_shortcode' ) ) {
    function themeworks_button_shortcode( $atts, $content = null ) {
        extract( shortcode_atts( array(
            'url' => '#',
            'title' => '',
            'target' => 'self',
            'rel' => '',
            'class' => ''
        ), $atts ) );

        $rel = ( $rel ) ? 'rel="' . $rel . '"' : NULL;

        $html = NULL;
        $html .= '<a href="' . $url . '" class="button ' . $class . '" target="_' . $target . '" title="' . $title . '" rel="' . $rel . '">';
        $html .= do_shortcode( $content );
        $html .= '</a>';

        return $html;
    }
}

/**
 * Icons
 * @since 1.1
 *
 */

if ( !function_exists( 'themeworks_icon_shortcode' ) ) {
    function themeworks_icon_shortcode( $atts, $content = null ) {
        extract(shortcode_atts(array(
            "type" => 'twitter',
            "size"   => '32px',
            "color"  => ''
       ), $atts));

        return '<span class="genericon genericon-' . $type . '" style="color:' . $color . ';font-size:' . $size . ';width:' . $size . ';height:' . $size . ';"></span>';

    }
}

/**
 * Alert Boxes
 * @since 1.1
 *
 */

if ( !function_exists( 'themeworks_box_shortcode' ) ) {
    function themeworks_box_shortcode( $atts, $content = null ) {
        extract( shortcode_atts( array(
            'color' => '',
            'background' => '',
            'float' => 'left',
            'text_align' => 'center',
            'width' => '100%',
            'margin_top' => '',
            'margin_bottom' => '',
            'class' => '',
          ), $atts ) );

            $style_attr = '';
            if ( $margin_bottom ) {
                $style_attr .= 'margin-bottom: '. $margin_bottom .';';
            }
            if ( $margin_top ) {
                $style_attr .= 'margin-top: '. $margin_top .';';
            }

          $html = '';
          $html .= '<div class="themeworks-box align' . $float . ' ' . $class . '" style="float:' . $float . '; text-align:' . $text_align . '; background-color:' . $background . '; color:' . $color . '; width:' . $width . ';' . $style_attr . '">';
          $html .= ' '. do_shortcode( $content ) .'</div>';

          return $html;
    }
}

/**
 * Highlights
 * @since 1.1
 *
 */

if ( !function_exists( 'themeworks_highlight_shortcode' ) ) {
    function themeworks_highlight_shortcode( $atts, $content = null ) {
        extract( shortcode_atts( array(
            'background' => 'yellow',
            'class' => '',
          ),
          $atts ) );
          return '<span class="themeworks-highlight ' . $class . '" style="background-color:' . $background . ';">' . do_shortcode( $content ) . '</span>';

    }
}

/**
 * Divider
 * @since 1.1
 *
 */

if ( !function_exists( 'themeworks_divider_shortcode' ) ) {
    function themeworks_divider_shortcode( $atts ) {
        extract( shortcode_atts( array(
            'type' => 'solid',
            'margin_top' => '4em',
            'margin_bottom' => '4em',
            'class' => '',
        ), $atts ) );
        $style_attr = '';
        if ( $margin_top && $margin_bottom ) {
            $style_attr = 'margin-top: ' . $margin_top . ';margin-bottom: ' . $margin_bottom . ';';
        } elseif ( $margin_bottom ) {
            $style_attr = 'margin-bottom: ' . $margin_bottom . ';';
        } elseif ( $margin_top ) {
            $style_attr = 'margin-top: ' . $margin_top . ';';
        } else {
            $style_attr = NULL;
        }
     return '<hr class="themeworks-divider ' . $type . ' ' . $class . '" style="' . $style_attr . '" />';
    }
}

/**
 * Pricing Table
 * @since 1.1
 *
 */

if ( !function_exists( 'themeworks_pricing_shortcode' ) ) {
    function themeworks_pricing_shortcode( $atts, $content = null  ) {

        extract( shortcode_atts( array(
            'color'         => '',
            'featured'      => 'no',
            'plan'          => __( 'Basic', 'themeworks' ),
            'cost'          => '$20',
            'per'           => __( 'month', 'themeworks' ),
            'button_url'    => '',
            'button_text'   => __( 'Sign Up', 'themeworks' ),
            'button_target' => 'self',
            'button_rel'    => 'nofollow',
            'class'         => ''
        ), $atts ) );

        //set variables
        $featured = ( $featured == 'yes' ) ? 'featured' : NULL;
        $color = ( ! empty ( $color )  ) ? 'style="background:'.$color.'"' : NULL;

        //start content
        $html = '';
        $html .= '<div class="themeworks-pricing-table ' . $featured . ' ' . $class . '">';
        $html .= '<header '. $color .'>';
        $html .= '<h3 class="themeworks-pricing-title">' . $plan . '</h3>';
        $html .= '<div class="price">' . $cost . '</div>';
        $html .= '<div class="per">' . $per . '</div>';
        $html .= '</header>';
        $html .= '<section>';
        $html .= '' . $content . '';
        $html .= '</section>';
        if ( $button_url ) {
            $html .= '<footer><a href="' . $button_url . '" class="button" target="_' . $button_target . '" rel="' . $button_rel . '">' . $button_text . '</a></footer>';
        }
        $html .= '</div>';

        return $html;
    }

}

/**
 * Signup
 * @since 1.1
 *
 */

if ( !function_exists( 'themeworks_signup_shortcode' ) ) {

    function themeworks_signup_shortcode( $atts, $content = null ) {

        extract( shortcode_atts( array(
            'display'       => 'inline',
            'location'      => '',
            'message'       => 1
        ), $atts ) );

        global $theme_options;
        $signup_bg      = ( ! empty( $theme_options['signup_bg_image'] ) ) ? "background:url( '" . $theme_options['signup_bg_image'] . "') no-repeat center center; background-size:cover" : "background:" . $theme_options['signup_bg_color'];
        $signup_note    = ( ! empty( $theme_options['signup_note'] ) ) ? '<p><strong>' . stripslashes_deep( $theme_options['signup_note'] ) . '</strong></p>' : '';
        $fields         = ( ! empty( $theme_options['signup_name'] ) ) ? true : false;
        $class          = ( $fields ) ? 'four-col' : 'two-col';
        // make buttons span full-width in header
        if ( 'header' == $location ) {
            $class = null;
        }
        $html = NULL;
        $html .= '<div id="section-signup" class="signup" style="' . $signup_bg . '">' . "\n";

        if ( $message ) {
            $html .= "\t" . '<div class="section-content text-center">' . "\n";
            $html .= "\t\t" . '<div class="section-signup-note xl">' . "\n";
            $html .= "\t\t\t" . $signup_note . "\n";
            $html .= "\t\t" . '</div>' . "\n";
        }

        $html .= "\t\t" . '<div class="section-signup-form">' . "\n";
        $html .= "\t\t\t" . '<form id="signup" class="signup-form-' . $display . '" name="signup">' . "\n";

        if ( ! empty( $theme_options['signup_name'] ) ) {
            $html .= "\t\t\t\t" . '<input class="text firstname signup-fields ' . $display . ' ' . $class . '" name="firstname" type="text" placeholder="' . __( 'First Name', 'themeworks' ) . '" />' . "\n";
            $html .= "\t\t\t\t" . '<input class="text lastname signup-fields ' . $display . ' ' . $class . '" name="lastname" type="text" placeholder="' . __( 'Last Name', 'themeworks' ) . '" />' . "\n";
        }

        $html .= "\t\t\t\t\t" . '<input class="email text signup-fields ' . $display . ' ' . $class . '" name="email" type="text" placeholder="' . __( 'Email Address', 'themeworks' ) . '"/>' . "\n";
        $html .= "\t\t\t\t\t" . '<a id="submitButton" href="javascript:void(0)" class="progress-button signup-button signup-fields button ' . $display . ' ' . $class . '" data-loading="' . __( 'Processing...', 'themeworks' ) . '"  data-finished="' . __( 'Thanks for signing up', 'themeworks' ) . '!" data-success="' . __( 'Thanks for signing up', 'themeworks' ) . '!" data-type="background-bar" data-fail="' . __( 'Sorry, try again', 'themeworks' ) . '!">' . __( 'Sign up', 'themeworks' ) . '</a>' . "\n";
        $html .= "\t\t\t" . '</form>' . "\n";
        $html .= "\t\t" . '</div>' . "\n";
        $html .= "\t" . '</div>' . "\n";
        $html .= '</div>' . "\n";

        return $html;
    }

}

/**
 * Add Tinymce button for shortcodes
 * @since 1.0
 *
 */
new ThemeWorks_Shortcode_Tinymce();
class ThemeWorks_Shortcode_Tinymce {
    public function __construct() {
        add_action( 'admin_init', array( $this, 'shortcode_button' ) );
        add_action( 'admin_footer', array( $this, 'get_shortcodes' ) );
        add_action( 'admin_enqueue_scripts', array( $this, 'editor_style' ) );
    }

    /**
     * Create a shortcode button for tinymce
     *
     * @return filter
     */
    public function shortcode_button() {
        if ( current_user_can( 'edit_posts' ) &&  current_user_can( 'edit_pages' ) ) {
            add_filter( 'mce_external_plugins', array( $this, 'add_buttons' ) );
            add_filter( 'mce_buttons', array( $this, 'register_buttons' ) );
        }
    }

    /**
     * Add new Javascript to the update script array
     *
     * @param  Array $themeworks_array - Array of scripts
     *
     * @return Array
     */
    public function add_buttons( $themeworks_array ) {
        $themeworks_array['themeworks_shortcodes'] = get_template_directory_uri() . '/inc/admin/js/shortcode-tinymce-button.js';
        return $themeworks_array;
    }

    /**
     * Add new button to tinymce
     *
     * @param  Array $buttons - Array of buttons
     *
     * @return Array
     */
    public function register_buttons( $buttons ) {
        array_push( $buttons, 'separator', 'themeworks_shortcodes' );
        return $buttons;
    }

    /**
     * Add shortcode JS to the page
     *
     * @return HTML
     */
    public function get_shortcodes() {
        //global $shortcode_tags;
        $shortcode_tags = Array(
            "tw_contact_form"       => "themeworks_contact_form_shortcode",
            "tw_grid"               => "themeworks_grid_shortcode",
            "tw_dropcap"    => "themeworks_dropcap_shortcode",
            "tw_button"     => "themeworks_button_shortcode",
            "tw_icon"       => "themeworks_icon_shortcode",
            "tw_box"        => "themeworks_box_shortcode",
            "tw_highlight"  => "themeworks_highlight_shortcode",
            "tw_divider"    => "themeworks_divider_shortcode",
            "tw_pricing"    => "themeworks_pricing_shortcode",
            "tw_signup"     => "themeworks_signup_shortcode"
        );

        echo '<script type="text/javascript">
        var shortcodes_button = new Array();';

        $count = 0;

        foreach($shortcode_tags as $tag => $code) {
            echo "shortcodes_button[{$count}] = '{$tag}';";
            $count++;
        }

        echo '</script>';
    }


    public function editor_style( $hook ) {
        if ( 'post.php' != $hook )
            return;
        echo '<style>.mce-menubtn span { padding-right: 15px; }</style>';
    }
}
?>