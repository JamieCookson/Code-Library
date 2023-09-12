<?php 
/**
 * Checkout
 */

	// Make telephone field required
		function require_phone_field( $fields ) {

			$fields['billing_phone']['required'] = true;
			return $fields;

		}
		add_filter( 'woocommerce_billing_fields', 'require_phone_field' );

	// Add the field to the checkout
		function your_reference_field( $checkout ) {

			woocommerce_form_field( 'your_reference', array(
				'type'          => 'text',
				'class'         => array('form-row-wide'),
				'label'         => __('Your purchase order number'),
				'placeholder'   => __(''),
			), $checkout->get_value( 'your_reference' ));

		}
		add_action( 'woocommerce_before_order_notes', 'your_reference_field' );

	// Disable bank transfer payment method for orders below £50
		function enable_disable_payment_gateway( $gateways ){

			if( WC()->cart->subtotal < 50 ){
				// unset the 'bacs' key
				unset( $gateways['bacs'] );
			}
			return $gateways;

		}
		add_filter( 'woocommerce_available_payment_gateways' , 'enable_disable_payment_gateway', 20, 1);

	// Change Create an account text on checkout page.
		function create_acct_text( $translated_text, $text, $domain ) {

			switch ( $translated_text ) {
				case 'Create an account?' :
					$translated_text = __( 'Create an account to receive reward points and to enjoy speedier shopping next time', 'woocommerce' );
				break;
			}
			return $translated_text;

		}
		add_filter( 'gettext', 'create_acct_text', 20, 3 );

	// Update checkout fields
		function update_checkout_fields( $fields ) {

			// Set maxlength

				// First name
				$fields['billing']['billing_first_name']['maxlength'] = 30;
				$fields['shipping']['shipping_first_name']['maxlength'] = 30;
				
				// Last name
				$fields['billing']['billing_last_name']['maxlength'] = 30;
				$fields['shipping']['shipping_last_name']['maxlength'] = 30;
				
				// Company name
				$fields['billing']['billing_company']['maxlength'] = 45;
				$fields['shipping']['shipping_company']['maxlength'] = 45;

				// Address
				$fields['billing']['billing_address_1']['maxlength'] = 45;
				$fields['shipping']['shipping_address_1']['maxlength'] = 45;
				$fields['billing']['billing_address_2']['maxlength'] = 45;
				$fields['shipping']['shipping_address_2']['maxlength'] = 45;
				$fields['billing']['billing_city']['maxlength'] = 45;
				$fields['shipping']['shipping_city']['maxlength'] = 45;
				$fields['billing']['billing_state']['maxlength'] = 45;
				$fields['shipping']['shipping_state']['maxlength'] = 45;

				// Order notes
				$fields[ 'order' ][ 'order_comments' ][ 'maxlength' ] = 20;

			// Rename company autocomplete name
				$fields[ 'billing' ][ 'billing_company' ][ 'autocomplete' ] = 'company_name';
				$fields[ 'shipping' ][ 'shipping_company' ][ 'autocomplete' ] = 'company_name';
			
			// Update label
				$fields[ 'order' ][ 'order_comments' ][ 'label' ] = 'Order notes (max 20 characters)';
			
			return $fields;

		}
		add_filter( 'woocommerce_checkout_fields' , 'update_checkout_fields' );

	// Add link to breadcrumb on cart page.
		function wpseo_breadcrumb_add_woo_shop_link( $links ) {

			global $post;
			if ( is_checkout() ) {

				$breadcrumb[] = array(
					'url' => '/basket/',
					'text' => 'Basket',
				);
				array_splice( $links, 1, -2, $breadcrumb );
				
			}
			return $links;

		}
		add_filter( 'wpseo_breadcrumb_links', 'wpseo_breadcrumb_add_woo_shop_link' );
