<?php
/**
 * Khối "Bài viết mới nhất" trên trang chủ — dùng lại component post-card
 * (không copy HTML giữa các template — PROJECT_RULES.md mục 5).
 *
 * @package TMNhanPhat
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$tmnhanphat_latest = new WP_Query( array(
	'post_type'           => 'post',
	'posts_per_page'      => 6,
	'ignore_sticky_posts' => true,
) );

if ( ! $tmnhanphat_latest->have_posts() ) {
	return;
}
?>
<section class="latest-posts">
	<div class="container">
		<h2 class="section-title"><?php esc_html_e( 'Bài viết mới nhất', 'tmnhanphat' ); ?></h2>

		<div class="post-grid">
			<?php
			while ( $tmnhanphat_latest->have_posts() ) :
				$tmnhanphat_latest->the_post();
				get_template_part( 'template-parts/components/post-card' );
			endwhile;
			?>
		</div>
	</div>
</section>
<?php
wp_reset_postdata();
