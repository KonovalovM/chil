<?php
/**
 * @package themeworks
 */
?>
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
        <?php the_content(); ?>
        <?php
            wp_link_pages( array(
                'before' => '<div class="pagination">' . __( 'Pages:', 'themeworks' ),
                'after'  => '</div>',
            ) );
        ?>
    </div><!-- .entry-content -->
    <?php do_action( 'tw_below_content_hook' ); ?>
    <?php do_action( 'tw_above_meta_hook' ); ?>
    <footer class="entry-footer">
        <?php
        if ( get_post_type() == 'portfolio' ) {

            $categories_list = get_the_term_list( $post->ID, 'portfolio-category', '', __( ', ', 'themeworks' ), '' ); ?>
            <span class="cat-links">
                <?php printf( $categories_list ); ?>
            </span>

        <?php } elseif ( get_post_type() == 'post' ) {

            /* translators: used between list items, there is a space after the comma */
            $category_list = get_the_category_list( __( ', ', 'themeworks' ) );

            /* translators: used between list items, there is a space after the comma */
            $tag_list = get_the_tag_list( '', __( ', ', 'themeworks' ) );
            $meta_text = '';
            if ( ! themeworks_categorized_blog() ) {
                // This blog only has 1 category so we just need to worry about tags in the meta text
                if ( '' != $tag_list ) {
                    $meta_text = __( '<p class="cats-tags">Tags: %2$s</p>', 'themeworks' );
                }

            } else {
                // But this blog has loads of categories so we should probably display them here
                if ( '' != $tag_list ) {
                    $meta_text = __( '<p class="cats-tags"><span class="cats">Categories: %1$s</span> <span class="tags">Tags: %2$s</span></p>', 'themeworks' );
                } else {
                    $meta_text = __( '<p class="cats-tags">Categories: %1$s</p>', 'themeworks' );
                }

            } // end check for categories on this blog

            printf(
                $meta_text,
                $category_list,
                $tag_list,
                get_permalink()
            );
        }
        ?>

        <?php edit_post_link( __( 'Edit', 'themeworks' ), '<span class="edit-link">', '</span>' ); ?>
    </footer><!-- .entry-footer -->
    <?php do_action( 'tw_below_meta_hook' ); ?>
</article><!-- #post-## -->