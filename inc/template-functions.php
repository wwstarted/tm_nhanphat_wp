<?php
/**
 * Template helper functions dùng chung cho header (trạng thái transparent/sticky, dynamic CSS).
 * Tách riêng khỏi inc/customizer để: header.php/template-parts đọc trạng thái qua đây,
 * còn inc/customizer/header-customizer.php chỉ lo đăng ký control — xem PROJECT_RULES.md mục 22.
 *
 * @package TMNhanPhat
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Giá trị mặc định cho toàn bộ setting Header Customizer.
 * Dùng chung bởi header-customizer.php (default control) và tmnhanphat_get_header_mod()
 * (fallback khi setting chưa từng được lưu) — tránh lặp số liệu ở 2 nơi (DRY).
 *
 * @return array<string, mixed>
 */
function tmnhanphat_header_defaults() {
	return array(
		// Figma: logo 96×96, header cao ~130 (logo 96 + 17 trên/dưới), khung nội dung
		// khớp container chung 1684, menu Inter 24/400 #F5F5F4, gap 32.
		'tmnhanphat_header_logo'                => '',
		'tmnhanphat_header_logo_retina'         => '',
		'tmnhanphat_header_logo_width'          => 96,
		'tmnhanphat_header_height'              => 130,
		'tmnhanphat_header_container_width'     => 1200, // Migrate: khớp container site 1200.
		'tmnhanphat_header_sticky_enable'       => true,
		'tmnhanphat_header_transparent_enable'  => true,
		'tmnhanphat_header_sticky_bg'           => '#ffffff',
		'tmnhanphat_header_sticky_text'         => '#1a1a1a',
		'tmnhanphat_header_transparent_text'    => '#ffffff',
		'tmnhanphat_header_bg'                  => '#ffffff',
		'tmnhanphat_header_menu_font'           => 'inherit',
		'tmnhanphat_header_menu_font_size'      => 24,
		'tmnhanphat_header_menu_font_weight'    => '400',
		'tmnhanphat_header_menu_spacing'        => 32,
		'tmnhanphat_header_shadow'              => 'soft',
		'tmnhanphat_header_border_bottom'       => false,
		'tmnhanphat_header_transition_duration' => 250,
		'tmnhanphat_header_mobile_breakpoint'   => 992,
		'tmnhanphat_header_padding_top'         => 0,
		'tmnhanphat_header_padding_bottom'      => 0,

		// Navigation (Floating) — Figma: menu #F5F5F4 trên hero; hover/active sáng lên
		// trắng đặc (brand blue quá tối trên ảnh nền tối, kém tương phản).
		'tmnhanphat_header_nav_text_floating'   => '#F5F5F4',
		'tmnhanphat_header_nav_hover_floating'  => '#ffffff',
		'tmnhanphat_header_nav_active_floating' => '#ffffff',

		// Navigation (Sticky) — hover/active dùng xanh brand (bỏ #0d6efd bootstrap cũ).
		'tmnhanphat_header_nav_text_sticky'     => '#222222',
		'tmnhanphat_header_nav_hover_sticky'    => '#046AB5',
		'tmnhanphat_header_nav_active_sticky'   => '#046AB5',
	);
}

/**
 * Đọc 1 theme_mod của Header kèm fallback lấy từ tmnhanphat_header_defaults().
 *
 * @param string $key Tên setting (khớp key trong tmnhanphat_header_defaults()).
 * @return mixed
 */
function tmnhanphat_get_header_mod( $key ) {
	$defaults = tmnhanphat_header_defaults();
	$default  = isset( $defaults[ $key ] ) ? $defaults[ $key ] : '';

	return get_theme_mod( $key, $default );
}

/**
 * Danh sách font-family cho phép chọn ở Customizer. Chỉ dùng font hệ thống có sẵn —
 * dự án không tải font/thư viện ngoài (PROJECT_RULES.md mục 21).
 *
 * @return array<string, string>
 */
function tmnhanphat_get_menu_font_choices() {
	return array(
		'inherit'   => __( 'Theo theme (mặc định)', 'tmnhanphat' ),
		'system'    => __( 'System UI', 'tmnhanphat' ),
		'serif'     => __( 'Serif (Georgia)', 'tmnhanphat' ),
		'monospace' => __( 'Monospace', 'tmnhanphat' ),
	);
}

/**
 * Quy đổi key font sang font-family CSS thực tế.
 *
 * @param string $key Key trong tmnhanphat_get_menu_font_choices().
 * @return string
 */
function tmnhanphat_get_menu_font_stack( $key ) {
	$stacks = array(
		'inherit'   => 'var(--font-family-base)',
		'system'    => 'system-ui, -apple-system, "Segoe UI", Roboto, sans-serif',
		'serif'     => 'Georgia, "Times New Roman", serif',
		'monospace' => '"Courier New", Courier, monospace',
	);

	return isset( $stacks[ $key ] ) ? $stacks[ $key ] : $stacks['inherit'];
}

/**
 * Danh sách mức bóng đổ Header cho phép chọn ở Customizer.
 *
 * @return array<string, string>
 */
function tmnhanphat_get_header_shadow_choices() {
	return array(
		'none'   => __( 'Không có', 'tmnhanphat' ),
		'soft'   => __( 'Nhẹ', 'tmnhanphat' ),
		'medium' => __( 'Vừa', 'tmnhanphat' ),
	);
}

/**
 * Quy đổi key bóng đổ sang giá trị box-shadow CSS thực tế.
 *
 * @param string $key Key trong tmnhanphat_get_header_shadow_choices().
 * @return string
 */
function tmnhanphat_get_header_shadow_value( $key ) {
	$shadows = array(
		'none'   => 'none',
		'soft'   => '0 2px 10px rgb(0 0 0 / 0.06)',
		'medium' => '0 4px 20px rgb(0 0 0 / 0.12)',
	);

	return isset( $shadows[ $key ] ) ? $shadows[ $key ] : $shadows['soft'];
}

/**
 * Header có đang ở trạng thái "trong suốt, nằm đè lên Hero" hay không.
 * Chỉ đúng ở Trang chủ và khi setting Transparent đang bật.
 *
 * @return bool
 */
function tmnhanphat_is_transparent_header() {
	return is_front_page() && (bool) tmnhanphat_get_header_mod( 'tmnhanphat_header_transparent_enable' );
}

/**
 * Site có bật hiệu ứng Sticky (chuyển position:fixed khi cuộn) hay không.
 *
 * @return bool
 */
function tmnhanphat_is_header_sticky_enabled() {
	return (bool) tmnhanphat_get_header_mod( 'tmnhanphat_header_sticky_enable' );
}

/**
 * Header có cố định (position:fixed) NGAY khi tải trang hay không — đúng với mọi trang
 * KHÔNG trong suốt khi Sticky đang bật (trang chủ tắt Transparent, hoặc mọi trang khác).
 *
 * @return bool
 */
function tmnhanphat_header_is_fixed_from_load() {
	return ! tmnhanphat_is_transparent_header() && tmnhanphat_is_header_sticky_enabled();
}

/**
 * Danh sách class cho thẻ <header> dựa trên trạng thái transparent/sticky hiện tại.
 *
 * @return string[]
 */
function tmnhanphat_get_header_classes() {
	$classes   = array( 'site-header' );
	$classes[] = tmnhanphat_is_transparent_header() ? 'site-header--transparent' : 'site-header--solid';

	if ( tmnhanphat_is_header_sticky_enabled() ) {
		$classes[] = 'site-header--sticky-enabled';
	}

	if ( tmnhanphat_header_is_fixed_from_load() ) {
		$classes[] = 'site-header--fixed';
	}

	return $classes;
}

/**
 * Thêm class cho <body> để CSS bù trừ khoảng trống khi header cố định ngay từ đầu
 * (tránh nội dung bị header đè lên — không cần cho header transparent vì nó nằm ngoài luồng).
 *
 * @param string[] $classes Danh sách class hiện có.
 * @return string[]
 */
function tmnhanphat_body_classes( $classes ) {
	if ( tmnhanphat_header_is_fixed_from_load() ) {
		$classes[] = 'has-fixed-header';
	}

	if ( tmnhanphat_is_transparent_header() ) {
		$classes[] = 'has-transparent-header';
	}

	return $classes;
}
add_filter( 'body_class', 'tmnhanphat_body_classes' );

/**
 * Sinh chuỗi CSS custom properties + media query theo Mobile Breakpoint từ theme_mod,
 * gắn vào stylesheet header qua wp_add_inline_style() trong inc/enqueue.php.
 *
 * Mobile Breakpoint phải render tĩnh vào @media vì CSS custom property không dùng được
 * trong điều kiện @media (giới hạn của CSS thuần, không phải build tool nào bù được).
 *
 * @return string
 */
function tmnhanphat_render_header_css_vars() {
	$logo_width  = absint( tmnhanphat_get_header_mod( 'tmnhanphat_header_logo_width' ) );
	$height      = absint( tmnhanphat_get_header_mod( 'tmnhanphat_header_height' ) );
	$container_w = absint( tmnhanphat_get_header_mod( 'tmnhanphat_header_container_width' ) );
	$sticky_bg   = tmnhanphat_get_header_mod( 'tmnhanphat_header_sticky_bg' );
	$sticky_text = tmnhanphat_get_header_mod( 'tmnhanphat_header_sticky_text' );
	$trans_text  = tmnhanphat_get_header_mod( 'tmnhanphat_header_transparent_text' );
	$bg          = tmnhanphat_get_header_mod( 'tmnhanphat_header_bg' );
	$menu_font   = tmnhanphat_get_menu_font_stack( tmnhanphat_get_header_mod( 'tmnhanphat_header_menu_font' ) );
	$menu_size   = absint( tmnhanphat_get_header_mod( 'tmnhanphat_header_menu_font_size' ) );
	$menu_weight = absint( tmnhanphat_get_header_mod( 'tmnhanphat_header_menu_font_weight' ) );
	$menu_gap    = absint( tmnhanphat_get_header_mod( 'tmnhanphat_header_menu_spacing' ) );
	$shadow      = tmnhanphat_get_header_shadow_value( tmnhanphat_get_header_mod( 'tmnhanphat_header_shadow' ) );
	$border      = tmnhanphat_get_header_mod( 'tmnhanphat_header_border_bottom' ) ? '1px solid var(--color-border)' : 'none';
	$duration    = absint( tmnhanphat_get_header_mod( 'tmnhanphat_header_transition_duration' ) );
	$padding_top = absint( tmnhanphat_get_header_mod( 'tmnhanphat_header_padding_top' ) );
	$padding_bot = absint( tmnhanphat_get_header_mod( 'tmnhanphat_header_padding_bottom' ) );
	$breakpoint  = absint( tmnhanphat_get_header_mod( 'tmnhanphat_header_mobile_breakpoint' ) );

	$nav_text_floating   = tmnhanphat_get_header_mod( 'tmnhanphat_header_nav_text_floating' );
	$nav_hover_floating  = tmnhanphat_get_header_mod( 'tmnhanphat_header_nav_hover_floating' );
	$nav_active_floating = tmnhanphat_get_header_mod( 'tmnhanphat_header_nav_active_floating' );
	$nav_text_sticky     = tmnhanphat_get_header_mod( 'tmnhanphat_header_nav_text_sticky' );
	$nav_hover_sticky    = tmnhanphat_get_header_mod( 'tmnhanphat_header_nav_hover_sticky' );
	$nav_active_sticky   = tmnhanphat_get_header_mod( 'tmnhanphat_header_nav_active_sticky' );

	$css  = ':root{';
	$css .= '--header-logo-width:' . $logo_width . 'px;';
	$css .= '--header-height:' . $height . 'px;';
	$css .= '--header-container-width:' . $container_w . 'px;';
	// Sticky glass (yêu cầu chủ dự án): nền mờ 85% + backdrop-blur (blur đặt ở
	// header.css) — màu gốc vẫn theo setting Sticky Background.
	$css .= '--header-sticky-bg:' . tmnhanphat_hex_to_rgba( $sticky_bg, 85 ) . ';';
	$css .= '--header-sticky-text:' . $sticky_text . ';';
	$css .= '--header-transparent-text:' . $trans_text . ';';
	$css .= '--header-bg:' . $bg . ';';
	$css .= '--header-menu-font:' . $menu_font . ';';
	$css .= '--header-menu-font-size:' . $menu_size . 'px;';
	$css .= '--header-menu-font-weight:' . $menu_weight . ';';
	$css .= '--header-menu-spacing:' . $menu_gap . 'px;';
	$css .= '--header-shadow:' . $shadow . ';';
	$css .= '--header-border-bottom:' . $border . ';';
	$css .= '--header-transition-duration:' . $duration . 'ms;';
	$css .= '--header-padding-top:' . $padding_top . 'px;';
	$css .= '--header-padding-bottom:' . $padding_bot . 'px;';
	$css .= '--header-nav-text-floating:' . $nav_text_floating . ';';
	$css .= '--header-nav-hover-floating:' . $nav_hover_floating . ';';
	$css .= '--header-nav-active-floating:' . $nav_active_floating . ';';
	$css .= '--header-nav-text-sticky:' . $nav_text_sticky . ';';
	$css .= '--header-nav-hover-sticky:' . $nav_hover_sticky . ';';
	$css .= '--header-nav-active-sticky:' . $nav_active_sticky . ';';
	$css .= '}';

	// Ngưỡng chuyển hamburger — bắt buộc render tĩnh (xem docblock).
	$css .= '@media (max-width:' . $breakpoint . 'px){';
	$css .= '.main-navigation__toggle{display:flex;}';
	$css .= '.primary-menu{flex-direction:column;align-items:flex-start;}';
	$css .= '.sub-menu{position:static;box-shadow:none;opacity:1;visibility:visible;transform:none;display:none;}';
	$css .= '.menu-item-has-children.is-submenu-open>.sub-menu{display:block;}';
	$css .= '.submenu-toggle{display:inline-flex;}';
	// Offcanvas panel luôn có nền sáng (assets/css/layout/header.css) bất kể Header đang
	// transparent hay sticky — ép menu bên trong dùng màu chữ tối để không bị "chữ trắng
	// trên nền trắng" khi mở offcanvas lúc Header còn ở trạng thái Floating.
	$css .= '.main-navigation__panel{--header-nav-color:var(--color-text);}';
	// Trong offcanvas: cỡ chữ menu CỐ ĐỊNH dễ đọc — không dùng công thức fluid
	// min(24 * --fig1px) của desktop (fig co nhỏ trên mobile làm chữ chỉ ~11px).
	$css .= '.main-navigation__panel .primary-menu{font-size:18px;gap:0;}';
	// Offcanvas: mỗi mục menu 1 dòng full-width có kẻ phân cách, xếp từ TRÊN xuống.
	$css .= '.main-navigation__panel .primary-menu>li{width:100%;border-bottom:1px solid #F0F2F5;}';
	$css .= '.main-navigation__panel .primary-menu>li>a{display:block;width:100%;padding:14px 0;}';
	// FIX offcanvas KHÔNG full màn hình: transform/will-change/animation VÀ CẢ
	// backdrop-filter trên header tạo CONTAINING BLOCK cho position:fixed → panel bị
	// "nhốt" trong khung header (cao 130px). Ở vùng hamburger loại bỏ hết các thuộc
	// tính đó (glass chỉ giữ trên desktop) để panel fixed bám đúng viewport.
	$css .= '.site-header--transparent{transform:none;will-change:auto;}';
	$css .= '.site-header--transparent.is-scrolled{animation:none;}';
	$css .= '.site-header--solid.site-header--fixed,.site-header--transparent.is-scrolled{backdrop-filter:none;-webkit-backdrop-filter:none;background-color:' . tmnhanphat_hex_to_rgba( $sticky_bg, 97 ) . ';}';
	$css .= '}';
	$css .= '@media (min-width:' . ( $breakpoint + 1 ) . 'px){';
	// Desktop: ẩn toàn bộ phần offcanvas-only — hamburger, overlay, head (logo + nút
	// đóng) và footer (social) của panel; menu inline như bình thường.
	$css .= '.main-navigation__toggle,.main-navigation__overlay,.main-navigation__panel-head,.main-navigation__panel-footer{display:none;}';
	$css .= '.main-navigation__panel{position:static;width:auto;height:auto;transform:none;box-shadow:none;background:transparent;padding:0;overflow:visible;}';
	$css .= '.submenu-toggle{display:none;}';
	$css .= '}';

	return $css;
}

/* ==========================================================================
 * FOOTER — helper riêng, không đụng tới bất kỳ hàm Header nào ở trên.
 * ========================================================================== */

/**
 * Giá trị mặc định cho toàn bộ setting Footer Customizer — dùng chung bởi
 * inc/customizer/footer-customizer.php (default control) và tmnhanphat_get_footer_mod()
 * (fallback khi setting chưa từng được lưu).
 *
 * @return array<string, mixed>
 */
function tmnhanphat_footer_defaults() {
	return array(
		// Palette Design mới: nền Footer xanh, chữ/heading trắng, gạch trắng mờ, copyright đỏ.
		// Chỉ đổi GIÁ TRỊ MẶC ĐỊNH (không đổi cấu trúc Customizer) — admin vẫn chỉnh được.
		// Menu/contact/divider để màu TRẮNG, độ mờ (opacity) xử lý ở footer.css để giữ hex
		// hợp lệ cho sanitize_hex_color mà vẫn ra rgba(255,255,255,.85) như Design.
		// Figma: nền #0C4B9B; link menu #A8AEB8 (xám xanh nhạt) hover trắng.
		'tmnhanphat_footer_bg'               => '#0C4B9B',
		'tmnhanphat_footer_text'             => '#ffffff',
		'tmnhanphat_footer_link'             => '#A8AEB8',
		'tmnhanphat_footer_link_hover'       => '#ffffff',
		'tmnhanphat_footer_bottom_bg'        => '#E31F2B',
		'tmnhanphat_footer_bottom_text'      => '#ffffff',
		/* translators: %s: năm hiện tại */
		'tmnhanphat_footer_copyright'        => sprintf( __( '© Copyright %s thangmaynhanphat.com All rights reserved.', 'tmnhanphat' ), gmdate( 'Y' ) ),
		'tmnhanphat_footer_logo'             => '',
		'tmnhanphat_footer_logo_width'       => 124, // Figma: logo footer 124×124.
		'tmnhanphat_footer_company_name'     => 'Thang máy Nhân Phát',
		'tmnhanphat_footer_email'            => 'nhanphatelevator@gmail.com',
		'tmnhanphat_footer_email_label'      => __( 'Email', 'tmnhanphat' ),
		'tmnhanphat_footer_email_enable'     => true,
		'tmnhanphat_footer_phone'            => '0933.94.8386',
		'tmnhanphat_footer_phone_label'      => __( 'Số điện thoại', 'tmnhanphat' ),
		'tmnhanphat_footer_phone_enable'     => true,
		'tmnhanphat_footer_address'          => 'Số 41 Trung Mỹ Tây 12, Phường Trung Mỹ Tây, TP.HCM',
		'tmnhanphat_footer_address_label'    => __( 'Địa chỉ', 'tmnhanphat' ),
		'tmnhanphat_footer_address_enable'   => true,
		'tmnhanphat_footer_facebook'         => '',
		'tmnhanphat_footer_youtube'          => '',
		'tmnhanphat_footer_zalo'             => '',
		'tmnhanphat_footer_tiktok'           => '',
		'tmnhanphat_footer_linkedin'         => '',
		'tmnhanphat_footer_social_enable'    => true,
		// Figma: pad trên 65 (đáy 71 ở CSS); khung khớp container chung 1684; gap cột 32.
		'tmnhanphat_footer_padding'          => 65,
		'tmnhanphat_footer_container_width'  => 1200, // Migrate: khớp container site 1200.
		'tmnhanphat_footer_heading_color'    => '#ffffff',
		'tmnhanphat_footer_divider_color'    => '#ffffff',
		'tmnhanphat_footer_logo_spacing'     => 22, // Figma: logo → heading 22.
		'tmnhanphat_footer_column_gap'       => 32,
	);
}

/**
 * Đọc 1 theme_mod của Footer kèm fallback lấy từ tmnhanphat_footer_defaults().
 *
 * @param string $key Tên setting (khớp key trong tmnhanphat_footer_defaults()).
 * @return mixed
 */
function tmnhanphat_get_footer_mod( $key ) {
	$defaults = tmnhanphat_footer_defaults();
	$default  = isset( $defaults[ $key ] ) ? $defaults[ $key ] : '';

	return get_theme_mod( $key, $default );
}

/**
 * Danh sách mạng xã hội hiển thị ở Column 1 Footer: label + setting theme_mod tương ứng +
 * icon SVG inline tĩnh (không phụ thuộc icon font/CDN ngoài — PROJECT_RULES.md mục 21).
 * Icon chỉ hiện khi setting URL tương ứng có giá trị (xem template-parts/footer/column-branding.php).
 *
 * @return array<string, array{label: string, mod: string, icon: string}>
 */
function tmnhanphat_get_footer_social_networks() {
	return array(
		'facebook' => array(
			'label' => __( 'Facebook', 'tmnhanphat' ),
			'mod'   => 'tmnhanphat_footer_facebook',
			'icon'  => '<svg viewBox="0 0 24 24" width="16" height="16" fill="currentColor" aria-hidden="true"><path d="M13.5 21v-7.5h2.52l.38-3H13.5V8.62c0-.87.24-1.46 1.5-1.46h1.6V4.46C16.32 4.4 15.32 4.3 14.16 4.3c-2.33 0-3.92 1.42-3.92 4.03v2.17H7.7v3h2.54V21h3.26z"/></svg>',
		),
		'youtube'  => array(
			'label' => __( 'YouTube', 'tmnhanphat' ),
			'mod'   => 'tmnhanphat_footer_youtube',
			'icon'  => '<svg viewBox="0 0 24 24" width="16" height="16" fill="currentColor" aria-hidden="true"><path d="M22 12s0-3.2-.4-4.7c-.23-.86-.9-1.53-1.76-1.76C18.34 5.1 12 5.1 12 5.1s-6.34 0-7.84.4c-.86.23-1.53.9-1.76 1.76C2 8.8 2 12 2 12s0 3.2.4 4.7c.23.86.9 1.53 1.76 1.76 1.5.44 7.84.44 7.84.44s6.34 0 7.84-.44c.86-.23 1.53-.9 1.76-1.76.4-1.5.4-4.7.4-4.7zM10.2 15.3V8.7l5.5 3.3-5.5 3.3z"/></svg>',
		),
		'zalo'     => array(
			'label' => __( 'Zalo', 'tmnhanphat' ),
			'mod'   => 'tmnhanphat_footer_zalo',
			'icon'  => '<svg viewBox="0 0 24 24" width="16" height="16" fill="currentColor" aria-hidden="true"><path d="M12 2.2c-5.5 0-10 3.9-10 8.7 0 2.8 1.5 5.3 3.9 6.9-.15 1-.6 2.4-1.5 3.7 0 0 2.4-.4 4.5-2 1 .3 2.1.4 3.1.4 5.5 0 10-3.9 10-8.7s-4.5-9-10-9z"/></svg>',
		),
		'tiktok'   => array(
			'label' => __( 'TikTok', 'tmnhanphat' ),
			'mod'   => 'tmnhanphat_footer_tiktok',
			'icon'  => '<svg viewBox="0 0 24 24" width="16" height="16" fill="currentColor" aria-hidden="true"><path d="M16.6 2h-3.1v13.4a2.6 2.6 0 1 1-2.6-2.6c.24 0 .47.02.7.07V9.6a5.8 5.8 0 1 0 5.3 5.77V9c1.2.85 2.6 1.3 4.1 1.3V7.1c-2.1 0-3.9-1.6-4.1-3.6L16.6 2z"/></svg>',
		),
		'linkedin' => array(
			'label' => __( 'LinkedIn', 'tmnhanphat' ),
			'mod'   => 'tmnhanphat_footer_linkedin',
			'icon'  => '<svg viewBox="0 0 24 24" width="16" height="16" fill="currentColor" aria-hidden="true"><path d="M6.94 8.5H4.1V20h2.84V8.5zM5.5 4a1.65 1.65 0 1 0 0 3.3A1.65 1.65 0 0 0 5.5 4zM20 20h-2.84v-6.1c0-1.46-.53-2.46-1.85-2.46-1.01 0-1.6.68-1.87 1.34-.1.23-.12.56-.12.88V20h-2.84s.04-10.4 0-11.5h2.84v1.63c.38-.58 1.05-1.42 2.57-1.42 1.87 0 3.27 1.22 3.27 3.85V20z"/></svg>',
		),
	);
}

/**
 * Danh sách item mặc định cho từng cột menu Footer khi admin chưa tạo Menu tương ứng
 * (khớp thiết kế mẫu) — dùng bởi tmnhanphat_footer_menu_fallback() trong inc/menus.php.
 *
 * @param string $column Key cột: 'company' | 'policy' | 'product' | 'service'.
 * @return array<int, array{label: string, url: string}>
 */
function tmnhanphat_get_footer_menu_fallback_items( $column ) {
	$items = array(
		'company' => array(
			array( 'label' => __( 'Về chúng tôi', 'tmnhanphat' ), 'url' => '#' ),
			array( 'label' => __( 'Dự án', 'tmnhanphat' ), 'url' => '#' ),
			array( 'label' => __( 'Tuyển dụng', 'tmnhanphat' ), 'url' => '#' ),
			array( 'label' => __( 'Liên hệ', 'tmnhanphat' ), 'url' => '#' ),
			array( 'label' => __( 'Tin tức', 'tmnhanphat' ), 'url' => '#' ),
		),
		'policy'  => array(
			array( 'label' => __( 'Chính sách bảo hành', 'tmnhanphat' ), 'url' => '#' ),
			array( 'label' => __( 'Chính sách hướng dẫn', 'tmnhanphat' ), 'url' => '#' ),
			array( 'label' => __( 'Chính sách thanh toán', 'tmnhanphat' ), 'url' => '#' ),
			array( 'label' => __( 'Chính sách hỗ trợ', 'tmnhanphat' ), 'url' => '#' ),
			array( 'label' => __( 'Chính sách bảo mật thông tin', 'tmnhanphat' ), 'url' => '#' ),
		),
		'product' => array(
			array( 'label' => __( 'Thang máy tải khách', 'tmnhanphat' ), 'url' => '#' ),
			array( 'label' => __( 'Thang máy tải thực phẩm', 'tmnhanphat' ), 'url' => '#' ),
			array( 'label' => __( 'Thang máy tải hàng', 'tmnhanphat' ), 'url' => '#' ),
			array( 'label' => __( 'Thang máy bệnh viện', 'tmnhanphat' ), 'url' => '#' ),
		),
		'service' => array(
			array( 'label' => __( 'Lắp đặt thang máy', 'tmnhanphat' ), 'url' => '#' ),
			array( 'label' => __( 'Sửa chữa thang máy', 'tmnhanphat' ), 'url' => '#' ),
			array( 'label' => __( 'Bảo trì định kỳ', 'tmnhanphat' ), 'url' => '#' ),
			array( 'label' => __( 'Nâng cấp thang máy', 'tmnhanphat' ), 'url' => '#' ),
		),
	);

	return isset( $items[ $column ] ) ? $items[ $column ] : array();
}

/**
 * Sinh chuỗi CSS custom properties cho Footer, gắn vào stylesheet qua wp_add_inline_style()
 * trong inc/enqueue.php — cùng quy ước với tmnhanphat_render_header_css_vars() (mục 22).
 *
 * @return string
 */
function tmnhanphat_render_footer_css_vars() {
	$bg            = tmnhanphat_get_footer_mod( 'tmnhanphat_footer_bg' );
	$text          = tmnhanphat_get_footer_mod( 'tmnhanphat_footer_text' );
	$link          = tmnhanphat_get_footer_mod( 'tmnhanphat_footer_link' );
	$link_hover    = tmnhanphat_get_footer_mod( 'tmnhanphat_footer_link_hover' );
	$bottom_bg     = tmnhanphat_get_footer_mod( 'tmnhanphat_footer_bottom_bg' );
	$bottom_text   = tmnhanphat_get_footer_mod( 'tmnhanphat_footer_bottom_text' );
	$logo_width    = absint( tmnhanphat_get_footer_mod( 'tmnhanphat_footer_logo_width' ) );
	$padding       = absint( tmnhanphat_get_footer_mod( 'tmnhanphat_footer_padding' ) );
	$container_w   = absint( tmnhanphat_get_footer_mod( 'tmnhanphat_footer_container_width' ) );
	$heading_color = tmnhanphat_get_footer_mod( 'tmnhanphat_footer_heading_color' );
	$divider_color = tmnhanphat_get_footer_mod( 'tmnhanphat_footer_divider_color' );
	$logo_spacing  = absint( tmnhanphat_get_footer_mod( 'tmnhanphat_footer_logo_spacing' ) );
	$column_gap    = absint( tmnhanphat_get_footer_mod( 'tmnhanphat_footer_column_gap' ) );

	$css  = ':root{';
	$css .= '--footer-bg:' . $bg . ';';
	$css .= '--footer-text:' . $text . ';';
	$css .= '--footer-link:' . $link . ';';
	$css .= '--footer-hover:' . $link_hover . ';';
	$css .= '--footer-bottom-bg:' . $bottom_bg . ';';
	$css .= '--footer-bottom-text:' . $bottom_text . ';';
	$css .= '--footer-logo-width:' . $logo_width . 'px;';
	$css .= '--footer-padding:' . $padding . 'px;';
	$css .= '--footer-container-width:' . $container_w . 'px;';
	$css .= '--footer-heading-color:' . $heading_color . ';';
	$css .= '--footer-divider-color:' . $divider_color . ';';
	$css .= '--footer-logo-spacing:' . $logo_spacing . 'px;';
	$css .= '--footer-column-gap:' . $column_gap . 'px;';
	$css .= '}';

	return $css;
}

/* ==========================================================================
 * HERO BANNER (trang chủ) — helper riêng, không đụng tới Header/Footer.
 * ========================================================================== */

/**
 * Giá trị mặc định cho toàn bộ setting Hero Banner Customizer — dùng chung bởi
 * inc/customizer/hero-customizer.php (default control) và tmnhanphat_get_hero_mod()
 * (fallback khi setting chưa từng được lưu).
 *
 * @return array<string, mixed>
 */
function tmnhanphat_hero_defaults() {
	$defaults = array(
		// Background
		'tmnhanphat_hero_bg_image'             => '',
		'tmnhanphat_hero_fallback_bg_color'    => '#1a1a1a',

		// Overlay — Figma KHÔNG có lớp overlay trên ảnh Hero (ảnh gốc đã tối) → mặc định
		// TẮT. Color/opacity giữ làm giá trị khởi điểm khi admin bật lại cho ảnh sáng.
		'tmnhanphat_hero_overlay_enable'       => false,
		'tmnhanphat_hero_overlay_color'        => '#000000',
		'tmnhanphat_hero_overlay_opacity'      => 55,
		'tmnhanphat_hero_overlay_blend'        => 'normal',

		// Subtitle — Figma: Inter 32px / weight 400, chữ thường (không uppercase), trắng.
		'tmnhanphat_hero_subtitle_enable'      => true,
		'tmnhanphat_hero_subtitle_text'        => 'Lắp đặt • Sửa chữa • Bảo trì',
		'tmnhanphat_hero_subtitle_color'       => '#ffffff',
		'tmnhanphat_hero_subtitle_font_size'   => 32,
		'tmnhanphat_hero_subtitle_font_weight' => '400',

		// Heading — Figma: 96px / weight 900, rộng ~1524px (xuống 2 dòng trên canvas 1920).
		'tmnhanphat_hero_heading_text'         => 'Giải pháp thang máy toàn diện cho mọi công trình',
		'tmnhanphat_hero_heading_color'        => '#ffffff',
		'tmnhanphat_hero_heading_font_size'    => 96,
		'tmnhanphat_hero_heading_font_weight'  => '900',

		// Description
		'tmnhanphat_hero_description_text'         => 'Thang Máy Nhân Phát mang đến giải pháp thang máy gia đình an toàn, hiện đại và phù hợp với từng không gian sống. Chúng tôi đồng hành từ tư vấn, thiết kế, lắp đặt đến bảo trì, giúp mỗi công trình có hệ thống thang máy bền đẹp, vận hành ổn định và tối ưu chi phí.',
		'tmnhanphat_hero_description_color'        => '#ffffff', // Figma: text trắng đặc.
		'tmnhanphat_hero_description_font_size'    => 20,       // Figma: Inter 20px.
		'tmnhanphat_hero_description_max_lines'    => 3,
		'tmnhanphat_hero_description_clamp_enable' => true,

		// Buttons
		'tmnhanphat_hero_btn_primary_enable'   => true,
		'tmnhanphat_hero_btn_primary_text'     => 'Yêu cầu báo giá',
		'tmnhanphat_hero_btn_primary_url'      => '#',
		'tmnhanphat_hero_btn_primary_new_tab'  => false,
		'tmnhanphat_hero_btn_secondary_enable' => true,
		'tmnhanphat_hero_btn_secondary_text'   => 'Liên hệ chúng tôi',
		'tmnhanphat_hero_btn_secondary_url'    => '#',
		'tmnhanphat_hero_btn_secondary_new_tab' => false,
		// Design mới: nút chính XANH (bỏ gold), chữ trắng, hover sáng nhẹ.
		'tmnhanphat_hero_btn_primary_bg'         => '#046AB5',
		'tmnhanphat_hero_btn_primary_text_color' => '#ffffff',
		'tmnhanphat_hero_btn_primary_hover_bg'   => '#0579CC',
		'tmnhanphat_hero_btn_secondary_bg'         => 'transparent',
		'tmnhanphat_hero_btn_secondary_text_color' => '#ffffff',
		'tmnhanphat_hero_btn_secondary_hover_bg'   => 'rgba(255,255,255,.08)',
		'tmnhanphat_hero_btn_secondary_border_color' => '#ffffff',
		// Figma: nút pill (248×55, radius 50), text 24px canh giữa → padding 12/20.
		'tmnhanphat_hero_btn_border_radius'    => 50,
		'tmnhanphat_hero_btn_padding_x'        => 20,
		'tmnhanphat_hero_btn_padding_y'        => 12,

		// Layout — Figma: Heading box 1524px trên canvas 1920 → content không tự bó hẹp
		// dưới container; 1560 để heading luôn fill hết bề rộng container chung.
		// Migrate 1200: heading giờ ~68px (fig cap 0.71) → khối content 940 đủ cho 2 dòng,
		// vừa trong inner 1152 (container 1200 − padding). Trước 1300 (heading 96 cũ).
		'tmnhanphat_hero_content_max_width'        => 940,
		'tmnhanphat_hero_content_max_width_tablet' => 600,
		'tmnhanphat_hero_content_max_width_mobile' => 600,
		'tmnhanphat_hero_content_align'            => 'center',
		'tmnhanphat_hero_height_desktop'           => 100,
		'tmnhanphat_hero_padding_tablet'           => 100,
		'tmnhanphat_hero_padding_mobile'           => 90,

		// Hero Stats Card — LAYOUT MỚI (straddle): card nổi vắt qua đáy Hero, NỬA DƯỚI nằm
		// trên nền SÁNG phía dưới → nền card phải ĐỤC (opacity 92) để chữ trắng đọc được
		// trên cả 2 nửa (bản cũ 20% chỉ hợp khi card nằm hẳn trên ảnh tối). Thêm shadow
		// medium cho card nổi (floating). Viền mảnh 20% (đặt trong render).
		'tmnhanphat_hero_stats_card_enable'       => true,
		'tmnhanphat_hero_stats_card_bg'           => '#22262E',
		'tmnhanphat_hero_stats_card_opacity'      => 92,
		'tmnhanphat_hero_stats_card_radius'       => 28, // Migrate: radius lớn giảm (40→28) cho card nhỏ hơn.
		'tmnhanphat_hero_stats_card_border_color' => '#ffffff',
		'tmnhanphat_hero_stats_card_border_width' => 1,
		'tmnhanphat_hero_stats_card_blur'         => 18,
		'tmnhanphat_hero_stats_card_shadow'       => 'medium',
		// Figma: số trắng; badge pill nền #A8AEB8, chữ tối #22262E.
		'tmnhanphat_hero_stats_number_color'      => '#ffffff',
		'tmnhanphat_hero_stats_badge_bg'          => '#A8AEB8',
		'tmnhanphat_hero_stats_badge_text_color'  => '#22262E',

		// Hero Spacing (desktop) — Figma: đỉnh Hero→Subtitle 176, đáy Card→đáy Hero 77.
		'tmnhanphat_hero_padding_top'              => 176,
		'tmnhanphat_hero_padding_bottom'           => 77,
		// Figma spacing (canvas 1184px @1920, Hero cao theo NỘI DUNG — không còn 100vh):
		// đỉnh Hero→Subtitle 176 (header float đè lên trong khoảng này), Subtitle→Heading
		// 24 (space-md ở CSS), Heading→Desc 37≈36, Desc→Buttons 46, Buttons→Stats 166
		// (CSS co theo 8.65vw ở màn nhỏ), đáy Card→đáy Hero 77.
		'tmnhanphat_hero_heading_margin_bottom'    => 36,
		'tmnhanphat_hero_description_margin_bottom' => 46,
		'tmnhanphat_hero_cta_margin_bottom'        => 24,
		'tmnhanphat_hero_stats_margin_top'         => 166,

		// Animation
		'tmnhanphat_hero_animation_enable'         => true,
		'tmnhanphat_hero_counter_animation_enable' => true,
		'tmnhanphat_hero_animation_duration'       => 600,
		'tmnhanphat_hero_animation_delay'          => 100,
	);

	$stat_defaults = array(
		1 => array(
			'number'      => '10+',
			'badge'       => 'Kinh nghiệm',
			'description' => '10+ năm đồng hành cùng hàng nghìn công trình trên cả nước',
		),
		2 => array(
			'number'      => '50+',
			'badge'       => 'Đội ngũ nhân sự',
			'description' => 'Hơn 50 nhân sự giàu kinh nghiệm, tận tâm với công việc',
		),
		3 => array(
			'number'      => '100+',
			'badge'       => 'Dự án',
			'description' => '10+ năm đồng hành cùng hàng nghìn công trình trên cả nước',
		),
		4 => array(
			'number'      => '300+',
			'badge'       => 'Khách hàng',
			'description' => '10+ năm đồng hành cùng hàng nghìn công trình trên cả nước',
		),
	);

	foreach ( $stat_defaults as $index => $stat ) {
		$defaults[ "tmnhanphat_hero_stat{$index}_enable" ]      = true;
		$defaults[ "tmnhanphat_hero_stat{$index}_number" ]      = $stat['number'];
		$defaults[ "tmnhanphat_hero_stat{$index}_badge" ]       = $stat['badge'];
		$defaults[ "tmnhanphat_hero_stat{$index}_description" ] = $stat['description'];
		$defaults[ "tmnhanphat_hero_stat{$index}_icon" ]        = '';
	}

	return $defaults;
}

/**
 * Đọc 1 theme_mod của Hero kèm fallback lấy từ tmnhanphat_hero_defaults().
 *
 * @param string $key Tên setting (khớp key trong tmnhanphat_hero_defaults()).
 * @return mixed
 */
function tmnhanphat_get_hero_mod( $key ) {
	$defaults = tmnhanphat_hero_defaults();
	$default  = isset( $defaults[ $key ] ) ? $defaults[ $key ] : '';

	return get_theme_mod( $key, $default );
}

/**
 * Danh sách lựa chọn Overlay Blend Mode cho phép chọn ở Customizer.
 *
 * @return array<string, string>
 */
function tmnhanphat_get_hero_overlay_blend_choices() {
	return array(
		'normal'     => __( 'Normal', 'tmnhanphat' ),
		'multiply'   => __( 'Multiply', 'tmnhanphat' ),
		'overlay'    => __( 'Overlay', 'tmnhanphat' ),
		'soft-light' => __( 'Soft Light', 'tmnhanphat' ),
	);
}

/**
 * Danh sách lựa chọn căn dọc nội dung Hero cho phép chọn ở Customizer.
 *
 * @return array<string, string>
 */
function tmnhanphat_get_hero_content_align_choices() {
	return array(
		'flex-start' => __( 'Đầu (Top)', 'tmnhanphat' ),
		'center'     => __( 'Giữa (Center)', 'tmnhanphat' ),
		'flex-end'   => __( 'Cuối (Bottom)', 'tmnhanphat' ),
	);
}

/**
 * Danh sách lựa chọn bóng đổ Stats Card cho phép chọn ở Customizer.
 *
 * @return array<string, string>
 */
function tmnhanphat_get_hero_stats_card_shadow_choices() {
	return array(
		'none'   => __( 'Không có', 'tmnhanphat' ),
		'soft'   => __( 'Nhẹ', 'tmnhanphat' ),
		'medium' => __( 'Vừa', 'tmnhanphat' ),
	);
}

/**
 * Quy đổi key bóng đổ Stats Card sang giá trị box-shadow CSS thực tế.
 *
 * @param string $key Key trong tmnhanphat_get_hero_stats_card_shadow_choices().
 * @return string
 */
function tmnhanphat_get_hero_stats_card_shadow_value( $key ) {
	$shadows = array(
		'none'   => 'none',
		'soft'   => '0 10px 30px rgb(0 0 0 / 0.08)',
		'medium' => '0 16px 40px rgb(0 0 0 / 0.16)',
	);

	return isset( $shadows[ $key ] ) ? $shadows[ $key ] : $shadows['soft'];
}

/**
 * Quy đổi màu hex + % opacity sang chuỗi rgba() dùng cho Stats Card Background —
 * Customizer Color Picker chỉ cho chọn hex nên cần ghép opacity riêng (mục 7: card
 * phải trong suốt nhẹ, không phải nền trắng đặc).
 *
 * @param string $hex     Màu dạng '#rrggbb'.
 * @param int    $opacity Phần trăm 0-100.
 * @return string Chuỗi 'rgba(r, g, b, a)'.
 */
function tmnhanphat_hex_to_rgba( $hex, $opacity ) {
	$hex = ltrim( (string) $hex, '#' );

	if ( 3 === strlen( $hex ) ) {
		$hex = $hex[0] . $hex[0] . $hex[1] . $hex[1] . $hex[2] . $hex[2];
	}

	if ( 6 !== strlen( $hex ) || ! ctype_xdigit( $hex ) ) {
		$hex = 'ffffff';
	}

	$r = hexdec( substr( $hex, 0, 2 ) );
	$g = hexdec( substr( $hex, 2, 2 ) );
	$b = hexdec( substr( $hex, 4, 2 ) );
	$a = max( 0, min( 100, (int) $opacity ) ) / 100;

	return "rgba({$r}, {$g}, {$b}, {$a})";
}

/**
 * Xây mảng 4 Company Stats từ theme_mod, dùng bởi template-parts/home/hero.php.
 *
 * @return array<int, array{enable: bool, number: string, badge: string, description: string, icon: string}>
 */
function tmnhanphat_get_hero_stats() {
	$stats = array();

	for ( $i = 1; $i <= 4; $i++ ) {
		$stats[] = array(
			'enable'      => (bool) tmnhanphat_get_hero_mod( "tmnhanphat_hero_stat{$i}_enable" ),
			'number'      => tmnhanphat_get_hero_mod( "tmnhanphat_hero_stat{$i}_number" ),
			'badge'       => tmnhanphat_get_hero_mod( "tmnhanphat_hero_stat{$i}_badge" ),
			'description' => tmnhanphat_get_hero_mod( "tmnhanphat_hero_stat{$i}_description" ),
			'icon'        => tmnhanphat_get_hero_mod( "tmnhanphat_hero_stat{$i}_icon" ),
		);
	}

	return $stats;
}

/**
 * Tách số đếm (Count-up) khỏi hậu tố hiển thị, ví dụ '10+' → value=10, suffix='+'.
 * Nếu chuỗi không bắt đầu bằng số (vd: "N/A"), trả về value=null — template khi đó
 * chỉ hiển thị nguyên văn, bỏ qua animation đếm số.
 *
 * @param string $text Giá trị "Number" nhập trong Customizer.
 * @return array{value: int|null, suffix: string}
 */
function tmnhanphat_get_hero_number_parts( $text ) {
	if ( preg_match( '/^(\d+)(.*)$/', trim( (string) $text ), $matches ) ) {
		return array(
			'value'  => (int) $matches[1],
			'suffix' => trim( $matches[2] ),
		);
	}

	return array(
		'value'  => null,
		'suffix' => '',
	);
}

/**
 * Sinh chuỗi CSS custom properties cho Hero, gắn vào stylesheet qua wp_add_inline_style()
 * trong inc/enqueue.php — cùng quy ước với Header/Footer (mục 22). Padding theo breakpoint
 * (desktop/tablet/mobile) được redefine lại biến --hero-padding-block bên trong @media
 * tương ứng — custom property được phép định nghĩa lại trong @media, chỉ riêng việc DÙNG
 * custom property LÀM điều kiện @media mới là giới hạn của CSS thuần.
 *
 * @return string
 */
function tmnhanphat_render_hero_css_vars() {
	$fallback_bg     = tmnhanphat_get_hero_mod( 'tmnhanphat_hero_fallback_bg_color' );
	$overlay_enable  = tmnhanphat_get_hero_mod( 'tmnhanphat_hero_overlay_enable' );
	$overlay_color   = tmnhanphat_get_hero_mod( 'tmnhanphat_hero_overlay_color' );
	$overlay_opacity = absint( tmnhanphat_get_hero_mod( 'tmnhanphat_hero_overlay_opacity' ) ) / 100;
	$overlay_blend   = tmnhanphat_get_hero_mod( 'tmnhanphat_hero_overlay_blend' );

	$subtitle_color  = tmnhanphat_get_hero_mod( 'tmnhanphat_hero_subtitle_color' );
	$subtitle_size   = absint( tmnhanphat_get_hero_mod( 'tmnhanphat_hero_subtitle_font_size' ) );
	$subtitle_weight = absint( tmnhanphat_get_hero_mod( 'tmnhanphat_hero_subtitle_font_weight' ) );

	$heading_color   = tmnhanphat_get_hero_mod( 'tmnhanphat_hero_heading_color' );
	$heading_size    = absint( tmnhanphat_get_hero_mod( 'tmnhanphat_hero_heading_font_size' ) );
	$heading_weight  = absint( tmnhanphat_get_hero_mod( 'tmnhanphat_hero_heading_font_weight' ) );

	$desc_color      = tmnhanphat_get_hero_mod( 'tmnhanphat_hero_description_color' );
	$desc_size       = absint( tmnhanphat_get_hero_mod( 'tmnhanphat_hero_description_font_size' ) );
	$desc_max_lines  = absint( tmnhanphat_get_hero_mod( 'tmnhanphat_hero_description_max_lines' ) );

	$btn_primary_bg          = tmnhanphat_get_hero_mod( 'tmnhanphat_hero_btn_primary_bg' );
	$btn_primary_text        = tmnhanphat_get_hero_mod( 'tmnhanphat_hero_btn_primary_text_color' );
	$btn_primary_hover_bg    = tmnhanphat_get_hero_mod( 'tmnhanphat_hero_btn_primary_hover_bg' );
	$btn_secondary_bg        = tmnhanphat_get_hero_mod( 'tmnhanphat_hero_btn_secondary_bg' );
	$btn_secondary_text      = tmnhanphat_get_hero_mod( 'tmnhanphat_hero_btn_secondary_text_color' );
	$btn_secondary_hover_bg  = tmnhanphat_get_hero_mod( 'tmnhanphat_hero_btn_secondary_hover_bg' );
	$btn_secondary_border    = tmnhanphat_get_hero_mod( 'tmnhanphat_hero_btn_secondary_border_color' );
	$btn_radius              = absint( tmnhanphat_get_hero_mod( 'tmnhanphat_hero_btn_border_radius' ) );
	$btn_padding_x           = absint( tmnhanphat_get_hero_mod( 'tmnhanphat_hero_btn_padding_x' ) );
	$btn_padding_y           = absint( tmnhanphat_get_hero_mod( 'tmnhanphat_hero_btn_padding_y' ) );

	$content_max_width        = absint( tmnhanphat_get_hero_mod( 'tmnhanphat_hero_content_max_width' ) );
	$content_max_width_tablet = absint( tmnhanphat_get_hero_mod( 'tmnhanphat_hero_content_max_width_tablet' ) );
	$content_max_width_mobile = absint( tmnhanphat_get_hero_mod( 'tmnhanphat_hero_content_max_width_mobile' ) );
	$content_align            = tmnhanphat_get_hero_mod( 'tmnhanphat_hero_content_align' );
	$height_desktop           = absint( tmnhanphat_get_hero_mod( 'tmnhanphat_hero_height_desktop' ) );

	$padding_top    = absint( tmnhanphat_get_hero_mod( 'tmnhanphat_hero_padding_top' ) );
	$padding_bottom = absint( tmnhanphat_get_hero_mod( 'tmnhanphat_hero_padding_bottom' ) );
	$padding_tablet = absint( tmnhanphat_get_hero_mod( 'tmnhanphat_hero_padding_tablet' ) );
	$padding_mobile = absint( tmnhanphat_get_hero_mod( 'tmnhanphat_hero_padding_mobile' ) );

	$heading_margin_bottom     = absint( tmnhanphat_get_hero_mod( 'tmnhanphat_hero_heading_margin_bottom' ) );
	$description_margin_bottom = absint( tmnhanphat_get_hero_mod( 'tmnhanphat_hero_description_margin_bottom' ) );
	$cta_margin_bottom         = absint( tmnhanphat_get_hero_mod( 'tmnhanphat_hero_cta_margin_bottom' ) );
	$stats_margin_top          = absint( tmnhanphat_get_hero_mod( 'tmnhanphat_hero_stats_margin_top' ) );

	$card_enable       = tmnhanphat_get_hero_mod( 'tmnhanphat_hero_stats_card_enable' );
	$card_bg           = tmnhanphat_get_hero_mod( 'tmnhanphat_hero_stats_card_bg' );
	$card_opacity      = absint( tmnhanphat_get_hero_mod( 'tmnhanphat_hero_stats_card_opacity' ) );
	$card_radius       = absint( tmnhanphat_get_hero_mod( 'tmnhanphat_hero_stats_card_radius' ) );
	$card_border_color = tmnhanphat_get_hero_mod( 'tmnhanphat_hero_stats_card_border_color' );
	$card_border_width = absint( tmnhanphat_get_hero_mod( 'tmnhanphat_hero_stats_card_border_width' ) );
	$card_blur         = absint( tmnhanphat_get_hero_mod( 'tmnhanphat_hero_stats_card_blur' ) );
	$card_shadow       = tmnhanphat_get_hero_stats_card_shadow_value( tmnhanphat_get_hero_mod( 'tmnhanphat_hero_stats_card_shadow' ) );

	$stats_number_color     = tmnhanphat_get_hero_mod( 'tmnhanphat_hero_stats_number_color' );
	$stats_badge_bg         = tmnhanphat_get_hero_mod( 'tmnhanphat_hero_stats_badge_bg' );
	$stats_badge_text_color = tmnhanphat_get_hero_mod( 'tmnhanphat_hero_stats_badge_text_color' );

	$anim_duration = absint( tmnhanphat_get_hero_mod( 'tmnhanphat_hero_animation_duration' ) );
	$anim_delay    = absint( tmnhanphat_get_hero_mod( 'tmnhanphat_hero_animation_delay' ) );

	$css  = ':root{';
	$css .= '--hero-fallback-bg:' . $fallback_bg . ';';
	$css .= '--hero-overlay-color:' . $overlay_color . ';';
	$css .= '--hero-overlay-opacity:' . ( $overlay_enable ? $overlay_opacity : 0 ) . ';';
	$css .= '--hero-overlay-blend:' . $overlay_blend . ';';
	$css .= '--hero-subtitle-color:' . $subtitle_color . ';';
	$css .= '--hero-subtitle-size:' . $subtitle_size . 'px;';
	$css .= '--hero-subtitle-weight:' . $subtitle_weight . ';';
	$css .= '--hero-heading-color:' . $heading_color . ';';
	$css .= '--hero-heading-size:' . $heading_size . 'px;';
	$css .= '--hero-heading-weight:' . $heading_weight . ';';
	$css .= '--hero-description-color:' . $desc_color . ';';
	$css .= '--hero-description-size:' . $desc_size . 'px;';
	$css .= '--hero-description-max-lines:' . $desc_max_lines . ';';
	$css .= '--hero-btn-primary-bg:' . $btn_primary_bg . ';';
	$css .= '--hero-btn-primary-text:' . $btn_primary_text . ';';
	$css .= '--hero-btn-primary-hover-bg:' . $btn_primary_hover_bg . ';';
	$css .= '--hero-btn-secondary-bg:' . $btn_secondary_bg . ';';
	$css .= '--hero-btn-secondary-text:' . $btn_secondary_text . ';';
	$css .= '--hero-btn-secondary-hover-bg:' . $btn_secondary_hover_bg . ';';
	// Figma: nút phụ viền 2px trắng ĐẶC (không phải 1px 40% như bản thiết kế cũ).
	$css .= '--hero-btn-secondary-border:2px solid ' . $btn_secondary_border . ';';
	$css .= '--hero-btn-radius:' . $btn_radius . 'px;';
	$css .= '--hero-btn-padding-x:' . $btn_padding_x . 'px;';
	$css .= '--hero-btn-padding-y:' . $btn_padding_y . 'px;';
	$css .= '--hero-content-max-width:' . $content_max_width . 'px;';
	$css .= '--hero-content-align:' . $content_align . ';';
	$css .= '--hero-height-desktop:' . $height_desktop . 'vh;';
	$css .= '--hero-padding-top:' . $padding_top . 'px;';
	$css .= '--hero-padding-bottom:' . $padding_bottom . 'px;';
	$css .= '--hero-heading-margin-bottom:' . $heading_margin_bottom . 'px;';
	$css .= '--hero-description-margin-bottom:' . $description_margin_bottom . 'px;';
	$css .= '--hero-cta-margin-bottom:' . $cta_margin_bottom . 'px;';
	$css .= '--hero-stats-margin-top:' . $stats_margin_top . 'px;';
	$css .= '--hero-stats-card-bg:' . ( $card_enable ? tmnhanphat_hex_to_rgba( $card_bg, $card_opacity ) : 'transparent' ) . ';';
	$css .= '--hero-stats-card-radius:' . ( $card_enable ? $card_radius : 0 ) . 'px;';
	// Viền rim mảnh 20% — card giờ đục (opacity 92) nên rim sáng chỉ cần nhẹ để gợi mép kính.
	$css .= '--hero-stats-card-border:' . ( $card_enable ? $card_border_width . 'px solid ' . tmnhanphat_hex_to_rgba( $card_border_color, 20 ) : 'none' ) . ';';
	$css .= '--hero-stats-card-blur:' . ( $card_enable ? $card_blur : 0 ) . 'px;';
	$css .= '--hero-stats-card-shadow:' . ( $card_enable ? $card_shadow : 'none' ) . ';';
	// Number/Badge/Description luôn đọc trực tiếp từ setting riêng (không còn phụ thuộc
	// Enable Background Card) — card giờ chỉ là lớp kính mờ (glass), không đổi từ sáng
	// sang tối như bản thiết kế cũ, nên chữ giữ nguyên 1 bộ màu bất kể Card bật/tắt.
	$css .= '--hero-stats-number-color:' . $stats_number_color . ';';
	$css .= '--hero-stats-desc-color:rgba(255,255,255,.75);';
	$css .= '--hero-stats-badge-bg:' . $stats_badge_bg . ';';
	$css .= '--hero-stats-badge-text:' . $stats_badge_text_color . ';';
	$css .= '--hero-animation-duration:' . $anim_duration . 'ms;';
	$css .= '--hero-animation-delay:' . $anim_delay . 'ms;';
	$css .= '}';

	$css .= '@media (max-width:991px){:root{';
	$css .= '--hero-padding-top:' . $padding_tablet . 'px;';
	$css .= '--hero-padding-bottom:' . $padding_tablet . 'px;';
	$css .= '--hero-content-max-width:' . $content_max_width_tablet . 'px;';
	$css .= '}}';

	$css .= '@media (max-width:599px){:root{';
	$css .= '--hero-padding-top:' . $padding_mobile . 'px;';
	$css .= '--hero-padding-bottom:' . $padding_mobile . 'px;';
	$css .= '--hero-content-max-width:' . $content_max_width_mobile . 'px;';
	$css .= '}}';

	return $css;
}

/* ==========================================================================
 * PARTNERS SECTION (trang chủ) — helper riêng, không đụng Header/Footer/Hero.
 * ========================================================================== */

/**
 * Số lượng "slot" Partner tối đa hỗ trợ trong Customizer.
 *
 * WordPress Customizer core không có control kiểu Repeater (thêm/bớt item động) —
 * đây KHÔNG phải plugin/thư viện ngoài nên không dùng được (PROJECT_RULES.md mục 21).
 * Giải pháp chuẩn khi chỉ dùng Core Customizer: khai báo sẵn N slot cố định, mỗi slot
 * có Enable riêng — admin chỉ bật/điền đúng số lượng cần dùng, slot tắt hoặc chưa có
 * Logo sẽ không render (xem tmnhanphat_get_partners()) nên KHÔNG bị hardcode số lượng
 * hiển thị ngoài site, chỉ giới hạn số lượng CÓ THỂ cấu hình. Muốn nhiều hơn 8, tăng
 * số này lên rồi thêm tương ứng trong tmnhanphat_partners_defaults()/partners-customizer.php.
 *
 * @return int
 */
function tmnhanphat_get_partners_slot_count() {
	return 8;
}

/**
 * Giá trị mặc định cho toàn bộ setting Partners Section Customizer.
 *
 * @return array<string, mixed>
 */
function tmnhanphat_partners_defaults() {
	$defaults = array(
		// General
		// Figma: CÓ heading "Đối Tác" (85/900 #046AB5, style ở partners.css); padding trên
		// = 0 vì khoảng 57px với section trước đã nằm trong padding-bottom của Products
		// (đáy section = 73px, cố định ở CSS).
		'tmnhanphat_partners_enable'             => true,
		'tmnhanphat_partners_title_enable'       => true,
		'tmnhanphat_partners_title'              => __( 'Đối Tác', 'tmnhanphat' ),
		'tmnhanphat_partners_description_enable' => false,
		'tmnhanphat_partners_description'        => '',
		// Đỉnh 64px (Taste review mục 6, owner duyệt): Figma để 0 → section nghẹt sát
		// vai About, là điểm gãy nhịp thở dọc duy nhất của trang (các section khác 36-203).
		'tmnhanphat_partners_padding_desktop'    => 48, // Migrate: nhịp dọc mới (64→48).
		'tmnhanphat_partners_padding_tablet'     => 48,
		'tmnhanphat_partners_padding_mobile'     => 32,
		'tmnhanphat_partners_bg'                 => '#ffffff',

		// Display Settings — giới hạn SỐ LƯỢNG Partner render, KHÔNG liên quan số cột.
		'tmnhanphat_partners_display_limit' => 6,

		// Layout — CHỈ quyết định số cột hiển thị, KHÔNG quyết định số Partner.
		'tmnhanphat_partners_columns_desktop' => 6,
		'tmnhanphat_partners_columns_tablet'  => 3,
		'tmnhanphat_partners_columns_mobile'  => 2,
		// Figma: card logo 284×120, radius 20, viền 1px #A8AEB8, gap giữa card 21.
		'tmnhanphat_partners_gap_desktop'     => 21,
		'tmnhanphat_partners_gap_tablet'      => 16,
		'tmnhanphat_partners_gap_mobile'      => 12,
		'tmnhanphat_partners_card_radius'     => 20,
		'tmnhanphat_partners_card_padding'    => 20,
		'tmnhanphat_partners_card_border'     => '#A8AEB8',
		'tmnhanphat_partners_hover_enable'    => true,
	);

	for ( $i = 1; $i <= tmnhanphat_get_partners_slot_count(); $i++ ) {
		// Mặc định BẬT (giống mọi Enable khác trong theme: Email/Phone/Address ở Footer,
		// Company Stats ở Hero...) — Enable ở đây dùng để TẠM ẨN 1 partner đã có Logo mà
		// không cần xoá ảnh, không phải điều kiện "phải bật tay mới hiện" (opt-out, không
		// phải opt-in) — tránh trường hợp admin upload Logo xong tưởng xong nhưng vẫn ẩn.
		$defaults[ "tmnhanphat_partner{$i}_enable" ]   = true;
		$defaults[ "tmnhanphat_partner{$i}_logo" ]     = '';
		/* translators: %d: số thứ tự đối tác (1-8) */
		$defaults[ "tmnhanphat_partner{$i}_alt" ]      = sprintf( __( 'Đối tác %d', 'tmnhanphat' ), $i );
		$defaults[ "tmnhanphat_partner{$i}_link" ]     = '';
		$defaults[ "tmnhanphat_partner{$i}_new_tab" ]  = false;
		$defaults[ "tmnhanphat_partner{$i}_order" ]    = $i * 10;
	}

	return $defaults;
}

/**
 * Đọc 1 theme_mod của Partners kèm fallback lấy từ tmnhanphat_partners_defaults().
 *
 * @param string $key Tên setting (khớp key trong tmnhanphat_partners_defaults()).
 * @return mixed
 */
function tmnhanphat_get_partners_mod( $key ) {
	$defaults = tmnhanphat_partners_defaults();
	$default  = isset( $defaults[ $key ] ) ? $defaults[ $key ] : '';

	return get_theme_mod( $key, $default );
}

/**
 * Danh sách lựa chọn "Number of Columns (Desktop)" cho phép chọn ở Customizer.
 *
 * @return array<string, string>
 */
function tmnhanphat_get_partners_column_choices() {
	return array(
		'4' => '4',
		'5' => '5',
		'6' => '6',
		'7' => '7',
		'8' => '8',
	);
}

/**
 * Danh sách lựa chọn "Tablet Columns" cho phép chọn ở Customizer.
 *
 * @return array<string, string>
 */
function tmnhanphat_get_partners_tablet_column_choices() {
	return array(
		'2' => '2',
		'3' => '3',
		'4' => '4',
	);
}

/**
 * Danh sách lựa chọn "Mobile Columns" cho phép chọn ở Customizer.
 *
 * @return array<string, string>
 */
function tmnhanphat_get_partners_mobile_column_choices() {
	return array(
		'1' => '1',
		'2' => '2',
		'3' => '3',
	);
}

/**
 * Xây danh sách Partner ĐÃ BẬT + CÓ LOGO, sắp xếp theo Display Order tăng dần, rồi CẮT
 * đúng theo "Number of Partners" (Display Limit) bằng array_slice() — giới hạn ở tầng DỮ
 * LIỆU, không phải ẩn bằng CSS. Partner thứ (limit+1) trở đi KHÔNG được đưa vào HTML.
 *
 * Nếu Display Limit lớn hơn số Partner thực sự có (đã bật + có Logo), array_slice() tự
 * nhiên trả về toàn bộ số đang có — không tạo thêm item rỗng/placeholder (mục "Empty Partner").
 *
 * @return array<int, array{logo: string, alt: string, link: string, new_tab: bool}>
 */
function tmnhanphat_get_partners() {
	$partners = array();

	for ( $i = 1; $i <= tmnhanphat_get_partners_slot_count(); $i++ ) {
		$enabled = tmnhanphat_get_partners_mod( "tmnhanphat_partner{$i}_enable" );
		$logo    = tmnhanphat_get_partners_mod( "tmnhanphat_partner{$i}_logo" );

		if ( ! $enabled || ! $logo ) {
			continue;
		}

		$partners[] = array(
			'logo'    => $logo,
			'alt'     => tmnhanphat_get_partners_mod( "tmnhanphat_partner{$i}_alt" ),
			'link'    => tmnhanphat_get_partners_mod( "tmnhanphat_partner{$i}_link" ),
			'new_tab' => (bool) tmnhanphat_get_partners_mod( "tmnhanphat_partner{$i}_new_tab" ),
			'order'   => absint( tmnhanphat_get_partners_mod( "tmnhanphat_partner{$i}_order" ) ),
		);
	}

	usort(
		$partners,
		static function ( $a, $b ) {
			return $a['order'] <=> $b['order'];
		}
	);

	$display_limit = absint( tmnhanphat_get_partners_mod( 'tmnhanphat_partners_display_limit' ) );

	return array_slice( $partners, 0, max( 1, $display_limit ) );
}

/**
 * Sinh chuỗi CSS custom properties cho Partners Section, gắn vào stylesheet qua
 * wp_add_inline_style() trong inc/enqueue.php — cùng quy ước Header/Footer/Hero (mục 22).
 *
 * Số cột (Desktop/Tablet/Mobile) là setting CỐ ĐỊNH, độc lập hoàn toàn với số Partner đang
 * hiển thị — KHÔNG còn auto-shrink theo count như bản cũ (đã refactor). Việc căn giữa hàng
 * cuối khi số Partner không chia hết cho số cột do CSS (Flexbox + justify-content:center
 * trong assets/css/components/partners.css) đảm nhiệm, không cần tính toán số cột động ở đây.
 *
 * @return string
 */
function tmnhanphat_render_partners_css_vars() {
	$columns_desktop = absint( tmnhanphat_get_partners_mod( 'tmnhanphat_partners_columns_desktop' ) );
	$columns_tablet  = absint( tmnhanphat_get_partners_mod( 'tmnhanphat_partners_columns_tablet' ) );
	$columns_mobile  = absint( tmnhanphat_get_partners_mod( 'tmnhanphat_partners_columns_mobile' ) );

	$padding_desktop = absint( tmnhanphat_get_partners_mod( 'tmnhanphat_partners_padding_desktop' ) );
	$padding_tablet  = absint( tmnhanphat_get_partners_mod( 'tmnhanphat_partners_padding_tablet' ) );
	$padding_mobile  = absint( tmnhanphat_get_partners_mod( 'tmnhanphat_partners_padding_mobile' ) );
	$bg              = tmnhanphat_get_partners_mod( 'tmnhanphat_partners_bg' );

	$gap_desktop = absint( tmnhanphat_get_partners_mod( 'tmnhanphat_partners_gap_desktop' ) );
	$gap_tablet  = absint( tmnhanphat_get_partners_mod( 'tmnhanphat_partners_gap_tablet' ) );
	$gap_mobile  = absint( tmnhanphat_get_partners_mod( 'tmnhanphat_partners_gap_mobile' ) );

	$card_radius = absint( tmnhanphat_get_partners_mod( 'tmnhanphat_partners_card_radius' ) );
	$card_padding = absint( tmnhanphat_get_partners_mod( 'tmnhanphat_partners_card_padding' ) );
	$card_border  = tmnhanphat_get_partners_mod( 'tmnhanphat_partners_card_border' );

	$css  = ':root{';
	$css .= '--partners-cols-desktop:' . max( 1, $columns_desktop ) . ';';
	$css .= '--partners-cols-tablet:' . max( 1, $columns_tablet ) . ';';
	$css .= '--partners-cols-mobile:' . max( 1, $columns_mobile ) . ';';
	$css .= '--partners-padding-desktop:' . $padding_desktop . 'px;';
	$css .= '--partners-padding-tablet:' . $padding_tablet . 'px;';
	$css .= '--partners-padding-mobile:' . $padding_mobile . 'px;';
	$css .= '--partners-bg:' . $bg . ';';
	$css .= '--partners-gap-desktop:' . $gap_desktop . 'px;';
	$css .= '--partners-gap-tablet:' . $gap_tablet . 'px;';
	$css .= '--partners-gap-mobile:' . $gap_mobile . 'px;';
	$css .= '--partners-card-radius:' . $card_radius . 'px;';
	$css .= '--partners-card-padding:' . $card_padding . 'px;';
	$css .= '--partners-card-border:' . $card_border . ';';
	$css .= '}';

	return $css;
}

/**
 * Render 1 logo Partner đúng chuẩn WordPress (PROJECT_RULES.md mục 13): quy URL lưu trong
 * Customizer về attachment ID để dùng wp_get_attachment_image() với size không crop
 * 'tmnhanphat-partner-logo' (đúng kích thước cần, có srcset/lazy) — nếu không resolve được
 * ID (ảnh URL ngoài, không thuộc Media Library) thì fallback về <img> thường.
 *
 * @param string $url URL logo lấy từ theme_mod.
 * @param string $alt Alt text.
 * @return string HTML đã escape, sẵn sàng echo.
 */
function tmnhanphat_get_partner_logo_html( $url, $alt ) {
	$attachment_id = attachment_url_to_postid( $url );

	if ( $attachment_id ) {
		return wp_get_attachment_image( $attachment_id, 'tmnhanphat-partner-logo', false, array(
			'class' => 'partners__logo',
			'alt'   => $alt,
		) );
	}

	return sprintf(
		'<img class="partners__logo" src="%s" alt="%s" loading="lazy" decoding="async" />',
		esc_url( $url ),
		esc_attr( $alt )
	);
}

/* ==========================================================================
 * GLOBAL LAYOUT (toàn site) — nguồn dữ liệu DUY NHẤT cho khoảng cách trái/phải,
 * dùng chung bởi Header, Hero, Partner, Footer, mọi Homepage Section, Archive,
 * Single, Page và mọi template khác (đều đọc chung 1 biến --container-padding,
 * xem PROJECT_RULES.md mục 27). Không đụng logic Header/Hero/Partner/Footer.
 * ========================================================================== */

/**
 * Giá trị mặc định cho toàn bộ setting Global Layout Customizer.
 *
 * @return array<string, mixed>
 */
function tmnhanphat_global_defaults() {
	return array(
		// Migrate container: trần 1200px (từ 1684 cũ). Nội dung ~1152 (1200 − 2×24). Toàn bộ
		// Design System re-scale theo Scale Guideline (fig1px cap 0.71 + giá trị optical).
		'tmnhanphat_container_width'            => 1200,
		'tmnhanphat_container_padding_desktop' => 24,
		'tmnhanphat_container_padding_tablet'  => 20,
		'tmnhanphat_container_padding_mobile'  => 16,
		// Global Section Spacing — ÁP DỤNG ĐỒNG LOẠT padding trên/dưới cho MỌI section
		// trang chủ. Mặc định 0 = KHÔNG override (mỗi section giữ padding riêng như hiện
		// tại) → không đổi giao diện cho tới khi admin đặt giá trị > 0 (mục XIV).
		'tmnhanphat_home_section_pt_desktop'   => 0,
		'tmnhanphat_home_section_pb_desktop'   => 0,
		'tmnhanphat_home_section_pt_tablet'    => 0,
		'tmnhanphat_home_section_pb_tablet'    => 0,
		'tmnhanphat_home_section_pt_mobile'    => 0,
		'tmnhanphat_home_section_pb_mobile'    => 0,
	);
}

/**
 * Đọc 1 theme_mod của Global Layout kèm fallback lấy từ tmnhanphat_global_defaults().
 *
 * @param string $key Tên setting (khớp key trong tmnhanphat_global_defaults()).
 * @return mixed
 */
function tmnhanphat_get_global_mod( $key ) {
	$defaults = tmnhanphat_global_defaults();
	$default  = isset( $defaults[ $key ] ) ? $defaults[ $key ] : '';

	return get_theme_mod( $key, $default );
}

/**
 * Sinh chuỗi CSS custom property --container-max-width + --container-padding (theo 3
 * breakpoint), gắn vào stylesheet "tmnhanphat-variables" (load ở MỌI trang, xem
 * inc/enqueue.php) qua wp_add_inline_style() — cùng quy ước Header/Footer/Hero/Partner
 * (mục 22).
 *
 * LƯU Ý phân biệt 2 khái niệm (mục 27): "Container Padding" là khoảng đệm BÊN TRONG
 * khung nội dung; "Container Width" là chính chiều rộng tối đa của khung đó (canh giữa
 * bằng margin:auto) — set Padding = 0 không làm khung rộng ra hết viewport nếu Width
 * vẫn nhỏ hơn viewport, đây là 2 biến độc lập, không phải bug.
 *
 * `--header-container-width` của Header là setting RIÊNG (Header Home → Layout), CỐ Ý
 * độc lập với `--container-width` toàn site để Header có thể có bố cục khác Body nếu
 * admin muốn — không bị ghi đè bởi Global Settings này.
 *
 * Vì mọi component còn lại (Hero, Partner, Footer, .container dùng ở Archive/Single/
 * Page/Search/404) đều đã đọc chung `var(--container-max-width)`/`var(--container-padding)`
 * từ trước, chỉ cần override lại giá trị 2 biến này ở :root là toàn site tự động áp dụng
 * — không cần sửa bất kỳ file CSS/HTML nào của từng section.
 *
 * @return string
 */
function tmnhanphat_render_global_css_vars() {
	$container_width = absint( tmnhanphat_get_global_mod( 'tmnhanphat_container_width' ) );
	$padding_desktop = absint( tmnhanphat_get_global_mod( 'tmnhanphat_container_padding_desktop' ) );
	$padding_tablet  = absint( tmnhanphat_get_global_mod( 'tmnhanphat_container_padding_tablet' ) );
	$padding_mobile  = absint( tmnhanphat_get_global_mod( 'tmnhanphat_container_padding_mobile' ) );

	// Container FLUID theo yêu cầu chủ dự án: khoảng hở 2 bên = MỘT NỬA khoảng hở của
	// container 1200 cũ ((100vw-1200)/4 mỗi bên ⇒ width = 50vw+600px), chặn trần bằng
	// Container Width setting (mặc định 1684 = cột nội dung Figma 1636 + 2×24 padding):
	// 1920 → hở 180px/bên; 1366 → 41px/bên; ≤1200 → full-width (tablet/mobile như cũ);
	// ≥2168 → khoá 1684px đúng Figma, nội dung không giãn vô hạn trên 2K/4K.
	$css  = ':root{--container-max-width:min(' . $container_width . 'px, 50vw + 600px);--container-padding:' . $padding_desktop . 'px;}';
	$css .= '@media (max-width:991px){:root{--container-padding:' . $padding_tablet . 'px;}}';
	$css .= '@media (max-width:599px){:root{--container-padding:' . $padding_mobile . 'px;}}';

	$css .= tmnhanphat_render_home_section_spacing_css();

	return $css;
}

/**
 * Global Section Spacing (Homepage) — override padding trên/dưới đồng loạt cho MỌI
 * section trang chủ khi admin bật (giá trị > 0). Selector `.site-main.front-page > section`
 * có độ đặc hiệu (0,2,1) cao hơn class đơn của từng section (0,1,0) nên thắng mà KHÔNG
 * cần !important. Mỗi breakpoint/chiều chỉ phát rule khi giá trị > 0 → mặc định 0 =
 * không có rule = giữ nguyên padding riêng của từng section (mục XIV: default không đổi UI).
 *
 * @return string
 */
function tmnhanphat_render_home_section_spacing_css() {
	if ( ! is_front_page() ) {
		return ''; // Chỉ áp dụng ở Homepage — không ảnh hưởng Archive/Single/Page.
	}

	$map = array(
		'desktop' => array( 'pt' => 'tmnhanphat_home_section_pt_desktop', 'pb' => 'tmnhanphat_home_section_pb_desktop', 'mq' => '' ),
		'tablet'  => array( 'pt' => 'tmnhanphat_home_section_pt_tablet',  'pb' => 'tmnhanphat_home_section_pb_tablet',  'mq' => '@media (max-width:991px)' ),
		'mobile'  => array( 'pt' => 'tmnhanphat_home_section_pt_mobile',  'pb' => 'tmnhanphat_home_section_pb_mobile',  'mq' => '@media (max-width:599px)' ),
	);

	$css = '';

	foreach ( $map as $bp ) {
		$pt = absint( tmnhanphat_get_global_mod( $bp['pt'] ) );
		$pb = absint( tmnhanphat_get_global_mod( $bp['pb'] ) );

		$decls = '';
		if ( $pt > 0 ) {
			$decls .= 'padding-top:' . $pt . 'px;';
		}
		if ( $pb > 0 ) {
			$decls .= 'padding-bottom:' . $pb . 'px;';
		}

		if ( '' === $decls ) {
			continue;
		}

		$rule = '.site-main.front-page > section{' . $decls . '}';
		$css .= '' === $bp['mq'] ? $rule : $bp['mq'] . '{' . $rule . '}';
	}

	return $css;
}

/* ==========================================================================
 * ABOUT COMPANY SECTION (trang chủ) — helper riêng, không đụng Hero/Partner/
 * Footer/Global Layout. Panel Customizer riêng "About Home" (mục 26).
 *
 * BẢN V3: dùng ẢNH THIẾT KẾ THẬT do designer xuất từ Figma (Rectangle 19.png làm nền góc
 * cạnh, ảnh thang máy PNG nền trong suốt đè lên trên) — KHÔNG còn tự vẽ hình bằng clip-path/
 * polygon()/skew()/SVG như bản v1-v2. Xem PROJECT_RULES.md mục 28 (bản v3).
 * ========================================================================== */

/**
 * Giá trị mặc định cho toàn bộ setting About Company Customizer — dùng chung bởi
 * inc/customizer/about-customizer.php (default control) và tmnhanphat_get_about_mod()
 * (fallback khi setting chưa từng được lưu).
 *
 * @return array<string, mixed>
 */
function tmnhanphat_about_defaults() {
	return array(
		// General
		'tmnhanphat_about_enable'           => true,
		'tmnhanphat_about_section_id'       => 'about',
		// 0 = dùng chung Container Width của Global Settings (mục 27), không hardcode riêng.
		'tmnhanphat_about_container_width'  => 0,

		// Content — Content Width/Max Description Width/Vertical Alignment gộp chung vào đây
		// (bản v1-v2 từng để ở Section "Layout" riêng, nay không còn khái niệm "Layout 2 cột").
		'tmnhanphat_about_label'                 => __( 'Về chúng tôi', 'tmnhanphat' ),
		'tmnhanphat_about_heading'                => __( 'Thang Máy Nhân Phát', 'tmnhanphat' ),
		'tmnhanphat_about_description'             => __( 'Ra đời với sứ mệnh nâng tầm chất lượng sống, Thang Máy Nhân Phát chuyên cung cấp các dòng thang máy hiện đại, an toàn, phù hợp với mọi loại công trình. Chúng tôi tự hào sở hữu đội ngũ kỹ thuật viên chuyên môn cao, dịch vụ hậu mãi tận tâm và quy trình lắp đặt - bảo trì đạt chuẩn quốc tế, tạo dựng niềm tin vững chắc với khách hàng trong suốt nhiều năm qua.', 'tmnhanphat' ),
		'tmnhanphat_about_text_width'              => 57,
		'tmnhanphat_about_description_max_width'   => 921, // Figma: box Description 921px.
		'tmnhanphat_about_vertical_align'          => 'center',

		// Background Shape — Upload Shape Image (Rectangle 19.png) làm background-image cho
		// .about-home__shape (thuần decoration, KHÔNG phải container). Fallback Background
		// Color hiển thị khi chưa upload ảnh, tránh section trống trơn không có gì.
		// Figma: polygon Rectangle 19 fill #046AB5 (xanh brand — không phải đỏ bản cũ).
		'tmnhanphat_about_bg_color'          => '#046AB5',
		'tmnhanphat_about_shape_enable'      => true,
		'tmnhanphat_about_shape_image'       => '',
		'tmnhanphat_about_shape_bg_size'     => '100% 100%',
		'tmnhanphat_about_shape_bg_position' => 'center',
		'tmnhanphat_about_shape_opacity'     => 100,

		// Elevator Image — 1 LAYER position:absolute riêng, KHÔNG nằm trong Grid/Flex nào.
		// "Elevator Size" (Desktop/Tablet/Mobile) là % CHIỀU CAO THAM CHIẾU của section
		// (--about-home-height trong about.css), KHÔNG phải % chiều rộng cột — vì ảnh dùng
		// object-fit:contain co theo chiều cao (giữ nguyên tỉ lệ, không méo/crop) — xem
		// ghi chú kỹ thuật trong tmnhanphat_render_about_css_vars().
		// Elevator Size (%) — từ audit lần 2 (hệ toạ độ Figma trong about.css): là hệ số so với
		// kích thước THIẾT KẾ GỐC 873×873 của asset (100 = đúng thiết kế), không còn là % chiều
		// cao section. Responsive co giãn tự động qua --about-fpx nên cả 3 breakpoint đều 100.
		'tmnhanphat_about_image'               => '',
		'tmnhanphat_about_image_width_desktop' => 100,
		'tmnhanphat_about_image_width_tablet'  => 100,
		'tmnhanphat_about_image_width_mobile'  => 100,
		'tmnhanphat_about_image_offset_x'      => 0,
		'tmnhanphat_about_image_offset_y'      => 0,
		'tmnhanphat_about_elevator_zindex'     => 3,

		// Responsive
		'tmnhanphat_about_mobile_layout' => 'text_first',
	);
}

/**
 * Đọc 1 theme_mod của About kèm fallback lấy từ tmnhanphat_about_defaults().
 *
 * @param string $key Tên setting (khớp key trong tmnhanphat_about_defaults()).
 * @return mixed
 */
function tmnhanphat_get_about_mod( $key ) {
	$defaults = tmnhanphat_about_defaults();
	$default  = isset( $defaults[ $key ] ) ? $defaults[ $key ] : '';

	return get_theme_mod( $key, $default );
}

/**
 * Danh sách lựa chọn Vertical Alignment cho phép chọn ở Customizer.
 *
 * @return array<string, string>
 */
function tmnhanphat_get_about_vertical_align_choices() {
	return array(
		'flex-start' => __( 'Đầu (Top)', 'tmnhanphat' ),
		'center'     => __( 'Giữa (Center)', 'tmnhanphat' ),
		'flex-end'   => __( 'Cuối (Bottom)', 'tmnhanphat' ),
	);
}

/**
 * Danh sách lựa chọn Mobile Layout Order cho phép chọn ở Customizer.
 *
 * @return array<string, string>
 */
function tmnhanphat_get_about_mobile_layout_choices() {
	return array(
		'text_first'  => __( 'Text trước, Ảnh sau', 'tmnhanphat' ),
		'image_first' => __( 'Ảnh trước, Text sau', 'tmnhanphat' ),
	);
}

/**
 * Whitelist "Mobile Layout".
 *
 * @param string $value Giá trị gửi lên từ Customizer.
 * @return string
 */
function tmnhanphat_sanitize_about_mobile_layout( $value ) {
	$choices = array_keys( tmnhanphat_get_about_mobile_layout_choices() );

	return in_array( $value, $choices, true ) ? $value : 'text_first';
}

/**
 * Whitelist "Vertical Alignment".
 *
 * @param string $value Giá trị gửi lên từ Customizer.
 * @return string
 */
function tmnhanphat_sanitize_about_vertical_align( $value ) {
	$choices = array_keys( tmnhanphat_get_about_vertical_align_choices() );

	return in_array( $value, $choices, true ) ? $value : 'center';
}

/**
 * Danh sách lựa chọn "Background Size" cho ảnh Shape (Rectangle 19.png) — khớp đúng 3 chế độ
 * chuẩn CSS background-size, KHÔNG tự thêm giá trị lạ để tránh méo/crop ngoài ý muốn.
 *
 * @return array<string, string>
 */
function tmnhanphat_get_about_bg_size_choices() {
	return array(
		'100% 100%' => __( 'Lấp đầy đúng khung (100% 100% — có thể co giãn nhẹ theo tỉ lệ khung)', 'tmnhanphat' ),
		'cover'     => __( 'Cover (lấp đầy, có thể crop cạnh thừa)', 'tmnhanphat' ),
		'contain'   => __( 'Contain (giữ nguyên tỉ lệ ảnh, có thể dư khoảng trống)', 'tmnhanphat' ),
	);
}

/**
 * Danh sách lựa chọn "Background Position" cho ảnh Shape.
 *
 * @return array<string, string>
 */
function tmnhanphat_get_about_bg_position_choices() {
	return array(
		'center'       => __( 'Giữa', 'tmnhanphat' ),
		'top'          => __( 'Trên', 'tmnhanphat' ),
		'bottom'       => __( 'Dưới', 'tmnhanphat' ),
		'left'         => __( 'Trái', 'tmnhanphat' ),
		'right'        => __( 'Phải', 'tmnhanphat' ),
		'top left'     => __( 'Trên trái', 'tmnhanphat' ),
		'top right'    => __( 'Trên phải', 'tmnhanphat' ),
		'bottom left'  => __( 'Dưới trái', 'tmnhanphat' ),
		'bottom right' => __( 'Dưới phải', 'tmnhanphat' ),
	);
}

/**
 * Whitelist "Background Size".
 *
 * @param string $value Giá trị gửi lên từ Customizer.
 * @return string
 */
function tmnhanphat_sanitize_about_bg_size( $value ) {
	$choices = array_keys( tmnhanphat_get_about_bg_size_choices() );

	return in_array( $value, $choices, true ) ? $value : '100% 100%';
}

/**
 * Whitelist "Background Position".
 *
 * @param string $value Giá trị gửi lên từ Customizer.
 * @return string
 */
function tmnhanphat_sanitize_about_bg_position( $value ) {
	$choices = array_keys( tmnhanphat_get_about_bg_position_choices() );

	return in_array( $value, $choices, true ) ? $value : 'center';
}

/**
 * Render ảnh thang máy đúng chuẩn WordPress (mục 13): quy URL Customizer về attachment ID
 * để dùng wp_get_attachment_image() với size không crop 'tmnhanphat-about-image' (giữ tỉ lệ
 * gốc, có srcset/lazy) — nếu không resolve được ID thì fallback về <img> thường. PNG nền
 * trong suốt do designer xuất — không xử lý gì thêm, hiển thị nguyên trạng.
 *
 * @param string $url URL ảnh lấy từ theme_mod.
 * @param string $alt Alt text.
 * @return string HTML đã escape, sẵn sàng echo.
 */
function tmnhanphat_get_about_image_html( $url, $alt ) {
	$attachment_id = attachment_url_to_postid( $url );

	if ( $attachment_id ) {
		return wp_get_attachment_image( $attachment_id, 'tmnhanphat-about-image', false, array(
			'class' => 'about-home__image-el',
			'alt'   => $alt,
		) );
	}

	return sprintf(
		'<img class="about-home__image-el" src="%s" alt="%s" loading="lazy" decoding="async" />',
		esc_url( $url ),
		esc_attr( $alt )
	);
}

/**
 * Sinh chuỗi CSS custom properties cho About Company Section, gắn vào stylesheet qua
 * wp_add_inline_style() trong inc/enqueue.php — cùng quy ước Header/Footer/Hero/Partner (mục 22).
 *
 * GHI CHÚ KỸ THUẬT (mục 28, bản v3): Ảnh Elevator vẫn `position:absolute` (1 LAYER riêng,
 * không nằm trong `.container`/Grid/Flex nào). `.about-home` có `height:auto` (chiều cao tự
 * nhiên theo Text) nên `height` dạng PHẦN TRĂM trên chính `.about-home` sẽ KHÔNG resolve đúng
 * chuẩn CSS cho ảnh Elevator. Giải pháp: khai báo 1 custom property THAM CHIẾU cố định
 * `--about-home-height` (khai báo trực tiếp trong about.css bằng `clamp()`, không qua PHP) rồi
 * cả `.about-home` (min-height) LẪN `.about-home__image` (height, nhân với tỉ lệ % ở đây) đều
 * dùng CHUNG biến này qua `calc()` — không phụ thuộc việc resolve % trên containing block,
 * luôn nhất quán giữa 2 phía dù `.about-home` có cao hơn tham chiếu vì nội dung dài.
 *
 * @return string
 */
function tmnhanphat_render_about_css_vars() {
	$container_width = absint( tmnhanphat_get_about_mod( 'tmnhanphat_about_container_width' ) );
	$about_container  = $container_width > 0 ? $container_width . 'px' : 'var(--container-max-width)';

	$text_width        = absint( tmnhanphat_get_about_mod( 'tmnhanphat_about_text_width' ) );
	$description_max_w = absint( tmnhanphat_get_about_mod( 'tmnhanphat_about_description_max_width' ) );
	$vertical_align    = tmnhanphat_get_about_mod( 'tmnhanphat_about_vertical_align' );

	$bg_color        = tmnhanphat_get_about_mod( 'tmnhanphat_about_bg_color' );
	$shape_enable    = tmnhanphat_get_about_mod( 'tmnhanphat_about_shape_enable' );
	$shape_image_url = tmnhanphat_get_about_mod( 'tmnhanphat_about_shape_image' );
	$shape_bg_size   = tmnhanphat_get_about_mod( 'tmnhanphat_about_shape_bg_size' );
	$shape_bg_pos    = tmnhanphat_get_about_mod( 'tmnhanphat_about_shape_bg_position' );
	$shape_opacity   = absint( tmnhanphat_get_about_mod( 'tmnhanphat_about_shape_opacity' ) ) / 100;

	$elevator_ratio_desktop = absint( tmnhanphat_get_about_mod( 'tmnhanphat_about_image_width_desktop' ) ) / 100;
	$elevator_ratio_tablet  = absint( tmnhanphat_get_about_mod( 'tmnhanphat_about_image_width_tablet' ) ) / 100;
	$elevator_ratio_mobile  = absint( tmnhanphat_get_about_mod( 'tmnhanphat_about_image_width_mobile' ) ) / 100;

	$offset_x   = (int) tmnhanphat_get_about_mod( 'tmnhanphat_about_image_offset_x' );
	$offset_y   = (int) tmnhanphat_get_about_mod( 'tmnhanphat_about_image_offset_y' );
	$elevator_z = absint( tmnhanphat_get_about_mod( 'tmnhanphat_about_elevator_zindex' ) );

	$css  = ':root{';
	$css .= '--about-container-width:' . $about_container . ';';
	$css .= '--about-text-width:' . $text_width . '%;';
	$css .= '--about-description-max-width:' . $description_max_w . 'px;';
	$css .= '--about-vertical-align:' . $vertical_align . ';';
	// Fallback color CHỈ khi chưa upload Shape Image — ảnh polygon PNG có vùng TRONG SUỐT
	// (góc vát), nếu vẫn tô màu nền đằng sau thì các góc trong suốt lộ màu → polygon
	// biến thành khối chữ nhật đặc (bug đã gặp khi fallback đổi sang xanh brand).
	$css .= '--about-background:' . ( $shape_image_url ? 'transparent' : $bg_color ) . ';';
	$css .= '--about-shape-display:' . ( $shape_enable ? 'block' : 'none' ) . ';';
	$css .= '--about-shape-image:' . ( $shape_image_url ? "url('" . esc_url( $shape_image_url ) . "')" : 'none' ) . ';';
	$css .= '--about-shape-bg-size:' . $shape_bg_size . ';';
	$css .= '--about-shape-bg-position:' . $shape_bg_pos . ';';
	$css .= '--about-shape-opacity:' . $shape_opacity . ';';
	$css .= '--about-elevator-ratio:' . $elevator_ratio_desktop . ';';
	$css .= '--about-image-offset-x:' . $offset_x . 'px;';
	$css .= '--about-image-offset-y:' . $offset_y . 'px;';
	$css .= '--about-elevator-zindex:' . $elevator_z . ';';
	$css .= '}';

	$css .= '@media (max-width:991px){:root{--about-elevator-ratio:' . $elevator_ratio_tablet . ';}}';
	$css .= '@media (max-width:599px){:root{--about-elevator-ratio:' . $elevator_ratio_mobile . ';}}';

	return $css;
}

/* ==========================================================================
 * SERVICES SECTION (trang chủ) — helper riêng, dữ liệu từ CPT tmnp_service
 * (inc/post-types.php), không đụng Header/Footer/Hero/Partner/About.
 * ========================================================================== */

/**
 * Giá trị mặc định cho toàn bộ setting Services Home Section Customizer.
 *
 * @return array<string, mixed>
 */
function tmnhanphat_services_defaults() {
	return array(
		// General
		'tmnhanphat_services_enable'          => true,
		'tmnhanphat_services_section_id'      => 'services',
		'tmnhanphat_services_bg_color'        => '#ffffff',
		// 0 = dùng chung Container Width của Global Settings (mục 27).
		'tmnhanphat_services_container_width' => 0,
		// Figma: khoảng cách đáy Hero→Heading = 137 (padding-top; đáy section 47 cố định ở CSS).
		'tmnhanphat_services_padding_desktop' => 96, // Migrate: nhịp dọc mới (137→96).
		'tmnhanphat_services_padding_tablet'  => 56,
		'tmnhanphat_services_padding_mobile'  => 40,

		// Heading — 2 phần text 2 màu trên cùng 1 dòng (đỏ + xanh), tách riêng để đổi độc lập.
		// Figma: Inter 85 / weight 900; Heading → Description cách 37px.
		'tmnhanphat_services_heading_red_text'      => 'Dịch vụ tại',
		'tmnhanphat_services_heading_blue_text'     => 'Thang Máy Nhân Phát',
		'tmnhanphat_services_heading_red_color'     => '#E31F2B',
		'tmnhanphat_services_heading_blue_color'    => '#046AB5',
		'tmnhanphat_services_heading_font_size'     => 85,
		'tmnhanphat_services_heading_font_weight'   => '900',
		'tmnhanphat_services_heading_align'         => 'center',
		'tmnhanphat_services_heading_margin_bottom' => 37,

		// Description
		'tmnhanphat_services_description_text'          => 'Cung cấp đầy đủ các dịch vụ từ tư vấn thiết kế, lắp đặt, bảo trì đến sửa chữa và cung cấp linh kiện thay thế. Với đội ngũ kỹ thuật giàu kinh nghiệm cùng quy trình làm việc chuyên nghiệp, chúng tôi cam kết mang đến giải pháp thang máy an toàn, bền bỉ và tối ưu chi phí cho mọi công trình.',
		// Figma: Inter 20/400 màu #464646, box 1068px; Description → Cards cách 57px.
		'tmnhanphat_services_description_color'         => '#464646',
		'tmnhanphat_services_description_font_size'     => 20,
		'tmnhanphat_services_description_max_width'     => 820, // Figma 1068 → dòng ~95 ký tự, quá vùng đọc 45–75; hạ 820 (~73 ký tự) cho dễ đọc.
		'tmnhanphat_services_description_margin_bottom' => 57,

		// Query
		'tmnhanphat_services_count'       => 6,
		'tmnhanphat_services_orderby'     => 'menu_order',
		'tmnhanphat_services_order'       => 'ASC',
		'tmnhanphat_services_offset'      => 0,
		'tmnhanphat_services_exclude_ids' => '',
		'tmnhanphat_services_include_ids' => '',

		// Slider
		'tmnhanphat_services_autoplay_enable'    => true,
		'tmnhanphat_services_autoplay_speed'     => 4000,
		'tmnhanphat_services_transition_speed'   => 600,
		'tmnhanphat_services_pause_hover'        => true,
		'tmnhanphat_services_infinite'           => true,
		'tmnhanphat_services_show_arrows'        => false,
		'tmnhanphat_services_show_dots'          => true,
		'tmnhanphat_services_drag_enable'        => true,
		'tmnhanphat_services_gap'                => 38, // Migrate: gap card co theo container hẹp (53→38).
		'tmnhanphat_services_cards_desktop'      => '3',
		'tmnhanphat_services_cards_tablet'       => '2',
		'tmnhanphat_services_cards_mobile'       => '1',
		'tmnhanphat_services_dot_active_color'   => '#046AB5',
		'tmnhanphat_services_dot_inactive_color' => '#D9D9D9',

		// Card — Figma: 510×453 r=30, inner-shadow đen 70% (tối dần về đáy), Title 48/900
		// trắng, Excerpt 20/400 trắng, nội dung cách mép trái card 35px.
		'tmnhanphat_services_card_radius'        => 22, // Migrate: radius mềm theo card nhỏ hơn (30→22).
		'tmnhanphat_services_card_ratio'         => '510-453',
		'tmnhanphat_services_overlay_color'      => '#000000',
		'tmnhanphat_services_overlay_opacity'    => 70,
		'tmnhanphat_services_title_color'        => '#ffffff',
		'tmnhanphat_services_title_hover_color'  => '#8fc7ff',
		'tmnhanphat_services_title_font_size'    => 48,
		'tmnhanphat_services_excerpt_color'      => '#ffffff',
		'tmnhanphat_services_excerpt_font_size'  => 20,
		'tmnhanphat_services_content_padding'    => 35,
		'tmnhanphat_services_arrow_color'        => '#E31F2B',
		'tmnhanphat_services_hover_zoom_enable'  => true,
		'tmnhanphat_services_hover_duration'     => 300,

		// CTA
		'tmnhanphat_services_cta_enable'      => true,
		'tmnhanphat_services_cta_text'        => 'Tìm hiểu tất cả dịch vụ',
		// '' = tự dẫn tới Archive của CPT Service (get_post_type_archive_link) — không hardcode URL.
		'tmnhanphat_services_cta_url'         => '',
		'tmnhanphat_services_cta_bg'          => '#046AB5',
		'tmnhanphat_services_cta_text_color'  => '#ffffff',
		'tmnhanphat_services_cta_hover_bg'    => '#03518a',
		// Figma CTA: pill 346×55, chữ 24/400 → padding 12/27; Dots → CTA cách 57px.
		'tmnhanphat_services_cta_radius'      => 999,
		'tmnhanphat_services_cta_padding_x'   => 27,
		'tmnhanphat_services_cta_padding_y'   => 12,
		'tmnhanphat_services_cta_font_size'   => 24,
		'tmnhanphat_services_cta_margin_top'  => 57,
	);
}

/**
 * Đọc 1 theme_mod của Services kèm fallback lấy từ tmnhanphat_services_defaults().
 *
 * @param string $key Tên setting (khớp key trong tmnhanphat_services_defaults()).
 * @return mixed
 */
function tmnhanphat_get_services_mod( $key ) {
	$defaults = tmnhanphat_services_defaults();
	$default  = isset( $defaults[ $key ] ) ? $defaults[ $key ] : '';

	return get_theme_mod( $key, $default );
}

/**
 * Danh sách lựa chọn "Order By" cho Query Services.
 *
 * @return array<string, string>
 */
function tmnhanphat_get_services_orderby_choices() {
	return array(
		'menu_order' => __( 'Thứ tự tuỳ chỉnh (Order)', 'tmnhanphat' ),
		'date'       => __( 'Ngày đăng', 'tmnhanphat' ),
		'title'      => __( 'Tiêu đề', 'tmnhanphat' ),
		'rand'       => __( 'Ngẫu nhiên', 'tmnhanphat' ),
	);
}

/**
 * Whitelist "Order By".
 *
 * @param string $value Giá trị gửi lên từ Customizer.
 * @return string
 */
function tmnhanphat_sanitize_services_orderby( $value ) {
	$choices = array_keys( tmnhanphat_get_services_orderby_choices() );

	return in_array( $value, $choices, true ) ? $value : 'menu_order';
}

/**
 * Danh sách lựa chọn "Order" (chiều sắp xếp).
 *
 * @return array<string, string>
 */
function tmnhanphat_get_services_order_choices() {
	return array(
		'ASC'  => __( 'Tăng dần (ASC)', 'tmnhanphat' ),
		'DESC' => __( 'Giảm dần (DESC)', 'tmnhanphat' ),
	);
}

/**
 * Whitelist "Order".
 *
 * @param string $value Giá trị gửi lên từ Customizer.
 * @return string
 */
function tmnhanphat_sanitize_services_order( $value ) {
	$choices = array_keys( tmnhanphat_get_services_order_choices() );

	return in_array( $value, $choices, true ) ? $value : 'ASC';
}

/**
 * Danh sách lựa chọn "Heading Alignment".
 *
 * @return array<string, string>
 */
function tmnhanphat_get_services_align_choices() {
	return array(
		'center' => __( 'Giữa', 'tmnhanphat' ),
		'left'   => __( 'Trái', 'tmnhanphat' ),
		'right'  => __( 'Phải', 'tmnhanphat' ),
	);
}

/**
 * Whitelist "Heading Alignment".
 *
 * @param string $value Giá trị gửi lên từ Customizer.
 * @return string
 */
function tmnhanphat_sanitize_services_align( $value ) {
	$choices = array_keys( tmnhanphat_get_services_align_choices() );

	return in_array( $value, $choices, true ) ? $value : 'center';
}

/**
 * Danh sách lựa chọn "Image Ratio" của Card (key "W-H" → aspect-ratio CSS "W / H").
 *
 * @return array<string, string>
 */
function tmnhanphat_get_services_ratio_choices() {
	return array(
		'510-453' => __( 'Chuẩn thiết kế (510×453)', 'tmnhanphat' ),
		'4-3'     => __( '4:3', 'tmnhanphat' ),
		'3-2'     => __( '3:2', 'tmnhanphat' ),
		'16-9'    => __( '16:9', 'tmnhanphat' ),
		'1-1'     => __( 'Vuông (1:1)', 'tmnhanphat' ),
	);
}

/**
 * Whitelist "Image Ratio".
 *
 * @param string $value Giá trị gửi lên từ Customizer.
 * @return string
 */
function tmnhanphat_sanitize_services_ratio( $value ) {
	$choices = array_keys( tmnhanphat_get_services_ratio_choices() );

	return in_array( $value, $choices, true ) ? $value : '510-453';
}

/**
 * Danh sách lựa chọn số card hiển thị đồng thời theo breakpoint.
 *
 * @param string $breakpoint 'desktop' | 'tablet' | 'mobile'.
 * @return array<string, string>
 */
function tmnhanphat_get_services_cards_choices( $breakpoint ) {
	$map = array(
		'desktop' => array( '2', '3', '4' ),
		'tablet'  => array( '1', '2', '3' ),
		'mobile'  => array( '1', '2' ),
	);
	$values  = isset( $map[ $breakpoint ] ) ? $map[ $breakpoint ] : $map['desktop'];
	$choices = array();

	foreach ( $values as $value ) {
		$choices[ $value ] = $value;
	}

	return $choices;
}

/**
 * Whitelist số card Desktop.
 *
 * @param string $value Giá trị gửi lên từ Customizer.
 * @return string
 */
function tmnhanphat_sanitize_services_cards_desktop( $value ) {
	$choices = array_keys( tmnhanphat_get_services_cards_choices( 'desktop' ) );

	return in_array( (string) $value, $choices, true ) ? (string) $value : '3';
}

/**
 * Whitelist số card Tablet.
 *
 * @param string $value Giá trị gửi lên từ Customizer.
 * @return string
 */
function tmnhanphat_sanitize_services_cards_tablet( $value ) {
	$choices = array_keys( tmnhanphat_get_services_cards_choices( 'tablet' ) );

	return in_array( (string) $value, $choices, true ) ? (string) $value : '2';
}

/**
 * Whitelist số card Mobile.
 *
 * @param string $value Giá trị gửi lên từ Customizer.
 * @return string
 */
function tmnhanphat_sanitize_services_cards_mobile( $value ) {
	$choices = array_keys( tmnhanphat_get_services_cards_choices( 'mobile' ) );

	return in_array( (string) $value, $choices, true ) ? (string) $value : '1';
}

/**
 * Sanitize danh sách ID dạng CSV ("12, 34,56" → "12,34,56") cho Include/Exclude IDs.
 *
 * @param string $value Giá trị gửi lên từ Customizer.
 * @return string
 */
function tmnhanphat_sanitize_id_list( $value ) {
	$ids = array_filter( array_map( 'absint', explode( ',', (string) $value ) ) );

	return implode( ',', $ids );
}

/**
 * Chuyển chuỗi CSV ID đã sanitize thành mảng int (dùng cho WP_Query).
 *
 * @param string $value Chuỗi CSV.
 * @return int[]
 */
function tmnhanphat_parse_id_list( $value ) {
	return array_filter( array_map( 'absint', explode( ',', (string) $value ) ) );
}

/**
 * Query danh sách Service cho Homepage theo đúng setting Customizer — chỉ lấy đúng số lượng
 * cần render (no_found_rows, không phân trang), KHÔNG query toàn bộ (PROJECT_RULES.md mục 15).
 *
 * @return WP_Query
 */
function tmnhanphat_get_services_query() {
	$args = array(
		'post_type'           => 'tmnp_service',
		'post_status'         => 'publish',
		'posts_per_page'      => max( 1, absint( tmnhanphat_get_services_mod( 'tmnhanphat_services_count' ) ) ),
		'orderby'             => tmnhanphat_get_services_mod( 'tmnhanphat_services_orderby' ),
		'order'               => tmnhanphat_get_services_mod( 'tmnhanphat_services_order' ),
		'no_found_rows'       => true,
		'ignore_sticky_posts' => true,
	);

	$offset = absint( tmnhanphat_get_services_mod( 'tmnhanphat_services_offset' ) );
	if ( $offset > 0 ) {
		$args['offset'] = $offset;
	}

	$exclude = tmnhanphat_parse_id_list( tmnhanphat_get_services_mod( 'tmnhanphat_services_exclude_ids' ) );
	if ( $exclude ) {
		$args['post__not_in'] = $exclude;
	}

	$include = tmnhanphat_parse_id_list( tmnhanphat_get_services_mod( 'tmnhanphat_services_include_ids' ) );
	if ( $include ) {
		$args['post__in'] = $include;
		// post__in cần orderby riêng nếu muốn giữ đúng thứ tự nhập — chỉ áp khi đang sort mặc định.
		if ( 'menu_order' === $args['orderby'] ) {
			$args['orderby'] = 'post__in';
		}
	}

	return new WP_Query( $args );
}

/**
 * URL đích của 1 Service card: meta Landing Page URL override (nếu có) hoặc permalink.
 *
 * @param int $post_id ID của Service.
 * @return string
 */
function tmnhanphat_get_service_link( $post_id ) {
	$override = get_post_meta( $post_id, '_tmnp_service_landing_url', true );

	return $override ? $override : get_permalink( $post_id );
}

/**
 * URL của CTA "Tìm hiểu tất cả dịch vụ": setting override hoặc Archive CPT Service.
 *
 * @return string
 */
function tmnhanphat_get_services_cta_url() {
	$override = tmnhanphat_get_services_mod( 'tmnhanphat_services_cta_url' );

	if ( $override ) {
		return $override;
	}

	$archive = get_post_type_archive_link( 'tmnp_service' );

	return $archive ? $archive : home_url( '/' );
}

/**
 * Sinh chuỗi CSS custom properties cho Services Home Section, gắn qua wp_add_inline_style()
 * trong inc/enqueue.php — cùng quy ước Header/Footer/Hero/Partner/About (mục 22).
 *
 * @return string
 */
function tmnhanphat_render_services_css_vars() {
	$bg_color        = tmnhanphat_get_services_mod( 'tmnhanphat_services_bg_color' );
	$container_width = absint( tmnhanphat_get_services_mod( 'tmnhanphat_services_container_width' ) );
	$padding_desktop = absint( tmnhanphat_get_services_mod( 'tmnhanphat_services_padding_desktop' ) );
	$padding_tablet  = absint( tmnhanphat_get_services_mod( 'tmnhanphat_services_padding_tablet' ) );
	$padding_mobile  = absint( tmnhanphat_get_services_mod( 'tmnhanphat_services_padding_mobile' ) );

	$heading_red_color  = tmnhanphat_get_services_mod( 'tmnhanphat_services_heading_red_color' );
	$heading_blue_color = tmnhanphat_get_services_mod( 'tmnhanphat_services_heading_blue_color' );
	$heading_size       = absint( tmnhanphat_get_services_mod( 'tmnhanphat_services_heading_font_size' ) );
	$heading_weight     = absint( tmnhanphat_get_services_mod( 'tmnhanphat_services_heading_font_weight' ) );
	$heading_align      = tmnhanphat_get_services_mod( 'tmnhanphat_services_heading_align' );
	$heading_margin     = absint( tmnhanphat_get_services_mod( 'tmnhanphat_services_heading_margin_bottom' ) );

	$desc_color  = tmnhanphat_get_services_mod( 'tmnhanphat_services_description_color' );
	$desc_size   = absint( tmnhanphat_get_services_mod( 'tmnhanphat_services_description_font_size' ) );
	$desc_width  = absint( tmnhanphat_get_services_mod( 'tmnhanphat_services_description_max_width' ) );
	$desc_margin = absint( tmnhanphat_get_services_mod( 'tmnhanphat_services_description_margin_bottom' ) );

	$gap           = absint( tmnhanphat_get_services_mod( 'tmnhanphat_services_gap' ) );
	$cards_desktop = absint( tmnhanphat_get_services_mod( 'tmnhanphat_services_cards_desktop' ) );
	$cards_tablet  = absint( tmnhanphat_get_services_mod( 'tmnhanphat_services_cards_tablet' ) );
	$cards_mobile  = absint( tmnhanphat_get_services_mod( 'tmnhanphat_services_cards_mobile' ) );
	$dot_active    = tmnhanphat_get_services_mod( 'tmnhanphat_services_dot_active_color' );
	$dot_inactive  = tmnhanphat_get_services_mod( 'tmnhanphat_services_dot_inactive_color' );
	$transition    = absint( tmnhanphat_get_services_mod( 'tmnhanphat_services_transition_speed' ) );

	$card_radius     = absint( tmnhanphat_get_services_mod( 'tmnhanphat_services_card_radius' ) );
	$card_ratio      = str_replace( '-', ' / ', tmnhanphat_get_services_mod( 'tmnhanphat_services_card_ratio' ) );
	$overlay_color   = tmnhanphat_get_services_mod( 'tmnhanphat_services_overlay_color' );
	$overlay_opacity = absint( tmnhanphat_get_services_mod( 'tmnhanphat_services_overlay_opacity' ) );
	$title_color     = tmnhanphat_get_services_mod( 'tmnhanphat_services_title_color' );
	$title_hover     = tmnhanphat_get_services_mod( 'tmnhanphat_services_title_hover_color' );
	$title_size      = absint( tmnhanphat_get_services_mod( 'tmnhanphat_services_title_font_size' ) );
	$excerpt_color   = tmnhanphat_get_services_mod( 'tmnhanphat_services_excerpt_color' );
	$excerpt_size    = absint( tmnhanphat_get_services_mod( 'tmnhanphat_services_excerpt_font_size' ) );
	$content_padding = absint( tmnhanphat_get_services_mod( 'tmnhanphat_services_content_padding' ) );
	$arrow_color     = tmnhanphat_get_services_mod( 'tmnhanphat_services_arrow_color' );
	$hover_zoom      = tmnhanphat_get_services_mod( 'tmnhanphat_services_hover_zoom_enable' );
	$hover_duration  = absint( tmnhanphat_get_services_mod( 'tmnhanphat_services_hover_duration' ) );

	$cta_bg         = tmnhanphat_get_services_mod( 'tmnhanphat_services_cta_bg' );
	$cta_text_color = tmnhanphat_get_services_mod( 'tmnhanphat_services_cta_text_color' );
	$cta_hover_bg   = tmnhanphat_get_services_mod( 'tmnhanphat_services_cta_hover_bg' );
	$cta_radius     = absint( tmnhanphat_get_services_mod( 'tmnhanphat_services_cta_radius' ) );
	$cta_padding_x  = absint( tmnhanphat_get_services_mod( 'tmnhanphat_services_cta_padding_x' ) );
	$cta_padding_y  = absint( tmnhanphat_get_services_mod( 'tmnhanphat_services_cta_padding_y' ) );
	$cta_font_size  = absint( tmnhanphat_get_services_mod( 'tmnhanphat_services_cta_font_size' ) );
	$cta_margin_top = absint( tmnhanphat_get_services_mod( 'tmnhanphat_services_cta_margin_top' ) );

	$css  = ':root{';
	$css .= '--services-bg:' . $bg_color . ';';
	$css .= '--services-container-width:' . ( $container_width > 0 ? $container_width . 'px' : 'var(--container-max-width)' ) . ';';
	$css .= '--services-padding:' . $padding_desktop . 'px;';
	$css .= '--services-heading-red:' . $heading_red_color . ';';
	$css .= '--services-heading-blue:' . $heading_blue_color . ';';
	$css .= '--services-heading-size:' . $heading_size . 'px;';
	$css .= '--services-heading-weight:' . $heading_weight . ';';
	$css .= '--services-heading-align:' . $heading_align . ';';
	$css .= '--services-heading-margin:' . $heading_margin . 'px;';
	$css .= '--services-desc-color:' . $desc_color . ';';
	$css .= '--services-desc-size:' . $desc_size . 'px;';
	$css .= '--services-desc-max-width:' . $desc_width . 'px;';
	$css .= '--services-desc-margin:' . $desc_margin . 'px;';
	$css .= '--services-gap:' . $gap . 'px;';
	$css .= '--services-cols:' . max( 1, $cards_desktop ) . ';';
	$css .= '--services-dot-active:' . $dot_active . ';';
	$css .= '--services-dot-inactive:' . $dot_inactive . ';';
	$css .= '--services-transition:' . $transition . 'ms;';
	$css .= '--services-card-radius:' . $card_radius . 'px;';
	$css .= '--services-card-ratio:' . $card_ratio . ';';
	$css .= '--services-overlay:' . tmnhanphat_hex_to_rgba( $overlay_color, $overlay_opacity ) . ';';
	$css .= '--services-overlay-hover:' . tmnhanphat_hex_to_rgba( $overlay_color, min( 100, $overlay_opacity + 15 ) ) . ';';
	$css .= '--services-title-color:' . $title_color . ';';
	$css .= '--services-title-hover:' . $title_hover . ';';
	$css .= '--services-title-size:' . $title_size . 'px;';
	$css .= '--services-excerpt-color:' . $excerpt_color . ';';
	$css .= '--services-excerpt-size:' . $excerpt_size . 'px;';
	$css .= '--services-content-padding:' . $content_padding . 'px;';
	$css .= '--services-arrow-color:' . $arrow_color . ';';
	$css .= '--services-hover-zoom:' . ( $hover_zoom ? '1.06' : '1' ) . ';';
	$css .= '--services-hover-duration:' . $hover_duration . 'ms;';
	$css .= '--services-cta-bg:' . $cta_bg . ';';
	$css .= '--services-cta-text:' . $cta_text_color . ';';
	$css .= '--services-cta-hover-bg:' . $cta_hover_bg . ';';
	$css .= '--services-cta-radius:' . $cta_radius . 'px;';
	$css .= '--services-cta-padding:' . $cta_padding_y . 'px ' . $cta_padding_x . 'px;';
	$css .= '--services-cta-size:' . $cta_font_size . 'px;';
	$css .= '--services-cta-margin-top:' . $cta_margin_top . 'px;';
	$css .= '}';

	$css .= '@media (max-width:991px){:root{';
	$css .= '--services-padding:' . $padding_tablet . 'px;';
	$css .= '--services-cols:' . max( 1, $cards_tablet ) . ';';
	$css .= '}}';

	$css .= '@media (max-width:599px){:root{';
	$css .= '--services-padding:' . $padding_mobile . 'px;';
	$css .= '--services-cols:' . max( 1, $cards_mobile ) . ';';
	$css .= '}}';

	return $css;
}

/* ==========================================================================
 * PRODUCTS SECTION (trang chủ) — helper riêng, không đụng các module khác.
 *
 * Dữ liệu là POST THƯỜNG thuộc Category cha "Sản phẩm" (mặc định slug 'san-pham'),
 * KHÔNG phải CPT — Category Navigation hiển thị các Sub Category của cha, click tab
 * đổi danh sách qua AJAX (inc/ajax.php::tmnhanphat_ajax_filter_products), fallback
 * không JS = link thẳng tới trang archive của category đó.
 *
 * Thương hiệu/Tiêu chuẩn của từng sản phẩm đọc từ post meta — meta key CẤU HÌNH ĐƯỢC
 * qua Customizer (mặc định _tmnp_product_brand/_tmnp_product_standard, nhập qua meta
 * box "Thông tin sản phẩm" trong inc/post-types.php; nếu sau này dùng ACF chỉ cần đổi
 * meta key trong Customizer, không sửa code). Field rỗng → ẩn dòng đó (không render
 * nhãn suông).
 * ========================================================================== */

/**
 * Giá trị mặc định cho toàn bộ setting Products Home Section Customizer.
 *
 * @return array<string, mixed>
 */
function tmnhanphat_products_defaults() {
	$parent = get_category_by_slug( 'san-pham' );

	return array(
		// General
		'tmnhanphat_products_enable'            => true,
		'tmnhanphat_products_section_id'        => 'products',
		// 0 = dùng chung Container Width của Global Settings (mục 27).
		'tmnhanphat_products_container_width'   => 0,
		'tmnhanphat_products_margin_top'        => 0,
		'tmnhanphat_products_margin_bottom'     => 0,
		// Figma: đáy About→Heading = 203 (padding-top; đáy section 57 cố định ở CSS).
		'tmnhanphat_products_padding_desktop'   => 128, // Migrate: nhịp dọc mới (203→128).
		'tmnhanphat_products_padding_tablet'    => 56,
		'tmnhanphat_products_padding_mobile'    => 40,
		'tmnhanphat_products_bg_color'          => '#ffffff',
		'tmnhanphat_products_bg_image'          => '',
		'tmnhanphat_products_decoration_image'  => '',
		'tmnhanphat_products_decoration_enable' => true,

		// Heading — 2 phần text 2 màu (đỏ + xanh) như Services Home.
		'tmnhanphat_products_heading_red_text'        => 'Sản phẩm của',
		'tmnhanphat_products_heading_blue_text'       => 'Thang Máy Nhân Phát',
		'tmnhanphat_products_heading_red_color'       => '#E31F2B',
		'tmnhanphat_products_heading_blue_color'      => '#046AB5',
		// Figma: Heading 85/900 (canvas 1920); tablet/mobile scale theo tỉ lệ tương đương.
		'tmnhanphat_products_heading_size_desktop'    => 85,
		'tmnhanphat_products_heading_size_tablet'     => 44,
		'tmnhanphat_products_heading_size_mobile'     => 30,
		'tmnhanphat_products_heading_font_weight'     => '900',
		'tmnhanphat_products_heading_line_height'     => 1.21,
		'tmnhanphat_products_heading_letter_spacing'  => -2, // Taste B: tracking âm nhẹ cho display (-2px ≈ -0.02em @ heading 85-96px).
		'tmnhanphat_products_heading_align'           => 'center',

		// Description
		'tmnhanphat_products_description_text'         => 'Với danh mục sản phẩm đa dạng và phong phú, Thang Máy Nhân Phát tự tin đáp ứng mọi yêu cầu của khách hàng, từ thang máy gia đình nhỏ gọn đến thang máy tải khách, tải hàng công suất lớn.',
		// Figma: Description 20/400 #464646, box 1068; Header → Tabs cách 110px.
		'tmnhanphat_products_description_color'        => '#464646',
		'tmnhanphat_products_description_size_desktop' => 20,
		'tmnhanphat_products_description_size_tablet'  => 16,
		'tmnhanphat_products_description_size_mobile'  => 14,
		'tmnhanphat_products_description_max_width'    => 820, // Figma 1068 → hạ 820 cho measure dễ đọc (cùng lý do Services).
		'tmnhanphat_products_description_line_clamp'   => 0,
		'tmnhanphat_products_header_bottom_spacing'    => 110,

		// Category Navigation
		'tmnhanphat_products_parent_cat'          => $parent ? (int) $parent->term_id : 0,
		'tmnhanphat_products_show_empty_cats'     => false,
		// Figma: tab active = pill 295×62 r20 nền xanh chữ trắng 24/400 (padding 15/30);
		// tab thường = CHỈ text #4A4F57 24/400 (không nền, không viền — border trắng =
		// vô hình trên nền trắng); gap giữa tab ~36; Tabs (kèm kẻ ngang) → Cards 78.
		'tmnhanphat_products_cats_align'          => 'left',
		'tmnhanphat_products_cats_gap'            => 36,
		'tmnhanphat_products_cats_padding_x'      => 30,
		'tmnhanphat_products_cats_padding_y'      => 15,
		'tmnhanphat_products_cats_radius'         => 20,
		'tmnhanphat_products_cats_font_size'      => 24,
		'tmnhanphat_products_cats_active_bg'      => '#046AB5',
		'tmnhanphat_products_cats_active_color'   => '#ffffff',
		'tmnhanphat_products_cats_inactive_bg'    => '#ffffff',
		'tmnhanphat_products_cats_inactive_color' => '#464646', // Gom token: Figma #4A4F57 → xám body chung.
		'tmnhanphat_products_cats_border'         => '#ffffff',
		'tmnhanphat_products_cats_hover_bg'       => '#eaf3fb',
		'tmnhanphat_products_cats_hover_color'    => '#046AB5',
		'tmnhanphat_products_cats_bottom_spacing' => 78,

		// Query — Cols × Rows quyết định số bài / slide (Desktop 3×2=6, Tablet 2×2=4, Mobile 1×1=1).
		'tmnhanphat_products_post_type'    => 'post',
		'tmnhanphat_products_total'        => 12,
		'tmnhanphat_products_cols_desktop' => 3,
		'tmnhanphat_products_cols_tablet'  => 2,
		'tmnhanphat_products_cols_mobile'  => 1,
		'tmnhanphat_products_rows_desktop' => 2,
		'tmnhanphat_products_rows_tablet'  => 2,
		'tmnhanphat_products_rows_mobile'  => 1,
		'tmnhanphat_products_orderby'      => 'date',
		'tmnhanphat_products_order'        => 'DESC',
		'tmnhanphat_products_exclude_cats' => '',

		// Card — Figma: 438×638 r=30, viền 1px #E8E6E1 (ở CSS), KHÔNG shadow, padding
		// trong 24; gap lưới: cột 123 / hàng 114 (hàng cố định ở CSS); Title 28/700;
		// Meta 20/400 #4A4F57.
		'tmnhanphat_products_card_radius'        => 22, // Migrate: radius mềm (30→22).
		'tmnhanphat_products_card_shadow'        => 'none',
		'tmnhanphat_products_card_padding'       => 24,
		'tmnhanphat_products_card_gap'           => 123,
		'tmnhanphat_products_image_ratio'        => '438-403',
		'tmnhanphat_products_image_radius'       => 0,
		'tmnhanphat_products_hover_zoom_enable'  => true,
		'tmnhanphat_products_title_size_desktop' => 28,
		'tmnhanphat_products_title_size_tablet'  => 20,
		'tmnhanphat_products_title_size_mobile'  => 18,
		'tmnhanphat_products_title_line_clamp'   => 2,
		'tmnhanphat_products_title_align'        => 'left',
		'tmnhanphat_products_meta_font_size'     => 20,
		'tmnhanphat_products_meta_color'         => '#464646', // Gom token: Figma #4A4F57 → xám body chung.
		'tmnhanphat_products_brand_enable'       => true,
		'tmnhanphat_products_brand_label'        => __( 'Thương hiệu', 'tmnhanphat' ),
		'tmnhanphat_products_brand_meta_key'     => '_tmnp_product_brand',
		'tmnhanphat_products_standard_enable'    => true,
		'tmnhanphat_products_standard_label'     => __( 'Tiêu chuẩn', 'tmnhanphat' ),
		'tmnhanphat_products_standard_meta_key'  => '_tmnp_product_standard',
		'tmnhanphat_products_price_label'        => __( 'Giá:', 'tmnhanphat' ),
		'tmnhanphat_products_contact_text'       => __( 'Liên hệ', 'tmnhanphat' ),
		'tmnhanphat_products_contact_color'      => '#E31F2B',
		// '' = chưa có trang liên hệ, render '#' — sau này trỏ tới Contact Page/Form.
		'tmnhanphat_products_contact_url'        => '',
		'tmnhanphat_products_button_text'        => __( 'Chi tiết', 'tmnhanphat' ),
		'tmnhanphat_products_button_radius'      => 999,
		'tmnhanphat_products_button_bg'          => '#046AB5',
		'tmnhanphat_products_button_hover_bg'    => '#03518a',
		'tmnhanphat_products_button_color'       => '#ffffff',

		// Slider
		// Autoplay TẮT mặc định (Taste review mục 5, owner duyệt): người dùng đang đọc/
		// so sánh sản phẩm mà lưới tự lật trang là motion chống lại nhiệm vụ của section.
		// Dots + drag + tabs vẫn đủ điều hướng; bật lại được trong Customizer nếu cần.
		'tmnhanphat_products_autoplay_enable'  => false,
		'tmnhanphat_products_autoplay_delay'   => 4000,
		'tmnhanphat_products_transition_speed' => 600,
		'tmnhanphat_products_infinite'         => true,
		'tmnhanphat_products_pause_hover'      => true,
		'tmnhanphat_products_drag_enable'      => true,
		'tmnhanphat_products_dots_enable'      => true,
		// Figma: dots 15px, gap 16.
		'tmnhanphat_products_dot_color'        => '#D9D9D9',
		'tmnhanphat_products_dot_active_color' => '#046AB5',
		'tmnhanphat_products_dot_size'         => 15,
		'tmnhanphat_products_dot_gap'          => 16,
	);
}

/**
 * Đọc 1 theme_mod của Products kèm fallback lấy từ tmnhanphat_products_defaults().
 *
 * @param string $key Tên setting (khớp key trong tmnhanphat_products_defaults()).
 * @return mixed
 */
function tmnhanphat_get_products_mod( $key ) {
	$defaults = tmnhanphat_products_defaults();
	$default  = isset( $defaults[ $key ] ) ? $defaults[ $key ] : '';

	return get_theme_mod( $key, $default );
}

/**
 * Whitelist Alignment (mặc định left) — dùng cho Category Navigation/Card Title,
 * chung danh sách choices với tmnhanphat_get_services_align_choices().
 *
 * @param string $value Giá trị gửi lên từ Customizer.
 * @return string
 */
function tmnhanphat_sanitize_products_align_left( $value ) {
	$choices = array_keys( tmnhanphat_get_services_align_choices() );

	return in_array( $value, $choices, true ) ? $value : 'left';
}

/**
 * Line Height cho phép số thập phân (1.2, 1.35...) — absint() làm mất phần lẻ nên
 * cần sanitize float riêng, tự giới hạn khoảng hợp lệ.
 *
 * @param mixed $value Giá trị gửi lên từ Customizer.
 * @return float
 */
function tmnhanphat_sanitize_products_line_height( $value ) {
	return max( 0.8, min( 3, (float) $value ) );
}

/**
 * Letter Spacing cho phép số ÂM (px) — absint() làm mất dấu nên cần sanitize riêng.
 *
 * @param mixed $value Giá trị gửi lên từ Customizer.
 * @return int
 */
function tmnhanphat_sanitize_products_letter_spacing( $value ) {
	return max( -5, min( 10, (int) $value ) );
}

/**
 * Danh sách lựa chọn "Order By" cho Query Products (post thường — không có menu_order).
 *
 * @return array<string, string>
 */
function tmnhanphat_get_products_orderby_choices() {
	return array(
		'date'  => __( 'Ngày đăng', 'tmnhanphat' ),
		'title' => __( 'Tiêu đề', 'tmnhanphat' ),
		'rand'  => __( 'Ngẫu nhiên', 'tmnhanphat' ),
	);
}

/**
 * Whitelist "Order By" của Products.
 *
 * @param string $value Giá trị gửi lên từ Customizer.
 * @return string
 */
function tmnhanphat_sanitize_products_orderby( $value ) {
	$choices = array_keys( tmnhanphat_get_products_orderby_choices() );

	return in_array( $value, $choices, true ) ? $value : 'date';
}

/**
 * Whitelist "Order" của Products (mặc định DESC — bài mới nhất trước).
 *
 * @param string $value Giá trị gửi lên từ Customizer.
 * @return string
 */
function tmnhanphat_sanitize_products_order( $value ) {
	return in_array( $value, array( 'ASC', 'DESC' ), true ) ? $value : 'DESC';
}

/**
 * Whitelist "Post Type" — chỉ chấp nhận post type public đang tồn tại.
 *
 * @param string $value Giá trị gửi lên từ Customizer.
 * @return string
 */
function tmnhanphat_sanitize_products_post_type( $value ) {
	$value = sanitize_key( $value );

	if ( $value && post_type_exists( $value ) && is_post_type_viewable( $value ) ) {
		return $value;
	}

	return 'post';
}

/**
 * Danh sách lựa chọn "Image Ratio" của Product Card (key "W-H" → aspect-ratio CSS "W / H").
 *
 * @return array<string, string>
 */
function tmnhanphat_get_products_ratio_choices() {
	return array(
		'438-403' => __( 'Chuẩn thiết kế (438×403)', 'tmnhanphat' ),
		'4-3'     => __( '4:3', 'tmnhanphat' ),
		'3-2'     => __( '3:2', 'tmnhanphat' ),
		'16-9'    => __( '16:9', 'tmnhanphat' ),
		'1-1'     => __( 'Vuông (1:1)', 'tmnhanphat' ),
	);
}

/**
 * Whitelist "Image Ratio" của Products.
 *
 * @param string $value Giá trị gửi lên từ Customizer.
 * @return string
 */
function tmnhanphat_sanitize_products_ratio( $value ) {
	$choices = array_keys( tmnhanphat_get_products_ratio_choices() );

	return in_array( $value, $choices, true ) ? $value : '438-403';
}

/**
 * Danh sách mức bóng đổ Product Card cho phép chọn ở Customizer.
 *
 * @return array<string, string>
 */
function tmnhanphat_get_products_card_shadow_choices() {
	return array(
		'none'   => __( 'Không có', 'tmnhanphat' ),
		'soft'   => __( 'Nhẹ', 'tmnhanphat' ),
		'medium' => __( 'Vừa', 'tmnhanphat' ),
	);
}

/**
 * Quy đổi key bóng đổ Card sang cặp giá trị box-shadow (mặc định / khi hover lift).
 *
 * @param string $key Key trong tmnhanphat_get_products_card_shadow_choices().
 * @return array{base: string, hover: string}
 */
function tmnhanphat_get_products_card_shadow_value( $key ) {
	$shadows = array(
		'none'   => array(
			'base'  => 'none',
			'hover' => '0 8px 20px rgb(6 24 44 / 0.10)',
		),
		'soft'   => array(
			'base'  => '0 2px 10px rgb(6 24 44 / 0.07)',
			'hover' => '0 12px 28px rgb(6 24 44 / 0.14)',
		),
		'medium' => array(
			'base'  => '0 6px 18px rgb(6 24 44 / 0.12)',
			'hover' => '0 16px 36px rgb(6 24 44 / 0.20)',
		),
	);

	return isset( $shadows[ $key ] ) ? $shadows[ $key ] : $shadows['soft'];
}

/**
 * Whitelist "Card Shadow" của Products.
 *
 * @param string $value Giá trị gửi lên từ Customizer.
 * @return string
 */
function tmnhanphat_sanitize_products_card_shadow( $value ) {
	$choices = array_keys( tmnhanphat_get_products_card_shadow_choices() );

	return in_array( $value, $choices, true ) ? $value : 'soft';
}

/**
 * ID Category cha "Sản phẩm" đang cấu hình (đã xác thực term tồn tại).
 *
 * @return int 0 nếu chưa cấu hình hoặc term không còn tồn tại.
 */
function tmnhanphat_get_products_parent_id() {
	$parent_id = absint( tmnhanphat_get_products_mod( 'tmnhanphat_products_parent_cat' ) );

	if ( ! $parent_id || ! term_exists( $parent_id, 'category' ) ) {
		return 0;
	}

	return $parent_id;
}

/**
 * Danh sách Sub Category của Category cha "Sản phẩm" cho Category Navigation —
 * lọc theo Show Empty + Exclude Category, thứ tự theo term_id (thứ tự tạo).
 *
 * @return WP_Term[]
 */
function tmnhanphat_get_product_categories() {
	$parent_id = tmnhanphat_get_products_parent_id();

	if ( ! $parent_id ) {
		return array();
	}

	$exclude = tmnhanphat_parse_id_list( tmnhanphat_get_products_mod( 'tmnhanphat_products_exclude_cats' ) );

	$terms = get_terms( array(
		'taxonomy'   => 'category',
		'parent'     => $parent_id,
		'hide_empty' => ! tmnhanphat_get_products_mod( 'tmnhanphat_products_show_empty_cats' ),
		'exclude'    => $exclude,
		'orderby'    => 'term_id',
		'order'      => 'ASC',
	) );

	return is_wp_error( $terms ) ? array() : $terms;
}

/**
 * Term ID có hợp lệ cho Products Section không: phải là chính Category cha hoặc
 * hậu duệ của nó — dùng để xác thực input AJAX (không cho query category tuỳ ý).
 *
 * @param int $term_id Term ID cần kiểm tra.
 * @return bool
 */
function tmnhanphat_is_valid_products_term( $term_id ) {
	$parent_id = tmnhanphat_get_products_parent_id();

	if ( ! $parent_id || ! $term_id ) {
		return false;
	}

	if ( $term_id === $parent_id ) {
		return true;
	}

	return cat_is_ancestor_of( $parent_id, $term_id );
}

/**
 * Query danh sách sản phẩm của 1 category cho Products Section — chỉ lấy đúng số lượng
 * cần render (no_found_rows, không phân trang — PROJECT_RULES.md mục 15). Dùng chung cho
 * cả render lần đầu (template-parts/home/products.php) lẫn AJAX (inc/ajax.php).
 *
 * @param int $term_id Category ID (đã xác thực bằng tmnhanphat_is_valid_products_term()).
 * @return WP_Query
 */
function tmnhanphat_get_products_query( $term_id ) {
	return new WP_Query( array(
		'post_type'           => tmnhanphat_sanitize_products_post_type( tmnhanphat_get_products_mod( 'tmnhanphat_products_post_type' ) ),
		'post_status'         => 'publish',
		'cat'                 => absint( $term_id ),
		'posts_per_page'      => max( 1, absint( tmnhanphat_get_products_mod( 'tmnhanphat_products_total' ) ) ),
		'orderby'             => tmnhanphat_get_products_mod( 'tmnhanphat_products_orderby' ),
		'order'               => tmnhanphat_get_products_mod( 'tmnhanphat_products_order' ),
		'no_found_rows'       => true,
		'ignore_sticky_posts' => true,
	) );
}

/**
 * Đọc giá trị Thương hiệu/Tiêu chuẩn của 1 sản phẩm theo meta key cấu hình trong
 * Customizer — trả chuỗi thuần (strip tag) để card không vỡ layout nếu meta chứa HTML.
 *
 * Nếu Admin đã nhập Meta Box thì luôn ưu tiên hiển thị dữ liệu thật. Nếu meta rỗng
 * (chưa nhập), trả về một giá trị fallback hiển thị — chỉ ở tầng hiển thị, KHÔNG
 * ghi lại vào post meta / không update_post_meta ở đây.
 *
 * @param int    $post_id ID bài sản phẩm.
 * @param string $field   'brand' | 'standard'.
 * @return string Dữ liệu thật, hoặc fallback nếu meta chưa nhập.
 */
function tmnhanphat_get_product_meta_value( $post_id, $field ) {
	$key_setting = 'standard' === $field ? 'tmnhanphat_products_standard_meta_key' : 'tmnhanphat_products_brand_meta_key';
	$meta_key    = sanitize_text_field( tmnhanphat_get_products_mod( $key_setting ) );

	$value = '';

	if ( $meta_key ) {
		$raw   = get_post_meta( $post_id, $meta_key, true );
		$value = is_string( $raw ) ? trim( wp_strip_all_tags( $raw ) ) : '';
	}

	if ( '' !== $value ) {
		return $value;
	}

	return 'standard' === $field
		? __( 'Đạt tiêu chuẩn chất lượng', 'tmnhanphat' )
		: __( 'Nhân Phát Elevator', 'tmnhanphat' );
}

/**
 * URL của "Liên hệ" trên card: setting override hoặc '#' (chưa có Contact Page).
 *
 * @return string
 */
function tmnhanphat_get_products_contact_url() {
	$url = tmnhanphat_get_products_mod( 'tmnhanphat_products_contact_url' );

	return $url ? $url : '#';
}

/**
 * Sinh chuỗi CSS custom properties cho Products Home Section, gắn qua wp_add_inline_style()
 * trong inc/enqueue.php — cùng quy ước Header/Footer/Hero/Partner/About/Services (mục 22).
 *
 * @return string
 */
function tmnhanphat_render_products_css_vars() {
	$container_width = absint( tmnhanphat_get_products_mod( 'tmnhanphat_products_container_width' ) );
	$margin_top      = absint( tmnhanphat_get_products_mod( 'tmnhanphat_products_margin_top' ) );
	$margin_bottom   = absint( tmnhanphat_get_products_mod( 'tmnhanphat_products_margin_bottom' ) );
	$padding_desktop = absint( tmnhanphat_get_products_mod( 'tmnhanphat_products_padding_desktop' ) );
	$padding_tablet  = absint( tmnhanphat_get_products_mod( 'tmnhanphat_products_padding_tablet' ) );
	$padding_mobile  = absint( tmnhanphat_get_products_mod( 'tmnhanphat_products_padding_mobile' ) );
	$bg_color        = tmnhanphat_get_products_mod( 'tmnhanphat_products_bg_color' );
	$bg_image        = tmnhanphat_get_products_mod( 'tmnhanphat_products_bg_image' );

	$heading_red     = tmnhanphat_get_products_mod( 'tmnhanphat_products_heading_red_color' );
	$heading_blue    = tmnhanphat_get_products_mod( 'tmnhanphat_products_heading_blue_color' );
	$heading_desktop = absint( tmnhanphat_get_products_mod( 'tmnhanphat_products_heading_size_desktop' ) );
	$heading_tablet  = absint( tmnhanphat_get_products_mod( 'tmnhanphat_products_heading_size_tablet' ) );
	$heading_mobile  = absint( tmnhanphat_get_products_mod( 'tmnhanphat_products_heading_size_mobile' ) );
	$heading_weight  = absint( tmnhanphat_get_products_mod( 'tmnhanphat_products_heading_font_weight' ) );
	$heading_lh      = (float) tmnhanphat_get_products_mod( 'tmnhanphat_products_heading_line_height' );
	$heading_ls      = (int) tmnhanphat_get_products_mod( 'tmnhanphat_products_heading_letter_spacing' );
	$heading_align   = tmnhanphat_get_products_mod( 'tmnhanphat_products_heading_align' );

	$desc_color   = tmnhanphat_get_products_mod( 'tmnhanphat_products_description_color' );
	$desc_desktop = absint( tmnhanphat_get_products_mod( 'tmnhanphat_products_description_size_desktop' ) );
	$desc_tablet  = absint( tmnhanphat_get_products_mod( 'tmnhanphat_products_description_size_tablet' ) );
	$desc_mobile  = absint( tmnhanphat_get_products_mod( 'tmnhanphat_products_description_size_mobile' ) );
	$desc_width   = absint( tmnhanphat_get_products_mod( 'tmnhanphat_products_description_max_width' ) );
	$desc_clamp   = absint( tmnhanphat_get_products_mod( 'tmnhanphat_products_description_line_clamp' ) );
	$header_gap   = absint( tmnhanphat_get_products_mod( 'tmnhanphat_products_header_bottom_spacing' ) );

	$cats_align       = tmnhanphat_get_products_mod( 'tmnhanphat_products_cats_align' );
	$cats_gap         = absint( tmnhanphat_get_products_mod( 'tmnhanphat_products_cats_gap' ) );
	$cats_padding_x   = absint( tmnhanphat_get_products_mod( 'tmnhanphat_products_cats_padding_x' ) );
	$cats_padding_y   = absint( tmnhanphat_get_products_mod( 'tmnhanphat_products_cats_padding_y' ) );
	$cats_radius      = absint( tmnhanphat_get_products_mod( 'tmnhanphat_products_cats_radius' ) );
	$cats_font_size   = absint( tmnhanphat_get_products_mod( 'tmnhanphat_products_cats_font_size' ) );
	$cats_active_bg   = tmnhanphat_get_products_mod( 'tmnhanphat_products_cats_active_bg' );
	$cats_active_col  = tmnhanphat_get_products_mod( 'tmnhanphat_products_cats_active_color' );
	$cats_inact_bg    = tmnhanphat_get_products_mod( 'tmnhanphat_products_cats_inactive_bg' );
	$cats_inact_col   = tmnhanphat_get_products_mod( 'tmnhanphat_products_cats_inactive_color' );
	$cats_border      = tmnhanphat_get_products_mod( 'tmnhanphat_products_cats_border' );
	$cats_hover_bg    = tmnhanphat_get_products_mod( 'tmnhanphat_products_cats_hover_bg' );
	$cats_hover_col   = tmnhanphat_get_products_mod( 'tmnhanphat_products_cats_hover_color' );
	$cats_bottom      = absint( tmnhanphat_get_products_mod( 'tmnhanphat_products_cats_bottom_spacing' ) );

	// Cols/Rows: clamp tại tầng render (number input + absint — theo tiền lệ Partners Display Limit).
	$cols_desktop = max( 1, min( 4, absint( tmnhanphat_get_products_mod( 'tmnhanphat_products_cols_desktop' ) ) ) );
	$cols_tablet  = max( 1, min( 3, absint( tmnhanphat_get_products_mod( 'tmnhanphat_products_cols_tablet' ) ) ) );
	$cols_mobile  = max( 1, min( 2, absint( tmnhanphat_get_products_mod( 'tmnhanphat_products_cols_mobile' ) ) ) );

	$card_radius  = absint( tmnhanphat_get_products_mod( 'tmnhanphat_products_card_radius' ) );
	$card_shadow  = tmnhanphat_get_products_card_shadow_value( tmnhanphat_get_products_mod( 'tmnhanphat_products_card_shadow' ) );
	$card_padding = absint( tmnhanphat_get_products_mod( 'tmnhanphat_products_card_padding' ) );
	$card_gap     = absint( tmnhanphat_get_products_mod( 'tmnhanphat_products_card_gap' ) );
	$image_ratio  = str_replace( '-', ' / ', tmnhanphat_get_products_mod( 'tmnhanphat_products_image_ratio' ) );
	$image_radius = absint( tmnhanphat_get_products_mod( 'tmnhanphat_products_image_radius' ) );
	$hover_zoom   = tmnhanphat_get_products_mod( 'tmnhanphat_products_hover_zoom_enable' );

	$title_desktop = absint( tmnhanphat_get_products_mod( 'tmnhanphat_products_title_size_desktop' ) );
	$title_tablet  = absint( tmnhanphat_get_products_mod( 'tmnhanphat_products_title_size_tablet' ) );
	$title_mobile  = absint( tmnhanphat_get_products_mod( 'tmnhanphat_products_title_size_mobile' ) );
	$title_clamp   = max( 1, min( 4, absint( tmnhanphat_get_products_mod( 'tmnhanphat_products_title_line_clamp' ) ) ) );
	$title_align   = tmnhanphat_get_products_mod( 'tmnhanphat_products_title_align' );

	$meta_size  = absint( tmnhanphat_get_products_mod( 'tmnhanphat_products_meta_font_size' ) );
	$meta_color = tmnhanphat_get_products_mod( 'tmnhanphat_products_meta_color' );

	$contact_color   = tmnhanphat_get_products_mod( 'tmnhanphat_products_contact_color' );
	$button_radius   = absint( tmnhanphat_get_products_mod( 'tmnhanphat_products_button_radius' ) );
	$button_bg       = tmnhanphat_get_products_mod( 'tmnhanphat_products_button_bg' );
	$button_hover_bg = tmnhanphat_get_products_mod( 'tmnhanphat_products_button_hover_bg' );
	$button_color    = tmnhanphat_get_products_mod( 'tmnhanphat_products_button_color' );

	$transition = absint( tmnhanphat_get_products_mod( 'tmnhanphat_products_transition_speed' ) );
	$dot_color  = tmnhanphat_get_products_mod( 'tmnhanphat_products_dot_color' );
	$dot_active = tmnhanphat_get_products_mod( 'tmnhanphat_products_dot_active_color' );
	$dot_size   = absint( tmnhanphat_get_products_mod( 'tmnhanphat_products_dot_size' ) );
	$dot_gap    = absint( tmnhanphat_get_products_mod( 'tmnhanphat_products_dot_gap' ) );

	$css  = ':root{';
	$css .= '--products-container-width:' . ( $container_width > 0 ? $container_width . 'px' : 'var(--container-max-width)' ) . ';';
	$css .= '--products-margin-top:' . $margin_top . 'px;';
	$css .= '--products-margin-bottom:' . $margin_bottom . 'px;';
	$css .= '--products-padding:' . $padding_desktop . 'px;';
	$css .= '--products-bg:' . $bg_color . ';';
	$css .= '--products-bg-image:' . ( $bg_image ? "url('" . esc_url( $bg_image ) . "')" : 'none' ) . ';';
	$css .= '--products-heading-red:' . $heading_red . ';';
	$css .= '--products-heading-blue:' . $heading_blue . ';';
	$css .= '--products-heading-size:' . $heading_desktop . 'px;';
	$css .= '--products-heading-weight:' . $heading_weight . ';';
	$css .= '--products-heading-lh:' . $heading_lh . ';';
	$css .= '--products-heading-ls:' . $heading_ls . 'px;';
	$css .= '--products-heading-align:' . $heading_align . ';';
	$css .= '--products-desc-color:' . $desc_color . ';';
	$css .= '--products-desc-size:' . $desc_desktop . 'px;';
	$css .= '--products-desc-max-width:' . $desc_width . 'px;';
	$css .= '--products-header-gap:' . $header_gap . 'px;';
	$css .= '--products-cats-justify:' . ( 'center' === $cats_align ? 'center' : ( 'right' === $cats_align ? 'flex-end' : 'flex-start' ) ) . ';';
	$css .= '--products-cats-gap:' . $cats_gap . 'px;';
	$css .= '--products-cats-padding:' . $cats_padding_y . 'px ' . $cats_padding_x . 'px;';
	$css .= '--products-cats-radius:' . $cats_radius . 'px;';
	$css .= '--products-cats-size:' . $cats_font_size . 'px;';
	$css .= '--products-cats-active-bg:' . $cats_active_bg . ';';
	$css .= '--products-cats-active-color:' . $cats_active_col . ';';
	$css .= '--products-cats-bg:' . $cats_inact_bg . ';';
	$css .= '--products-cats-color:' . $cats_inact_col . ';';
	$css .= '--products-cats-border:' . $cats_border . ';';
	$css .= '--products-cats-hover-bg:' . $cats_hover_bg . ';';
	$css .= '--products-cats-hover-color:' . $cats_hover_col . ';';
	$css .= '--products-cats-bottom:' . $cats_bottom . 'px;';
	$css .= '--products-cols:' . $cols_desktop . ';';
	$css .= '--products-card-radius:' . $card_radius . 'px;';
	$css .= '--products-card-shadow:' . $card_shadow['base'] . ';';
	$css .= '--products-card-shadow-hover:' . $card_shadow['hover'] . ';';
	$css .= '--products-card-padding:' . $card_padding . 'px;';
	$css .= '--products-gap:' . $card_gap . 'px;';
	$css .= '--products-image-ratio:' . $image_ratio . ';';
	$css .= '--products-image-radius:' . $image_radius . 'px;';
	$css .= '--products-hover-zoom:' . ( $hover_zoom ? '1.05' : '1' ) . ';';
	$css .= '--products-title-size:' . $title_desktop . 'px;';
	$css .= '--products-title-clamp:' . $title_clamp . ';';
	$css .= '--products-title-align:' . $title_align . ';';
	$css .= '--products-meta-size:' . $meta_size . 'px;';
	$css .= '--products-meta-color:' . $meta_color . ';';
	$css .= '--products-contact-color:' . $contact_color . ';';
	$css .= '--products-button-radius:' . $button_radius . 'px;';
	$css .= '--products-button-bg:' . $button_bg . ';';
	$css .= '--products-button-hover-bg:' . $button_hover_bg . ';';
	$css .= '--products-button-color:' . $button_color . ';';
	$css .= '--products-transition:' . $transition . 'ms;';
	$css .= '--products-dot-color:' . $dot_color . ';';
	$css .= '--products-dot-active:' . $dot_active . ';';
	$css .= '--products-dot-size:' . $dot_size . 'px;';
	$css .= '--products-dot-gap:' . $dot_gap . 'px;';
	$css .= '}';

	// Description Line Clamp: 0 = tắt — render rule tĩnh thay vì biến, tránh -webkit-line-clamp:0 ẩn hết chữ.
	if ( $desc_clamp > 0 ) {
		$css .= '.products-home__description{display:-webkit-box;-webkit-box-orient:vertical;-webkit-line-clamp:' . $desc_clamp . ';overflow:hidden;}';
	}

	$css .= '@media (max-width:991px){:root{';
	$css .= '--products-padding:' . $padding_tablet . 'px;';
	$css .= '--products-cols:' . $cols_tablet . ';';
	$css .= '--products-heading-size:' . $heading_tablet . 'px;';
	$css .= '--products-desc-size:' . $desc_tablet . 'px;';
	$css .= '--products-title-size:' . $title_tablet . 'px;';
	$css .= '}}';

	$css .= '@media (max-width:599px){:root{';
	$css .= '--products-padding:' . $padding_mobile . 'px;';
	$css .= '--products-cols:' . $cols_mobile . ';';
	$css .= '--products-heading-size:' . $heading_mobile . 'px;';
	$css .= '--products-desc-size:' . $desc_mobile . 'px;';
	$css .= '--products-title-size:' . $title_mobile . 'px;';
	$css .= '}}';

	return $css;
}

/* ==========================================================================
 * WHY CHOOSE SECTION (trang chủ) — helper riêng, không đụng các module khác.
 *
 * "Tại sao nên chọn Thang Máy Nhân Phát": toàn bộ nội dung (Title/Description/
 * Icon/Image/Button từng Feature Item) quản lý 100% trong Customizer panel
 * "Why Choose Home" — KHÔNG dùng WP_Query/ACF/Meta Box (khác Services/Products).
 * Feature Item dùng pattern Fixed-slot Repeater (PROJECT_RULES.md mục 24) như
 * Partners: N slot cố định + Enable từng slot, sort theo Order.
 *
 * Slider tái sử dụng markup .tmnp-slider + assets/js/components/slider.js của
 * Services (file đó generic sẵn — chỉ cần đúng markup + data-attribute). Section
 * này KHÔNG có Dots/Arrows: chỉ autoplay tự trượt + drag/swipe trái phải.
 * ========================================================================== */

/**
 * Số lượng "slot" Feature Item tối đa hỗ trợ trong Customizer (xem lý do fixed-slot
 * tại tmnhanphat_get_partners_slot_count() — Core Customizer không có Repeater control).
 *
 * @return int
 */
function tmnhanphat_get_why_choose_slot_count() {
	return 8;
}

/**
 * Default value tập trung cho toàn bộ setting của Why Choose Section (mục 22).
 *
 * @return array<string, mixed>
 */
function tmnhanphat_why_choose_defaults() {
	$defaults = array(
		// ----- General -----
		'tmnhanphat_why_choose_enable'          => true,
		'tmnhanphat_why_choose_section_id'      => 'why-choose',
		'tmnhanphat_why_choose_container_width' => 0,
		'tmnhanphat_why_choose_bg_color'        => '#ffffff',
		'tmnhanphat_why_choose_bg_image'        => '',
		'tmnhanphat_why_choose_overlay_enable'  => false,
		'tmnhanphat_why_choose_overlay_color'   => '#ffffff',
		'tmnhanphat_why_choose_overlay_opacity' => 80,
		'tmnhanphat_why_choose_padding_desktop' => 64, // Migrate: nhịp dọc mới (92→64).
		'tmnhanphat_why_choose_padding_tablet'  => 72,
		'tmnhanphat_why_choose_padding_mobile'  => 48,
		'tmnhanphat_why_choose_margin_desktop'  => 0,
		'tmnhanphat_why_choose_margin_tablet'   => 0,
		'tmnhanphat_why_choose_margin_mobile'   => 0,

		// ----- Decoration (tam giác xanh góc trái — thiết kế 828.5 × 857.5) -----
		'tmnhanphat_why_choose_deco_enable'      => true,
		'tmnhanphat_why_choose_deco_image'       => '',
		'tmnhanphat_why_choose_deco_width'       => 828,
		'tmnhanphat_why_choose_deco_height'      => 0,
		'tmnhanphat_why_choose_deco_offset_x'    => -2, // Figma: Rectangle X = -2 (tràn nhẹ mép trái).
		'tmnhanphat_why_choose_deco_offset_y'    => 24, // Figma: tam giác thấp hơn tâm section 24px.
		'tmnhanphat_why_choose_deco_hide_mobile' => true,

		// ----- Heading (2 dòng 2 màu: xanh trên, đỏ dưới — đúng design) -----
		'tmnhanphat_why_choose_heading_blue_text'      => 'Tại sao nên chọn',
		'tmnhanphat_why_choose_heading_red_text'       => 'Thang Máy Nhân Phát',
		'tmnhanphat_why_choose_heading_blue_color'     => '#046AB5',
		'tmnhanphat_why_choose_heading_red_color'      => '#E31F2B',
		// Figma: Heading 96/900 lh1.21, 2 dòng; Heading → Description cách 24px.
		'tmnhanphat_why_choose_heading_size'           => 96,
		'tmnhanphat_why_choose_heading_size_tablet'    => 48,
		'tmnhanphat_why_choose_heading_size_mobile'    => 30,
		'tmnhanphat_why_choose_heading_weight'         => '900',
		'tmnhanphat_why_choose_heading_line_height'    => 1.21,
		'tmnhanphat_why_choose_heading_letter_spacing' => -2, // Taste B: tracking âm nhẹ cho display (-2px ≈ -0.02em @ heading 96px).
		'tmnhanphat_why_choose_heading_align'          => 'left',
		'tmnhanphat_why_choose_heading_margin_bottom'  => 24,

		// ----- Description -----
		'tmnhanphat_why_choose_desc_text'          => 'Sản phẩm chính hãng, đội ngũ kỹ thuật chuyên nghiệp cùng chế độ bảo hành - bảo trì tận tâm là lý do hàng nghìn khách hàng tin chọn Nhân Phát.',
		// Figma: Description 20/400 ĐEN, box 1080; Description → Cards cách 86px.
		'tmnhanphat_why_choose_desc_color'         => '#000000',
		'tmnhanphat_why_choose_desc_size'          => 20,
		'tmnhanphat_why_choose_desc_size_tablet'   => 16,
		'tmnhanphat_why_choose_desc_size_mobile'   => 14,
		'tmnhanphat_why_choose_desc_max_width'     => 820, // Figma 1080 → hạ 820 cho measure dễ đọc (cùng lý do Services).
		'tmnhanphat_why_choose_desc_clamp'         => 0,
		'tmnhanphat_why_choose_desc_margin_bottom' => 86,

		// ----- Slider (KHÔNG có dots — autoplay + drag/swipe) -----
		'tmnhanphat_why_choose_cards_desktop'    => '4',
		'tmnhanphat_why_choose_cards_tablet'     => '2',
		'tmnhanphat_why_choose_cards_mobile'     => '1',
		'tmnhanphat_why_choose_gap_desktop'      => 10, // Figma: card cách nhau 10px.
		'tmnhanphat_why_choose_gap_tablet'       => 16,
		'tmnhanphat_why_choose_gap_mobile'       => 16,
		'tmnhanphat_why_choose_autoplay_enable'  => true,
		'tmnhanphat_why_choose_autoplay_speed'   => 4000,
		'tmnhanphat_why_choose_transition_speed' => 600,
		'tmnhanphat_why_choose_pause_hover'      => true,
		'tmnhanphat_why_choose_infinite'         => true,
		'tmnhanphat_why_choose_drag_enable'      => true,

		// ----- Card (Figma: 453 × 459, radius 39, ảnh 423 × 290 inset 15) -----
		// Figma: viền card 1px ĐEN, KHÔNG shadow; nội dung cách mép trái/trên card ~48.
		'tmnhanphat_why_choose_card_height'          => 459,
		'tmnhanphat_why_choose_card_radius'          => 28, // Migrate: radius mềm (39→28).
		'tmnhanphat_why_choose_card_border_width'    => 1,
		'tmnhanphat_why_choose_card_border_color'    => '#000000',
		'tmnhanphat_why_choose_card_bg'              => '#ffffff',
		'tmnhanphat_why_choose_card_shadow'          => 'none',
		'tmnhanphat_why_choose_card_hover_shadow'    => 'medium',
		'tmnhanphat_why_choose_card_hover_translate' => 6,
		'tmnhanphat_why_choose_card_padding'         => 34, // Migrate: padding card mềm (48→34).
		'tmnhanphat_why_choose_card_content_align'   => 'flex-start',
		'tmnhanphat_why_choose_icon_enable'          => false,

		// Figma: Title card 28/700 ĐEN, cách Description 14; Description card 20/400 #464646.
		'tmnhanphat_why_choose_card_title_size'          => 28,
		'tmnhanphat_why_choose_card_title_size_tablet'   => 20,
		'tmnhanphat_why_choose_card_title_size_mobile'   => 18,
		'tmnhanphat_why_choose_card_title_weight'        => '700',
		'tmnhanphat_why_choose_card_title_color'         => '#000000',
		'tmnhanphat_why_choose_card_title_clamp'         => 2,
		'tmnhanphat_why_choose_card_title_margin_bottom' => 14,

		'tmnhanphat_why_choose_card_desc_size'          => 20,
		'tmnhanphat_why_choose_card_desc_size_tablet'   => 15,
		'tmnhanphat_why_choose_card_desc_size_mobile'   => 14,
		'tmnhanphat_why_choose_card_desc_color'         => '#464646',
		'tmnhanphat_why_choose_card_desc_clamp'         => 4,
		'tmnhanphat_why_choose_card_desc_margin_bottom' => 12, // Figma: Description → Image gần nhau hơn.

		'tmnhanphat_why_choose_card_image_height'     => 290,
		'tmnhanphat_why_choose_card_image_radius'     => 0, // Figma: Rayon d'angle ảnh = 0.
		'tmnhanphat_why_choose_card_image_fit'        => 'contain',
		'tmnhanphat_why_choose_card_image_zoom'       => true,
		'tmnhanphat_why_choose_card_image_margin_top' => 8,
		// Figma: ảnh 423 rộng hơn vùng text — inset mép card chỉ 15px ((453-423)/2), nhỏ hơn
		// Card Padding (30). CSS dùng margin âm (inset - padding) để ảnh "bung" khỏi padding.
		'tmnhanphat_why_choose_card_image_inset'      => 15,
	);

	// Nội dung mẫu theo design cho 4 slot đầu — slot 5-8 tắt sẵn, admin bật khi cần.
	$sample_items = array(
		1 => array(
			'title'       => 'Kinh nghiệm lâu năm',
			'description' => 'Hơn 10 năm kinh nghiệm trong lĩnh vực thang máy, thấu hiểu nhu cầu khách hàng.',
		),
		2 => array(
			'title'       => 'Sản phẩm chính hãng',
			'description' => 'Cam kết cung cấp thang máy và linh kiện đạt chuẩn, nguồn gốc rõ ràng, bảo hành minh bạch cho từng công trình.',
		),
		3 => array(
			'title'       => 'Chi phí minh bạch',
			'description' => 'Báo giá rõ ràng theo từng hạng mục, giúp khách hàng dễ dàng kiểm soát ngân sách và lựa chọn giải pháp phù hợp.',
		),
		4 => array(
			'title'       => 'Dịch vụ tận tâm',
			'description' => 'Đội ngũ kỹ thuật chuyên nghiệp, hỗ trợ nhanh chóng và bảo trì định kỳ trong suốt quá trình sử dụng.',
		),
	);

	for ( $i = 1; $i <= tmnhanphat_get_why_choose_slot_count(); $i++ ) {
		$sample = isset( $sample_items[ $i ] ) ? $sample_items[ $i ] : array(
			'title'       => '',
			'description' => '',
		);

		$defaults[ "tmnhanphat_why_choose_item{$i}_enable" ]      = isset( $sample_items[ $i ] );
		$defaults[ "tmnhanphat_why_choose_item{$i}_icon" ]        = '';
		$defaults[ "tmnhanphat_why_choose_item{$i}_title" ]       = $sample['title'];
		$defaults[ "tmnhanphat_why_choose_item{$i}_description" ] = $sample['description'];
		$defaults[ "tmnhanphat_why_choose_item{$i}_image" ]       = '';
		$defaults[ "tmnhanphat_why_choose_item{$i}_btn_enable" ]  = false;
		$defaults[ "tmnhanphat_why_choose_item{$i}_btn_text" ]    = '';
		$defaults[ "tmnhanphat_why_choose_item{$i}_btn_url" ]     = '';
		$defaults[ "tmnhanphat_why_choose_item{$i}_btn_new_tab" ] = false;
		$defaults[ "tmnhanphat_why_choose_item{$i}_order" ]       = $i;
	}

	return $defaults;
}

/**
 * Đọc 1 theme_mod của Why Choose với default tập trung — template KHÔNG gọi
 * get_theme_mod() trực tiếp (mục 22).
 *
 * @param string $key Setting id.
 * @return mixed
 */
function tmnhanphat_get_why_choose_mod( $key ) {
	$defaults = tmnhanphat_why_choose_defaults();
	$default  = isset( $defaults[ $key ] ) ? $defaults[ $key ] : '';

	return get_theme_mod( $key, $default );
}

/**
 * Xây danh sách Feature Item từ các slot: chỉ lấy slot Enable + có Title, sort theo
 * Order tăng dần — template không bao giờ loop slot thô (mục 24).
 *
 * @return array<int, array<string, mixed>>
 */
function tmnhanphat_get_why_choose_items() {
	$items = array();

	for ( $i = 1; $i <= tmnhanphat_get_why_choose_slot_count(); $i++ ) {
		$enabled = tmnhanphat_get_why_choose_mod( "tmnhanphat_why_choose_item{$i}_enable" );
		$title   = tmnhanphat_get_why_choose_mod( "tmnhanphat_why_choose_item{$i}_title" );

		if ( ! $enabled || ! $title ) {
			continue;
		}

		$items[] = array(
			'icon'        => tmnhanphat_get_why_choose_mod( "tmnhanphat_why_choose_item{$i}_icon" ),
			'title'       => $title,
			'description' => tmnhanphat_get_why_choose_mod( "tmnhanphat_why_choose_item{$i}_description" ),
			'image'       => tmnhanphat_get_why_choose_mod( "tmnhanphat_why_choose_item{$i}_image" ),
			'btn_enable'  => (bool) tmnhanphat_get_why_choose_mod( "tmnhanphat_why_choose_item{$i}_btn_enable" ),
			'btn_text'    => tmnhanphat_get_why_choose_mod( "tmnhanphat_why_choose_item{$i}_btn_text" ),
			'btn_url'     => tmnhanphat_get_why_choose_mod( "tmnhanphat_why_choose_item{$i}_btn_url" ),
			'btn_new_tab' => (bool) tmnhanphat_get_why_choose_mod( "tmnhanphat_why_choose_item{$i}_btn_new_tab" ),
			'order'       => absint( tmnhanphat_get_why_choose_mod( "tmnhanphat_why_choose_item{$i}_order" ) ),
		);
	}

	usort(
		$items,
		static function ( $a, $b ) {
			return $a['order'] <=> $b['order'];
		}
	);

	return $items;
}

/**
 * Danh sách lựa chọn căn dọc nội dung trong Card.
 *
 * @return array<string, string>
 */
function tmnhanphat_get_why_choose_content_align_choices() {
	return array(
		'flex-start' => __( 'Đầu (Top)', 'tmnhanphat' ),
		'center'     => __( 'Giữa (Center)', 'tmnhanphat' ),
		'flex-end'   => __( 'Cuối (Bottom)', 'tmnhanphat' ),
	);
}

/**
 * Whitelist "Content Alignment".
 *
 * @param string $value Giá trị gửi lên từ Customizer.
 * @return string
 */
function tmnhanphat_sanitize_why_choose_content_align( $value ) {
	$choices = array_keys( tmnhanphat_get_why_choose_content_align_choices() );

	return in_array( $value, $choices, true ) ? $value : 'flex-start';
}

/**
 * Danh sách lựa chọn "Object Fit" cho ảnh trong Card.
 *
 * @return array<string, string>
 */
function tmnhanphat_get_why_choose_image_fit_choices() {
	return array(
		'contain' => __( 'Contain (trọn ảnh, không cắt)', 'tmnhanphat' ),
		'cover'   => __( 'Cover (phủ kín khung, crop center)', 'tmnhanphat' ),
	);
}

/**
 * Whitelist "Object Fit".
 *
 * @param string $value Giá trị gửi lên từ Customizer.
 * @return string
 */
function tmnhanphat_sanitize_why_choose_image_fit( $value ) {
	$choices = array_keys( tmnhanphat_get_why_choose_image_fit_choices() );

	return in_array( $value, $choices, true ) ? $value : 'contain';
}

/**
 * Danh sách lựa chọn bóng đổ Card (dùng cho cả Box Shadow lẫn Hover Shadow).
 *
 * @return array<string, string>
 */
function tmnhanphat_get_why_choose_shadow_choices() {
	return array(
		'none'   => __( 'Không có', 'tmnhanphat' ),
		'soft'   => __( 'Nhẹ', 'tmnhanphat' ),
		'medium' => __( 'Vừa', 'tmnhanphat' ),
	);
}

/**
 * Whitelist Shadow.
 *
 * @param string $value Giá trị gửi lên từ Customizer.
 * @return string
 */
function tmnhanphat_sanitize_why_choose_shadow( $value ) {
	$choices = array_keys( tmnhanphat_get_why_choose_shadow_choices() );

	return in_array( $value, $choices, true ) ? $value : 'soft';
}

/**
 * Quy đổi key bóng đổ sang giá trị box-shadow CSS thực tế.
 *
 * @param string $key Key trong tmnhanphat_get_why_choose_shadow_choices().
 * @return string
 */
function tmnhanphat_get_why_choose_shadow_value( $key ) {
	$shadows = array(
		'none'   => 'none',
		'soft'   => '0 6px 20px rgb(6 24 44 / 0.06)',
		'medium' => '0 14px 34px rgb(6 24 44 / 0.14)',
	);

	return isset( $shadows[ $key ] ) ? $shadows[ $key ] : $shadows['soft'];
}

/**
 * Line Height heading cho phép số thập phân — absint() làm mất phần lẻ (cùng lý do
 * tmnhanphat_sanitize_products_line_height, mỗi module giữ sanitizer riêng).
 *
 * @param mixed $value Giá trị gửi lên từ Customizer.
 * @return float
 */
function tmnhanphat_sanitize_why_choose_line_height( $value ) {
	return max( 0.8, min( 3, (float) $value ) );
}

/**
 * Letter Spacing cho phép số ÂM (px).
 *
 * @param mixed $value Giá trị gửi lên từ Customizer.
 * @return int
 */
function tmnhanphat_sanitize_why_choose_letter_spacing( $value ) {
	return max( -5, min( 10, (int) $value ) );
}

/**
 * Offset Decoration cho phép số ÂM (px) — decoration lớn (828px) nên khoảng cho phép rộng.
 *
 * @param mixed $value Giá trị gửi lên từ Customizer.
 * @return int
 */
function tmnhanphat_sanitize_why_choose_deco_offset( $value ) {
	return max( -800, min( 800, (int) $value ) );
}

/**
 * Render 1 ảnh của Why Choose đúng chuẩn WordPress (mục 13): quy URL Customizer về
 * attachment ID để dùng wp_get_attachment_image() (có srcset + lazy load qua filter
 * tmnhanphat_image_attributes) — fallback <img> thường nếu không resolve được ID.
 *
 * @param string $url   URL ảnh lấy từ theme_mod.
 * @param string $alt   Alt text ('' cho ảnh trang trí).
 * @param string $size  Image size đăng ký trong inc/images.php (hoặc 'full').
 * @param string $class Class gắn lên <img>.
 * @return string HTML đã escape, sẵn sàng echo.
 */
function tmnhanphat_get_why_choose_image_html( $url, $alt, $size, $class ) {
	$attachment_id = attachment_url_to_postid( $url );

	if ( $attachment_id ) {
		return wp_get_attachment_image( $attachment_id, $size, false, array(
			'class' => $class,
			'alt'   => $alt,
		) );
	}

	return sprintf(
		'<img class="%s" src="%s" alt="%s" loading="lazy" decoding="async" />',
		esc_attr( $class ),
		esc_url( $url ),
		esc_attr( $alt )
	);
}

/**
 * Sinh chuỗi CSS custom properties cho Why Choose Section, gắn vào stylesheet qua
 * wp_add_inline_style() trong inc/enqueue.php — cùng quy ước mọi module (mục 22).
 *
 * @return string
 */
function tmnhanphat_render_why_choose_css_vars() {
	$bg_color        = tmnhanphat_get_why_choose_mod( 'tmnhanphat_why_choose_bg_color' );
	$container_width = absint( tmnhanphat_get_why_choose_mod( 'tmnhanphat_why_choose_container_width' ) );
	$overlay_enable  = tmnhanphat_get_why_choose_mod( 'tmnhanphat_why_choose_overlay_enable' );
	$overlay_color   = tmnhanphat_get_why_choose_mod( 'tmnhanphat_why_choose_overlay_color' );
	$overlay_opacity = absint( tmnhanphat_get_why_choose_mod( 'tmnhanphat_why_choose_overlay_opacity' ) );

	$padding_desktop = absint( tmnhanphat_get_why_choose_mod( 'tmnhanphat_why_choose_padding_desktop' ) );
	$padding_tablet  = absint( tmnhanphat_get_why_choose_mod( 'tmnhanphat_why_choose_padding_tablet' ) );
	$padding_mobile  = absint( tmnhanphat_get_why_choose_mod( 'tmnhanphat_why_choose_padding_mobile' ) );
	$margin_desktop  = absint( tmnhanphat_get_why_choose_mod( 'tmnhanphat_why_choose_margin_desktop' ) );
	$margin_tablet   = absint( tmnhanphat_get_why_choose_mod( 'tmnhanphat_why_choose_margin_tablet' ) );
	$margin_mobile   = absint( tmnhanphat_get_why_choose_mod( 'tmnhanphat_why_choose_margin_mobile' ) );

	$deco_width       = absint( tmnhanphat_get_why_choose_mod( 'tmnhanphat_why_choose_deco_width' ) );
	$deco_height      = absint( tmnhanphat_get_why_choose_mod( 'tmnhanphat_why_choose_deco_height' ) );
	$deco_offset_x    = (int) tmnhanphat_get_why_choose_mod( 'tmnhanphat_why_choose_deco_offset_x' );
	$deco_offset_y    = (int) tmnhanphat_get_why_choose_mod( 'tmnhanphat_why_choose_deco_offset_y' );
	$deco_hide_mobile = tmnhanphat_get_why_choose_mod( 'tmnhanphat_why_choose_deco_hide_mobile' );

	$heading_blue_color = tmnhanphat_get_why_choose_mod( 'tmnhanphat_why_choose_heading_blue_color' );
	$heading_red_color  = tmnhanphat_get_why_choose_mod( 'tmnhanphat_why_choose_heading_red_color' );
	$heading_size       = absint( tmnhanphat_get_why_choose_mod( 'tmnhanphat_why_choose_heading_size' ) );
	$heading_tablet     = absint( tmnhanphat_get_why_choose_mod( 'tmnhanphat_why_choose_heading_size_tablet' ) );
	$heading_mobile     = absint( tmnhanphat_get_why_choose_mod( 'tmnhanphat_why_choose_heading_size_mobile' ) );
	$heading_weight     = absint( tmnhanphat_get_why_choose_mod( 'tmnhanphat_why_choose_heading_weight' ) );
	$heading_lh         = (float) tmnhanphat_get_why_choose_mod( 'tmnhanphat_why_choose_heading_line_height' );
	$heading_ls         = (int) tmnhanphat_get_why_choose_mod( 'tmnhanphat_why_choose_heading_letter_spacing' );
	$heading_align      = tmnhanphat_get_why_choose_mod( 'tmnhanphat_why_choose_heading_align' );
	$heading_margin     = absint( tmnhanphat_get_why_choose_mod( 'tmnhanphat_why_choose_heading_margin_bottom' ) );

	$desc_color  = tmnhanphat_get_why_choose_mod( 'tmnhanphat_why_choose_desc_color' );
	$desc_size   = absint( tmnhanphat_get_why_choose_mod( 'tmnhanphat_why_choose_desc_size' ) );
	$desc_tablet = absint( tmnhanphat_get_why_choose_mod( 'tmnhanphat_why_choose_desc_size_tablet' ) );
	$desc_mobile = absint( tmnhanphat_get_why_choose_mod( 'tmnhanphat_why_choose_desc_size_mobile' ) );
	$desc_width  = absint( tmnhanphat_get_why_choose_mod( 'tmnhanphat_why_choose_desc_max_width' ) );
	$desc_clamp  = absint( tmnhanphat_get_why_choose_mod( 'tmnhanphat_why_choose_desc_clamp' ) );
	$desc_margin = absint( tmnhanphat_get_why_choose_mod( 'tmnhanphat_why_choose_desc_margin_bottom' ) );

	$cols_desktop = max( 1, absint( tmnhanphat_get_why_choose_mod( 'tmnhanphat_why_choose_cards_desktop' ) ) );
	$cols_tablet  = max( 1, absint( tmnhanphat_get_why_choose_mod( 'tmnhanphat_why_choose_cards_tablet' ) ) );
	$cols_mobile  = max( 1, absint( tmnhanphat_get_why_choose_mod( 'tmnhanphat_why_choose_cards_mobile' ) ) );
	$gap_desktop  = absint( tmnhanphat_get_why_choose_mod( 'tmnhanphat_why_choose_gap_desktop' ) );
	$gap_tablet   = absint( tmnhanphat_get_why_choose_mod( 'tmnhanphat_why_choose_gap_tablet' ) );
	$gap_mobile   = absint( tmnhanphat_get_why_choose_mod( 'tmnhanphat_why_choose_gap_mobile' ) );
	$transition   = absint( tmnhanphat_get_why_choose_mod( 'tmnhanphat_why_choose_transition_speed' ) );

	$card_height       = absint( tmnhanphat_get_why_choose_mod( 'tmnhanphat_why_choose_card_height' ) );
	$card_radius       = absint( tmnhanphat_get_why_choose_mod( 'tmnhanphat_why_choose_card_radius' ) );
	$card_border_width = absint( tmnhanphat_get_why_choose_mod( 'tmnhanphat_why_choose_card_border_width' ) );
	$card_border_color = tmnhanphat_get_why_choose_mod( 'tmnhanphat_why_choose_card_border_color' );
	$card_bg           = tmnhanphat_get_why_choose_mod( 'tmnhanphat_why_choose_card_bg' );
	$card_shadow       = tmnhanphat_get_why_choose_shadow_value( tmnhanphat_get_why_choose_mod( 'tmnhanphat_why_choose_card_shadow' ) );
	$card_hover_shadow = tmnhanphat_get_why_choose_shadow_value( tmnhanphat_get_why_choose_mod( 'tmnhanphat_why_choose_card_hover_shadow' ) );
	$hover_translate   = absint( tmnhanphat_get_why_choose_mod( 'tmnhanphat_why_choose_card_hover_translate' ) );
	$card_padding      = absint( tmnhanphat_get_why_choose_mod( 'tmnhanphat_why_choose_card_padding' ) );
	$content_align     = tmnhanphat_get_why_choose_mod( 'tmnhanphat_why_choose_card_content_align' );

	$title_size   = absint( tmnhanphat_get_why_choose_mod( 'tmnhanphat_why_choose_card_title_size' ) );
	$title_tablet = absint( tmnhanphat_get_why_choose_mod( 'tmnhanphat_why_choose_card_title_size_tablet' ) );
	$title_mobile = absint( tmnhanphat_get_why_choose_mod( 'tmnhanphat_why_choose_card_title_size_mobile' ) );
	$title_weight = absint( tmnhanphat_get_why_choose_mod( 'tmnhanphat_why_choose_card_title_weight' ) );
	$title_color  = tmnhanphat_get_why_choose_mod( 'tmnhanphat_why_choose_card_title_color' );
	$title_clamp  = absint( tmnhanphat_get_why_choose_mod( 'tmnhanphat_why_choose_card_title_clamp' ) );
	$title_margin = absint( tmnhanphat_get_why_choose_mod( 'tmnhanphat_why_choose_card_title_margin_bottom' ) );

	$cdesc_size   = absint( tmnhanphat_get_why_choose_mod( 'tmnhanphat_why_choose_card_desc_size' ) );
	$cdesc_tablet = absint( tmnhanphat_get_why_choose_mod( 'tmnhanphat_why_choose_card_desc_size_tablet' ) );
	$cdesc_mobile = absint( tmnhanphat_get_why_choose_mod( 'tmnhanphat_why_choose_card_desc_size_mobile' ) );
	$cdesc_color  = tmnhanphat_get_why_choose_mod( 'tmnhanphat_why_choose_card_desc_color' );
	$cdesc_clamp  = absint( tmnhanphat_get_why_choose_mod( 'tmnhanphat_why_choose_card_desc_clamp' ) );
	$cdesc_margin = absint( tmnhanphat_get_why_choose_mod( 'tmnhanphat_why_choose_card_desc_margin_bottom' ) );

	$image_height     = absint( tmnhanphat_get_why_choose_mod( 'tmnhanphat_why_choose_card_image_height' ) );
	$image_radius     = absint( tmnhanphat_get_why_choose_mod( 'tmnhanphat_why_choose_card_image_radius' ) );
	$image_fit        = tmnhanphat_get_why_choose_mod( 'tmnhanphat_why_choose_card_image_fit' );
	$image_zoom       = tmnhanphat_get_why_choose_mod( 'tmnhanphat_why_choose_card_image_zoom' );
	$image_margin_top = absint( tmnhanphat_get_why_choose_mod( 'tmnhanphat_why_choose_card_image_margin_top' ) );
	$image_inset      = absint( tmnhanphat_get_why_choose_mod( 'tmnhanphat_why_choose_card_image_inset' ) );

	$css  = ':root{';
	$css .= '--why-choose-bg:' . $bg_color . ';';
	$css .= '--why-choose-container-width:' . ( $container_width > 0 ? $container_width . 'px' : 'var(--container-max-width)' ) . ';';
	$css .= '--why-choose-overlay:' . ( $overlay_enable ? tmnhanphat_hex_to_rgba( $overlay_color, $overlay_opacity ) : 'transparent' ) . ';';
	$css .= '--why-choose-padding:' . $padding_desktop . 'px;';
	$css .= '--why-choose-margin:' . $margin_desktop . 'px;';
	$css .= '--why-choose-deco-width:' . $deco_width . 'px;';
	$css .= '--why-choose-deco-height:' . ( $deco_height > 0 ? $deco_height . 'px' : 'auto' ) . ';';
	$css .= '--why-choose-deco-top:' . $deco_offset_y . 'px;';
	$css .= '--why-choose-deco-left:' . $deco_offset_x . 'px;';
	$css .= '--why-choose-heading-blue:' . $heading_blue_color . ';';
	$css .= '--why-choose-heading-red:' . $heading_red_color . ';';
	$css .= '--why-choose-heading-size:' . $heading_size . 'px;';
	$css .= '--why-choose-heading-weight:' . $heading_weight . ';';
	$css .= '--why-choose-heading-lh:' . $heading_lh . ';';
	$css .= '--why-choose-heading-ls:' . $heading_ls . 'px;';
	$css .= '--why-choose-heading-align:' . $heading_align . ';';
	$css .= '--why-choose-heading-margin:' . $heading_margin . 'px;';
	$css .= '--why-choose-desc-color:' . $desc_color . ';';
	$css .= '--why-choose-desc-size:' . $desc_size . 'px;';
	$css .= '--why-choose-desc-max-width:' . $desc_width . 'px;';
	$css .= '--why-choose-desc-margin:' . $desc_margin . 'px;';
	// Description căn theo Heading Alignment: center → hộp max-width tự căn giữa.
	$css .= '--why-choose-desc-margin-x:' . ( 'center' === $heading_align ? 'auto' : '0' ) . ';';
	$css .= '--why-choose-cols:' . $cols_desktop . ';';
	$css .= '--why-choose-gap:' . $gap_desktop . 'px;';
	$css .= '--why-choose-transition:' . $transition . 'ms;';
	$css .= '--why-choose-card-height:' . ( $card_height > 0 ? $card_height . 'px' : 'auto' ) . ';';
	$css .= '--why-choose-card-radius:' . $card_radius . 'px;';
	$css .= '--why-choose-card-border:' . ( $card_border_width > 0 ? $card_border_width . 'px solid ' . $card_border_color : 'none' ) . ';';
	$css .= '--why-choose-card-bg:' . $card_bg . ';';
	$css .= '--why-choose-card-shadow:' . $card_shadow . ';';
	$css .= '--why-choose-card-hover-shadow:' . $card_hover_shadow . ';';
	$css .= '--why-choose-card-hover-translate:-' . $hover_translate . 'px;';
	$css .= '--why-choose-card-padding:' . $card_padding . 'px;';
	$css .= '--why-choose-card-align:' . $content_align . ';';
	$css .= '--why-choose-title-size:' . $title_size . 'px;';
	$css .= '--why-choose-title-weight:' . $title_weight . ';';
	$css .= '--why-choose-title-color:' . $title_color . ';';
	$css .= '--why-choose-title-margin:' . $title_margin . 'px;';
	$css .= '--why-choose-card-desc-size:' . $cdesc_size . 'px;';
	$css .= '--why-choose-card-desc-color:' . $cdesc_color . ';';
	$css .= '--why-choose-card-desc-margin:' . $cdesc_margin . 'px;';
	$css .= '--why-choose-image-height:' . $image_height . 'px;';
	$css .= '--why-choose-image-radius:' . $image_radius . 'px;';
	$css .= '--why-choose-image-fit:' . $image_fit . ';';
	$css .= '--why-choose-image-zoom:' . ( $image_zoom ? '1.05' : '1' ) . ';';
	$css .= '--why-choose-image-margin-top:' . $image_margin_top . 'px;';
	$css .= '--why-choose-image-inset:' . $image_inset . 'px;';
	$css .= '}';

	// Line Clamp = 0 nghĩa là tắt — render rule tĩnh, tránh -webkit-line-clamp:0 ẩn hết chữ
	// (cùng pattern Products).
	if ( $desc_clamp > 0 ) {
		$css .= '.why-choose-home__description{display:-webkit-box;-webkit-box-orient:vertical;-webkit-line-clamp:' . $desc_clamp . ';overflow:hidden;}';
	}

	if ( $title_clamp > 0 ) {
		$css .= '.feature-card__title{display:-webkit-box;-webkit-box-orient:vertical;-webkit-line-clamp:' . $title_clamp . ';overflow:hidden;}';
	}

	if ( $cdesc_clamp > 0 ) {
		$css .= '.feature-card__description{display:-webkit-box;-webkit-box-orient:vertical;-webkit-line-clamp:' . $cdesc_clamp . ';overflow:hidden;}';
	}

	$css .= '@media (max-width:991px){:root{';
	$css .= '--why-choose-padding:' . $padding_tablet . 'px;';
	$css .= '--why-choose-margin:' . $margin_tablet . 'px;';
	$css .= '--why-choose-cols:' . $cols_tablet . ';';
	$css .= '--why-choose-gap:' . $gap_tablet . 'px;';
	$css .= '--why-choose-heading-size:' . $heading_tablet . 'px;';
	$css .= '--why-choose-desc-size:' . $desc_tablet . 'px;';
	$css .= '--why-choose-title-size:' . $title_tablet . 'px;';
	$css .= '--why-choose-card-desc-size:' . $cdesc_tablet . 'px;';
	$css .= '}}';

	$css .= '@media (max-width:599px){:root{';
	$css .= '--why-choose-padding:' . $padding_mobile . 'px;';
	$css .= '--why-choose-margin:' . $margin_mobile . 'px;';
	$css .= '--why-choose-cols:' . $cols_mobile . ';';
	$css .= '--why-choose-gap:' . $gap_mobile . 'px;';
	$css .= '--why-choose-heading-size:' . $heading_mobile . 'px;';
	$css .= '--why-choose-desc-size:' . $desc_mobile . 'px;';
	$css .= '--why-choose-title-size:' . $title_mobile . 'px;';
	$css .= '--why-choose-card-desc-size:' . $cdesc_mobile . 'px;';
	$css .= '}';

	if ( $deco_hide_mobile ) {
		$css .= '.why-choose-home__decoration{display:none;}';
	}

	$css .= '}';

	return $css;
}

/* ==========================================================================
 * PROCESS SECTION ("Quy trình hợp tác", trang chủ) — helper riêng.
 *
 * Layout 2 cột: trái = Timeline Steps (đường dọc + circle số thứ tự, auto-active
 * xoay vòng bằng assets/js/components/process.js), phải = Image (đồng bộ theo
 * Step active nếu Step có ảnh riêng — fallback ảnh mặc định của section).
 * Toàn bộ nội dung từ Customizer panel "Process Home" — KHÔNG WP_Query/ACF.
 * Steps dùng pattern Fixed-slot Repeater (PROJECT_RULES.md mục 24) như Partners/
 * Why Choose: N slot cố định + Enable từng slot, sort theo Order.
 *
 * Progressive enhancement: PHP render sẵn Step 1 active — không có JS thì timeline
 * đứng yên ở Step 1, nội dung vẫn đọc đủ (không gắn class ready từ PHP).
 * ========================================================================== */

/**
 * Số lượng "slot" Step tối đa hỗ trợ trong Customizer (xem lý do fixed-slot tại
 * tmnhanphat_get_partners_slot_count()).
 *
 * @return int
 */
function tmnhanphat_get_process_slot_count() {
	return 6;
}

/**
 * Default value tập trung cho toàn bộ setting của Process Section (mục 22).
 *
 * @return array<string, mixed>
 */
function tmnhanphat_process_defaults() {
	$defaults = array(
		// ----- General -----
		'tmnhanphat_process_enable'          => true,
		'tmnhanphat_process_section_id'      => 'quy-trinh',
		'tmnhanphat_process_container_width' => 0,
		'tmnhanphat_process_bg_color'        => '#ffffff',
		'tmnhanphat_process_bg_image'        => '',
		// Figma: đáy Why-Choose (5987) → Heading (6040) = 53; đáy = 0 (ảnh phải chạm Projects).
		'tmnhanphat_process_padding_desktop' => 40, // Migrate: nhịp dọc mới (53→40).
		'tmnhanphat_process_padding_tablet'  => 64,
		'tmnhanphat_process_padding_mobile'  => 48,
		'tmnhanphat_process_margin_desktop'  => 0,
		'tmnhanphat_process_margin_tablet'   => 0,
		'tmnhanphat_process_margin_mobile'   => 0,

		// ----- Heading (2 màu trên cùng 1 dòng: "Quy trình" xanh + "hợp tác" đỏ) -----
		'tmnhanphat_process_heading_blue_text'      => 'Quy trình',
		'tmnhanphat_process_heading_red_text'       => 'hợp tác',
		'tmnhanphat_process_heading_blue_color'     => '#046AB5',
		'tmnhanphat_process_heading_red_color'      => '#E31F2B',
		// Figma: Heading 96/900 lh1.21; Heading → Description cách 24px.
		'tmnhanphat_process_heading_size'           => 96,
		'tmnhanphat_process_heading_size_tablet'    => 48,
		'tmnhanphat_process_heading_size_mobile'    => 30,
		'tmnhanphat_process_heading_weight'         => '900',
		'tmnhanphat_process_heading_line_height'    => 1.21,
		'tmnhanphat_process_heading_letter_spacing' => -2, // Taste B: tracking âm nhẹ cho display (-2px ≈ -0.02em @ heading 96px).
		'tmnhanphat_process_heading_align'          => 'left',
		'tmnhanphat_process_heading_margin_bottom'  => 24,

		// ----- Description -----
		'tmnhanphat_process_desc_text'          => 'Sản phẩm chính hãng, đội ngũ kỹ thuật chuyên nghiệp cùng chế độ bảo hành - bảo trì tận tâm là lý do hàng nghìn khách hàng tin chọn Nhân Phát.',
		// Figma: Description 20/400 ĐEN, box 1080; Description → Timeline cách 85px.
		'tmnhanphat_process_desc_color'         => '#000000',
		'tmnhanphat_process_desc_size'          => 20,
		'tmnhanphat_process_desc_size_tablet'   => 16,
		'tmnhanphat_process_desc_size_mobile'   => 14,
		'tmnhanphat_process_desc_max_width'     => 820, // Figma 1080 → hạ 820 cho measure dễ đọc (cùng lý do Services).
		'tmnhanphat_process_desc_clamp'         => 0,
		'tmnhanphat_process_desc_margin_bottom' => 85,

		// ----- Timeline (trái) — Figma: cột ~43%, line dọc 1px #ECECEC, circle 76px,
		// step cách nhau 98px; title inactive #ECECEC (nhạt), active #1A1D23. -----
		'tmnhanphat_process_timeline_width'         => 43,
		'tmnhanphat_process_line_color'             => '#ECECEC',
		'tmnhanphat_process_line_width'             => 1,
		'tmnhanphat_process_step_gap'               => 98,
		'tmnhanphat_process_circle_size'            => 56, // Migrate: circle số gọn theo scale mới (76→56); padding-top title tự tính theo biến này.
		'tmnhanphat_process_circle_border_width'    => 1,
		'tmnhanphat_process_circle_border_color'    => '#D9D9D9',
		'tmnhanphat_process_circle_active_bg'       => '#E31F2B',
		'tmnhanphat_process_circle_inactive_bg'     => '#ffffff',
		'tmnhanphat_process_active_text_color'      => '#1A1D23',
		'tmnhanphat_process_inactive_text_color'    => '#ECECEC',
		'tmnhanphat_process_active_desc_opacity'    => 100,
		'tmnhanphat_process_inactive_desc_opacity'  => 15,
		'tmnhanphat_process_step_padding'           => 0,
		'tmnhanphat_process_step_radius'            => 0,

		// ----- Animation (auto-active xoay vòng) -----
		'tmnhanphat_process_animation_enable'   => true,
		'tmnhanphat_process_auto_active_enable' => true,
		'tmnhanphat_process_interval'           => 3000,
		'tmnhanphat_process_transition_speed'   => 400,
		'tmnhanphat_process_pause_hover'        => true,
		'tmnhanphat_process_loop'               => true,

		// ----- Right Image -----
		'tmnhanphat_process_image'        => '',
		'tmnhanphat_process_image_width'  => 0, // 0 = auto theo cột.
		'tmnhanphat_process_image_height' => 0, // 0 = khung landscape 4:3 cố định (CSS aspect-ratio).
		'tmnhanphat_process_image_fit'    => 'cover', // Lấp đầy khung vuông, crop center — ảnh dọc không kéo giãn section.
		'tmnhanphat_process_image_align'  => 'center',
		'tmnhanphat_process_image_radius' => 0, // Figma: ảnh 856×856 không bo góc.
		'tmnhanphat_process_image_lazy'   => true,
	);

	// Nội dung mẫu theo design cho 3 slot đầu — slot 4-6 tắt sẵn, admin bật khi cần.
	$sample_steps = array(
		1 => array(
			'title'       => 'Tiếp nhận yêu cầu',
			'description' => 'Nhận yêu cầu về loại công trình, số tầng, tải trọng mong muốn và ngân sách dự kiến của khách hàng.',
		),
		2 => array(
			'title'       => 'Khảo sát thực tế',
			'description' => 'Đội ngũ kỹ thuật đến trực tiếp công trình để đo đạc, kiểm tra mặt bằng và đánh giá điều kiện lắp đặt.',
		),
		3 => array(
			'title'       => 'Tư vấn giải pháp & báo giá',
			'description' => 'Dựa trên khảo sát, Nhân Phát đề xuất phương án thang máy phù hợp, kèm báo giá chi tiết và minh bạch.',
		),
	);

	for ( $i = 1; $i <= tmnhanphat_get_process_slot_count(); $i++ ) {
		$sample = isset( $sample_steps[ $i ] ) ? $sample_steps[ $i ] : array(
			'title'       => '',
			'description' => '',
		);

		$defaults[ "tmnhanphat_process_step{$i}_enable" ]      = isset( $sample_steps[ $i ] );
		$defaults[ "tmnhanphat_process_step{$i}_number" ]      = ''; // Rỗng = tự đánh số theo thứ tự hiển thị.
		$defaults[ "tmnhanphat_process_step{$i}_title" ]       = $sample['title'];
		$defaults[ "tmnhanphat_process_step{$i}_description" ] = $sample['description'];
		$defaults[ "tmnhanphat_process_step{$i}_icon" ]        = '';
		$defaults[ "tmnhanphat_process_step{$i}_image" ]       = ''; // Ảnh riêng cho Step (Sync Image) — rỗng = dùng ảnh mặc định.
		$defaults[ "tmnhanphat_process_step{$i}_order" ]       = $i;
	}

	return $defaults;
}

/**
 * Đọc 1 theme_mod của Process với default tập trung — template KHÔNG gọi
 * get_theme_mod() trực tiếp (mục 22).
 *
 * @param string $key Setting id.
 * @return mixed
 */
function tmnhanphat_get_process_mod( $key ) {
	$defaults = tmnhanphat_process_defaults();
	$default  = isset( $defaults[ $key ] ) ? $defaults[ $key ] : '';

	return get_theme_mod( $key, $default );
}

/**
 * Xây danh sách Step từ các slot: chỉ lấy slot Enable + có Title, sort theo Order
 * tăng dần; Step Number rỗng được tự đánh số 1..N theo thứ tự SAU KHI sort (mục 24).
 *
 * @return array<int, array<string, mixed>>
 */
function tmnhanphat_get_process_steps() {
	$steps = array();

	for ( $i = 1; $i <= tmnhanphat_get_process_slot_count(); $i++ ) {
		$enabled = tmnhanphat_get_process_mod( "tmnhanphat_process_step{$i}_enable" );
		$title   = tmnhanphat_get_process_mod( "tmnhanphat_process_step{$i}_title" );

		if ( ! $enabled || ! $title ) {
			continue;
		}

		$steps[] = array(
			'number'      => tmnhanphat_get_process_mod( "tmnhanphat_process_step{$i}_number" ),
			'title'       => $title,
			'description' => tmnhanphat_get_process_mod( "tmnhanphat_process_step{$i}_description" ),
			'icon'        => tmnhanphat_get_process_mod( "tmnhanphat_process_step{$i}_icon" ),
			'image'       => tmnhanphat_get_process_mod( "tmnhanphat_process_step{$i}_image" ),
			'order'       => absint( tmnhanphat_get_process_mod( "tmnhanphat_process_step{$i}_order" ) ),
		);
	}

	usort(
		$steps,
		static function ( $a, $b ) {
			return $a['order'] <=> $b['order'];
		}
	);

	foreach ( $steps as $index => $step ) {
		if ( '' === trim( (string) $step['number'] ) ) {
			$steps[ $index ]['number'] = (string) ( $index + 1 );
		}
	}

	return $steps;
}

/**
 * Whitelist Line Height heading (thập phân) — cùng lý do các module trước.
 *
 * @param mixed $value Giá trị gửi lên từ Customizer.
 * @return float
 */
function tmnhanphat_sanitize_process_line_height( $value ) {
	return max( 0.8, min( 3, (float) $value ) );
}

/**
 * Letter Spacing cho phép số ÂM (px).
 *
 * @param mixed $value Giá trị gửi lên từ Customizer.
 * @return int
 */
function tmnhanphat_sanitize_process_letter_spacing( $value ) {
	return max( -5, min( 10, (int) $value ) );
}

/**
 * Sinh chuỗi CSS custom properties cho Process Section — cùng quy ước mọi module (mục 22).
 *
 * @return string
 */
function tmnhanphat_render_process_css_vars() {
	$bg_color        = tmnhanphat_get_process_mod( 'tmnhanphat_process_bg_color' );
	$container_width = absint( tmnhanphat_get_process_mod( 'tmnhanphat_process_container_width' ) );

	$padding_desktop = absint( tmnhanphat_get_process_mod( 'tmnhanphat_process_padding_desktop' ) );
	$padding_tablet  = absint( tmnhanphat_get_process_mod( 'tmnhanphat_process_padding_tablet' ) );
	$padding_mobile  = absint( tmnhanphat_get_process_mod( 'tmnhanphat_process_padding_mobile' ) );
	$margin_desktop  = absint( tmnhanphat_get_process_mod( 'tmnhanphat_process_margin_desktop' ) );
	$margin_tablet   = absint( tmnhanphat_get_process_mod( 'tmnhanphat_process_margin_tablet' ) );
	$margin_mobile   = absint( tmnhanphat_get_process_mod( 'tmnhanphat_process_margin_mobile' ) );

	$heading_blue   = tmnhanphat_get_process_mod( 'tmnhanphat_process_heading_blue_color' );
	$heading_red    = tmnhanphat_get_process_mod( 'tmnhanphat_process_heading_red_color' );
	$heading_size   = absint( tmnhanphat_get_process_mod( 'tmnhanphat_process_heading_size' ) );
	$heading_tablet = absint( tmnhanphat_get_process_mod( 'tmnhanphat_process_heading_size_tablet' ) );
	$heading_mobile = absint( tmnhanphat_get_process_mod( 'tmnhanphat_process_heading_size_mobile' ) );
	$heading_weight = absint( tmnhanphat_get_process_mod( 'tmnhanphat_process_heading_weight' ) );
	$heading_lh     = (float) tmnhanphat_get_process_mod( 'tmnhanphat_process_heading_line_height' );
	$heading_ls     = (int) tmnhanphat_get_process_mod( 'tmnhanphat_process_heading_letter_spacing' );
	$heading_align  = tmnhanphat_get_process_mod( 'tmnhanphat_process_heading_align' );
	$heading_margin = absint( tmnhanphat_get_process_mod( 'tmnhanphat_process_heading_margin_bottom' ) );

	$desc_color  = tmnhanphat_get_process_mod( 'tmnhanphat_process_desc_color' );
	$desc_size   = absint( tmnhanphat_get_process_mod( 'tmnhanphat_process_desc_size' ) );
	$desc_tablet = absint( tmnhanphat_get_process_mod( 'tmnhanphat_process_desc_size_tablet' ) );
	$desc_mobile = absint( tmnhanphat_get_process_mod( 'tmnhanphat_process_desc_size_mobile' ) );
	$desc_width  = absint( tmnhanphat_get_process_mod( 'tmnhanphat_process_desc_max_width' ) );
	$desc_clamp  = absint( tmnhanphat_get_process_mod( 'tmnhanphat_process_desc_clamp' ) );
	$desc_margin = absint( tmnhanphat_get_process_mod( 'tmnhanphat_process_desc_margin_bottom' ) );

	$timeline_width        = absint( tmnhanphat_get_process_mod( 'tmnhanphat_process_timeline_width' ) );
	$line_color            = tmnhanphat_get_process_mod( 'tmnhanphat_process_line_color' );
	$line_width            = absint( tmnhanphat_get_process_mod( 'tmnhanphat_process_line_width' ) );
	$step_gap              = absint( tmnhanphat_get_process_mod( 'tmnhanphat_process_step_gap' ) );
	$circle_size           = absint( tmnhanphat_get_process_mod( 'tmnhanphat_process_circle_size' ) );
	$circle_border_width   = absint( tmnhanphat_get_process_mod( 'tmnhanphat_process_circle_border_width' ) );
	$circle_border_color   = tmnhanphat_get_process_mod( 'tmnhanphat_process_circle_border_color' );
	$circle_active_bg      = tmnhanphat_get_process_mod( 'tmnhanphat_process_circle_active_bg' );
	$circle_inactive_bg    = tmnhanphat_get_process_mod( 'tmnhanphat_process_circle_inactive_bg' );
	$active_text_color     = tmnhanphat_get_process_mod( 'tmnhanphat_process_active_text_color' );
	$inactive_text_color   = tmnhanphat_get_process_mod( 'tmnhanphat_process_inactive_text_color' );
	$active_desc_opacity   = absint( tmnhanphat_get_process_mod( 'tmnhanphat_process_active_desc_opacity' ) ) / 100;
	$inactive_desc_opacity = absint( tmnhanphat_get_process_mod( 'tmnhanphat_process_inactive_desc_opacity' ) ) / 100;
	$step_padding          = absint( tmnhanphat_get_process_mod( 'tmnhanphat_process_step_padding' ) );
	$step_radius           = absint( tmnhanphat_get_process_mod( 'tmnhanphat_process_step_radius' ) );

	$transition = absint( tmnhanphat_get_process_mod( 'tmnhanphat_process_transition_speed' ) );

	$image_width  = absint( tmnhanphat_get_process_mod( 'tmnhanphat_process_image_width' ) );
	$image_height = absint( tmnhanphat_get_process_mod( 'tmnhanphat_process_image_height' ) );
	$image_fit    = tmnhanphat_get_process_mod( 'tmnhanphat_process_image_fit' );
	$image_align  = tmnhanphat_get_process_mod( 'tmnhanphat_process_image_align' );
	$image_radius = absint( tmnhanphat_get_process_mod( 'tmnhanphat_process_image_radius' ) );

	$align_map   = array(
		'left'   => 'flex-start',
		'center' => 'center',
		'right'  => 'flex-end',
	);
	$image_align = isset( $align_map[ $image_align ] ) ? $align_map[ $image_align ] : 'center';

	$css  = ':root{';
	$css .= '--process-bg:' . $bg_color . ';';
	$css .= '--process-container-width:' . ( $container_width > 0 ? $container_width . 'px' : 'var(--container-max-width)' ) . ';';
	$css .= '--process-padding:' . $padding_desktop . 'px;';
	$css .= '--process-margin:' . $margin_desktop . 'px;';
	$css .= '--process-heading-blue:' . $heading_blue . ';';
	$css .= '--process-heading-red:' . $heading_red . ';';
	$css .= '--process-heading-size:' . $heading_size . 'px;';
	$css .= '--process-heading-weight:' . $heading_weight . ';';
	$css .= '--process-heading-lh:' . $heading_lh . ';';
	$css .= '--process-heading-ls:' . $heading_ls . 'px;';
	$css .= '--process-heading-align:' . $heading_align . ';';
	$css .= '--process-heading-margin:' . $heading_margin . 'px;';
	$css .= '--process-desc-color:' . $desc_color . ';';
	$css .= '--process-desc-size:' . $desc_size . 'px;';
	$css .= '--process-desc-max-width:' . $desc_width . 'px;';
	$css .= '--process-desc-margin:' . $desc_margin . 'px;';
	$css .= '--process-desc-margin-x:' . ( 'center' === $heading_align ? 'auto' : '0' ) . ';';
	$css .= '--process-timeline-width:' . max( 20, min( 80, $timeline_width ) ) . '%;';
	$css .= '--process-line-color:' . $line_color . ';';
	$css .= '--process-line-width:' . $line_width . 'px;';
	$css .= '--process-step-gap:' . $step_gap . 'px;';
	$css .= '--process-circle-size:' . $circle_size . 'px;';
	$css .= '--process-circle-border:' . $circle_border_width . 'px solid ' . $circle_border_color . ';';
	$css .= '--process-circle-active-bg:' . $circle_active_bg . ';';
	// Quầng sáng (ring) quanh circle active = màu active pha 15% opacity.
	$css .= '--process-circle-ring:' . tmnhanphat_hex_to_rgba( $circle_active_bg, 15 ) . ';';
	$css .= '--process-circle-inactive-bg:' . $circle_inactive_bg . ';';
	$css .= '--process-active-text:' . $active_text_color . ';';
	$css .= '--process-inactive-text:' . $inactive_text_color . ';';
	$css .= '--process-active-desc-opacity:' . $active_desc_opacity . ';';
	$css .= '--process-inactive-desc-opacity:' . $inactive_desc_opacity . ';';
	$css .= '--process-step-padding:' . $step_padding . 'px;';
	$css .= '--process-step-radius:' . $step_radius . 'px;';
	$css .= '--process-transition:' . $transition . 'ms;';
	$css .= '--process-image-width:' . ( $image_width > 0 ? $image_width . 'px' : '100%' ) . ';';
	$css .= '--process-image-height:' . ( $image_height > 0 ? $image_height . 'px' : 'auto' ) . ';';
	$css .= '--process-image-fit:' . $image_fit . ';';
	$css .= '--process-image-align:' . $image_align . ';';
	$css .= '--process-image-radius:' . $image_radius . 'px;';
	$css .= '}';

	// Line Clamp = 0 nghĩa là tắt — render rule tĩnh (cùng pattern Products/Why Choose).
	if ( $desc_clamp > 0 ) {
		$css .= '.process-home__description{display:-webkit-box;-webkit-box-orient:vertical;-webkit-line-clamp:' . $desc_clamp . ';overflow:hidden;}';
	}

	$css .= '@media (max-width:991px){:root{';
	$css .= '--process-padding:' . $padding_tablet . 'px;';
	$css .= '--process-margin:' . $margin_tablet . 'px;';
	$css .= '--process-heading-size:' . $heading_tablet . 'px;';
	$css .= '--process-desc-size:' . $desc_tablet . 'px;';
	$css .= '}}';

	$css .= '@media (max-width:599px){:root{';
	$css .= '--process-padding:' . $padding_mobile . 'px;';
	$css .= '--process-margin:' . $margin_mobile . 'px;';
	$css .= '--process-heading-size:' . $heading_mobile . 'px;';
	$css .= '--process-desc-size:' . $desc_mobile . 'px;';
	$css .= '}}';

	return $css;
}

/* ==========================================================================
 * FEATURED PROJECTS SECTION ("Dự án tiêu biểu", trang chủ) — helper riêng.
 *
 * Dữ liệu là POST THƯỜNG thuộc Category "Dự án" chọn trong Customizer (WP_Query,
 * KHÔNG CPT/Taxonomy mới, không hardcode ID category — giống mô hình Products).
 * Meta Loại hình/Tải trọng/Bàn giao đọc từ post meta với meta key CẤU HÌNH ĐƯỢC
 * (mặc định _tmnp_project_type/_load/_year, nhập qua meta box "Thông tin dự án"
 * trong inc/post-types.php — đổi được sang key ACF sau này). Meta rỗng có FALLBACK
 * ở tầng hiển thị, KHÔNG ghi vào database.
 *
 * Slider 1 slide/khung nhìn + dots, tái sử dụng .tmnp-slider + slider.js của
 * Services (data-cards 1/1/1, data-dots true).
 * ========================================================================== */

/**
 * Số lượng Meta hiển thị trên mỗi Project Card (Loại hình/Tải trọng/Bàn giao —
 * cố định 3 theo design).
 *
 * @return int
 */
function tmnhanphat_get_projects_meta_count() {
	return 3;
}

/**
 * Default value tập trung cho toàn bộ setting của Projects Section (mục 22).
 *
 * @return array<string, mixed>
 */
function tmnhanphat_projects_defaults() {
	$defaults = array(
		// ----- General -----
		'tmnhanphat_projects_enable'          => true,
		'tmnhanphat_projects_section_id'      => 'du-an',
		'tmnhanphat_projects_container_width' => 0,
		// Figma: nền xanh (7060) → Heading (7221) = 161 (đáy 91 cố định ở CSS).
		'tmnhanphat_projects_padding_desktop' => 112, // Migrate: nhịp dọc mới (161→112).
		'tmnhanphat_projects_padding_tablet'  => 64,
		'tmnhanphat_projects_padding_mobile'  => 48,
		'tmnhanphat_projects_margin_desktop'  => 0,
		'tmnhanphat_projects_margin_tablet'   => 0,
		'tmnhanphat_projects_margin_mobile'   => 0,
		'tmnhanphat_projects_bg_color'        => '#046AB5',
		'tmnhanphat_projects_deco_enable'     => true,
		'tmnhanphat_projects_deco_image'      => '',
		'tmnhanphat_projects_deco_opacity'    => 100,
		'tmnhanphat_projects_deco_position'   => 'center',
		'tmnhanphat_projects_deco_size'       => 'cover',

		// ----- Header (1 màu trắng trên nền xanh — khác các section 2 màu) -----
		// Figma: Heading 96/900 trắng; Description 20/400 TRẮNG ĐẶC box 1006;
		// Description → Card cách 72px.
		'tmnhanphat_projects_title'                => 'Dự án tiêu biểu',
		'tmnhanphat_projects_title_color'          => '#ffffff',
		'tmnhanphat_projects_title_size'           => 96,
		'tmnhanphat_projects_title_size_tablet'    => 48,
		'tmnhanphat_projects_title_size_mobile'    => 30,
		'tmnhanphat_projects_title_weight'         => '900',
		'tmnhanphat_projects_title_align'          => 'center',
		'tmnhanphat_projects_desc_text'            => 'Đây là những công trình Thang Máy Nhân Phát đã tư vấn, lắp đặt và bàn giao. Mỗi dự án thể hiện sự chuyên nghiệp, an toàn và tính thẩm mỹ trong từng giải pháp thang máy.',
		'tmnhanphat_projects_desc_color'           => '#ffffff',
		'tmnhanphat_projects_desc_size'            => 20,
		'tmnhanphat_projects_desc_size_tablet'     => 16,
		'tmnhanphat_projects_desc_size_mobile'     => 14,
		'tmnhanphat_projects_desc_max_width'       => 820, // Figma 1006 → hạ 820 cho measure dễ đọc (cùng lý do Services).
		'tmnhanphat_projects_desc_clamp'           => 0,
		'tmnhanphat_projects_desc_align'           => 'center',
		'tmnhanphat_projects_header_margin_bottom' => 72,

		// ----- Query -----
		'tmnhanphat_projects_category'    => 0, // Admin BẮT BUỘC chọn — 0 = chưa cấu hình, không render.
		'tmnhanphat_projects_count'       => 6,
		'tmnhanphat_projects_orderby'     => 'date',
		'tmnhanphat_projects_order'       => 'DESC',
		'tmnhanphat_projects_exclude_ids' => '',

		// ----- Content Layout — Figma: card 1590×750 r=40; padding 4 phía 67/97/55/90
		// đặt ở CSS. Figma vẽ nền ĐẶC #A1ACC1 nhưng chữ trên card toàn TRẮNG → tương
		// phản ~2.2:1 (fail WCAG AA, lỗi design). Taste review (mục 1, owner duyệt):
		// hạ opacity 100→25 thành kính mờ trên nền xanh section — nền hiệu dụng sau
		// chữ ~#2B7AB9, trắng đạt ~4.5:1, đồng bộ vật liệu glass với Hero stats card. -----
		'tmnhanphat_projects_left_width'   => 65,
		'tmnhanphat_projects_content_gap'  => 33,
		'tmnhanphat_projects_valign'       => 'center',
		'tmnhanphat_projects_card_bg'      => '#A1ACC1',
		'tmnhanphat_projects_card_opacity' => 25,
		'tmnhanphat_projects_card_radius'  => 28, // Migrate: radius mềm (40→28).
		'tmnhanphat_projects_card_padding' => 30, // Migrate: padding card mềm (40→30).

		// ----- Project Title — Figma: 55/700 trắng, cách Description 27px. -----
		'tmnhanphat_projects_ptitle_size'          => 55,
		'tmnhanphat_projects_ptitle_size_tablet'   => 32,
		'tmnhanphat_projects_ptitle_size_mobile'   => 24,
		'tmnhanphat_projects_ptitle_weight'        => '700',
		'tmnhanphat_projects_ptitle_color'         => '#ffffff',
		'tmnhanphat_projects_ptitle_clamp'         => 2,
		'tmnhanphat_projects_ptitle_margin_bottom' => 27,

		// ----- Project Description (excerpt) — Figma: 20/400 trắng, cách Meta 37px. -----
		'tmnhanphat_projects_pdesc_size'          => 20,
		'tmnhanphat_projects_pdesc_size_tablet'   => 15,
		'tmnhanphat_projects_pdesc_size_mobile'   => 14,
		'tmnhanphat_projects_pdesc_color'         => '#ffffff',
		'tmnhanphat_projects_pdesc_clamp'         => 3,
		'tmnhanphat_projects_pdesc_margin_bottom' => 37,

		// ----- Meta — Figma: divider TRẮNG 1px rộng 376, label 20 #E7F0FF, value 32/700. -----
		'tmnhanphat_projects_meta_enable'          => true,
		'tmnhanphat_projects_divider_enable'       => true,
		'tmnhanphat_projects_divider_color'        => '#ffffff',
		'tmnhanphat_projects_divider_width'        => 1,
		'tmnhanphat_projects_meta_label_color'     => '#E7F0FF',
		'tmnhanphat_projects_meta_value_color'     => '#ffffff',
		'tmnhanphat_projects_meta_label_size'      => 20,
		'tmnhanphat_projects_meta_value_size'      => 32,
		'tmnhanphat_projects_meta_gap'             => 18,
		'tmnhanphat_projects_meta_fallback_enable' => true,

		// ----- Thumbnail — Figma: 492×628, radius 30. -----
		'tmnhanphat_projects_thumb_width'  => 0, // 0 = auto theo cột phải.
		'tmnhanphat_projects_thumb_height' => 628,
		'tmnhanphat_projects_thumb_radius' => 30,
		'tmnhanphat_projects_thumb_fit'    => 'cover',
		'tmnhanphat_projects_thumb_shadow' => 'soft',
		'tmnhanphat_projects_thumb_zoom'   => true,

		// ----- Slider -----
		'tmnhanphat_projects_autoplay_enable'  => true,
		'tmnhanphat_projects_autoplay_speed'   => 5000,
		'tmnhanphat_projects_transition_speed' => 600,
		'tmnhanphat_projects_infinite'         => true,
		'tmnhanphat_projects_pause_hover'      => true,
		'tmnhanphat_projects_drag_enable'      => true,
		// Figma: dots 19px, gap 27, inactive #A2A2A2, active trắng.
		'tmnhanphat_projects_show_dots'        => true,
		'tmnhanphat_projects_dot_size'         => 19,
		'tmnhanphat_projects_dot_gap'          => 27,
		'tmnhanphat_projects_dot_color'        => '#A8AEB8', // Gom token: Figma #A2A2A2 → xám nhạt chung.
		'tmnhanphat_projects_dot_active_color' => '#ffffff',
	);

	// 3 nhóm Meta theo design: Label + Meta Key (đổi được sang ACF) + Fallback hiển thị.
	$meta_samples = array(
		1 => array(
			'label'    => 'Loại hình',
			'key'      => '_tmnp_project_type',
			'fallback' => 'Thang máy gia đình',
		),
		2 => array(
			'label'    => 'Tải trọng',
			'key'      => '_tmnp_project_load',
			'fallback' => '350kg',
		),
		3 => array(
			'label'    => 'Bàn giao',
			'key'      => '_tmnp_project_year',
			'fallback' => gmdate( 'Y' ),
		),
	);

	foreach ( $meta_samples as $i => $meta ) {
		$defaults[ "tmnhanphat_projects_meta{$i}_label" ]    = $meta['label'];
		$defaults[ "tmnhanphat_projects_meta{$i}_key" ]      = $meta['key'];
		$defaults[ "tmnhanphat_projects_meta{$i}_fallback" ] = $meta['fallback'];
	}

	return $defaults;
}

/**
 * Đọc 1 theme_mod của Projects với default tập trung (mục 22).
 *
 * @param string $key Setting id.
 * @return mixed
 */
function tmnhanphat_get_projects_mod( $key ) {
	$defaults = tmnhanphat_projects_defaults();
	$default  = isset( $defaults[ $key ] ) ? $defaults[ $key ] : '';

	return get_theme_mod( $key, $default );
}

/**
 * Category "Dự án" đã chọn trong Customizer — 0 nếu chưa cấu hình/không tồn tại.
 *
 * @return int
 */
function tmnhanphat_get_projects_category_id() {
	$cat_id = absint( tmnhanphat_get_projects_mod( 'tmnhanphat_projects_category' ) );

	if ( ! $cat_id || ! term_exists( $cat_id, 'category' ) ) {
		return 0;
	}

	return $cat_id;
}

/**
 * Query danh sách Dự án theo setting Customizer — 'cat' tự bao gồm category con,
 * chỉ lấy đúng số lượng cần render (no_found_rows — mục 15).
 *
 * @return WP_Query
 */
function tmnhanphat_get_projects_query() {
	return new WP_Query( array(
		'post_type'           => 'post',
		'post_status'         => 'publish',
		'cat'                 => tmnhanphat_get_projects_category_id(),
		'posts_per_page'      => max( 1, absint( tmnhanphat_get_projects_mod( 'tmnhanphat_projects_count' ) ) ),
		'orderby'             => tmnhanphat_get_projects_mod( 'tmnhanphat_projects_orderby' ),
		'order'               => tmnhanphat_get_projects_mod( 'tmnhanphat_projects_order' ),
		'post__not_in'        => tmnhanphat_parse_id_list( tmnhanphat_get_projects_mod( 'tmnhanphat_projects_exclude_ids' ) ),
		'no_found_rows'       => true,
		'ignore_sticky_posts' => true,
	) );
}

/**
 * Whitelist "Order By" của Projects (dùng chung danh sách với Products — post thường).
 *
 * @param string $value Giá trị gửi lên từ Customizer.
 * @return string
 */
function tmnhanphat_sanitize_projects_orderby( $value ) {
	$choices = array_keys( tmnhanphat_get_products_orderby_choices() );

	return in_array( $value, $choices, true ) ? $value : 'date';
}

/**
 * Whitelist "Order".
 *
 * @param string $value Giá trị gửi lên từ Customizer.
 * @return string
 */
function tmnhanphat_sanitize_projects_order( $value ) {
	return in_array( $value, array( 'ASC', 'DESC' ), true ) ? $value : 'DESC';
}

/**
 * Whitelist "Vertical Alignment" (căn dọc 2 cột trong card).
 *
 * @param string $value Giá trị gửi lên từ Customizer.
 * @return string
 */
function tmnhanphat_sanitize_projects_valign( $value ) {
	$choices = array( 'flex-start', 'center', 'flex-end' );

	return in_array( $value, $choices, true ) ? $value : 'center';
}

/**
 * Xây 3 mục Meta (Loại hình/Tải trọng/Bàn giao) của 1 Dự án: đọc post meta theo key
 * cấu hình; meta RỖNG → dùng Fallback CHỈ Ở TẦNG HIỂN THỊ (không update_post_meta,
 * không ghi database). Fallback tắt → mục meta rỗng bị ẩn (Hide Empty Meta).
 *
 * @param int $post_id ID bài dự án.
 * @return array<int, array{label: string, value: string}>
 */
function tmnhanphat_get_project_meta_items( $post_id ) {
	$items           = array();
	$fallback_enable = tmnhanphat_get_projects_mod( 'tmnhanphat_projects_meta_fallback_enable' );

	for ( $i = 1; $i <= tmnhanphat_get_projects_meta_count(); $i++ ) {
		$label    = tmnhanphat_get_projects_mod( "tmnhanphat_projects_meta{$i}_label" );
		$meta_key = sanitize_text_field( tmnhanphat_get_projects_mod( "tmnhanphat_projects_meta{$i}_key" ) );

		if ( ! $label ) {
			continue;
		}

		$value = '';

		if ( $meta_key ) {
			$raw   = get_post_meta( $post_id, $meta_key, true );
			$value = is_string( $raw ) ? trim( wp_strip_all_tags( $raw ) ) : '';
		}

		if ( '' === $value && $fallback_enable ) {
			$value = tmnhanphat_get_projects_mod( "tmnhanphat_projects_meta{$i}_fallback" );
		}

		if ( '' === $value ) {
			continue; // Không hiển thị Empty/Null/N/A.
		}

		$items[] = array(
			'label' => $label,
			'value' => $value,
		);
	}

	return $items;
}

/**
 * Sinh chuỗi CSS custom properties cho Projects Section (mục 22).
 *
 * @return string
 */
function tmnhanphat_render_projects_css_vars() {
	$bg_color        = tmnhanphat_get_projects_mod( 'tmnhanphat_projects_bg_color' );
	$container_width = absint( tmnhanphat_get_projects_mod( 'tmnhanphat_projects_container_width' ) );

	$padding_desktop = absint( tmnhanphat_get_projects_mod( 'tmnhanphat_projects_padding_desktop' ) );
	$padding_tablet  = absint( tmnhanphat_get_projects_mod( 'tmnhanphat_projects_padding_tablet' ) );
	$padding_mobile  = absint( tmnhanphat_get_projects_mod( 'tmnhanphat_projects_padding_mobile' ) );
	$margin_desktop  = absint( tmnhanphat_get_projects_mod( 'tmnhanphat_projects_margin_desktop' ) );
	$margin_tablet   = absint( tmnhanphat_get_projects_mod( 'tmnhanphat_projects_margin_tablet' ) );
	$margin_mobile   = absint( tmnhanphat_get_projects_mod( 'tmnhanphat_projects_margin_mobile' ) );

	$deco_image   = tmnhanphat_get_projects_mod( 'tmnhanphat_projects_deco_image' );
	$deco_opacity = absint( tmnhanphat_get_projects_mod( 'tmnhanphat_projects_deco_opacity' ) ) / 100;
	$deco_pos     = tmnhanphat_get_projects_mod( 'tmnhanphat_projects_deco_position' );
	$deco_size    = tmnhanphat_get_projects_mod( 'tmnhanphat_projects_deco_size' );

	$title_color  = tmnhanphat_get_projects_mod( 'tmnhanphat_projects_title_color' );
	$title_size   = absint( tmnhanphat_get_projects_mod( 'tmnhanphat_projects_title_size' ) );
	$title_tablet = absint( tmnhanphat_get_projects_mod( 'tmnhanphat_projects_title_size_tablet' ) );
	$title_mobile = absint( tmnhanphat_get_projects_mod( 'tmnhanphat_projects_title_size_mobile' ) );
	$title_weight = absint( tmnhanphat_get_projects_mod( 'tmnhanphat_projects_title_weight' ) );
	$title_align  = tmnhanphat_get_projects_mod( 'tmnhanphat_projects_title_align' );

	$desc_color   = tmnhanphat_get_projects_mod( 'tmnhanphat_projects_desc_color' );
	$desc_size    = absint( tmnhanphat_get_projects_mod( 'tmnhanphat_projects_desc_size' ) );
	$desc_tablet  = absint( tmnhanphat_get_projects_mod( 'tmnhanphat_projects_desc_size_tablet' ) );
	$desc_mobile  = absint( tmnhanphat_get_projects_mod( 'tmnhanphat_projects_desc_size_mobile' ) );
	$desc_width   = absint( tmnhanphat_get_projects_mod( 'tmnhanphat_projects_desc_max_width' ) );
	$desc_clamp   = absint( tmnhanphat_get_projects_mod( 'tmnhanphat_projects_desc_clamp' ) );
	$desc_align   = tmnhanphat_get_projects_mod( 'tmnhanphat_projects_desc_align' );
	$header_margin = absint( tmnhanphat_get_projects_mod( 'tmnhanphat_projects_header_margin_bottom' ) );

	$left_width   = max( 40, min( 80, absint( tmnhanphat_get_projects_mod( 'tmnhanphat_projects_left_width' ) ) ) );
	$content_gap  = absint( tmnhanphat_get_projects_mod( 'tmnhanphat_projects_content_gap' ) );
	$valign       = tmnhanphat_get_projects_mod( 'tmnhanphat_projects_valign' );
	$card_bg      = tmnhanphat_get_projects_mod( 'tmnhanphat_projects_card_bg' );
	$card_opacity = absint( tmnhanphat_get_projects_mod( 'tmnhanphat_projects_card_opacity' ) );
	$card_radius  = absint( tmnhanphat_get_projects_mod( 'tmnhanphat_projects_card_radius' ) );
	$card_padding = absint( tmnhanphat_get_projects_mod( 'tmnhanphat_projects_card_padding' ) );

	$ptitle_size   = absint( tmnhanphat_get_projects_mod( 'tmnhanphat_projects_ptitle_size' ) );
	$ptitle_tablet = absint( tmnhanphat_get_projects_mod( 'tmnhanphat_projects_ptitle_size_tablet' ) );
	$ptitle_mobile = absint( tmnhanphat_get_projects_mod( 'tmnhanphat_projects_ptitle_size_mobile' ) );
	$ptitle_weight = absint( tmnhanphat_get_projects_mod( 'tmnhanphat_projects_ptitle_weight' ) );
	$ptitle_color  = tmnhanphat_get_projects_mod( 'tmnhanphat_projects_ptitle_color' );
	$ptitle_clamp  = absint( tmnhanphat_get_projects_mod( 'tmnhanphat_projects_ptitle_clamp' ) );
	$ptitle_margin = absint( tmnhanphat_get_projects_mod( 'tmnhanphat_projects_ptitle_margin_bottom' ) );

	$pdesc_size   = absint( tmnhanphat_get_projects_mod( 'tmnhanphat_projects_pdesc_size' ) );
	$pdesc_tablet = absint( tmnhanphat_get_projects_mod( 'tmnhanphat_projects_pdesc_size_tablet' ) );
	$pdesc_mobile = absint( tmnhanphat_get_projects_mod( 'tmnhanphat_projects_pdesc_size_mobile' ) );
	$pdesc_color  = tmnhanphat_get_projects_mod( 'tmnhanphat_projects_pdesc_color' );
	$pdesc_clamp  = absint( tmnhanphat_get_projects_mod( 'tmnhanphat_projects_pdesc_clamp' ) );
	$pdesc_margin = absint( tmnhanphat_get_projects_mod( 'tmnhanphat_projects_pdesc_margin_bottom' ) );

	$divider_enable = tmnhanphat_get_projects_mod( 'tmnhanphat_projects_divider_enable' );
	$divider_color  = tmnhanphat_get_projects_mod( 'tmnhanphat_projects_divider_color' );
	$divider_width  = absint( tmnhanphat_get_projects_mod( 'tmnhanphat_projects_divider_width' ) );
	$label_color    = tmnhanphat_get_projects_mod( 'tmnhanphat_projects_meta_label_color' );
	$value_color    = tmnhanphat_get_projects_mod( 'tmnhanphat_projects_meta_value_color' );
	$label_size     = absint( tmnhanphat_get_projects_mod( 'tmnhanphat_projects_meta_label_size' ) );
	$value_size     = absint( tmnhanphat_get_projects_mod( 'tmnhanphat_projects_meta_value_size' ) );
	$meta_gap       = absint( tmnhanphat_get_projects_mod( 'tmnhanphat_projects_meta_gap' ) );

	$thumb_width  = absint( tmnhanphat_get_projects_mod( 'tmnhanphat_projects_thumb_width' ) );
	$thumb_height = absint( tmnhanphat_get_projects_mod( 'tmnhanphat_projects_thumb_height' ) );
	$thumb_radius = absint( tmnhanphat_get_projects_mod( 'tmnhanphat_projects_thumb_radius' ) );
	$thumb_fit    = tmnhanphat_get_projects_mod( 'tmnhanphat_projects_thumb_fit' );
	$thumb_shadow = tmnhanphat_get_why_choose_shadow_value( tmnhanphat_get_projects_mod( 'tmnhanphat_projects_thumb_shadow' ) );
	$thumb_zoom   = tmnhanphat_get_projects_mod( 'tmnhanphat_projects_thumb_zoom' );

	$transition = absint( tmnhanphat_get_projects_mod( 'tmnhanphat_projects_transition_speed' ) );
	$dot_size   = absint( tmnhanphat_get_projects_mod( 'tmnhanphat_projects_dot_size' ) );
	$dot_gap    = absint( tmnhanphat_get_projects_mod( 'tmnhanphat_projects_dot_gap' ) );
	$dot_color  = tmnhanphat_get_projects_mod( 'tmnhanphat_projects_dot_color' );
	$dot_active = tmnhanphat_get_projects_mod( 'tmnhanphat_projects_dot_active_color' );

	$css  = ':root{';
	$css .= '--projects-bg:' . $bg_color . ';';
	$css .= '--projects-container-width:' . ( $container_width > 0 ? $container_width . 'px' : 'var(--container-max-width)' ) . ';';
	$css .= '--projects-padding:' . $padding_desktop . 'px;';
	$css .= '--projects-margin:' . $margin_desktop . 'px;';
	$css .= '--projects-deco-image:' . ( $deco_image ? 'url("' . esc_url( $deco_image ) . '")' : 'none' ) . ';';
	$css .= '--projects-deco-opacity:' . $deco_opacity . ';';
	$css .= '--projects-deco-pos:' . $deco_pos . ';';
	$css .= '--projects-deco-size:' . $deco_size . ';';
	$css .= '--projects-title-color:' . $title_color . ';';
	$css .= '--projects-title-size:' . $title_size . 'px;';
	$css .= '--projects-title-weight:' . $title_weight . ';';
	$css .= '--projects-title-align:' . $title_align . ';';
	$css .= '--projects-desc-color:' . $desc_color . ';';
	$css .= '--projects-desc-size:' . $desc_size . 'px;';
	$css .= '--projects-desc-max-width:' . $desc_width . 'px;';
	$css .= '--projects-desc-align:' . $desc_align . ';';
	$css .= '--projects-desc-margin-x:' . ( 'center' === $desc_align ? 'auto' : '0' ) . ';';
	$css .= '--projects-header-margin:' . $header_margin . 'px;';
	$css .= '--projects-left-width:' . $left_width . '%;';
	$css .= '--projects-content-gap:' . $content_gap . 'px;';
	$css .= '--projects-valign:' . $valign . ';';
	$css .= '--projects-card-bg:' . tmnhanphat_hex_to_rgba( $card_bg, $card_opacity ) . ';';
	$css .= '--projects-card-radius:' . $card_radius . 'px;';
	$css .= '--projects-card-padding:' . $card_padding . 'px;';
	$css .= '--projects-ptitle-size:' . $ptitle_size . 'px;';
	$css .= '--projects-ptitle-weight:' . $ptitle_weight . ';';
	$css .= '--projects-ptitle-color:' . $ptitle_color . ';';
	$css .= '--projects-ptitle-margin:' . $ptitle_margin . 'px;';
	$css .= '--projects-pdesc-size:' . $pdesc_size . 'px;';
	$css .= '--projects-pdesc-color:' . $pdesc_color . ';';
	$css .= '--projects-pdesc-margin:' . $pdesc_margin . 'px;';
	$css .= '--projects-divider:' . ( $divider_enable ? $divider_width . 'px solid ' . $divider_color : 'none' ) . ';';
	$css .= '--projects-meta-label-color:' . $label_color . ';';
	$css .= '--projects-meta-value-color:' . $value_color . ';';
	$css .= '--projects-meta-label-size:' . $label_size . 'px;';
	$css .= '--projects-meta-value-size:' . $value_size . 'px;';
	$css .= '--projects-meta-gap:' . $meta_gap . 'px;';
	$css .= '--projects-thumb-width:' . ( $thumb_width > 0 ? $thumb_width . 'px' : '100%' ) . ';';
	$css .= '--projects-thumb-height:' . ( $thumb_height > 0 ? $thumb_height . 'px' : 'auto' ) . ';';
	$css .= '--projects-thumb-radius:' . $thumb_radius . 'px;';
	$css .= '--projects-thumb-fit:' . $thumb_fit . ';';
	$css .= '--projects-thumb-shadow:' . $thumb_shadow . ';';
	$css .= '--projects-thumb-zoom:' . ( $thumb_zoom ? '1.05' : '1' ) . ';';
	$css .= '--projects-transition:' . $transition . 'ms;';
	$css .= '--projects-dot-size:' . $dot_size . 'px;';
	$css .= '--projects-dot-gap:' . $dot_gap . 'px;';
	$css .= '--projects-dot-color:' . $dot_color . ';';
	$css .= '--projects-dot-active:' . $dot_active . ';';
	$css .= '}';

	// Line Clamp = 0 nghĩa là tắt — rule tĩnh (cùng pattern các module trước).
	if ( $desc_clamp > 0 ) {
		$css .= '.projects-home__description{display:-webkit-box;-webkit-box-orient:vertical;-webkit-line-clamp:' . $desc_clamp . ';overflow:hidden;}';
	}

	if ( $ptitle_clamp > 0 ) {
		$css .= '.project-card__title{display:-webkit-box;-webkit-box-orient:vertical;-webkit-line-clamp:' . $ptitle_clamp . ';overflow:hidden;}';
	}

	if ( $pdesc_clamp > 0 ) {
		$css .= '.project-card__description{display:-webkit-box;-webkit-box-orient:vertical;-webkit-line-clamp:' . $pdesc_clamp . ';overflow:hidden;}';
	}

	$css .= '@media (max-width:991px){:root{';
	$css .= '--projects-padding:' . $padding_tablet . 'px;';
	$css .= '--projects-margin:' . $margin_tablet . 'px;';
	$css .= '--projects-title-size:' . $title_tablet . 'px;';
	$css .= '--projects-desc-size:' . $desc_tablet . 'px;';
	$css .= '--projects-ptitle-size:' . $ptitle_tablet . 'px;';
	$css .= '--projects-pdesc-size:' . $pdesc_tablet . 'px;';
	// Tablet: 60/40 giữ ảnh rõ hơn (spec Responsive).
	$css .= '--projects-left-width:60%;';
	$css .= '}}';

	$css .= '@media (max-width:599px){:root{';
	$css .= '--projects-padding:' . $padding_mobile . 'px;';
	$css .= '--projects-margin:' . $margin_mobile . 'px;';
	$css .= '--projects-title-size:' . $title_mobile . 'px;';
	$css .= '--projects-desc-size:' . $desc_mobile . 'px;';
	$css .= '--projects-ptitle-size:' . $ptitle_mobile . 'px;';
	$css .= '--projects-pdesc-size:' . $pdesc_mobile . 'px;';
	$css .= '}}';

	return $css;
}

/* ==========================================================================
 * CUSTOMER REVIEW SECTION ("Đánh giá thực tế của khách hàng", trang chủ) — helper
 * riêng. Dữ liệu 100% từ CPT customer_review (inc/post-types.php) qua WP_Query —
 * KHÔNG wp_posts thường, KHÔNG category Blog, KHÔNG Page. Customizer panel
 * "Customer Review Home" chỉ điều khiển CÁCH hiển thị (query/layout/animation),
 * không chứa nội dung review.
 *
 * Marquee dọc vô hạn (KHÔNG slider/Swiper): 3 cột cố định (Column 1/3 chạy Bottom→
 * Top, Column 2 chạy Top→Bottom theo default — mỗi cột đổi hướng riêng được), mỗi
 * cột render danh sách CLONE GẤP ĐÔI ở tầng hiển thị rồi CSS animation translateY
 * 0 → -50% loop vô hạn — 2 nửa giống nhau tuyệt đối nên điểm nối vô hình, không cần
 * đo chiều cao bằng JS. Responsive chỉ ẩn/hiện cột 2-3 bằng CSS, không truy vấn lại.
 * ========================================================================== */

/**
 * Số cột cố định của Marquee (kiến trúc luôn dựng đủ 3 cột trong DOM; setting
 * Columns Desktop/Tablet/Mobile chỉ quyết định ẨN/HIỆN bao nhiêu cột ở từng
 * breakpoint — xem tmnhanphat_render_customer_review_css_vars()).
 *
 * @return int
 */
function tmnhanphat_get_customer_review_column_count() {
	return 3;
}

/**
 * Default value tập trung cho toàn bộ setting của Customer Review Section (mục 22).
 *
 * @return array<string, mixed>
 */
function tmnhanphat_customer_review_defaults() {
	return array(
		// ----- General -----
		'tmnhanphat_review_enable'          => true,
		'tmnhanphat_review_section_id'      => 'danh-gia',
		'tmnhanphat_review_container_width' => 0,
		'tmnhanphat_review_bg_color'        => '#ffffff',
		'tmnhanphat_review_bg_image'        => '',
		// Figma: đáy Projects (8387) → Heading (8558) = 171 (đáy 88 cố định ở CSS).
		'tmnhanphat_review_padding_desktop' => 112, // Migrate: nhịp dọc mới (171→112).
		'tmnhanphat_review_padding_tablet'  => 64,
		'tmnhanphat_review_padding_mobile'  => 48,
		'tmnhanphat_review_margin_desktop'  => 0,
		'tmnhanphat_review_margin_tablet'   => 0,
		'tmnhanphat_review_margin_mobile'   => 0,

		// ----- Header (Figma 2 màu 1 dòng: phần đầu đỏ + 2 từ cuối "khách hàng" xanh —
		// template tự tách 2 từ cuối thành span accent xanh, Title Color chỉnh phần đỏ) -----
		// Figma: Heading 85/900; Description 20/400 #464646 box 1068; Desc → Marquee 59.
		'tmnhanphat_review_title'                => 'Đánh giá thực tế của khách hàng',
		'tmnhanphat_review_title_color'          => '#E31F2B',
		'tmnhanphat_review_title_size'           => 85,
		'tmnhanphat_review_title_size_tablet'    => 44,
		'tmnhanphat_review_title_size_mobile'    => 28,
		'tmnhanphat_review_title_align'          => 'center',
		'tmnhanphat_review_desc_text'            => 'Những chia sẻ chân thực từ khách hàng đã và đang sử dụng dịch vụ tại Thang Máy Nhân Phát, là minh chứng rõ nét nhất cho chất lượng sản phẩm và sự tận tâm trong từng công trình mà chúng tôi mang lại.',
		'tmnhanphat_review_desc_color'           => '#464646',
		'tmnhanphat_review_desc_size'            => 20,
		'tmnhanphat_review_desc_size_tablet'     => 16,
		'tmnhanphat_review_desc_size_mobile'     => 14,
		'tmnhanphat_review_desc_max_width'       => 820, // Figma 1068 → hạ 820 cho measure dễ đọc (cùng lý do Services).
		'tmnhanphat_review_header_margin_bottom' => 59,

		// ----- Query -----
		'tmnhanphat_review_posts_per_column' => 4,
		'tmnhanphat_review_order'            => 'ASC',
		'tmnhanphat_review_orderby'          => 'sort_order',
		'tmnhanphat_review_hide_draft'       => true,
		'tmnhanphat_review_hide_empty'       => true,

		// ----- Layout -----
		'tmnhanphat_review_cols_desktop'      => 3,
		'tmnhanphat_review_cols_tablet'       => 2,
		'tmnhanphat_review_cols_mobile'       => 1,
		'tmnhanphat_review_card_gap'          => 24, // Figma: gap dọc = gap ngang giữa các Column.
		'tmnhanphat_review_column_gap'        => 24,
		// Figma: card r12, viền 1px #212121, KHÔNG shadow, padding trong 25.
		'tmnhanphat_review_card_radius'       => 12,
		'tmnhanphat_review_card_border_width' => 1,
		'tmnhanphat_review_card_border_color' => '#212121',
		'tmnhanphat_review_card_bg'           => '#ffffff',
		'tmnhanphat_review_card_shadow'       => 'none',
		'tmnhanphat_review_card_hover_shadow' => 'medium',
		'tmnhanphat_review_card_padding'      => 20, // Migrate: padding card mềm (25→20).

		// ----- Avatar -----
		'tmnhanphat_review_avatar_size'          => 44,
		'tmnhanphat_review_avatar_size_tablet'   => 40,
		'tmnhanphat_review_avatar_size_mobile'   => 36,
		'tmnhanphat_review_avatar_radius'        => 100,
		'tmnhanphat_review_avatar_fit'           => 'cover',

		// ----- Name / Role / Comment — Figma: Name 15/400 (fill gốc #F5F5F5 là LỖI design
		// — trắng nhạt vô hình trên card trắng — nên giữ màu tối đọc được), Role 13 (Figma
		// #AEAEAE chỉ đạt ~2.3:1 trên nền trắng — fail WCAG AA, nâng lên #667180 ~4.9:1),
		// Comment 15/400 ĐEN lh 160%. -----
		'tmnhanphat_review_name_size'     => 15,
		'tmnhanphat_review_name_weight'   => '400',
		'tmnhanphat_review_name_color'    => '#1a1a1a',
		'tmnhanphat_review_role_size'     => 13,
		'tmnhanphat_review_role_color'    => '#667180',
		'tmnhanphat_review_comment_size'         => 15,
		'tmnhanphat_review_comment_color'        => '#000000',
		'tmnhanphat_review_comment_clamp'        => 4,
		'tmnhanphat_review_comment_clamp_mobile' => 3,

		// ----- Animation -----
		'tmnhanphat_review_animation_enable' => true,
		'tmnhanphat_review_speed'            => 40, // giây / 1 vòng lặp (tốc độ cuộn — số nhỏ = nhanh hơn).
		'tmnhanphat_review_speed_mobile'     => 60, // Mobile chậm hơn Desktop theo spec Responsive.
		'tmnhanphat_review_pause_hover'      => true,
		'tmnhanphat_review_loop'             => true,
		'tmnhanphat_review_col1_direction'   => 'up',
		'tmnhanphat_review_col2_direction'   => 'down',
		'tmnhanphat_review_col3_direction'   => 'up',
		'tmnhanphat_review_mask_enable'      => true,
		'tmnhanphat_review_mask_fade_top'    => true,
		'tmnhanphat_review_mask_fade_bottom' => true,
		'tmnhanphat_review_mask_height'      => 80,
	);
}

/**
 * Đọc 1 theme_mod của Customer Review với default tập trung (mục 22).
 *
 * @param string $key Setting id.
 * @return mixed
 */
function tmnhanphat_get_customer_review_mod( $key ) {
	$defaults = tmnhanphat_customer_review_defaults();
	$default  = isset( $defaults[ $key ] ) ? $defaults[ $key ] : '';

	return get_theme_mod( $key, $default );
}

/**
 * Danh sách lựa chọn "Order By".
 *
 * @return array<string, string>
 */
function tmnhanphat_get_customer_review_orderby_choices() {
	return array(
		'sort_order' => __( 'Sort Order (Meta Box)', 'tmnhanphat' ),
		'date'       => __( 'Ngày đăng', 'tmnhanphat' ),
		'title'      => __( 'Tên khách hàng', 'tmnhanphat' ),
		'rand'       => __( 'Ngẫu nhiên', 'tmnhanphat' ),
	);
}

/**
 * Whitelist "Order By".
 *
 * @param string $value Giá trị gửi lên từ Customizer.
 * @return string
 */
function tmnhanphat_sanitize_customer_review_orderby( $value ) {
	$choices = array_keys( tmnhanphat_get_customer_review_orderby_choices() );

	return in_array( $value, $choices, true ) ? $value : 'sort_order';
}

/**
 * Whitelist "Order".
 *
 * @param string $value Giá trị gửi lên từ Customizer.
 * @return string
 */
function tmnhanphat_sanitize_customer_review_order( $value ) {
	return in_array( $value, array( 'ASC', 'DESC' ), true ) ? $value : 'ASC';
}

/**
 * Danh sách lựa chọn hướng chạy Marquee cho từng cột.
 *
 * @return array<string, string>
 */
function tmnhanphat_get_customer_review_direction_choices() {
	return array(
		'up'   => __( 'Up (Bottom → Top)', 'tmnhanphat' ),
		'down' => __( 'Down (Top → Bottom)', 'tmnhanphat' ),
	);
}

/**
 * Whitelist hướng chạy Marquee.
 *
 * @param string $value Giá trị gửi lên từ Customizer.
 * @return string
 */
function tmnhanphat_sanitize_customer_review_direction( $value ) {
	$choices = array_keys( tmnhanphat_get_customer_review_direction_choices() );

	return in_array( $value, $choices, true ) ? $value : 'up';
}

/**
 * Query danh sách Review theo setting Customizer — lấy đủ 3 cột × Posts Per Column
 * (cố định 3 cột vật lý trong DOM, bất kể Columns hiển thị bao nhiêu ở breakpoint
 * hiện tại — xem tmnhanphat_get_customer_review_column_count()), chỉ lấy đúng số
 * lượng cần render (no_found_rows — mục 15).
 *
 * @return WP_Query
 */
function tmnhanphat_get_customer_review_query() {
	$per_column = max( 1, absint( tmnhanphat_get_customer_review_mod( 'tmnhanphat_review_posts_per_column' ) ) );
	$orderby    = tmnhanphat_get_customer_review_mod( 'tmnhanphat_review_orderby' );
	$order      = tmnhanphat_get_customer_review_mod( 'tmnhanphat_review_order' );
	$hide_draft = tmnhanphat_get_customer_review_mod( 'tmnhanphat_review_hide_draft' );

	$args = array(
		'post_type'           => 'customer_review',
		'post_status'         => $hide_draft ? 'publish' : array( 'publish', 'draft' ),
		'posts_per_page'      => $per_column * tmnhanphat_get_customer_review_column_count(),
		'order'               => $order,
		'no_found_rows'       => true,
		'ignore_sticky_posts' => true,
		// "Hide Review" tắt riêng từng bài (mục 3/4) — độc lập với trạng thái Draft/Publish.
		'meta_query'          => array(
			'relation' => 'OR',
			array(
				'key'     => '_tmnp_review_hide',
				'value'   => '1',
				'compare' => '!=',
			),
			array(
				'key'     => '_tmnp_review_hide',
				'compare' => 'NOT EXISTS',
			),
		),
	);

	if ( 'sort_order' === $orderby ) {
		$args['orderby']  = 'meta_value_num';
		$args['meta_key'] = '_tmnp_review_order'; // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key -- danh sách Review nhỏ, orderby theo Sort Order là tính năng chính của Meta Box.
	} else {
		$args['orderby'] = $orderby;
	}

	return new WP_Query( $args );
}

/**
 * Fallback Comment mặc định (mục 4) — hiển thị khi Editor rỗng, KHÔNG ghi Database.
 *
 * @return string
 */
function tmnhanphat_get_customer_review_default_comment() {
	return __( 'Chúng tôi rất hài lòng về chất lượng sản phẩm, quá trình thi công và dịch vụ hỗ trợ sau bán hàng.', 'tmnhanphat' );
}

/**
 * SVG Avatar mặc định — dùng khi Review chưa có Featured Image (mục 4), cùng tinh
 * thần fallback icon mặc định của Why Choose Section (không cần asset ảnh riêng).
 *
 * @return string HTML đã an toàn (SVG tĩnh, không chứa dữ liệu động).
 */
function tmnhanphat_get_customer_review_default_avatar_svg() {
	return '<svg class="review-card__avatar-fallback" width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" focusable="false" aria-hidden="true">'
		. '<circle cx="12" cy="8" r="4" fill="currentColor" />'
		. '<path d="M4 20c0-4.4 3.6-7 8-7s8 2.6 8 7" fill="currentColor" />'
		. '</svg>';
}

/**
 * Đọc + xử lý fallback đầy đủ 1 Review từ CPT customer_review (mục 4 — fallback
 * CHỈ ở tầng hiển thị, không update_post_meta/DB nào ở đây).
 *
 * @param int $post_id ID bài customer_review.
 * @return array<string, mixed>
 */
function tmnhanphat_get_customer_review_item( $post_id ) {
	$name    = get_the_title( $post_id );
	$role    = get_post_meta( $post_id, '_tmnp_review_role', true );
	$comment = tmnhanphat_get_excerpt( $post_id, 40 );

	return array(
		'name'          => '' !== trim( (string) $name ) ? $name : __( 'Khách hàng', 'tmnhanphat' ),
		'role'          => '' !== trim( (string) $role ) ? $role : __( 'Khách hàng đã xác minh', 'tmnhanphat' ),
		'comment'       => '' !== trim( wp_strip_all_tags( (string) $comment ) ) ? $comment : esc_html( tmnhanphat_get_customer_review_default_comment() ),
		'has_avatar'    => has_post_thumbnail( $post_id ),
		'avatar_id'     => get_post_thumbnail_id( $post_id ),
		'is_raw_empty'  => ( '' === trim( (string) $name ) && '' === trim( wp_strip_all_tags( (string) $comment ) ) ),
	);
}

/**
 * Danh sách Review DEMO (mục 9 audit) — hiển thị khi CPT customer_review CHƯA có
 * bài nào, để section không bao giờ trống/lỗi. CHỈ tồn tại ở tầng hiển thị:
 * không tạo Post tự động, không ghi Database, không CPT Demo. Nhập Review thật
 * đầu tiên là demo tự biến mất.
 *
 * @return array<int, array<string, mixed>> Item cùng shape với tmnhanphat_get_customer_review_item().
 */
function tmnhanphat_get_customer_review_demo_items() {
	$demos = array(
		array( 'Nguyễn Văn An', 'Khách hàng đã xác minh', 'Chúng tôi rất hài lòng về chất lượng sản phẩm, quy trình lắp đặt chuyên nghiệp và dịch vụ hỗ trợ tận tình của Thang Máy Nhân Phát.' ),
		array( 'Trần Thị Bích', 'Chủ đầu tư', 'Thang máy vận hành êm ái, đội ngũ kỹ thuật tư vấn rõ ràng từ khâu khảo sát đến bàn giao. Rất đáng tin cậy.' ),
		array( 'Lê Minh Cường', 'Quản lý dự án', 'Tiến độ thi công đúng cam kết, báo giá minh bạch không phát sinh. Sẽ tiếp tục hợp tác ở các công trình sau.' ),
		array( 'Phạm Hoàng Dũng', 'Khách hàng cá nhân', 'Lắp thang máy gia đình 350kg, hoàn thiện gọn gàng trong 2 tuần. Bảo trì định kỳ rất chu đáo.' ),
		array( 'Võ Thu Hà', 'CEO', 'Dịch vụ hậu mãi nhanh chóng, gọi là có mặt. Chất lượng xứng đáng với chi phí đầu tư.' ),
		array( 'Đặng Quốc Huy', 'Kỹ sư xây dựng', 'Thiết bị chính hãng, hồ sơ kiểm định đầy đủ. Đối tác chuyên nghiệp hiếm có trong lĩnh vực thang máy.' ),
	);

	$items = array();

	foreach ( $demos as $demo ) {
		$items[] = array(
			'name'         => $demo[0],
			'role'         => $demo[1],
			'comment'      => esc_html( $demo[2] ),
			'has_avatar'   => false, // Demo dùng Avatar mặc định của theme (SVG fallback).
			'avatar_id'    => 0,
			'is_raw_empty' => false,
		);
	}

	return $items;
}

/**
 * Xây 3 cột dữ liệu Review đã xử lý fallback, chia ROUND-ROBIN theo thứ tự Query —
 * mỗi cột là 1 danh sách item độc lập cho Marquee (mục 7/8). CPT chưa có bài nào
 * → tự đổ dữ liệu Demo (tầng hiển thị, mục 9 audit) để section luôn đầy đủ.
 *
 * @return array<int, array<int, array<string, mixed>>> Luôn có đúng 3 phần tử (có thể rỗng).
 */
function tmnhanphat_get_customer_review_columns() {
	$column_count = tmnhanphat_get_customer_review_column_count();
	$columns      = array_fill( 0, $column_count, array() );
	$hide_empty   = tmnhanphat_get_customer_review_mod( 'tmnhanphat_review_hide_empty' );

	$query = tmnhanphat_get_customer_review_query();
	$index = 0;

	while ( $query->have_posts() ) {
		$query->the_post();

		$item = tmnhanphat_get_customer_review_item( get_the_ID() );

		if ( $hide_empty && $item['is_raw_empty'] ) {
			continue; // Review hoàn toàn trống (không Title, không Comment) — bỏ qua, không tính vào round-robin.
		}

		$columns[ $index % $column_count ][] = $item;
		++$index;
	}

	wp_reset_postdata();

	// CPT trống hoàn toàn → đổ Demo round-robin (không render section trống — mục 9 audit).
	if ( 0 === $index ) {
		foreach ( tmnhanphat_get_customer_review_demo_items() as $demo_index => $demo_item ) {
			$columns[ $demo_index % $column_count ][] = $demo_item;
		}
	}

	return $columns;
}

/**
 * Sinh chuỗi CSS custom properties + rule tĩnh cho Customer Review Section (mục 22).
 *
 * @return string
 */
function tmnhanphat_render_customer_review_css_vars() {
	$bg_color        = tmnhanphat_get_customer_review_mod( 'tmnhanphat_review_bg_color' );
	$bg_image        = tmnhanphat_get_customer_review_mod( 'tmnhanphat_review_bg_image' );
	$container_width = absint( tmnhanphat_get_customer_review_mod( 'tmnhanphat_review_container_width' ) );

	$padding_desktop = absint( tmnhanphat_get_customer_review_mod( 'tmnhanphat_review_padding_desktop' ) );
	$padding_tablet  = absint( tmnhanphat_get_customer_review_mod( 'tmnhanphat_review_padding_tablet' ) );
	$padding_mobile  = absint( tmnhanphat_get_customer_review_mod( 'tmnhanphat_review_padding_mobile' ) );
	$margin_desktop  = absint( tmnhanphat_get_customer_review_mod( 'tmnhanphat_review_margin_desktop' ) );
	$margin_tablet   = absint( tmnhanphat_get_customer_review_mod( 'tmnhanphat_review_margin_tablet' ) );
	$margin_mobile   = absint( tmnhanphat_get_customer_review_mod( 'tmnhanphat_review_margin_mobile' ) );

	$title_color  = tmnhanphat_get_customer_review_mod( 'tmnhanphat_review_title_color' );
	$title_size   = absint( tmnhanphat_get_customer_review_mod( 'tmnhanphat_review_title_size' ) );
	$title_tablet = absint( tmnhanphat_get_customer_review_mod( 'tmnhanphat_review_title_size_tablet' ) );
	$title_mobile = absint( tmnhanphat_get_customer_review_mod( 'tmnhanphat_review_title_size_mobile' ) );
	$title_align  = tmnhanphat_get_customer_review_mod( 'tmnhanphat_review_title_align' );

	$desc_color  = tmnhanphat_get_customer_review_mod( 'tmnhanphat_review_desc_color' );
	$desc_size   = absint( tmnhanphat_get_customer_review_mod( 'tmnhanphat_review_desc_size' ) );
	$desc_tablet = absint( tmnhanphat_get_customer_review_mod( 'tmnhanphat_review_desc_size_tablet' ) );
	$desc_mobile = absint( tmnhanphat_get_customer_review_mod( 'tmnhanphat_review_desc_size_mobile' ) );
	$desc_width  = absint( tmnhanphat_get_customer_review_mod( 'tmnhanphat_review_desc_max_width' ) );
	$header_margin = absint( tmnhanphat_get_customer_review_mod( 'tmnhanphat_review_header_margin_bottom' ) );

	$cols_desktop = max( 1, min( 3, absint( tmnhanphat_get_customer_review_mod( 'tmnhanphat_review_cols_desktop' ) ) ) );
	$cols_tablet  = max( 1, min( 3, absint( tmnhanphat_get_customer_review_mod( 'tmnhanphat_review_cols_tablet' ) ) ) );
	$cols_mobile  = max( 1, min( 3, absint( tmnhanphat_get_customer_review_mod( 'tmnhanphat_review_cols_mobile' ) ) ) );

	$card_gap          = absint( tmnhanphat_get_customer_review_mod( 'tmnhanphat_review_card_gap' ) );
	$column_gap        = absint( tmnhanphat_get_customer_review_mod( 'tmnhanphat_review_column_gap' ) );
	$card_radius       = absint( tmnhanphat_get_customer_review_mod( 'tmnhanphat_review_card_radius' ) );
	$card_border_width = absint( tmnhanphat_get_customer_review_mod( 'tmnhanphat_review_card_border_width' ) );
	$card_border_color = tmnhanphat_get_customer_review_mod( 'tmnhanphat_review_card_border_color' );
	$card_bg           = tmnhanphat_get_customer_review_mod( 'tmnhanphat_review_card_bg' );
	$card_shadow       = tmnhanphat_get_why_choose_shadow_value( tmnhanphat_get_customer_review_mod( 'tmnhanphat_review_card_shadow' ) );
	$card_hover_shadow = tmnhanphat_get_why_choose_shadow_value( tmnhanphat_get_customer_review_mod( 'tmnhanphat_review_card_hover_shadow' ) );
	$card_padding      = absint( tmnhanphat_get_customer_review_mod( 'tmnhanphat_review_card_padding' ) );

	$avatar_size        = absint( tmnhanphat_get_customer_review_mod( 'tmnhanphat_review_avatar_size' ) );
	$avatar_size_tablet = absint( tmnhanphat_get_customer_review_mod( 'tmnhanphat_review_avatar_size_tablet' ) );
	$avatar_size_mobile = absint( tmnhanphat_get_customer_review_mod( 'tmnhanphat_review_avatar_size_mobile' ) );
	$avatar_radius      = absint( tmnhanphat_get_customer_review_mod( 'tmnhanphat_review_avatar_radius' ) );
	$avatar_fit         = tmnhanphat_get_customer_review_mod( 'tmnhanphat_review_avatar_fit' );

	$name_size    = absint( tmnhanphat_get_customer_review_mod( 'tmnhanphat_review_name_size' ) );
	$name_weight  = absint( tmnhanphat_get_customer_review_mod( 'tmnhanphat_review_name_weight' ) );
	$name_color   = tmnhanphat_get_customer_review_mod( 'tmnhanphat_review_name_color' );
	$role_size    = absint( tmnhanphat_get_customer_review_mod( 'tmnhanphat_review_role_size' ) );
	$role_color   = tmnhanphat_get_customer_review_mod( 'tmnhanphat_review_role_color' );
	$comment_size = absint( tmnhanphat_get_customer_review_mod( 'tmnhanphat_review_comment_size' ) );
	$comment_color = tmnhanphat_get_customer_review_mod( 'tmnhanphat_review_comment_color' );
	$comment_clamp = absint( tmnhanphat_get_customer_review_mod( 'tmnhanphat_review_comment_clamp' ) );
	$comment_clamp_mobile = absint( tmnhanphat_get_customer_review_mod( 'tmnhanphat_review_comment_clamp_mobile' ) );

	$animation_enable = tmnhanphat_get_customer_review_mod( 'tmnhanphat_review_animation_enable' );
	$speed            = max( 5, absint( tmnhanphat_get_customer_review_mod( 'tmnhanphat_review_speed' ) ) );
	$speed_mobile     = max( 5, absint( tmnhanphat_get_customer_review_mod( 'tmnhanphat_review_speed_mobile' ) ) );
	$loop             = tmnhanphat_get_customer_review_mod( 'tmnhanphat_review_loop' );
	$mask_enable      = tmnhanphat_get_customer_review_mod( 'tmnhanphat_review_mask_enable' );
	$mask_top         = tmnhanphat_get_customer_review_mod( 'tmnhanphat_review_mask_fade_top' );
	$mask_bottom      = tmnhanphat_get_customer_review_mod( 'tmnhanphat_review_mask_fade_bottom' );
	$mask_height      = absint( tmnhanphat_get_customer_review_mod( 'tmnhanphat_review_mask_height' ) );

	$direction_map = array( 'up' => 'normal', 'down' => 'reverse' );
	$col1_dir      = isset( $direction_map[ tmnhanphat_get_customer_review_mod( 'tmnhanphat_review_col1_direction' ) ] ) ? $direction_map[ tmnhanphat_get_customer_review_mod( 'tmnhanphat_review_col1_direction' ) ] : 'normal';
	$col2_dir      = isset( $direction_map[ tmnhanphat_get_customer_review_mod( 'tmnhanphat_review_col2_direction' ) ] ) ? $direction_map[ tmnhanphat_get_customer_review_mod( 'tmnhanphat_review_col2_direction' ) ] : 'reverse';
	$col3_dir      = isset( $direction_map[ tmnhanphat_get_customer_review_mod( 'tmnhanphat_review_col3_direction' ) ] ) ? $direction_map[ tmnhanphat_get_customer_review_mod( 'tmnhanphat_review_col3_direction' ) ] : 'normal';

	$css  = ':root{';
	$css .= '--review-bg:' . $bg_color . ';';
	$css .= '--review-bg-image:' . ( $bg_image ? 'url("' . esc_url( $bg_image ) . '")' : 'none' ) . ';';
	$css .= '--review-container-width:' . ( $container_width > 0 ? $container_width . 'px' : 'var(--container-max-width)' ) . ';';
	$css .= '--review-padding:' . $padding_desktop . 'px;';
	$css .= '--review-margin:' . $margin_desktop . 'px;';
	$css .= '--review-title-color:' . $title_color . ';';
	$css .= '--review-title-size:' . $title_size . 'px;';
	$css .= '--review-title-align:' . $title_align . ';';
	$css .= '--review-desc-color:' . $desc_color . ';';
	$css .= '--review-desc-size:' . $desc_size . 'px;';
	$css .= '--review-desc-max-width:' . $desc_width . 'px;';
	$css .= '--review-desc-margin-x:' . ( 'center' === $title_align ? 'auto' : '0' ) . ';';
	$css .= '--review-header-margin:' . $header_margin . 'px;';
	$css .= '--review-card-gap:' . $card_gap . 'px;';
	$css .= '--review-column-gap:' . $column_gap . 'px;';
	$css .= '--review-card-radius:' . $card_radius . 'px;';
	$css .= '--review-card-border:' . ( $card_border_width > 0 ? $card_border_width . 'px solid ' . $card_border_color : 'none' ) . ';';
	$css .= '--review-card-bg:' . $card_bg . ';';
	$css .= '--review-card-shadow:' . $card_shadow . ';';
	$css .= '--review-card-hover-shadow:' . $card_hover_shadow . ';';
	$css .= '--review-card-padding:' . $card_padding . 'px;';
	$css .= '--review-avatar-size:' . $avatar_size . 'px;';
	$css .= '--review-avatar-radius:' . $avatar_radius . '%;';
	$css .= '--review-avatar-fit:' . $avatar_fit . ';';
	$css .= '--review-name-size:' . $name_size . 'px;';
	$css .= '--review-name-weight:' . $name_weight . ';';
	$css .= '--review-name-color:' . $name_color . ';';
	$css .= '--review-role-size:' . $role_size . 'px;';
	$css .= '--review-role-color:' . $role_color . ';';
	$css .= '--review-comment-size:' . $comment_size . 'px;';
	$css .= '--review-comment-color:' . $comment_color . ';';
	$css .= '--review-comment-clamp:' . $comment_clamp . ';';
	$css .= '--review-speed:' . $speed . 's;';
	$css .= '--review-col1-direction:' . $col1_dir . ';';
	$css .= '--review-col2-direction:' . $col2_dir . ';';
	$css .= '--review-col3-direction:' . $col3_dir . ';';
	$css .= '--review-mask-height:' . $mask_height . 'px;';
	$css .= '--review-mask-color:' . $bg_color . ';';
	$css .= '}';

	// Animation tắt hoàn toàn (setting, KHÔNG phải prefers-reduced-motion — file JS xử lý
	// riêng phần đó) — track đứng yên, hiển thị danh sách tĩnh (mục 15: Enable Animation).
	if ( ! $animation_enable ) {
		$css .= '.review-marquee__track{animation:none !important;}';
	} elseif ( ! $loop ) {
		// Loop tắt: chạy đúng 1 vòng rồi dừng (mặc định luôn lặp vô hạn theo mục 8).
		$css .= '.review-marquee__track{animation-iteration-count:1;}';
	}

	if ( ! $mask_enable || ! $mask_top ) {
		$css .= '.review-marquee::before{display:none;}';
	}

	if ( ! $mask_enable || ! $mask_bottom ) {
		$css .= '.review-marquee::after{display:none;}';
	}

	// Ẩn/hiện cột theo breakpoint (kiến trúc 3 cột cố định trong DOM — mục 16 Responsive).
	$css .= tmnhanphat_get_customer_review_column_visibility_css( $cols_desktop );

	$css .= '@media (max-width:991px){:root{';
	$css .= '--review-padding:' . $padding_tablet . 'px;';
	$css .= '--review-margin:' . $margin_tablet . 'px;';
	$css .= '--review-title-size:' . $title_tablet . 'px;';
	$css .= '--review-desc-size:' . $desc_tablet . 'px;';
	$css .= '--review-avatar-size:' . $avatar_size_tablet . 'px;';
	$css .= '}' . tmnhanphat_get_customer_review_column_visibility_css( $cols_tablet ) . '}';

	$css .= '@media (max-width:599px){:root{';
	$css .= '--review-padding:' . $padding_mobile . 'px;';
	$css .= '--review-margin:' . $margin_mobile . 'px;';
	$css .= '--review-title-size:' . $title_mobile . 'px;';
	$css .= '--review-desc-size:' . $desc_mobile . 'px;';
	$css .= '--review-avatar-size:' . $avatar_size_mobile . 'px;';
	$css .= '--review-comment-clamp:' . $comment_clamp_mobile . ';';
	$css .= '--review-speed:' . $speed_mobile . 's;'; // Mobile chậm hơn Desktop theo spec.
	$css .= '}' . tmnhanphat_get_customer_review_column_visibility_css( $cols_mobile ) . '}';

	return $css;
}

/**
 * Sinh rule ẨN cột thứ (N+1)..3 khi số cột hiển thị < 3 (kiến trúc 3 cột cố định
 * trong DOM — mục 16). Không cần rule gì khi hiển thị đủ 3 cột.
 *
 * @param int $visible_count Số cột hiển thị (1-3).
 * @return string
 */
function tmnhanphat_get_customer_review_column_visibility_css( $visible_count ) {
	$css = '';

	for ( $i = $visible_count + 1; $i <= tmnhanphat_get_customer_review_column_count(); $i++ ) {
		$css .= '.review-marquee__column:nth-child(' . $i . '){display:none;}';
	}

	return $css;
}

/* ==========================================================================
 * FEATURED NEWS SECTION ("Tin tức nổi bật", trang chủ) — helper riêng.
 *
 * Dữ liệu là POST THƯỜNG (wp_posts) theo Category chọn trong Customizer (0 = mọi
 * bài viết mới nhất) — KHÔNG CPT/Taxonomy mới, không hardcode ID, WP_Query chỉ lấy
 * đúng số bài cần render. Chưa có bài nào → tự đổ Demo Data ở tầng hiển thị
 * (không ghi Database) để section không bao giờ trống.
 *
 * Header TÁI SỬ DỤNG style review-home__heading (gạch đôi đỏ/xanh dưới title) —
 * template gắn thêm class đó khi "Reuse Heading Style" bật, News chỉ override
 * size/màu qua biến --news-* trong scope .news-home (không nhân bản CSS).
 *
 * Slider tái sử dụng .tmnp-slider + slider.js (3/2/1 card mỗi khung nhìn, dots +
 * arrows sinh DOM động). Lưu ý: dots của slider.js là 1 dot/BÀI (trượt từng card),
 * không phải 1 dot/trang-3-bài — chấp nhận để không đụng logic slider dùng chung.
 * ========================================================================== */

/**
 * Default value tập trung cho toàn bộ setting của News Section (mục 22).
 *
 * @return array<string, mixed>
 */
function tmnhanphat_news_defaults() {
	return array(
		// ----- General -----
		'tmnhanphat_news_enable'          => true,
		'tmnhanphat_news_section_id'      => 'tin-tuc',
		// Dùng Global Container (0) để padding 2 bên KHỚP các section khác — 3 card tự co
		// theo container (~384px mỗi card ở 1200), không giữ width cố định 513 gây tràn.
		'tmnhanphat_news_container_width' => 0,
		// Figma: bg (9791) → Header (9827) = 36 (đáy 90 cố định ở CSS).
		'tmnhanphat_news_padding_desktop' => 28, // Migrate: nhịp dọc mới (36→28).
		'tmnhanphat_news_padding_tablet'  => 64,
		'tmnhanphat_news_padding_mobile'  => 48,
		'tmnhanphat_news_margin_desktop'  => 0,
		'tmnhanphat_news_margin_tablet'   => 0,
		'tmnhanphat_news_margin_mobile'   => 0,
		'tmnhanphat_news_bg_color'        => '#ffffff',
		'tmnhanphat_news_bg_image'        => '',
		'tmnhanphat_news_bg_opacity'      => 100,
		'tmnhanphat_news_bg_position'     => 'center',
		'tmnhanphat_news_bg_size'         => 'cover',
		// Decoration tam giác xanh góc trên phải (Figma 381 × 464).
		'tmnhanphat_news_deco_enable'     => true,
		'tmnhanphat_news_deco_image'      => '',
		'tmnhanphat_news_deco_width'      => 381,
		'tmnhanphat_news_deco_height'     => 464,

		// ----- Header (reuse style review-home__heading) — Figma: eyebrow 12/700 #0C4B9B,
		// Heading 60/700 MỘT MÀU #0C4B9B (2 span giữ nguyên markup, cùng màu), ls -3.3,
		// Description 14/400 #667180 box 817; Header → Cards cách 151. -----
		'tmnhanphat_news_small_title'          => 'KIẾN THỨC & XU HƯỚNG',
		'tmnhanphat_news_small_size'           => 12,
		'tmnhanphat_news_heading_blue_text'    => 'Tin tức',
		'tmnhanphat_news_heading_red_text'     => 'nổi bật',
		'tmnhanphat_news_heading_blue_color'   => '#0C4B9B',
		'tmnhanphat_news_heading_red_color'    => '#E31F2B', // Chủ dự án chốt: "nổi bật" giữ màu đỏ.
		'tmnhanphat_news_title_size'           => 60,
		'tmnhanphat_news_title_size_tablet'    => 36,
		'tmnhanphat_news_title_size_mobile'    => 26,
		'tmnhanphat_news_desc_text'            => 'Tổng hợp thông tin hữu ích, kiến thức chuyên ngành và những cập nhật mới nhất từ Nhân Phát, giúp khách hàng nắm bắt xu hướng thị trường.',
		'tmnhanphat_news_desc_color'           => '#667180',
		'tmnhanphat_news_desc_size'            => 14,
		'tmnhanphat_news_desc_max_width'       => 817,
		'tmnhanphat_news_header_align'         => 'center',
		'tmnhanphat_news_header_margin_bottom' => 151,
		'tmnhanphat_news_reuse_heading'        => true,

		// ----- Query -----
		'tmnhanphat_news_category'     => 0, // 0 = mọi bài viết (mặc định hoạt động ngay, admin chọn Category Tin tức sau).
		'tmnhanphat_news_count'        => 9,
		'tmnhanphat_news_orderby'      => 'date',
		'tmnhanphat_news_order'        => 'DESC',
		'tmnhanphat_news_exclude_cats' => '',
		'tmnhanphat_news_hide_sticky'  => true,

		// ----- Card (Figma 513×561 ở khung rộng — co theo container, chiều cao AUTO cân
		// theo nội dung, flex-track tự đồng đều chiều cao các card cùng hàng) -----
		// Figma: card 513×561 r=20, viền #E8E6E1 (CSS), KHÔNG shadow, padding trong 28,
		// gap giữa card 15.
		'tmnhanphat_news_card_width'        => 513,
		'tmnhanphat_news_card_height'       => 0, // 0 = auto (giảm chiều cao box, không ép 561).
		'tmnhanphat_news_card_radius'       => 20,
		'tmnhanphat_news_card_shadow'       => 'none',
		'tmnhanphat_news_card_hover_shadow' => 'medium',
		'tmnhanphat_news_card_padding'      => 22, // Migrate: padding card mềm (28→22).
		'tmnhanphat_news_gap'               => 15,

		// ----- Thumbnail (ảnh khớp SÁT viền card, bo góc trên theo Card Radius — không
		// có khoảng thừa quanh ảnh; radius để 0 vì card overflow:hidden tự bo góc trên) -----
		'tmnhanphat_news_thumb_height' => 286, // Figma: ảnh card 513×286.
		'tmnhanphat_news_thumb_radius' => 0,
		'tmnhanphat_news_thumb_fit'    => 'cover',
		'tmnhanphat_news_thumb_zoom'   => true,

		// ----- Meta — Figma: date/author 20/400 #A8AEB8 (~2.3:1 trên nền trắng — fail
		// WCAG AA, nâng lên #667180 ~4.9:1, giữ đúng họ xám xanh của design). -----
		'tmnhanphat_news_show_date'   => true,
		'tmnhanphat_news_show_author' => true,
		'tmnhanphat_news_date_format' => 'd/m/Y',
		'tmnhanphat_news_meta_size'   => 20,
		'tmnhanphat_news_meta_color'  => '#667180',

		// ----- Card Title — Figma: 30/700 ĐEN. -----
		'tmnhanphat_news_ptitle_size'        => 30,
		'tmnhanphat_news_ptitle_size_tablet' => 20,
		'tmnhanphat_news_ptitle_size_mobile' => 18,
		'tmnhanphat_news_ptitle_weight'      => '700',
		'tmnhanphat_news_ptitle_clamp'       => 2,
		'tmnhanphat_news_ptitle_color'       => '#000000',

		// ----- Card Description — Figma: 20/400 #A8AEB8 (fail WCAG AA trên nền trắng,
		// nâng lên #667180 — cùng lý do Meta ở trên). -----
		'tmnhanphat_news_pdesc_size'         => 20,
		'tmnhanphat_news_pdesc_clamp'        => 2,
		'tmnhanphat_news_pdesc_clamp_mobile' => 2,
		'tmnhanphat_news_pdesc_color'        => '#667180',

		// ----- Read More — Figma: 20/400 vàng đồng #B8955A, căn phải. TOKEN GOLD CHÍNH
		// của theme (vai trò: điểm nhấn hành động phụ trên nền sáng; hover #9A7A46).
		// Ngoại lệ duy nhất được ghi chú: viền submit Quote #F4B844 (glow trên nền navy
		// — vai trò khác, cần độ chói cao hơn). Thêm gold mới phải quy về 1 trong 2. -----
		'tmnhanphat_news_readmore_text'        => 'Xem thêm',
		'tmnhanphat_news_readmore_color'       => '#B8955A',
		'tmnhanphat_news_readmore_hover_color' => '#9A7A46',

		// ----- Slider -----
		'tmnhanphat_news_autoplay_enable'  => true,
		'tmnhanphat_news_autoplay_speed'   => 5000,
		'tmnhanphat_news_transition_speed' => 600,
		'tmnhanphat_news_infinite'         => true,
		'tmnhanphat_news_pause_hover'      => true,
		'tmnhanphat_news_drag_enable'      => true,
		// Figma CÓ nút Prev/Next (Ellipse 68×68 #046AB5 hai bên) + dots 15px active #0D5EA3.
		'tmnhanphat_news_show_arrows'      => true,
		'tmnhanphat_news_show_dots'        => true,
		'tmnhanphat_news_dot_size'         => 15,
		'tmnhanphat_news_dot_gap'          => 16,
		'tmnhanphat_news_dot_active'       => '#0D5EA3',
		'tmnhanphat_news_dot_inactive'     => '#D9D9D9',
		'tmnhanphat_news_arrow_size'       => 68,
		'tmnhanphat_news_arrow_bg'         => '#046AB5',
		'tmnhanphat_news_arrow_color'      => '#ffffff',
		'tmnhanphat_news_arrow_radius'     => 100,
	);
}

/**
 * Đọc 1 theme_mod của News với default tập trung (mục 22).
 *
 * @param string $key Setting id.
 * @return mixed
 */
function tmnhanphat_get_news_mod( $key ) {
	$defaults = tmnhanphat_news_defaults();
	$default  = isset( $defaults[ $key ] ) ? $defaults[ $key ] : '';

	return get_theme_mod( $key, $default );
}

/**
 * Query danh sách Tin tức theo setting Customizer — Category 0 = mọi bài, chỉ lấy
 * đúng số bài cần render (no_found_rows — mục 15).
 *
 * @return WP_Query
 */
function tmnhanphat_get_news_query() {
	$args = array(
		'post_type'           => 'post',
		'post_status'         => 'publish',
		'posts_per_page'      => max( 1, absint( tmnhanphat_get_news_mod( 'tmnhanphat_news_count' ) ) ),
		'orderby'             => tmnhanphat_get_news_mod( 'tmnhanphat_news_orderby' ),
		'order'               => tmnhanphat_get_news_mod( 'tmnhanphat_news_order' ),
		'no_found_rows'       => true,
		'ignore_sticky_posts' => tmnhanphat_get_news_mod( 'tmnhanphat_news_hide_sticky' ),
	);

	$category = absint( tmnhanphat_get_news_mod( 'tmnhanphat_news_category' ) );
	if ( $category && term_exists( $category, 'category' ) ) {
		$args['cat'] = $category;
	}

	$exclude_cats = tmnhanphat_parse_id_list( tmnhanphat_get_news_mod( 'tmnhanphat_news_exclude_cats' ) );
	if ( ! empty( $exclude_cats ) ) {
		$args['category__not_in'] = $exclude_cats;
	}

	return new WP_Query( $args );
}

/**
 * Xây danh sách item Tin tức đã xử lý fallback: Excerpt rỗng → đầu Content
 * (tmnhanphat_get_excerpt), Author rỗng → "Admin", Date = Publish Date theo
 * Date Format. Chưa có bài nào → Demo Data (tầng hiển thị, không ghi DB — mục 13).
 *
 * @return array<int, array<string, mixed>>
 */
function tmnhanphat_get_news_items() {
	$items       = array();
	$date_format = tmnhanphat_get_news_mod( 'tmnhanphat_news_date_format' );
	$query       = tmnhanphat_get_news_query();

	while ( $query->have_posts() ) {
		$query->the_post();

		$author = get_the_author();

		$items[] = array(
			'title'     => get_the_title(),
			'excerpt'   => tmnhanphat_get_excerpt( get_the_ID(), 22 ),
			'date'      => esc_html( get_the_date( $date_format ) ),
			'author'    => '' !== trim( (string) $author ) ? $author : __( 'Admin', 'tmnhanphat' ),
			'permalink' => get_permalink(),
			'has_thumb' => has_post_thumbnail(),
			'thumb_id'  => get_post_thumbnail_id(),
		);
	}

	wp_reset_postdata();

	// Chưa có bài viết nào → Demo Data ×3 (đúng nội dung design) để section không trống.
	if ( empty( $items ) ) {
		for ( $i = 0; $i < 3; $i++ ) {
			$items[] = array(
				'title'     => __( 'Top các hãng thang máy gia đình uy tín trên thị trường', 'tmnhanphat' ),
				'excerpt'   => esc_html__( 'Khám phá các hãng thang máy gia đình được nhiều khách hàng tin chọn cùng kinh nghiệm lựa chọn phù hợp.', 'tmnhanphat' ),
				'date'      => esc_html( date_i18n( $date_format ) ),
				'author'    => __( 'Admin', 'tmnhanphat' ),
				'permalink' => '',
				'has_thumb' => false,
				'thumb_id'  => 0,
			);
		}
	}

	return $items;
}

/**
 * Sinh chuỗi CSS custom properties cho News Section (mục 22).
 *
 * @return string
 */
function tmnhanphat_render_news_css_vars() {
	$m = 'tmnhanphat_get_news_mod';

	$container_width = absint( $m( 'tmnhanphat_news_container_width' ) );
	$bg_image        = $m( 'tmnhanphat_news_bg_image' );

	$css  = ':root{';
	$css .= '--news-bg:' . $m( 'tmnhanphat_news_bg_color' ) . ';';
	$css .= '--news-bg-image:' . ( $bg_image ? 'url("' . esc_url( $bg_image ) . '")' : 'none' ) . ';';
	$css .= '--news-bg-opacity:' . ( absint( $m( 'tmnhanphat_news_bg_opacity' ) ) / 100 ) . ';';
	$css .= '--news-bg-position:' . $m( 'tmnhanphat_news_bg_position' ) . ';';
	$css .= '--news-bg-size:' . $m( 'tmnhanphat_news_bg_size' ) . ';';
	$css .= '--news-container-width:' . ( $container_width > 0 ? $container_width . 'px' : 'var(--container-max-width)' ) . ';';
	$css .= '--news-padding:' . absint( $m( 'tmnhanphat_news_padding_desktop' ) ) . 'px;';
	$css .= '--news-margin:' . absint( $m( 'tmnhanphat_news_margin_desktop' ) ) . 'px;';
	$css .= '--news-deco-width:' . absint( $m( 'tmnhanphat_news_deco_width' ) ) . 'px;';
	$css .= '--news-deco-height:' . absint( $m( 'tmnhanphat_news_deco_height' ) ) . 'px;';
	$css .= '--news-small-size:' . absint( $m( 'tmnhanphat_news_small_size' ) ) . 'px;';
	$css .= '--news-heading-blue:' . $m( 'tmnhanphat_news_heading_blue_color' ) . ';';
	$css .= '--news-heading-red:' . $m( 'tmnhanphat_news_heading_red_color' ) . ';';
	$css .= '--news-title-size:' . absint( $m( 'tmnhanphat_news_title_size' ) ) . 'px;';
	$css .= '--news-desc-color:' . $m( 'tmnhanphat_news_desc_color' ) . ';';
	$css .= '--news-desc-size:' . absint( $m( 'tmnhanphat_news_desc_size' ) ) . 'px;';
	$css .= '--news-desc-max-width:' . absint( $m( 'tmnhanphat_news_desc_max_width' ) ) . 'px;';
	$css .= '--news-header-align:' . $m( 'tmnhanphat_news_header_align' ) . ';';
	$css .= '--news-header-margin-x:' . ( 'center' === $m( 'tmnhanphat_news_header_align' ) ? 'auto' : '0' ) . ';';
	$css .= '--news-header-margin:' . absint( $m( 'tmnhanphat_news_header_margin_bottom' ) ) . 'px;';
	$news_card_h = absint( $m( 'tmnhanphat_news_card_height' ) );
	$css .= '--news-card-width:' . absint( $m( 'tmnhanphat_news_card_width' ) ) . 'px;';
	$css .= '--news-card-height:' . ( $news_card_h > 0 ? $news_card_h . 'px' : 'auto' ) . ';';
	$css .= '--news-card-radius:' . absint( $m( 'tmnhanphat_news_card_radius' ) ) . 'px;';
	$css .= '--news-card-shadow:' . tmnhanphat_get_why_choose_shadow_value( $m( 'tmnhanphat_news_card_shadow' ) ) . ';';
	$css .= '--news-card-hover-shadow:' . tmnhanphat_get_why_choose_shadow_value( $m( 'tmnhanphat_news_card_hover_shadow' ) ) . ';';
	$css .= '--news-card-padding:' . absint( $m( 'tmnhanphat_news_card_padding' ) ) . 'px;';
	$css .= '--news-gap:' . absint( $m( 'tmnhanphat_news_gap' ) ) . 'px;';
	$css .= '--news-thumb-height:' . absint( $m( 'tmnhanphat_news_thumb_height' ) ) . 'px;';
	$css .= '--news-thumb-radius:' . absint( $m( 'tmnhanphat_news_thumb_radius' ) ) . 'px;';
	$css .= '--news-thumb-fit:' . $m( 'tmnhanphat_news_thumb_fit' ) . ';';
	$css .= '--news-thumb-zoom:' . ( $m( 'tmnhanphat_news_thumb_zoom' ) ? '1.05' : '1' ) . ';';
	$css .= '--news-meta-size:' . absint( $m( 'tmnhanphat_news_meta_size' ) ) . 'px;';
	$css .= '--news-meta-color:' . $m( 'tmnhanphat_news_meta_color' ) . ';';
	$css .= '--news-ptitle-size:' . absint( $m( 'tmnhanphat_news_ptitle_size' ) ) . 'px;';
	$css .= '--news-ptitle-weight:' . absint( $m( 'tmnhanphat_news_ptitle_weight' ) ) . ';';
	$css .= '--news-ptitle-color:' . $m( 'tmnhanphat_news_ptitle_color' ) . ';';
	$css .= '--news-pdesc-size:' . absint( $m( 'tmnhanphat_news_pdesc_size' ) ) . 'px;';
	$css .= '--news-pdesc-color:' . $m( 'tmnhanphat_news_pdesc_color' ) . ';';
	$css .= '--news-readmore-color:' . $m( 'tmnhanphat_news_readmore_color' ) . ';';
	$css .= '--news-readmore-hover:' . $m( 'tmnhanphat_news_readmore_hover_color' ) . ';';
	$css .= '--news-transition:' . absint( $m( 'tmnhanphat_news_transition_speed' ) ) . 'ms;';
	$css .= '--news-dot-size:' . absint( $m( 'tmnhanphat_news_dot_size' ) ) . 'px;';
	$css .= '--news-dot-gap:' . absint( $m( 'tmnhanphat_news_dot_gap' ) ) . 'px;';
	$css .= '--news-dot-active:' . $m( 'tmnhanphat_news_dot_active' ) . ';';
	$css .= '--news-dot-inactive:' . $m( 'tmnhanphat_news_dot_inactive' ) . ';';
	$css .= '--news-arrow-size:' . absint( $m( 'tmnhanphat_news_arrow_size' ) ) . 'px;';
	$css .= '--news-arrow-bg:' . $m( 'tmnhanphat_news_arrow_bg' ) . ';';
	$css .= '--news-arrow-color:' . $m( 'tmnhanphat_news_arrow_color' ) . ';';
	$css .= '--news-arrow-radius:' . absint( $m( 'tmnhanphat_news_arrow_radius' ) ) . '%;';
	$css .= '}';

	// Line Clamp rule tĩnh (0 = tắt — cùng pattern các module trước).
	$ptitle_clamp = absint( $m( 'tmnhanphat_news_ptitle_clamp' ) );
	$pdesc_clamp  = absint( $m( 'tmnhanphat_news_pdesc_clamp' ) );
	$pdesc_mobile = absint( $m( 'tmnhanphat_news_pdesc_clamp_mobile' ) );

	if ( $ptitle_clamp > 0 ) {
		$css .= '.news-card__title{display:-webkit-box;-webkit-box-orient:vertical;-webkit-line-clamp:' . $ptitle_clamp . ';overflow:hidden;}';
	}

	if ( $pdesc_clamp > 0 ) {
		$css .= '.news-card__excerpt{display:-webkit-box;-webkit-box-orient:vertical;-webkit-line-clamp:' . $pdesc_clamp . ';overflow:hidden;}';
	}

	$css .= '@media (max-width:991px){:root{';
	$css .= '--news-padding:' . absint( $m( 'tmnhanphat_news_padding_tablet' ) ) . 'px;';
	$css .= '--news-margin:' . absint( $m( 'tmnhanphat_news_margin_tablet' ) ) . 'px;';
	$css .= '--news-title-size:' . absint( $m( 'tmnhanphat_news_title_size_tablet' ) ) . 'px;';
	$css .= '--news-ptitle-size:' . absint( $m( 'tmnhanphat_news_ptitle_size_tablet' ) ) . 'px;';
	$css .= '}}';

	$css .= '@media (max-width:599px){:root{';
	$css .= '--news-padding:' . absint( $m( 'tmnhanphat_news_padding_mobile' ) ) . 'px;';
	$css .= '--news-margin:' . absint( $m( 'tmnhanphat_news_margin_mobile' ) ) . 'px;';
	$css .= '--news-title-size:' . absint( $m( 'tmnhanphat_news_title_size_mobile' ) ) . 'px;';
	$css .= '--news-ptitle-size:' . absint( $m( 'tmnhanphat_news_ptitle_size_mobile' ) ) . 'px;';
	$css .= '}';

	if ( $pdesc_mobile > 0 ) {
		$css .= '.news-card__excerpt{-webkit-line-clamp:' . $pdesc_mobile . ';}';
	}

	$css .= '}';

	return $css;
}

/* ==========================================================================
 * QUOTE CONTACT SECTION ("Liên hệ báo giá", trang chủ) — helper riêng.
 *
 * Layout 50/50: trái = Quote Form Card (nền xanh đậm), phải = Company Information.
 * Giá trị Email/Phone/Address TÁI SỬ DỤNG mod của Footer (tmnhanphat_footer_*) —
 * công ty nhập MỘT LẦN ở panel Footer, cả Footer lẫn Quote Section cùng hiển thị
 * (không duplicate dữ liệu — PROJECT_RULES.md mục 5/19). Quote panel chỉ điều khiển
 * hiển thị + form + heading, không chứa lại email/phone/address.
 *
 * Form ở bước này chỉ HTML + validation Frontend (assets/js/components/quote.js) —
 * chưa gửi mail (backend sau). Heading phải reuse review-home__heading (mục 11).
 * ========================================================================== */

/**
 * Default value tập trung cho toàn bộ setting của Quote Section (mục 22).
 *
 * @return array<string, mixed>
 */
function tmnhanphat_quote_defaults() {
	return array(
		// ----- General -----
		'tmnhanphat_quote_enable'          => true,
		'tmnhanphat_quote_section_id'      => 'lien-he-bao-gia',
		'tmnhanphat_quote_container_width' => 0,
		// Figma: đáy News (10878) → Form (11030) = 152 (đáy 153 cố định ở CSS).
		'tmnhanphat_quote_padding_desktop' => 104, // Migrate: nhịp dọc mới (152→104).
		'tmnhanphat_quote_padding_tablet'  => 64,
		'tmnhanphat_quote_padding_mobile'  => 48,
		'tmnhanphat_quote_margin_desktop'  => 0,
		'tmnhanphat_quote_margin_tablet'   => 0,
		'tmnhanphat_quote_margin_mobile'   => 0,
		'tmnhanphat_quote_bg_color'        => '#ffffff',
		'tmnhanphat_quote_bg_image'        => '',
		'tmnhanphat_quote_gap'             => 92, // Figma: 2 cột cách nhau 92.

		// ----- Left Card — Figma: r=31, nền GRADIENT xanh đậm (#062B69→#0A4D96, vẽ ở
		// CSS đè lên màu nền này), shadow 0/20/44 (CSS), padding 48/64/34/64 (CSS). -----
		'tmnhanphat_quote_card_enable'  => true,
		'tmnhanphat_quote_card_radius'  => 22, // Migrate: radius mềm (30→22).
		'tmnhanphat_quote_card_shadow'  => 'medium',
		'tmnhanphat_quote_card_bg'      => '#07306F',
		'tmnhanphat_quote_card_padding' => 34, // Migrate: padding card mềm (48→34).

		// ----- Form Header — Figma: title 37/700 trắng, underline đỏ 86×4 (Figma #ED1C24
		// gom về token đỏ thương hiệu #E31F2B — chênh lệch dưới ngưỡng mắt phân biệt). -----
		'tmnhanphat_quote_header_title'          => 'Thông tin khách hàng',
		'tmnhanphat_quote_header_title_color'    => '#ffffff',
		'tmnhanphat_quote_underline_color'       => '#E31F2B',
		'tmnhanphat_quote_header_size'           => 37,
		'tmnhanphat_quote_header_size_tablet'    => 24,
		'tmnhanphat_quote_header_size_mobile'    => 20,

		// ----- Form Fields (toggle + required) -----
		'tmnhanphat_quote_show_name'    => true,
		'tmnhanphat_quote_show_phone'   => true,
		'tmnhanphat_quote_show_email'   => true,
		'tmnhanphat_quote_show_service' => true,
		'tmnhanphat_quote_show_message' => true,
		'tmnhanphat_quote_required'     => true,

		// ----- Form Input Style — Figma: field cao 67, r10, viền #D0D8E3,
		// placeholder 17/400 (Figma #7A8495 ~3.8:1 — fail WCAG AA, nâng lên #667180
		// ~4.9:1 trên nền input trắng), cách nhau 15. -----
		'tmnhanphat_quote_input_height'      => 52, // Migrate: input gọn theo form hẹp hơn (67→52); floating-label top tự tính theo biến này.
		'tmnhanphat_quote_input_radius'      => 10,
		'tmnhanphat_quote_input_font_size'   => 17,
		'tmnhanphat_quote_placeholder_color' => '#667180',
		'tmnhanphat_quote_border_color'      => '#D0D8E3',
		'tmnhanphat_quote_focus_color'       => '#046AB5',
		'tmnhanphat_quote_input_gap'         => 15,
		// Danh sách loại dịch vụ — mỗi dòng 1 lựa chọn (admin sửa tự do, không hardcode/CPT).
		'tmnhanphat_quote_service_options'   => "Thang máy gia đình\nThang máy tải khách\nThang máy tải hàng\nBảo trì\nSửa chữa",

		// ----- Submit Button — Figma: 632×71 r11, GRADIENT đỏ + viền vàng 2px #F4B844
		// (vẽ ở CSS đè lên màu nền này), chữ 21/700 trắng. -----
		'tmnhanphat_quote_btn_text'       => 'Yêu cầu tư vấn',
		'tmnhanphat_quote_btn_bg'         => '#D11821',
		'tmnhanphat_quote_btn_hover_bg'   => '#C01823',
		'tmnhanphat_quote_btn_text_color' => '#ffffff',
		'tmnhanphat_quote_btn_radius'     => 11,
		'tmnhanphat_quote_btn_height'     => 71,
		'tmnhanphat_quote_btn_icon'       => true,

		// ----- Verify — Figma: 15/400 TRẮNG. -----
		'tmnhanphat_quote_verify_enable' => true,
		'tmnhanphat_quote_verify_text'   => 'Thông tin của bạn được bảo mật tuyệt đối',
		'tmnhanphat_quote_verify_color'  => '#ffffff',
		'tmnhanphat_quote_verify_size'   => 15,

		// ----- Right Content -----
		'tmnhanphat_quote_show_logo'          => true,
		'tmnhanphat_quote_logo'               => '', // Bỏ trống = dùng Logo Footer (tmnhanphat_footer_logo).
		// Figma: logo 137; Heading 70/700 "Liên hệ" #073B91 + "báo giá" #D81721;
		// Description 17/400 #1D2430 box 619.
		'tmnhanphat_quote_logo_width'         => 137,
		'tmnhanphat_quote_heading_blue_text'  => 'Liên hệ',
		'tmnhanphat_quote_heading_red_text'   => 'báo giá',
		// Gom token: Figma #073B91/#D81721 → navy #0C4B9B + đỏ #E31F2B (đồng bộ heading News/FAQ).
		'tmnhanphat_quote_heading_blue_color' => '#0C4B9B',
		'tmnhanphat_quote_heading_red_color'  => '#E31F2B',
		'tmnhanphat_quote_heading_size'       => 70,
		'tmnhanphat_quote_desc_text'          => 'Sản phẩm chính hãng, đội ngũ kỹ thuật chuyên nghiệp cùng chế độ bảo hành - bảo trì tận tâm là lý do hàng nghìn khách hàng tin chọn Nhân Phát.',
		'tmnhanphat_quote_desc_color'         => '#1D2430',
		'tmnhanphat_quote_desc_size'          => 17,
		'tmnhanphat_quote_right_align'        => 'left',
		'tmnhanphat_quote_right_max_width'    => 650,

		// ----- Contact Information — Figma: icon tròn 69 viền #DDE5EF, nét icon #0955AA;
		// label 19/700 #003C91 (CSS); value 17/400 #1C2430; item pad-block 17. -----
		'tmnhanphat_quote_show_contact_email'   => true,
		'tmnhanphat_quote_show_contact_phone'   => true,
		'tmnhanphat_quote_show_contact_address' => true,
		'tmnhanphat_quote_icon_bg'              => '#ffffff',
		'tmnhanphat_quote_icon_color'           => '#046AB5', // Gom token: Figma #0955AA → primary.
		'tmnhanphat_quote_icon_shadow'          => 'soft',
		'tmnhanphat_quote_label_size'           => 19,
		'tmnhanphat_quote_value_size'           => 17,
		'tmnhanphat_quote_divider_color'        => '#CFD6DF',
		'tmnhanphat_quote_contact_gap'          => 17,
	);
}

/**
 * Đọc 1 theme_mod của Quote với default tập trung (mục 22).
 *
 * @param string $key Setting id.
 * @return mixed
 */
function tmnhanphat_get_quote_mod( $key ) {
	$defaults = tmnhanphat_quote_defaults();
	$default  = isset( $defaults[ $key ] ) ? $defaults[ $key ] : '';

	return get_theme_mod( $key, $default );
}

/**
 * Phân tích danh sách "Loại dịch vụ" (mỗi dòng 1 option) → mảng chuỗi đã sanitize.
 *
 * @return string[]
 */
function tmnhanphat_get_quote_service_options() {
	$raw   = (string) tmnhanphat_get_quote_mod( 'tmnhanphat_quote_service_options' );
	$lines = preg_split( '/\r\n|\r|\n/', $raw );
	$out   = array();

	foreach ( $lines as $line ) {
		$line = trim( wp_strip_all_tags( $line ) );
		if ( '' !== $line ) {
			$out[] = $line;
		}
	}

	return $out;
}

/**
 * Xây danh sách Contact Item hiển thị bên phải — VALUE lấy từ mod Footer (nhập 1 lần
 * ở panel Footer), toggle hiển thị theo setting Quote. Item thiếu giá trị/bị tắt sẽ
 * bỏ qua (không hiện dòng rỗng).
 *
 * @return array<int, array{type: string, label: string, value: string, href: string}>
 */
function tmnhanphat_get_quote_contact_items() {
	$items = array();

	// Email.
	if ( tmnhanphat_get_quote_mod( 'tmnhanphat_quote_show_contact_email' ) && tmnhanphat_get_footer_mod( 'tmnhanphat_footer_email_enable' ) ) {
		$email = trim( (string) tmnhanphat_get_footer_mod( 'tmnhanphat_footer_email' ) );
		if ( '' !== $email ) {
			$items[] = array(
				'type'  => 'email',
				'label' => tmnhanphat_get_footer_mod( 'tmnhanphat_footer_email_label' ),
				'value' => $email,
				'href'  => 'mailto:' . $email,
			);
		}
	}

	// Phone.
	if ( tmnhanphat_get_quote_mod( 'tmnhanphat_quote_show_contact_phone' ) && tmnhanphat_get_footer_mod( 'tmnhanphat_footer_phone_enable' ) ) {
		$phone = trim( (string) tmnhanphat_get_footer_mod( 'tmnhanphat_footer_phone' ) );
		if ( '' !== $phone ) {
			$items[] = array(
				'type'  => 'phone',
				'label' => tmnhanphat_get_footer_mod( 'tmnhanphat_footer_phone_label' ),
				'value' => $phone,
				'href'  => 'tel:' . preg_replace( '/[^0-9+]/', '', $phone ),
			);
		}
	}

	// Address (text thường, không link).
	if ( tmnhanphat_get_quote_mod( 'tmnhanphat_quote_show_contact_address' ) && tmnhanphat_get_footer_mod( 'tmnhanphat_footer_address_enable' ) ) {
		$address = trim( (string) tmnhanphat_get_footer_mod( 'tmnhanphat_footer_address' ) );
		if ( '' !== $address ) {
			$items[] = array(
				'type'  => 'address',
				'label' => tmnhanphat_get_footer_mod( 'tmnhanphat_footer_address_label' ),
				'value' => $address,
				'href'  => '',
			);
		}
	}

	return $items;
}

/**
 * URL Logo bên phải Quote: setting riêng → fallback Logo Footer → '' (template ẩn).
 *
 * @return string
 */
function tmnhanphat_get_quote_logo_url() {
	$logo = tmnhanphat_get_quote_mod( 'tmnhanphat_quote_logo' );

	if ( ! $logo ) {
		$logo = tmnhanphat_get_footer_mod( 'tmnhanphat_footer_logo' );
	}

	return (string) $logo;
}

/**
 * SVG icon dùng cho Form field / Contact item / Verify (icon tĩnh của theme — không
 * phụ thuộc thư viện ngoài, mục 21). Trả HTML SVG an toàn (không chứa dữ liệu động).
 *
 * @param string $name Tên icon.
 * @return string
 */
function tmnhanphat_get_quote_icon_svg( $name ) {
	$icons = array(
		'user'    => '<path d="M12 12a4 4 0 100-8 4 4 0 000 8zm0 2c-4 0-7 2.2-7 5v1h14v-1c0-2.8-3-5-7-5z" fill="currentColor"/>',
		'phone'   => '<path d="M6.6 3H4a1 1 0 00-1 1c0 9.4 7.6 17 17 17a1 1 0 001-1v-2.6a1 1 0 00-.8-1l-3.3-.7a1 1 0 00-1 .3l-1.2 1.5a14.5 14.5 0 01-6-6l1.5-1.2a1 1 0 00.3-1l-.7-3.3a1 1 0 00-1-.8z" fill="currentColor"/>',
		'email'   => '<path d="M4 5h16a1 1 0 011 1v12a1 1 0 01-1 1H4a1 1 0 01-1-1V6a1 1 0 011-1zm8 7l8-5H4l8 5zm0 2L4 9v8h16V9l-8 5z" fill="currentColor"/>',
		'service' => '<path d="M4 5h16v3H4V5zm0 5.5h16v3H4v-3zM4 16h16v3H4v-3z" fill="currentColor"/>',
		'message' => '<path d="M4 4h16a1 1 0 011 1v11a1 1 0 01-1 1H8l-4 4V5a1 1 0 011-1z" fill="currentColor"/>',
		'address' => '<path d="M12 2a7 7 0 00-7 7c0 5 7 13 7 13s7-8 7-13a7 7 0 00-7-7zm0 9.5A2.5 2.5 0 1112 6.5a2.5 2.5 0 010 5z" fill="currentColor"/>',
		'send'    => '<path d="M3 11l18-8-8 18-2-7-8-3z" fill="currentColor"/>',
		'verify'  => '<path d="M12 2l2.4 1.8 3 .3 1 2.8 2.2 2-1 2.8.4 3-2.6 1.5-1.5 2.6-3-.4-2.9 1L9.6 21 7.4 18.4l-3-.3-1-2.8L1.2 13l1-2.8L1.8 7l2.6-1.5L5.9 3l3 .4L12 2z" fill="currentColor"/><path d="M9 12l2 2 4-4" stroke="#fff" stroke-width="1.6" fill="none" stroke-linecap="round" stroke-linejoin="round"/>',
	);

	$path = isset( $icons[ $name ] ) ? $icons[ $name ] : '';

	return '<svg viewBox="0 0 24 24" width="20" height="20" aria-hidden="true" focusable="false">' . $path . '</svg>';
}

/**
 * Sinh chuỗi CSS custom properties cho Quote Section (mục 22).
 *
 * @return string
 */
function tmnhanphat_render_quote_css_vars() {
	$m = 'tmnhanphat_get_quote_mod';

	$container_width = absint( $m( 'tmnhanphat_quote_container_width' ) );
	$bg_image        = $m( 'tmnhanphat_quote_bg_image' );
	$card_height     = 0; // Card cao tự nhiên theo form.

	$css  = ':root{';
	$css .= '--quote-bg:' . $m( 'tmnhanphat_quote_bg_color' ) . ';';
	$css .= '--quote-bg-image:' . ( $bg_image ? 'url("' . esc_url( $bg_image ) . '")' : 'none' ) . ';';
	$css .= '--quote-container-width:' . ( $container_width > 0 ? $container_width . 'px' : 'var(--container-max-width)' ) . ';';
	$css .= '--quote-padding:' . absint( $m( 'tmnhanphat_quote_padding_desktop' ) ) . 'px;';
	$css .= '--quote-margin:' . absint( $m( 'tmnhanphat_quote_margin_desktop' ) ) . 'px;';
	$css .= '--quote-gap:' . absint( $m( 'tmnhanphat_quote_gap' ) ) . 'px;';
	$css .= '--quote-card-radius:' . absint( $m( 'tmnhanphat_quote_card_radius' ) ) . 'px;';
	$css .= '--quote-card-shadow:' . tmnhanphat_get_why_choose_shadow_value( $m( 'tmnhanphat_quote_card_shadow' ) ) . ';';
	$css .= '--quote-card-bg:' . $m( 'tmnhanphat_quote_card_bg' ) . ';';
	$css .= '--quote-card-padding:' . absint( $m( 'tmnhanphat_quote_card_padding' ) ) . 'px;';
	$css .= '--quote-header-color:' . $m( 'tmnhanphat_quote_header_title_color' ) . ';';
	$css .= '--quote-underline:' . $m( 'tmnhanphat_quote_underline_color' ) . ';';
	$css .= '--quote-header-size:' . absint( $m( 'tmnhanphat_quote_header_size' ) ) . 'px;';
	$css .= '--quote-input-height:' . absint( $m( 'tmnhanphat_quote_input_height' ) ) . 'px;';
	$css .= '--quote-input-radius:' . absint( $m( 'tmnhanphat_quote_input_radius' ) ) . 'px;';
	$css .= '--quote-input-size:' . absint( $m( 'tmnhanphat_quote_input_font_size' ) ) . 'px;';
	$css .= '--quote-placeholder:' . $m( 'tmnhanphat_quote_placeholder_color' ) . ';';
	$css .= '--quote-border:' . $m( 'tmnhanphat_quote_border_color' ) . ';';
	$css .= '--quote-focus:' . $m( 'tmnhanphat_quote_focus_color' ) . ';';
	$css .= '--quote-input-gap:' . absint( $m( 'tmnhanphat_quote_input_gap' ) ) . 'px;';
	$css .= '--quote-btn-bg:' . $m( 'tmnhanphat_quote_btn_bg' ) . ';';
	$css .= '--quote-btn-hover-bg:' . $m( 'tmnhanphat_quote_btn_hover_bg' ) . ';';
	$css .= '--quote-btn-text:' . $m( 'tmnhanphat_quote_btn_text_color' ) . ';';
	$css .= '--quote-btn-radius:' . absint( $m( 'tmnhanphat_quote_btn_radius' ) ) . 'px;';
	$css .= '--quote-btn-height:' . absint( $m( 'tmnhanphat_quote_btn_height' ) ) . 'px;';
	$css .= '--quote-verify-color:' . $m( 'tmnhanphat_quote_verify_color' ) . ';';
	$css .= '--quote-verify-size:' . absint( $m( 'tmnhanphat_quote_verify_size' ) ) . 'px;';
	$css .= '--quote-logo-width:' . absint( $m( 'tmnhanphat_quote_logo_width' ) ) . 'px;';
	$css .= '--quote-heading-blue:' . $m( 'tmnhanphat_quote_heading_blue_color' ) . ';';
	$css .= '--quote-heading-red:' . $m( 'tmnhanphat_quote_heading_red_color' ) . ';';
	$css .= '--quote-heading-size:' . absint( $m( 'tmnhanphat_quote_heading_size' ) ) . 'px;';
	$css .= '--quote-desc-color:' . $m( 'tmnhanphat_quote_desc_color' ) . ';';
	$css .= '--quote-desc-size:' . absint( $m( 'tmnhanphat_quote_desc_size' ) ) . 'px;';
	$css .= '--quote-right-align:' . $m( 'tmnhanphat_quote_right_align' ) . ';';
	$css .= '--quote-right-margin-x:' . ( 'center' === $m( 'tmnhanphat_quote_right_align' ) ? 'auto' : '0' ) . ';';
	$css .= '--quote-right-max-width:' . absint( $m( 'tmnhanphat_quote_right_max_width' ) ) . 'px;';
	$css .= '--quote-icon-bg:' . $m( 'tmnhanphat_quote_icon_bg' ) . ';';
	$css .= '--quote-icon-color:' . $m( 'tmnhanphat_quote_icon_color' ) . ';';
	$css .= '--quote-icon-shadow:' . tmnhanphat_get_why_choose_shadow_value( $m( 'tmnhanphat_quote_icon_shadow' ) ) . ';';
	$css .= '--quote-label-size:' . absint( $m( 'tmnhanphat_quote_label_size' ) ) . 'px;';
	$css .= '--quote-value-size:' . absint( $m( 'tmnhanphat_quote_value_size' ) ) . 'px;';
	$css .= '--quote-divider:' . $m( 'tmnhanphat_quote_divider_color' ) . ';';
	$css .= '--quote-contact-gap:' . absint( $m( 'tmnhanphat_quote_contact_gap' ) ) . 'px;';
	$css .= '}';

	$css .= '@media (max-width:991px){:root{';
	$css .= '--quote-padding:' . absint( $m( 'tmnhanphat_quote_padding_tablet' ) ) . 'px;';
	$css .= '--quote-margin:' . absint( $m( 'tmnhanphat_quote_margin_tablet' ) ) . 'px;';
	$css .= '--quote-header-size:' . absint( $m( 'tmnhanphat_quote_header_size_tablet' ) ) . 'px;';
	$css .= '}}';

	$css .= '@media (max-width:599px){:root{';
	$css .= '--quote-padding:' . absint( $m( 'tmnhanphat_quote_padding_mobile' ) ) . 'px;';
	$css .= '--quote-margin:' . absint( $m( 'tmnhanphat_quote_margin_mobile' ) ) . 'px;';
	$css .= '--quote-header-size:' . absint( $m( 'tmnhanphat_quote_header_size_mobile' ) ) . 'px;';
	$css .= '}}';

	return $css;
}

/* ==========================================================================
 * FAQ SECTION ("Câu hỏi thường gặp", trang chủ) — helper riêng. Dữ liệu 100% từ
 * CPT faq (inc/post-types.php) qua WP_Query — KHÔNG wp_posts thường/category Blog.
 * Customizer panel "FAQ Home" chỉ điều khiển hiển thị/accordion, không chứa nội dung.
 *
 * Grid 2 cột × 3 hàng = 6 FAQ (không accordion 1 cột toàn màn hình, không masonry).
 * Accordion: chỉ 1 FAQ mở cùng lúc (assets/js/components/faq.js — slide down/up),
 * FAQ có "Expand Default" đầu tiên (theo Sort Order) mở sẵn khi tải trang.
 * Chưa có FAQ nào → Demo Data (tầng hiển thị, không ghi DB — mục 3).
 * ========================================================================== */

/**
 * Default value tập trung cho toàn bộ setting của FAQ Section (mục 22).
 *
 * @return array<string, mixed>
 */
function tmnhanphat_faq_defaults() {
	return array(
		// ----- General -----
		'tmnhanphat_faq_enable'          => true,
		'tmnhanphat_faq_section_id'      => 'faq',
		'tmnhanphat_faq_container_width' => 0,
		// Figma: pad trên 76 (đáy 135 cố định ở CSS — grid → Footer).
		'tmnhanphat_faq_padding_desktop' => 56, // Migrate: nhịp dọc mới (76→56).
		'tmnhanphat_faq_padding_tablet'  => 64,
		'tmnhanphat_faq_padding_mobile'  => 48,
		'tmnhanphat_faq_margin_desktop'  => 0,
		'tmnhanphat_faq_margin_tablet'   => 0,
		'tmnhanphat_faq_margin_mobile'   => 0,
		'tmnhanphat_faq_bg_color'        => '#ffffff',
		'tmnhanphat_faq_bg_image'        => '',

		// ----- Header (reuse review-home__heading) -----
		// Figma: eyebrow 12/700 #0C4B9B; Heading 62/700 "Câu hỏi" #0C4B9B + "thường gặp"
		// #ED2429 (char-override), ls -3.41; Description 14/400 #5E6978 box 768;
		// Header → Grid cách 42.
		'tmnhanphat_faq_reuse_heading'        => true,
		'tmnhanphat_faq_small_title'          => 'HỖ TRỢ KHÁCH HÀNG',
		'tmnhanphat_faq_small_size'           => 12,
		'tmnhanphat_faq_heading_blue_text'    => 'Câu hỏi',
		'tmnhanphat_faq_heading_red_text'     => 'thường gặp',
		'tmnhanphat_faq_heading_blue_color'   => '#0C4B9B',
		'tmnhanphat_faq_heading_red_color'    => '#E31F2B', // Gom token: Figma #ED2429 → đỏ thương hiệu.
		'tmnhanphat_faq_title_size'           => 62,
		'tmnhanphat_faq_title_size_tablet'    => 36,
		'tmnhanphat_faq_title_size_mobile'    => 26,
		'tmnhanphat_faq_desc_text'            => 'Giải đáp những thắc mắc phổ biến về sản phẩm, chi phí, quy trình lắp đặt, bảo hành và sửa chữa thang máy tại Nhân Phát.',
		'tmnhanphat_faq_desc_color'           => '#667180', // Gom token: Figma #5E6978 → xám muted chung.
		'tmnhanphat_faq_desc_size'            => 14,
		'tmnhanphat_faq_desc_max_width'       => 768,
		'tmnhanphat_faq_header_align'         => 'center',
		'tmnhanphat_faq_header_margin_bottom' => 42,

		// ----- Query -----
		'tmnhanphat_faq_count'      => 6,
		'tmnhanphat_faq_orderby'    => 'sort_order',
		'tmnhanphat_faq_order'      => 'ASC',
		'tmnhanphat_faq_hide_draft' => true,
		'tmnhanphat_faq_hide_empty' => true,

		// ----- FAQ Card -----
		// Figma: grid gap cột 17 (hàng 18 ở CSS); card r16 viền #DCE4ED (mở #B4CCE1 +
		// dải trái 5px #0C4B9B), shadow tĩnh ở CSS.
		'tmnhanphat_faq_cols_desktop'         => 2,
		'tmnhanphat_faq_cols_tablet'          => 2,
		'tmnhanphat_faq_cols_mobile'          => 1,
		'tmnhanphat_faq_gap'                  => 17,
		'tmnhanphat_faq_card_radius'          => 16,
		'tmnhanphat_faq_card_padding'         => 20,
		'tmnhanphat_faq_border_width'         => 1,
		'tmnhanphat_faq_border_color'         => '#DCE4ED',
		'tmnhanphat_faq_active_border_color'  => '#0C4B9B',
		'tmnhanphat_faq_card_shadow'          => 'none',
		'tmnhanphat_faq_card_hover_shadow'    => 'soft',

		// ----- Number Badge — Figma: 42×42 r12, thường #EAF3FB chữ #0C4B9B, active
		// #ED2429 chữ trắng, số 16/700. -----
		'tmnhanphat_faq_badge_enable'   => true,
		'tmnhanphat_faq_badge_size'     => 42,
		'tmnhanphat_faq_badge_radius'   => 12,
		'tmnhanphat_faq_badge_bg'       => '#EAF3FB',
		'tmnhanphat_faq_badge_color'    => '#0C4B9B',
		'tmnhanphat_faq_badge_active_bg'    => '#E31F2B', // Gom token: Figma #ED2429 → đỏ thương hiệu.
		'tmnhanphat_faq_badge_active_color' => '#ffffff',

		// ----- Question — Figma: 24/700, thường #172437, card mở #062B69. -----
		'tmnhanphat_faq_q_size'        => 24,
		'tmnhanphat_faq_q_size_tablet' => 18,
		'tmnhanphat_faq_q_size_mobile' => 16,
		'tmnhanphat_faq_q_weight'      => '700',
		'tmnhanphat_faq_q_color'       => '#172437',
		'tmnhanphat_faq_q_hover_color' => '#062B69',

		// ----- Answer — Figma: 15/400 #5E6978 lh 136%, pad trên 14. -----
		'tmnhanphat_faq_a_size'           => 15,
		'tmnhanphat_faq_a_color'          => '#667180', // Gom token: Figma #5E6978 → xám muted chung.
		'tmnhanphat_faq_a_line_height'    => 1.36,
		'tmnhanphat_faq_a_padding_top'    => 14,
		'tmnhanphat_faq_a_padding_bottom' => 4,

		// ----- Icon — Figma: ô tròn 31×31, thường #F2F6FA nét #0C4B9B, mở #0C4B9B. -----
		'tmnhanphat_faq_icon_enable'      => true,
		'tmnhanphat_faq_icon_size'        => 16,
		'tmnhanphat_faq_circle_size'      => 31,
		'tmnhanphat_faq_circle_bg'        => '#F2F6FA',
		'tmnhanphat_faq_circle_active_bg' => '#0C4B9B',

		// ----- Accordion -----
		'tmnhanphat_faq_allow_multiple'    => false,
		'tmnhanphat_faq_expand_duration'   => 300,
		'tmnhanphat_faq_collapse_duration' => 300,
	);
}

/**
 * Đọc 1 theme_mod của FAQ với default tập trung (mục 22).
 *
 * @param string $key Setting id.
 * @return mixed
 */
function tmnhanphat_get_faq_mod( $key ) {
	$defaults = tmnhanphat_faq_defaults();
	$default  = isset( $defaults[ $key ] ) ? $defaults[ $key ] : '';

	return get_theme_mod( $key, $default );
}

/**
 * Danh sách lựa chọn "Order By".
 *
 * @return array<string, string>
 */
function tmnhanphat_get_faq_orderby_choices() {
	return array(
		'sort_order' => __( 'Sort Order (Meta Box)', 'tmnhanphat' ),
		'date'       => __( 'Ngày đăng', 'tmnhanphat' ),
		'title'      => __( 'Câu hỏi (A-Z)', 'tmnhanphat' ),
	);
}

/**
 * Whitelist "Order By".
 *
 * @param string $value Giá trị gửi lên từ Customizer.
 * @return string
 */
function tmnhanphat_sanitize_faq_orderby( $value ) {
	$choices = array_keys( tmnhanphat_get_faq_orderby_choices() );

	return in_array( $value, $choices, true ) ? $value : 'sort_order';
}

/**
 * Whitelist "Order".
 *
 * @param string $value Giá trị gửi lên từ Customizer.
 * @return string
 */
function tmnhanphat_sanitize_faq_order( $value ) {
	return in_array( $value, array( 'ASC', 'DESC' ), true ) ? $value : 'ASC';
}

/**
 * Whitelist Line Height Answer (thập phân).
 *
 * @param mixed $value Giá trị gửi lên từ Customizer.
 * @return float
 */
function tmnhanphat_sanitize_faq_line_height( $value ) {
	return max( 1, min( 3, (float) $value ) );
}

/**
 * Query danh sách FAQ theo setting Customizer — chỉ lấy đúng số cần render (mục 15).
 * "Hide FAQ" (_tmnp_faq_hide=1) bị loại qua meta_query.
 *
 * @return WP_Query
 */
function tmnhanphat_get_faq_query() {
	$orderby    = tmnhanphat_get_faq_mod( 'tmnhanphat_faq_orderby' );
	$hide_draft = tmnhanphat_get_faq_mod( 'tmnhanphat_faq_hide_draft' );

	$args = array(
		'post_type'           => 'faq',
		'post_status'         => $hide_draft ? 'publish' : array( 'publish', 'draft' ),
		'posts_per_page'      => max( 1, absint( tmnhanphat_get_faq_mod( 'tmnhanphat_faq_count' ) ) ),
		'order'               => tmnhanphat_get_faq_mod( 'tmnhanphat_faq_order' ),
		'no_found_rows'       => true,
		'ignore_sticky_posts' => true,
		'meta_query'          => array(
			'relation' => 'OR',
			array(
				'key'     => '_tmnp_faq_hide',
				'value'   => '1',
				'compare' => '!=',
			),
			array(
				'key'     => '_tmnp_faq_hide',
				'compare' => 'NOT EXISTS',
			),
		),
	);

	if ( 'sort_order' === $orderby ) {
		$args['orderby']  = 'meta_value_num';
		$args['meta_key'] = '_tmnp_faq_order'; // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key -- danh sách FAQ nhỏ, orderby theo Sort Order là tính năng chính của Meta Box.
	} else {
		$args['orderby'] = $orderby;
	}

	return new WP_Query( $args );
}

/**
 * Danh sách FAQ Demo (mục 3) — hiển thị khi CPT chưa có bài, KHÔNG ghi DB.
 *
 * @return array<int, array<string, mixed>>
 */
function tmnhanphat_get_faq_demo_items() {
	$demos = array(
		array(
			'Thời gian lắp đặt một chiếc thang máy mất bao lâu?',
			'Tùy theo từng loại thang máy và điều kiện công trình, thời gian lắp đặt thường dao động từ 15 đến 30 ngày. Đội ngũ kỹ thuật sẽ khảo sát và tư vấn chi tiết trước khi thi công.',
		),
		array(
			'Chi phí lắp đặt thang máy được tính như thế nào?',
			'Chi phí phụ thuộc vào loại thang máy, tải trọng, số điểm dừng và vật liệu hoàn thiện. Nhân Phát báo giá minh bạch, chi tiết từng hạng mục sau khi khảo sát thực tế.',
		),
		array(
			'Chế độ bảo trì thang máy ra sao?',
			'Chúng tôi cung cấp gói bảo trì định kỳ hàng tháng hoặc theo quý, kiểm tra toàn bộ hệ thống để đảm bảo thang máy vận hành an toàn và ổn định.',
		),
		array(
			'Thời gian bảo hành thang máy là bao lâu?',
			'Thang máy được bảo hành chính hãng từ 24 đến 36 tháng tùy dòng sản phẩm, kèm hỗ trợ kỹ thuật nhanh chóng trong suốt quá trình sử dụng.',
		),
		array(
			'Thang máy gia đình có những mức tải trọng nào?',
			'Thang máy gia đình phổ biến ở các mức tải 250kg, 320kg, 350kg và 450kg, phù hợp với nhiều diện tích và nhu cầu sử dụng khác nhau.',
		),
		array(
			'Quy trình lắp đặt thang máy gồm những bước nào?',
			'Quy trình gồm: tiếp nhận yêu cầu, khảo sát thực tế, tư vấn giải pháp và báo giá, thi công lắp đặt, kiểm định và bàn giao, cuối cùng là bảo trì định kỳ.',
		),
	);

	$items = array();

	foreach ( $demos as $index => $demo ) {
		$items[] = array(
			'number'   => $index + 1,
			'question' => $demo[0],
			'answer'   => wpautop( esc_html( $demo[1] ) ),
			'expand'   => 0 === $index, // FAQ đầu mở sẵn (giống design).
		);
	}

	return $items;
}

/**
 * Xây danh sách FAQ đã xử lý (số thứ tự tự đánh theo thứ tự hiển thị, Answer render
 * an toàn qua wp_kses_post). CPT trống → Demo Data (mục 3). Chỉ 1 FAQ Expand Default
 * được mở (FAQ đầu tiên theo thứ tự có _tmnp_faq_expand=1).
 *
 * @return array<int, array<string, mixed>>
 */
function tmnhanphat_get_faq_items() {
	$items      = array();
	$hide_empty = tmnhanphat_get_faq_mod( 'tmnhanphat_faq_hide_empty' );
	$query      = tmnhanphat_get_faq_query();
	$number     = 0;

	while ( $query->have_posts() ) {
		$query->the_post();

		$question = get_the_title();
		$answer   = get_the_content();

		if ( $hide_empty && '' === trim( (string) $question ) && '' === trim( wp_strip_all_tags( (string) $answer ) ) ) {
			continue; // FAQ trống hoàn toàn — bỏ qua.
		}

		++$number;

		$items[] = array(
			'number'   => $number,
			'question' => '' !== trim( (string) $question ) ? $question : __( 'Câu hỏi', 'tmnhanphat' ),
			'answer'   => wp_kses_post( wpautop( $answer ) ),
			'expand'   => '1' === get_post_meta( get_the_ID(), '_tmnp_faq_expand', true ),
		);
	}

	wp_reset_postdata();

	if ( empty( $items ) ) {
		$items = tmnhanphat_get_faq_demo_items();
	}

	// Chỉ 1 FAQ mở sẵn: giữ Expand Default ĐẦU TIÊN, tắt các cái sau (mục 2).
	$found_expanded = false;
	foreach ( $items as $index => $item ) {
		if ( $item['expand'] && ! $found_expanded ) {
			$found_expanded = true;
			continue;
		}
		$items[ $index ]['expand'] = false;
	}

	return $items;
}

/**
 * Sinh chuỗi CSS custom properties cho FAQ Section (mục 22).
 *
 * @return string
 */
function tmnhanphat_render_faq_css_vars() {
	$m = 'tmnhanphat_get_faq_mod';

	$container_width = absint( $m( 'tmnhanphat_faq_container_width' ) );
	$bg_image        = $m( 'tmnhanphat_faq_bg_image' );

	$css  = ':root{';
	$css .= '--faq-bg:' . $m( 'tmnhanphat_faq_bg_color' ) . ';';
	$css .= '--faq-bg-image:' . ( $bg_image ? 'url("' . esc_url( $bg_image ) . '")' : 'none' ) . ';';
	$css .= '--faq-container-width:' . ( $container_width > 0 ? $container_width . 'px' : 'var(--container-max-width)' ) . ';';
	$css .= '--faq-padding:' . absint( $m( 'tmnhanphat_faq_padding_desktop' ) ) . 'px;';
	$css .= '--faq-margin:' . absint( $m( 'tmnhanphat_faq_margin_desktop' ) ) . 'px;';
	$css .= '--faq-small-size:' . absint( $m( 'tmnhanphat_faq_small_size' ) ) . 'px;';
	$css .= '--faq-heading-blue:' . $m( 'tmnhanphat_faq_heading_blue_color' ) . ';';
	$css .= '--faq-heading-red:' . $m( 'tmnhanphat_faq_heading_red_color' ) . ';';
	$css .= '--faq-title-size:' . absint( $m( 'tmnhanphat_faq_title_size' ) ) . 'px;';
	$css .= '--faq-desc-color:' . $m( 'tmnhanphat_faq_desc_color' ) . ';';
	$css .= '--faq-desc-size:' . absint( $m( 'tmnhanphat_faq_desc_size' ) ) . 'px;';
	$css .= '--faq-desc-max-width:' . absint( $m( 'tmnhanphat_faq_desc_max_width' ) ) . 'px;';
	$css .= '--faq-header-align:' . $m( 'tmnhanphat_faq_header_align' ) . ';';
	$css .= '--faq-header-margin-x:' . ( 'center' === $m( 'tmnhanphat_faq_header_align' ) ? 'auto' : '0' ) . ';';
	$css .= '--faq-header-margin:' . absint( $m( 'tmnhanphat_faq_header_margin_bottom' ) ) . 'px;';
	$css .= '--faq-cols:' . max( 1, absint( $m( 'tmnhanphat_faq_cols_desktop' ) ) ) . ';';
	$css .= '--faq-gap:' . absint( $m( 'tmnhanphat_faq_gap' ) ) . 'px;';
	$css .= '--faq-card-radius:' . absint( $m( 'tmnhanphat_faq_card_radius' ) ) . 'px;';
	$css .= '--faq-card-padding:' . absint( $m( 'tmnhanphat_faq_card_padding' ) ) . 'px;';
	$css .= '--faq-border-width:' . absint( $m( 'tmnhanphat_faq_border_width' ) ) . 'px;';
	$css .= '--faq-border-color:' . $m( 'tmnhanphat_faq_border_color' ) . ';';
	$css .= '--faq-active-border:' . $m( 'tmnhanphat_faq_active_border_color' ) . ';';
	$css .= '--faq-card-shadow:' . tmnhanphat_get_why_choose_shadow_value( $m( 'tmnhanphat_faq_card_shadow' ) ) . ';';
	$css .= '--faq-card-hover-shadow:' . tmnhanphat_get_why_choose_shadow_value( $m( 'tmnhanphat_faq_card_hover_shadow' ) ) . ';';
	$css .= '--faq-badge-size:' . absint( $m( 'tmnhanphat_faq_badge_size' ) ) . 'px;';
	$css .= '--faq-badge-radius:' . absint( $m( 'tmnhanphat_faq_badge_radius' ) ) . 'px;';
	$css .= '--faq-badge-bg:' . $m( 'tmnhanphat_faq_badge_bg' ) . ';';
	$css .= '--faq-badge-color:' . $m( 'tmnhanphat_faq_badge_color' ) . ';';
	$css .= '--faq-badge-active-bg:' . $m( 'tmnhanphat_faq_badge_active_bg' ) . ';';
	$css .= '--faq-badge-active-color:' . $m( 'tmnhanphat_faq_badge_active_color' ) . ';';
	$css .= '--faq-q-size:' . absint( $m( 'tmnhanphat_faq_q_size' ) ) . 'px;';
	$css .= '--faq-q-weight:' . absint( $m( 'tmnhanphat_faq_q_weight' ) ) . ';';
	$css .= '--faq-q-color:' . $m( 'tmnhanphat_faq_q_color' ) . ';';
	$css .= '--faq-q-hover-color:' . $m( 'tmnhanphat_faq_q_hover_color' ) . ';';
	$css .= '--faq-a-size:' . absint( $m( 'tmnhanphat_faq_a_size' ) ) . 'px;';
	$css .= '--faq-a-color:' . $m( 'tmnhanphat_faq_a_color' ) . ';';
	$css .= '--faq-a-lh:' . (float) $m( 'tmnhanphat_faq_a_line_height' ) . ';';
	$css .= '--faq-a-pt:' . absint( $m( 'tmnhanphat_faq_a_padding_top' ) ) . 'px;';
	$css .= '--faq-a-pb:' . absint( $m( 'tmnhanphat_faq_a_padding_bottom' ) ) . 'px;';
	$css .= '--faq-icon-size:' . absint( $m( 'tmnhanphat_faq_icon_size' ) ) . 'px;';
	$css .= '--faq-circle-size:' . absint( $m( 'tmnhanphat_faq_circle_size' ) ) . 'px;';
	$css .= '--faq-circle-bg:' . $m( 'tmnhanphat_faq_circle_bg' ) . ';';
	$css .= '--faq-circle-active-bg:' . $m( 'tmnhanphat_faq_circle_active_bg' ) . ';';
	$css .= '--faq-expand-duration:' . absint( $m( 'tmnhanphat_faq_expand_duration' ) ) . 'ms;';
	$css .= '}';

	$css .= '@media (max-width:991px){:root{';
	$css .= '--faq-padding:' . absint( $m( 'tmnhanphat_faq_padding_tablet' ) ) . 'px;';
	$css .= '--faq-margin:' . absint( $m( 'tmnhanphat_faq_margin_tablet' ) ) . 'px;';
	$css .= '--faq-title-size:' . absint( $m( 'tmnhanphat_faq_title_size_tablet' ) ) . 'px;';
	$css .= '--faq-q-size:' . absint( $m( 'tmnhanphat_faq_q_size_tablet' ) ) . 'px;';
	$css .= '--faq-cols:' . max( 1, absint( $m( 'tmnhanphat_faq_cols_tablet' ) ) ) . ';';
	$css .= '}}';

	$css .= '@media (max-width:599px){:root{';
	$css .= '--faq-padding:' . absint( $m( 'tmnhanphat_faq_padding_mobile' ) ) . 'px;';
	$css .= '--faq-margin:' . absint( $m( 'tmnhanphat_faq_margin_mobile' ) ) . 'px;';
	$css .= '--faq-title-size:' . absint( $m( 'tmnhanphat_faq_title_size_mobile' ) ) . 'px;';
	$css .= '--faq-q-size:' . absint( $m( 'tmnhanphat_faq_q_size_mobile' ) ) . 'px;';
	$css .= '--faq-cols:' . max( 1, absint( $m( 'tmnhanphat_faq_cols_mobile' ) ) ) . ';';
	$css .= '}}';

	return $css;
}

/* ==========================================================================
 * HOMEPAGE SECTION MANAGER — bật/tắt + đổi thứ tự các section trang chủ ngay trong
 * Customizer, KHÔNG cần sửa code (PROJECT_RULES.md mục 4/22). front-page.php render
 * ĐỘNG theo kết quả tmnhanphat_get_ordered_home_sections(). Mặc định: đủ 12 section,
 * bật hết, đúng thứ tự hiện tại → không đổi gì nếu admin chưa chỉnh (mục XIV).
 *
 * Lưu ý: mỗi section vẫn giữ toggle "Enable Section" riêng trong panel của nó; Section
 * Manager là lớp gate CẤP CAO HƠN (quyết định có gọi get_template_part hay không +
 * thứ tự). Tắt ở Manager = không render; bật ở Manager nhưng tắt trong panel section =
 * section tự return sớm. Hai lớp độc lập, không phá logic cũ.
 * ========================================================================== */

/**
 * Registry các section Homepage: slug (dùng cho setting id + template path), nhãn hiển
 * thị trong Customizer, và thứ tự mặc định (khớp thứ tự front-page.php cũ).
 *
 * @return array<int, array{slug: string, template: string, label: string}>
 */
function tmnhanphat_get_home_sections() {
	return array(
		array( 'slug' => 'hero',            'template' => 'template-parts/home/hero',            'label' => __( 'Hero', 'tmnhanphat' ) ),
		array( 'slug' => 'partners',        'template' => 'template-parts/home/partners',        'label' => __( 'Logo Partner', 'tmnhanphat' ) ),
		array( 'slug' => 'about',           'template' => 'template-parts/home/about',           'label' => __( 'About', 'tmnhanphat' ) ),
		array( 'slug' => 'services',        'template' => 'template-parts/home/services',        'label' => __( 'Dịch vụ (Services)', 'tmnhanphat' ) ),
		array( 'slug' => 'why-choose',      'template' => 'template-parts/home/why-choose',      'label' => __( 'Why Choose Us', 'tmnhanphat' ) ),
		array( 'slug' => 'process',         'template' => 'template-parts/home/process',         'label' => __( 'Process', 'tmnhanphat' ) ),
		array( 'slug' => 'products',        'template' => 'template-parts/home/products',        'label' => __( 'Product', 'tmnhanphat' ) ),
		array( 'slug' => 'projects',        'template' => 'template-parts/home/projects',        'label' => __( 'Project', 'tmnhanphat' ) ),
		array( 'slug' => 'customer-review', 'template' => 'template-parts/home/customer-review', 'label' => __( 'Review', 'tmnhanphat' ) ),
		array( 'slug' => 'news',            'template' => 'template-parts/home/news',            'label' => __( 'News', 'tmnhanphat' ) ),
		array( 'slug' => 'quote',           'template' => 'template-parts/home/quote',           'label' => __( 'Contact (Quote)', 'tmnhanphat' ) ),
		array( 'slug' => 'faq',             'template' => 'template-parts/home/faq',             'label' => __( 'FAQ', 'tmnhanphat' ) ),
	);
}

/**
 * Setting id chuẩn hoá cho 1 section trong Section Manager (slug có dấu '-' → '_').
 *
 * @param string $slug   Slug section.
 * @param string $suffix 'enable' | 'order'.
 * @return string
 */
function tmnhanphat_home_section_setting_id( $slug, $suffix ) {
	return 'tmnhanphat_home_section_' . str_replace( '-', '_', $slug ) . '_' . $suffix;
}

/**
 * Danh sách section Homepage ĐÃ lọc (bật) + sắp theo Order — dùng bởi front-page.php.
 * Order rỗng/trùng: giữ ổn định theo thứ tự registry (usort ổn định trên PHP 8.0+).
 *
 * @return array<int, array<string, string>>
 */
function tmnhanphat_get_ordered_home_sections() {
	$sections = tmnhanphat_get_home_sections();
	$list     = array();

	foreach ( $sections as $index => $section ) {
		$enable_id = tmnhanphat_home_section_setting_id( $section['slug'], 'enable' );
		$order_id  = tmnhanphat_home_section_setting_id( $section['slug'], 'order' );

		if ( ! get_theme_mod( $enable_id, true ) ) {
			continue; // Tắt ở Section Manager → không render.
		}

		$section['order']    = absint( get_theme_mod( $order_id, $index + 1 ) );
		$section['_fallback'] = $index; // Giữ thứ tự gốc khi Order trùng nhau.
		$list[]              = $section;
	}

	usort(
		$list,
		static function ( $a, $b ) {
			if ( $a['order'] === $b['order'] ) {
				return $a['_fallback'] <=> $b['_fallback'];
			}
			return $a['order'] <=> $b['order'];
		}
	);

	return $list;
}
