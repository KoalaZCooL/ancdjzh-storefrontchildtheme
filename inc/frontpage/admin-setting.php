<?php
add_action('admin_menu','add_anc_frontpage_setup_menu',1);

function add_anc_frontpage_setup_menu(){
	add_submenu_page(
		'edit.php?post_type=page',
		'ANC Frontpage',
		'ANC Frontpage',
		'edit_pages',
		'anc-manage-frontpage',
		'admin_featured_pages'
	);
}

include_once( get_stylesheet_directory() .'/inc/frontpage/admin_featured_pages.php');