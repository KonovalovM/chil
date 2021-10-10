<?php
/**
 * themeworks functions and definitions
 *
 * @package themeworks
 */

/**
 * Dynamically load files in a given directory
 */
function themeworks_autoload( $directory ){
    $files = glob( $directory, GLOB_BRACE );
    foreach( $files as $file ) {
        if ( file_exists( $file ) ) {
            require $file;
        }
    }
}

/**
 * Load includes
 */
themeworks_autoload( get_template_directory() . '/inc/*.php', 'back-compat.php' );

/**
 * Load theme options
 */
themeworks_autoload( get_template_directory() . '/inc/admin/*.php' );

/**
 * Set the content width based on the theme's design and stylesheet.
 */
if ( ! isset( $content_width ) ) {
    $content_width = 1200; /* pixels */
}

/**
 * Set the theme option variable for use throughout theme.
 */
global $theme_options;
$theme_options = themeworks_get_theme_options();

if ( ! function_exists( 'themeworks_setup' ) ) :
/**
 * Sets up theme defaults and registers support for various WordPress features.
 *
 * Note that this function is hooked into the after_setup_theme hook, which
 * runs before the init hook. The init hook is too late for some features, such
 * as indicating support for post thumbnails.
 */
function themeworks_setup() {

    /*
     * Make theme available for translation.
     * Translations can be filed in the /languages/ directory.
     * If you're building a theme based on themeworks, use a find and replace
     * to change 'themeworks' to the name of your theme in all the template files
     */
    load_theme_textdomain( 'themeworks', get_template_directory() . '/languages' );

    // Add default posts and comments RSS feed links to head.
    add_theme_support( 'automatic-feed-links' );

    /*
     * Enable support for Post Thumbnails on posts and pages.
     *
     * @link http://codex.wordpress.org/Function_Reference/add_theme_support#Post_Thumbnails
     */
    add_theme_support( 'post-thumbnails' );
    set_post_thumbnail_size( 150, 150, true );
    add_image_size( 'square', 480, 480, true );
    add_image_size( 'square-large', 640, 640, true );
    add_image_size( 'wide', 1000, 400, true );
    add_image_size( 'xl', 1600, 0, false );
    add_image_size( 'header-image', 1200, 550, true );

    // This theme uses wp_nav_menu() in one location.
    register_nav_menus( array(
        'primary' => __( 'Primary Menu', 'themeworks' ),
        'secondary' => __( 'Secondary Menu', 'themeworks' )
    ) );

    // Enable support for Post Formats.
    add_theme_support( 'post-formats', array( 'aside', 'audio', 'image', 'gallery', 'video', 'quote', 'link' ) );

    // Setup the WordPress core custom background feature.
    add_theme_support( 'custom-background', apply_filters( 'themeworks_custom_background_args', array(
        'default-color' => 'ffffff',
        'default-image' => '',
    ) ) );

    // Enable support for HTML5 markup.
    add_theme_support( 'html5', array(
        'comment-list',
        'search-form',
        'comment-form',
        'gallery',
        'caption',
    ) );

    /**
     * Enable support for Sell Media
     */
    if ( class_exists( 'SellMedia' ) ) {
        add_theme_support( 'sell_media' );
    }

    // Add editor style
    //add_editor_style( 'editor-style.css' );

}
endif; // themeworks_setup
add_action( 'after_setup_theme', 'themeworks_setup' );

/**
 * Update media setting options to those required by the theme
 */
function tw_after_theme_switch() {
    if ( get_option( 'thumbnail_size_w' ) != 150 ) update_option( 'thumbnail_size_w', 150 );
    if ( get_option( 'thumbnail_size_h' ) != 150 ) update_option( 'thumbnail_size_h', 150 );
    if ( get_option( 'medium_size_w') != 800 ) update_option( 'medium_size_w', 800 );
    if ( get_option( 'medium_size_h' )!= 0 ) update_option( 'medium_size_h', 0 );
    if ( get_option( 'large_size_w' ) != 1200 ) update_option( 'large_size_w', 1200 );
    if ( get_option( 'large_size_h' ) != 0 ) update_option( 'large_size_h', 0 );
}
add_action( 'after_switch_theme', 'tw_after_theme_switch', 10, 2 );

/**
 * Register widget area.
 *
 * @link http://codex.wordpress.org/Function_Reference/register_sidebar
 */
function themeworks_widgets_init() {

    $widgets = apply_filters( 'themeworks_widgets', array( 'Sidebar', 'Footer' ) );
    if ( $widgets ) foreach ( $widgets as $widget ) :

        register_sidebar( array(
            'name'          => __( $widget, 'themeworks' ),
            'id'            => esc_attr( strtolower( str_replace( ' ', '-', $widget ) ) ),
            'description'   => '',
            'before_widget' => '<aside id="%1$s" class="widget %2$s">',
            'after_widget'  => '</aside>',
            'before_title'  => '<h2 class="widget-title">',
            'after_title'   => '</h2>',
        ) );

    endforeach;
}
add_action( 'widgets_init', 'themeworks_widgets_init' );

/**
 * Enqueue scripts and styles.
 */
function themeworks_scripts() {

    global $theme_options;

    if ( is_rtl() )
        wp_enqueue_style( 'themeworks-rtl', get_template_directory_uri() . '/css/rtl.css' );

    // Load our main stylesheet.
    wp_enqueue_style( 'themeworks-style', get_stylesheet_uri() );

    // Load our main js.
    wp_enqueue_script( 'themeworks_theme', get_template_directory_uri() . '/js/theme.min.js', array( 'jquery' ) );

    // comments
    if ( is_singular() && comments_open() && get_option( 'thread_comments' ) )
        wp_enqueue_script( 'comment-reply' );

    // locaize js for front end scripts
    wp_localize_script( 'themeworks_theme', 'themeworks_theme', array(
        'ajaxurl'                   => admin_url( 'admin-ajax.php' ),
        'base_url'                  => get_site_url( 1 ),
        'site_url'                  => site_url(),
        'slideshow_navigation'      => $theme_options['slideshow_navigation'],
        'slideshow_animation'       => $theme_options['slideshow_animation'],
        'slideshow_smooth_height'   => $theme_options['slideshow_smooth_height'],
        'slideshow_autostart'       => $theme_options['slideshow_autostart']
    ) );

    // localize js for admin scripts
    wp_localize_script( 'themeworks_customizer', 'themeworks_customizer', array(
        'theme'                     => themeworks_get_current_theme_id(),
        'template_directory_uri'    => get_template_directory_uri()
    ) );
}
add_action( 'wp_enqueue_scripts', 'themeworks_scripts' );

/**
 * Get theme options.
 */
function themeworks_header_options() {
    global $theme_options;
    $theme_options = themeworks_get_theme_options();

    if ( ! empty( $theme_options['favicon'] ) ) :
        echo '<link rel="shortcut icon" href="' . esc_url( $theme_options['favicon'] ) . '" />';
    endif;
}
add_action( 'wp_head', 'themeworks_header_options' );
/**
 * Filter register sidebars for home widgets
 */
function themeworks_widgets_filter(){
	$widgets = array( "Sidebar", "Footer" );
	return $widgets;
}
add_filter( "themeworks_widgets", "themeworks_widgets_filter" );
