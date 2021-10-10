<?php
/**
 * If you would like to overwrite the css of the theme you will need to uncomment this action
 */

add_action('wp_enqueue_scripts', 'load_child_theme_styles', 999);

function load_child_theme_styles(){

    // If your css changes are minimal we recommend you to put them in the main style.css.
    // In this case uncomment bellow


    wp_enqueue_style( 'font-awesome-4', get_stylesheet_directory_uri() . '/font-awesome/css/font-awesome.min.css' );
    wp_enqueue_style( 'bxslider-style', get_stylesheet_directory_uri() . '/plugins/jquery.bxslider/jquery.bxslider.css' );
    wp_enqueue_style( 'child-theme-style', get_stylesheet_directory_uri() . '/style.css' );

    // If you want to create your own file.css you will need to load it like this (Don't forget to uncomment bellow) :
    //** wp_enqueue_style( 'custom-child-theme-style', get_stylesheet_directory_uri() . '/file.css' );
    wp_enqueue_script( 'bxslider-js', get_stylesheet_directory_uri() . '/plugins/jquery.bxslider/jquery.bxslider.min.js' );
}

/*
 * Load the translations from the child theme if present
 */
add_action( 'before_wpgrade_core', 'rosa_child_theme_setup' );
function rosa_child_theme_setup() {
	load_child_theme_textdomain( 'rosa_txtd', get_stylesheet_directory() . '/languages' );
}

add_action('admin_head', 'custom_admin_style');

function custom_admin_style() {
  echo '<style>
    .cmb-td{ position:relative; }
    .wp-picker-container{ right:auto !important;left:50px; }
  </style>';
}

require_once 'inc/shortcodes.php';
require_once 'inc/meta-boxes.php';
require_once 'restaurantPostType.php';