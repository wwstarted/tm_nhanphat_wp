<?php
/**
 * Custom image sizes và tối ưu ảnh — xem PROJECT_RULES.md mục 13 & 15.
 *
 * @package TMNhanPhat
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Khai báo các kích thước ảnh dùng trong theme (post-card, hero...).
 */
function tmnhanphat_image_sizes() {
	add_image_size( 'tmnhanphat-card', 480, 320, true );
	add_image_size( 'tmnhanphat-hero', 1600, 900, true );
	add_image_size( 'tmnhanphat-thumb', 150, 150, true );
}
add_action( 'after_setup_theme', 'tmnhanphat_image_sizes' );

/**
 * Hiển thị các kích thước ảnh tuỳ chỉnh trong bộ chọn ảnh của editor.
 *
 * @param array $sizes Danh sách size mặc định.
 * @return array
 */
function tmnhanphat_custom_image_sizes_names( $sizes ) {
	return array_merge( $sizes, array(
		'tmnhanphat-card' => __( 'Thẻ bài viết', 'tmnhanphat' ),
		'tmnhanphat-hero' => __( 'Ảnh Hero', 'tmnhanphat' ),
	) );
}
add_filter( 'image_size_names_choose', 'tmnhanphat_custom_image_sizes_names' );

/**
 * Đảm bảo mọi ảnh nội dung đều lazy-load (Core Web Vitals), trừ ảnh above-the-fold
 * do template tự set loading="eager" khi cần.
 *
 * @param string $attr Chuỗi HTML attribute.
 * @param mixed  $attachment Không dùng.
 * @param mixed  $size Không dùng.
 * @return array
 */
function tmnhanphat_image_attributes( $attr ) {
	if ( ! isset( $attr['loading'] ) ) {
		$attr['loading'] = 'lazy';
	}
	$attr['decoding'] = 'async';

	return $attr;
}
add_filter( 'wp_get_attachment_image_attributes', 'tmnhanphat_image_attributes' );
