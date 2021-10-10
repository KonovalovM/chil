<?php
global $theme_options;

/**
 * The Header for our theme.
 *
 * Displays all of the <head> section and everything up till <div id="content">
 *
 * @package themeworks
 */
?><!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?php wp_title( '|', true, 'right' ); ?></title>
<link rel="profile" href="http://gmpg.org/xfn/11">
<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>">

<?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
<div id="page" class="hfeed site">
    <a class="skip-link screen-reader-text" href="#content"><?php _e( 'Skip to content', 'themeworks' ); ?></a>

    <header id="masthead" class="site-header site-header-3" role="banner">
        <?php do_action( 'tw_header_above_hook' ); ?>
        <div class="container">
            <div class="site-utilities clear">
                <div class="site-branding">
                    <h1 class="site-title">
                        <a href="<?php echo home_url( '/' ); ?>" title="<?php echo esc_attr( get_bloginfo( 'name', 'display' ) ); ?>" rel="home">
                            <?php if ( ! empty( $theme_options['logo'] ) ) : ?>
                                <img class="sitetitle" src="<?php echo esc_url( $theme_options['logo'] ); ?>" alt="<?php bloginfo( 'name' ); ?>" />
                            <?php else : ?>
                                <?php bloginfo( 'name' ); ?>
                            <?php endif; ?>
                        </a>
                    </h1>
                    <h2 class="site-description"><?php bloginfo( 'description' ); ?></h2>
                </div>

                <?php themeworks_social( 'header' ); ?>

                <nav id="site-navigation" class="main-navigation" role="navigation">
                    <button class="menu-toggle"><span class="genericon genericon-menu"></span></button>
                    <?php wp_nav_menu( array( 'theme_location' => 'primary', 'menu_class' => 'primary-menu', 'fallback_cb' => 'themeworks_menu_fallback' ) ); ?>
                </nav><!-- #site-navigation -->

                <?php get_template_part( 'content', 'welcome' ); ?>

            </div>
        </div>
        <?php do_action( 'tw_header_below_hook' ); ?>
    </header><!-- #masthead -->

    <div id="content" class="site-content<?php if ( ! is_home() && ! is_page_template( 'page-blog.php' ) ) echo ' container'; ?>">