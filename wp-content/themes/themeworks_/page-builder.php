<?php
/*
 * Template Name: Page Builder
 * The template for displaying a wide page without a sidebar.
 *
 * @package themeworks
 */

get_header(); ?>

    <div id="primary" class="content-area wide-page">
        <main id="main" class="site-main" role="main">

            <?php themeworks_get_page_sections( $post->ID ); ?>

        </main><!-- #main -->
    </div><!-- #primary -->

<?php get_footer(); ?>