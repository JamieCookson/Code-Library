<?php 
/**
 * My Account
 */

	// Update fields on registering via My acct page 
		
		// Add fields
			function wp_register_acct_fields() { ?>

				<p class="form-row form-row-first">
					<label for="reg_billing_first_name"><?php _e( 'First name', 'woocommerce' ); ?><span class="required">*</span></label>
					<input type="text" class="input-text" name="billing_first_name" id="reg_billing_first_name" value="<?php if ( ! empty( $_POST['billing_first_name'] ) ) esc_attr_e( $_POST['billing_first_name'] ); ?>" />
				</p>
				
				<p class="form-row form-row-last">
					<label for="reg_billing_last_name"><?php _e( 'Last name', 'woocommerce' ); ?><span class="required">*</span></label>
					<input type="text" class="input-text" name="billing_last_name" id="reg_billing_last_name" value="<?php if ( ! empty( $_POST['billing_last_name'] ) ) esc_attr_e( $_POST['billing_last_name'] ); ?>" />
				</p>

				<p class="form-row form-row-wide">
					<label for="reg_billing_phone"><?php _e( 'Phone', 'woocommerce' ); ?><span class="required">*</span></label>
					<input type="text" class="input-text" name="billing_phone" id="reg_billing_phone" value="<?php esc_attr_e( $_POST['billing_phone'] ); ?>" />
				</p>

				<div class="clear"></div>
				
			<?php }
			add_action( 'woocommerce_register_form', 'wp_register_acct_fields', 1 );

		// Validate fields
			function wp_validate_acct_fields( $username, $email, $validation_errors ) {

				if ( isset( $_POST['billing_first_name'] ) && empty( $_POST['billing_first_name'] ) ) {
					$validation_errors->add( 'billing_first_name_error', __( '<strong>Error</strong>: First name is required!', 'woocommerce' ) );
				}

				if ( isset( $_POST['billing_last_name'] ) && empty( $_POST['billing_last_name'] ) ) {
					$validation_errors->add( 'billing_last_name_error', __( '<strong>Error</strong>: Last name is required!.', 'woocommerce' ) );
				}

				if ( isset( $_POST['billing_phone'] ) && empty( $_POST['billing_phone'] ) ) {
					$validation_errors->add( 'billing_phone_error', __( '<strong>Error</strong>: Phone number is required!.', 'woocommerce' ) );
				}

				return $validation_errors;

			}
			add_action( 'woocommerce_register_post', 'wp_validate_acct_fields', 10, 3 );

		// Save data
			function wp_save_acct_fields( $customer_id ) {

				if ( isset( $_POST['billing_phone'] ) ) {

					// Phone input filed which is used in WooCommerce
					update_user_meta( $customer_id, 'billing_phone', sanitize_text_field( $_POST['billing_phone'] ) );

				}
			
				if ( isset( $_POST['billing_first_name'] ) ) {

					//First name field which is by default
					update_user_meta( $customer_id, 'first_name', sanitize_text_field( $_POST['billing_first_name'] ) );
					
					// First name field which is used in WooCommerce
					update_user_meta( $customer_id, 'billing_first_name', sanitize_text_field( $_POST['billing_first_name'] ) );

				}
			
				if ( isset( $_POST['billing_last_name'] ) ) {

					// Last name field which is by default
					update_user_meta( $customer_id, 'last_name', sanitize_text_field( $_POST['billing_last_name'] ) );
					
					// Last name field which is used in WooCommerce
					update_user_meta( $customer_id, 'billing_last_name', sanitize_text_field( $_POST['billing_last_name'] ) );

				}
			
			}
			add_action( 'woocommerce_created_customer', 'wp_save_acct_fields' );

	// Add button to order items again
		function add_order_again_bttn( $actions, $order ) {
			
			if ( ! $order || ! $order->has_status( apply_filters( 'woocommerce_valid_order_statuses_for_order_again', array( 'completed' ) ) ) || ! is_user_logged_in() ) {
				return $actions;
			}

			$actions['order-again'] = array(
				'url'  => wp_nonce_url( add_query_arg( 'order_again', $order->get_id() ) , 'woocommerce-order_again' ),
				'name' => __( 'Order Again', 'woocommerce' )
			);
			return $actions;

		}
		add_filter( 'woocommerce_my_account_my_orders_actions', 'add_order_again_bttn', 10, 2 );
