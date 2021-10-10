<?php

/**
 * Use Color class
 */
use Mexitek\PHPColors\Color;

/**
 * Implement support for the Customizer
 */
function themeworks_customize_register( $wp_customize ) {

    /**
    * Common variables used below
    */
    $theme          = themeworks_get_current_theme_id();
    $color_scheme   = themeworks_get_color_scheme();
    $type           = 'option'; // 'option' or 'theme_mod'

    /**
     * Enable live update for default options
     */
    $wp_customize->get_setting( 'blogname' )->transport = 'postMessage';
    $wp_customize->get_setting( 'blogdescription' )->transport = 'postMessage';
    $wp_customize->remove_control( 'header_textcolor' );

    /**
    * Panels
    *
    * Panels contain sections.
    * Sections contain settings.
    * Each setting has a control.
    */

    $general_panel = $theme .'_general';
    $wp_customize->add_panel( $general_panel, array(
        'title'     => __( 'General', 'themeworks' ),
        'priority'  => 10
    ) );

    $typography_panel = $theme .'_typography';
    $wp_customize->add_panel( $typography_panel, array(
        'title'     => __( 'Typography', 'themeworks' ),
        'priority'  => 20
    ) );

    $colors_panel = $theme .'_colors';
    $wp_customize->add_panel( $colors_panel, array(
        'title'     => __( 'Colors', 'themeworks' ),
        'priority'  => 30
    ) );

    $header_panel = $theme .'_header';
    $wp_customize->add_panel( $header_panel, array(
        'title'     => __( 'Header', 'themeworks' ),
        'priority'  => 40
    ) );

    $sections_panel = $theme .'_sections';
    $wp_customize->add_panel( $sections_panel, array(
        'title'         => __( 'Sections', 'themeworks' ),
        'description'   => __( 'The following settings control the layout and content appearing on all sections that you originally added to your theme design.', 'themeworks' ),
        'priority'      => 50
    ) );

    /**
    * Sections & Settings
    *
    * Panels contain sections.
    * Sections contain settings.
    * Each setting has a control.
    */

    /**
     * Site Title & Tagline Section
     */
    $wp_customize->add_section( 'title_tagline', array(
        'title'         => __( 'Site Title & Tagline', 'themeworks' ),
        'priority'      => 10,
        'panel'         => $general_panel
    ) );

    /**
     * Logo & Favicon Section
     */
    $wp_customize->add_section( 'logo', array(
        'title'         => __( 'Logo & Favicon', 'themeworks' ),
        'priority'      => 20,
        'panel'         => $general_panel
    ) );

    // Logo Setting
    $wp_customize->add_setting( $theme .'_options[logo]', array(
        'default'       => '',
        'type'          => $type
    ) );

    // Logo Control
    $wp_customize->add_control( new WP_Customize_Image_Control( $wp_customize, $theme .'_logo', array(
        'label'         => __( 'Logo', 'themeworks' ),
        'section'       => 'logo',
        'settings'      => $theme .'_options[logo]'
    ) ) );

    // Favicon Setting
    $wp_customize->add_setting( $theme .'_options[favicon]', array(
        'default'       => '',
        'type'          => $type,
        'transport'     => 'postMessage'
    ) );

    // Favicon Control
    $wp_customize->add_control( new WP_Customize_Image_Control( $wp_customize, $theme .'_favicon', array(
        'label'         => __( 'Favicon', 'themeworks' ),
        'section'       => 'logo',
        'settings'      => $theme .'_options[favicon]'
    ) ) );

    /**
     * Static Front Page Section
     */
    $wp_customize->add_section( 'static_front_page', array(
        'title'         => __( 'Static Front Page', 'themeworks' ),
        'priority'      => 90,
        'panel'         => $general_panel
    ) );

    /**
     * Social Section
     */
    $wp_customize->add_section( 'social', array(
        'title'         => __( 'Social Media', 'themeworks' ),
        'priority'      => 40,
        'panel'         => $general_panel
    ) );

    // Social Location Setting
    $wp_customize->add_setting( $theme .'_options[social]', array(
        'default'       => '',
        'type'          => $type,
    ) );

    // Social Location Control
    $wp_customize->add_control( $theme .'_social', array(
        'label'         => __( 'Location', 'themeworks' ),
        'description'   => __( 'Choose the location where your social media links will be displayed.', 'themeworks' ),
        'section'       => 'social',
        'settings'      => $theme .'_options[social]',
        'type'          => 'select',
        'priority'      => 1,
        'choices'       => array(
            "header"    => __( 'Header', 'themeworks' ),
            "sidebar"   => __( 'Sidebar', 'themeworks' ),
            "footer"    => __( 'Footer', 'themeworks' ),
            "all"       => __( 'All', 'themeworks' )
        )
    ) );

    // Social Twitter Setting
    $wp_customize->add_setting( $theme .'_options[twitter]', array(
        'default'       => '',
        'type'          => $type,
    ) );

    // Social Twitter Control
    $wp_customize->add_control( $theme .'_twitter', array(
        'label'         => __( 'Twitter', 'themeworks' ),
        'description'   => __( 'Add your url.', 'themeworks' ),
        'section'       => 'social',
        'settings'      => $theme .'_options[twitter]',
        'type'          => 'text',
    ) );

    // Social Facebook Setting
    $wp_customize->add_setting( $theme .'_options[facebook]', array(
        'default'       => '',
        'type'          => $type,
    ) );

    // Social Facebook Control
    $wp_customize->add_control( $theme .'_facebook', array(
        'label'         => __( 'Facebook', 'themeworks' ),
        'description'   => __( 'Add your url.', 'themeworks' ),
        'section'       => 'social',
        'settings'      => $theme .'_options[facebook]',
        'type'          => 'text',
    ) );

    // Social Googleplus Setting
    $wp_customize->add_setting( $theme .'_options[googleplus]', array(
        'default'       => '',
        'type'          => $type,
    ) );

    // Social Googleplus Control
    $wp_customize->add_control( $theme .'_googleplus', array(
        'label'         => __( 'GooglePlus', 'themeworks' ),
        'description'   => __( 'Add your url.', 'themeworks' ),
        'section'       => 'social',
        'settings'      => $theme .'_options[googleplus]',
        'type'          => 'text',
    ) );

    // Social YouTube Setting
    $wp_customize->add_setting( $theme .'_options[youtube]', array(
        'default'       => '',
        'type'          => $type,
    ) );

    // Social YouTube Control
    $wp_customize->add_control( $theme .'_youtube', array(
        'label'         => __( 'YouTube', 'themeworks' ),
        'description'   => __( 'Add your url.', 'themeworks' ),
        'section'       => 'social',
        'settings'      => $theme .'_options[youtube]',
        'type'          => 'text',
    ) );

    // Social Instagram Setting
    $wp_customize->add_setting( $theme .'_options[instagram]', array(
        'default'       => '',
        'type'          => $type,
    ) );

    // Social Instagram Control
    $wp_customize->add_control( $theme .'_instagram', array(
        'label'         => __( 'Instagram', 'themeworks' ),
        'description'   => __( 'Add your url.', 'themeworks' ),
        'section'       => 'social',
        'settings'      => $theme .'_options[instagram]',
        'type'          => 'text',
    ) );

    // Social Linkedin Setting
    $wp_customize->add_setting( $theme .'_options[linkedin]', array(
        'default'       => '',
        'type'          => $type,
    ) );

    // Social Linkedin Control
    $wp_customize->add_control( $theme .'_linkedin', array(
        'label'         => __( 'Linkedin', 'themeworks' ),
        'description'   => __( 'Add your url.', 'themeworks' ),
        'section'       => 'social',
        'settings'      => $theme .'_options[linkedin]',
        'type'          => 'text',
    ) );

    // Social Pinterest Setting
    $wp_customize->add_setting( $theme .'_options[pinterest]', array(
        'default'       => '',
        'type'          => $type,
    ) );

    // Social Pinterest Control
    $wp_customize->add_control( $theme .'_pinterest', array(
        'label'         => __( 'Pinterest', 'themeworks' ),
        'description'   => __( 'Add your url.', 'themeworks' ),
        'section'       => 'social',
        'settings'      => $theme .'_options[pinterest]',
        'type'          => 'text',
    ) );

    /**
     * Signup Section
     */
    $wp_customize->add_section( 'signup', array(
        'title'         => __( 'Signup Forms', 'themeworks' ),
        'description'   => __( 'The settings below control your newsletter signup forms. You can use the [tw_signup] shortcode on any page or in any text widget to show the signup form.', 'themeworks' ),
        'priority'      => 40,
        'panel'         => $general_panel
    ) );

    // Signup Mailchimp API Key Setting
    $wp_customize->add_setting( $theme .'_options[mailchimp_api_key]', array(
        'default'       => '',
        'type'          => $type,
    ) );

    // Signup Mailchimp API Key Control
    $wp_customize->add_control( $theme .'_mailchimp_api_key', array(
        'label'         => __( 'API key', 'themeworks' ),
        'description'   => __( 'Add your Mailchimp API key.', 'themeworks' ),
        'section'       => 'signup',
        'settings'      => $theme .'_options[mailchimp_api_key]',
        'type'          => 'text',
    ) );

    // Signup Mailchimp Lists Setting
    $wp_customize->add_setting( $theme .'_options[mailchimp_list]', array(
        'default'       => '',
        'type'          => $type,
    ) );

    // Signup Mailchimp List Control
    $wp_customize->add_control( $theme .'_mailchimp_list', array(
        'label'         => __( 'Newsletter list', 'themeworks' ),
        'description'   => __( 'After saving your API key, select the list to add your subscribers to.', 'themeworks' ),
        'section'       => 'signup',
        'settings'      => $theme .'_options[mailchimp_list]',
        'type'          => 'select',
        'choices'       => themeworks_extract_valid_options( themeworks_mailchimp_get_lists() )
    ) );

    // Signup Mailchimp Note Setting
    $wp_customize->add_setting( $theme .'_options[signup_note]', array(
        'default'       => '',
        'type'          => $type,
    ) );

    // Signup Mailchimp Not Control
    $wp_customize->add_control( $theme .'_signup_note', array(
        'label'         => __( 'Signup message', 'themeworks' ),
        'description'   => __( 'Add a message that appears near your signup form to prompt users to sign up.', 'themeworks' ),
        'section'       => 'signup',
        'settings'      => $theme .'_options[signup_note]',
        'type'          => 'textarea',
    ) );

    // Signup Mailchimp Name Setting
    $wp_customize->add_setting( $theme .'_options[signup_name]', array(
        'default'       => '',
        'type'          => $type,
    ) );

    // Signup Mailchimp Name Control
    $wp_customize->add_control( $theme .'_signup_name', array(
        'label'         => __( 'Additional Fields', 'themworks' ),
        'description'   => __( 'Check to show the First Name and Last Name fields in your signup forms', 'themeworks' ),
        'section'       => 'signup',
        'settings'      => $theme .'_options[signup_name]',
        'type'          => 'checkbox',
    ) );

    // Signup Mailchimp Background Image Setting
    $wp_customize->add_setting( $theme .'_options[signup_bg_image]', array(
        'default'       => '',
        'type'          => $type,
    ) );

    // Signup Mailchimp Background Image Control
    $wp_customize->add_control( new WP_Customize_Image_Control( $wp_customize, $theme .'_signup_bg_image', array(
        'label'         => __( 'Background Image', 'themeworks' ),
        'section'       => 'signup',
        'settings'      => $theme .'_options[signup_bg_image]'
    ) ) );

    // Signup Mailchimp Background Color Setting
    $wp_customize->add_setting( $theme .'_options[signup_bg_color]', array(
        'default'       => '',
        'type'          => $type,
        'transport' => 'postMessage'

    ) );

    // Signup Mailchimp Background Color Control
    $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, $theme .'_signup_bg_color', array(
        'label'         => __( 'Background Color', 'themeworks' ),
        'section'       => 'signup',
        'settings'      => $theme .'_options[signup_bg_color]'
    ) ) );

    /**
     * Slideshow Section
     */
    $wp_customize->add_section( 'slideshow_options', array(
        'title'         => __( 'Slideshow Options', 'themeworks' ),
        'priority'      => 50,
        'panel'         => $general_panel
    ) );

    // Slideshow Navigation Setting
    $wp_customize->add_setting( $theme .'_options[slideshow_navigation]', array(
        'default'       => '',
        'type'          => $type,
    ) );

    // Slideshow Navigation Control
    $wp_customize->add_control( $theme .'_slideshow_navigation', array(
        'label'         => __( 'Slideshow Navigation', 'themeworks' ),
        'section'       => 'slideshow_options',
        'settings'      => $theme .'_options[slideshow_navigation]',
        'type'          => 'select',
        'choices'       => array(
            'show'      => __( 'Thumbnails', 'themeworks' ),
            'hide'      => __( 'Shapes', 'themeworks' )
        )
    ) );

    // Slideshow Autostart Setting
    $wp_customize->add_setting( $theme .'_options[slideshow_autostart]', array(
        'default'       => '',
        'type'          => $type,
    ) );

    // Slideshow Autostart Control
    $wp_customize->add_control( $theme .'_slideshow_autostart', array(
        'label'         => __( 'Slideshow Autostart', 'themeworks' ),
        'section'       => 'slideshow_options',
        'settings'      => $theme .'_options[slideshow_autostart]',
        'type'          => 'select',
        'choices'       => array(
            'auto'      => __( 'Auto start', 'themeworks' ),
            'click'     => __( 'Click start', 'themeworks' )
        )
    ) );

    // Slideshow Animation Setting
    $wp_customize->add_setting( $theme .'_options[slideshow_animation]', array(
        'default'       => '',
        'type'          => $type,
    ) );

    // Slideshow Animation Control
    $wp_customize->add_control( $theme .'_slideshow_animation', array(
        'label'         => __( 'Slideshow Animation', 'themeworks' ),
        'section'       => 'slideshow_options',
        'settings'      => $theme .'_options[slideshow_animation]',
        'type'          => 'select',
        'choices'       => array(
            'fade'      => __( 'Fade', 'themeworks' ),
            'slide'     => __( 'Slide', 'themeworks' )
        )
    ) );

    // Slideshow Smooth Height Setting
    $wp_customize->add_setting( $theme .'_options[slideshow_smooth_height]', array(
        'default'       => '',
        'type'          => $type,
    ) );

    // Slideshow Smooth Height Control
    $wp_customize->add_control( $theme .'_slideshow_smooth_height', array(
        'label'         => __( 'Slideshow Height', 'themeworks' ),
        'description'   => __( 'If fluid, the slideshow will resize vertically for every slide. If fixed, the slideshow will remain a consistent height of 600 pixels.', 'themeworks' ),
        'section'       => 'slideshow_options',
        'settings'      => $theme .'_options[slideshow_smooth_height]',
        'type'          => 'select',
        'choices'       => array(
            'fluid'     => __( 'Fluid', 'themeworks' ),
            'fixed'     => __( 'Fixed', 'themeworks' )
        )
    ) );

    /**
     * Fonts Section
     */
    $wp_customize->add_section( 'fonts', array(
        'title'         => __( 'Fonts', 'themeworks' ),
        'description'   => sprintf(
            __( 'Choose from over 650 %1$s to match your personal style. %2$s.', 'themeworks' ),
            sprintf(
                '<a href="%1$s" target="_blank">%2$s</a>',
                'https://www.google.com/fonts',
                __( 'Google Fonts', 'themeworks' )
            ),
            sprintf(
                '<a href="%1$s" target="_blank">%2$s</a>',
                'http://femmebot.github.io/google-type/',
                __( 'Get inspired', 'themeworks' )
            )
        ),
        'priority'      => 10,
        'panel'         => $typography_panel
    ) );

    // Font Setting
    $wp_customize->add_setting( $theme .'_options[font]', array(
        'default'       => '',
        'type'          => $type,
        'transport'     => 'postMessage'
    ) );

    // Font Control
    $wp_customize->add_control( $theme .'_font', array(
        'label'         => __( 'Headline Font', 'themeworks' ),
        'section'       => 'fonts',
        'settings'      => $theme .'_options[font]',
        'type'          => 'select',
        'choices'       => themeworks_extract_valid_options( themeworks_font_array() )
    ) );

    // Font Alt Setting
    $wp_customize->add_setting( $theme .'_options[font_alt]', array(
        'default'       => '',
        'type'          => $type,
        'transport'     => 'postMessage'
    ) );

    // Font Alt Control
    $wp_customize->add_control( $theme .'_font_alt', array(
        'label'         => __( 'Body Font', 'themeworks' ),
        'section'       => 'fonts',
        'settings'      => $theme .'_options[font_alt]',
        'type'          => 'select',
        'choices'       => themeworks_extract_valid_options( themeworks_font_array() )
    ) );

    /**
     * Color Section
     */
    $wp_customize->add_section( 'colors', array(
        'title'             => __( 'Color Scheme', 'themeworks' ),
        'priority'          => 10,
        'panel'             => $colors_panel
    ) );

    // Color Scheme Setting
    $wp_customize->add_setting( $theme .'_options[color]', array(
        'default'           => 'default',
        'sanitize_callback' => 'themeworks_sanitize_color_scheme',
        'type'              => $type,
        'transport' => 'postMessage'
    ) );

    // Color Scheme Control
    $wp_customize->add_control( $theme .'_color', array(
        'label'             => __( 'Color Scheme', 'themeworks' ),
        'section'           => 'colors',
        'type'              => 'select',
        'settings'          => $theme .'_options[color]',
        'choices'           => themeworks_get_color_scheme_choices(),
        'priority'          => 1,
    ) );

    // Accent Color Setting
    $wp_customize->add_setting( $theme .'_options[accent_color]', array(
        'default'           => $color_scheme[1],
        'panel'             => $colors_panel,
        'type'              => $type,
        'transport' => 'postMessage'
    ) );

    // Accent Color Control
    $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, $theme .'_accent_color', array(
        'label'             => __( 'Accent Color', 'themeworks' ),
        'section'           => 'colors',
        'settings'          => $theme .'_options[accent_color]'
    ) ) );

    // Dark Color Setting
    $wp_customize->add_setting( $theme .'_options[dark_color]', array(
        'default'           => $color_scheme[2],
        'panel'             => $colors_panel,
        'type'              => $type,
        'transport' => 'postMessage'
    ) );

    // Dark Color Control
    $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, $theme .'_dark_color', array(
        'label'             => __( 'Header and Footer Background Color', 'themeworks' ),
        'section'           => 'colors',
        'settings'          => $theme .'_options[dark_color]'
    ) ) );

    /**
     * Background Section
     */
    $wp_customize->add_section( 'background_image', array(
        'title'         => __( 'Background Image', 'themeworks' ),
        'priority'      => 30,
        'panel'         => $colors_panel
    ) );

    /**
     * Custom CSS Section
     */
    $wp_customize->add_section( 'custom_css', array(
        'title'         => __( 'Custom CSS', 'themeworks' ),
        'priority'      => 90,
        'panel'         => $colors_panel
    ) );

    // Custom CSS Setting
    $wp_customize->add_setting( $theme .'_options[css]', array(
        'default'       => '',
        'type'          => $type,
        'transport'     => 'postMessage'
    ) );

    // Custom CSS Control
    $wp_customize->add_control( $theme .'_css', array(
        'label'         => __( 'CSS', 'themeworks' ),
        'section'       => 'custom_css',
        'settings'      => $theme .'_options[css]',
        'type'          => 'textarea',
    ) );

    /**
     * Header Section
     */
    $wp_customize->add_section( 'header_image', array(
        'title'         => 'Header Image',
        'priority'      => 10,
        'panel'         => $header_panel
    ) );

    /**
     * Navigation Section
     */
    $wp_customize->add_section( 'nav', array(
        'title'         => __( 'Navigation', 'themeworks' ),
        'priority'      => 20,
        'panel'         => $header_panel
    ) );

    /**
     * Welcome Message Section
     */
    $wp_customize->add_section( 'welcomemessage', array(
        'title'         => __( 'Welcome Message', 'themeworks' ),
        'priority'      => 30,
        'panel'         => $header_panel
    ) );

    // Welcome Headline Setting
    $wp_customize->add_setting( $theme .'_options[welcome_headline]', array(
        'default'       => '',
        'type'          => $type,
    ) );

    // Welcome Headline Control
    $wp_customize->add_control( $theme .'_welcome_headline', array(
        'label'         => __( 'Headline', 'themeworks' ),
        'section'       => 'welcomemessage',
        'settings'      => $theme .'_options[welcome_headline]',
        'type'          => 'text',
    ) );

    // Welcome Message Setting
    $wp_customize->add_setting( $theme .'_options[welcome_message]', array(
        'default'       => '',
        'type'          => $type,
    ) );

    // Welcome Message Control
    $wp_customize->add_control( $theme .'_welcome_message', array(
        'label'         => __( 'Message', 'themeworks' ),
        'section'       => 'welcomemessage',
        'settings'      => $theme .'_options[welcome_message]',
        'type'          => 'textarea',
    ) );

     // Welcome Button Setting
    $wp_customize->add_setting( $theme .'_options[button]', array(
        'default'       => '',
        'type'          => $type,
    ) );

    // Welcome Button Control
    $wp_customize->add_control( $theme .'_button', array(
        'label'         => __( 'Button Text', 'themeworks' ),
        'description'   => __( 'The text for your button.', 'themeworks' ),
        'section'       => 'welcomemessage',
        'settings'      => $theme .'_options[button]',
        'type'          => 'text',
    ) );

     // Welcome Button URL Setting
    $wp_customize->add_setting( $theme .'_options[button_url]', array(
        'default'       => '',
        'type'          => $type,
    ) );

    // Welcome Button URL Control
    $wp_customize->add_control( $theme .'_button_url', array(
        'label'         => __( 'Button URL', 'themeworks' ),
        'description'   => __( 'The url for your button.', 'themeworks' ),
        'section'       => 'welcomemessage',
        'settings'      => $theme .'_options[button_url]',
        'type'          => 'text',
    ) );

    /**
     * Sections General Section
     */
    $wp_customize->add_section( 'sections_general', array(
        'title'         => __( 'General', 'themeworks' ),
        'priority'      => 10,
        'panel'         => $sections_panel
    ) );

    // Parallax Setting
    $wp_customize->add_setting( $theme .'_options[parallax]', array(
        'default'       => '',
        'type'          => $type,
    ) );

    // Parallax Control
    $wp_customize->add_control( $theme .'_parallax', array(
        'label'         => __( 'Parallax effect', 'themeworks' ),
        'description'   => __( 'Click to enable parallax effect on all sections with background images.', 'themeworks' ),
        'section'       => 'sections_general',
        'settings'      => $theme .'_options[parallax]',
        'type'          => 'checkbox',
    ) );

}
add_action( 'customize_register', 'themeworks_customize_register' );

/**
 * Register color schemes for themeworks.
 *
 * Can be filtered with {@see 'themeworks_color_schemes'}.
 *
 * The order of colors in a colors array:
 * 1. Main Background Color.
 * 2. Base Accent Color.
 * 3. Base Dark Color.
 *
 * @since themeworks 1.1
 *
 * @return array An associative array of color scheme options.
 */
function themeworks_get_color_schemes() {
    return apply_filters( 'themeworks_color_schemes', array(
        'default' => array(
            'label'  => __( 'Default', 'themeworks' ),
            'colors' => array(
                '#ffffff', // background color
                '#3498db', // link color
                '#34495e' // dark color
            ),
        ),
        'beautiful'  => array(
            'label'  => __( 'Beautiful', 'themeworks' ),
            'colors' => array(
                '#F4F1E3',
                '#63A599',
                '#1F2F3F'
            ),
        ),
        'blossom'  => array(
            'label'  => __( 'Blossom', 'themeworks' ),
            'colors' => array(
                '#FFFCEC',
                '#FF6165',
                '#ABDCD6'
            ),
        ),
        'camo'  => array(
            'label'  => __( 'Camo', 'themeworks' ),
            'colors' => array(
                '#F0EFDD',
                '#659B79',
                '#477d5b'
            ),
        ),
        'dark'  => array(
            'label'  => __( 'Dark', 'themeworks' ),
            'colors' => array(
                '#111111',
                '#E6262E',
                '#000000'
            ),
        ),
        'ice'  => array(
            'label'  => __( 'Ice', 'themeworks' ),
            'colors' => array(
                '#88A6AF',
                '#435A66',
                '#39505c'
            ),
        ),
        'oreo'  => array(
            'label'  => __( 'Oreo', 'themeworks' ),
            'colors' => array(
                '#FFFFFF',
                '#E6262E',
                '#000000'
            ),
        ),
        'retro'  => array(
            'label'  => __( 'Retro', 'themeworks' ),
            'colors' => array(
                '#E5DDCB',
                '#EB7B59',
                '#e1714f'
            ),
        ),
        'splash'  => array(
            'label'  => __( 'Splash', 'themeworks' ),
            'colors' => array(
                '#F3F7F8',
                '#E87A64',
                '#ca5c46'
            ),
        ),
        'spring'  => array(
            'label'  => __( 'Spring', 'themeworks' ),
            'colors' => array(
                '#F0EEE2',
                '#F24A4F',
                '#3B4159'
            ),
        )
    ) );
}

if ( ! function_exists( 'themeworks_get_color_scheme' ) ) :
/**
 * Get the current themeworks color scheme.
 *
 * @since themeworks 1.1
 *
 * @return array An associative array of either the current or default color scheme hex values.
 */
function themeworks_get_color_scheme() {
    global $theme_options;

    $color_scheme_option = $theme_options['color'];
    $color_schemes       = themeworks_get_color_schemes();

    if ( array_key_exists( $color_scheme_option, $color_schemes ) ) {
        return $color_schemes[ $color_scheme_option ]['colors'];
    }

    return $color_schemes['default']['colors'];
}
endif; // themeworks_get_color_scheme

if ( ! function_exists( 'themeworks_get_color_scheme_choices' ) ) :
/**
 * Returns an array of color scheme choices registered for Twenty Fifteen.
 *
 * @since themeworks 1.1
 *
 * @return array Array of color schemes.
 */
function themeworks_get_color_scheme_choices() {
    $color_schemes                = themeworks_get_color_schemes();
    $color_scheme_control_options = array();

    foreach ( $color_schemes as $color_scheme => $value ) {
        $color_scheme_control_options[ $color_scheme ] = $value['label'];
    }

    return $color_scheme_control_options;
}
endif; // themeworks_get_color_scheme_choices

if ( ! function_exists( 'themeworks_sanitize_color_scheme' ) ) :
/**
 * Sanitization callback for color schemes.
 *
 * @since themeworks 1.1
 *
 * @param string $value Color scheme name value.
 * @return string Color scheme name.
 */
function themeworks_sanitize_color_scheme( $value ) {
    $color_schemes = themeworks_get_color_scheme_choices();

    if ( ! array_key_exists( $value, $color_schemes ) ) {
        $value = 'default';
    }

    return $value;
}
endif; // themeworks_sanitize_color_scheme


/**
 * Enqueues front-end CSS for color scheme.
 *
 * @since themeworks 1.0
 *
 * @see wp_add_inline_style()
 */
function themeworks_color_scheme_css() {
    global $theme_options;

    $default_bg_color = ( get_theme_mod( 'background_color' ) != false ? get_theme_mod( 'background_color' ) : 'FFFFFF' );

    $bg_color = new Color( $default_bg_color );
    $accent_color = new Color( $theme_options['accent_color'] );
    $dark_color = new Color( $theme_options['dark_color'] );

    if ( $bg_color->isDark() ) {
        $font_color = '#' . $bg_color->lighten(70);
    } else {
        $font_color = '#' . $bg_color->darken(70);
    }
    $final_font_color = new Color( $font_color );

    if ( $dark_color->isDark() ) {
        $header_font_color = '#' . $dark_color->lighten(30);
        $header_link_color = '#' . $dark_color->lighten(50);
    } else {
        $header_font_color = '#' . $dark_color->darken(30);
        $header_link_color = '#' . $dark_color->darken(50);
    }

    $accent_color_darker_15      = '#' . $accent_color->darken(15);
    $dark_color_darker_10        = '#' . $dark_color->darken(10);
    $dark_color_darker_5         = '#' . $dark_color->darken(5);
    $background_color_darker_25  = '#' . $bg_color->darken(25);
    $background_color_lighter_50 = '#' . $bg_color->lighten(50);
    $font_color_darker_30        = '#' . $final_font_color->darken(25);
    $font_color_lighter_15       = '#' . $final_font_color->lighten(15);

    $colors = array(
        'background_color'            => get_theme_mod( 'background_color' ), // needs to change
        'font_color'                  => $font_color,
        'accent_color'                => $theme_options['accent_color'],
        'accent_color_darker_30'      => $accent_color_darker_15,
        'dark_color'                  => $theme_options['dark_color'],
        'dark_color_darker_10'        => $dark_color_darker_10,
        'dark_color_darker_5'         => $dark_color_darker_5,
        'header_font_color'           => $header_font_color,
        'header_link_color'           => $header_link_color,
        'background_color_darker_25'  => $background_color_darker_25,
        'background_color_lighter_50' => $background_color_lighter_50,
        'font_color_darker_30'        => $font_color_darker_30,
        'font_color_lighter_15'       => $font_color_lighter_15,

    );

    $color_scheme_css = themeworks_get_color_scheme_css( $colors );

    wp_add_inline_style( 'themeworks-style', $color_scheme_css );
}
add_action( 'wp_enqueue_scripts', 'themeworks_color_scheme_css' );

/**
 * Binds JS listener to make Customizer color_scheme control.
 *
 * Passes color scheme data as colorScheme global.
 *
 * @since themeworks 1.1
 */
function themeworks_customize_control_js() {
    wp_enqueue_script( 'color-scheme-control', get_template_directory_uri() . '/inc/admin/js/customizer-color-schemes-control.js', array( 'customize-controls', 'iris', 'underscore', 'wp-util' ), '20150123', true );
    wp_localize_script( 'color-scheme-control', 'colorScheme', themeworks_get_color_schemes() );
}
add_action( 'customize_controls_enqueue_scripts', 'themeworks_customize_control_js' );

/**
 * Binds JS handlers to make Theme Customizer preview reload changes asynchronously.
 */
function themeworks_customize_preview_js() {
    wp_enqueue_script( 'themeworks_customizer', get_template_directory_uri() . '/inc/admin/js/customizer.js', array( 'customize-preview' ), '20150129', true );
}
add_action( 'customize_preview_init', 'themeworks_customize_preview_js' );

/**
 * Binds to customizer options panel.
 */
function themeworks_customize_panel() {
    wp_enqueue_style( 'themeworks_customizer', get_template_directory_uri() . '/inc/admin/css/customizer.css' );
}
add_action( 'customize_controls_enqueue_scripts', 'themeworks_customize_panel' );

/**
 * Returns CSS for the color schemes.
 *
 * @since themeworks 1.1
 *
 * @param array $colors Color scheme colors.
 * @return string Color scheme CSS.
 */
function themeworks_get_color_scheme_css( $colors ) {
    $defaults = array(
        'background_color'              => $colors['background_color'],
        'font_color'                    => $colors['font_color'],
        'accent_color'                  => $colors['accent_color'],
        'dark_color'                    => $colors['dark_color'],
        'background_color_darker_25'    => $colors['background_color_darker_25'],
        'background_color_lighter_50'   => $colors['background_color_lighter_50'],
        'accent_color_darker_30'        => $colors['accent_color_darker_30'],
        'dark_color_darker_10'          => $colors['dark_color_darker_10'],
        'dark_color_darker_5'           => $colors['dark_color_darker_5'],
        'header_font_color'             => $colors['header_font_color'],
        'header_link_color'             => $colors['header_link_color'],
        'font_color_darker_30'          => $colors['font_color_darker_30'],
        'font_color_lighter_15'         => $colors['font_color_lighter_15']
    );
    $colors = wp_parse_args( $colors, $defaults );

    $css = <<<CSS
    /* Color Scheme */

    /* Background Color */
    body {
        background-color: #{$colors['background_color']};
    }
    .themeworks-pricing-table section ul li {
        border-color: #{$colors['background_color']};
    }

    /* Font Color */
    body,
    .entry-meta,
    .button-border,
    .button-border:hover {
        color: {$colors['font_color']};
    }
    .button-border {
        border-color: {$colors['font_color']};
    }
    .button-border:hover {
        border-color: {$colors['font_color']} !important;
    }
    .comments-area .comment-list .comment .comment-meta .comment-metadata .fn {
        color: {$colors['font_color_darker_30']};
    }

    /* Font Color 15% lighter */
    blockquote {
        color: {$colors['font_color_lighter_15']};
    }

    /* Accent Color */
    a {
        color: {$colors['accent_color']};
    }
    .button,
    button,
    input[type=submit],
    input[type="submit"] {
        background-color: {$colors['accent_color']};
    }
    .button:hover:not(:disabled),
    button:hover:not(:disabled),
    input[type=submit]:hover:not(:disabled),
    input[type="submit"]:hover:not(:disabled) {
        background-color: {$colors['accent_color_darker_30']};
    }
    .entry-meta a,
    .entry-footer a {
        color: {$colors['accent_color']};
    }

    @media screen and (max-width: 768px) {
        .site-header .main-navigation ul {
            background-color: {$colors['accent_color']};
        }
        .site-header .main-navigation ul li a {
            background: {$colors['accent_color']};
        }
        .site-header .main-navigation ul li a:hover {
            background: {$colors['accent_color_darker_30']};
        }
        .site-header .main-navigation ul.sub-menu a:hover {
            background-color: {$colors['accent_color_darker_30']};
        }
    }

    /* Accent Color 30% darker */
    a:hover,
    a:active,
    a:focus,
    .entry-meta a:hover,
    .entry-footer a:hover {
        color: {$colors['accent_color_darker_30']};
    }

    /* Dark Color */
    .site-footer,
    .site-header,
    .tooltip,
    .themeworks-box,
    .themeworks-pricing-table header {
        background-color: {$colors['dark_color']};
    }
    .tooltip:after {
        border-top-color: {$colors['dark_color']};
    }

    /* Dark Color 10% darker */
    .main-navigation ul > li ul li a,
    .site-footer .site-info {
        background-color: {$colors['dark_color_darker_10']};
    }
    .site-header .main-navigation ul.sub-menu:before,
    .site-header .main-navigation ul.sub-menu:after {
        border-bottom-color: {$colors['dark_color_darker_10']};;
    }

    /* Dark Color 25% lighter */
    .site-description,
    h2.entry-title,
    .site-welcome,
    .site-footer {
        color: {$colors['header_font_color']}
    }
    .main-navigation ul > li ul li a:hover {
        background-color: {$colors['dark_color_darker_5']}
    }

    /* Dark Color 50% lighter */
    .site-title a,
    .site-action a,
    .site-header .site-action .button-border,
    .site-header .site-action .button-border:hover,
    .site-header .social a,
    .main-navigation ul li a,
    .main-navigation ul > li ul li a:hover,
    .site-footer a {
        color: {$colors['header_link_color']};
    }
    .site-header .site-action .button-border {
        border-color: {$colors['header_link_color']};
    }
    .site-header .site-action .button-border:hover {
        border-color: {$colors['header_link_color']} !important;
    }

    .has-header-image .site-header .site-action .button-border:hover {
        color: #FFFFFF;
        border-color: #FFFFFF !important;
        opacity: 0.8;
    }

    /* Background Color 25% darker */
    textarea,
    input,
    table tbody tr:first-child th,
    table tbody tr:first-child td,
    table tbody th,
    table tbody td,
    hr,
    form textarea,
    input[type="email"],
    input[type="number"],
    input[type="password"],
    input[type="search"],
    input[type="tel"],
    input[type="text"],
    input[type="url"],
    input[type="color"],
    input[type="date"],
    input[type="datetime"],
    input[type="datetime-local"],
    input[type="month"],
    input[type="time"],
    input[type="week"],
    select[multiple=multiple],
    .widget-area ul li.recentcomments,
    .widget-area ul li a,
    .comments-title,
    .themeworks-pricing-table,
    .themeworks-divider.solid,
    .themeworks-divider.dashed,
    .themeworks-divider.dotted,
    .themeworks-divider.double {
        border-color: {$colors['background_color_darker_25']};
    }

    /* Background Color 50% lighter */
    blockquote {
        background: {$colors['background_color_lighter_50']};
    }
    .themeworks-pricing-table section,
    .themeworks-pricing-table footer {
        background: {$colors['background_color_darker_25']};
    }

    .has-header-image.customizer-preview .site-title a,
    .has-header-image.customizer-preview .main-navigation ul li a,
    .has-header-image.customizer-preview .site-action a,
    .has-header-image.customizer-preview .site-header .social a,
    .has-header-image.customizer-preview .site-welcome {
        color: #FFFFFF;
    }

    .has-header-image.customizer-preview .site-header .site-action .button-border,
    .has-header-image.customizer-preview .site-header .site-action .button-border:hover {
        border-color: #FFFFFF;
        color: #FFFFFF;
    }

CSS;

    return $css;
}

/**
 * Output an Underscore template for generating CSS for the color scheme.
 *
 * The template generates the css dynamically for instant display in the Customizer
 * preview.
 *
 * @since themeworks 1.0
 */
function themeworks_color_scheme_css_template() {
    $colors = array(
        'background_color'            => '{{ data.background_color }}',
        'font_color'                  => '{{ data.font_color }}',
        'accent_color'                => '{{ data.accent_color }}',
        'dark_color'                  => '{{ data.dark_color }}',
        'accent_color_darker_30'      => '{{ data.accent_color_darker_30 }}',
        'dark_color_darker_10'        => '{{ data.dark_color_darker_10 }}',
        'dark_color_darker_5'         => '{{ data.dark_color_darker_5 }}',
        'header_font_color'           => '{{ data.header_font_color }}',
        'header_link_color'           => '{{ data.header_link_color }}',
        'background_color_darker_25'  => '{{ data.background_color_darker_25 }}',
        'background_color_lighter_50' => '{{ data.background_color_lighter_50 }}',
        'font_color_darker_30'        => '{{ data.font_color_darker_30 }}',
        'font_color_lighter_15'       => '{{ data.font_color_lighter_15 }}',
    );
    ?>
    <script type="text/html" id="tmpl-themeworks-color-scheme">
        <?php echo themeworks_get_color_scheme_css( $colors ); ?>
    </script>
    <?php
}
add_action( 'customize_controls_print_footer_scripts', 'themeworks_color_scheme_css_template' );

/**
 * Validates theme customizer options
 */
function themeworks_extract_valid_options( $options ) {
    $new_options = array();
    foreach( $options as $option ) {
        if ( isset( $option['variants'] ) && '' != $option['variants'] ) {
            $variants = implode( ',', $option['variants'] );
            $opt =  $option['label'] . ':' . $variants;
        } else {
            $opt = $option['name'];
        }
        if ( isset ( $option['label'] ) ) {
            $new_options[ $opt ] = $option['label'];
        } else {
            $new_options[ $opt ] = $option['title'];
        }
    }
    return $new_options;
}