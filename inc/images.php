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
	// Logo đối tác: KHÔNG crop (false) — chỉ resize vừa khung, giữ nguyên tỉ lệ gốc,
	// tránh méo/kéo giãn logo thương hiệu (PROJECT_RULES.md — Partners Section).
	add_image_size( 'tmnhanphat-partner-logo', 240, 120, false );
	// Ảnh thang máy About Section: KHÔNG crop (false) — chỉ resize vừa khung tối đa, giữ
	// nguyên tỉ lệ gốc, không méo/kéo giãn (PROJECT_RULES.md — About Company Section).
	add_image_size( 'tmnhanphat-about-image', 800, 900, false );
	// Card dịch vụ (Services Home Section): crop đúng khung 510×453 theo Figma — card dùng
	// object-fit:cover nên crop từ server giúp tải đúng kích thước cần, không tải ảnh gốc thừa.
	add_image_size( 'tmnhanphat-service-card', 510, 453, true );
	// Card sản phẩm (Products Home Section): crop center đúng khung 438×403 theo Figma —
	// không méo ảnh, tải đúng kích thước cần (PROJECT_RULES.md mục 13 & 15).
	add_image_size( 'tmnhanphat-product-card', 438, 403, true );
	// Ảnh Feature Item (Why Choose Section): KHÔNG crop (false) — khung 423×290 theo Figma
	// nhưng card dùng object-fit:contain (ảnh minh hoạ nền trong suốt), giữ nguyên tỉ lệ gốc.
	add_image_size( 'tmnhanphat-why-choose', 423, 290, false );
	// Thumbnail Dự án (Featured Projects Section): crop center 600×440 — khung cố định
	// dùng object-fit:cover, tải đúng kích thước cần (mục 13 & 15).
	add_image_size( 'tmnhanphat-project-card', 600, 440, true );
	// Avatar Customer Review (CPT customer_review): crop center vuông nhỏ, đủ cho khung
	// tròn 24-80px trong Customizer — không cần tải ảnh gốc lớn cho 1 icon nhỏ.
	add_image_size( 'tmnhanphat-review-avatar', 120, 120, true );
	// Thumbnail Tin tức (Featured News Section): crop center ~513×340 theo Figma card
	// 513px, dùng object-fit:cover — tải đúng kích thước, không méo (mục 13 & 15).
	add_image_size( 'tmnhanphat-news-card', 520, 340, true );
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
		'tmnhanphat-card'          => __( 'Thẻ bài viết', 'tmnhanphat' ),
		'tmnhanphat-hero'          => __( 'Ảnh Hero', 'tmnhanphat' ),
		'tmnhanphat-partner-logo'  => __( 'Logo đối tác', 'tmnhanphat' ),
		'tmnhanphat-about-image'   => __( 'Ảnh About Company', 'tmnhanphat' ),
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
