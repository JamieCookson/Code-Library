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
