<?php
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
		<?php }//欢迎来到博客的----素部分。?>
			<div class="col-full">
				<div  class="blognshop-intro">
					<h1><?=$cat->name?></h1>
					<p><?=$cat->description?></p>
				</div>
			</div>
		<?php
	}else if(is_shop() ) //||is_product_tag()
	{
		echo do_shortcode('[smartslider3 slider=2]');

		?><div class="col-full">
				<div class="blognshop-intro"><?php
					do_action( 'woocommerce_archive_description' );
			?></div>
			</div><?php

	}else if(is_product_category() )
	{
		global $wp_query;
		$cat = $wp_query->get_queried_object();

		$banner_img = function_exists('z_taxonomy_image_url')?z_taxonomy_image_url():'';

		if($banner_img){?>
			<div class="category_banner" style="background-image:url(<?=$banner_img?>)">
				<img class="slope" src="<?=get_stylesheet_directory_uri().'/images/slope-white-sliderbg.png'?>" alt="" />
			</div>
		<?php }//欢迎来到博客的----素部分。?>
			<div class="col-full">
				<div  class="blognshop-intro">
					<h1><?=$cat->name?></h1>
					<p><?=$cat->description?></p>
				</div>
			</div>
		<?php
	}
}