<?php
function admin_product_usp(){
	if(!empty($_POST['opts'])){
		$resp = update_option( 'anc_product_usp', $_POST['opts'] );
	}
	$opts = get_option('anc_product_usp');
	ob_start();
	require_once( get_stylesheet_directory() .'/inc/product/admin_tpl_manage_product_usp.php');
	return ob_get_flush();
}
