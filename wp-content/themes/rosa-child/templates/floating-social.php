<ul class="chil--items-social">
<?php

$social_links = wpgrade::option( 'social_icons' );

$target = '';
if ( wpgrade::option( 'social_icons_target_blank' ) ) {
	$target = 'target="_blank"';
}

if ( ! empty( $social_links ) ) {
	foreach ( $social_links as $domain => $icon ) {
		if ( isset( $icon['value'] ) && isset( $icon['checkboxes']['header'] ) ) {
			$value = $icon['value']; ?>
			<li>
				<a class="social-icon" href="<?php echo $value ?>" <?php echo $target ?>>
					<i class="icon-e-<?php echo $domain; ?>"></i>
				</a>
			</li>
		<?php
		}
	}
}

?>
</ul>
<div id="social_show_tab" class="social_show_tab"><i class="icon-e-share"></i></div>

<script>
jQuery(document).ready(function(){
	jQuery('#social_show_tab').click(function(){
		if(jQuery('.chil--items-social').hasClass('show')){
			jQuery('.chil--items-social').slideToggle('200');
			jQuery('.chil--items-social').removeClass('show');
		} else {
			jQuery('.chil--items-social').slideToggle('200');
			jQuery('.chil--items-social').addClass('show')
		}
	});
});
</script>