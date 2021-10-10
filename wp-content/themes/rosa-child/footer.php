<?php
/**
 * The template for displaying the footer widget areas.
 * @package Rosa
 * @since   Rosa 1.0
 **/

global $is_gmap, $footer_needs_big_waves;

if ( $is_gmap === true ) {
	//we definitely need the Google Maps API
	wp_enqueue_script( 'google-maps-api' );
}

if ( ! is_404() ):
	$footer_sidebar_style    = 'sidebar--footer__' . wpgrade::option( 'footer_sidebar_style' );
	$footer_bottom_bar_style = 'copyright-area__' . wpgrade::option( 'footer_bottombar_style' );
	?>

	<footer class="site-footer <?php //echo $footer_needs_big_waves === true ? 'border-waves' : '' ?>">
		<aside class="sidebar  sidebar--footer <?php echo $footer_sidebar_style ?>">
			<div class="container">
				<?php get_template_part( 'sidebar-footer' ); ?>
			</div>
		</aside>
		<!-- .sidebar.sidebar- -footer -->
		<div class="copyright-area <?php echo $footer_bottom_bar_style ?>">
            <!-- <svg class="blurp--bottom" width="192" height="61" version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 160.7 61.5" enable-background="new 0 0 160.7 61.5" xml:space="preserve"><path fill="#FFFFFF" d="M80.3,61.5c0,0,22.1-2.7,43.1-5.4s41-5.4,36.6-5.4c-21.7,0-34.1-12.7-44.9-25.4S95.3,0,80.3,0c-15,0-24.1,12.7-34.9,25.4S22.3,50.8,0.6,50.8c-4.3,0-6.5,0,3.5,1.3S36.2,56.1,80.3,61.5z"/></svg>
            <div class="btn--top">
                <a href="#" class="btn--top_text">
                    <span class="btn__arrow btn__arrow--top"></span>
                    <span class="btn__arrow btn__arrow--bottom"></span>
                </a>
            </div> -->
			<div class="container">
				<div class="footer-container">
					<?php $copyright_text = wpgrade::option( 'copyright_text' );
					if ( ! empty( $copyright_text ) ) : ?>
						<div class="copyright-text">
							<?php echo wpgrade::option( 'copyright_text' ) ?>
						</div>
					<?php endif;
					wpgrade_footer_nav( '<nav class="navigation  navigation--footer">', '</nav>' ); ?>

				</div>
			</div>
		</div>
		<!-- .copyright-area -->
	</footer><!-- .site--footer -->

<?php endif; ?>

</div><!-- #page -->

<?php get_template_part('templates/floating-social'); ?>
<?php get_template_part('templates/pop-ups'); ?>

<script>
jQuery(document).ready(function(){
	jQuery('.chil_ordernow a').attr('id', 'chil_ordernow');
	jQuery('#chil_ordernow').click(function(){
		jQuery('#chil_order').fadeIn('300');
	});
	jQuery('#ordernow').click(function(){
		jQuery('#chil_order').fadeIn('300');
	});

	jQuery('.chil_close').click(function(){
		jQuery(this).parent().parent().fadeOut('300');
	});

	jQuery('#chil_newsletter .chil_close').click(function(e){
		jQuery.ajax({
		    type: 'POST',
		    url: '<?php echo site_url(); ?>/wp-content/themes/rosa-child/set-session.php',
		    data: {
		    	'chilantroSession': 'true'
		    },
		    success: function(result){
			    //alert(result);
			}
		});
	});

	var siteHeader_height = jQuery('.site-header').height();
	jQuery('.chil_page_header_image').css('padding-top', siteHeader_height - 10);
	jQuery('.home_mobile_wrapper').css('padding-top', siteHeader_height - 10);

	var logoPosition = jQuery('.site-header__branding').position();
	var chilHeaderTextPosition = logoPosition.left;
	jQuery('.chil_slide_text').css('left', chilHeaderTextPosition);
});

jQuery(window).load(function(){
	setTimeout(function(){
		jQuery('.page_loader_wrapper').fadeOut('1000');
	}, 500);

	<?php
		if(!isset($_SESSION['chilantro'])):
		?>

			setTimeout(function(){
				jQuery('#chil_newsletter').fadeIn('1000');
			}, 10000);

		<?php endif; ?>
});

jQuery(window).resize(function(){
	var logoPosition = jQuery('.site-header__branding').position();
	var chilHeaderTextPosition = logoPosition.left;
	jQuery('.chil_slide_text').css('left', chilHeaderTextPosition);
});

</script>

<a id="ordernow" href="/locations" class="show_mobile"><span>ORDER NOW</span></a>

<?php wp_footer(); ?>
</body>
</html>
