<?php
/**
 * Bottom Footer: thanh copyright căn giữa, nền đỏ thương hiệu — xem PROJECT_RULES.md mục 5.
 *
 * @package TMNhanPhat
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<div class="footer-bottom">
	<p class="footer-bottom__copyright">
		<?php echo esc_html( tmnhanphat_get_footer_mod( 'tmnhanphat_footer_copyright' ) ); ?>
	</p>
</div>
