<?php
/**
 * Markup cuối <body> (site footer): Top Footer (4 cột) + Bottom Footer (copyright).
 * Nội dung chi tiết render qua template-parts/footer/* (PROJECT_RULES.md mục 4).
 * Trạng thái màu/bố cục lấy từ Customizer panel "Footer" (inc/customizer/footer-customizer.php).
 *
 * @package TMNhanPhat
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
	<footer id="colophon" class="site-footer">
		<?php
		get_template_part( 'template-parts/footer/top-footer' );
		get_template_part( 'template-parts/footer/bottom-footer' );
		?>
	</footer>

<?php wp_footer(); ?>
</body>
</html>
