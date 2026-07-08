<?php
/**
 * Trang chủ tĩnh (Settings → Reading → A static page). Nội dung chia theo section
 * trong template-parts/home/* để front-page.php luôn ngắn gọn (PROJECT_RULES.md mục 4).
 *
 * @package TMNhanPhat
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();
?>

<main id="main" class="site-main front-page">
	<?php
	get_template_part( 'template-parts/home/hero' );
	get_template_part( 'template-parts/home/latest-posts' );

	while ( have_posts() ) :
		the_post();
		the_content();
	endwhile;
	?>
</main>

<?php
get_footer();
