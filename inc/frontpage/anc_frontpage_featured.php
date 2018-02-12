<?php
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