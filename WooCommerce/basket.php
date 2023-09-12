<?php
/**
 * Basket page 
 */

	// Update 'no shipping methods' text
		function change_no_shipping_message() {
			print 'To calculate shipping costs to your address, please click Save and Share Basket and send the link to <a href="mailto:info@karagroup.co.uk">info@karagroup.co.uk</a>. We will confirm the shipping cost by email.';
		}
		add_filter( 'woocommerce_cart_no_shipping_available_html', 'change_no_shipping_message' );
		add_filter( 'woocommerce_no_shipping_available_html', 'change_no_shipping_message' );

	// Add a call to action on the basket page to show if the user has spent less than the free delivery amount
		function free_delivery_cta() {

			global $woocommerce;  
			$sub_total = $woocommerce->cart->subtotal;
			$free_delivery = get_field('free_delivery_amount', 'option');
			$activate_free_delivery = get_field_object('activate_free_delivery_call_to_action', 'option');

			if($sub_total < $free_delivery) {

				if($activate_free_delivery['value'] == 'activate'):

					$free_delivery_difference = $free_delivery - $sub_total;
					$free_delivery_difference = money_format('%i', $free_delivery_difference);
					$delivery_message = get_field('free_delivery_message', 'option');

					echo '<div class="basket-cta">';
						echo '<div class="basket-message">';
						echo '<span class="delivery"></span>';
						echo '<div class="text">Spend £' . $free_delivery_difference . ' ' . $delivery_message . '</div>';
						echo '</div>';
						echo '<a href="/shop/" class="button">Continue shopping</a>';
					echo '</div>';

				endif;

			}

		}
		add_filter( 'woocommerce_before_cart_contents', 'free_delivery_cta' );

	// Hide shipping rates when free shipping is available.
		function hide_shipping_when_free_is_available( $rates, $package ) {

			$new_rates = array();
			foreach ( $rates as $rate_id => $rate ) {
				// Only modify rates if free_shipping is present.
				if ( 'free_shipping' === $rate->method_id ) {
					$new_rates[ $rate_id ] = $rate;
					break;
				}
			}

			if ( ! empty( $new_rates ) ) {
				//Save local pickup if it's present.
				foreach ( $rates as $rate_id => $rate ) {
					if ('local_pickup' === $rate->method_id ) {
						$new_rates[ $rate_id ] = $rate;
						break;
					}
				}
				return $new_rates;
			}
			return $rates;

		}
		add_filter( 'woocommerce_package_rates', 'hide_shipping_when_free_is_available', 10, 2 );

	// Add SKU to basket
		function display_sku_after_item_name ( $item_name, $cart_item, $cart_item_key ) {

			$product = $cart_item['data']; // The WC_Product Object
			if( is_cart() && $product->get_sku() ) {
				$item_name .= '<span class="item-sku"> - '. $product->get_sku() . '</span>';
			}
			return $item_name;

		}
		add_filter( 'woocommerce_cart_item_name', 'display_sku_after_item_name', 5, 3 );

	// qty based discounts
	// https://www.businessbloomer.com/woocommerce-bulk-dynamic-pricing-without-a-plugin/
		function quantity_based_pricing ( $cart ) {
			
			if ( is_admin() && ! defined( 'DOING_AJAX' ) ) return;
			if ( did_action( 'woocommerce_before_calculate_totals' ) >= 2 ) return;
			
			// Define discount rules and thresholds
				$threshold1 = 101; // Change price if items > 100
				$discount1 = 0.05; // Reduce unit price by 5%
				
				$threshold2 = 1001; // Change price if items > 1000
				$discount2 = 0.1; // Reduce unit price by 10%
			
			foreach ( $cart->get_cart() as $cart_item_key => $cart_item ) {
				
				if ( $cart_item['quantity'] >= $threshold1 && $cart_item['quantity'] < $threshold2 ) {
					$price = round( $cart_item['data']->get_price() * ( 1 - $discount1 ), 2 );
					$cart_item['data']->set_price( $price );
				} elseif ( $cart_item['quantity'] >= $threshold2 ) {
					$price = round( $cart_item['data']->get_price() * ( 1 - $discount2 ), 2 );
					$cart_item['data']->set_price( $price );
				} 

			}
			
		}
		add_action( 'woocommerce_before_calculate_totals', 'quantity_based_pricing', 9999 );

	// Display sale & regular price
	// https://www.businessbloomer.com/woocommerce-display-regularsale-price-cart-table/
		function basket_sale_and_regular_price( $price, $values, $cart_item_key ) {
			
			$slashed_price = $values['data']->get_price_html();
			$is_on_sale = $values['data']->is_on_sale();
			
			if ( $is_on_sale ) {
				$price = $slashed_price;
			}
			return $price;

		}
		add_filter( 'woocommerce_cart_item_price', 'basket_sale_and_regular_price', 30, 3 );