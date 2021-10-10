<?php
/**
 * The Template part for displaying all welcome messages.
 *
 * @package themeworks
 */

global $theme_options;
?>

<?php if ( is_front_page() || is_home() && ( ! empty( $theme_options['welcome_headline'] ) || ! empty( $theme_options['welcome_message'] ) || ! empty( $theme_options['button_url'] ) ) ) : ?>

    <div class="site-welcome">
        <?php if ( ! empty( $theme_options['welcome_headline'] ) ) : ?>
            <h2 class="xxxl"><span><?php echo stripslashes_deep( $theme_options['welcome_headline'] ); ?></span></h2>
        <?php endif; ?>
        <?php if ( ! empty( $theme_options['welcome_message'] ) ) : ?>
             <p class="l"><span><?php echo stripslashes_deep( $theme_options['welcome_message'] ); ?></span></p>
        <?php endif; ?>
        <?php if ( ! empty( $theme_options['button'] ) && ! empty( $theme_options['button_url'] ) ) : ?>
            <div class="site-action">
                <a href="<?php echo esc_url( $theme_options['button_url'] ); ?>" class="button-border" title="<?php echo stripslashes_deep( $theme_options['button'] ); ?>"><?php echo stripslashes_deep( $theme_options['button'] ); ?></a>
            </div>
        <?php endif; ?>
    </div>

<?php endif; ?>