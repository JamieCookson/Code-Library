<?php 
/**
 * General WooCommerce functions
 */

	// Create function for woocommerce accounts ( different text for logged in )
		function account_login_links() { 

			if ( is_user_logged_in() ) {

				$logout_url = wp_logout_url( get_permalink( $myaccount_page_id ) );
				echo '<li class="your-account"><a href="'.get_permalink( get_option('woocommerce_myaccount_page_id') ).'"">Your Account</a></li>';
				echo '<li class="sign-out"><a href="' . $logout_url . '">Sign Out</a></li>';

			} else {

				echo '<li class="sign-in"><a href="' . get_permalink( get_option('woocommerce_myaccount_page_id') ) . '">Sign in</a></li>';
				echo '<li class="create-account"><a href="' . get_permalink( get_option('woocommerce_myaccount_page_id') ) . '">Create account</a></li>';
				
			}

		}

	// Change the placeholder image
		function placeholder_img_src( $src ) {

			$upload_dir = wp_upload_dir();
			$uploads = untrailingslashit( $upload_dir['baseurl'] );
			$src = $uploads . '/2020/04/placeholder-1.jpg';
			return $src;

		}
		add_filter('woocommerce_placeholder_img_src', 'placeholder_img_src');

	// Display prices with VAT & exc VAT
	// https://www.businessbloomer.com/woocommerce-prices-inc-and-ex-tax/
		function update_price_suffix ( $suffix, $product, $price, $qty ){

			$suffix = ' <small>ex. TAX</small> - ' . wc_price( wc_get_price_including_tax( $product ) ) . ' <small>inc. TAX</small>';
			return $suffix;

		}
		add_filter( 'woocommerce_get_price_suffix', 'update_price_suffix', 99, 4 );

	// Bulk settings 
	// https://www.businessbloomer.com/woocommerce-override-single-product-settings/
		
		// Enable/Disable reviews on all products
			function force_enable_reviews ( $enable, $post_id ) {
				
				if ( 'product' === get_post_type( $post_id ) ) {
					$enable = true;
				}
				return $enable;

			}
			add_filter( 'comments_open', 'force_enable_reviews', 9999, 2 );

		// Force sold individually / no qty box
			add_filter( 'woocommerce_is_sold_individually', '__return_true' );

