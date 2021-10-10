jQuery(document).ready(function(){
	var didScroll;
	var screenWidth = jQuery(window).width();

	var position = jQuery('.site-header .container .flexbox').offset();
	if(screenWidth > 899){
		jQuery('.chil_menu_tab').offset({ left: position.left });
	} else {
		jQuery('.chil_menu_tab').offset({ left: '0px' });
	}

	jQuery(window).scroll(function(event){
		didScroll = true;
	});

	// run hasScrolled() and reset didScroll status
	setInterval(function() {
		if (didScroll) {
			hasScrolled();
			didScroll = false;
		}
	}, 100);

	var lastScrollTop = jQuery(window).scrollTop();
	function hasScrolled() {
		var st = jQuery(this).scrollTop();
		var windowLocation = jQuery(window).scrollTop();

		if(st > 15){
			if (st > lastScrollTop){
				// downscroll code
				jQuery('.chil_menu_tab').addClass('pulldown');
			} else {
			  	// upscroll code
			    jQuery('.chil_menu_tab').removeClass('pulldown');
			}
			lastScrollTop = st;
		}
	}

	jQuery('.form_hidden_dropdown h4').click(function(){
		if(jQuery(this).children('i').hasClass('fa-plus')){
			jQuery(this).children('i').removeClass('fa-plus');
			jQuery(this).children('i').addClass('fa-minus')
		} else {
			jQuery(this).children('i').removeClass('fa-minus');
			jQuery(this).children('i').addClass('fa-plus')
		}
		jQuery(this).parent('div').children('.form_hidden_dropdown_content').slideToggle('200');
	});
});