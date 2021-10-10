<?php
/**
 * Metaboxes for page builder
 *
 * @package themeworks
 */

/*
 * Setup our metaboxes
 */
function themeworks_page_builder_meta_boxes_setup() {
    /* Add meta boxes on the 'add_meta_boxes' hook. */
    add_action( 'add_meta_boxes', 'themeworks_page_builder_add_post_meta_boxes', 10, 2 );
    /* Save post meta on the 'save_post' hook. */
    add_action( 'save_post', 'themeworks_page_builder_save_meta_options', 10, 2 );
}
add_action( 'load-post.php', 'themeworks_page_builder_meta_boxes_setup' );
add_action( 'load-post-new.php', 'themeworks_page_builder_meta_boxes_setup' );

/*
 * Create one or more meta boxes to be displayed on the post editor screen.
 */
function themeworks_page_builder_add_post_meta_boxes() {
    add_meta_box(
        'tw_page_sections', // Unique ID
        esc_html__( 'Page Builder', 'themeworks' ), // Title
        'themeworks_page_builder_meta_options', // Callback function
        'page', // Post Type
        'normal', // Context
        'default' // Priority
    );
}

/*
 * Display the metabox
 */
function themeworks_page_builder_meta_options( $object, $box ) { ?>

    <?php wp_nonce_field( basename( __FILE__ ), 'themeworks_meta_nonce' ); ?>
    <?php $saved_sections = esc_attr( get_post_meta( $object->ID, '_tw_sections', true ) ); ?>
    <?php $sections = themeworks_get_active_section( null ); ?>

    <input class="themeworks-page-sections" type="hidden" name="_tw_sections" value="<?php if ( '' != $saved_sections ) echo $saved_sections; ?>" />
    <div class="themeworks-available-sections">
        <h4><?php _e( 'Your Design Options', 'themeworks' ); ?></h4>
        <ul class="themeworks-section-list">
        <?php foreach ( $sections as $section => $number ) {
            if ( $section != 'header' && $section != 'footer' ) { ?>
                <li><a href="javascript:void(0);" id="<?php echo $section . '-' . $number; ?>" class="themeworks-builder-add-section" title="<?php _e( 'Click to add', 'themeworks' ); ?>"><img src="<?php echo get_template_directory_uri() . '/inc/admin/images/sections/' . $section . '-' . $number . '.png'; ?>" title="<?php _e( 'Click and drag to move', 'themeworks' ); ?>" /><span class="section-label"><span class="dashicons dashicons-plus"></span><?php echo __( 'Add ', 'themeworks' ) . $section; ?></span></a></li>
            <?php }
        } ?>
        </ul>
    </div>

    <div class="themeworks-sortable-container">
        <h4><?php _e( 'Your Page Design', 'themeworks' ); ?></h4>
        <img src="<?php echo get_template_directory_uri() . '/inc/admin/images/sections/chrome-bar.png'; ?>" alt="chrome-bar" class="chrome-bar" />
        <div class="themeworks-page-builder-sortable">
            <p class="help-text"><?php _e( 'Choose a design from the left to get started.', 'themeworks' ); ?></p>

            <?php if ( ! empty ( $saved_sections ) ) {
                $sections = explode( ',', $saved_sections );

                foreach ( $sections as $section ) {
                    $id = explode('-', $section ); ?>

                    <div class="active-section" id="<?php echo $section; ?>">
                        <span class="tw-delete-section dashicons dashicons-no"></span>
                        <div class="drag-handle">
                            <img src="<?php echo get_template_directory_uri() . '/inc/admin/images/sections/' . $section . '.png'; ?>" title="<?php _e( 'Click and drag to move', 'themeworks' ); ?>" />
                        </div>
                    </div>

                <?php } ?>
            <?php } ?>

        </div>
    </div>

<?php }

/*
 * Save the metadata.
 */
function themeworks_page_builder_save_meta_options( $post_id, $post ) {

    /* Verify the nonce before proceeding. */
    if ( ! isset( $_POST['themeworks_meta_nonce'] ) || ! wp_verify_nonce( $_POST['themeworks_meta_nonce'], basename( __FILE__ ) ) )
        return $post_id;

    /* Get the post type object. */
    $post_type = get_post_type_object( $post->post_type );

    /* Check if the current user has permission to edit the post. */
    if ( !current_user_can( $post_type->cap->edit_post, $post_id ) )
        return $post_id;

    /* more than one, so set to an array */
    $names = array( '_tw_sections' );

    foreach ( $names as $name ) {

        $new_meta_value = ( isset( $_POST[ $name ] ) ? $_POST[ $name ] : '' );

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
 * Enqueue scripts to admin_head only on pages
 */
function themeworks_page_builder_admin_scripts( $hook ){

    if ( $hook == 'post.php' && get_post_type() == 'page' || $hook == 'post-new.php' && get_post_type() == 'page' ) {
        wp_enqueue_script( 'themeworks_page_builder', get_template_directory_uri() . '/inc/admin/js/page-builder.js', array( 'jquery-ui-sortable', 'jquery' ) );
        wp_enqueue_style( 'themeworks_page_builder', get_template_directory_uri() . '/inc/admin/css/themeworks-page-builder.css' );

        // localize js for admin scripts
        wp_localize_script( 'themeworks_page_builder', 'themeworks', array(
            'themedir' => get_template_directory_uri()
    ) );
    }
}
add_action( 'admin_enqueue_scripts', 'themeworks_page_builder_admin_scripts' );