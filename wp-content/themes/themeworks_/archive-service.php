<?php
/**
 * The template for displaying Service Archive pages.
 *
 * Learn more: http://codex.wordpress.org/Template_Hierarchy
 *
 * @package themeworks
 */

get_header();

locate_template( themeworks_get_section( 'service' ), true, false );

get_footer();

?>