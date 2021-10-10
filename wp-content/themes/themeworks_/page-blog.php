<?php
/*
 * Template Name: Blog Page
 * The template for displaying all posts.
 *
 * @package themeworks
 */

get_header();
?>
<?php if( '' != themeworks_get_section( 'blog' ) ) {
    locate_template( themeworks_get_section( 'blog' ), true, false );
 } else { ?>
    <div id="primary" class="content-area">
        <main id="main" class="site-main" role="main">

        <?php get_template_part( 'content', 'blog' ); ?>

        </main>
    </div>
<?php } ?>
<?php get_footer(); ?>