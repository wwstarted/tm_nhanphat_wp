<?php
/**
 * Bài viết liên quan (cùng category), tái sử dụng component post-card.
 *
 * @package TMNhanPhat
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$tmnhanphat_categories = wp_get_post_categories( get_the_ID() );

if ( empty( $tmnhanphat_categories ) ) {
	return;
}

$tmnhanphat_related = new WP_Query( array(
	'post_type'           => 'post',
	'posts_per_page'      => 3,
	'post__not_in'        => array( get_the_ID() ),
	'category__in'        => $tmnhanphat_categories,
	'ignore_sticky_posts' => true,
) );

if ( ! $tmnhanphat_related->have_posts() ) {
	return;
}
?>
<section class="related-posts">
	<h2 class="section-title"><?php esc_html_e( 'Bài viết liên quan', 'tmnhanphat' ); ?></h2>

	<div class="post-grid">
		<?php
		while ( $tmnhanphat_related->have_posts() ) :
			$tmnhanphat_related->the_post();
			get_template_part( 'template-parts/components/post-card' );
		endwhile;
		?>
	</div>
</section>
<?php
wp_reset_postdata();
