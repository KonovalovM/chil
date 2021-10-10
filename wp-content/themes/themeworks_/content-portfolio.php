<?php
/**
 * @package themeworks
 */

global $themeworks_columns, $themeworks_image_size;

$image_size  = ( ! empty( $themeworks_image_size ) ) ? $themeworks_image_size : 'square';
$columns     = ( ! empty( $themeworks_columns ) ) ? $themeworks_columns : 'three-col';

?>
<article id="post-<?php the_ID(); ?>" <?php post_class( 'col ' . $columns ); ?>>
    <?php  if ( has_post_thumbnail() ) : ?>
        <div class="entry-image">
            <a href="<?php the_permalink(); ?>">
                <?php the_post_thumbnail( $image_size ); ?>
            </a>
        </div>
    <?php endif; ?>
    <div class="post-details">
        <h2 class="entry-title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
        <?php $categories_list = get_the_term_list( $post->ID, 'portfolio-category', '', __( ', ', 'themeworks' ), '' ); ?>
        <span class="cat-links">
            <?php printf( $categories_list ); ?>
        </span>
    </div>
</article>