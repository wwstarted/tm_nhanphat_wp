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
		'tmnhanphat_header_logo'                => '',
		'tmnhanphat_header_logo_retina'         => '',
		'tmnhanphat_header_logo_width'          => 160,
		'tmnhanphat_header_height'              => 96,
		'tmnhanphat_header_container_width'     => 1200,
		'tmnhanphat_header_sticky_enable'       => true,
		'tmnhanphat_header_transparent_enable'  => true,
		'tmnhanphat_header_sticky_bg'           => '#ffffff',
		'tmnhanphat_header_sticky_text'         => '#1a1a1a',
		'tmnhanphat_header_transparent_text'    => '#ffffff',
		'tmnhanphat_header_bg'                  => '#ffffff',
		'tmnhanphat_header_menu_font'           => 'inherit',
		'tmnhanphat_header_menu_font_size'      => 16,
		'tmnhanphat_header_menu_font_weight'    => '500',
		'tmnhanphat_header_menu_spacing'        => 32,
		'tmnhanphat_header_shadow'              => 'soft',
		'tmnhanphat_header_border_bottom'       => false,
		'tmnhanphat_header_transition_duration' => 250,
		'tmnhanphat_header_mobile_breakpoint'   => 992,
		'tmnhanphat_header_padding_top'         => 0,
		'tmnhanphat_header_padding_bottom'      => 0,

		// Navigation (Floating) — màu riêng cho menu khi Header đang trong suốt, độc lập
		// với tmnhanphat_header_transparent_text (màu đó vẫn giữ nguyên, chỉ dùng cho Logo/Site title).
		'tmnhanphat_header_nav_text_floating'   => '#ffffff',
		'tmnhanphat_header_nav_hover_floating'  => '#0d6efd',
		'tmnhanphat_header_nav_active_floating' => '#0d6efd',

		// Navigation (Sticky) — màu riêng cho menu khi Header đã sticky/solid.
		'tmnhanphat_header_nav_text_sticky'     => '#222222',
		'tmnhanphat_header_nav_hover_sticky'    => '#0d6efd',
		'tmnhanphat_header_nav_active_sticky'   => '#0d6efd',
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
	$css .= '--header-sticky-bg:' . $sticky_bg . ';';
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
	$css .= '}';
	$css .= '@media (min-width:' . ( $breakpoint + 1 ) . 'px){';
	$css .= '.main-navigation__toggle,.main-navigation__overlay,.main-navigation__panel-close{display:none;}';
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
		'tmnhanphat_footer_bg'               => '#f2f2f2',
		'tmnhanphat_footer_text'             => '#4a4a4a',
		'tmnhanphat_footer_link'             => '#4a4a4a',
		'tmnhanphat_footer_link_hover'       => '#cc322d',
		'tmnhanphat_footer_bottom_bg'        => '#cc322d',
		'tmnhanphat_footer_bottom_text'      => '#ffffff',
		/* translators: %s: năm hiện tại */
		'tmnhanphat_footer_copyright'        => sprintf( __( '© Copyright %s thangmaynhanphat.com All rights reserved.', 'tmnhanphat' ), gmdate( 'Y' ) ),
		'tmnhanphat_footer_logo'             => '',
		'tmnhanphat_footer_logo_width'       => 160,
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
		'tmnhanphat_footer_padding'          => 64,
		'tmnhanphat_footer_container_width'  => 1200,
		'tmnhanphat_footer_heading_color'    => '#1a1a1a',
		'tmnhanphat_footer_divider_color'    => '#e0e0e0',
		'tmnhanphat_footer_logo_spacing'     => 24,
		'tmnhanphat_footer_column_gap'       => 40,
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

		// Overlay
		'tmnhanphat_hero_overlay_enable'       => true,
		'tmnhanphat_hero_overlay_color'        => '#000000',
		'tmnhanphat_hero_overlay_opacity'      => 45,
		'tmnhanphat_hero_overlay_blend'        => 'normal',

		// Subtitle
		'tmnhanphat_hero_subtitle_enable'      => true,
		'tmnhanphat_hero_subtitle_text'        => 'Lắp đặt • Sửa chữa • Bảo trì',
		'tmnhanphat_hero_subtitle_color'       => '#ffffff',
		'tmnhanphat_hero_subtitle_font_size'   => 18,
		'tmnhanphat_hero_subtitle_font_weight' => '500',

		// Heading
		'tmnhanphat_hero_heading_text'         => 'Giải pháp thang máy toàn diện cho mọi công trình',
		'tmnhanphat_hero_heading_color'        => '#ffffff',
		'tmnhanphat_hero_heading_font_size'    => 48,
		'tmnhanphat_hero_heading_font_weight'  => '800',

		// Description
		'tmnhanphat_hero_description_text'         => 'Thang Máy Nhân Phát mang đến giải pháp thang máy gia đình an toàn, hiện đại và phù hợp với từng không gian sống. Chúng tôi đồng hành từ tư vấn, thiết kế, lắp đặt đến bảo trì, giúp mỗi công trình có hệ thống thang máy bền đẹp, vận hành ổn định và tối ưu chi phí.',
		'tmnhanphat_hero_description_color'        => '#e5e5e5',
		'tmnhanphat_hero_description_font_size'    => 16,
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
		'tmnhanphat_hero_btn_primary_bg'         => '#cc322d',
		'tmnhanphat_hero_btn_primary_text_color' => '#ffffff',
		'tmnhanphat_hero_btn_primary_hover_bg'   => '#a8281f',
		'tmnhanphat_hero_btn_secondary_bg'         => '#0d6efd',
		'tmnhanphat_hero_btn_secondary_text_color' => '#ffffff',
		'tmnhanphat_hero_btn_secondary_hover_bg'   => '#0b5ed7',
		'tmnhanphat_hero_btn_border_radius'    => 6,
		'tmnhanphat_hero_btn_padding_x'        => 28,
		'tmnhanphat_hero_btn_padding_y'        => 14,

		// Layout
		'tmnhanphat_hero_content_max_width'        => 720,
		'tmnhanphat_hero_content_max_width_tablet' => 600,
		'tmnhanphat_hero_content_max_width_mobile' => 600,
		'tmnhanphat_hero_content_align'            => 'center',
		'tmnhanphat_hero_height_desktop'           => 100,
		'tmnhanphat_hero_padding_tablet'           => 100,
		'tmnhanphat_hero_padding_mobile'           => 90,

		// Hero Stats Card
		'tmnhanphat_hero_stats_card_enable'       => true,
		'tmnhanphat_hero_stats_card_bg'           => '#ffffff',
		'tmnhanphat_hero_stats_card_opacity'      => 78,
		'tmnhanphat_hero_stats_card_radius'       => 24,
		'tmnhanphat_hero_stats_card_border_color' => '#ffffff',
		'tmnhanphat_hero_stats_card_border_width' => 1,
		'tmnhanphat_hero_stats_card_blur'         => 18,
		'tmnhanphat_hero_stats_card_shadow'       => 'soft',

		// Hero Spacing (desktop)
		'tmnhanphat_hero_padding_top'              => 100,
		'tmnhanphat_hero_padding_bottom'           => 40,
		'tmnhanphat_hero_heading_margin_bottom'    => 16,
		'tmnhanphat_hero_description_margin_bottom' => 20,
		'tmnhanphat_hero_cta_margin_bottom'        => 24,
		'tmnhanphat_hero_stats_margin_top'         => 32,

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
	$css .= '--hero-stats-card-border:' . ( $card_enable ? $card_border_width . 'px solid ' . $card_border_color : 'none' ) . ';';
	$css .= '--hero-stats-card-blur:' . ( $card_enable ? $card_blur : 0 ) . 'px;';
	$css .= '--hero-stats-card-shadow:' . ( $card_enable ? $card_shadow : 'none' ) . ';';
	// Card sáng (mặc định) cần chữ tối để đọc được; khi tắt Card, Stats nổi trực tiếp trên
	// ảnh nền tối như trước nên cần đổi lại về chữ trắng — không thể "if" trong CSS thuần
	// nên tính sẵn giá trị cuối cùng ở PHP theo trạng thái Enable Background Card.
	$css .= '--hero-stats-number-color:' . ( $card_enable ? '#1a1a1a' : '#ffffff' ) . ';';
	$css .= '--hero-stats-desc-color:' . ( $card_enable ? '#5c5c5c' : 'rgb(255 255 255 / 0.75)' ) . ';';
	$css .= '--hero-stats-badge-bg:' . ( $card_enable ? $btn_secondary_bg : 'rgb(255 255 255 / 0.15)' ) . ';';
	$css .= '--hero-stats-badge-text:' . ( $card_enable ? $btn_secondary_text : '#ffffff' ) . ';';
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
		'tmnhanphat_partners_enable'             => true,
		'tmnhanphat_partners_title_enable'       => false,
		'tmnhanphat_partners_title'              => __( 'Đối tác đồng hành', 'tmnhanphat' ),
		'tmnhanphat_partners_description_enable' => false,
		'tmnhanphat_partners_description'        => '',
		'tmnhanphat_partners_padding_desktop'    => 64,
		'tmnhanphat_partners_padding_tablet'     => 48,
		'tmnhanphat_partners_padding_mobile'     => 32,
		'tmnhanphat_partners_bg'                 => '#ffffff',

		// Display Settings — giới hạn SỐ LƯỢNG Partner render, KHÔNG liên quan số cột.
		'tmnhanphat_partners_display_limit' => 6,

		// Layout — CHỈ quyết định số cột hiển thị, KHÔNG quyết định số Partner.
		'tmnhanphat_partners_columns_desktop' => 6,
		'tmnhanphat_partners_columns_tablet'  => 3,
		'tmnhanphat_partners_columns_mobile'  => 2,
		'tmnhanphat_partners_gap_desktop'     => 24,
		'tmnhanphat_partners_gap_tablet'      => 16,
		'tmnhanphat_partners_gap_mobile'      => 12,
		'tmnhanphat_partners_card_radius'     => 14,
		'tmnhanphat_partners_card_padding'    => 20,
		'tmnhanphat_partners_card_border'     => '#e8e8e8',
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
		'tmnhanphat_container_width'            => 1200,
		'tmnhanphat_container_padding_desktop' => 24,
		'tmnhanphat_container_padding_tablet'  => 20,
		'tmnhanphat_container_padding_mobile'  => 16,
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

	$css  = ':root{--container-max-width:' . $container_width . 'px;--container-padding:' . $padding_desktop . 'px;}';
	$css .= '@media (max-width:991px){:root{--container-padding:' . $padding_tablet . 'px;}}';
	$css .= '@media (max-width:599px){:root{--container-padding:' . $padding_mobile . 'px;}}';

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
		'tmnhanphat_about_text_width'              => 42,
		'tmnhanphat_about_description_max_width'   => 420,
		'tmnhanphat_about_vertical_align'          => 'center',

		// Background Shape — Upload Shape Image (Rectangle 19.png) làm background-image cho
		// .about-home__shape (thuần decoration, KHÔNG phải container). Fallback Background
		// Color hiển thị khi chưa upload ảnh, tránh section trống trơn không có gì.
		'tmnhanphat_about_bg_color'          => '#cc322d',
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
		'tmnhanphat_about_image'               => '',
		'tmnhanphat_about_image_width_desktop' => 86,
		'tmnhanphat_about_image_width_tablet'  => 75,
		'tmnhanphat_about_image_width_mobile'  => 60,
		'tmnhanphat_about_image_offset_x'      => -20,
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
	$css .= '--about-background:' . $bg_color . ';';
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
