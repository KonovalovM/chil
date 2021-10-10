<?php
/**
 * @package themeworks
 */
?>

    <div class="entry-summary">
        <?php

            $pattern = get_shortcode_regex();

            if ( preg_match( "/$pattern/s", get_the_content(), $match ) ) {
                if ( 'gallery' == $match[2] ) {
                    echo do_shortcode_tag( $match );
                }
            }

        ?>
    </div><!-- .entry-summary -->
