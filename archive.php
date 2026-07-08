<?php
/**
 * Template cho category/tag/author/date archive (fallback tự động theo Template Hierarchy
 * khi không có category.php/tag.php/author.php riêng — xem PROJECT_RULES.md mục 3).
 *
 * @package TMNhanPhat
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();
?>

<main id="main" class="site-main archive">
	<div class="container">
		<?php get_template_part( 'template-parts/components/breadcrumb' ); ?>

		<header class="archive-header">
			<h1 class="archive-title"><?php the_archive_title(); ?></h1>
			<?php the_archive_description( '<div class="archive-description">', '</div>' ); ?>
		</header>

		<?php if ( have_posts() ) : ?>
			<div class="post-grid">
				<?php
				while ( have_posts() ) :
					the_post();
					get_template_part( 'template-parts/archive/content' );
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
