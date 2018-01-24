<?php
// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

// BEGIN ENQUEUE PARENT ACTION
// AUTO GENERATED - Do not modify or remove comment markers above or below:

// END ENQUEUE PARENT ACTION

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

#DIRTY usage of shortcode params using LineBreaks
#one liners shortcode params will pass the associative array correctly, BUT the page editor will be harder to manage
function anc_frontpage_featured( $atts ) {
//	$atts = shortcode_atts(
//		array(
//			'urls' => '',
//			'images' => ''
//		), $atts
//	);

//	$atts['urls'] = explode(';', $atts['urls']);
//	$atts['images'] = explode(';', $atts['images']);

	//https://wordpress.stackexchange.com/questions/195425/display-featured-products-through-custom-loop-in-woocommerce-on-template-page
	$shortcode_content_featured_products = '';
	if ( storefront_is_woocommerce_activated() ) {

		$args_featured_products = apply_filters( 'storefront_featured_products_args', array(
			'limit'   => 8,
			'columns' => 4,
			'orderby' => 'date',
			'order'   => 'desc',
			'title'   => __( '特 色 产 品', 'anc' ),
		) );

		$shortcode_content_featured_products = storefront_do_shortcode( 'featured_products', apply_filters( 'storefront_featured_products_shortcode_args', array(
			'per_page' => intval( $args_featured_products['limit'] ),
			'columns'  => intval( $args_featured_products['columns'] ),
			'orderby'  => esc_attr( $args_featured_products['orderby'] ),
			'order'    => esc_attr( $args_featured_products['order'] ),
		) ) );
	}

	$product_subcategories = get_terms(['taxonomy'   => "product_cat"]);
	$blog_subcategories = get_categories();
	ob_start();
?><section class="anc-featured-pages hide-on-mobile" aria-label="特 色 板 块">
		<div class="anc-section-divider"><h2 class="section-title"><?=wp_kses_post( __( '特 色 板 块', 'anc' ) )?></h2></div>
		<ul class="featured_pages">
			<?php $l = 0;
			if(is_array($atts) )
			foreach ($atts as $featured_page) {
				$featured_page = str_replace(['<p>','</p>'], '', trim($featured_page));
				if(empty($featured_page) ){	continue;}
				$featured_page = explode(';', $featured_page);
			?><li class="feature-page">
				<div class="thumbnail" style="background-image: url(<?=$featured_page[1]?>)">
					<a href="<?=$featured_page[0]?>"><div class="caption">
						健康产品
					</div></a>
				</div>
				<div class="excerpt">
					<?=$featured_page[2]?>
					<a href="<?=$featured_page[0]?>"><span class="readmore">阅读更多 &gt;&gt;&gt;</span></a>
				</div>
			</li>
			<?php $l++;}?>
		</ul>
	</section><?php
	/**
	 * Only display the section if the featured products shortcode returns products
	 */
	if ( false !== strpos( $shortcode_content_featured_products, 'product' ) ) {
	?><section class="storefront-product-section storefront-featured-products" aria-label="<?=esc_attr__( 'Featured Products', 'storefront' )?>">
		<div class="anc-section-divider"><h2 class="section-title"><?=wp_kses_post( $args_featured_products['title'] )?></h2></div>
		<?=$shortcode_content_featured_products #uses the template yourtheme/woocommerce/content-product.php?>
		</section><?php
	}

?><section class="anc-featured-pages hide-on-desktop" aria-label="特 色 板 块">
		<div class="anc-section-divider"><h2 class="section-title"><?=wp_kses_post( __( '特 色 板 块', 'anc' ) )?></h2></div>
		<ul class="featured_pages">
			<li class="feature-page">
				<div class="thumbnail" style="background-image: url(/wp-content/uploads/2017/12/banner_beauty.jpg)">
					<div class="caption">
						产品分类
					</div>
				</div>
				<ul class="subcategories">
				<?php foreach ($product_subcategories as $subcat) {?>
					<li class="cat">
						<a href="/product-category/<?=$subcat->name?>"><?=$subcat->name?></a>
					</li>
				<?php }?>
				</ul>
			</li><li class="feature-page">
				<div class="thumbnail" style="background-image: url(/wp-content/uploads/2017/12/cinnamon.jpg)">
					<div class="caption">
						健康信息
					</div>
				</div>
				<ul class="subcategories">
				<?php foreach ($blog_subcategories as $blogcat) {
					if($blogcat->category_count > 11){continue;}
//['自然生活','营养保健','维生素','减肥']?>
					<li class="cat">
						<a href="/category/<?=$blogcat->name?>"><?=$blogcat->name?></a>
					</li>
				<?php }//*///?>
				</ul>
			</li>
		</ul>
	</section><?php
	
	return ob_get_clean();
}

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

function anc_blognshop_header()
{
	if(is_home())#BLOGS ||is_tag()
	{
		echo do_shortcode('[smartslider3 slider=3]');

		#https://www.thatweblook.co.uk/advice/tutorial-wordpress-show-content-of-static-posts-page-above-posts-list/
		global $post;
		$page_for_posts_id = get_option('page_for_posts');
		if ( $page_for_posts_id ) :
			$post = get_page($page_for_posts_id);
			setup_postdata($post);
		?><div class="col-full">
				<div id="post-<?php the_ID(); ?>" class="blognshop-intro">
					<?php the_content(); ?>
				</div>
			</div><?php
			rewind_posts();
		endif;

	}else if(is_category())#BLOGS
	{
		global $wp_query;
		$cat = $wp_query->get_queried_object();

		$banner_img = function_exists('z_taxonomy_image_url')?z_taxonomy_image_url():'';

//			$filename = get_stylesheet_directory()."/images/categories/{$cat->term_id}.jpg";
//			if(file_exists($filename)){
//				$banner_img = get_stylesheet_directory_uri()."/images/categories/{$cat->term_id}.jpg";
//			} else {
//				$banner_img = '/wp-content/uploads/2017/12/cinnamon.jpg';
//			}
		if($banner_img){?>
			<div class="category_banner" style="background-image:url(<?=$banner_img?>)">
				<img class="slope" src="<?=get_stylesheet_directory_uri().'/images/slope-white-sliderbg.png'?>" alt="" />
			</div>
			<div class="col-full">
				<div  class="blognshop-intro">
					<h1>欢迎来到博客的<?=$cat->name?>素部分。</h1>
				</div>
			</div>
		<?php }

	}else if(is_shop())
	{
		echo do_shortcode('[smartslider3 slider=2]');

		?><div class="col-full">
				<div class="blognshop-intro"><?php
					do_action( 'woocommerce_archive_description' );
			?></div>
			</div><?php

	}else if(is_product_category()||is_product_tag())
	{
		echo do_shortcode('[smartslider3 slider=2]');

		global $wp_query;
		$cat = $wp_query->get_queried_object();

		if(empty($cat->description))
		{
			$page_for_posts_id = get_option('woocommerce_shop_page_id');
			if ( $page_for_posts_id ) :
				$post = get_page($page_for_posts_id);
				setup_postdata($post);
			?><div class="col-full">
					<div id="post-<?php the_ID(); ?>" class="blognshop-intro">
						<?php the_content(); ?>
					</div>
				</div><?php
				rewind_posts();
			endif;

		}else{
		?><div class="col-full">
				<div class="blognshop-intro">
					<p><?=$cat->description ?></p>
				</div>
			</div><?php
		}	
	}
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

function anc_products_usp() {?>
<ul class="anc-prod-usp">
	<li><span>Free Shipping Orders Over $99</span></li>
	<li><span>Australian Made Vitamins</span></li>
	<li><span>60 Days Returns</span></li>
	<li><span>Free Gift With Orders Over $90</span></li>
</ul>
<?php
}

function storefront_credit() {
	?>
	<div class="site-info">
		版权所有ANC健康私人有限公司2018年
	</div><!-- .site-info -->
	<?php
}