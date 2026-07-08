<?php
/**
 * Đăng ký vị trí menu và các tuỳ chỉnh liên quan đến wp_nav_menu.
 *
 * @package TMNhanPhat
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Đăng ký nav menu locations.
 */
function tmnhanphat_register_menus() {
	register_nav_menus( array(
		'primary'        => __( 'Menu chính (Header)', 'tmnhanphat' ),
		'footer_company' => __( 'Footer - Cột công ty', 'tmnhanphat' ),
		'footer_policy'  => __( 'Footer - Cột chính sách', 'tmnhanphat' ),
		'footer_product' => __( 'Footer - Cột sản phẩm', 'tmnhanphat' ),
		'footer_service' => __( 'Footer - Cột dịch vụ', 'tmnhanphat' ),
	) );
}
add_action( 'after_setup_theme', 'tmnhanphat_register_menus' );

/**
 * Ép container=false cho mọi wp_nav_menu() — theme tự bọc <nav> ở template-parts,
 * không cần WordPress render thêm div bọc ngoài.
 *
 * @param array $args Tham số wp_nav_menu.
 * @return array
 */
function tmnhanphat_nav_menu_args( $args ) {
	$args['container'] = false;

	return $args;
}
add_filter( 'wp_nav_menu_args', 'tmnhanphat_nav_menu_args' );

/**
 * Fallback khi menu location "primary" chưa được gán menu trong Appearance → Menus.
 * Render danh sách Page cấp 1 với đúng id/class "primary-menu" để CSS/JS (dropdown,
 * offcanvas) vẫn hoạt động bình thường dù admin chưa tạo menu.
 */
function tmnhanphat_primary_menu_fallback() {
	echo '<ul id="primary-menu" class="primary-menu">';
	wp_list_pages( array(
		'title_li' => '',
		'depth'    => 1,
	) );
	echo '</ul>';
}

/**
 * Chèn nút toggle (mũi tên) ngay sau <a> của menu item cấp 1 có submenu, phục vụ
 * mở/đóng dropdown bằng cảm ứng hoặc bàn phím trong offcanvas mobile — không ảnh
 * hưởng hover dropdown trên desktop (CSS xử lý riêng qua :hover/:focus-within).
 *
 * @param string   $item_output Markup <a> đã render cho menu item.
 * @param WP_Post  $item        Đối tượng menu item.
 * @param int      $depth       Cấp độ lồng nhau (0 = cấp 1).
 * @param stdClass $args        Tham số truyền vào wp_nav_menu().
 * @return string
 */
function tmnhanphat_add_submenu_toggle( $item_output, $item, $depth, $args ) {
	if ( 0 !== $depth || empty( $args->theme_location ) || 'primary' !== $args->theme_location ) {
		return $item_output;
	}

	if ( empty( $item->classes ) || ! in_array( 'menu-item-has-children', $item->classes, true ) ) {
		return $item_output;
	}

	$toggle  = '<button type="button" class="submenu-toggle" aria-expanded="false" aria-label="' . esc_attr__( 'Mở menu con', 'tmnhanphat' ) . '">';
	$toggle .= '<span class="submenu-toggle-icon" aria-hidden="true"></span>';
	$toggle .= '</button>';

	return $item_output . $toggle;
}
add_filter( 'walker_nav_menu_start_el', 'tmnhanphat_add_submenu_toggle', 10, 4 );

/**
 * Fallback dùng chung cho cả 4 vị trí menu Footer (footer_company/policy/product/service)
 * khi admin chưa tạo Menu tương ứng trong Appearance → Menus — hiển thị đúng danh sách mặc
 * định theo thiết kế (tmnhanphat_get_footer_menu_fallback_items() trong inc/template-functions.php).
 * Một hàm duy nhất dùng cho cả 4 cột (DRY) nhờ key "tmnhanphat_column" truyền qua $args
 * của wp_nav_menu() — xem template-parts/footer/column-links.php.
 *
 * @param array $args Tham số đã truyền vào wp_nav_menu(), gồm key tuỳ biến "tmnhanphat_column".
 */
function tmnhanphat_footer_menu_fallback( $args ) {
	$column = isset( $args['tmnhanphat_column'] ) ? $args['tmnhanphat_column'] : '';
	$items  = tmnhanphat_get_footer_menu_fallback_items( $column );

	if ( empty( $items ) ) {
		return;
	}

	echo '<ul class="footer-menu">';
	foreach ( $items as $item ) {
		printf( '<li><a href="%s">%s</a></li>', esc_url( $item['url'] ), esc_html( $item['label'] ) );
	}
	echo '</ul>';
}
