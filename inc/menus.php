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
		'primary' => __( 'Menu chính', 'tmnhanphat' ),
		'footer'  => __( 'Menu footer', 'tmnhanphat' ),
	) );
}
add_action( 'after_setup_theme', 'tmnhanphat_register_menus' );

/**
 * Thêm class Bootstrap-free, thuần semantic cho menu container.
 *
 * @param array $args Tham số wp_nav_menu.
 * @return array
 */
function tmnhanphat_nav_menu_args( $args ) {
	$args['container']  = false;
	$args['fallback_cb'] = false;

	return $args;
}
add_filter( 'wp_nav_menu_args', 'tmnhanphat_nav_menu_args' );
