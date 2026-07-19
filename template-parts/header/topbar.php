<?php
/**
 * Header Top Bar (tầng trên của header 2 tầng) — component độc lập (PROJECT_RULES.md mục 5).
 * Nội dung: menu phụ (menu location "header_top") + hotline/email (helper dùng chung với
 * offcanvas — tmnhanphat_get_header_contact_html) + nút Search. Ẩn ở vùng hamburger (nội
 * dung liên hệ chuyển vào offcanvas — xem navigation.php). Khi sticky: COLLAPSE mượt (CSS).
 *
 * @package TMNhanPhat
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! tmnhanphat_get_header_mod( 'tmnhanphat_header_topbar_enable' ) ) {
	return;
}
?>
<div class="site-header__topbar" aria-label="<?php esc_attr_e( 'Thanh thông tin phụ', 'tmnhanphat' ); ?>">
	<?php if ( has_nav_menu( 'header_top' ) ) : ?>
		<?php
		// Menu phụ (trái). Nguồn page/category/custom link sẽ thêm ở bước Settings (B7).
		wp_nav_menu( array(
			'theme_location' => 'header_top',
			'menu_class'     => 'topbar-menu',
			'container'      => false,
			'depth'          => 1,
			'fallback_cb'    => false,
		) );
		?>
	<?php endif; ?>

	<div class="site-header__topbar-actions">
		<?php echo tmnhanphat_get_header_contact_html(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- helper đã escape toàn bộ (SVG tĩnh + esc). ?>

		<?php /* Nút Search — panel tìm kiếm nối ở B3. Hiện là điểm neo + a11y. */ ?>
		<button type="button" class="site-header__search-toggle" aria-label="<?php esc_attr_e( 'Tìm kiếm', 'tmnhanphat' ); ?>">
			<svg width="18" height="18" viewBox="0 0 24 24" fill="none" aria-hidden="true" focusable="false">
				<circle cx="11" cy="11" r="7" stroke="currentColor" stroke-width="1.8" />
				<path d="m20 20-3.5-3.5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" />
			</svg>
		</button>
	</div>
</div>
