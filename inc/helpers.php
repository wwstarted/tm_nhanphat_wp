<?php
/**
 * Các hàm dùng chung nhiều nơi trong theme — xem PROJECT_RULES.md mục 12.
 *
 * @package TMNhanPhat
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Ước tính thời gian đọc bài viết (dùng cho single/related-posts/post-card).
 *
 * @param int $post_id ID bài viết.
 * @return int Số phút đọc (tối thiểu 1).
 */
function tmnhanphat_reading_time( $post_id ) {
	$content     = get_post_field( 'post_content', $post_id );
	$word_count  = str_word_count( wp_strip_all_tags( $content ) );
	$minutes     = (int) ceil( $word_count / 200 );

	return max( 1, $minutes );
}

/**
 * Lấy excerpt an toàn với độ dài tùy chỉnh, không phụ thuộc filter global.
 *
 * @param int $post_id ID bài viết.
 * @param int $length  Số từ tối đa.
 * @return string Excerpt đã escape.
 */
function tmnhanphat_get_excerpt( $post_id, $length = 25 ) {
	$post = get_post( $post_id );

	if ( ! $post ) {
		return '';
	}

	$text = $post->post_excerpt ? $post->post_excerpt : $post->post_content;
	$text = wp_strip_all_tags( strip_shortcodes( $text ) );

	return esc_html( wp_trim_words( $text, $length, '…' ) );
}

/**
 * Kiểm tra theme hiện có đang chạy chế độ phát triển (WP_DEBUG) hay không.
 * Dùng trong inc/enqueue.php để quyết định đọc manifest.json hay dùng file dev không hash.
 *
 * @return bool
 */
function tmnhanphat_is_dev_mode() {
	return defined( 'WP_DEBUG' ) && WP_DEBUG;
}

/**
 * Gắn id="" (slug từ nội dung heading) cho mọi thẻ H2 trong the_content(),
 * để template-parts/single/toc.php có thể liên kết #anchor tới đúng vị trí.
 *
 * @param string $content Nội dung bài viết.
 * @return string
 */
function tmnhanphat_add_heading_anchors( $content ) {
	if ( ! is_singular( 'post' ) || ! in_the_loop() || ! is_main_query() ) {
		return $content;
	}

	return preg_replace_callback(
		'/<h2([^>]*)>(.*?)<\/h2>/i',
		function ( $matches ) {
			$slug = sanitize_title( wp_strip_all_tags( $matches[2] ) );
			return '<h2' . $matches[1] . ' id="' . esc_attr( $slug ) . '">' . $matches[2] . '</h2>';
		},
		$content
	);
}
add_filter( 'the_content', 'tmnhanphat_add_heading_anchors' );
