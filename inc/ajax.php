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

/**
 * Gửi form Quote Contact (Homepage) — hoàn thiện backend cho mục 15: nhận dữ liệu đã
 * validate Frontend (assets/js/components/quote.js), validate LẠI phía server (không tin
 * client), rồi gửi mail về admin_email qua wp_mail().
 *
 * Validation server mirror đúng luật Frontend: field bắt buộc theo Customizer setting
 * tmnhanphat_quote_required + chỉ áp dụng với field đang bật (show_*); phone/email kiểm tra
 * định dạng; service phải nằm trong danh sách tmnhanphat_get_quote_service_options().
 */
function tmnhanphat_ajax_submit_quote() {
	tmnhanphat_verify_ajax_request();

	$required = (bool) tmnhanphat_get_quote_mod( 'tmnhanphat_quote_required' );

	$name    = isset( $_POST['quote_name'] ) ? sanitize_text_field( wp_unslash( $_POST['quote_name'] ) ) : '';
	$phone   = isset( $_POST['quote_phone'] ) ? sanitize_text_field( wp_unslash( $_POST['quote_phone'] ) ) : '';
	$email   = isset( $_POST['quote_email'] ) ? sanitize_email( wp_unslash( $_POST['quote_email'] ) ) : '';
	$service = isset( $_POST['quote_service'] ) ? sanitize_text_field( wp_unslash( $_POST['quote_service'] ) ) : '';
	$message = isset( $_POST['quote_message'] ) ? sanitize_textarea_field( wp_unslash( $_POST['quote_message'] ) ) : '';

	$errors = array();

	if ( $required ) {
		if ( tmnhanphat_get_quote_mod( 'tmnhanphat_quote_show_name' ) && '' === $name ) {
			$errors['quote_name'] = __( 'Vui lòng điền thông tin này.', 'tmnhanphat' );
		}
		if ( tmnhanphat_get_quote_mod( 'tmnhanphat_quote_show_phone' ) && '' === $phone ) {
			$errors['quote_phone'] = __( 'Vui lòng điền thông tin này.', 'tmnhanphat' );
		}
		if ( tmnhanphat_get_quote_mod( 'tmnhanphat_quote_show_email' ) && '' === $email ) {
			$errors['quote_email'] = __( 'Vui lòng điền thông tin này.', 'tmnhanphat' );
		}
		if ( tmnhanphat_get_quote_mod( 'tmnhanphat_quote_show_service' ) && '' === $service ) {
			$errors['quote_service'] = __( 'Vui lòng chọn loại dịch vụ.', 'tmnhanphat' );
		}
		if ( tmnhanphat_get_quote_mod( 'tmnhanphat_quote_show_message' ) && '' === $message ) {
			$errors['quote_message'] = __( 'Vui lòng điền thông tin này.', 'tmnhanphat' );
		}
	}

	// Định dạng chỉ kiểm tra khi có giá trị (field không bắt buộc + bỏ trống = hợp lệ).
	if ( '' !== $phone && ! preg_match( '/^[0-9()+\-.\s]{8,15}$/', $phone ) ) {
		$errors['quote_phone'] = __( 'Số điện thoại không hợp lệ.', 'tmnhanphat' );
	}
	if ( '' !== $email && ! is_email( $email ) ) {
		$errors['quote_email'] = __( 'Email không hợp lệ.', 'tmnhanphat' );
	}
	if ( '' !== $service && ! in_array( $service, tmnhanphat_get_quote_service_options(), true ) ) {
		$errors['quote_service'] = __( 'Danh mục dịch vụ không hợp lệ.', 'tmnhanphat' );
	}

	if ( ! empty( $errors ) ) {
		wp_send_json_error( array(
			'message' => __( 'Vui lòng kiểm tra lại thông tin đã nhập.', 'tmnhanphat' ),
			'errors'  => $errors,
		), 400 );
	}

	$to      = get_option( 'admin_email' );
	$subject = sprintf(
		/* translators: 1: site name, 2: customer name. */
		__( '[%1$s] Yêu cầu báo giá mới từ %2$s', 'tmnhanphat' ),
		get_bloginfo( 'name' ),
		'' !== $name ? $name : __( 'khách truy cập', 'tmnhanphat' )
	);

	$body_lines = array(
		__( 'Họ và tên', 'tmnhanphat' ) . ': ' . $name,
		__( 'Số điện thoại', 'tmnhanphat' ) . ': ' . $phone,
		__( 'Email', 'tmnhanphat' ) . ': ' . $email,
		__( 'Loại dịch vụ', 'tmnhanphat' ) . ': ' . $service,
		__( 'Nội dung yêu cầu', 'tmnhanphat' ) . ':',
		$message,
		'',
		__( 'Gửi từ form Báo giá trang chủ', 'tmnhanphat' ) . ' — ' . home_url( '/' ),
	);

	$headers = array( 'Content-Type: text/plain; charset=UTF-8' );
	if ( '' !== $email ) {
		$headers[] = 'Reply-To: ' . ( '' !== $name ? $name . ' <' . $email . '>' : $email );
	}

	$sent = wp_mail( $to, $subject, implode( "\n", $body_lines ), $headers );

	if ( ! $sent ) {
		wp_send_json_error( array(
			'message' => __( 'Không thể gửi yêu cầu lúc này. Vui lòng gọi trực tiếp hotline hoặc thử lại sau.', 'tmnhanphat' ),
		), 500 );
	}

	wp_send_json_success( array(
		'message' => __( 'Cảm ơn bạn! Yêu cầu đã được gửi, chúng tôi sẽ liên hệ lại sớm.', 'tmnhanphat' ),
	) );
}
add_action( 'wp_ajax_tmnhanphat_submit_quote', 'tmnhanphat_ajax_submit_quote' );
add_action( 'wp_ajax_nopriv_tmnhanphat_submit_quote', 'tmnhanphat_ajax_submit_quote' );
