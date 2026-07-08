<?php
/**
 * Pagination component logic. Markup thực tế render ở template-parts/components/pagination.php.
 *
 * @package TMNhanPhat
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Trả về mảng link phân trang cho vòng lặp hiện tại (dùng paginate_links).
 *
 * @param WP_Query|null $query Query cần phân trang, mặc định dùng global $wp_query.
 * @return string[] Mảng HTML link/span đã escape bởi WordPress core.
 */
function tmnhanphat_get_pagination_links( $query = null ) {
	global $wp_query;
	$query = $query ? $query : $wp_query;

	$total = $query->max_num_pages;
	if ( $total <= 1 ) {
		return array();
	}

	$current = max( 1, get_query_var( 'paged' ) ? get_query_var( 'paged' ) : get_query_var( 'page' ) );

	return paginate_links( array(
		'total'     => $total,
		'current'   => $current,
		'mid_size'  => 2,
		'end_size'  => 1,
		'prev_text' => __( '&laquo; Trước', 'tmnhanphat' ),
		'next_text' => __( 'Sau &raquo;', 'tmnhanphat' ),
		'type'      => 'array',
	) );
}
