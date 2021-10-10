<?php
/**
 * The Template for displaying all single image attachments.
 *
 * @package themeworks
 */

get_header(); ?>

    <div id="primary" class="content-area">
        <main id="main" class="site-main" role="main">
        <?php do_action( 'tw_above_entry_hook' ); ?>
        <?php while ( have_posts() ) : the_post(); ?>
            <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
                <?php do_action( 'tw_above_title_hook' ); ?>
                <header class="entry-header">
                    <?php the_title( '<h1 class="entry-title">', '</h1>' ); ?>
                    <div class="entry-meta">
                        <?php themeworks_posted_on(); ?>
                    </div><!-- .entry-meta -->
                </header><!-- .entry-header -->
                <?php do_action( 'tw_below_title_hook' ); ?>
                <?php do_action( 'tw_above_content_hook' ); ?>
                <div class="entry-content">
                    <div class="entry-attachment">
                            <div class="attachment">
                                <?php
                                    $attachments = array_values( get_children( array( 'post_parent' => $post->post_parent, 'post_status' => 'inherit', 'post_type' => 'attachment', 'post_mime_type' => 'image', 'order' => 'ASC', 'orderby' => 'menu_order ID' ) ) );
                                    foreach ( $attachments as $k => $attachment ) {
                                        if ( $attachment->ID == $post->ID )
                                            break;
                                    }
                                    $k++;
                                    // If there is more than 1 attachment in a gallery
                                    if ( count( $attachments ) > 1 ) {
                                        if ( isset( $attachments[ $k ] ) )
                                            // get the URL of the next image attachment
                                            $next_attachment_url = get_attachment_link( $attachments[ $k ]->ID );
                                        else
                                            // or get the URL of the first image attachment
                                            $next_attachment_url = get_attachment_link( $attachments[ 0 ]->ID );
                                    } else {
                                        // or, if there's only 1 image, get the URL of the image
                                        $next_attachment_url = wp_get_attachment_url();
                                    }
                                ?>

                                <a href="<?php echo $next_attachment_url; ?>" title="<?php echo esc_attr( get_the_title() ); ?>" rel="attachment"><?php
                                    echo wp_get_attachment_image( $post->ID, 'large' );
                                ?></a>
                            </div><!-- .attachment -->

                            <?php if ( ! empty( $post->post_excerpt ) ) : ?>
                            <div class="entry-caption">
                                <?php the_excerpt(); ?>
                            </div>
                            <?php endif; ?>
                        </div><!-- .entry-attachment -->

                        <?php the_content(); ?>
                        <?php wp_link_pages( array( 'before' => '<div class="page-links">' . __( 'Pages:', 'albedo' ), 'after' => '</div>' ) ); ?>
                </div><!-- .entry-content -->
                <?php do_action( 'tw_below_content_hook' ); ?>
                <?php do_action( 'tw_above_meta_hook' ); ?>
                <footer class="entry-footer">
                    <?php edit_post_link( __( 'Edit', 'themeworks' ), '<span class="edit-link">', '</span>' ); ?>
                </footer><!-- .entry-footer -->
                <?php do_action( 'tw_below_meta_hook' ); ?>
            </article><!-- #post-## -->
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

<?php get_footer(); ?>