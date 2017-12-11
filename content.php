<?php
/**
 * Template used to display post content.
 *
 * @package storefront
 */
$has_thumbnail = has_post_thumbnail();
?>
<article id="post-<?php the_ID(); ?>" <?php post_class(); ?> >
	<div>
		<a target="_blank" href="<?=get_permalink()?>"
		><div class="thumbnail-image <?= $has_thumbnail?:'no-img';?>">
			<?php if ( $has_thumbnail ) {
				the_post_thumbnail( 'full' );
			}?>
			<div class="caption">
				全文阅读
			</div>
		</div
		></a>
		
		<div class="summary-wrapper <?= $has_thumbnail?:'no-img';?>">
			<?php if ( is_single() ) {
				the_title( '<span class="entry-title">', '</span>' );
			} else {
				the_title( sprintf( '<span class="alpha entry-title"><a href="%s" rel="bookmark">', esc_url( get_permalink() ) ), '</a></span>' );
			}?>
			<div class="excerpt">
				<?=get_the_excerpt()?>
			</div>
			<div class="author">
				<?php if ( 'post' == get_post_type() ) {?>
					<div class="label"><?= esc_attr( __( '作者: ', 'storefront' ) ); the_author_posts_link(); ?> <?php storefront_posted_on();
					?><a target="_blank" href="<?=get_permalink()?>"><span class="f-right">全文阅读</span></a><?php
				?></div><?php }?>
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
