<?php
/**
 * Theme.Works back compat functionality
 *
 * Prevents themeworks from running on WordPress versions prior to 4.1,
 * since this theme is not meant to be backward compatible beyond that and
 * relies on many newer functions and markup changes introduced in 4.1.
 *
 * @package themeworks
 * @since themeworks 1.1
 */

if ( version_compare( $GLOBALS['wp_version'], '4.1-alpha', '<' ) ) {

    /**
     * Prevent switching to themeworks on old versions of WordPress.
     *
     * Switches to the default theme.
     *
     * @since themeworks 1.1
     */
    function themeworks_switch_theme() {
        switch_theme( WP_DEFAULT_THEME, WP_DEFAULT_THEME );
        unset( $_GET['activated'] );
        add_action( 'admin_notices', 'themeworks_upgrade_notice' );
    }
    add_action( 'after_switch_theme', 'themeworks_switch_theme' );

    /**
     * Add message for unsuccessful theme switch.
     *
     * Prints an update nag after an unsuccessful attempt to switch to
     * themeworks on WordPress versions prior to 4.1.
     *
     * @since themeworks 1.0.1
     */
    function themeworks_upgrade_notice() {
        $message = sprintf( __( 'Theme.Works requires at least WordPress version 4.1. You are running version %s. Please upgrade and try again.', 'themeworks' ), $GLOBALS['wp_version'] );
        printf( '<div class="error"><p>%s</p></div>', $message );
    }

    /**
     * Prevent the Customizer from being loaded on WordPress versions prior to 4.1.
     *
     * @since themeworks 1.1
     */
    function themeworks_customize() {
        wp_die( sprintf( __( 'Theme.Works requires at least WordPress version 4.1. You are running version %s. Please upgrade and try again.', 'themeworks' ), $GLOBALS['wp_version'] ), '', array(
            'back_link' => true,
        ) );
    }
    add_action( 'load-customize.php', 'themeworks_customize' );

    /**
     * Prevent the Theme Preview from being loaded on WordPress versions prior to 4.1.
     *
     * @since themeworks 1.1
     */
    function themeworks_preview() {
        if ( isset( $_GET['preview'] ) ) {
            wp_die( sprintf( __( 'Theme.Works requires at least WordPress version 4.1. You are running version %s. Please upgrade and try again.', 'themeworks' ), $GLOBALS['wp_version'] ) );
        }
    }
    add_action( 'template_redirect', 'themeworks_preview' );

}