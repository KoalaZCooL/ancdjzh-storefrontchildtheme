<?php
/**
 * The template for displaying the footer.
 *
 * Contains the closing of the #content div and all content after
 *
 * @package storefront
 */
?>
			<div class="anc_footer_social">
				<div class="anc_social_qrs">
					<div class="qr_container">
						<img src="<?=get_stylesheet_directory_uri()?>/images/weixin_anc.jpg" />
						<img src="<?=get_stylesheet_directory_uri()?>/images/weixin.jpg" />
					</div>
					<div class="qr_container">
						<img src="<?=get_stylesheet_directory_uri()?>/images/weibo.jpg" />
					</div>
				</div>
				<div class="anc_subscribe">
				<?php
				if(function_exists('es_subbox') ){
					es_subbox($namefield = "NO", $desc = "订阅我们的新闻", $group = "Public"); 
				}?>
				</div>
			</div>
		</div><!-- .col-full -->
	</div><!-- #content -->

	<?php do_action( 'storefront_before_footer' ); ?>

	<footer id="colophon" class="site-footer" role="contentinfo">
		<div class="col-full">

			<?php
			/**
			 * Functions hooked in to storefront_footer action
			 *
			 * @hooked storefront_footer_widgets - 10
			 * @hooked storefront_credit         - 20
			 */
			do_action( 'storefront_footer' ); ?>

		</div><!-- .col-full -->
	</footer><!-- #colophon -->

	<?php do_action( 'storefront_after_footer' ); ?>

</div><!-- #page -->

<?php wp_footer(); ?>
<script>
	jQuery(function() {
		<?php if(is_category()){?>
		jQuery('#ui-id-3').click();
		<?php }?>
	});
</script>
</body>
</html>
