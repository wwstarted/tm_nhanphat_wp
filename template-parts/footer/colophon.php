<?php
/**
 * Menu footer + copyright.
 *
 * @package TMNhanPhat
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<div class="site-footer__bottom">
	<?php
	if ( has_nav_menu( 'footer' ) ) {
		wp_nav_menu( array(
			'theme_location' => 'footer',
			'menu_id'        => 'footer-menu',
			'menu_class'     => 'footer-menu',
		) );
	}
	?>
	<p class="site-footer__copyright">
		&copy; <?php echo esc_html( gmdate( 'Y' ) ); ?> <?php bloginfo( 'name' ); ?>.
		<?php esc_html_e( 'Bảo lưu mọi quyền.', 'tmnhanphat' ); ?>
	</p>
</div>
