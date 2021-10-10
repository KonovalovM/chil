<?php
/**
 * The template for displaying the footer.
 *
 * Contains the closing of the #content div and all content after
 *
 * @package themeworks
 */
?>

    </div><!-- #content -->

    <footer id="colophon" class="site-footer" role="contentinfo">
        <?php do_action( 'tw_footer_above_hook' ); ?>
        <?php if ( is_active_sidebar( 'footer' ) ) : ?>
            <div class="container">
                <div id="section-footer-3" class="section widgets footer-3">
                    <?php dynamic_sidebar( 'footer' ); ?>
                </div>
            </div>
        <?php endif; // end check if any footer widgets are active ?>
        <div class="site-info">
            <div class="container">
                <?php themeworks_social( 'footer' ); ?>
                <?php echo apply_filters( 'tw_site_credit', __( 'Custom WordPress theme by <a href="https://theme.works/?ref=footer">Theme.Works</a>', 'themeworks' ) ); ?>
                <nav id="second-navigation" class="second-navigation" role="navigation">
                    <?php wp_nav_menu( array( 'theme_location' => 'secondary', 'menu_class' => 'secondary-menu', 'fallback_cb' => 'themeworks_menu_fallback' ) ); ?>
                </nav><!-- #second-navigation -->
            </div><!-- .container -->
        </div><!-- .site-info -->
        <?php do_action( 'tw_footer_below_hook' ); ?>
    </footer><!-- #colophon -->
</div><!-- #page -->

<?php wp_footer(); ?>

</body>
</html>