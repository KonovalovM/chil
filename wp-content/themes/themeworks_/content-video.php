<?php
/**
 * @package themeworks
 */
?>

    <div class="entry-summary">
        <?php

            $content = get_the_content();
            $content = apply_filters( 'the_content', $content );
            $embed_code = '';
            $videourl = get_post_meta( $post->ID, 'themeworks-video', true );

            if( '' == $videourl ) {
                $videourl = get_post_meta( $post->ID, 'themeworks-video', true );
            }
            if ( '' != $videourl ) {
                $embed_code = wp_oembed_get( $videourl );
            }
            if ( '' != $embed_code ) {
                echo $embed_code;
            }
        ?>
    </div><!-- .entry-summary -->
