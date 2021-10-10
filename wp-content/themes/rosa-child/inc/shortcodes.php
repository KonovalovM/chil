<?php

function cta_btn_func( $atts, $content = null ) {
    $a = shortcode_atts( array(
        'color' => 'orange_btn',
        'link' => 'javascript:void();',
        'target' => '',
    ), $atts );
    if($a['color'] == 'black'){
    	$a['color'] = 'black_btn';
    }

    return '<a href="'.$a['link'].'" class="cta_btn '.$a['color'].'" target="'.$a['target'].'">'.$content.'</a>';
}
add_shortcode( 'cta_btn', 'cta_btn_func' );

function cta_link_func( $atts, $content = null ) {
    $a = shortcode_atts( array(
        'color' => '',
        'link' => 'javascript:void();',
        'target' => '',
    ), $atts );

    return '<a href="'.$a['link'].'" class="cta_link" target="'.$a['target'].'">'.$content.'</a>';
}
add_shortcode( 'cta_link', 'cta_link_func' );

function footer_hr_func( $atts ) {
    $a = shortcode_atts( array(
        'type' => '',
    ), $atts );

    return '<div class="footer_hr"></div>';
}
add_shortcode( 'footer_hr', 'footer_hr_func' );

function chil_spacer_func( $atts ) {
    $a = shortcode_atts( array(
        'size' => '',
    ), $atts );

    return '<div style="margin-bottom:'.$a['size'].'px;"></div>';
}
add_shortcode( 'chil_spacer', 'chil_spacer_func' );

function chil_container_func( $atts, $content = null ) {
    $a = shortcode_atts( array(
        'type' => '',
    ), $atts );

    return '<div class="container chil_container">'.do_shortcode($content).'</div>';
}
add_shortcode( 'chil_container', 'chil_container_func' );

function home_chil_menu_location_func( $atts, $content = null ) {
    $a = shortcode_atts( array(
    	'title' => 'Signature<br/>Menu',
        'image' => '',
    ), $atts );

    $result = '<div class="chil_menu_location">';
    $result .= 	'<div class="home_chil_menu_title" style="background-image:url(\''.$a['image'].'\')">'.$a['title'].'</div>';
    $result .=	'<div class="chil_menu_location_content">'.wpautop(do_shortcode($content)).'</div>';
    $result .= '</div>';

    return $result;
}
add_shortcode( 'home_chil_menu_location', 'home_chil_menu_location_func' );

function chil_press_func( $atts, $content = null ) {
    $a = shortcode_atts( array(
    	'qty' => '3',
    ), $atts );
    $args = array(
		'posts_per_page'=>$a['qty'],
		'post_type'=>'press_article'
	);
	
	$press = new WP_Query( $args );

    $result = '<div class="press_article_container">';
    while($press->have_posts()){
    	$press->the_post();
    	//$permalink = get_permalink(get_the_ID());
        $content = get_the_content();
        $word_count = str_word_count($content);
        if($word_count > 30){
            $the_content = '';
            $n = 0;
            $words = explode(" ", $content);
            while($n < 25){
                $the_content .= ' '.$words[$n];
                $n++;
            }

            $the_content .= '[...]';
        } else {
            $the_content = $content;
        }
        $permalink = get_post_meta(get_the_ID(), '_chil_press_external_link', true);
    	$result .= '<div class="press_article_wrapper">';
    	$result .= ' <div class="press_featured_image">'.get_the_post_thumbnail().'</div>';
        $result .= ' <div class="press_title"><h4 class="chil_orange">'.get_the_title().'</h4></div>';
    	//$result .= ' <div class="press_excerpt"><p>'.wpautop(do_shortcode(get_the_content())).'</p></div>';
        $result .= ' <div class="press_excerpt"><p>'.$the_content.'</p></div>';
        if(!empty($permalink)){
        	$result .= ' <div class="press_read_more"><p><a href="'.$permalink.'" class="cta_link chil_black" target="_blank">Read More</a></p></div>';
        }
    	$result .= '</div>';
    }

    $result .= '</div>';

    //$result .= '<div class="read_all_press"><a href="#" class="cta_btn orange_btn">View All Press</a></div>';

    return $result;
    wp_reset_postdata();
	wp_reset_query();
}
add_shortcode( 'chil_press', 'chil_press_func' );

function chil_separator_func( $atts, $content = null ) {
    $a = shortcode_atts( array(
        'color' => 'light_blue_separator',
    ), $atts );

    return '<div class="chil_separator '.$a['color'].'"></div>';
}
add_shortcode( 'chil_separator', 'chil_separator_func' );

function chil_background_func( $atts, $content = null ) {
    $a = shortcode_atts( array(
        'image' => '',
        'parallax' => 'true',
    ), $atts );
    $parallax = 'chil_parallax';
    if($a['parallax'] == 'false'){
        $parallax = 'chil_fixed';
    }

    return '<div class="chil_background '.$parallax.'" style="background-image:url(\''.$a['image'].'\');">'.do_shortcode($content).'</div>';
}
add_shortcode( 'chil_background', 'chil_background_func' );

function chil_inspired_func( $atts, $content = null ) {
    $a = shortcode_atts( array(
        'heading' => 'Korean',
        'script' => 'inspired',
    ), $atts );

    $result = '<div class="chil_inspired"><div>';
    $result .= ' <div class="chil_inspired_heading chil_orange uppercase">'.$a['heading'].'</div>';
    $result .= ' <div class="chil_inspired_script chil_white">'.$a['script'].'</div>';
    $result .= '</div></div>';

    return $result;
}
add_shortcode( 'chil_inspired', 'chil_inspired_func' );
