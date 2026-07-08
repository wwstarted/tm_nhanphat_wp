<?php
/**
 * Template trang 404.
 *
 * @package TMNhanPhat
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();
?>

<main id="main" class="site-main error-404">
	<div class="container">
		<h1><?php esc_html_e( '404 — Không tìm thấy trang', 'tmnhanphat' ); ?></h1>
		<p><?php esc_html_e( 'Trang bạn tìm không tồn tại hoặc đã bị di chuyển.', 'tmnhanphat' ); ?></p>

		<?php get_search_form(); ?>
	</div>
</main>

<?php
get_footer();
