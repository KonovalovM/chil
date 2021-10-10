<?php

/**
 * Register Service post type
 */
function themeworks_service_create_type() {
    $labels = array(
            'name'                      => __( 'Services', 'themeworks' ),
            'singular_name'             => __( 'Service', 'themeworks' ),
            'add_new'                   => __( 'Add New', 'themeworks' ),
            'add_new_item'              => __( 'Add Service', 'themeworks' ),
            'new_item'                  => __( 'Add Service', 'themeworks' ),
            'view_item'                 => __( 'View Service', 'themeworks' ),
            'search_items'              => __( 'Search Services', 'themeworks' ),
            'edit_item'                 => __( 'Edit Service', 'themeworks' ),
            'all_items'                 => __( 'All Services', 'themeworks' ),
            'not_found'                 => __( 'No Services found', 'themeworks' ),
            'not_found_in_trash'        => __( 'No Services found in Trash', 'themeworks' )
        );

    register_post_type('service',
        array(
            'labels' => apply_filters( 'tw_service_labels_filter', $labels ),
            'public' => true,
            'show_ui' => true,
            'capability_type' => 'post',
            'hierarchical' => false,
            'rewrite' => array( 'slug' => apply_filters( 'tw_service_slug_filter', __( 'service', 'themeworks' ) ), 'with_front' => false ),
            'query_var' => false,
            'supports' => array( 'title', 'revisions', 'thumbnail', 'editor', 'excerpt' ),
            'menu_position' => 4,
            'has_archive' => apply_filters( 'tw_service_archive_filter', __( 'services', 'themeworks' ) )
        )
    );
    flush_rewrite_rules();
}
add_action( 'init', 'themeworks_service_create_type' );

?>