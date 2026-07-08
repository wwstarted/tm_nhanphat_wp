<?php
/**
 * Template trang kết quả tìm kiếm.
 *
 * @package TMNhanPhat
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();
?>

<main id="main" class="site-main search-results">
	<div class="container">
		<?php get_template_part( 'template-parts/components/breadcrumb' ); ?>

		<header class="archive-header">
			<h1 class="archive-title">
				<?php
				/* translators: %s: search query */
				printf( esc_html__( 'Kết quả tìm kiếm cho: %s', 'tmnhanphat' ), '<span>' . esc_html( get_search_query() ) . '</span>' );
				?>
			</h1>
		</header>

		<?php if ( have_posts() ) : ?>
			<div class="post-grid">
				<?php
				while ( have_posts() ) :
					the_post();
					get_template_part( 'template-parts/search/content' );
				endwhile;
				?>
			</div>

			<?php get_template_part( 'template-parts/components/pagination' ); ?>
		<?php else : ?>
			<?php get_template_part( 'template-parts/components/nothing-found' ); ?>
		<?php endif; ?>
	</div>
</main>

<?php
get_sidebar();
get_footer();
