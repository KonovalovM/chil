<?php get_header(); ?>

<section id="primary" class="content-area portfolio-page">

    <h2 class="section-title"><?php the_title(); ?></h2>

    <main id="main" class="site-main" role="main">

        <?php
        global $paged;

        if ( get_query_var('paged') ) {
            $paged = get_query_var('paged');
        } elseif ( get_query_var('page') ) {
            $paged = get_query_var('page');
        } else {
            $paged = 1;
        }

        $args = array(
            'post_type'=>'portfolio',
            'paged' => $paged
        );

        $temp = $wp_query;
        $wp_query = null;

        $wp_query = new WP_Query();
        $wp_query->query( $args );
        $max_pages = $wp_query->max_num_pages;

        ?>

        <?php if ( $wp_query->have_posts() ) : ?>

            <?php while ( $wp_query->have_posts() ) : $wp_query->the_post(); ?>

                <?php get_template_part( 'content', 'portfolio' ); ?>

            <?php endwhile; wp_reset_query(); $wp_query = $temp; ?>

            <?php edit_post_link( __( 'Edit', 'themeworks' ), '<p class="edit-link">', '</p>' ); ?>
            <?php
                if ( function_exists( 'themeworks_custom_pagination' ) && ( is_page_template( 'page-portfolio-1.php' ) || is_page_template( 'page-portfolio-2.php' ) || is_page_template( 'page-portfolio-3.php' ) || is_page_template( 'page-portfolio-4.php' ) ) ) {
                    themeworks_custom_pagination( $max_pages, "", $paged );
                }
            ?>

        <?php else : ?>

            <?php get_template_part( 'content', 'none' ); ?>

        <?php endif; ?>

    </main><!-- #main -->
</section><!-- #primary -->

<?php get_footer(); ?>