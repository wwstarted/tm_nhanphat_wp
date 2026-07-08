<?php
/**
 * Fallback template bắt buộc theo Template Hierarchy — chỉ dùng khi không có
 * template cụ thể nào khác khớp (front-page/single/page/archive/... đều có file riêng).
 *
 * @package TMNhanPhat
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();
?>

<main id="main" class="site-main">
	<div class="container">
		<?php if ( have_posts() ) : ?>
			<?php
			while ( have_posts() ) :
				the_post();
				get_template_part( 'template-parts/archive/content' );
			endwhile;

			get_template_part( 'template-parts/components/pagination' );
			?>
		<?php else : ?>
			<?php get_template_part( 'template-parts/components/nothing-found' ); ?>
		<?php endif; ?>
	</div>
</main>

<?php
get_sidebar();
get_footer();
