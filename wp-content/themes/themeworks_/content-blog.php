<?php
 /**
 * The Template part for displaying normal blog.
 *
 * @package themeworks
 */
global $paged, $max_pages;

if ( get_query_var( 'paged' ) ) {
    $paged = get_query_var( 'paged' );
} elseif ( get_query_var( 'page' ) ) {
    $paged = get_query_var( 'page' );
} else {
    $paged = 1;
}

$args = array(
    'paged' => $paged
);

$temp = $wp_query;
$wp_query = null;

$wp_query = new WP_Query();
$wp_query->query( $args );
$max_pages = $wp_query->max_num_pages;
?>

<section id="section-blog-1" class="section blog blog-1">
    <div class="container">
        <h2 class="section-title"><?php echo apply_filters( 'tw_blog_title', __( 'Blog', 'themeworks' ) ); ?></h2>
        <div class="section-content col two-thirds-col">

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
                        <?php the_content( __( 'Continue reading <span class="meta-nav">&rarr;</span>', 'themeworks' ) ); ?>
                        <?php
                            wp_link_pages( array(
                                'before' => '<div class="pagination">' . __( 'Pages:', 'themeworks' ),
                                'after'  => '</div>',
                            ) );
                        ?>
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

                        <?php if ( ! post_password_required() && ( comments_open() || '0' != get_comments_number() ) ) : ?>
                        <span class="comments-link"><?php comments_popup_link( __( 'Leave a comment', 'themeworks' ), __( '1 Comment', 'themeworks' ), __( '% Comments', 'themeworks' ) ); ?></span>
                        <?php endif; ?>

                        <?php edit_post_link( __( 'Edit', 'themeworks' ), '<span class="edit-link">', '</span>' ); ?>
                    </footer><!-- .entry-footer -->
                </article><!-- #post-## -->

            <?php endwhile; ?>

            <?php
                if ( function_exists( 'themeworks_custom_pagination' ) ) {
                    themeworks_custom_pagination( "", "", $paged );
                }
            ?>

            <?php wp_reset_query(); ?>

        </div><!-- .section-content -->

        <?php get_sidebar(); ?>

    </div><!-- .container -->
</section>