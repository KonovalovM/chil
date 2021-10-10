<?php
add_action( 'cmb2_init', 'chil_slides' );
add_action( 'cmb2_init', 'chil_home_mobile' );
add_action( 'cmb2_init', 'chil_page_header' );
add_action( 'cmb2_init', 'chil_press' );

function chil_show_if_front_page( $cmb ) {
	// Don't show this metabox if it's not the front page template
	if ( $cmb->object_id !== get_option( 'page_on_front' ) ) {
		return false;
	}
	return true;
}

function chil_slides() {

	// Start with an underscore to hide fields from custom fields list
	$prefix = '__chil_';

	$cmb = new_cmb2_box( array(
		'id'            => $prefix . 'metabox',
		'title'         => __( 'Chilantro Slides', 'cmb2' ),
		'object_types'  => array( 'page', ), // Post type
		'context'    	=> 'normal',
		'priority'		=> 'high',
		'show_on'		=> array( 'key' => 'page-template', 'value' => 'page-templates/slideshow.php' ),
	) );

	$group_field_id = $cmb->add_field( array(
    	'id'          => $prefix . 'slides',
    	'type'        => 'group',
    	'description' => __( '', 'cmb2' ),
    	'repeatable'  => true, // use false if you want non-repeatable group
    	'options'     => array(
        	'group_title'   => __( 'Slide {#}', 'cmb2' ), // since version 1.1.4, {#} gets replaced by row number
        	'add_button'    => __( 'Add Another Slide', 'cmb2' ),
        	'remove_button' => __( 'Remove Slide', 'cmb2' ),
        	'sortable'      => true, // beta
        	// 'closed'     => true, // true to have the groups closed by default
    	),
	) );

	$cmb->add_group_field( $group_field_id, array(
		'name' => 'Image',
		'desc' => 'Upload an image or enter an URL.',
		'id' => $prefix . 'image',
		'type' => 'file',
		'allow' => array( 'url', 'attachment' )
	) );

	$cmb->add_group_field( $group_field_id, array(
		'name' => 'Text',
		'desc' => '',
		'id' => $prefix . 'text',
		'type' => 'wysiwyg',
		'options' => array(),
	) );
}

function chil_home_mobile() {

    // Start with an underscore to hide fields from custom fields list
    $prefix = '_chil_home_mobile_';

    $cmb = new_cmb2_box( array(
        'id'            => $prefix . 'metabox',
        'title'         => __( 'Chilantro Mobile Header', 'cmb2' ),
        'object_types'  => array( 'page', ), // Post type
        'context'    => 'normal',
        'priority'   => 'high',
        'show_on'		=> array( 'key' => 'page-template', 'value' => 'page-templates/slideshow.php' ),
    ) );

    $cmb->add_field( array(
        'name' => 'Mobile Header Image',
		'desc' => 'Upload an image or enter an URL.',
		'id' => $prefix . 'image',
		'type' => 'file',
		'allow' => array( 'url', 'attachment' )
    ) );

    $cmb->add_field( array(
        'name' => 'Mobile Header Text',
		'desc' => '',
		'id' => $prefix . 'text',
		'type' => 'wysiwyg',
		'options' => array(),
    ) );
}

function chil_page_header() {

    // Start with an underscore to hide fields from custom fields list
    $prefix = '_chil_page_header_';

    $cmb = new_cmb2_box( array(
        'id'            => $prefix . 'metabox',
        'title'         => __( 'Chilantro Page Header', 'cmb2' ),
        'object_types'  => array( 'page', ), // Post type
        'context'    => 'normal',
        'priority'   => 'high',
    ) );

    $cmb->add_field( array(
        'name' => 'Header Image',
		'desc' => 'Upload an image or enter an URL.',
		'id' => $prefix . 'image',
		'type' => 'file',
		'allow' => array( 'url', 'attachment' )
    ) );

    $cmb->add_field( array(
        'name' => 'Header Text',
		'desc' => '',
		'id' => $prefix . 'text',
		'type' => 'wysiwyg',
		'options' => array(),
    ) );

    $cmb->add_field( array(
        'name' => 'Header Text Background Image',
		'desc' => 'Upload an image or enter an URL.',
		'id' => $prefix . 'bg_image',
		'type' => 'file',
		'allow' => array( 'url', 'attachment' )
    ) );

    $cmb->add_field( array(
	    'name' => 'Header Text Background Color',
	    'id'   => $prefix . 'bg_color',
	    'type' => 'colorpicker',
	    'default'  => '#ffffff',
    ) );
}

function chil_press() {

    // Start with an underscore to hide fields from custom fields list
    $prefix = '_chil_press_';

    $cmb = new_cmb2_box( array(
        'id'            => $prefix . 'metabox',
        'title'         => __( 'Chilantro Press Link', 'cmb2' ),
        'object_types'  => array( 'press_article', ), // Post type
        'context'    => 'normal',
        'priority'   => 'high',
    ) );

    $cmb->add_field( array(
		'name' => __( 'External Link', 'cmb2' ),
		'id'   => $prefix . 'external_link',
		'type' => 'text_url',
		// 'protocols' => array( 'http', 'https', 'ftp', 'ftps', 'mailto', 'news', 'irc', 'gopher', 'nntp', 'feed', 'telnet' ), // Array of allowed protocols
    ) );
}

?>