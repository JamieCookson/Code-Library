<?php
/**
 * General functions
 */

	// Disable the Storefront customiser
		add_filter( 'storefront_customizer_enabled', '__return_false' );

	// Remove admin bar on mobile
		if( wp_is_mobile() ) {
			add_filter( 'show_admin_bar', '__return_false' );
		}

	// Allow SVG's through Media Upload
		function cc_mime_types($mimes) {
			$mimes['svg'] = 'image/svg+xml';
			$mimes['svgz'] = 'image/svg+xml';
			return $mimes;
		}
		add_filter('upload_mimes', 'cc_mime_types');
		define('ALLOW_UNFILTERED_UPLOADS', true);

	// Add Styling to login screen
		function login_page_styling() { ?>

			<style>
				body.login {
					background: #FFF;
				}
				#login h1 a, .login h1 a {
					background-image: url(<?php echo get_stylesheet_directory_uri(); ?>/images/logo.svg);
					background-size: contain;
					height: 100px;
					width: 100%;
				}
				.login #login form {
					background: #e9edf2;
					border-radius: 5px;
					box-shadow: 0 0 5px #033968;
				}
			</style>

		<?php }
		add_action( 'login_enqueue_scripts', 'login_page_styling' );
	
	// Link logo to homepage on login page
		function login_page_logo_url() {
			return home_url();
		}
		add_filter( 'login_headerurl', 'login_page_logo_url' );

	// Set Yoast SEO priority to low, so that CFS etc. will appear above by default
		add_filter( 'wpseo_metabox_prio', function() { return 'low';});

	// Increasing max filesize upload (!!DO NOT USE ON BFI SERVERS!!)
		function increase_upload_size( $bytes ) {
			return 8388608; // 4MB
		}
		add_filter( 'upload_size_limit', 'increase_upload_size' );
