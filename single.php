<?php
/**
 * Template bài viết đơn — chia nhỏ theo template-parts/single/* (PROJECT_RULES.md mục 4).
 *
 * @package TMNhanPhat
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();
?>

<main id="main" class="site-main single">
	<div class="container">
		<?php get_template_part( 'template-parts/components/breadcrumb' ); ?>

		<?php
		while ( have_posts() ) :
			the_post();

			get_template_part( 'template-parts/single/toc' );
			get_template_part( 'template-parts/single/content' );
			get_template_part( 'template-parts/single/share' );
			get_template_part( 'template-parts/single/author-box' );
			get_template_part( 'template-parts/single/related-posts' );

			if ( comments_open() || get_comments_number() ) {
				comments_template();
			}
		endwhile;
		?>
	</div>
</main>

<?php
get_sidebar();
get_footer();
