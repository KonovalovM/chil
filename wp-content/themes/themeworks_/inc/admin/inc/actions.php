<?php

/**
 * Inline CSS
 */
function themeworks_inline_style() {

    $theme          = themeworks_get_current_theme_id();
    $theme_options  = get_option( $theme . '_options' );
    $sections       = array ( 'banner', 'blog', 'client', 'contact', 'ecommerce', 'footer', 'header', 'portfolio', 'service', 'signup', 'slideshow', 'social', 'testimonial', 'widget' );
    $css            = null;

    // Custom CSS
    if ( ! empty( $theme_options['css'] ) ) {
        $css .= wp_filter_nohtml_kses( $theme_options['css'] );
    }

    // Font
    if ( ! empty( $theme_options['font'] ) ) {
        $font = explode( ':', $theme_options['font'] );
        $font_name = str_replace('+', ' ', $font[0] );
        $font_name = "'" . $font_name . "'";

        $css .= 'h1, h2, h3, h4, h5, h6 { font-family: ' . $font_name .'; }' . "\n";
    }

    // Font Alt
    if ( ! empty( $theme_options['font_alt'] ) ) {
        $font_alt = explode( ':', $theme_options['font_alt'] );
        $font_alt_name = str_replace( '+', ' ', $font_alt[0] );
        $font_alt_name = "'" . $font_alt_name . "'";

        $css .= 'body, p, textarea, input, select, label, h2.site-description { font-family: ' . $font_alt_name .'; }' . "\n";
    }

    // Sections custom backgrounds and colors
    if ( $sections ) foreach ( $sections as $section ) {

        // Background image
        if ( ! empty( $theme_options[ $section . '_bg_image'] ) ) {
            $css .= '.' . $section . ' { background-image: url("' . $theme_options[ $section . '_bg_image'] . '"); }' . "\n";
        }

        // Background color
        if ( ! empty( $theme_options[ $section . '_bg_color'] ) ) {
            $css .= '.' . $section . ' { background-color: ' . $theme_options[ $section . '_bg_color'] . '; }' . "\n";
        }

        // Font color
        if ( ! empty( $theme_options[ $section . '_font_color'] ) ) {
            $css .= '.' . $section . ' { color: ' . $theme_options[ $section . '_font_color'] . '; }' . "\n";
        }

        // Header color
        if ( ! empty( $theme_options[ $section . '_header_color'] ) ) {
            $css .= '.' . $section . ' h2 { color: ' . $theme_options[ $section . '_header_color'] . '; }' . "\n";
        }

        // Link color
        if ( ! empty( $theme_options[ $section . '_link_color'] ) ) {
            $css .= '.' . $section . ' a { color: ' . $theme_options[ $section . '_link_color'] . '; }' . "\n";
        }

    }

    wp_add_inline_style( $theme . '-style', $css );
}
add_action( 'wp_enqueue_scripts', 'themeworks_inline_style' );

/**
 * Enqueue Fonts
 */
function themeworks_enqueue_fonts() {

    $theme_options = get_option( themeworks_get_current_theme_id() . '_options' );

    if ( ! empty( $theme_options['font'] ) || ! empty( $theme_options['font_alt'] ) ) {
        $protocol = is_ssl() ? 'https' : 'http';

        // Font Family
        $header = explode( ':', $theme_options['font'] );
        $header_name = str_replace(' ', '+', $header[0] );

        // Font Attributes
        $header_params = ( ! empty( $header[1] ) ) ? ':' . $header[1] : null;

        // Body Font Family
        $body = explode( ':', $theme_options['font_alt'] );
        $body_name = str_replace(' ', '+', $body[0] );

        // Body Font Attributes
        $body_params = ( ! empty( $body[1] ) ) ? ':' . $body[1] : null;

        // Font Separator
        $sep = ( ! empty( $theme_options['font'] ) && ! empty( $theme_options['font_alt'] ) ) ? '|' : '';

        // Final Fonts
        $fonts = ( $theme_options['font'] == $theme_options['font_alt'] ) ? rawurldecode( $header_name . $header_params ) : rawurldecode( $header_name . $header_params . $sep . $body_name . $body_params );

        wp_enqueue_style( 'themeworks-custom-fonts', "$protocol://fonts.googleapis.com/css?family={$fonts}", array(), null );
    }
}

add_action( 'wp_enqueue_scripts', 'themeworks_enqueue_fonts' );