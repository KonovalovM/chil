<?php

/**
 * Welcome screen
 * The functions below add a nice welcome screen to WordPress
 * after activating a Theme.Works theme.
 */

/**
 * Redirect after theme activation
 */
if ( is_admin() && isset( $_GET['activated'] ) && $GLOBALS['pagenow'] == 'themes.php' ) {
	wp_safe_redirect( add_query_arg( array( 'page' => 'tw-welcome-screen' ), admin_url( 'themes.php' ) ) );
	exit;
}

/**
 * Add admin menu page
 */
function tw_welcome_screen_pages() {
	add_submenu_page(
		'themes.php',
		__( 'Theme.Works Guide', 'themeworks' ),
		__( 'Theme.Works Guide', 'themeworks' ),
		'read',
		'tw-welcome-screen',
		'tw_welcome_screen_content'
	);
}
add_action( 'admin_menu', 'tw_welcome_screen_pages' );


/**
 * The actual welcome page
 */
function tw_welcome_screen_content() {
	?>
	<div class="wrap about-wrap">
		<h1><?php _e( 'Theme.Works Quick Start Guide', 'themeworks' ); ?></h1>
		<div class="about-text">
			<?php _e( 'Theme setup and customization is easy. Just follow these four simple steps:', 'themeworks' ); ?>
		</div>
		<div class="about-description">
			<div class="what-is-new">
				<div class="feature-section">
					<div id="tw-import-help" class="clear">
						<div class="col">
							<h3>1. <?php _e( 'Import Sample Content', 'themeworks'); ?></h3>
							<p><?php _e( 'Having some sample content to work with can be helpful, especially if you\'re new to using WordPress. This is also the easiest way to have your new website like the demo. Follow these simple steps to import sample content:', 'themeworks' ); ?></p>
							<p>
								<ol>
									<li><?php _e( '<a href="https://themeworks.s3.amazonaws.com/sample-content/themeworks.wordpress.2015-02-03.xml">Download this XML file.</a>', 'themeworks' ); ?></li>
									<li><?php _e( 'Visit Tools -&gt; Import.', 'themeworks' ); ?></li>
									<li><?php _e( 'Upload the XML file.', 'themeworks' ); ?></li>
								</ol>
							</p>
						</div>

						<div class="col">
							<img src="https://themeworks.s3.amazonaws.com/images/themeworks-welcome-import.png" />
						</div>
					</div>

					<div id="tw-customize-help" class="clear">
						<div class="col">
							<img src="https://themeworks.s3.amazonaws.com/images/themeworks-welcome-customizer.png" />
						</div>
						<div class="col">
							<h3>2. <?php _e( 'Customize Your Website', 'themeworks' ); ?></h3>
							<p>
								<?php _e( 'The <a href="' . admin_url( 'customize.php' ) . '">Customizer</a> is where you will customize the design of your website. The Customizer contains dozens of options for logos, slides, welcome messages, background images, header images, newsletter signup form integration, color schemes, fonts, menus and widgets. Here you can preview your changes before making them publicly visible.', 'themeworks' ); ?>
							</p>
						</div>
					</div>

					<div id="tw-content-help" class="clear">
						<div class="col">
							<h3>3. <?php _e( 'Add Content', 'themeworks' ); ?></h3>
							<p>
								<?php _e( 'Your website should be unique, right? Add <a href="' . admin_url( 'post-new.php' ) . '">Posts</a>, <a href="' . admin_url( 'post-new.php?post_type=page' ) . '">Pages</a>, and other post types and be sure to always set Featured Images for all entries. If you see menu links for Testimonials, Portfolios or other post types, add some entries into those as well. Now that you\'ve started to add your own unique content, it\'s probably a good time to delete any sample content that you might have added before.', 'themeworks' ); ?>
							</p>
						</div>
						<div class="col">
							<img src="https://themeworks.s3.amazonaws.com/images/themeworks-welcome-content.png" />
						</div>
					</div>

					<div id="tw-menus-help" class="clear">
						<div class="col">
							<img src="https://themeworks.s3.amazonaws.com/images/themeworks-welcome-menus.png" />
						</div>

						<div class="col">
							<h3>4. <?php _e( 'Add Menus &amp; Widgets', 'themeworks' ); ?></h3>
							<p>
								<?php _e( 'Add a custom <a href="' . admin_url( 'nav-menus.php' ) . '">Menu</a> to your website navigation or add <a href="' . admin_url( 'widgets.php' ) . '">Widgets</a>. Widgets are independent sections of content that can be placed into any widgetized area provided by your theme.', 'themeworks' ); ?>
							</p>
						</div>
					</div>
				</div>

				<h3><?php _e( 'Want to learn more?', 'themeworks' ); ?></h3>
				<p><?php _e( 'See the full Theme.Works theme documentation for detailed instructions and screenshots for all aspects of theme configuration and customization.', 'themeworks' ); ?></p>
				<p><a href="<?php echo get_template_directory_uri(); ?>/README.html" class="button button-primary button-hero" target="_blank"><?php _e( 'View Full Documentation', 'themeworks' ); ?></a></p>
				<p><?php _e( 'Still need help? <a href="https://theme.works/contact/" target="_blank">Contact us</a>', 'themeworks' ); ?>.</p>
			
			</div>
		</div>
	</div>
	<?php
}