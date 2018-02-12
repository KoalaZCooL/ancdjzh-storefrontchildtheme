<?php
// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

// BEGIN ENQUEUE PARENT ACTION
// AUTO GENERATED - Do not modify or remove comment markers above or below:

// END ENQUEUE PARENT ACTION

require_once ( get_stylesheet_directory()  . '/inc/anc_blognshop_header.php' );
require_once ( get_stylesheet_directory()  . '/inc/admin-setting.php' );
require_once ( get_stylesheet_directory()  . '/inc/frontpage/anc_frontpage_featured.php' );

add_action( 'init', 'override_actions_parent_theme');
function override_actions_parent_theme() {

	remove_action( 'storefront_header', 'storefront_skip_links', 0 );

	#reposition nav-widgets
	remove_action( 'storefront_header', 'storefront_product_search', 40 );
	remove_action( 'storefront_header', 'storefront_header_cart',    60 );
	add_action( 'storefront_header', 'storefront_header_cart',    17 );
	add_action( 'storefront_header', 'storefront_product_search', 23 );

	#remove homepage title from body
	remove_action( 'storefront_homepage', 'storefront_homepage_header', 10 );

	#remove storefront default homepage widgets
	remove_action( 'homepage', 'storefront_product_categories',    20 );
	remove_action( 'homepage', 'storefront_recent_products',       30 );
	remove_action( 'homepage', 'storefront_featured_products',     40 );
	remove_action( 'homepage', 'storefront_popular_products',      50 );
	remove_action( 'homepage', 'storefront_on_sale_products',      60 );
	remove_action( 'homepage', 'storefront_best_selling_products', 70 );

	add_action( 'storefront_before_content', 'anc_blognshop_header', 9);

	#PRODUCT-SINGLE PAGE
	remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_excerpt', 20 );
	add_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_excerpt', 7 );
	add_action( 'woocommerce_single_product_summary', 'anc_singleproduct_sellingpoints', 8 );
	add_action( 'woocommerce_single_product_summary', 'anc_products_usp', 51 );
};
add_shortcode( 'anc_frontpage_featured', 'anc_frontpage_featured');
add_filter('woocommerce_is_purchasable', 'anc_homepage_products_filter');
add_filter( 'woocommerce_loop_add_to_cart_link', 'quantity_inputs_for_woocommerce_loop_add_to_cart_link', 10, 2 );
add_shortcode( 'anc_frontpage_latest_articles', 'anc_frontpage_latest_articles');

function custom_excerpt_more( $more ) {
//	global $post;get_permalink().
	return '<a href="'.get_permalink().'"><span class="readmore">全文阅读</span></a>';
}
add_filter( 'excerpt_more', 'custom_excerpt_more' );

function search_autocomplete() {?>
		<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
		<script src="//code.jquery.com/jquery-1.12.4.js"></script>
		<script src="//code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
		<script>
			jQuery(function(){
				var availableTags = [
			<?php
					// Get All Products 
					$args = array(
						'post_type'     =>  'product', // Get Product Post Type
						'numberposts'   =>  -1,        // Display all Products
						'order'         =>  'ASC'      // Order By Ascend
					);

					// Get All Products & data
					$selectableOptions = get_posts( $args );

					// Loop though each product and display their titles.
					foreach ($selectableOptions as $key => $val)
					{
						$products = '"' . sanitize_text_field( $val->post_title ) . '",';
						echo $products;
					}
			?>
				];
				jQuery( "#woocommerce-product-search-field-0" ).autocomplete({
					source: availableTags,
					appendTo: ".site-search",
					minLength: 2
				});
				jQuery( "#woocommerce-product-search-field-1" ).autocomplete({
					source: availableTags,
					appendTo: ".storefront-handheld-footer-bar",
					position: { my: "left bottom", at: "left top" },
					minLength: 2
				});
			});
		</script>
	<?php
}
add_action('wp_footer', 'search_autocomplete');

function storefront_primary_navigation() {
	?><nav id="site-navigation" class="main-navigation" role="navigation" aria-label="<?php esc_html_e( 'Primary Navigation', 'storefront' ); ?>"
	><button class="menu-toggle" aria-controls="site-navigation" aria-expanded="false"><span><?=esc_attr( apply_filters( 'storefront_menu_toggle_text', __( '菜单', 'storefront' ) ) )?></span></button><?php
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

function storefront_handheld_footer_bar_account_link() {
	echo '<a></a>';//do_shortcode('[responsive_menu]').
}

//https://wordpress.stackexchange.com/questions/107141/check-if-current-page-is-the-blog-page
function anc_homepage_products_filter() {
	return is_front_page()?false:true;
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

function anc_frontpage_latest_articles( $args ) {
	$qry = array(
		'numberposts' => 3,
		'offset' => 0,
		'category' => 0,
		'orderby' => 'post_date',
		'order' => 'DESC',
		'include' => '',
		'exclude' => '',
		'meta_key' => '',
		'meta_value' =>'',
		'post_type' => 'post',
		'post_status' => 'publish',
		'suppress_filters' => true
	);

	$recent_posts = wp_get_recent_posts( $qry, ARRAY_A );
	ob_start();
?><section class="anc-featured-pages hide-on-mobile" aria-label="健 康 信 息">
		<div class="anc-section-divider"><h2 class="section-title"><?=wp_kses_post( __( '健 康 信 息', 'anc' ) )?></h2></div>
		<ul class="featured_pages"><?php
		foreach ($recent_posts as $v) {
			$post_thumbnail_id = get_post_thumbnail_id( $v['ID'] );
			$readmore = '<a href="'.$v['guid'].'"><span class="readmore">阅读更多 &gt;&gt;&gt;</span></a>';
		?><li class="feature-page">
				<a href="<?=$v['guid']?>"><div class="article thumbnail" <?php if($post_thumbnail_id){?>style="background-image: url(<?=get_the_post_thumbnail_url($v['ID'])?>)"<?php }?> >
					<div class="goto">
						<img src="<?=get_stylesheet_directory_uri()?>/images/petals_halftop.png">
						全文阅读
					</div>
				</div></a>
				<div class="excerpt"><?=wp_kses_post( wp_trim_words( $v['post_content'], 55, $readmore ) )?>
				</div>
			</li><?php
		}
?></ul></section><?php
	return ob_get_clean();
}

// https://docs.woocommerce.com/document/override-loop-template-and-show-quantities-next-to-add-to-cart-buttons/
function quantity_inputs_for_woocommerce_loop_add_to_cart_link( $html, $product ) {
	if ( $product && $product->is_type( 'simple' ) && $product->is_purchasable() && $product->is_in_stock() && ! $product->is_sold_individually() ) {
		$html = '<form action="' . esc_url( $product->add_to_cart_url() ) . '" class="cart" method="post" enctype="multipart/form-data">';
		$html .= woocommerce_quantity_input( array(), $product, false );
		$html .= '<button type="submit" class="button alt">' . esc_html( $product->add_to_cart_text() ) . '</button>';
		$html .= '</form>';
	}
	return $html;
}

function anc_singleproduct_sellingpoints() {
	$product = $GLOBALS['product'];
	$attributes = $product->get_attributes();

	$sellingpoints = array();
	foreach ( $attributes as $attribute ) :
			$specname = wc_attribute_label( $attribute->get_name() );
			$attribute_value = $product->get_attribute($specname);

			switch ($specname) {
				case 'sp1':
				case 'sp2':
				case 'sp3':
					if($attribute_value){
						$sellingpoints[$specname] = $attribute_value;
					}
					break;

				default:
					break;
			}
	endforeach;

	echo '<ul class="sellingpoints">';
	foreach ($sellingpoints as $v) {
		echo '<li>'.$v.'</li>';
	}
	echo '</ul>';
}

function storefront_paging_nav() {
	global $wp_query;

	$args = array(
		'type' 	    => 'list',
		'next_text' => _x( '&nbsp;', 'Next post', 'storefront' ),
		'prev_text' => _x( '&nbsp;', 'Previous post', 'storefront' ),
		'end_size'     => 1,
		'mid_size'     => 1
		);

	the_posts_pagination( $args );
}

function anc_products_usp() {
	$opts = get_option('anc_product_usp');?>
<ul class="anc-prod-usp">
	<?php
	foreach ($opts as $usp) {?>
	<li><span><?=$usp?></span></li>
	<?php }?>
</ul>
<?php
}

add_filter('wp_head',function(){
	global $WOOCS;
	if (!session_id()){
		@session_start();
	}
	if(!empty($_SESSION['anc_checkout_was_paypal'])){
		$WOOCS->set_currency('CNY');
		unset($_SESSION['anc_checkout_was_paypal']);
	}
});

#https://docs.woocommerce.com/document/hide-other-shipping-methods-when-free-shipping-is-available/
#may need to check coupons settings for conflict in freeshipping
add_filter( 'woocommerce_package_rates', 'my_hide_shipping_when_free_is_available', 100 );
function my_hide_shipping_when_free_is_available( $rates ) {
	$free = array();
	foreach ( $rates as $rate_id => $rate ) {
		if ( 'free_shipping' === $rate->method_id ) {
			$free[ $rate_id ] = $rate;
			break;
		}
	}
	return ! empty( $free ) ? $free : $rates;
}

add_action('woocommerce_checkout_process', 'anc_process_checkout');
function anc_process_checkout()
{
	#send AUD amount to paypal
	if(strpos(strtolower($_POST['payment_method']), 'paypal')!==false){
		if (!session_id()){
			@session_start();
		}
		global $WOOCS;
		$WOOCS->set_currency('AUD');
		$_SESSION['anc_checkout_was_paypal'] = true;
	}
}

#https://docs.woocommerce.com/document/tutorial-customising-checkout-fields-using-actions-and-filters/
add_action( 'woocommerce_checkout_update_order_meta', 'anc_checkout_field_update_order_meta' );
function anc_checkout_field_update_order_meta( $order_id )
{
	#save order exchange rate in orders meta
	global $WOOCS;
	update_post_meta( $order_id, 'woocs_exchange_rate', serialize($WOOCS->get_currencies()) );

	#save coupons used as meta
	$coupons = [];
	// Coupons used in the order LOOP (as they can be multiple)
	foreach( wc_get_order($order_id)->get_used_coupons() as $coupon_name ){

		// Retrieving the coupon ID
		$coupon_post_obj = get_page_by_title($coupon_name, OBJECT, 'shop_coupon');
		$coupon_id = $coupon_post_obj->ID;

		// Get an instance of WC_Coupon object in an array(necesary to use WC_Coupon methods)
		$coupons_obj = new WC_Coupon($coupon_id);
		$coupons[$coupon_name]['type'] = $coupons_obj->get_discount_type();
		$coupons[$coupon_name]['amount'] = $coupons_obj->get_amount();
		$coupons[$coupon_name]['products'] = $coupons_obj->get_product_ids();
	}
	if($coupons){
		update_post_meta( $order_id, 'anc_used_coupons', serialize($coupons) );
	}
}

#add custom fields on checkout page, custom $fields will automatically be saved as orders-meta
add_filter( 'woocommerce_checkout_fields' , 'anc_override_checkout_fields' );
function anc_override_checkout_fields( $fields ) {
	$fields['shipping']['shipping_phone'] = array(
		'label'				=> __('Phone', 'woocommerce'),
		'placeholder'	=> _x('Phone', 'placeholder', 'woocommerce'),
		'required'		=> false,
		'class'				=> array('form-row-wide'),
		'clear'				=> true
	);
	return $fields;
}
#Display field value on the order edit page
add_action( 'woocommerce_admin_order_data_after_shipping_address', 'anc_checkout_field_display_admin_order_meta', 10, 1 );
function anc_checkout_field_display_admin_order_meta($order){
	echo '<p><strong>'.__('Phone From Checkout Form').':</strong> ' . get_post_meta( $order->get_id(), '_shipping_phone', true ) . '</p>';
}

function storefront_credit() {
	?>
	<div class="site-info">
		版权所有ANC健康私人有限公司2018年
	</div><!-- .site-info -->
	<?php
}