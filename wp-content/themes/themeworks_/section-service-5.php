<?php
    /**
     * Service 5
     *
     * The template used for displaying service section in four column layout
     *
     * @package themeworks
     */

    global $paged, $post, $max_pages;
    $paged = ( get_query_var('paged') ) ? get_query_var('paged') : 1;

    $args = array(
        'post_type' => 'service',
        'posts_per_page' => 4,
        'ignore_sticky_posts' => true,
        'paged' => $paged
        );

    $temp = $wp_query;
    $wp_query = null;

    $wp_query = new WP_Query();
    $wp_query->query( apply_filters( 'tw_service_args_filter', $args ) );
    $max_pages = $wp_query->max_num_pages;

?>
<?php do_action( 'tw_service_above_hook' ); ?>
<?php if ( have_posts() ) : ?>

    <section id="section-service-5" class="section service service-5">
        <h2 class="section-title"><?php echo apply_filters( 'tw_service_title', __( 'Services', 'themeworks' ) ); ?></h2>
        <div class="container">
            <div class="section-content">

                <?php /* Start the Loop */ ?>
                <?php while ( have_posts() ) : the_post(); ?>

                    <article id="post-<?php the_ID(); ?>" <?php post_class( 'col four-col' ); ?>>
                        <figure>
                            <?php  if ( has_post_thumbnail() ) : ?>
                                <div class="entry-image"><a href="<?php the_permalink(); ?>"><?php the_post_thumbnail('square'); ?></a></div>
                            <?php endif; ?>

                            <figcaption>
                                <h3><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
                                <?php the_excerpt(); ?>
                            </figcaption>
                        </figure>
                    </article><!-- #post-## -->

                <?php endwhile; wp_reset_query(); $wp_query = $temp; ?>
                <?php
                    if ( function_exists( 'themeworks_custom_pagination' ) && is_page_template( 'page-service.php' ) ) {
                        themeworks_custom_pagination( $max_pages, "", $paged );
                    }
                ?>

            </div><!-- .section-content -->
        </div><!-- .container -->
    </section>

<?php endif; ?>
<?php do_action( 'tw_service_below_hook' ); ?>