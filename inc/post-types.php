<?php
/**
 * Custom Post Types của theme — hiện tại: Service (tmnp_service).
 *
 * Mỗi Service là 1 dịch vụ (Lắp đặt/Bảo trì/Sửa chữa...) có landing page riêng (single
 * tmnp_service) phục vụ SEO/marketing — Homepage (Services Home Section) chỉ QUERY dữ liệu
 * từ đây để hiển thị, không render từ bài viết thường/Post Category (yêu cầu kiến trúc
 * Services Section). Archive dùng chung markup archive.php qua Template Hierarchy fallback
 * (PROJECT_RULES.md mục 3 — không tạo archive-tmnp_service.php khi chưa cần markup riêng).
 *
 * Cấu trúc dữ liệu 1 Service:
 * - Title/Excerpt (Short Description)/Featured Image/Status: dùng chuẩn WordPress.
 * - Order: menu_order (supports page-attributes).
 * - Landing Page URL: mặc định là permalink của chính post; có thể override qua meta box
 *   (khi landing page nằm ngoài single CPT, ví dụ 1 Page builder riêng).
 * - Optional CTA Text/URL: meta box, dành cho landing page/card nâng cao sau này.
 *
 * @package TMNhanPhat
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Đăng ký CPT Service.
 */
function tmnhanphat_register_service_post_type() {
	register_post_type( 'tmnp_service', array(
		'labels'       => array(
			'name'               => __( 'Dịch vụ', 'tmnhanphat' ),
			'singular_name'      => __( 'Dịch vụ', 'tmnhanphat' ),
			'add_new'            => __( 'Thêm dịch vụ', 'tmnhanphat' ),
			'add_new_item'       => __( 'Thêm dịch vụ mới', 'tmnhanphat' ),
			'edit_item'          => __( 'Sửa dịch vụ', 'tmnhanphat' ),
			'new_item'           => __( 'Dịch vụ mới', 'tmnhanphat' ),
			'view_item'          => __( 'Xem dịch vụ', 'tmnhanphat' ),
			'search_items'       => __( 'Tìm dịch vụ', 'tmnhanphat' ),
			'not_found'          => __( 'Không tìm thấy dịch vụ nào', 'tmnhanphat' ),
			'not_found_in_trash' => __( 'Thùng rác trống', 'tmnhanphat' ),
			'all_items'          => __( 'Tất cả dịch vụ', 'tmnhanphat' ),
			'menu_name'          => __( 'Dịch vụ', 'tmnhanphat' ),
		),
		'public'       => true,
		'has_archive'  => true,
		'rewrite'      => array(
			'slug'       => 'dich-vu',
			'with_front' => false,
		),
		'menu_icon'    => 'dashicons-building',
		'menu_position' => 20,
		'supports'     => array( 'title', 'editor', 'excerpt', 'thumbnail', 'page-attributes' ),
		'show_in_rest' => true, // Gutenberg + REST API (PROJECT_RULES.md mục 17).
	) );
}
add_action( 'init', 'tmnhanphat_register_service_post_type' );

/**
 * Flush rewrite rules đúng 1 lần khi kích hoạt theme — để permalink /dich-vu/ hoạt động ngay,
 * không flush trên mỗi request (rất tốn kém, sai chuẩn WordPress).
 */
function tmnhanphat_service_rewrite_flush() {
	tmnhanphat_register_service_post_type();
	flush_rewrite_rules();
}
add_action( 'after_switch_theme', 'tmnhanphat_service_rewrite_flush' );

/**
 * Meta box "Thông tin dịch vụ": Landing Page URL override + Optional CTA Text/URL.
 */
function tmnhanphat_service_meta_boxes() {
	add_meta_box(
		'tmnp_service_details',
		__( 'Thông tin dịch vụ', 'tmnhanphat' ),
		'tmnhanphat_render_service_meta_box',
		'tmnp_service',
		'normal',
		'default'
	);
}
add_action( 'add_meta_boxes', 'tmnhanphat_service_meta_boxes' );

/**
 * In nội dung meta box.
 *
 * @param WP_Post $post Post đang sửa.
 */
function tmnhanphat_render_service_meta_box( $post ) {
	wp_nonce_field( 'tmnp_service_meta_save', 'tmnp_service_meta_nonce' );

	$landing_url = get_post_meta( $post->ID, '_tmnp_service_landing_url', true );
	$cta_text    = get_post_meta( $post->ID, '_tmnp_service_cta_text', true );
	$cta_url     = get_post_meta( $post->ID, '_tmnp_service_cta_url', true );
	?>
	<p>
		<label for="tmnp_service_landing_url"><strong><?php esc_html_e( 'Landing Page URL (tuỳ chọn)', 'tmnhanphat' ); ?></strong></label><br />
		<input type="url" class="widefat" id="tmnp_service_landing_url" name="tmnp_service_landing_url" value="<?php echo esc_attr( $landing_url ); ?>" placeholder="<?php echo esc_attr( get_permalink( $post ) ); ?>" />
		<span class="description"><?php esc_html_e( 'Bỏ trống = dùng chính trang chi tiết dịch vụ này. Chỉ điền khi landing page nằm ở URL khác.', 'tmnhanphat' ); ?></span>
	</p>
	<p>
		<label for="tmnp_service_cta_text"><strong><?php esc_html_e( 'CTA Text (tuỳ chọn)', 'tmnhanphat' ); ?></strong></label><br />
		<input type="text" class="widefat" id="tmnp_service_cta_text" name="tmnp_service_cta_text" value="<?php echo esc_attr( $cta_text ); ?>" />
	</p>
	<p>
		<label for="tmnp_service_cta_url"><strong><?php esc_html_e( 'CTA URL (tuỳ chọn)', 'tmnhanphat' ); ?></strong></label><br />
		<input type="url" class="widefat" id="tmnp_service_cta_url" name="tmnp_service_cta_url" value="<?php echo esc_attr( $cta_url ); ?>" />
	</p>
	<?php
}

/**
 * Lưu meta box (nonce + capability + sanitize — PROJECT_RULES.md mục 18).
 *
 * @param int $post_id ID post đang lưu.
 */
function tmnhanphat_save_service_meta( $post_id ) {
	if ( ! isset( $_POST['tmnp_service_meta_nonce'] ) || ! wp_verify_nonce( sanitize_key( $_POST['tmnp_service_meta_nonce'] ), 'tmnp_service_meta_save' ) ) {
		return;
	}

	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}

	if ( ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}

	$fields = array(
		'tmnp_service_landing_url' => 'esc_url_raw',
		'tmnp_service_cta_text'    => 'sanitize_text_field',
		'tmnp_service_cta_url'     => 'esc_url_raw',
	);

	foreach ( $fields as $field => $sanitize ) {
		$value = isset( $_POST[ $field ] ) ? call_user_func( $sanitize, wp_unslash( $_POST[ $field ] ) ) : '';

		if ( '' === $value ) {
			delete_post_meta( $post_id, "_{$field}" );
		} else {
			update_post_meta( $post_id, "_{$field}", $value );
		}
	}
}
add_action( 'save_post_tmnp_service', 'tmnhanphat_save_service_meta' );

/* ==========================================================================
 * PRODUCT META (post thường thuộc Category "Sản phẩm") — Thương hiệu/Tiêu chuẩn
 * hiển thị trên Product Card của Products Home Section. Không phải CPT: sản phẩm
 * là bài viết trong cây category cha "Sản phẩm" (dữ liệu có sẵn trong wp_posts).
 * Meta key mặc định _tmnp_product_brand/_tmnp_product_standard — Products Section
 * đọc qua meta key CẤU HÌNH ĐƯỢC trong Customizer (đổi được sang key ACF sau này,
 * xem tmnhanphat_get_product_meta_value() trong inc/template-functions.php).
 * ========================================================================== */

/**
 * Meta box "Thông tin sản phẩm": Thương hiệu + Tiêu chuẩn cho post thường.
 * Đăng ký cho mọi post (không lọc theo category — category chỉ được gán lúc lưu,
 * lọc trước không đáng tin cậy); bài không thuộc "Sản phẩm" cứ để trống, không ảnh hưởng.
 */
function tmnhanphat_product_meta_boxes() {
	add_meta_box(
		'tmnp_product_details',
		__( 'Thông tin sản phẩm (chỉ dùng cho bài thuộc danh mục Sản phẩm)', 'tmnhanphat' ),
		'tmnhanphat_render_product_meta_box',
		'post',
		'side',
		'default'
	);
}
add_action( 'add_meta_boxes', 'tmnhanphat_product_meta_boxes' );

/**
 * In nội dung meta box sản phẩm.
 *
 * @param WP_Post $post Post đang sửa.
 */
function tmnhanphat_render_product_meta_box( $post ) {
	wp_nonce_field( 'tmnp_product_meta_save', 'tmnp_product_meta_nonce' );

	$brand    = get_post_meta( $post->ID, '_tmnp_product_brand', true );
	$standard = get_post_meta( $post->ID, '_tmnp_product_standard', true );
	?>
	<p>
		<label for="tmnp_product_brand"><strong><?php esc_html_e( 'Thương hiệu', 'tmnhanphat' ); ?></strong></label><br />
		<input type="text" class="widefat" id="tmnp_product_brand" name="tmnp_product_brand" value="<?php echo esc_attr( $brand ); ?>" placeholder="<?php esc_attr_e( 'VD: Nhân Phát Elevator', 'tmnhanphat' ); ?>" />
	</p>
	<p>
		<label for="tmnp_product_standard"><strong><?php esc_html_e( 'Tiêu chuẩn', 'tmnhanphat' ); ?></strong></label><br />
		<input type="text" class="widefat" id="tmnp_product_standard" name="tmnp_product_standard" value="<?php echo esc_attr( $standard ); ?>" placeholder="<?php esc_attr_e( 'VD: ISO 9001-2015', 'tmnhanphat' ); ?>" />
		<span class="description"><?php esc_html_e( 'Bỏ trống dòng nào thì Card ngoài trang chủ tự ẩn dòng đó.', 'tmnhanphat' ); ?></span>
	</p>
	<?php
}

/**
 * Lưu meta box sản phẩm (nonce + capability + sanitize — PROJECT_RULES.md mục 18).
 *
 * @param int $post_id ID post đang lưu.
 */
function tmnhanphat_save_product_meta( $post_id ) {
	if ( ! isset( $_POST['tmnp_product_meta_nonce'] ) || ! wp_verify_nonce( sanitize_key( $_POST['tmnp_product_meta_nonce'] ), 'tmnp_product_meta_save' ) ) {
		return;
	}

	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}

	if ( ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}

	$fields = array(
		'tmnp_product_brand'    => 'sanitize_text_field',
		'tmnp_product_standard' => 'sanitize_text_field',
	);

	foreach ( $fields as $field => $sanitize ) {
		$value = isset( $_POST[ $field ] ) ? call_user_func( $sanitize, wp_unslash( $_POST[ $field ] ) ) : '';

		if ( '' === $value ) {
			delete_post_meta( $post_id, "_{$field}" );
		} else {
			update_post_meta( $post_id, "_{$field}", $value );
		}
	}
}
add_action( 'save_post_post', 'tmnhanphat_save_product_meta' );

/* ==========================================================================
 * PROJECT META (post thường thuộc Category "Dự án") — Loại hình/Tải trọng/Bàn giao
 * hiển thị trên Featured Projects Section trang chủ. Không phải CPT: dự án là bài
 * viết trong category "Dự án" chọn ở Customizer (dữ liệu có sẵn trong wp_posts).
 * Meta key mặc định _tmnp_project_type/_load/_year — Projects Section đọc qua meta
 * key CẤU HÌNH ĐƯỢC trong Customizer (đổi được sang key ACF sau này, xem
 * tmnhanphat_get_project_meta_items() trong inc/template-functions.php).
 * ========================================================================== */

/**
 * Meta box "Thông tin dự án" cho post thường (cùng lý do đăng ký mọi post như
 * meta box Sản phẩm — bài không thuộc "Dự án" cứ để trống, không ảnh hưởng).
 */
function tmnhanphat_project_meta_boxes() {
	add_meta_box(
		'tmnp_project_details',
		__( 'Thông tin dự án (chỉ dùng cho bài thuộc danh mục Dự án)', 'tmnhanphat' ),
		'tmnhanphat_render_project_meta_box',
		'post',
		'side',
		'default'
	);
}
add_action( 'add_meta_boxes', 'tmnhanphat_project_meta_boxes' );

/**
 * In nội dung meta box dự án.
 *
 * @param WP_Post $post Post đang sửa.
 */
function tmnhanphat_render_project_meta_box( $post ) {
	wp_nonce_field( 'tmnp_project_meta_save', 'tmnp_project_meta_nonce' );

	$type = get_post_meta( $post->ID, '_tmnp_project_type', true );
	$load = get_post_meta( $post->ID, '_tmnp_project_load', true );
	$year = get_post_meta( $post->ID, '_tmnp_project_year', true );
	?>
	<p>
		<label for="tmnp_project_type"><strong><?php esc_html_e( 'Loại hình', 'tmnhanphat' ); ?></strong></label><br />
		<input type="text" class="widefat" id="tmnp_project_type" name="tmnp_project_type" value="<?php echo esc_attr( $type ); ?>" placeholder="<?php esc_attr_e( 'VD: Thang máy gia đình', 'tmnhanphat' ); ?>" />
	</p>
	<p>
		<label for="tmnp_project_load"><strong><?php esc_html_e( 'Tải trọng', 'tmnhanphat' ); ?></strong></label><br />
		<input type="text" class="widefat" id="tmnp_project_load" name="tmnp_project_load" value="<?php echo esc_attr( $load ); ?>" placeholder="<?php esc_attr_e( 'VD: 350kg', 'tmnhanphat' ); ?>" />
	</p>
	<p>
		<label for="tmnp_project_year"><strong><?php esc_html_e( 'Bàn giao', 'tmnhanphat' ); ?></strong></label><br />
		<input type="text" class="widefat" id="tmnp_project_year" name="tmnp_project_year" value="<?php echo esc_attr( $year ); ?>" placeholder="<?php esc_attr_e( 'VD: 2024', 'tmnhanphat' ); ?>" />
		<span class="description"><?php esc_html_e( 'Bỏ trống thì trang chủ hiển thị giá trị fallback cấu hình trong Customizer.', 'tmnhanphat' ); ?></span>
	</p>
	<?php
}

/**
 * Lưu meta box dự án (nonce + capability + sanitize — PROJECT_RULES.md mục 18).
 *
 * @param int $post_id ID post đang lưu.
 */
function tmnhanphat_save_project_meta( $post_id ) {
	if ( ! isset( $_POST['tmnp_project_meta_nonce'] ) || ! wp_verify_nonce( sanitize_key( $_POST['tmnp_project_meta_nonce'] ), 'tmnp_project_meta_save' ) ) {
		return;
	}

	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}

	if ( ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}

	$fields = array(
		'tmnp_project_type' => 'sanitize_text_field',
		'tmnp_project_load' => 'sanitize_text_field',
		'tmnp_project_year' => 'sanitize_text_field',
	);

	foreach ( $fields as $field => $sanitize ) {
		$value = isset( $_POST[ $field ] ) ? call_user_func( $sanitize, wp_unslash( $_POST[ $field ] ) ) : '';

		if ( '' === $value ) {
			delete_post_meta( $post_id, "_{$field}" );
		} else {
			update_post_meta( $post_id, "_{$field}", $value );
		}
	}
}
add_action( 'save_post_post', 'tmnhanphat_save_project_meta' );

/* ==========================================================================
 * CUSTOM POST TYPE "customer_review" — Đánh giá thực tế của khách hàng, hiển thị
 * ở Customer Review Home Section (trang chủ). Post-like đầy đủ (Title/Editor/
 * Featured Image + Add/Edit/Delete/Draft/Publish); Title = Reviewer Name, Editor
 * (post_content) = Comment (đọc qua tmnhanphat_get_excerpt() — tự fallback sang
 * content nếu chưa nhập Excerpt, cùng cách Projects Section đọc mô tả), Featured
 * Image = Avatar. Meta box riêng chỉ cho các field KHÔNG có sẵn trong post-like UI:
 * Role, Rating (ẩn hiển thị nhưng tạo sẵn), Sort Order, Hide Review.
 * ========================================================================== */

/**
 * Đăng ký CPT customer_review — không public (không có single/archive ngoài site,
 * chỉ hiển thị qua Customer Review Home Section), quản lý trong wp-admin như Post.
 */
function tmnhanphat_register_customer_review_post_type() {
	register_post_type( 'customer_review', array(
		'labels'              => array(
			'name'                     => __( 'Customer Reviews', 'tmnhanphat' ),
			'singular_name'            => __( 'Customer Review', 'tmnhanphat' ),
			'add_new'                  => __( 'Thêm Đánh giá', 'tmnhanphat' ),
			'add_new_item'             => __( 'Thêm Đánh giá mới', 'tmnhanphat' ),
			'edit_item'                => __( 'Sửa Đánh giá', 'tmnhanphat' ),
			'new_item'                 => __( 'Đánh giá mới', 'tmnhanphat' ),
			'view_item'                => __( 'Xem Đánh giá', 'tmnhanphat' ),
			'search_items'             => __( 'Tìm Đánh giá', 'tmnhanphat' ),
			'not_found'                => __( 'Chưa có Đánh giá nào', 'tmnhanphat' ),
			'not_found_in_trash'       => __( 'Không có Đánh giá nào trong Thùng rác', 'tmnhanphat' ),
			'all_items'                => __( 'Tất cả Đánh giá', 'tmnhanphat' ),
			'menu_name'                => __( 'Customer Reviews', 'tmnhanphat' ),
			'featured_image'           => __( 'Avatar', 'tmnhanphat' ),
			'set_featured_image'       => __( 'Chọn Avatar', 'tmnhanphat' ),
			'remove_featured_image'    => __( 'Xoá Avatar', 'tmnhanphat' ),
			'title_field_placeholder'  => __( 'Tên khách hàng — VD: David Pereira', 'tmnhanphat' ),
		),
		'public'              => false,
		'show_ui'             => true,
		'show_in_menu'        => true,
		'show_in_rest'        => true,
		'menu_icon'           => 'dashicons-testimonial',
		'menu_position'       => 22,
		'hierarchical'        => false,
		'has_archive'         => false,
		'rewrite'             => false,
		'exclude_from_search' => true,
		'capability_type'     => 'post',
		'supports'            => array( 'title', 'editor', 'thumbnail' ),
	) );
}
add_action( 'init', 'tmnhanphat_register_customer_review_post_type' );

/**
 * Meta box "Thông tin đánh giá": Role/Rating/Sort Order/Hide Review — các field
 * KHÔNG có sẵn trong UI post-like chuẩn (Title=Reviewer Name, Editor=Comment,
 * Featured Image=Avatar đã đủ, không cần field riêng).
 */
function tmnhanphat_customer_review_meta_boxes() {
	add_meta_box(
		'tmnp_review_details',
		__( 'Thông tin đánh giá', 'tmnhanphat' ),
		'tmnhanphat_render_customer_review_meta_box',
		'customer_review',
		'side',
		'default'
	);
}
add_action( 'add_meta_boxes', 'tmnhanphat_customer_review_meta_boxes' );

/**
 * In nội dung meta box đánh giá.
 *
 * @param WP_Post $post Post đang sửa.
 */
function tmnhanphat_render_customer_review_meta_box( $post ) {
	wp_nonce_field( 'tmnp_review_meta_save', 'tmnp_review_meta_nonce' );

	$role   = get_post_meta( $post->ID, '_tmnp_review_role', true );
	$rating = get_post_meta( $post->ID, '_tmnp_review_rating', true );
	$order  = get_post_meta( $post->ID, '_tmnp_review_order', true );
	$hide   = get_post_meta( $post->ID, '_tmnp_review_hide', true );
	?>
	<p>
		<label for="tmnp_review_role"><strong><?php esc_html_e( 'Reviewer Role', 'tmnhanphat' ); ?></strong></label><br />
		<input type="text" class="widefat" id="tmnp_review_role" name="tmnp_review_role" value="<?php echo esc_attr( $role ); ?>" placeholder="<?php esc_attr_e( 'VD: CTO, CEO, Khách hàng cá nhân', 'tmnhanphat' ); ?>" />
		<span class="description"><?php esc_html_e( 'Bỏ trống thì trang chủ hiển thị "Khách hàng đã xác minh".', 'tmnhanphat' ); ?></span>
	</p>
	<p>
		<label for="tmnp_review_rating"><strong><?php esc_html_e( 'Rating (1-5 sao, chưa hiển thị ngoài site)', 'tmnhanphat' ); ?></strong></label><br />
		<select class="widefat" id="tmnp_review_rating" name="tmnp_review_rating">
			<option value=""><?php esc_html_e( '— Không đánh giá —', 'tmnhanphat' ); ?></option>
			<?php for ( $i = 1; $i <= 5; $i++ ) : ?>
				<option value="<?php echo esc_attr( $i ); ?>" <?php selected( (string) $rating, (string) $i ); ?>><?php echo esc_html( $i ); ?></option>
			<?php endfor; ?>
		</select>
	</p>
	<p>
		<label for="tmnp_review_order"><strong><?php esc_html_e( 'Sort Order', 'tmnhanphat' ); ?></strong></label><br />
		<input type="number" class="widefat" id="tmnp_review_order" name="tmnp_review_order" value="<?php echo esc_attr( $order ); ?>" min="0" step="1" />
	</p>
	<p>
		<label>
			<input type="checkbox" id="tmnp_review_hide" name="tmnp_review_hide" value="1" <?php checked( $hide, '1' ); ?> />
			<strong><?php esc_html_e( 'Hide Review (ẩn khỏi trang chủ, không cần đổi trạng thái Publish)', 'tmnhanphat' ); ?></strong>
		</label>
	</p>
	<?php
}

/**
 * Lưu meta box đánh giá (nonce + capability + sanitize — PROJECT_RULES.md mục 18).
 *
 * @param int $post_id ID post đang lưu.
 */
function tmnhanphat_save_customer_review_meta( $post_id ) {
	if ( ! isset( $_POST['tmnp_review_meta_nonce'] ) || ! wp_verify_nonce( sanitize_key( $_POST['tmnp_review_meta_nonce'] ), 'tmnp_review_meta_save' ) ) {
		return;
	}

	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}

	if ( ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}

	$role  = isset( $_POST['tmnp_review_role'] ) ? sanitize_text_field( wp_unslash( $_POST['tmnp_review_role'] ) ) : '';
	$order = isset( $_POST['tmnp_review_order'] ) ? absint( $_POST['tmnp_review_order'] ) : 0;
	$hide  = isset( $_POST['tmnp_review_hide'] ) ? '1' : '0';

	$rating_raw = isset( $_POST['tmnp_review_rating'] ) ? absint( $_POST['tmnp_review_rating'] ) : 0;
	$rating     = ( $rating_raw >= 1 && $rating_raw <= 5 ) ? $rating_raw : '';

	if ( '' === $role ) {
		delete_post_meta( $post_id, '_tmnp_review_role' );
	} else {
		update_post_meta( $post_id, '_tmnp_review_role', $role );
	}

	if ( '' === $rating ) {
		delete_post_meta( $post_id, '_tmnp_review_rating' );
	} else {
		update_post_meta( $post_id, '_tmnp_review_rating', $rating );
	}

	update_post_meta( $post_id, '_tmnp_review_order', $order );
	update_post_meta( $post_id, '_tmnp_review_hide', $hide );
}
add_action( 'save_post_customer_review', 'tmnhanphat_save_customer_review_meta' );

/* ==========================================================================
 * CUSTOM POST TYPE "faq" — Câu hỏi thường gặp, hiển thị ở FAQ Home Section
 * (trang chủ). Post-like: Title = Question, Editor (post_content) = Answer.
 * Meta box riêng chỉ cho field KHÔNG có sẵn trong UI post-like: Sort Order,
 * Expand Default (mở sẵn khi tải trang), Hide FAQ. Không dùng category Blog.
 * ========================================================================== */

/**
 * Đăng ký CPT faq — không public (chỉ hiển thị qua FAQ Home Section), quản lý
 * trong wp-admin như Post.
 */
function tmnhanphat_register_faq_post_type() {
	register_post_type( 'faq', array(
		'labels'              => array(
			'name'                    => __( 'Câu hỏi thường gặp', 'tmnhanphat' ),
			'singular_name'           => __( 'Câu hỏi', 'tmnhanphat' ),
			'add_new'                 => __( 'Thêm câu hỏi', 'tmnhanphat' ),
			'add_new_item'            => __( 'Thêm câu hỏi mới', 'tmnhanphat' ),
			'edit_item'               => __( 'Sửa câu hỏi', 'tmnhanphat' ),
			'new_item'                => __( 'Câu hỏi mới', 'tmnhanphat' ),
			'view_item'               => __( 'Xem câu hỏi', 'tmnhanphat' ),
			'search_items'            => __( 'Tìm câu hỏi', 'tmnhanphat' ),
			'not_found'               => __( 'Chưa có câu hỏi nào', 'tmnhanphat' ),
			'not_found_in_trash'      => __( 'Không có câu hỏi nào trong Thùng rác', 'tmnhanphat' ),
			'all_items'               => __( 'Tất cả câu hỏi', 'tmnhanphat' ),
			'menu_name'               => __( 'Câu hỏi thường gặp', 'tmnhanphat' ),
			'title_field_placeholder' => __( 'Câu hỏi — VD: Thời gian lắp đặt thang máy mất bao lâu?', 'tmnhanphat' ),
		),
		'public'              => false,
		'show_ui'             => true,
		'show_in_menu'        => true,
		'show_in_rest'        => true,
		'menu_icon'           => 'dashicons-editor-help',
		'menu_position'       => 23,
		'hierarchical'        => false,
		'has_archive'         => false,
		'rewrite'             => false,
		'exclude_from_search' => true,
		'capability_type'     => 'post',
		'supports'            => array( 'title', 'editor' ),
	) );
}
add_action( 'init', 'tmnhanphat_register_faq_post_type' );

/**
 * Meta box "Thiết lập FAQ": Sort Order / Expand Default / Hide FAQ.
 */
function tmnhanphat_faq_meta_boxes() {
	add_meta_box(
		'tmnp_faq_details',
		__( 'Thiết lập FAQ', 'tmnhanphat' ),
		'tmnhanphat_render_faq_meta_box',
		'faq',
		'side',
		'default'
	);
}
add_action( 'add_meta_boxes', 'tmnhanphat_faq_meta_boxes' );

/**
 * In nội dung meta box FAQ.
 *
 * @param WP_Post $post Post đang sửa.
 */
function tmnhanphat_render_faq_meta_box( $post ) {
	wp_nonce_field( 'tmnp_faq_meta_save', 'tmnp_faq_meta_nonce' );

	$order  = get_post_meta( $post->ID, '_tmnp_faq_order', true );
	$expand = get_post_meta( $post->ID, '_tmnp_faq_expand', true );
	$hide   = get_post_meta( $post->ID, '_tmnp_faq_hide', true );
	?>
	<p>
		<label for="tmnp_faq_order"><strong><?php esc_html_e( 'Sort Order', 'tmnhanphat' ); ?></strong></label><br />
		<input type="number" class="widefat" id="tmnp_faq_order" name="tmnp_faq_order" value="<?php echo esc_attr( $order ); ?>" min="0" step="1" />
	</p>
	<p>
		<label>
			<input type="checkbox" id="tmnp_faq_expand" name="tmnp_faq_expand" value="1" <?php checked( $expand, '1' ); ?> />
			<strong><?php esc_html_e( 'Expand Default (mở sẵn khi tải trang)', 'tmnhanphat' ); ?></strong>
		</label><br />
		<span class="description"><?php esc_html_e( 'Nếu nhiều FAQ cùng bật, chỉ FAQ đầu tiên theo Sort Order được mở.', 'tmnhanphat' ); ?></span>
	</p>
	<p>
		<label>
			<input type="checkbox" id="tmnp_faq_hide" name="tmnp_faq_hide" value="1" <?php checked( $hide, '1' ); ?> />
			<strong><?php esc_html_e( 'Hide FAQ (ẩn khỏi trang chủ)', 'tmnhanphat' ); ?></strong>
		</label>
	</p>
	<?php
}

/**
 * Lưu meta box FAQ (nonce + capability + sanitize — PROJECT_RULES.md mục 18).
 *
 * @param int $post_id ID post đang lưu.
 */
function tmnhanphat_save_faq_meta( $post_id ) {
	if ( ! isset( $_POST['tmnp_faq_meta_nonce'] ) || ! wp_verify_nonce( sanitize_key( $_POST['tmnp_faq_meta_nonce'] ), 'tmnp_faq_meta_save' ) ) {
		return;
	}

	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}

	if ( ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}

	update_post_meta( $post_id, '_tmnp_faq_order', isset( $_POST['tmnp_faq_order'] ) ? absint( $_POST['tmnp_faq_order'] ) : 0 );
	update_post_meta( $post_id, '_tmnp_faq_expand', isset( $_POST['tmnp_faq_expand'] ) ? '1' : '0' );
	update_post_meta( $post_id, '_tmnp_faq_hide', isset( $_POST['tmnp_faq_hide'] ) ? '1' : '0' );
}
add_action( 'save_post_faq', 'tmnhanphat_save_faq_meta' );
