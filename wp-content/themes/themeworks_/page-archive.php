<?php
/*
 * Template Name: Archives Page
 * The template for displaying post archives
 *
 * @package themeworks
 */

get_header(); ?>

<section id="primary" class="content-area archives-page">
    <main id="main" class="site-main" role="main">

        <div class="col three-col">
            <h2><?php _e( 'Categories', 'themeworks' ); ?></h2>
            <ul><?php wp_list_categories( array('show_count'=>1 , 'hide_empty'=>0, 'title_li'=>'' ) ); ?></ul>
        </div>

        <div class="col three-col">
            <h2><?php _e( 'Monthly Archive', 'themeworks' ); ?></h2>
            <ul><?php wp_get_archives( array('type'=>'monthly', 'echo'=>1, 'show_post_count'=>true ) ); ?></ul>
        </div>

        <div class="col three-col">
            <h2><?php _e( 'Post List', 'themeworks' ); ?></h2>
            <ul><?php wp_get_archives( array('type'=>'postbypost', 'echo'=>1 ) ); ?></ul>
        </div>

        <?php edit_post_link( __( 'Edit', 'themeworks' ), '<p class="edit-link">', '</p>' ); ?>

    </main><!-- #main -->
</section><!-- #primary -->

<?php get_footer(); ?>