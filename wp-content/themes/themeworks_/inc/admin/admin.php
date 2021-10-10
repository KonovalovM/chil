<?php

/**
 * Return current theme ID
 */
function themeworks_get_current_theme_id() {

    $themedata = wp_get_theme();
    $theme_title = $themedata->title;
    $theme_shortname = strtolower( preg_replace( '/[^\da-z]/i', '_', $theme_title ) );

    return $theme_shortname;

}

/**
 * Load customizer functionality
 */
function themeworks_after_setup_theme() {

    require get_template_directory() . '/inc/admin/inc/actions.php';
    require get_template_directory() . '/inc/admin/inc/customizer.php';
    require get_template_directory() . '/inc/admin/inc/customizer-extends.php';
    require get_template_directory() . '/inc/admin/inc/fonts.php';
    require get_template_directory() . '/inc/admin/inc/helpers.php';
    require get_template_directory() . '/inc/admin/inc/welcome.php';

}
add_action( 'after_setup_theme', 'themeworks_after_setup_theme', 10 );

/**
 * Add custom url field to media uploader
 */
function themeworks_image_attachment_fields_to_edit( $form_fields, $post ) {

    $form_fields['themeworks_custom_url']['label'] = __( 'URL', 'themeworks' );
    $form_fields['themeworks_custom_url']['input'] = 'text';
    $form_fields['themeworks_custom_url']['value'] = get_post_meta( $post->ID, '_themeworks_custom_url', true );
    $form_fields['themeworks_custom_url']['helps'] = __( 'URL to link this image', 'themeworks' );

    return $form_fields;
}
add_filter( 'attachment_fields_to_edit', 'themeworks_image_attachment_fields_to_edit', null, 2 );

/**
 * Save custom url field in media uploader
 */
function themeworks_image_attachment_fields_to_save( $post, $attachment ) {

    if ( isset( $attachment['themeworks_custom_url'] ) ) {
        update_post_meta( $post['ID'], '_themeworks_custom_url', $attachment['themeworks_custom_url'] );
    }
    return $post;
}
add_filter( 'attachment_fields_to_save', 'themeworks_image_attachment_fields_to_save', null, 2 );