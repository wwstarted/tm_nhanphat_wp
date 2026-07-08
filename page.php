<?php
/**
 * Template mặc định cho page thường (không có Page Template riêng).
 *
 * @package TMNhanPhat
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();
?>

<main id="main" class="site-main page">
	<div class="container">
		<?php get_template_part( 'template-parts/components/breadcrumb' ); ?>

		<?php
		while ( have_posts() ) :
			the_post();
			get_template_part( 'template-parts/page/content' );

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
