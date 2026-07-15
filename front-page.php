<?php
/**
 * Trang chủ tĩnh (Settings → Reading → A static page). Nội dung chia theo section
 * trong template-parts/home/* để front-page.php luôn ngắn gọn (PROJECT_RULES.md mục 4).
 *
 * @package TMNhanPhat
 */

if (!defined('ABSPATH')) {
	exit;
}

get_header();
?>

<main id="main" class="site-main front-page">
	<?php
	// Render động theo Homepage Section Manager (Customize → Homepage Sections): bật/tắt +
	// thứ tự. Mặc định = đủ 12 section, đúng thứ tự cũ nên không đổi gì (PROJECT_RULES.md).
	foreach ( tmnhanphat_get_ordered_home_sections() as $tmnhanphat_home_section ) {
		get_template_part( $tmnhanphat_home_section['template'] );
	}
	?>
</main>

<?php
get_footer();