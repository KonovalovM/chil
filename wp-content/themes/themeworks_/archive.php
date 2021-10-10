<?php
/**
 * The template for displaying Archive pages.
 *
 * Learn more: http://codex.wordpress.org/Template_Hierarchy
 *
 * @package themeworks
 */

get_header(); ?>

    <section id="primary" class="content-area">
        <main id="main" class="site-main" role="main">

        <?php if ( have_posts() ) : ?>

            <header class="page-header">
                <h1 class="page-title">
                    <?php echo themeworks_post_title(); ?>
                </h1>
            </header><!-- .page-header -->

            <?php /* Start the Loop */ ?>
            <?php while ( have_posts() ) : the_post(); ?>

                <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
                    <header class="entry-header">
                        <?php the_title( sprintf( '<h1 class="entry-title"><a href="%s" rel="bookmark">', esc_url( get_permalink() ) ), '</a></h1>' ); ?>

                        <?php if ( 'post' == get_post_type() ) : ?>
                        <div class="entry-meta">
                            <?php themeworks_posted_on(); ?>
                        </div><!-- .entry-meta -->
                        <?php endif; ?>
                    </header><!-- .entry-header -->


                    <div class="entry-content">
                        <?php the_excerpt(); ?>
                    </div><!-- .entry-content -->

                    <footer class="entry-footer">
                        <?php if ( 'post' == get_post_type() ) : // Hide category and tag text for pages on Search ?>
                            <?php
                                /* translators: used between list items, there is a space after the comma */
                                $categories_list = get_the_category_list( __( ', ', 'themeworks' ) );
                                if ( $categories_list && themeworks_categorized_blog() ) :
                            ?>
                            <span class="cat-links">
                                <?php printf( __( 'Posted in %1$s', 'themeworks' ), $categories_list ); ?>
                            </span>
                            <?php endif; // End if categories ?>

                            <?php
                                /* translators: used between list items, there is a space after the comma */
                                $tags_list = get_the_tag_list( '', __( ', ', 'themeworks' ) );
                                if ( $tags_list ) :
                            ?>
                            <span class="tags-links">
                                <?php printf( __( 'Tagged %1$s', 'themeworks' ), $tags_list ); ?>
                            </span>
                            <?php endif; // End if $tags_list ?>
                        <?php endif; // End if 'post' == get_post_type() ?>
                    </footer><!-- .entry-footer -->
                </article><!-- #post-## -->

            <?php endwhile; ?>

            <?php themeworks_paging_nav(); ?>

        <?php else : ?>

            <?php get_template_part( 'content', 'none' ); ?>

        <?php endif; ?>

        </main><!-- #main -->
    </section><!-- #primary -->

<?php get_sidebar(); ?>
<?php get_footer(); ?>