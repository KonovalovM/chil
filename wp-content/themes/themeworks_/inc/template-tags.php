<?php
/**
 * Custom template tags for this theme.
 *
 * Eventually, some of the functionality here could be replaced by core features.
 *
 * @package themeworks
 */

if ( ! function_exists( 'themeworks_get_theme_options' ) ) :
/**
 * Set the theme option variable for use throughout theme.
 */
function themeworks_get_theme_options() {
    $default_options = array(
        'color' => 'default',
        'slideshow_navigation'      => 'hide',
        'slideshow_autostart'       => 'auto',
        'slideshow_animation'       => 'fade',
        'slideshow_smooth_height'   => 'fluid',
        'accent_color'              => '#63A599',
        'dark_color'                => '#1F2F3F'
    );

    $theme_options = wp_parse_args(
        get_option( 'themeworks_options', array() ),
        $default_options
    );

    return apply_filters( 'themeworks_get_theme_options_filter', $theme_options );
}
endif;

if ( ! function_exists( 'themeworks_paging_nav' ) ) :
/**
 * Display navigation to next/previous set of posts when applicable.
 */
function themeworks_paging_nav() {
    global $wp_query;
    // Don't print empty markup if there's only one page.
    if ( $wp_query->max_num_pages < 2 ) {
        return;
    }
    ?>
    <nav class="navigation paging-navigation" role="navigation">
        <h1 class="screen-reader-text"><?php _e( 'Posts navigation', 'themeworks' ); ?></h1>
        <div class="pagination">
            <?php
            global $wp_query;
            $big = 999999999;
            echo paginate_links( array(
                'base' => str_replace( $big, '%#%', esc_url( get_pagenum_link( $big ) ) ),
                'format' => '?paged=%#%',
                'current' => max( 1, get_query_var('paged') ),
                'total' => $wp_query->max_num_pages
            ) );
            ?>
        </div><!-- .pagination -->
    </nav><!-- .navigation -->
    <?php
}
endif;

if ( ! function_exists( 'themeworks_post_nav' ) ) :
/**
 * Display navigation to next/previous post when applicable.
 */
function themeworks_post_nav() {
    // Don't print empty markup if there's nowhere to navigate.
    $previous = ( is_attachment() ) ? get_post( get_post()->post_parent ) : get_adjacent_post( false, '', true );
    $next     = get_adjacent_post( false, '', false );

    if ( ! $next && ! $previous ) {
        return;
    }
    ?>
    <nav class="navigation post-navigation" role="navigation">
        <h1 class="screen-reader-text"><?php _e( 'Post navigation', 'themeworks' ); ?></h1>
        <div class="nav-links">
            <?php
                previous_post_link( '<div class="nav-previous">%link</div>', _x( '<span class="meta-nav">&larr;</span> %title', 'Previous post link', 'themeworks' ) );
                next_post_link(     '<div class="nav-next">%link</div>',     _x( '%title <span class="meta-nav">&rarr;</span>', 'Next post link',     'themeworks' ) );
            ?>
        </div><!-- .nav-links -->
    </nav><!-- .navigation -->
    <?php
}
endif;

if ( ! function_exists( 'themeworks_posted_on' ) ) :
/**
 * Prints HTML with meta information for the current post-date/time and author.
 */
function themeworks_posted_on() {
    $time_string = '<time class="entry-date published" datetime="%1$s">%2$s</time>';
    if ( get_the_time( 'U' ) !== get_the_modified_time( 'U' ) ) {
        $time_string .= '<time class="updated" datetime="%3$s">%4$s</time>';
    }

    $time_string = sprintf( $time_string,
        esc_attr( get_the_date( 'c' ) ),
        esc_html( get_the_date() ),
        esc_attr( get_the_modified_date( 'c' ) ),
        esc_html( get_the_modified_date() )
    );

    printf( __( '<span class="posted-on">%1$s</span> / <span class="byline">%2$s</span>', 'themeworks' ),
        sprintf( '<a href="%1$s" rel="bookmark">%2$s</a>',
            esc_url( get_permalink() ),
            $time_string
        ),
        sprintf( '<span class="author vcard"><a class="url fn n" href="%1$s">%2$s</a></span>',
            esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ),
            esc_html( get_the_author() )
        )
    );
}
endif;

/**
 * Returns true if a blog has more than 1 category.
 *
 * @return bool
 */
function themeworks_categorized_blog() {
    if ( false === ( $all_the_cool_cats = get_transient( 'themeworks_categories' ) ) ) {
        // Create an array of all the categories that are attached to posts.
        $all_the_cool_cats = get_categories( array(
            'fields'     => 'ids',
            'hide_empty' => 1,

            // We only need to know if there is more than one category.
            'number'     => 2,
        ) );

        // Count the number of categories that are attached to the posts.
        $all_the_cool_cats = count( $all_the_cool_cats );

        set_transient( 'themeworks_categories', $all_the_cool_cats );
    }

    if ( $all_the_cool_cats > 1 ) {
        // This blog has more than 1 category so themeworks_categorized_blog should return true.
        return true;
    } else {
        // This blog has only 1 category so themeworks_categorized_blog should return false.
        return false;
    }
}

/**
 * Flush out the transients used in themeworks_categorized_blog.
 */
function themeworks_category_transient_flusher() {
    // Like, beat it. Dig?
    delete_transient( 'themeworks_categories' );
}
add_action( 'edit_category', 'themeworks_category_transient_flusher' );
add_action( 'save_post', 'themeworks_category_transient_flusher' );

/**
 * Callback function in case menu position is not set
 */
function themeworks_menu_fallback() {
    return false;
}

/**
 * Counts the number of widgets in a specific sidebar
 *
 * @param   string  $id
 * @return  integer number of widgets in the sidebar
 */
function themeworks_count_widgets( $id ) {
    $count = 0;
    $sidebars_widgets = wp_get_sidebars_widgets();
    $count = ( int ) count( ( array ) $sidebars_widgets[ $id ] );
    return $count;
}

/**
 * Widget column class helper
 *
 * @param   string  $id
 * @return  string  grid class
 */
function themeworks_widget_column_class( $id, $columns ) {

    $count = themeworks_count_widgets( $id );
    $class = 'col ';
    if ( 'sidebar' != $id ) {
        if ( $columns == 4 ) {
            if ( $count >= $columns ) {
                $class .= 'four-col';
            } elseif ( $count == 3 ) {
                $class .= 'three-col';
            } elseif ( $count == 2 ) {
                $class .= 'two-col';
            } else {
                $class .= 'one-col';
            }
        } elseif ( $columns == 3 ) {
            if ( $count >= $columns ) {
                $class .= 'three-col';
            } elseif ( $count == 2 ) {
                $class .= 'two-col';
            } else {
                $class .= 'one-col';
            }
        } elseif ( $columns == 2 ) {
            if ( $count >= $columns ) {
                $class .= 'two-col';
            } else {
                $class .= 'one-col';
            }
        } else {
            $class .= 'one-col';
        }
    } else {
        $class = '';
    }

    return $class;
}

/**
 * Dynamic sidebar classes
 * @param   $params
 * @return  $params
 */
function themeworks_dynamic_sidebar_params( $params ) {

    $id         = $params[0]['id'];
    $section    = ( 'footer' != $id ) ? 'widget' : 'footer';
    $columns    = themeworks_get_active_section( $section );
    if ( $columns == 6 ) {
        $columns = 2;
    } elseif ( $columns == 7 ) {
        $columns = 3;
    } elseif ( $columns == 8 ) {
        $columns = 4;
    }
    $class      = themeworks_widget_column_class( $id, $columns );

    $params[0]['before_widget'] = preg_replace( '/class="/', 'class="' . $class . ' ', $params[0]['before_widget'], 1 );

    return $params;
}
add_filter( 'dynamic_sidebar_params', 'themeworks_dynamic_sidebar_params' );


/**
 * Slideshows
 * Generates the markup for all slideshows powered by theme options
 *
 * @param   $option The name of the theme option to get containing slideshow image ids
 * @param   $show_thumbnails (boolean)
 * @return  html
 */
function themeworks_slideshow( $option ){

    global $theme_options;
    if ( isset ( $theme_options[$option] ) && '' <> $theme_options[$option] ) :
        // Get Slides
        $slides = $theme_options[$option];
        $images = explode( ',', $slides );
        ?>

        <div class="section-content">
            <div class="flexslider loading">
                <ul class="slides">

                    <?php
                    foreach( $images as $id ) :

                        $attachment_caption = get_post_field('post_excerpt', $id);
                        $attachment_title = get_post_field('post_title', $id);
                        $attachment_button_text = get_post_meta( $id, 'themeworks_button_text', true );
                        $attachment_button_url = get_post_meta( $id, 'themeworks_button_url', true );
                        $attachment_alignment = get_post_meta( $id, 'themeworks_alignment', true );

                        $classes = ( $attachment_alignment ) ? 'slide-' . $attachment_alignment : 'slide-left';
                        $classes .= ( empty( $attachment_title ) ) ? ' slide-no-title' : '';

                        $button_text = ( $attachment_button_text ) ? $attachment_button_text : __( 'More info', 'themeworks' );

                        ?>
                        <li>
                            <div class="slide <?php echo $classes; ?>">
                                <?php echo wp_get_attachment_image( $id, "xl", 0 ); ?>
                                <div class="slideshow-caption">
                                    <div class="container">
                                        <?php
                                        if ( ! empty ( $attachment_title ) )
                                            echo '<h2 class="slide-title">' . $attachment_title . '</h2>';
                                        if ( ! empty ( $attachment_caption ) )
                                            echo '<p class="slide-caption">' . $attachment_caption . '</p>';
                                        if ( ! empty ( $attachment_button_url ) )
                                            echo '<a href="' . $attachment_button_url . '" title="' . $attachment_title . '" class="button slide-link">' . $button_text . '</a>';
                                        ?>
                                    </div>
                                </div><!-- slideshow-caption -->
                            </div><!--  .slide -->
                        </li>
                    <?php endforeach; ?>
                </ul><!-- .slides -->
                <?php if ( isset ( $theme_options['slideshow_navigation'] ) && $theme_options['slideshow_navigation'] == 'show' ) : ?>
                    <ul class="slide-thumbs">
                        <?php foreach( $images as $id ) : ?>
                            <?php $image = wp_get_attachment_image_src( $id, 'thumbnail' ); ?>
                                <li>
                                    <img src="<?php echo $image[0]; ?>" />
                                </li>
                        <?php endforeach; ?>
                    </ul><!-- .slide-thumbs -->
                <?php endif; ?>
            </div><!-- .flexslider -->
            </div><!-- .section-content -->
    <?php endif; // end slideshow check
}

/**
 * Page and Post Titles
 * Used to show post and page titles in the header if a header image is set
 *
 * @param  $post_id (int)
 * @return  $title (string)
 */
function themeworks_post_title(){

    if ( is_singular() ) :
        $title = get_the_title();

    elseif ( is_category() ) :
        $title = single_cat_title();

    elseif ( is_tag() ) :
        $title = single_tag_title();

    elseif ( is_author() ) :
        $title = sprintf( __( 'Author: %s', 'themeworks' ), '<span class="vcard">' . get_the_author() . '</span>' );

    elseif ( is_day() ) :
        $title = sprintf( __( 'Day: %s', 'themeworks' ), '<span>' . get_the_date() . '</span>' );

    elseif ( is_month() ) :
        $title = sprintf( __( 'Month: %s', 'themeworks' ), '<span>' . get_the_date( _x( 'F Y', 'monthly archives date format', 'themeworks' ) ) . '</span>' );

    elseif ( is_year() ) :
        $title = sprintf( __( 'Year: %s', 'themeworks' ), '<span>' . get_the_date( _x( 'Y', 'yearly archives date format', 'themeworks' ) ) . '</span>' );

    elseif ( is_tax( 'post_format', 'post-format-aside' ) ) :
        $title = _e( 'Asides', 'themeworks' );

    elseif ( is_tax( 'post_format', 'post-format-gallery' ) ) :
        $title = _e( 'Galleries', 'themeworks');

    elseif ( is_tax( 'post_format', 'post-format-image' ) ) :
        $title = _e( 'Images', 'themeworks');

    elseif ( is_tax( 'post_format', 'post-format-video' ) ) :
        $title = _e( 'Videos', 'themeworks' );

    elseif ( is_tax( 'post_format', 'post-format-quote' ) ) :
        $title = _e( 'Quotes', 'themeworks' );

    elseif ( is_tax( 'post_format', 'post-format-link' ) ) :
        $title = _e( 'Links', 'themeworks' );

    elseif ( is_tax( 'post_format', 'post-format-status' ) ) :
        $title = _e( 'Statuses', 'themeworks' );

    elseif ( is_tax( 'post_format', 'post-format-audio' ) ) :
        $title = _e( 'Audios', 'themeworks' );

    elseif ( is_tax( 'post_format', 'post-format-chat' ) ) :
        $title = _e( 'Chats', 'themeworks' );

    else :
        $title = _e( 'Archives', 'themeworks' );

    endif;

    return $title;
}

/**
 * Social media icons
 */
function themeworks_social( $location ) {

    global $theme_options;

    if ( ! empty( $theme_options['social'] ) && ( $location == $theme_options['social'] || 'all' == $theme_options['social'] ) ) {

        $fields = array( 'twitter', 'facebook', 'googleplus', 'youtube', 'vimeo', 'instagram', 'linkedin', 'pinterest' );

        // Filter so users can add additional links
        // See: https://gist.github.com/thadallender/e6d61401688327f97056
        if ( has_filter( 'themeworks_filter_social' ) ) {
            $fields = apply_filters( 'themeworks_filter_social', $fields );
        }

        echo  '<ul id="social-' . $location . '" class="social">';

        foreach( $fields as $field ) {
            if ( ! empty( $theme_options[$field] ) )
                echo '<li><a href="' . $theme_options[$field] . '" class="social-' . $field . '" title="' . $field . '"><span class="genericon genericon-' . $field . '"></span></a></li>';
        }

        echo '</ul>';

    }
}

/**
 * Dynamically derive the registered sidebar id for use in widget templates.
 * We simply loop over the registered sidebars and count those prefixed with "themeworks"
 * Then, we match the id of the sidebar to the static $count variable
 * If the static $count variable equals the registered widget id, we have a match.
 * So, we return the $sidebar['id']
 *
 * @return  $sidebar['id']
 */
function themeworks_registered_sidebar_id() {
    // get registered sidebar ids
    global $wp_registered_sidebars;
    // count uses of this function to help determine the sidebar id to return
    static $count = 0;
    $count++;
    $id = null;

    // loop over the sidebars, skipping the sidebar and footer
    foreach ( $wp_registered_sidebars as $sidebar ) {
        // only check widgets starting with "themeworks"
        // we register these widgets dynamically based on users selection during the build process
        $prefix = explode( '-', $sidebar['id'] );
        if ( 'themeworks' == $prefix[0] ) {
            $id = explode( '-', $sidebar['id'] );
        }

        if ( $id[2] == $count )
            return $sidebar['id'];
    }
}

/**
 * Get the section template part if it exists
 */
if ( ! function_exists( 'themeworks_get_section' ) ) {

    function themeworks_get_section( $section ) {

        if ( file_exists( get_template_directory() . '/sections/' . $section . '/' . $section . '-' . themeworks_get_active_section( $section ) . '.php' ) ) {
            $file = 'sections/' . $section . '/' . $section . '-' . themeworks_get_active_section( $section ) . '.php';
        } else {
            $file = 'section-' . $section . '-' . themeworks_get_active_section( $section ) . '.php';
        }

        if ( '' != locate_template( $file ) ) {
            return $file;
        } else {
            return false;
        }
    }
}

/**
 * Get page sections and show them
 */
function themeworks_get_page_sections( $post_id ) {

    $saved_sections = esc_attr( get_post_meta( $post_id, '_tw_sections', true ) );

    if ( ! empty ( $saved_sections )  ) {
        $sections = explode( ',', $saved_sections );

        foreach ( $sections as $section ) {
            $id = explode( '-', $section );
            if ( locate_template( 'section-' . $section . '.php' ) != '') {
                // yep, load the template from active theme root
                get_template_part( 'section', $section );
            } else {
                // nope, load from subdir
                get_template_part( 'sections/' . $id[0] . '/' . $id[0], $id[1] );
            }
        }
    }
}
/**
 * An array of section ids
 */
if ( ! function_exists( "themeworks_get_active_section" ) ) {
	function themeworks_get_active_section( $id ){
		$sections = array(
			"header" => "3",
			"service" => "5",
			"footer" => "3",
		);
		if ( isset ( $id ) && ! empty ( $id ) ) {
			if ( ! empty ( $sections[$id] ) )
				return $sections[$id];
		} else {
			return $sections;
		}
	}
}
