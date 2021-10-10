<?php
/**
 * Sample implementation of the Custom Header feature
 * http://codex.wordpress.org/Custom_Headers
 *
 * You can add an optional custom header image to header.php like so ...

    <?php if ( get_header_image() ) : ?>
    <a href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home">
        <img src="<?php header_image(); ?>" width="<?php echo get_custom_header()->width; ?>" height="<?php echo get_custom_header()->height; ?>" alt="">
    </a>
    <?php endif; // End header image check. ?>

 *
 * @package themeworks
 */

register_default_headers( array(
    'mountains' => array(
        'url'           => get_template_directory_uri() . '/images/headers/mountains.jpg',
        'thumbnail_url' => get_template_directory_uri() . '/images/headers/mountains-thumbnail.jpg',
        'description'   => __( 'Mountains', 'themeworks' )
    ),
    'buildings' => array(
        'url'           => get_template_directory_uri() . '/images/headers/buildings.jpg',
        'thumbnail_url' => get_template_directory_uri() . '/images/headers/buildings-thumbnail.jpg',
        'description'   => __( 'Buildings', 'themeworks' )
    ),
    'trees' => array(
        'url'           => get_template_directory_uri() . '/images/headers/trees.jpg',
        'thumbnail_url' => get_template_directory_uri() . '/images/headers/trees-thumbnail.jpg',
        'description'   => __( 'Trees', 'themeworks' )
    ),
    'tea' => array(
        'url'           => get_template_directory_uri() . '/images/headers/tea.jpg',
        'thumbnail_url' => get_template_directory_uri() . '/images/headers/tea-thumbnail.jpg',
        'description'   => __( 'Tea', 'themeworks' )
    ),
    'water' => array(
        'url'           => get_template_directory_uri() . '/images/headers/water.jpg',
        'thumbnail_url' => get_template_directory_uri() . '/images/headers/water-thumbnail.jpg',
        'description'   => __( 'Water', 'themeworks' )
    ),
    'digital' => array(
        'url'           => get_template_directory_uri() . '/images/headers/digital.jpg',
        'thumbnail_url' => get_template_directory_uri() . '/images/headers/digital-thumbnail.jpg',
        'description'   => __( 'Digital', 'themeworks' )
    ),
    'sky' => array(
        'url'           => get_template_directory_uri() . '/images/headers/sky.jpg',
        'thumbnail_url' => get_template_directory_uri() . '/images/headers/sky-thumbnail.jpg',
        'description'   => __( 'Sky', 'themeworks' )
    )
) );

/**
 * Setup the WordPress core custom header feature.
 *
 * @uses themeworks_header_style()
 * @uses themeworks_admin_header_style()
 * @uses themeworks_admin_header_image()
 */
function themeworks_custom_header_setup() {
    add_theme_support( 'custom-header', apply_filters( 'themeworks_custom_header_args', array(
        'default-image'          => get_template_directory_uri() . '/images/headers/mountains.jpg',
        'default-text-color'     => 'ffffff',
        'header-text'            => true,
        'width'                  => 1200,
        'height'                 => 600,
        'flex-height'            => true,
        'flex-width'             => true,
        'wp-head-callback'       => 'themeworks_header_style',
        'admin-head-callback'    => 'themeworks_admin_header_style',
        'admin-preview-callback' => 'themeworks_admin_header_image'
    ) ) );
}
add_action( 'after_setup_theme', 'themeworks_custom_header_setup' );


if ( ! function_exists( 'themeworks_header_style' ) ) :
/**
 * Styles the header image and text displayed on the blog
 *
 * @see themeworks_custom_header_setup().
 */
function themeworks_header_style() {
    if ( get_post() ) {
        $themeworks_saved_header_image_id = esc_attr( get_post_meta( get_the_ID(), '_themeworks-header-image', true ) );
        $themeworks_saved_header_image_array = wp_get_attachment_image_src( $themeworks_saved_header_image_id, 'header-image' );
        if ( ! empty( $themeworks_saved_header_image_id ) && empty( $themeworks_saved_header_image_array ) ) {
            $themeworks_saved_header_image_array = wp_get_attachment_image_src( $themeworks_saved_header_image_id );
        }
        $themeworks_saved_header_image = $themeworks_saved_header_image_array[0];
        if ( ! empty( $themeworks_saved_header_image ) && is_singular() && ! is_attachment() ) {
            $custom_header_url = $themeworks_saved_header_image;
            $custom_header_height = '600px';
        } else {
            $custom_header_url = get_header_image();
            $custom_header_height = get_custom_header()->height . 'px';
        }
    } else {
        $custom_header_url = get_header_image();
        $custom_header_height = get_custom_header()->height;
    }
    if ( empty( $themeworks_saved_header_image ) && ! is_front_page() ) {
        $custom_header_height = 'auto';
    }
    if ( get_header_image() || ! empty( $themeworks_saved_header_image ) ) : ?>
    <style type="text/css">
        #masthead {
            background-image: url( <?php echo $custom_header_url; ?> );
            background-repeat: no-repeat;
            background-position: center;
            background-size: cover;
            height: <?php echo $custom_header_height; ?>;
        }
        .site-title a,
        .site-description,
        .main-navigation ul li a,
        h2.entry-title,
        .site-welcome,
        .site-action a,
        .site-header .social a,
        .site-header .site-action .button-border {
            color: #FFFFFF;
        }
        .site-header .site-action .button-border {
            border-color: #FFFFFF;
        }

    </style>
    <?php endif;

    if ( get_post() ) {
        $themeworks_saved_text_color = esc_attr( get_post_meta( get_the_ID(), '_themeworks-text-color', true ) );
        if ( ! empty( $themeworks_saved_text_color ) && is_singular() ) {
            $header_text_color = $themeworks_saved_text_color;
        } else {
            $header_text_color = get_header_textcolor();
        }
    } else {
        $header_text_color = get_header_textcolor();
    }

    // If no custom options for text are set, let's bail
    // get_header_textcolor() options: HEADER_TEXTCOLOR is default, hide text (returns 'blank') or any hex value
    if ( HEADER_TEXTCOLOR == $header_text_color ) {
        return;
    }

    // If we get this far, we have custom styles. Let's do this.
}
endif; // themeworks_header_style

if ( ! function_exists( 'themeworks_admin_header_style' ) ) :
/**
 * Styles the header image displayed on the Appearance > Header admin panel.
 *
 * @see themeworks_custom_header_setup().
 */
function themeworks_admin_header_style() {
    $header_text_color = ( get_header_textcolor() ) ? get_header_textcolor() : HEADER_TEXTCOLOR;
?>
    <style type="text/css">
        .appearance_page_custom-header #headimg {
            border: none;
        }
        #headimg {
            position: relative;
        }
        #headimg h1,
        #desc {
            position: absolute;
            left: 50px;
        }
        #headimg h1 {
            top: 30px;
        }
        #desc {
            top: 100px;
        }
        #headimg h1,
        #headimg h1 a,
        #desc {
            color: #<?php echo $header_text_color; ?>;
            text-decoration: none;
        }
        #headimg img {
        }
    </style>
<?php
}
endif; // themeworks_admin_header_style

if ( ! function_exists( 'themeworks_admin_header_image' ) ) :
/**
 * Custom header image markup displayed on the Appearance > Header admin panel.
 *
 * @see themeworks_custom_header_setup().
 */
function themeworks_admin_header_image() {
    $style = sprintf( ' style="color:#%s;"', get_header_textcolor() );
?>
    <div id="headimg">
        <h1 class="displaying-header-text"><a id="name"<?php echo $style; ?> onclick="return false;" href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php bloginfo( 'name' ); ?></a></h1>
        <div class="displaying-header-text" id="desc"<?php echo $style; ?>><?php bloginfo( 'description' ); ?></div>
        <?php if ( get_header_image() ) : ?>
        <img src="<?php header_image(); ?>" alt="">
        <?php endif; ?>
    </div>
<?php
}
endif; // themeworks_admin_header_image
