<?php
/**
 * The template used for displaying page content in page.php
 *
 * @package themeworks
 */
?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
    <?php do_action( 'tw_above_title_hook' ); ?>
    <header class="entry-header">
        <?php the_title( '<h1 class="entry-title">', '</h1>' ); ?>
    </header><!-- .entry-header -->
    <?php do_action( 'tw_below_title_hook' ); ?>
    <?php do_action( 'tw_above_content_hook' ); ?>
    <div class="entry-content">
        <?php the_content(); ?>
        <?php
            wp_link_pages( array(
                'before' => '<div class="pagination">' . __( 'Pages:', 'themeworks' ),
                'after'  => '</div>',
            ) );
        ?>
    </div><!-- .entry-content -->
    <?php do_action( 'tw_below_content_hook' ); ?>
    <?php do_action( 'tw_above_meta_hook' ); ?>
    <footer class="entry-footer">
        <?php edit_post_link( __( 'Edit', 'themeworks' ), '<span class="edit-link">', '</span>' ); ?>
    </footer><!-- .entry-footer -->
    <?php do_action( 'tw_below_meta_hook' ); ?>
</article><!-- #post-## -->
