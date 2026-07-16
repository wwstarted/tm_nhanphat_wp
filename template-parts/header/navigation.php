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
		<?php /* Head offcanvas (chỉ hiện ở mobile/tablet — desktop ẩn qua media động):
		Logo trái + nút đóng phải. Logo tái sử dụng Header Logo mod. */ ?>
		<div class="main-navigation__panel-head">
			<a class="main-navigation__panel-logo" href="<?php echo esc_url( home_url( '/' ) ); ?>">
				<?php $tmnhanphat_panel_logo = tmnhanphat_get_header_mod( 'tmnhanphat_header_logo' ); ?>
				<?php if ( $tmnhanphat_panel_logo ) : ?>
					<img src="<?php echo esc_url( $tmnhanphat_panel_logo ); ?>" alt="<?php echo esc_attr( get_bloginfo( 'name' ) ); ?>" loading="lazy" />
				<?php else : ?>
					<span class="main-navigation__panel-title"><?php bloginfo( 'name' ); ?></span>
				<?php endif; ?>
			</a>

			<button type="button" class="main-navigation__panel-close" aria-label="<?php esc_attr_e( 'Đóng menu', 'tmnhanphat' ); ?>">
				<span aria-hidden="true">&times;</span>
			</button>
		</div>

		<?php
		wp_nav_menu( array(
			'theme_location' => 'primary',
			'menu_id'        => 'primary-menu',
			'menu_class'     => 'primary-menu',
			'container'      => false,
			'fallback_cb'    => 'tmnhanphat_primary_menu_fallback',
		) );
		?>

		<?php /* Đáy offcanvas: Social icons tái sử dụng dữ liệu + style .footer-social
		(footer.css load mọi trang) — DRY, không copy SVG (PROJECT_RULES.md mục 5). */ ?>
		<div class="main-navigation__panel-footer">
			<?php if ( tmnhanphat_get_footer_mod( 'tmnhanphat_footer_social_enable' ) ) : ?>
				<ul class="footer-social" aria-label="<?php esc_attr_e( 'Mạng xã hội', 'tmnhanphat' ); ?>">
					<?php foreach ( tmnhanphat_get_footer_social_networks() as $tmnhanphat_network ) : ?>
						<?php
						$tmnhanphat_social_url = tmnhanphat_get_footer_mod( $tmnhanphat_network['mod'] );
						if ( ! $tmnhanphat_social_url ) {
							continue;
						}
						?>
						<li>
							<a
								href="<?php echo esc_url( $tmnhanphat_social_url ); ?>"
								class="footer-social__link"
								target="_blank"
								rel="noopener noreferrer"
								aria-label="<?php echo esc_attr( $tmnhanphat_network['label'] ); ?>"
							><?php echo $tmnhanphat_network['icon']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- SVG tĩnh định nghĩa sẵn trong inc/template-functions.php. ?></a>
						</li>
					<?php endforeach; ?>
				</ul>
			<?php endif; ?>
		</div>
	</div>
</nav>
