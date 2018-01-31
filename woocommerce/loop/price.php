<?php
/**
 * Loop Price
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/loop/price.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see 	    https://docs.woocommerce.com/document/template-structure/
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     1.6.4
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $product;
if ( $price_html = $product->get_price_html() ) : 

	#front page featured products loop
	if(is_front_page() ){
	?><div class="anc-frontpage-featuredproducts-price"><h2 class="price-prefix">价格</h2> <span class="price"><?=$price_html?></span><h2>/瓶</h2></div><?php

	#default product listings loop 在线零售价格
	}else{
	?><span class="price">价格 <?=$price_html?></span><?php	
	}
endif; ?>
