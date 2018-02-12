<?php
function admin_featured_pages(){
	if(!empty($_POST['opts'])){
		$resp = update_option( 'anc_frontpage_featured_pages', $_POST['opts'] );
	}
	$opts = get_option('anc_frontpage_featured_pages');
	ob_start();
	require_once( get_stylesheet_directory() .'/inc/frontpage/admin_tpl_manage_featured_pages.php');
	return ob_get_flush();
}
