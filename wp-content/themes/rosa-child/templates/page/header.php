<?php
/**
 * This template handles the page headers with image and cover text
 */

global $is_gmap, $page_section_idx, $header_height;

//increment the page section number
$page_section_idx++;

//header general classes
$classes = "article__header  article__header--page";

//first lets get to know this page a little better
$header_height = get_post_meta( wpgrade::lang_page_id( get_the_ID() ), wpgrade::prefix() . 'page_header_height', true );
if ( empty($header_height) ) {
	$header_height = 'half-height'; //the default
}
$classes .= ' ' . $header_height;

$subtitle = trim( __(get_post_meta( wpgrade::lang_page_id( get_the_ID() ), wpgrade::prefix() . 'page_cover_subtitle', true ) ));
//we need to mess with the subtitle a little bit - because it deserves it
//we need to wrap the first subtitle letter in a span so we can control it - height wise
if ( ! empty( $subtitle ) ) {
	$subtitle   = esc_html( $subtitle );
	$first_char = mb_substr( $subtitle, 0, 1 );
	$subtitle   = '<span class="first-letter">' . $first_char . '</span>' . mb_substr( $subtitle, 1 );
}

$title = __(get_post_meta( wpgrade::lang_page_id( get_the_ID() ), wpgrade::prefix() . 'page_cover_title', true ));
if ( empty( $title ) ) {
	//use the page title if empty
	$title = get_the_title();
}
$description = __(get_post_meta( wpgrade::lang_page_id( get_the_ID() ), wpgrade::prefix() . 'page_cover_description', true ));
//filter the content with some limitations to avoid having plugins doing nasty things to it
$description = wpgrade::filter_content( $description, 'default' );

/* FIRST TEST FOR CONTACT PAGE TEMPLATE */

//get the Google Maps URL to test if empty
$gmap_url = get_post_meta( wpgrade::lang_page_id( get_the_ID() ), wpgrade::prefix() . 'gmap_url', true );

if ( get_page_template_slug( get_the_ID() ) == 'page-templates/contact.php' ) {
	if ( ! empty( $gmap_url ) ) {
		//set the global so everybody knows that we are in dire need of the Google Maps API
		$is_gmap = true;

		$gmap_custom_style   = get_post_meta( wpgrade::lang_page_id( get_the_ID() ), wpgrade::prefix() . 'gmap_custom_style', true );
		$gmap_marker_content = get_post_meta( wpgrade::lang_page_id( get_the_ID() ), wpgrade::prefix() . 'gmap_marker_content', true );
		$gmap_height         = get_post_meta( wpgrade::lang_page_id( get_the_ID() ), wpgrade::prefix() . 'page_gmap_height', true );
		if ( empty( $gmap_height ) ) {
			$gmap_height = 'half-height'; //the default
		}
		$classes .= ' ' . $gmap_height;
		?>
		<!-- <header id="post-<?php the_ID() ?>-title" class="<?php echo $classes ?>">
			<div id="gmap"
				data-url="<?php esc_attr_e( $gmap_url ); ?>" <?php echo ( $gmap_custom_style == 'on' ) ? 'data-customstyle' : ''; ?>
				data-markercontent="<?php echo esc_attr( $gmap_marker_content ); ?>"></div>
		</header> -->
	<?php
	}
} else {
	/* THEN TEST FOR SLIDESHOW PAGE TEMPLATE */
	$slides = get_post_meta(get_the_ID(), '__chil_slides', true);
	$mobile_image = get_post_meta(get_the_ID(), '_chil_home_mobile_image', true);
	$mobile_text = get_post_meta(get_the_ID(), '_chil_home_mobile_text', true);
	$hide_mobile = '';

	if(!empty($mobile_image) || !empty($mobile_text)){
		$hide_mobile = "hide_mobile";
	}
	$gallery_ids = get_post_meta( get_the_ID(), wpgrade::prefix() . 'main_gallery', true );

	if ( get_page_template_slug( get_the_ID() ) == 'page-templates/slideshow.php' && ! empty( $gallery_ids ) ) {
		$gallery_ids = explode( ',', $gallery_ids );

		if ( ! empty( $gallery_ids ) ) {
			$attachments = get_posts( array(
				'post_type'      => 'attachment',
				'posts_per_page' => - 1,
				'orderby'        => "post__in",
				'post__in'       => $gallery_ids
			) );
		} else {
			$attachments = array();
		}

		if ( ! empty( $slides ) ) { //let's grab the info regarding the slider ?>

			<div id="post-<?php the_ID() ?>-title" class="<?php //echo $classes ?> chil_bxslider <?php echo $hide_mobile; ?>">
				<ul class="bxslider">
				<?php
					$count = 1;
					foreach ( $slides as $slide ) {

						$full_img          = wp_get_attachment_image_src( $attachment->ID, 'full-size' );
						$attachment_fields = get_post_custom( $attachment->ID );
				?>
					<li <?php if($count > 1){ echo 'style="display:none;"'; } ?>>
						<img src="<?php echo $slide['__chil_image']; ?>" class="attachment-blog-big" alt="<?php echo $attachment->post_excerpt; ?>" itemprop="contentURL"/>
						<div class="chil_slide_text"><?php echo wpautop(do_shortcode($slide['__chil_text'])); ?></div>
					</li>

				<?php $count++; } ?>
				</ul>
				<?php //display_header_down_arrow( $page_section_idx, $header_height ); ?>
			</div>
			<?php if(!empty($mobile_image) || !empty($mobile_text)){ ?>
				<div class="home_mobile_wrapper show_mobile">
					<?php if(!empty($mobile_image)): ?>
						<div class="home_mobile_image"><img src="<?php echo $mobile_image; ?>" /></div>
					<?php endif; ?>
					<?php if(!empty($mobile_text)): ?>
						<div class="home_mobile_text"><?php echo wpautop(do_shortcode($mobile_text)); ?></div>
					<?php endif; ?>
				</div>
			<?php } ?>
		<?php } else { ?>
			<div class="empty-slideshow">
				<?php _e( 'Currently there are no images assigned to this slideshow', 'rosa_txtd' ); ?>
			</div>
		<?php }

	} else { /* OR REGULAR PAGE */
		$background_img = get_post_meta(get_the_ID(), _chil_page_header_image, true);
		$description = get_post_meta(get_the_ID(), _chil_page_header_text, true);
		$bg_color = get_post_meta(get_the_ID(), _chil_page_header_bg_color, true);
		$bg_image = get_post_meta(get_the_ID(), _chil_page_header_bg_image, true);

		if ( ! empty( $description ) ) { ?>
			<?php
				/*
				 *  featured image = $image[0]
				 *  description = $description
				 *  
				 */
			?>
			<!--<header id="post-<?php the_ID() ?>-title" class="<?php echo $classes ?>" data-type="image">
				<?php if ( has_post_thumbnail() ) {
					$image = wp_get_attachment_image_src( get_post_thumbnail_id(), 'full-size' );
					if ( ! empty( $image[0] ) ) { ?>
						<div class="article__parallax  article__parallax--img">
							<img src="<?php echo $image[0] ?>" alt="<?php the_title(); ?>"/>
						</div>
					<?php
					}
				}
				?>
			</header>-->
			<div id="post-<?php the_ID() ?>-title" class="chil_page_header <?php //echo $classes ?>">
				<div class="chil_page_header_image"><img src="<?php echo $background_img ?>" alt="<?php the_title(); ?>"/></div>
				<div class="chil_page_header_text" style="background-image:url('<?php echo $bg_image; ?>');background-color:<?php echo $bg_color; ?>;">
					<?php echo wpautop(do_shortcode($description)); ?>
				</div>
				<div class="chil_page_header_image_bg" style="background-image:url('<?php echo $background_img ?>');"></div>
			</div>

			<script>
				jQuery(document).ready(function(){
					var logoPosition = jQuery('.site-header__branding').position();
					var chilHeaderTextPosition = logoPosition.left;
					jQuery('.chil_page_header_text').css('padding-left', chilHeaderTextPosition);
				});

				jQuery(window).resize(function(){
					var logoPosition = jQuery('.site-header__branding').position();
					var chilHeaderTextPosition = logoPosition.left;
					jQuery('.chil_page_header_text').css('padding-left', chilHeaderTextPosition);
				});
			</script>
		<?php } else { ?>
			<header id="post-<?php the_ID() ?>-title" class="<?php echo $classes ?>" style="display: none"></header>
		<?php }
	}
} ?>

<script>
jQuery(document).ready(function(){
	jQuery('.bxslider').bxSlider({
		'mode':'fade',
		'touchEnabled':true,
		'swipeThreshold':'150px',
	});

	var screenWidth = jQuery(document).width();
	var headerHeight = jQuery('.site-header').height();
	if(screenWidth < 900){
		jQuery('.chil_bxslider').css('padding-top', headerHeight);
	}
});
jQuery(window).load(function(){
	jQuery('a.bx-prev').html('<div class="rsArrowIcn"></div>');
	jQuery('a.bx-next').html('<div class="rsArrowIcn"></div>');
});
</script>