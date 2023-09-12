<?php 
/**
 * Category page
 */

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

	// Hide category product count in product archives
		add_filter( 'woocommerce_subcategory_count_html', '__return_false' );
