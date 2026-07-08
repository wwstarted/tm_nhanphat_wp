<?php
/**
 * Security hardening cơ bản — xem PROJECT_RULES.md mục 18.
 *
 * @package TMNhanPhat
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Ẩn số phiên bản WordPress khỏi <head>, RSS feed, script/style query string.
 */
remove_action( 'wp_head', 'wp_generator' );
add_filter( 'the_generator', '__return_empty_string' );

/**
 * Chặn liệt kê user qua ?author=1 (user enumeration) trên frontend.
 */
function tmnhanphat_block_author_enum() {
	if ( is_admin() ) {
		return;
	}

	if ( isset( $_GET['author'] ) && ! is_user_logged_in() ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		wp_safe_redirect( home_url( '/' ), 301 );
		exit;
	}
}
add_action( 'template_redirect', 'tmnhanphat_block_author_enum' );

/**
 * Tắt XML-RPC pingback để giảm bề mặt tấn công DDoS/pingback.
 *
 * @param array $methods Danh sách XML-RPC methods.
 * @return array
 */
function tmnhanphat_disable_xmlrpc_pingback( $methods ) {
	unset( $methods['pingback.ping'] );
	return $methods;
}
add_filter( 'xmlrpc_methods', 'tmnhanphat_disable_xmlrpc_pingback' );

/**
 * Helper verify nonce dùng chung cho AJAX/form (xem inc/ajax.php).
 *
 * @param string $nonce  Giá trị nonce nhận được.
 * @param string $action Tên action đã dùng khi tạo nonce.
 * @return bool
 */
function tmnhanphat_verify_nonce( $nonce, $action ) {
	return (bool) wp_verify_nonce( sanitize_key( $nonce ), $action );
}
