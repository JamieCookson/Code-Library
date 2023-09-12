<?php 
/**
 * Category page
 */

	// Remove prices
	// https://www.businessbloomer.com/woocommerce-hide-prices-shop-category-pages/
		remove_action( 'woocommerce_after_shop_loop_item_title', 'woocommerce_template_loop_price', 10 );

	// Hide category product count in product archives
		add_filter( 'woocommerce_subcategory_count_html', '__return_false' );

	// Remove sorting dropdown
	// https://www.businessbloomer.com/woocommerce-remove-default-sorting-dropdown/
		remove_action( 'woocommerce_before_shop_loop', 'woocommerce_catalog_ordering', 30 );

	// Change add to cart button [archive] text
		function universal_archive_add_to_cart_text () {
			global $product; 
			
			if( $product->is_type( 'simple' ) ){
				return __( 'Quick Buy', 'woocommerce' );
			} else {
				return __( 'View Options', 'woocommerce' );
			}
		}
		add_filter( 'woocommerce_product_add_to_cart_text', 'universal_archive_add_to_cart_text' );

	// Add % sales badge 
		function percentage_sale_price () {

			global $product;
			if( $product->is_on_sale() ):


				$notice_sale_price = $product->is_type('variable') ? $product->get_variation_sale_price() : $product->get_sale_price();
				$notice_reg_price = $product->is_type('variable') ? $product->get_variation_regular_price() : $product->get_regular_price();
				
				$percentage = round( ( ( $notice_reg_price - $notice_sale_price ) / $notice_reg_price ) * 100 ); ?>
				<span class="onsale percentage"><?php echo $percentage; ?>% off</span>

			<?php endif;

		}
		add_filter('woocommerce_before_shop_loop_item', 'percentage_sale_price', 15, 5);
	
	// Add custom flag
		function custom_prod_flag () {

			global $product;
			$flag_colour = get_field('flag_colour', $product->id);
			$product_flag = get_field('product_flag', $product->id);

			if($product_flag) {  ?>
				<div class="product-flag custom-flag" style="background-color: <?php echo $flag_colour; ?>"><?php echo $product_flag; ?></div>
			<?php }

		}
		add_action( 'woocommerce_before_shop_loop_item', 'custom_prod_flag', 1 );

	// New flag
	// https://www.businessbloomer.com/woocommerce-new-badge-recent-products/
		function new_flag() {

			global $product;
			$newness_days = 30; // Number of days
			$created = strtotime( $product->get_date_created() );
			if ( ( time() - ( 60 * 60 * 24 * $newness_days ) ) < $created ) {
				echo '<span class="itsnew onsale">' . esc_html__( 'New!', 'woocommerce' ) . '</span>';
			}

		}
		add_action( 'woocommerce_before_shop_loop_item_title', 'new_flag', 3 );

	// Change number of products that are displayed per page (shop page)
		function number_products_on_category_page ( $cols ) {

			$cols = 24;
			return $cols;

		}
		add_filter( 'loop_shop_per_page', 'number_products_on_category_page', 20 );

	// Update number of products per line
		function products_per_row () {
			return 3;
		}
		add_filter('loop_shop_columns', 'products_per_row', 999);

	// Remove title from category page
		function hide_cat_page_title ( $title ) {

			if ( is_product_category() ) $title = false;
			if ( is_shop() ) $title = false;
			return $title;

		}
		add_filter( 'woocommerce_show_page_title', 'hide_cat_page_title' );

	// Remove sorting option from dropdown
	// https://www.businessbloomer.com/woocommerce-remove-rename-add-default-sorting-options-shop/
		function remove_sorting_option ( $options ) {
			
			unset( $options['menu_order'] );   
			unset( $options['popularity'] );   
			unset( $options['rating'] );   
			unset( $options['date'] );   
			unset( $options['price'] );   
			unset( $options['price-desc'] );   
			return $options;

		}
		add_filter( 'woocommerce_catalog_orderby', 'remove_sorting_option' );

	// Rename sorting option
		function rename_sorting_option ( $options ) {

			$options['price'] = 'Sort by price (asc)';   
			return $options;
		
		}
		add_filter( 'woocommerce_catalog_orderby', 'rename_sorting_option' );

	// Remove "Add to cart" add "view product"
	// https://www.businessbloomer.com/woocommerce-remove-add-cart-add-view-product-loop/

		// Remove "Add to cart" button
			remove_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_add_to_cart', 10 );
  
		// add View Product Button
			function view_product_button() {

				global $product;
				$link = $product->get_permalink();
				echo '<a href="' . $link . '" class="button addtocartbutton">View Product</a>';

			}
			add_action( 'woocommerce_after_shop_loop_item', 'view_product_button', 10 );