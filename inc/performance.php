<?php
/**
 * Tối ưu hiệu năng / Core Web Vitals — xem PROJECT_RULES.md mục 15.
 *
 * @package TMNhanPhat
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Thêm defer cho JS của theme (không đụng tới script trong admin hoặc script có phụ thuộc đặc biệt).
 *
 * @param string $tag    Thẻ <script> gốc.
 * @param string $handle Handle của script.
 * @return string
 */
function tmnhanphat_defer_scripts( $tag, $handle ) {
	$defer_handles = apply_filters( 'tmnhanphat_defer_script_handles', array(
		'tmnhanphat-helpers',
		'tmnhanphat-app',
		'tmnhanphat-header-js',
		'tmnhanphat-mobile-menu',
		'tmnhanphat-single',
	) );

	if ( in_array( $handle, $defer_handles, true ) && false === strpos( $tag, 'defer' ) ) {
		$tag = str_replace( ' src=', ' defer src=', $tag );
	}

	return $tag;
}
add_filter( 'script_loader_tag', 'tmnhanphat_defer_scripts', 10, 2 );

/**
 * Giới hạn số revision bài viết để giảm dung lượng database (không ảnh hưởng frontend
 * nhưng gián tiếp giúp truy vấn admin nhanh hơn).
 */
if ( ! defined( 'WP_POST_REVISIONS' ) ) {
	define( 'WP_POST_REVISIONS', 5 );
}
