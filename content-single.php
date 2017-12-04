<?php
/**
 * Template used to display post content on single pages.
 *
 * @package storefront
 */

?>

<div id="post-<?php the_ID(); ?>" <?php post_class(); ?> >
	<div>
		<?php
		if ( is_single() ) {
			the_title( '<h1 class="entry-title">', '</h1>' );
		} else {
			the_title( sprintf( '<h2 class="alpha entry-title"><a href="%s" rel="bookmark">', esc_url( get_permalink() ) ), '</a></h2>' );
		}
		?>
	</div>
	<div>
		<div class="author">
			<?php if ( 'post' == get_post_type() ) {?>
			<div class="label"><?= esc_attr( __( 'Written by ', 'storefront' ) ); the_author_posts_link(); ?> <?php storefront_posted_on();
			}?>
			</div>
		</div>
	</div>
	<div>
		<div class="thumbnail-image">
			<?php do_action( 'storefront_post_content_before' );?>
		</div>
		<?php the_content();?>
	</div>
	<?php
	/**
	 * Functions hooked in to storefront_single_post_bottom action
	 *
	 * @hooked storefront_post_nav         - 10
	 * @hooked storefront_display_comments - 20
	 */
	do_action( 'storefront_single_post_bottom' );
	?>

</div><!-- #post-## -->
