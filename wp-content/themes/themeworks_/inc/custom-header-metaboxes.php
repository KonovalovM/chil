<?php

/**
 * Metaboxes for creative control over Custom Header and Text colors
 * Useful in case background images conflict with default text color
 *
 * @package themeworks
 */

/*
 * Setup our metaboxes
 */
function themeworks_meta_boxes_setup() {
    /* Add meta boxes on the 'add_meta_boxes' hook. */
    add_action( 'add_meta_boxes', 'themeworks_add_meta_boxes' );
    /* Save post meta on the 'save_post' hook. */
    add_action( 'save_post', 'themeworks_save_text_fx_meta', 10, 2 );
}
add_action( 'load-post.php', 'themeworks_meta_boxes_setup' );
add_action( 'load-post-new.php', 'themeworks_meta_boxes_setup' );

/*
 * Enqueue scripts and styles
 */
function themeworks_enqueue_header_scripts_styles( $hook ) {
    // only add to specific admin pages
    if ( 'post.php' == $hook || 'post-new.php' == $hook ) {
        wp_enqueue_script( 'wp-color-picker' );
        wp_enqueue_style( 'wp-color-picker' );
    }
}
add_action( 'admin_enqueue_scripts', 'themeworks_enqueue_header_scripts_styles', 40 );

/*
 * Enqueue scripts to admin_head only on post
 */
function themeworks_enqueue_header_scripts() {
    global $pagenow;
    // only add to specific admin pages
    if ( 'post.php' == $pagenow || 'post-new.php' == $pagenow ) {
        wp_enqueue_script( 'themeworks-custom-header', get_template_directory_uri() .'/inc/admin/js/custom-header.js', array( 'jquery' ) );
    }
}
add_action( 'admin_enqueue_scripts', 'themeworks_enqueue_header_scripts', 100 );

/*
 * Create one or more meta boxes to be displayed on the post editor screen.
 */
function themeworks_add_meta_boxes( $post_type ) {
    add_meta_box(
        'themeworks-header-image', // Unique ID
        esc_html__( 'Custom Header', 'themeworks' ), // Title
        'themeworks_header_image_meta_box', // Callback function
        $post_type, // Admin page (or post type)
        'side', // Context
        'default' // Priority
    );
}

/*
 * Add post type support
 */
function post_type_support() {
    add_post_type_support( 'post', 'custom-header' );
    add_post_type_support( 'page', 'custom-header' );
    add_post_type_support( 'portfolio', 'custom-header' );
    add_post_type_support( 'services', 'custom-header' );
}
add_action( 'init', 'post_type_support' );

/*
 * Display the text positioning metabox
 */
function themeworks_header_image_meta_box( $object, $box ) {
    wp_nonce_field( basename( __FILE__ ), 'themeworks_header_meta_nonce' );

    $themeworks_saved_header_image_id = esc_attr( get_post_meta( $object->ID, '_themeworks-header-image', true ) );
    $themeworks_saved_header_image_array = wp_get_attachment_image_src( $themeworks_saved_header_image_id, 'header-image' );
    $themeworks_saved_header_image = $themeworks_saved_header_image_array[0];
    $themeworks_saved_text_color = esc_attr( get_post_meta( $object->ID, '_themeworks-text-color', true ) );
    ?>

    <p>
        <a href="#" class="themeworks-add-image"><img class="themeworks-header-image-url" src="<?php echo $themeworks_saved_header_image; ?>" style="max-width: 100%; max-height: 200px; height: auto; display: block;" /></a>
        <input type="hidden" name="_themeworks-header-image" class="themeworks-header-image" id="themeworks-header-image" value="<?php if( '' != $themeworks_saved_header_image_id ) { echo $themeworks_saved_header_image_id; } ?>" />
        <a href="#" class="themeworks-set-image"><?php _e( 'Set header image', 'themeworks' ); ?></a>
        <a href="#" class="themeworks-remove-image"><?php _e( 'Remove header image', 'themeworks' ); ?></a>
    </p>

    <p class="colorPickerWrapper">
        <label for="themeworks-text-color"><strong><?php _e( 'Text Color:', 'themeworks' ); ?></strong></label><br/>
        <input id="textcolorpicker" name="_themeworks-text-color" class="color-picker" type="text" value="#<?php if( '' == $themeworks_saved_text_color ) { echo 'ffffff'; } else { echo $themeworks_saved_text_color; } ?>" />
    </p>
<?php }

/*
 * Save the text position post metadata.
 */
function themeworks_save_text_fx_meta( $post_id, $post ) {

    /* Verify the nonce before proceeding. */
    if ( ! isset( $_POST['themeworks_header_meta_nonce'] ) || ! wp_verify_nonce( $_POST['themeworks_header_meta_nonce'], basename( __FILE__ ) ) )
        return $post_id;

    /* Get the post type object. */
    $post_type = get_post_type_object( $post->post_type );

    /* Check if the current user has permission to edit the post. */
    if ( !current_user_can( $post_type->cap->edit_post, $post_id ) )
        return $post_id;

    /* more than one, so set to an array */
    $names = array( '_themeworks-header-image', '_themeworks-text-color' );

    foreach ( $names as $name ) {

        /* Get the posted data and sanitize it for use as an HTML class. */
        $new_meta_value = ( isset( $_POST[ $name ] ) ? ( $_POST[ $name ] ) : '' );
        $new_meta_value= ltrim ( $new_meta_value, '#' );
        /* Get the meta key. */
        $meta_key = $name;

        /* Get the meta value of the custom field key. */
        $meta_value = get_post_meta( $post_id, $meta_key, true );

        /* If a new meta value was added and there was no previous value, add it. */
        if ( $new_meta_value && '' == $meta_value )
            add_post_meta( $post_id, $meta_key, $new_meta_value, true );

        /* If the new meta value does not match the old value, update it. */
        elseif ( $new_meta_value && $new_meta_value != $meta_value )
            update_post_meta( $post_id, $meta_key, $new_meta_value );

        /* If there is no new meta value but an old value exists, delete it. */
        elseif ( '' == $new_meta_value && $meta_value )
            delete_post_meta( $post_id, $meta_key, $meta_value );

    }
}

/*
 * The user-define text and border colors
 */
function themeworks_text_color() {
    global $post;
    $color = esc_attr( get_post_meta( $post->ID, 'themeworks-text-color', true ) );
    if ( $color ) {
        $style = '<style>.post-' . $post->ID . ' .entry-inner, .post-' . $post->ID . ' .entry-content, .post-' . $post->ID . ' .entry-inner a, .post-' . $post->ID . ' .entry-inner a:hover { color:#' . $color . '; border-color: #' . $color . '; }</style>';
        echo $style;
    }
}
add_action( 'themeworks_above_entry', 'themeworks_text_color' );

?>