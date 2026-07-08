<?php
/**
 * Markup cuối <body> (site footer) — nội dung chi tiết render qua template-parts/footer/*.
 *
 * @package TMNhanPhat
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
	<footer id="colophon" class="site-footer">
		<?php
		get_template_part( 'template-parts/footer/widgets' );
		get_template_part( 'template-parts/footer/colophon' );
		?>
	</footer>

<?php wp_footer(); ?>
</body>
</html>
