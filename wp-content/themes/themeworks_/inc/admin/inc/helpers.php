<?php

/**
 * Get list of taxonomies
 */
function themeworks_get_taxonomy_list( $taxonomy = 'category', $firstblank = false ) {

    $args = array(
        'hide_empty' => 1
    );

    $terms_obj = get_terms( $taxonomy, $args );
    $terms = array();
    if( $firstblank ) {
        $terms['']['name'] = '';
        $terms['']['title'] = __( '-- Choose One --', 'themeworks' );
    }
    foreach ( $terms_obj as $tt ) {
        $terms[ $tt->slug ]['name'] = $tt->slug;
        $terms[ $tt->slug ]['title'] = $tt->name;
    }

    return $terms;
}

/**
 * Get ecommerce post type
 */
function themeworks_ecommerce_post_type() {

    global $theme_options;

    if ( $theme_options['ecommerce_type'] == 'sell_media' ):
        $post_type = 'sell_media_item';

    elseif ( $theme_options['ecommerce_type'] == 'woocommerce' ):
        $post_type = 'product';

    elseif ( $theme_options['ecommerce_type'] == 'edd' ):
        $post_type = 'download';

    else:
        $post_type = 'undefined';

    endif;

    return $post_type;

}