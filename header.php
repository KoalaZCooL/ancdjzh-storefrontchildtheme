<?php
/**
 * The header for our theme.
 *
 * Displays all of the <head> section and everything up till <div id="content">
 *
 * @package storefront
 */

?><!doctype html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1.0, user-scalable=no">
<link rel="apple-touch-icon" sizes="180x180" href="<?=get_stylesheet_directory_uri()?>/icons/apple-touch-icon.png">
<link rel="icon" type="image/png" sizes="32x32" href="<?=get_stylesheet_directory_uri()?>/icons/favicon-32x32.png">
<link rel="icon" type="image/png" sizes="16x16" href="<?=get_stylesheet_directory_uri()?>/icons/favicon-16x16.png">
<link rel="manifest" href="<?=get_stylesheet_directory_uri()?>/icons/manifest.json">
<link rel="mask-icon" href="<?=get_stylesheet_directory_uri()?>/icons/safari-pinned-tab.svg" color="#ffffff">
<link rel="shortcut icon" href="<?=get_stylesheet_directory_uri()?>/icons/favicon.ico">
<meta name="msapplication-config" content="<?=get_stylesheet_directory_uri()?>/icons/browserconfig.xml">
<meta name="theme-color" content="#4c4c4c">
<link rel="profile" href="http://gmpg.org/xfn/11">
<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>">

<?php wp_head(); ?>
</head>

<body <?php body_class(); ?>
	><?php do_action( 'storefront_before_site' ); 
?><div id="page" class="hfeed site"
	><?php do_action( 'storefront_before_header' ); 
?><header id="masthead" class="site-header" role="banner" style="<?php storefront_header_styles(); ?>"
		><div id="masthead-tilted-bg-white"></div
		><img id="masthead-tilted-bg-slope" src="<?=get_stylesheet_directory_uri()?>/images/slope-siteheader.png"
		><div class="col-full"><span id="curr-zh">中文</span><a id="goto-en" href="http://www.ausnaturalcare.com.au/"> | 英语</a><?php
			/**
			 * Functions hooked into storefront_header action
			 *
			 * @hooked storefront_skip_links                       - 0
			 * @hooked storefront_social_icons                     - 10
			 * @hooked storefront_site_branding                    - 20
			 * @hooked storefront_secondary_navigation             - 30
			 * @hooked storefront_product_search                   - 40
			 * @hooked storefront_primary_navigation_wrapper       - 42
			 * @hooked storefront_primary_navigation               - 50
			 * @hooked storefront_header_cart                      - 60
			 * @hooked storefront_primary_navigation_wrapper_close - 68
			 */
			do_action( 'storefront_header' ); 
	?></div
	></header><!-- #masthead --><?php
	/**
	 * Functions hooked in to storefront_before_content
	 *
	 * @hooked storefront_header_widget_region - 10
	 */
	do_action( 'storefront_before_content' ); 
?><div id="content" class="site-content" tabindex="-1">
		<div class="col-full"><?php
		/**
		 * Functions hooked in to storefront_content_top
		 *
		 * @hooked woocommerce_breadcrumb - 10
		 */
		do_action( 'storefront_content_top' );
