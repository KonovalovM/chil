<?php
/**
 * The template for displaying all pages.
 *
 * This is the template that displays all pages by default.
 * Please note that this is the WordPress construct of pages
 * and that other 'pages' on your WordPress site will use a
 * different template.
 *
 * @package themeworks
 */

get_header(); ?>

    <div id="primary" class="content-area">
        <main id="main" class="site-main" role="main">
        <?php do_action( 'tw_above_entry_hook' ); ?>
            <?php while ( have_posts() ) : the_post(); ?>

                <?php get_template_part( 'content', 'page' ); ?>
                <?php do_action( 'tw_above_comments_hook' ); ?>
                <?php
                    // If comments are open or we have at least one comment, load up the comment template
                    if ( comments_open() || '0' != get_comments_number() ) :
                        comments_template();
                    endif;
                ?>
                <?php do_action( 'tw_below_comments_hook' ); ?>
            <?php endwhile; // end of the loop. ?>
            <?php do_action( 'tw_below_entry_hook' ); ?>
        </main><!-- #main -->
    </div><!-- #primary -->


<?php get_sidebar(); ?>
<?php get_footer(); ?>