<?php
/**
 * Menu chính + nút toggle mobile menu — component độc lập (PROJECT_RULES.md mục 5).
 * Logic JS toggle nằm ở assets/src/js/layout/mobile-menu.js.
 *
 * @package TMNhanPhat
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<nav id="site-navigation" class="main-navigation" aria-label="<?php esc_attr_e( 'Menu chính', 'tmnhanphat' ); ?>">
	<button
		class="mobile-menu-toggle"
		aria-controls="primary-menu"
		aria-expanded="false"
		aria-label="<?php esc_attr_e( 'Bật/tắt menu', 'tmnhanphat' ); ?>"
	>
		<span class="mobile-menu-toggle__bar"></span>
		<span class="mobile-menu-toggle__bar"></span>
		<span class="mobile-menu-toggle__bar"></span>
	</button>

	<?php
	wp_nav_menu( array(
		'theme_location' => 'primary',
		'menu_id'        => 'primary-menu',
		'menu_class'     => 'primary-menu',
	) );
	?>
</nav>
