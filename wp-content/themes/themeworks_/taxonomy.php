<?php
/**
 * The template for displaying Portfolio Taxonomy Archive pages.
 *
 * Learn more: http://codex.wordpress.org/Template_Hierarchy
 *
 * @package themeworks
 */

get_header(); ?>

    <section id="primary" class="content-area">
        <main id="main" class="site-main" role="main">

        <?php if ( have_posts() ) : ?>

            <header class="entry-header">
                <h1 class="entry-title">
                    <?php if ( is_tax() ) :
                            single_term_title();

                        else :
                            _e( 'Archives', 'themeworks' );

                        endif;
                    ?>
                </h1>
            </header><!-- .page-header -->

            <div class="portfolio-gallery portfolio-columns">
                <?php /* Start the Loop */ ?>
                <?php while ( have_posts() ) : the_post(); ?>

                    <?php get_template_part( 'content', 'portfolio' ); ?>

                <?php endwhile; ?>

            </div>
            <?php themeworks_paging_nav(); ?>

        <?php else : ?>

            <?php get_template_part( 'content', 'none' ); ?>

        <?php endif; ?>

        </main><!-- #main -->
    </section><!-- #primary -->

<?php get_footer(); ?>