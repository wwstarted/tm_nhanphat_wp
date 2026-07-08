<?php
/**
 * Primary Navigation Menu + hamburger/offcanvas cho tablet/mobile — component độc lập
 * (PROJECT_RULES.md mục 5). Logic JS ở assets/js/layout/mobile-menu.js.
 *
 * Cùng 1 <ul id="primary-menu"> được dùng cho cả 2 layout (inline desktop / offcanvas
 * mobile) — chỉ khác CSS theo Mobile Breakpoint (Customizer), tránh render trùng menu 2 lần.
 *
 * @package TMNhanPhat
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$tmnhanphat_breakpoint = absint( tmnhanphat_get_header_mod( 'tmnhanphat_header_mobile_breakpoint' ) );
?>
<nav
	id="site-navigation"
	class="main-navigation"
	aria-label="<?php esc_attr_e( 'Menu chính', 'tmnhanphat' ); ?>"
	data-breakpoint="<?php echo esc_attr( $tmnhanphat_breakpoint ); ?>"
>
	<button
		type="button"
		class="main-navigation__toggle"
		aria-controls="primary-menu"
		aria-expanded="false"
		aria-label="<?php esc_attr_e( 'Bật/tắt menu', 'tmnhanphat' ); ?>"
	>
		<span class="main-navigation__toggle-bar"></span>
		<span class="main-navigation__toggle-bar"></span>
		<span class="main-navigation__toggle-bar"></span>
	</button>

	<div class="main-navigation__overlay"></div>

	<div class="main-navigation__panel">
		<button type="button" class="main-navigation__panel-close" aria-label="<?php esc_attr_e( 'Đóng menu', 'tmnhanphat' ); ?>">
			<span aria-hidden="true">&times;</span>
		</button>

		<?php
		wp_nav_menu( array(
			'theme_location' => 'primary',
			'menu_id'        => 'primary-menu',
			'menu_class'     => 'primary-menu',
			'container'      => false,
			'fallback_cb'    => 'tmnhanphat_primary_menu_fallback',
		) );
		?>
	</div>
</nav>
