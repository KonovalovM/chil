<?php
/**
 * Custom functions that act independently of the theme templates
 *
 * Eventually, some of the functionality here could be replaced by core features
 *
 * @package themeworks
 */

/**
 * Add theme support for Infinite Scroll.
 * See: http://jetpack.me/support/infinite-scroll/
 */
function themeworks_jetpack_setup() {
    add_theme_support( 'infinite-scroll', array(
        'container' => 'main',
        'footer'    => 'page',
    ) );
}
add_action( 'after_setup_theme', 'themeworks_jetpack_setup' );

/**
 * Get our wp_nav_menu() fallback, wp_page_menu(), to show a home link.
 *
 * @param array $args Configuration arguments.
 * @return array
 */
function themeworks_page_menu_args( $args ) {
    $args['show_home'] = true;
    return $args;
}
add_filter( 'wp_page_menu_args', 'themeworks_page_menu_args' );

/**
 * Adds custom classes to the array of body classes.
 *
 * @param array $classes Classes for the body element.
 * @return array
 */
function themeworks_body_classes( $classes ) {
    global $theme_options;

    // Adds a class of group-blog to blogs with more than 1 published author.
    if ( is_multi_author() )
        $classes[] = 'group-blog';

    // Adds class of has-sidebar if sidebar is active and has widgets
    if ( ( is_active_sidebar( 'sidebar' ) && is_singular( 'post' ) ) || ( is_active_sidebar( 'sidebar' ) && is_category() ) || ( is_active_sidebar( 'sidebar' ) && is_date() ) || ( is_active_sidebar( 'sidebar' ) && is_tag() ) || is_singular( 'page' ) || is_post_type_archive( 'post' ) || is_post_type_archive( 'page' ) ||  is_search() || is_page_template( 'page-blog.php' ) )
        $classes[] = 'has-sidebar';

    // Adds class of has-header if a header is set
    if ( get_header_image() )
        $classes[] = 'has-header';
    else
        $classes[] = 'no-header';

    // Adds class of has-header-slideshow if header slideshow has images
    if ( ! empty( $theme_options['header_slideshow'] ) )
        $classes[] = 'has-header-slideshow';
    if( is_page_template( 'page-portfolio.php' ) )
        $classes[] = 'is-section';
    // Adds class of current color palette
    if ( ! empty( $theme_options['color'] ) )
        $classes[] = $theme_options['color'];

    if ( get_header_image() )
        $classes[] = 'has-header-image';

    if ( is_customize_preview() )
        $classes[] = 'customizer-preview';

    if ( is_home() && ! empty( $theme_options['parallax'] ) )
        $classes[] = 'parallax';


    return $classes;
}
add_filter( 'body_class', 'themeworks_body_classes' );

/**
 * Filters wp_title to print a neat <title> tag based on what is being viewed.
 *
 * @param string $title Default title text for current view.
 * @param string $sep Optional separator.
 * @return string The filtered title.
 */
function themeworks_wp_title( $title, $sep ) {
    if ( is_feed() ) {
        return $title;
    }

    global $page, $paged;

    // Add the blog name
    $title .= get_bloginfo( 'name', 'display' );

    // Add the blog description for the home/front page.
    $site_description = get_bloginfo( 'description', 'display' );
    if ( $site_description && ( is_home() || is_front_page() ) ) {
        $title .= " $sep $site_description";
    }

    // Add a page number if necessary:
    if ( ( $paged >= 2 || $page >= 2 ) && ! is_404() ) {
        $title .= " $sep " . sprintf( __( 'Page %s', 'themeworks' ), max( $paged, $page ) );
    }

    return $title;
}
add_filter( 'wp_title', 'themeworks_wp_title', 10, 2 );

/**
 * Sets the authordata global when viewing an author archive.
 *
 * This provides backwards compatibility with
 * http://core.trac.wordpress.org/changeset/25574
 *
 * It removes the need to call the_post() and rewind_posts() in an author
 * template to print information about the author.
 *
 * @global WP_Query $wp_query WordPress Query object.
 * @return void
 */
function themeworks_setup_author() {
    global $wp_query;

    if ( $wp_query->is_author() && isset( $wp_query->post ) ) {
        $GLOBALS['authordata'] = get_userdata( $wp_query->post->post_author );
    }
}
add_action( 'wp', 'themeworks_setup_author' );



if ( ! function_exists( 'themeworks_comment' ) ) :
/**
 * Template for comments and pingbacks.
 *
 * To override this walker in a child theme without modifying the comments template
 * simply create your own themeworks_comment(), and that function will be used instead.
 *
 * Used as a callback by wp_list_comments() for displaying the comments.
 *
 * @since themeworks 1.0
 */
function themeworks_comment( $comment, $args, $depth ) {
    $GLOBALS['comment'] = $comment;
    switch ( $comment->comment_type ) :
        case 'pingback' :
        case 'trackback' :
        // Display trackbacks differently than normal comments.
    ?>
    <li <?php comment_class(); ?> id="comment-<?php comment_ID(); ?>">
        <p><?php _e( 'Pingback:', 'themeworks' ); ?> <?php comment_author_link(); ?> <?php edit_comment_link( __( '(Edit)', 'themeworks' ), '<span class="edit-link">', '</span>' ); ?></p>
    <?php
            break;
        default :
        // Proceed with normal comments.
        global $post;
    ?>
    <li <?php comment_class(); ?> id="li-comment-<?php comment_ID(); ?>">
        <article id="comment-<?php comment_ID(); ?>" class="comment-article">
            <header class="comment-meta comment-author vcard">
                <?php
                    echo get_avatar( $comment, 64 );
                    echo '<div class="comment-metadata">';
                        printf( '<cite><b class="fn">%1$s</b> %2$s</cite>',
                            get_comment_author(),
                            // If current post author is also comment author, make it known visually.
                            ( $comment->user_id === $post->post_author ) ? '<span>' . __( 'Post author', 'themeworks' ) . '</span>' : ''
                        );
                        printf( '<p class="datetime"><a href="%1$s"><time datetime="%2$s">%3$s</time></a></p>',
                            esc_url( get_comment_link( $comment->comment_ID ) ),
                            get_comment_time( 'c' ),
                            /* translators: 1: date, 2: time */
                            sprintf( __( '%1$s at %2$s', 'themeworks' ), get_comment_date(), get_comment_time() )
                        );
                    echo '</div>';
                ?>
            </header><!-- .comment-meta -->

            <?php if ( '0' == $comment->comment_approved ) : ?>
                <p class="comment-awaiting-moderation"><?php _e( 'Your comment is awaiting moderation.', 'themeworks' ); ?></p>
            <?php endif; ?>

            <section class="comment-content">
                <?php comment_text(); ?>
                <?php edit_comment_link( __( 'Edit', 'themeworks' ), '<p class="edit-link">', '</p>' ); ?>
            </section><!-- .comment-content -->

            <div class="reply">
                <?php comment_reply_link( array_merge( $args, array( 'reply_text' => __( 'Reply', 'themeworks' ), 'after' => '', 'depth' => $depth, 'max_depth' => $args['max_depth'] ) ) ); ?>
            </div><!-- .reply -->
        </article><!-- #comment-## -->
    <?php
        break;
    endswitch; // end comment_type check
}
endif;

/**
 * Flush your rewrite rules for custom post type and taxonomies added in theme
 */
function themeworks_flush_rewrite_rules() {
    global $pagenow, $wp_rewrite;

    if ( 'themes.php' == $pagenow && isset( $_GET['activated'] ) )
        $wp_rewrite->flush_rules();
}

add_action( 'load-themes.php', 'themeworks_flush_rewrite_rules' );

/**
 * WordPress gallery support
 * Converts gallery shortcode markup into markup required for slideshows
 * @param  $attr
 * @return $output
 */
function themeworks_gallery_shortcode( $attr ) {

    global $theme_options;
    $post = get_post();

    static $instance = 0;
    $instance++;

    if ( ! empty( $attr['ids'] ) ) {
        // 'ids' is explicitly ordered, unless you specify otherwise.
        if ( empty( $attr['orderby'] ) )
            $attr['orderby'] = 'post__in';
        $attr['include'] = $attr['ids'];
    }

    // Allow plugins/themes to override the default gallery template.
    $output = apply_filters('post_gallery', '', $attr);
    if ( $output != '' )
        return $output;

    // We're trusting author input, so let's at least make sure it looks like a valid orderby statement
    if ( isset( $attr['orderby'] ) ) {
        $attr['orderby'] = sanitize_sql_orderby( $attr['orderby'] );
        if ( !$attr['orderby'] )
            unset( $attr['orderby'] );
    }

    extract(shortcode_atts(array(
        'order'      => 'ASC',
        'orderby'    => 'menu_order ID',
        'id'         => $post->ID,
        'itemtag'    => 'dl',
        'icontag'    => 'dt',
        'captiontag' => 'dd',
        'columns'    => 3,
        'size'       => 'large',
        'include'    => '',
        'exclude'    => ''
    ), $attr));

    $id = intval($id);
    if ( 'RAND' == $order )
        $orderby = 'none';

    if ( !empty($include) ) {
        $_attachments = get_posts( array('include' => $include, 'post_status' => 'inherit', 'post_type' => 'attachment', 'post_mime_type' => 'image', 'order' => $order, 'orderby' => $orderby) );

        $attachments = array();
        foreach ( $_attachments as $key => $val ) {
            $attachments[$val->ID] = $_attachments[$key];
        }
    } elseif ( !empty($exclude) ) {
        $attachments = get_children( array('post_parent' => $id, 'exclude' => $exclude, 'post_status' => 'inherit', 'post_type' => 'attachment', 'post_mime_type' => 'image', 'order' => $order, 'orderby' => $orderby) );
    } else {
        $attachments = get_children( array('post_parent' => $id, 'post_status' => 'inherit', 'post_type' => 'attachment', 'post_mime_type' => 'image', 'order' => $order, 'orderby' => $orderby) );
    }

    if ( empty($attachments) )
        return '';

    if ( is_feed() ) {
        $output = "\n";
        foreach ( $attachments as $att_id => $attachment )
            $output .= wp_get_attachment_link($att_id, $size, true) . "\n";
        return $output;
    }

    $itemtag = tag_escape($itemtag);
    $captiontag = tag_escape($captiontag);
    $columns = intval($columns);
    $itemwidth = $columns > 0 ? floor(100/$columns) : 100;
    $float = is_rtl() ? 'right' : 'left';

    $selector = "gallery-{$instance}";

    $gallery_style = $gallery_div = '';
    if ( apply_filters( 'use_default_gallery_style', true ) )
        $gallery_style = "
        <style type='text/css'>
            #{$selector} {
                margin: auto;
            }
            #{$selector} .gallery-item {
                float: {$float};
                margin-top: 10px;
                text-align: center;
                width: {$itemwidth}%;
            }
            #{$selector} img {
                border: 2px solid #cfcfcf;
            }
            #{$selector} .gallery-caption {
                margin-left: 0;
            }
        </style>
        <!-- see gallery_shortcode() in wp-includes/media.php -->";
    $size_class = sanitize_html_class( $size );
    $gallery_div = "<div id='$selector' class='gallery themeworks-gallery flexslider galleryid-{$id} gallery-columns-{$columns} gallery-size-{$size_class}'>";
    $output = apply_filters( 'gallery_style', $gallery_style . "\n\t\t" . $gallery_div );
    $output .= "<ul class='slides'>";
    $i = 0;
    foreach ( $attachments as $id => $attachment ) {
        $link = wp_get_attachment_image( $id, "large", 0 );
        $output .= "<li>";
        $output .= "$link";
        if ( $captiontag && trim($attachment->post_excerpt) ) {
            $output .= "
                <p class='flex-caption'>
                " . wptexturize($attachment->post_excerpt) . "
                </p>";

        }
        $output .= "</li>";
    }
    $output .= "</ul> <!-- .slides -->";

    if ( $theme_options['slideshow_navigation'] == true ) {

        $output .= '<ul class="slide-thumbs">';
        foreach( $attachments as $id => $attachment ) {
            $link = wp_get_attachment_image( $id, "thumbnail", 0 );
            $output .= '<li>';
            $output .= "$link";
            $output .= '</li>';
        }
        $output .= '</ul>';
    }

    $output .= "</div>\n";

    return $output;
}

/**
 * Replaces gallery shortcode
 */
function themeworks_replace_gallery_shortcode() {

    // Return if JetPack is installed
    if ( class_exists( 'Jetpack' ) && Jetpack::is_module_active( 'shortcodes' ) )
        return;

    remove_shortcode( 'gallery' );
    add_shortcode( 'gallery' , 'themeworks_gallery_shortcode' );

}
add_action( 'init', 'themeworks_replace_gallery_shortcode' );

/**
 * Get ecommerce post thumbnail
 */
function themeworks_ecommerce_thumbnail() {

    global $theme_options, $post;

    if ( $theme_options['ecommerce_type'] == 'sell_media' && class_exists('SellMedia') ):

        //Get Post Attachment ID
        $sell_media_attachment_id = get_post_meta( $post->ID, '_sell_media_attachment_id', true );
        if ( $sell_media_attachment_id ){
            $attachment_id = $sell_media_attachment_id;
        } else {
            $attachment_id = get_post_thumbnail_id( $post->ID );
        }

        sell_media_item_icon( $attachment_id, 'square' );

    else:
        the_post_thumbnail( 'square' );

    endif;

}

/**
 * User profile fields page
 * @return array
 */
function themeworks_user_profile_fields() {

    $fields = array(
        'staff' => array(
            'id' => 'staff',
            'title' => 'Staff',
            'description' => __( 'Check to list as staff member', 'themeworks' ),
            'type' => 'checkbox'
        ),
        'title' => array(
            'id' => 'title',
            'title' => 'Job Title',
            'description' => __( 'Your job title', 'themeworks' ),
            'type' => 'text'
        ),
        'twitter' => array(
            'id' => 'twitter',
            'title' => 'Twitter',
            'description' => __( 'Your Twitter url', 'themeworks' ),
            'type' => 'text'
        ),
        'facebook' => array(
            'id' => 'facebook',
            'title' => 'Facebook',
            'description' => __( 'Your Facebook url', 'themeworks' ),
            'type' => 'text'
        ),
        'googleplus' => array(
            'id' => 'googleplus',
            'title' => 'Google+',
            'description' => __( 'Your Google+ url', 'themeworks' ),
            'type' => 'text'
        ),
        'youtube' => array(
            'id' => 'youtube',
            'title' => 'YouTube',
            'description' => __( 'Your YouTube url', 'themeworks' ),
            'type' => 'text'
        ),
        'vimeo' => array(
            'id' => 'vimeo',
            'title' => 'Vimeo',
            'description' => __( 'Your vimeo url', 'themeworks' ),
            'type' => 'text'
        )
    );

    return $fields;

}

/**
 * Add custom meta fields on user profile page
 * @param  $user [object]
 * @return html
 */
function themeworks_add_custom_user_profile_fields( $user ) {

    if ( current_user_can( 'manage_options' ) ) {

        echo '<h3>' . __( 'Profile Information', 'themeworks' ) . '</h3>';
        echo '<table class="form-table">';

        $fields = themeworks_user_profile_fields();

        foreach ( $fields as $field ) {
            $checked = ( esc_attr( get_the_author_meta( $field['id'], $user->ID ) ) == '1' ) ? 'checked' : '';
            $value = ( esc_attr( get_the_author_meta( $field['id'], $user->ID ) ) );
            echo '<tr>';
            echo '<th><label for="' . $field['id'] . '">' . ucfirst( $field['title'] ) . '</label></th>';
            echo '<td>';
            if ( 'checkbox' == $field['type'] ) {
                echo '<input type="checkbox" name="' . $field['id'] . '" id="' . $field['id'] . '" value="1"' . $checked . ' /><br />';
            } else {
                echo '<input type="text" name="' . $field['id'] . '" id="' . $field['id'] . '" value="' . $value . '" class="regular-text" /><br />';
            }
            echo '<span class="description">' . $field['description'] . '</span>';
            echo '</td>';
            echo '</tr>';
        }
        echo '</table>';
    }
}
add_action( 'show_user_profile', 'themeworks_add_custom_user_profile_fields' );
add_action( 'edit_user_profile', 'themeworks_add_custom_user_profile_fields' );

/**
 * Save custom meta fields on user profile page
 * @param  $user_id [int]
 * @return null
 */
function themeworks_save_custom_user_profile_fields( $user_id ) {
    if ( ! current_user_can( 'edit_user', $user_id ) )
        return FALSE;

    $fields = themeworks_user_profile_fields();

    foreach ( $fields as $field ) {
        if ( 'checkbox' == $field['type'] ) {
            update_user_meta( $user_id, $field['id'], isset( $_POST[$field['id']] ) );
        } else {
            update_user_meta( $user_id, $field['id'], $_POST[$field['id']] );
        }
    }
}
add_action( 'personal_options_update', 'themeworks_save_custom_user_profile_fields' );
add_action( 'edit_user_profile_update', 'themeworks_save_custom_user_profile_fields' );

/**
 * Get the first video from the multiple videos
 */
function themeworks_video_grabber( $cache, $url, $attr, $post_ID ) {
    global $video_id;

    if ( '' != $url ){
        if ( $video_id != $post_ID && '' == get_post_meta( $post_ID, 'themeworks-video', true ) ) {
            update_post_meta( $post_ID, 'themeworks-video', $url );
            $video_id = $post_ID;
        } else {
            return $cache;
        }
    } else {
        delete_post_meta( $post_ID, 'themeworks-video' );
    }
}
add_filter( 'embed_oembed_html', 'themeworks_video_grabber', 10, 4 );

/**
 * Add Button Text, Button URL and Slide Alignment fields to media uploader
 *
 * @param $form_fields array, fields to include in attachment form
 * @param $post object, attachment record in database
 * @return $form_fields, modified form fields
 */
function themeworks_attachment_fields( $form_fields, $post ) {

    // Button text for slides
    $form_fields['themeworks-button-text'] = array(
        'label' => __( 'Button Text', 'themeworks' ),
        'input' => 'text',
        'value' => get_post_meta( $post->ID, 'themeworks_button_text', true ),
        'helps' => ''
    );

    // Button url for slides
    $form_fields['themeworks-button-url'] = array(
        'label' => __( 'Button URL', 'themeworks' ),
        'input' => 'text',
        'value' => get_post_meta( $post->ID, 'themeworks_button_url', true ),
        'helps' => ''
    );

    // Alignment for slide text
    // Set up options
    $options = array( 'left' => __( 'Left', 'themeworks' ), 'center' => __( 'Center', 'themeworks' ), 'right' => __( 'Right', 'themeworks' ) );

    // Build the form
    $html = "<select name='attachments[$post->ID][themeworks-alignment]' class='themeworks-alignment-option'>";

    // Display each option
    foreach ( $options as $value => $label ) {

        // Get currently selected value
        $selected = get_post_meta( $post->ID, 'themeworks_alignment', true );

        // If no selected value, default to 'No'
        if ( !isset( $selected ) )
            $selected = 'left';

        if ( $selected == $value )
            $selected = " selected='selected'";

        $html .= "<option value='{$value}'$selected>{$label}</option>";
    }

    $html .= '</select>';
    $out[] = $html;

    // Construct the form field
    $form_fields['themeworks-alignment'] = array(
        'label' => 'Slide Align',
        'input' => 'html',
        'html'  => join("\n", $out)
    );

    return $form_fields;
}
add_filter( 'attachment_fields_to_edit', 'themeworks_attachment_fields', 10, 2 );

/**
 * Save Button Text, Button URL and Slide Alignment values in media uploader
 *
 * @param $post array, the post data for database
 * @param $attachment array, attachment fields from $_POST form
 * @return $post array, modified post data
 */
function themeworks_attachment_fields_save( $post, $attachment ) {

    $fields = array( 'themeworks-button-text', 'themeworks-button-url', 'themeworks-alignment' );
    foreach ( $fields as $field ) {
        if ( isset( $attachment[$field] ) )
            update_post_meta( $post['ID'], str_replace( '-', '_', $field ), $attachment[$field] );
    }

    return $post;
}
add_filter( 'attachment_fields_to_save', 'themeworks_attachment_fields_save', 10, 2 );

/**
 * Hex to decimal conversion for custom header text color improvements
 */
function themeworks_hex2rgb( $colour ) {
    if ( $colour[0] == '#' ) {
        $colour = substr( $colour, 1 );
    }
    if ( strlen( $colour ) == 6 ) {
        list( $r, $g, $b ) = array( $colour[0] . $colour[1], $colour[2] . $colour[3], $colour[4] . $colour[5] );
    } elseif ( strlen( $colour ) == 3 ) {
        list( $r, $g, $b ) = array( $colour[0] . $colour[0], $colour[1] . $colour[1], $colour[2] . $colour[2] );
    } else {
        return false;
    }
    $r = hexdec( $r );
    $g = hexdec( $g );
    $b = hexdec( $b );
    return array( 'r' => $r, 'g' => $g, 'b' => $b );
}

/**
 * Adding the Open Graph in the Language Attributes
 */
function themeworks_add_opengraph_doctype( $output ) {
        return $output . ' xmlns:og="http://opengraphprotocol.org/schema/" xmlns:fb="http://www.facebook.com/2008/fbml"';
    }
add_filter( 'language_attributes', 'themeworks_add_opengraph_doctype' );

/**
 * Add Open Graph Meta Info
 */
function themeworks_insert_fb_meta() {
    global $post, $theme_options;
    $default_image = '';
    if ( !is_singular() ) //if it is not a post or a page
        return;

    echo '<meta property="og:title" content="' . get_the_title() . '"/>';
    echo '<meta property="og:type" content="article"/>';
    echo '<meta property="og:url" content="' . get_permalink() . '"/>';
    echo '<meta property="og:site_name" content="' . get_bloginfo( 'name' ) . '"/>';
    if ( ! has_post_thumbnail( $post->ID ) ) {
        if ( ! empty( $theme_options['logo'] ) ) {
            $default_image = $theme_options['logo'];
        } elseif ( get_header_image() ) {
            $default_image = get_header_image();
        }
        echo '<meta property="og:image" content="' . $default_image . '"/>';
    } else {
        $thumbnail_src = wp_get_attachment_image_src( get_post_thumbnail_id( $post->ID ), 'medium' );
        echo '<meta property="og:image" content="' . esc_attr( $thumbnail_src[0] ) . '"/>';
    }
}
add_action( 'wp_head', 'themeworks_insert_fb_meta', 5 );

/**
 * Custom Pagination for Page Templates
 */
function themeworks_custom_pagination( $numpages = '', $pagerange = '', $paged = '' ) {

    if ( empty( $pagerange ) ) {
        $pagerange = 2;
    }

    /**
    * This first part of our function is a fallback
    * for custom pagination inside a regular loop that
    * uses the global $paged and global $wp_query variables.
    *
    * It's good because we can now override default pagination
    * in our theme, and use this function in default quries
    * and custom queries.
    */
    global $paged;
    if ( empty( $paged ) ) {
        $paged = 1;
    }
    if ( $numpages == '' ) {
        global $wp_query;
        $numpages = $wp_query->max_num_pages;
        if( !$numpages ) {
            $numpages = 1;
        }
    }

    /**
    * We construct the pagination arguments to enter into our paginate_links
    * function.
    */
    $big = 999999999;
    $pagination_args = array(
        //'base'            => '%_%',
        'base'            => str_replace( $big, '%#%', esc_url( get_pagenum_link( $big ) ) ),
        'format'          => '?page=%#%',
        'total'           => $numpages,
        'current'         => $paged,
        'show_all'        => False,
        'end_size'        => 1,
        'mid_size'        => $pagerange,
        'prev_next'       => True,
        'prev_text'       => '&laquo;',
        'next_text'       => '&raquo;',
        'type'            => 'plain',
        'add_args'        => false,
        'add_fragment'    => ''
    );

    $paginate_links = paginate_links( $pagination_args );

    if ( $paginate_links ) {
        echo '<nav class="navigation paging-navigation" role="navigation"><div class="pagination">', "\n";
        echo '<span class="page-numbers page-num">Page ' . $paged . ' of ' . $numpages . '</span> ', "\n";
        echo $paginate_links, "\n";
        echo '</div></nav>', "\n";
    }

}

/**
 * Remove Shortcode from the_excerpt
 */
function themeworks_excerpt_no_shortcodes( $text = '' ) {
    $raw_excerpt = $text;
    if ( '' == $text ) {
        $text = get_the_content( '' );
        $text = do_shortcode( $text );
        $text = apply_filters( 'the_content', $text );
        $text = str_replace( ']]>', ']]>', $text );
        $excerpt_length = apply_filters( 'excerpt_length', 60 );
        $excerpt_more = apply_filters( 'excerpt_more', ' ' . '...' );
        $text = wp_trim_words( $text, $excerpt_length, $excerpt_more );
    }
    return apply_filters( 'wp_trim_excerpt', $text, $raw_excerpt );
}
remove_filter( 'get_the_excerpt', 'wp_trim_excerpt'  );
add_filter( 'get_the_excerpt', 'themeworks_excerpt_no_shortcodes'  );

/**
 * Allow shortcodes in widget text
 */
add_filter( 'widget_text', 'do_shortcode', 11 );