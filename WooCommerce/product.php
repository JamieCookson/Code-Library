<?php 
/**
 * Product pages
 */

	// Hide weight
		add_filter( 'wc_product_enable_dimensions_display', '__return_false' );

	// Increase maximum number of variations per product (from 50 to 1000)
		define('WC_MAX_LINKED_VARIATIONS', 1000);

	// Allow duplicate SKUs
		add_filter( 'wc_product_has_unique_sku', '__return_false' ); 

	// Change sale badge text
		function wc_custom_replace_sale_text( $html ) {
			return str_replace( __( 'Sale!', 'woocommerce' ), __( 'Sale', 'woocommerce' ), $html );
		}
		add_filter( 'woocommerce_sale_flash', 'wc_custom_replace_sale_text' );

	// Change number of upsells output
		function number_products_on_upsell( $args ) {
			$args['posts_per_page'] = 4; 
			$args['columns']        = 4; 
			return $args;
		}
		add_filter( 'woocommerce_upsell_display_args', 'number_products_on_upsell' );

	// Add the SKU to the item meta
		function add_sku_to_meta( $item_id, $values, $cart_item_key ) {

			$item_sku  =  get_post_meta( $values[ 'product_id' ], '_sku', true );
			$item_has_variation  =  ( ! empty( $values[ 'variation_id' ] ) ? true : false );
			
			if( $item_has_variation ) {
				wc_add_order_item_meta( $item_id, 'parent_sku', $item_sku, false );
				$variation_sku  =  get_post_meta( $values[ 'variation_id' ], '_sku', true );
				wc_add_order_item_meta( $item_id, 'variation_sku', $variation_sku, false );
			} else {
				wc_add_order_item_meta( $item_id, 'sku', $item_sku , false );
			}

		}
		add_action( 'woocommerce_add_order_item_meta', 'add_sku_to_meta', 10, 3 );

	// Change out of stock alert message.
		function change_out_of_stock_text( $params, $handle ) {

			if ( $handle === 'wc-add-to-cart-variation' )
				$params['i18n_no_matching_variations_text'] = __( 'Sorry, your selection is currently out of stock. Please select a different size or colour', 'domain' );
			return $params;

		}
		add_filter( 'woocommerce_get_script_data', 'change_out_of_stock_text', 10, 2 );
		
	// Add revisions to product pages
		function cinch_add_revision_support( $supports ) {
			$supports['supports'][] = 'revisions';
			return $supports;
		}
		add_filter( 'woocommerce_register_post_type_product', 'cinch_add_revision_support' );