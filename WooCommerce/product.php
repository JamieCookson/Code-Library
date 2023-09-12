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

	// Always show variation price
		add_filter( 'woocommerce_show_variation_price', '__return_true' );
			
	// Change sale badge text
		function wc_custom_replace_sale_text( $html ) {
			return str_replace( __( 'Sale!', 'woocommerce' ), __( 'Sale', 'woocommerce' ), $html );
		}
		add_filter( 'woocommerce_sale_flash', 'wc_custom_replace_sale_text' );

	// Change number of upsells or related output
		function number_related_products( $args ) {
			$args['posts_per_page'] = 4; 
			$args['columns']        = 4; 
			return $args;
		}
		add_filter( 'woocommerce_output_related_products_args', 'number_related_products', 20 ); // Related / You might also like
		add_filter( 'woocommerce_upsell_display_args', 'number_related_products' ); // Upsell

	// Change related products title
		function change_related_product_title ( $translated ) {

			$translated = str_replace( 'Related products', 'Related products and accessories', $translated );
			return $translated;

		}
		add_filter( 'gettext', 'change_related_product_title' );

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

	// Add Plus/minus button on product page

		// Add buttons
			function bfi_display_quantity_plus() {
				echo '<button type="button" class="add"><span>+</span></button>';
			}
			function bfi_display_quantity_minus() {
				echo '<button type="button" class="remove"><span>-</span></button>';
			}
			add_action( 'woocommerce_after_quantity_input_field', 'bfi_display_quantity_plus' );
			add_action( 'woocommerce_before_quantity_input_field', 'bfi_display_quantity_minus' );

		// Trigger update quantity script
			function bfi_add_cart_quantity_plus_minus() {
				if ( ! is_product() && ! is_cart() ) return;
				wc_enqueue_js( "

					$(document).on( 'click', 'button.add, button.remove', function() {

						var qty = $( this ).parent( '.quantity' ).find( '.qty' );
						var val = parseFloat(qty.val());
						var max = parseFloat(qty.attr( 'max' ));
						var min = parseFloat(qty.attr( 'min' ));
						var step = parseFloat(qty.attr( 'step' ));

						if ( $( this ).is( '.add' ) ) {
							if ( max && ( max <= val ) ) {
							qty.val( max ).change();
							} else {
							qty.val( val + step ).change();
							}
						} else {
							if ( min && ( min >= val ) ) {
							qty.val( min ).change();
							} else if ( val > 1 ) {
							qty.val( val - step ).change();
							}
						}

					});

				" );
			}
			add_action( 'wp_footer', 'bfi_add_cart_quantity_plus_minus' );

	// Show related categories 
	// https://www.businessbloomer.com/woocommerce-display-related-product-categories-single-product-page/
		function related_product_categories () {
			
			global $product;
			$cat_ids = $product->get_category_ids();

			echo '<section class="related categories"><h2>Related Categories</h2>';
				echo do_shortcode( '[product_categories ids="' . implode( ",", $cat_ids ) . '"]' );
			echo '</section>';

		}
		add_action( 'woocommerce_after_single_product_summary' , 'related_product_categories', 21 );
 
	// Show add to cart on category pages - could be used on pop-up? like quick view
	// https://www.businessbloomer.com/woocommerce-add-to-cart-form-shop-page/
		function display_add_cart_form () {
			
			global $product;
			if ( ! $product->is_purchasable() ) return;
			remove_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_add_to_cart', 10 );
			add_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_single_add_to_cart', 11 );
		
		}
		add_action( 'woocommerce_after_shop_loop_item', 'display_add_cart_form', 1 );

	// Add subtotal when increasing qty
	// https://www.businessbloomer.com/woocommerce-calculate-subtotal-on-quantity-increment-single-product/
		function product_price_recalculate () {

			global $product;
			echo '<div id="subtot" style="display:inline-block;">Total: <span></span></div>';
			$price = $product->get_price();
			$currency = get_woocommerce_currency_symbol();
			wc_enqueue_js( "     
				$('[name=quantity]').on('input change', function() { 
					var qty = $(this).val();
					var price = '" . esc_js( $price ) . "';
					var price_string = (price*qty).toFixed(2);
					$('#subtot > span').html('" . esc_js( $currency ) . "'+price_string);
				}).change();
			" );

		}
		add_action( 'woocommerce_after_add_to_cart_button', 'product_price_recalculate' );

	// Add POA message if price field is empty
	// https://www.businessbloomer.com/woocommerce-poa-call-price-button-price-empty/
		function poa_message () {
			
			$html = '<div class="poa">POA</div>';
			$html .= '<button type="submit" id="trigger_cf" class="single_add_to_cart_button button alt"> Request Price Now </button>';
			$html .=  '<div id="product_inq" style="display:none">';
			$html .= do_shortcode('[paste_your_contact_form_7_shortcode_here]');
			$html .=  '</div>';
			return $html;

		}
		add_filter( 'woocommerce_empty_price_html', 'poa_message' );

	// Add custom WooCommerce field to product page - E.G. RRP field
	// https://www.businessbloomer.com/woocommerce-display-rrp-msrp-manufacturer-price/
		
		// Add input to product edit page    
			function add_input_to_products () {  

				woocommerce_wp_text_input( array( 
					'id' => 'rrp', 
					'class' => 'short wc_input_price', 
					'label' => __( 'RRP', 'woocommerce' ) . ' (' . get_woocommerce_currency_symbol() . ')',
					'data_type' => 'price', 
				));

			}
			add_action( 'woocommerce_product_options_pricing', 'add_input_to_products' );  
		
		// Save field
			function save_input ( $product_id ) {

				global $typenow;
				if ( 'product' === $typenow ) {
					if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;
					if ( isset( $_POST['rrp'] ) ) {
						update_post_meta( $product_id, 'rrp', $_POST['rrp'] );
					}
				}

			}
			add_action( 'save_post_product', 'bbloomer_save_RRP' );
		
		// Display field on front-end
			function display_input () {

				global $product;
				$rrp = get_post_meta( $product->get_id(), 'rrp', true );
				echo '<div class="woocommerce_rrp">';
					_e( 'RRP: ', 'woocommerce' );
					echo '<span>' . wc_price( $rrp ) . '</span>';
				echo '</div>';

			}
			add_action( 'woocommerce_single_product_summary', 'display_input', 9 );