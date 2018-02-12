<?php
add_action('admin_menu','add_anc_admin_menu',1);

function add_anc_admin_menu(){
	add_submenu_page(
		'edit.php?post_type=page',
		'ANC Frontpage',
		'ANC Frontpage',
		'edit_pages',
		'anc-manage-frontpage',
		'admin_featured_pages'
	);
	add_submenu_page(
		'edit.php?post_type=product',
		'ANC USP',
		'ANC USP',
		'edit_posts',
		'anc-manage-usp',
		'admin_product_usp'
	);
}

include_once( get_stylesheet_directory() .'/inc/frontpage/admin_featured_pages.php');
include_once( get_stylesheet_directory() .'/inc/product/admin_product_usp.php');