<?php
/**
 * The Sidebar containing the main widget areas.
 *
 * @package themeworks
 */
?>
    <div id="secondary" class="widget-area col one-third-col last" role="complementary">
        <?php themeworks_social( 'sidebar' ); ?>
        <?php dynamic_sidebar( 'sidebar' ); ?>
    </div><!-- #secondary -->
