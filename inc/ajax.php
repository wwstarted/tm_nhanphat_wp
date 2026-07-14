<?php
/**
 * AJAX handlers của theme. Mỗi handler mới thêm vào đây, tuyệt đối không tạo file ajax riêng lẻ
 * ở nơi khác (tránh trùng vai trò — xem PROJECT_RULES.md mục 9).
 *
 * @package TMNhanPhat
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Mẫu handler "Load more posts" cho archive/front-page (kèm nonce + sanitize input).
 */
function tmnhanphat_ajax_load_more_posts() {
	tmnhanphat_verify_ajax_request();

	$paged = isset( $_POST['page'] ) ? absint( $_POST['page'] ) : 1;

	$query = new WP_Query( array(
		'post_type'      => 'post',
		'paged'          => $paged,
		'posts_per_page' => get_option( 'posts_per_page' ),
	) );

	ob_start();
	if ( $query->have_posts() ) {
		while ( $query->have_posts() ) {
			$query->the_post();
			get_template_part( 'template-parts/components/post-card' );
		}
		wp_reset_postdata();
	}
	$html = ob_get_clean();

	wp_send_json_success( array(
		'html'     => $html,
		'has_more' => $paged < $query->max_num_pages,
	) );
}
add_action( 'wp_ajax_tmnhanphat_load_more_posts', 'tmnhanphat_ajax_load_more_posts' );
add_action( 'wp_ajax_nopriv_tmnhanphat_load_more_posts', 'tmnhanphat_ajax_load_more_posts' );

/**
 * Xác thực nonce chung cho mọi AJAX request của theme, chặn ngay nếu không hợp lệ.
 */
function tmnhanphat_verify_ajax_request() {
	$nonce = isset( $_POST['nonce'] ) ? sanitize_key( wp_unslash( $_POST['nonce'] ) ) : '';

	if ( ! tmnhanphat_verify_nonce( $nonce, 'tmnhanphat_ajax_nonce' ) ) {
		wp_send_json_error( array( 'message' => __( 'Yêu cầu không hợp lệ.', 'tmnhanphat' ) ), 403 );
	}
}

/**
 * Lọc danh sách sản phẩm theo Sub Category cho Products Home Section — trả về HTML các
 * product-card (đúng component template-parts/components/product-card.php dùng khi render
 * lần đầu, không lặp markup). Term ID được xác thực phải thuộc cây Category cha "Sản phẩm"
 * (tmnhanphat_is_valid_products_term) — không cho query category tuỳ ý (mục 18).
 *
 * Fallback không JS: tab là link thẳng tới archive của category, handler này không cần chạy.
 */
function tmnhanphat_ajax_filter_products() {
	tmnhanphat_verify_ajax_request();

	$term_id = isset( $_POST['term_id'] ) ? absint( $_POST['term_id'] ) : 0;

	if ( ! tmnhanphat_is_valid_products_term( $term_id ) ) {
		wp_send_json_error( array( 'message' => __( 'Danh mục không hợp lệ.', 'tmnhanphat' ) ), 400 );
	}

	$query = tmnhanphat_get_products_query( $term_id );

	ob_start();
	if ( $query->have_posts() ) {
		while ( $query->have_posts() ) {
			$query->the_post();
			get_template_part( 'template-parts/components/product-card' );
		}
		wp_reset_postdata();
	}
	$html = ob_get_clean();

	wp_send_json_success( array(
		'html'  => $html,
		'count' => (int) $query->post_count,
	) );
}
add_action( 'wp_ajax_tmnhanphat_filter_products', 'tmnhanphat_ajax_filter_products' );
add_action( 'wp_ajax_nopriv_tmnhanphat_filter_products', 'tmnhanphat_ajax_filter_products' );
