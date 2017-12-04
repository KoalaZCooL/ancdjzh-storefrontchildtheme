<?php
/**
 * Template used to display post content.
 *
 * @package storefront
 */

?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?> >
	<div>
		<div class="thumbnail-image">
			<?php do_action( 'storefront_post_content_before' );?>
		</div>
		
		<div>
			<div>
				<?php
				if ( is_single() ) {
					the_title( '<span class="entry-title">', '</span>' );
				} else {
					the_title( sprintf( '<span class="alpha entry-title"><a href="%s" rel="bookmark">', esc_url( get_permalink() ) ), '</a></span>' );
				}
				echo get_the_excerpt();
				?>
			</div>
			<div class="author">
				<?php if ( 'post' == get_post_type() ) {?>
					<div class="label"><?= esc_attr( __( 'Written by ', 'storefront' ) ); the_author_posts_link(); ?> <?php storefront_posted_on();?>
					<?php 
				}
				wp_link_pages( array(
					'before' => '<div class="page-links">' . __( 'Pages:', 'storefront' ),
					'after'  => '</div>',
				) );
				?></div>
			</div>
		</div>
	</div>
<?php
//echo get_avatar( get_the_author_meta( 'ID' ), 128 );
//$categories_list = get_the_category_list( __( ', ', 'storefront' ) );
//echo wp_kses_post( $categories_list );
//$tags_list = get_the_tag_list( '', __( ', ', 'storefront' ) );
//echo wp_kses_post( $tags_list );
?>
</article><!-- #post-## -->
