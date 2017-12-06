<?php
// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

// BEGIN ENQUEUE PARENT ACTION
// AUTO GENERATED - Do not modify or remove comment markers above or below:

// END ENQUEUE PARENT ACTION

add_action( 'init', 'remove_actions_parent_theme');
function remove_actions_parent_theme() {

	#reposition nav-widgets
	remove_action( 'storefront_header', 'storefront_product_search', 40 );
	remove_action( 'storefront_header', 'storefront_header_cart',    60 );
	add_action( 'storefront_header', 'storefront_header_cart',    17 );
	add_action( 'storefront_header', 'storefront_product_search', 23 );

	#remove homepage title from body
	remove_action( 'storefront_homepage',       'storefront_homepage_header',      10 );

	#remove storefront default homepage widgets
	remove_action( 'homepage', 'storefront_product_categories',    20 );
	remove_action( 'homepage', 'storefront_recent_products',       30 );
	remove_action( 'homepage', 'storefront_featured_products',     40 );
	remove_action( 'homepage', 'storefront_popular_products',      50 );
	remove_action( 'homepage', 'storefront_on_sale_products',      60 );
	remove_action( 'homepage', 'storefront_best_selling_products', 70 );
};

function storefront_primary_navigation() {
	?><nav id="site-navigation" class="main-navigation" role="navigation" aria-label="<?php esc_html_e( 'Primary Navigation', 'storefront' ); ?>"
	><button class="menu-toggle" aria-controls="site-navigation" aria-expanded="false"><span><?php echo esc_attr( apply_filters( 'storefront_menu_toggle_text', __( 'Menu', 'storefront' ) ) ); ?></span></button><?php
		wp_nav_menu(
			array(
				'theme_location'	=> 'primary',
				'container_class'	=> 'primary-navigation',
#added this divider
				'before'          => '<div class="anc-nav-divider"></div>'
				)
		);

		wp_nav_menu(
			array(
				'theme_location'	=> 'handheld',
				'container_class'	=> 'handheld-navigation',
				)
		);
		?></nav><!-- #site-navigation --><?php
}

//https://wordpress.stackexchange.com/questions/107141/check-if-current-page-is-the-blog-page
add_filter('woocommerce_is_purchasable', 'anc_homepage_products_filter');
function anc_homepage_products_filter() {
	return is_front_page()||is_product()?false:true;
}
//https://docs.woocommerce.com/document/hide-loop-read-more-buttons-for-out-of-stock-items/
/*
if (!function_exists('woocommerce_template_loop_add_to_cart')) {
	function woocommerce_template_loop_add_to_cart() {
		global $product;
		if ( ! $product->is_in_stock() || ! $product->is_purchasable() ) return;
		woocommerce_get_template('loop/add-to-cart.php');
	}
}
//*///

//https://wordpress.stackexchange.com/questions/195425/display-featured-products-through-custom-loop-in-woocommerce-on-template-page
add_shortcode( 'anc_featured_products', 'anc_featured_products');
function anc_featured_products( $args ) {
	ob_start();
	if ( storefront_is_woocommerce_activated() ) {

		$args = apply_filters( 'storefront_featured_products_args', array(
			'limit'   => 8,
			'columns' => 4,
			'orderby' => 'date',
			'order'   => 'desc',
			'title'   => __( '特 色 产 品', 'storefront' ),
		) );

		$shortcode_content = storefront_do_shortcode( 'featured_products', apply_filters( 'storefront_featured_products_shortcode_args', array(
			'per_page' => intval( $args['limit'] ),
			'columns'  => intval( $args['columns'] ),
			'orderby'  => esc_attr( $args['orderby'] ),
			'order'    => esc_attr( $args['order'] ),
		) ) );

		/**
		 * Only display the section if the shortcode returns products
		 */
		if ( false !== strpos( $shortcode_content, 'product' ) ) {

			echo '<section class="storefront-product-section storefront-featured-products" aria-label="' . esc_attr__( 'Featured Products', 'storefront' ) . '">';

			do_action( 'storefront_homepage_before_featured_products' );

			echo '<h2 class="section-title">' . wp_kses_post( $args['title'] ) . '</h2>';

			do_action( 'storefront_homepage_after_featured_products_title' );

			echo $shortcode_content;

			do_action( 'storefront_homepage_after_featured_products' );

			echo '</section>';

		}
	}
	return ob_get_clean();
}

// https://docs.woocommerce.com/document/override-loop-template-and-show-quantities-next-to-add-to-cart-buttons/
add_filter( 'woocommerce_loop_add_to_cart_link', 'quantity_inputs_for_woocommerce_loop_add_to_cart_link', 10, 2 );
function quantity_inputs_for_woocommerce_loop_add_to_cart_link( $html, $product ) {
	if ( $product && $product->is_type( 'simple' ) && $product->is_purchasable() && $product->is_in_stock() && ! $product->is_sold_individually() ) {
		$html = '<form action="' . esc_url( $product->add_to_cart_url() ) . '" class="cart" method="post" enctype="multipart/form-data">';
		$html .= woocommerce_quantity_input( array(), $product, false );
		$html .= '<button type="submit" class="button alt">' . esc_html( $product->add_to_cart_text() ) . '</button>';
		$html .= '</form>';
	}
	return $html;
}

function storefront_credit() {
	?>
	<div class="site-info">
		版权所有ANC健康私人有限公司2017年
		<span class="f-right">免责声明</span>
	</div><!-- .site-info -->
	<?php
}