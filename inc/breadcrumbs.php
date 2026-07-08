<?php
/**
 * Breadcrumb component logic — chỉ xây danh sách item để hiển thị HTML (vị trí điều hướng).
 * Markup thực tế render ở template-parts/components/breadcrumb.php.
 *
 * Theme KHÔNG in Breadcrumb Schema (JSON-LD) — dữ liệu SEO/structured data là trách nhiệm
 * riêng của Plugin SEO (Rank Math/Yoast/...), xem PROJECT_RULES.md mục 23.
 *
 * @package TMNhanPhat
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Xây danh sách breadcrumb items cho trang hiện tại.
 *
 * @return array<int, array{label: string, url: string}> Item cuối cùng có url rỗng (trang hiện tại).
 */
function tmnhanphat_get_breadcrumb_items() {
	$items = array(
		array(
			'label' => __( 'Trang chủ', 'tmnhanphat' ),
			'url'   => home_url( '/' ),
		),
	);

	if ( is_singular( 'post' ) ) {
		$categories = get_the_category();
		if ( ! empty( $categories ) ) {
			$items[] = array(
				'label' => $categories[0]->name,
				'url'   => get_category_link( $categories[0]->term_id ),
			);
		}
		$items[] = array( 'label' => get_the_title(), 'url' => '' );
	} elseif ( is_page() ) {
		$items[] = array( 'label' => get_the_title(), 'url' => '' );
	} elseif ( is_category() || is_tag() || is_tax() ) {
		$items[] = array( 'label' => single_term_title( '', false ), 'url' => '' );
	} elseif ( is_search() ) {
		/* translators: %s: search query */
		$items[] = array( 'label' => sprintf( __( 'Kết quả tìm kiếm cho: %s', 'tmnhanphat' ), get_search_query() ), 'url' => '' );
	} elseif ( is_404() ) {
		$items[] = array( 'label' => __( 'Không tìm thấy trang', 'tmnhanphat' ), 'url' => '' );
	} elseif ( is_archive() ) {
		$items[] = array( 'label' => wp_strip_all_tags( get_the_archive_title() ), 'url' => '' );
	}

	return apply_filters( 'tmnhanphat_breadcrumb_items', $items );
}
