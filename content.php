<?php
/**
 * Template used to display post content.
 *
 * @package storefront
 */
$has_thumbnail = has_post_thumbnail();
?>
<article id="post-<?php the_ID(); ?>" <?php post_class(); ?> >
	<div class="posts-wrapper">
		<a href="<?=get_permalink()?>"
		><div class="thumbnail-image" >
			<?php if ( $has_thumbnail ) {
				the_post_thumbnail( 'thumbnail' );
			}else{?>
				<img width="150" height="150" src="<?=get_stylesheet_directory_uri()?>/images/petals_full.png" class="attachment-thumbnail size-thumbnail wp-post-image">
			<?php }?>
			<div class="caption">
				<div>全文阅读</div>
			</div>
		</div
		></a>
		
		<div class="summary-wrapper">
			<?php if ( is_single() ) {
				the_title( '<span class="entry-title">', '</span>' );
			} else {
				the_title( sprintf( '<span class="alpha entry-title"><a href="%s" rel="bookmark">', esc_url( get_permalink() ) ), '</a></span>' );
			}?>
			<div class="excerpt">
				<?=get_the_excerpt()?>
			</div>
			<?php if ( 'post' == get_post_type() ) {?>
			<div class="author">
				<div class="label">
				<?= esc_attr( __( '作者: ', 'storefront' ) ); the_author_posts_link(); ?> <?php storefront_posted_on();?>
				</div>
			</div>
			<?php }?>
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
