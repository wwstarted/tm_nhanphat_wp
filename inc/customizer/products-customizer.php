<?php
/**
 * Đăng ký Customizer Panel riêng "Product Home" (PROJECT_RULES.md mục 26 — mỗi feature
 * Homepage 1 Panel cấp cao nhất, priority nối tiếp dải 30-59, KHÔNG lồng trong panel
 * "Homepage" nào vì WordPress Customizer không hỗ trợ panel-trong-panel): General, Heading,
 * Description, Categories, Query, Card, Slider. Cùng quy ước config-driven như các module
 * trước (mục 22).
 *
 * Dữ liệu hiển thị là POST THƯỜNG thuộc Category cha "Sản phẩm" (không phải CPT) — Customizer
 * chỉ điều khiển CÁCH hiển thị (query/category nav/slider/style), nội dung sản phẩm quản lý
 * qua Posts + meta box "Thông tin sản phẩm" (inc/post-types.php).
 *
 * @package TMNhanPhat
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Danh sách choices Category (id => tên) cho control "Parent Category" — build tại thời
 * điểm customize_register nên luôn phản ánh đúng danh mục hiện có.
 *
 * @return array<int, string>
 */
function tmnhanphat_get_products_category_choices() {
	$choices = array( 0 => __( '— Chọn Category cha —', 'tmnhanphat' ) );

	$terms = get_terms( array(
		'taxonomy'   => 'category',
		'hide_empty' => false,
		'orderby'    => 'name',
	) );

	if ( ! is_wp_error( $terms ) ) {
		foreach ( $terms as $term ) {
			$choices[ $term->term_id ] = $term->name;
		}
	}

	return $choices;
}

/**
 * Danh sách choices Post Type public (name => label) cho control "Post Type".
 *
 * @return array<string, string>
 */
function tmnhanphat_get_products_post_type_choices() {
	$choices = array();

	foreach ( get_post_types( array( 'public' => true ), 'objects' ) as $post_type ) {
		if ( 'attachment' === $post_type->name || 'page' === $post_type->name ) {
			continue;
		}
		$choices[ $post_type->name ] = $post_type->labels->name;
	}

	return $choices;
}

/**
 * Khai báo toàn bộ field của Products Home Section dạng config (id => định nghĩa).
 *
 * @return array<string, array>
 */
function tmnhanphat_get_products_customizer_fields() {
	$defaults = tmnhanphat_products_defaults();

	return array(
		// ----- General -----
		'tmnhanphat_products_enable'            => array(
			'label'             => __( 'Hiển thị Products Section', 'tmnhanphat' ),
			'section'           => 'general',
			'type'              => 'checkbox',
			'default'           => $defaults['tmnhanphat_products_enable'],
			'sanitize_callback' => 'tmnhanphat_sanitize_checkbox',
		),
		'tmnhanphat_products_section_id'        => array(
			'label'             => __( 'Section ID (anchor)', 'tmnhanphat' ),
			'section'           => 'general',
			'type'              => 'text',
			'default'           => $defaults['tmnhanphat_products_section_id'],
			'sanitize_callback' => 'sanitize_key',
		),
		'tmnhanphat_products_container_width'   => array(
			'label'             => __( 'Container Width (px, 0 = dùng Global Settings)', 'tmnhanphat' ),
			'section'           => 'general',
			'type'              => 'number',
			'default'           => $defaults['tmnhanphat_products_container_width'],
			'sanitize_callback' => 'absint',
			'input_attrs'       => array( 'min' => 0, 'max' => 1920, 'step' => 10 ),
		),
		'tmnhanphat_products_margin_top'        => array(
			'label'             => __( 'Top Margin (px)', 'tmnhanphat' ),
			'section'           => 'general',
			'type'              => 'number',
			'default'           => $defaults['tmnhanphat_products_margin_top'],
			'sanitize_callback' => 'absint',
			'input_attrs'       => array( 'min' => 0, 'max' => 300, 'step' => 4 ),
		),
		'tmnhanphat_products_margin_bottom'     => array(
			'label'             => __( 'Bottom Margin (px)', 'tmnhanphat' ),
			'section'           => 'general',
			'type'              => 'number',
			'default'           => $defaults['tmnhanphat_products_margin_bottom'],
			'sanitize_callback' => 'absint',
			'input_attrs'       => array( 'min' => 0, 'max' => 300, 'step' => 4 ),
		),
		'tmnhanphat_products_padding_desktop'   => array(
			'label'             => __( 'Section Padding — Desktop (px)', 'tmnhanphat' ),
			'section'           => 'general',
			'type'              => 'number',
			'default'           => $defaults['tmnhanphat_products_padding_desktop'],
			'sanitize_callback' => 'absint',
		),
		'tmnhanphat_products_padding_tablet'    => array(
			'label'             => __( 'Section Padding — Tablet (px)', 'tmnhanphat' ),
			'section'           => 'general',
			'type'              => 'number',
			'default'           => $defaults['tmnhanphat_products_padding_tablet'],
			'sanitize_callback' => 'absint',
		),
		'tmnhanphat_products_padding_mobile'    => array(
			'label'             => __( 'Section Padding — Mobile (px)', 'tmnhanphat' ),
			'section'           => 'general',
			'type'              => 'number',
			'default'           => $defaults['tmnhanphat_products_padding_mobile'],
			'sanitize_callback' => 'absint',
		),
		'tmnhanphat_products_bg_color'          => array(
			'label'             => __( 'Background Color', 'tmnhanphat' ),
			'section'           => 'general',
			'type'              => 'color',
			'default'           => $defaults['tmnhanphat_products_bg_color'],
			'sanitize_callback' => 'sanitize_hex_color',
		),
		'tmnhanphat_products_bg_image'          => array(
			'label'             => __( 'Background Image (tuỳ chọn)', 'tmnhanphat' ),
			'section'           => 'general',
			'type'              => 'image',
			'default'           => $defaults['tmnhanphat_products_bg_image'],
			'sanitize_callback' => 'esc_url_raw',
		),
		'tmnhanphat_products_decoration_image'  => array(
			'label'             => __( 'Decoration Image (tam giác xanh góc phải)', 'tmnhanphat' ),
			'section'           => 'general',
			'type'              => 'image',
			'default'           => $defaults['tmnhanphat_products_decoration_image'],
			'sanitize_callback' => 'esc_url_raw',
		),
		'tmnhanphat_products_decoration_enable' => array(
			'label'             => __( 'Hiển thị Decoration', 'tmnhanphat' ),
			'section'           => 'general',
			'type'              => 'checkbox',
			'default'           => $defaults['tmnhanphat_products_decoration_enable'],
			'sanitize_callback' => 'tmnhanphat_sanitize_checkbox',
		),

		// ----- Heading -----
		'tmnhanphat_products_heading_red_text'       => array(
			'label'             => __( 'Red Title (phần chữ màu đỏ)', 'tmnhanphat' ),
			'section'           => 'heading',
			'type'              => 'text',
			'default'           => $defaults['tmnhanphat_products_heading_red_text'],
			'sanitize_callback' => 'sanitize_text_field',
		),
		'tmnhanphat_products_heading_blue_text'      => array(
			'label'             => __( 'Blue Title (phần chữ màu xanh)', 'tmnhanphat' ),
			'section'           => 'heading',
			'type'              => 'text',
			'default'           => $defaults['tmnhanphat_products_heading_blue_text'],
			'sanitize_callback' => 'sanitize_text_field',
		),
		'tmnhanphat_products_heading_red_color'      => array(
			'label'             => __( 'Red Title Color', 'tmnhanphat' ),
			'section'           => 'heading',
			'type'              => 'color',
			'default'           => $defaults['tmnhanphat_products_heading_red_color'],
			'sanitize_callback' => 'sanitize_hex_color',
		),
		'tmnhanphat_products_heading_blue_color'     => array(
			'label'             => __( 'Blue Title Color', 'tmnhanphat' ),
			'section'           => 'heading',
			'type'              => 'color',
			'default'           => $defaults['tmnhanphat_products_heading_blue_color'],
			'sanitize_callback' => 'sanitize_hex_color',
		),
		'tmnhanphat_products_heading_size_desktop'   => array(
			'label'             => __( 'Title Font Size — Desktop (px)', 'tmnhanphat' ),
			'section'           => 'heading',
			'type'              => 'number',
			'default'           => $defaults['tmnhanphat_products_heading_size_desktop'],
			'sanitize_callback' => 'absint',
		),
		'tmnhanphat_products_heading_size_tablet'    => array(
			'label'             => __( 'Title Font Size — Tablet (px)', 'tmnhanphat' ),
			'section'           => 'heading',
			'type'              => 'number',
			'default'           => $defaults['tmnhanphat_products_heading_size_tablet'],
			'sanitize_callback' => 'absint',
		),
		'tmnhanphat_products_heading_size_mobile'    => array(
			'label'             => __( 'Title Font Size — Mobile (px)', 'tmnhanphat' ),
			'section'           => 'heading',
			'type'              => 'number',
			'default'           => $defaults['tmnhanphat_products_heading_size_mobile'],
			'sanitize_callback' => 'absint',
		),
		'tmnhanphat_products_heading_font_weight'    => array(
			'label'             => __( 'Title Weight', 'tmnhanphat' ),
			'section'           => 'heading',
			'choices'           => tmnhanphat_get_font_weight_choices(),
			'default'           => $defaults['tmnhanphat_products_heading_font_weight'],
			'sanitize_callback' => 'tmnhanphat_sanitize_font_weight',
		),
		'tmnhanphat_products_heading_line_height'    => array(
			'label'             => __( 'Line Height (vd 1.2)', 'tmnhanphat' ),
			'section'           => 'heading',
			'type'              => 'number',
			'default'           => $defaults['tmnhanphat_products_heading_line_height'],
			'sanitize_callback' => 'tmnhanphat_sanitize_products_line_height',
			'input_attrs'       => array( 'min' => 0.8, 'max' => 3, 'step' => 0.05 ),
		),
		'tmnhanphat_products_heading_letter_spacing' => array(
			'label'             => __( 'Letter Spacing (px, cho phép âm)', 'tmnhanphat' ),
			'section'           => 'heading',
			'type'              => 'number',
			'default'           => $defaults['tmnhanphat_products_heading_letter_spacing'],
			'sanitize_callback' => 'tmnhanphat_sanitize_products_letter_spacing',
			'input_attrs'       => array( 'min' => -5, 'max' => 10, 'step' => 1 ),
		),
		'tmnhanphat_products_heading_align'          => array(
			'label'             => __( 'Title Alignment', 'tmnhanphat' ),
			'section'           => 'heading',
			'choices'           => tmnhanphat_get_services_align_choices(),
			'default'           => $defaults['tmnhanphat_products_heading_align'],
			'sanitize_callback' => 'tmnhanphat_sanitize_services_align',
		),

		// ----- Description -----
		'tmnhanphat_products_description_text'         => array(
			'label'             => __( 'Description', 'tmnhanphat' ),
			'section'           => 'description',
			'type'              => 'textarea',
			'default'           => $defaults['tmnhanphat_products_description_text'],
			'sanitize_callback' => 'sanitize_textarea_field',
		),
		'tmnhanphat_products_description_color'        => array(
			'label'             => __( 'Description Color', 'tmnhanphat' ),
			'section'           => 'description',
			'type'              => 'color',
			'default'           => $defaults['tmnhanphat_products_description_color'],
			'sanitize_callback' => 'sanitize_hex_color',
		),
		'tmnhanphat_products_description_size_desktop' => array(
			'label'             => __( 'Description Font Size — Desktop (px)', 'tmnhanphat' ),
			'section'           => 'description',
			'type'              => 'number',
			'default'           => $defaults['tmnhanphat_products_description_size_desktop'],
			'sanitize_callback' => 'absint',
		),
		'tmnhanphat_products_description_size_tablet'  => array(
			'label'             => __( 'Description Font Size — Tablet (px)', 'tmnhanphat' ),
			'section'           => 'description',
			'type'              => 'number',
			'default'           => $defaults['tmnhanphat_products_description_size_tablet'],
			'sanitize_callback' => 'absint',
		),
		'tmnhanphat_products_description_size_mobile'  => array(
			'label'             => __( 'Description Font Size — Mobile (px)', 'tmnhanphat' ),
			'section'           => 'description',
			'type'              => 'number',
			'default'           => $defaults['tmnhanphat_products_description_size_mobile'],
			'sanitize_callback' => 'absint',
		),
		'tmnhanphat_products_description_max_width'    => array(
			'label'             => __( 'Description Width (px)', 'tmnhanphat' ),
			'section'           => 'description',
			'type'              => 'number',
			'default'           => $defaults['tmnhanphat_products_description_max_width'],
			'sanitize_callback' => 'absint',
			'input_attrs'       => array( 'min' => 300, 'max' => 1200, 'step' => 10 ),
		),
		'tmnhanphat_products_description_line_clamp'   => array(
			'label'             => __( 'Description Line Clamp (0 = không giới hạn)', 'tmnhanphat' ),
			'section'           => 'description',
			'type'              => 'number',
			'default'           => $defaults['tmnhanphat_products_description_line_clamp'],
			'sanitize_callback' => 'absint',
			'input_attrs'       => array( 'min' => 0, 'max' => 6, 'step' => 1 ),
		),
		'tmnhanphat_products_header_bottom_spacing'    => array(
			'label'             => __( 'Header Bottom Spacing (px)', 'tmnhanphat' ),
			'section'           => 'description',
			'type'              => 'number',
			'default'           => $defaults['tmnhanphat_products_header_bottom_spacing'],
			'sanitize_callback' => 'absint',
		),

		// ----- Categories -----
		'tmnhanphat_products_parent_cat'          => array(
			'label'             => __( 'Parent Category (Category cha "Sản phẩm")', 'tmnhanphat' ),
			'section'           => 'categories',
			'choices'           => tmnhanphat_get_products_category_choices(),
			'default'           => $defaults['tmnhanphat_products_parent_cat'],
			'sanitize_callback' => 'absint',
		),
		'tmnhanphat_products_show_empty_cats'     => array(
			'label'             => __( 'Show Empty Category (hiện cả danh mục chưa có bài)', 'tmnhanphat' ),
			'section'           => 'categories',
			'type'              => 'checkbox',
			'default'           => $defaults['tmnhanphat_products_show_empty_cats'],
			'sanitize_callback' => 'tmnhanphat_sanitize_checkbox',
		),
		'tmnhanphat_products_cats_align'          => array(
			'label'             => __( 'Category Alignment', 'tmnhanphat' ),
			'section'           => 'categories',
			'choices'           => tmnhanphat_get_services_align_choices(),
			'default'           => $defaults['tmnhanphat_products_cats_align'],
			'sanitize_callback' => 'tmnhanphat_sanitize_products_align_left',
		),
		'tmnhanphat_products_cats_gap'            => array(
			'label'             => __( 'Category Gap (px)', 'tmnhanphat' ),
			'section'           => 'categories',
			'type'              => 'number',
			'default'           => $defaults['tmnhanphat_products_cats_gap'],
			'sanitize_callback' => 'absint',
		),
		'tmnhanphat_products_cats_padding_x'      => array(
			'label'             => __( 'Category Padding ngang (px)', 'tmnhanphat' ),
			'section'           => 'categories',
			'type'              => 'number',
			'default'           => $defaults['tmnhanphat_products_cats_padding_x'],
			'sanitize_callback' => 'absint',
		),
		'tmnhanphat_products_cats_padding_y'      => array(
			'label'             => __( 'Category Padding dọc (px)', 'tmnhanphat' ),
			'section'           => 'categories',
			'type'              => 'number',
			'default'           => $defaults['tmnhanphat_products_cats_padding_y'],
			'sanitize_callback' => 'absint',
		),
		'tmnhanphat_products_cats_radius'         => array(
			'label'             => __( 'Border Radius (px)', 'tmnhanphat' ),
			'section'           => 'categories',
			'type'              => 'number',
			'default'           => $defaults['tmnhanphat_products_cats_radius'],
			'sanitize_callback' => 'absint',
		),
		'tmnhanphat_products_cats_font_size'      => array(
			'label'             => __( 'Font Size (px)', 'tmnhanphat' ),
			'section'           => 'categories',
			'type'              => 'number',
			'default'           => $defaults['tmnhanphat_products_cats_font_size'],
			'sanitize_callback' => 'absint',
		),
		'tmnhanphat_products_cats_active_bg'      => array(
			'label'             => __( 'Active Background', 'tmnhanphat' ),
			'section'           => 'categories',
			'type'              => 'color',
			'default'           => $defaults['tmnhanphat_products_cats_active_bg'],
			'sanitize_callback' => 'sanitize_hex_color',
		),
		'tmnhanphat_products_cats_active_color'   => array(
			'label'             => __( 'Active Color', 'tmnhanphat' ),
			'section'           => 'categories',
			'type'              => 'color',
			'default'           => $defaults['tmnhanphat_products_cats_active_color'],
			'sanitize_callback' => 'sanitize_hex_color',
		),
		'tmnhanphat_products_cats_inactive_bg'    => array(
			'label'             => __( 'Inactive Background', 'tmnhanphat' ),
			'section'           => 'categories',
			'type'              => 'color',
			'default'           => $defaults['tmnhanphat_products_cats_inactive_bg'],
			'sanitize_callback' => 'sanitize_hex_color',
		),
		'tmnhanphat_products_cats_inactive_color' => array(
			'label'             => __( 'Inactive Color', 'tmnhanphat' ),
			'section'           => 'categories',
			'type'              => 'color',
			'default'           => $defaults['tmnhanphat_products_cats_inactive_color'],
			'sanitize_callback' => 'sanitize_hex_color',
		),
		'tmnhanphat_products_cats_border'         => array(
			'label'             => __( 'Inactive Border Color', 'tmnhanphat' ),
			'section'           => 'categories',
			'type'              => 'color',
			'default'           => $defaults['tmnhanphat_products_cats_border'],
			'sanitize_callback' => 'sanitize_hex_color',
		),
		'tmnhanphat_products_cats_hover_bg'       => array(
			'label'             => __( 'Hover Background', 'tmnhanphat' ),
			'section'           => 'categories',
			'type'              => 'color',
			'default'           => $defaults['tmnhanphat_products_cats_hover_bg'],
			'sanitize_callback' => 'sanitize_hex_color',
		),
		'tmnhanphat_products_cats_hover_color'    => array(
			'label'             => __( 'Hover Color', 'tmnhanphat' ),
			'section'           => 'categories',
			'type'              => 'color',
			'default'           => $defaults['tmnhanphat_products_cats_hover_color'],
			'sanitize_callback' => 'sanitize_hex_color',
		),
		'tmnhanphat_products_cats_bottom_spacing' => array(
			'label'             => __( 'Category Bottom Spacing (px)', 'tmnhanphat' ),
			'section'           => 'categories',
			'type'              => 'number',
			'default'           => $defaults['tmnhanphat_products_cats_bottom_spacing'],
			'sanitize_callback' => 'absint',
		),

		// ----- Query -----
		'tmnhanphat_products_post_type'    => array(
			'label'             => __( 'Post Type', 'tmnhanphat' ),
			'section'           => 'query',
			'choices'           => tmnhanphat_get_products_post_type_choices(),
			'default'           => $defaults['tmnhanphat_products_post_type'],
			'sanitize_callback' => 'tmnhanphat_sanitize_products_post_type',
		),
		'tmnhanphat_products_total'        => array(
			'label'             => __( 'Số sản phẩm tối đa mỗi danh mục', 'tmnhanphat' ),
			'section'           => 'query',
			'type'              => 'number',
			'default'           => $defaults['tmnhanphat_products_total'],
			'sanitize_callback' => 'absint',
			'input_attrs'       => array( 'min' => 1, 'max' => 48, 'step' => 1 ),
		),
		'tmnhanphat_products_cols_desktop' => array(
			'label'             => __( 'Columns Desktop', 'tmnhanphat' ),
			'section'           => 'query',
			'type'              => 'number',
			'default'           => $defaults['tmnhanphat_products_cols_desktop'],
			'sanitize_callback' => 'absint',
			'input_attrs'       => array( 'min' => 1, 'max' => 4, 'step' => 1 ),
		),
		'tmnhanphat_products_cols_tablet'  => array(
			'label'             => __( 'Columns Tablet', 'tmnhanphat' ),
			'section'           => 'query',
			'type'              => 'number',
			'default'           => $defaults['tmnhanphat_products_cols_tablet'],
			'sanitize_callback' => 'absint',
			'input_attrs'       => array( 'min' => 1, 'max' => 3, 'step' => 1 ),
		),
		'tmnhanphat_products_cols_mobile'  => array(
			'label'             => __( 'Columns Mobile', 'tmnhanphat' ),
			'section'           => 'query',
			'type'              => 'number',
			'default'           => $defaults['tmnhanphat_products_cols_mobile'],
			'sanitize_callback' => 'absint',
			'input_attrs'       => array( 'min' => 1, 'max' => 2, 'step' => 1 ),
		),
		'tmnhanphat_products_rows_desktop' => array(
			'label'             => __( 'Rows / Slide — Desktop (3 cột × 2 hàng = 6 bài)', 'tmnhanphat' ),
			'section'           => 'query',
			'type'              => 'number',
			'default'           => $defaults['tmnhanphat_products_rows_desktop'],
			'sanitize_callback' => 'absint',
			'input_attrs'       => array( 'min' => 1, 'max' => 3, 'step' => 1 ),
		),
		'tmnhanphat_products_rows_tablet'  => array(
			'label'             => __( 'Rows / Slide — Tablet', 'tmnhanphat' ),
			'section'           => 'query',
			'type'              => 'number',
			'default'           => $defaults['tmnhanphat_products_rows_tablet'],
			'sanitize_callback' => 'absint',
			'input_attrs'       => array( 'min' => 1, 'max' => 3, 'step' => 1 ),
		),
		'tmnhanphat_products_rows_mobile'  => array(
			'label'             => __( 'Rows / Slide — Mobile', 'tmnhanphat' ),
			'section'           => 'query',
			'type'              => 'number',
			'default'           => $defaults['tmnhanphat_products_rows_mobile'],
			'sanitize_callback' => 'absint',
			'input_attrs'       => array( 'min' => 1, 'max' => 3, 'step' => 1 ),
		),
		'tmnhanphat_products_orderby'      => array(
			'label'             => __( 'Order By', 'tmnhanphat' ),
			'section'           => 'query',
			'choices'           => tmnhanphat_get_products_orderby_choices(),
			'default'           => $defaults['tmnhanphat_products_orderby'],
			'sanitize_callback' => 'tmnhanphat_sanitize_products_orderby',
		),
		'tmnhanphat_products_order'        => array(
			'label'             => __( 'Order', 'tmnhanphat' ),
			'section'           => 'query',
			'choices'           => array(
				'DESC' => __( 'Giảm dần (mới nhất trước)', 'tmnhanphat' ),
				'ASC'  => __( 'Tăng dần (cũ nhất trước)', 'tmnhanphat' ),
			),
			'default'           => $defaults['tmnhanphat_products_order'],
			'sanitize_callback' => 'tmnhanphat_sanitize_products_order',
		),
		'tmnhanphat_products_exclude_cats' => array(
			'label'             => __( 'Exclude Category IDs (CSV, ví dụ: 14,17)', 'tmnhanphat' ),
			'section'           => 'query',
			'type'              => 'text',
			'default'           => $defaults['tmnhanphat_products_exclude_cats'],
			'sanitize_callback' => 'tmnhanphat_sanitize_id_list',
		),

		// ----- Card -----
		'tmnhanphat_products_card_radius'        => array(
			'label'             => __( 'Card Radius (px)', 'tmnhanphat' ),
			'section'           => 'card',
			'type'              => 'number',
			'default'           => $defaults['tmnhanphat_products_card_radius'],
			'sanitize_callback' => 'absint',
		),
		'tmnhanphat_products_card_shadow'        => array(
			'label'             => __( 'Card Shadow', 'tmnhanphat' ),
			'section'           => 'card',
			'choices'           => tmnhanphat_get_products_card_shadow_choices(),
			'default'           => $defaults['tmnhanphat_products_card_shadow'],
			'sanitize_callback' => 'tmnhanphat_sanitize_products_card_shadow',
		),
		'tmnhanphat_products_card_padding'       => array(
			'label'             => __( 'Card Content Padding (px)', 'tmnhanphat' ),
			'section'           => 'card',
			'type'              => 'number',
			'default'           => $defaults['tmnhanphat_products_card_padding'],
			'sanitize_callback' => 'absint',
		),
		'tmnhanphat_products_card_gap'           => array(
			'label'             => __( 'Gap giữa các Card (px)', 'tmnhanphat' ),
			'section'           => 'card',
			'type'              => 'number',
			'default'           => $defaults['tmnhanphat_products_card_gap'],
			'sanitize_callback' => 'absint',
			'input_attrs'       => array( 'min' => 0, 'max' => 60, 'step' => 2 ),
		),
		'tmnhanphat_products_image_ratio'        => array(
			'label'             => __( 'Image Ratio', 'tmnhanphat' ),
			'section'           => 'card',
			'choices'           => tmnhanphat_get_products_ratio_choices(),
			'default'           => $defaults['tmnhanphat_products_image_ratio'],
			'sanitize_callback' => 'tmnhanphat_sanitize_products_ratio',
		),
		'tmnhanphat_products_image_radius'       => array(
			'label'             => __( 'Image Radius (px)', 'tmnhanphat' ),
			'section'           => 'card',
			'type'              => 'number',
			'default'           => $defaults['tmnhanphat_products_image_radius'],
			'sanitize_callback' => 'absint',
		),
		'tmnhanphat_products_hover_zoom_enable'  => array(
			'label'             => __( 'Image Hover Zoom', 'tmnhanphat' ),
			'section'           => 'card',
			'type'              => 'checkbox',
			'default'           => $defaults['tmnhanphat_products_hover_zoom_enable'],
			'sanitize_callback' => 'tmnhanphat_sanitize_checkbox',
		),
		'tmnhanphat_products_title_size_desktop' => array(
			'label'             => __( 'Title Font Size — Desktop (px)', 'tmnhanphat' ),
			'section'           => 'card',
			'type'              => 'number',
			'default'           => $defaults['tmnhanphat_products_title_size_desktop'],
			'sanitize_callback' => 'absint',
		),
		'tmnhanphat_products_title_size_tablet'  => array(
			'label'             => __( 'Title Font Size — Tablet (px)', 'tmnhanphat' ),
			'section'           => 'card',
			'type'              => 'number',
			'default'           => $defaults['tmnhanphat_products_title_size_tablet'],
			'sanitize_callback' => 'absint',
		),
		'tmnhanphat_products_title_size_mobile'  => array(
			'label'             => __( 'Title Font Size — Mobile (px)', 'tmnhanphat' ),
			'section'           => 'card',
			'type'              => 'number',
			'default'           => $defaults['tmnhanphat_products_title_size_mobile'],
			'sanitize_callback' => 'absint',
		),
		'tmnhanphat_products_title_line_clamp'   => array(
			'label'             => __( 'Title Line Clamp (1-4 dòng, dài hơn hiện ...)', 'tmnhanphat' ),
			'section'           => 'card',
			'type'              => 'number',
			'default'           => $defaults['tmnhanphat_products_title_line_clamp'],
			'sanitize_callback' => 'absint',
			'input_attrs'       => array( 'min' => 1, 'max' => 4, 'step' => 1 ),
		),
		'tmnhanphat_products_title_align'        => array(
			'label'             => __( 'Title Alignment', 'tmnhanphat' ),
			'section'           => 'card',
			'choices'           => tmnhanphat_get_services_align_choices(),
			'default'           => $defaults['tmnhanphat_products_title_align'],
			'sanitize_callback' => 'tmnhanphat_sanitize_products_align_left',
		),
		'tmnhanphat_products_meta_font_size'     => array(
			'label'             => __( 'Meta Font Size (px)', 'tmnhanphat' ),
			'section'           => 'card',
			'type'              => 'number',
			'default'           => $defaults['tmnhanphat_products_meta_font_size'],
			'sanitize_callback' => 'absint',
		),
		'tmnhanphat_products_meta_color'         => array(
			'label'             => __( 'Meta Color', 'tmnhanphat' ),
			'section'           => 'card',
			'type'              => 'color',
			'default'           => $defaults['tmnhanphat_products_meta_color'],
			'sanitize_callback' => 'sanitize_hex_color',
		),
		'tmnhanphat_products_brand_enable'       => array(
			'label'             => __( 'Hiển thị Thương hiệu', 'tmnhanphat' ),
			'section'           => 'card',
			'type'              => 'checkbox',
			'default'           => $defaults['tmnhanphat_products_brand_enable'],
			'sanitize_callback' => 'tmnhanphat_sanitize_checkbox',
		),
		'tmnhanphat_products_brand_label'        => array(
			'label'             => __( 'Brand Label', 'tmnhanphat' ),
			'section'           => 'card',
			'type'              => 'text',
			'default'           => $defaults['tmnhanphat_products_brand_label'],
			'sanitize_callback' => 'sanitize_text_field',
		),
		'tmnhanphat_products_brand_meta_key'     => array(
			'label'             => __( 'Brand Meta Key (đổi khi dùng ACF)', 'tmnhanphat' ),
			'section'           => 'card',
			'type'              => 'text',
			'default'           => $defaults['tmnhanphat_products_brand_meta_key'],
			'sanitize_callback' => 'sanitize_text_field',
		),
		'tmnhanphat_products_standard_enable'    => array(
			'label'             => __( 'Hiển thị Tiêu chuẩn', 'tmnhanphat' ),
			'section'           => 'card',
			'type'              => 'checkbox',
			'default'           => $defaults['tmnhanphat_products_standard_enable'],
			'sanitize_callback' => 'tmnhanphat_sanitize_checkbox',
		),
		'tmnhanphat_products_standard_label'     => array(
			'label'             => __( 'Standard Label', 'tmnhanphat' ),
			'section'           => 'card',
			'type'              => 'text',
			'default'           => $defaults['tmnhanphat_products_standard_label'],
			'sanitize_callback' => 'sanitize_text_field',
		),
		'tmnhanphat_products_standard_meta_key'  => array(
			'label'             => __( 'Standard Meta Key (đổi khi dùng ACF)', 'tmnhanphat' ),
			'section'           => 'card',
			'type'              => 'text',
			'default'           => $defaults['tmnhanphat_products_standard_meta_key'],
			'sanitize_callback' => 'sanitize_text_field',
		),
		'tmnhanphat_products_price_label'        => array(
			'label'             => __( 'Price Label', 'tmnhanphat' ),
			'section'           => 'card',
			'type'              => 'text',
			'default'           => $defaults['tmnhanphat_products_price_label'],
			'sanitize_callback' => 'sanitize_text_field',
		),
		'tmnhanphat_products_contact_text'       => array(
			'label'             => __( 'Contact Text', 'tmnhanphat' ),
			'section'           => 'card',
			'type'              => 'text',
			'default'           => $defaults['tmnhanphat_products_contact_text'],
			'sanitize_callback' => 'sanitize_text_field',
		),
		'tmnhanphat_products_contact_color'      => array(
			'label'             => __( 'Contact Text Color', 'tmnhanphat' ),
			'section'           => 'card',
			'type'              => 'color',
			'default'           => $defaults['tmnhanphat_products_contact_color'],
			'sanitize_callback' => 'sanitize_hex_color',
		),
		'tmnhanphat_products_contact_url'        => array(
			'label'             => __( 'Contact URL (bỏ trống = # — sau này trỏ Contact Page)', 'tmnhanphat' ),
			'section'           => 'card',
			'type'              => 'text',
			'default'           => $defaults['tmnhanphat_products_contact_url'],
			'sanitize_callback' => 'esc_url_raw',
		),
		'tmnhanphat_products_button_text'        => array(
			'label'             => __( 'Button Text', 'tmnhanphat' ),
			'section'           => 'card',
			'type'              => 'text',
			'default'           => $defaults['tmnhanphat_products_button_text'],
			'sanitize_callback' => 'sanitize_text_field',
		),
		'tmnhanphat_products_button_radius'      => array(
			'label'             => __( 'Button Radius (px)', 'tmnhanphat' ),
			'section'           => 'card',
			'type'              => 'number',
			'default'           => $defaults['tmnhanphat_products_button_radius'],
			'sanitize_callback' => 'absint',
		),
		'tmnhanphat_products_button_bg'          => array(
			'label'             => __( 'Button Background', 'tmnhanphat' ),
			'section'           => 'card',
			'type'              => 'color',
			'default'           => $defaults['tmnhanphat_products_button_bg'],
			'sanitize_callback' => 'sanitize_hex_color',
		),
		'tmnhanphat_products_button_hover_bg'    => array(
			'label'             => __( 'Button Hover Background', 'tmnhanphat' ),
			'section'           => 'card',
			'type'              => 'color',
			'default'           => $defaults['tmnhanphat_products_button_hover_bg'],
			'sanitize_callback' => 'sanitize_hex_color',
		),
		'tmnhanphat_products_button_color'       => array(
			'label'             => __( 'Button Color', 'tmnhanphat' ),
			'section'           => 'card',
			'type'              => 'color',
			'default'           => $defaults['tmnhanphat_products_button_color'],
			'sanitize_callback' => 'sanitize_hex_color',
		),

		// ----- Slider -----
		'tmnhanphat_products_autoplay_enable'  => array(
			'label'             => __( 'Enable Autoplay', 'tmnhanphat' ),
			'section'           => 'slider',
			'type'              => 'checkbox',
			'default'           => $defaults['tmnhanphat_products_autoplay_enable'],
			'sanitize_callback' => 'tmnhanphat_sanitize_checkbox',
		),
		'tmnhanphat_products_autoplay_delay'   => array(
			'label'             => __( 'Autoplay Delay (ms)', 'tmnhanphat' ),
			'section'           => 'slider',
			'type'              => 'number',
			'default'           => $defaults['tmnhanphat_products_autoplay_delay'],
			'sanitize_callback' => 'absint',
			'input_attrs'       => array( 'min' => 1000, 'max' => 15000, 'step' => 500 ),
		),
		'tmnhanphat_products_transition_speed' => array(
			'label'             => __( 'Transition Speed (ms)', 'tmnhanphat' ),
			'section'           => 'slider',
			'type'              => 'number',
			'default'           => $defaults['tmnhanphat_products_transition_speed'],
			'sanitize_callback' => 'absint',
			'input_attrs'       => array( 'min' => 150, 'max' => 2000, 'step' => 50 ),
		),
		'tmnhanphat_products_infinite'         => array(
			'label'             => __( 'Loop (hết slide cuối quay về đầu)', 'tmnhanphat' ),
			'section'           => 'slider',
			'type'              => 'checkbox',
			'default'           => $defaults['tmnhanphat_products_infinite'],
			'sanitize_callback' => 'tmnhanphat_sanitize_checkbox',
		),
		'tmnhanphat_products_pause_hover'      => array(
			'label'             => __( 'Pause On Hover', 'tmnhanphat' ),
			'section'           => 'slider',
			'type'              => 'checkbox',
			'default'           => $defaults['tmnhanphat_products_pause_hover'],
			'sanitize_callback' => 'tmnhanphat_sanitize_checkbox',
		),
		'tmnhanphat_products_drag_enable'      => array(
			'label'             => __( 'Enable Swipe/Drag', 'tmnhanphat' ),
			'section'           => 'slider',
			'type'              => 'checkbox',
			'default'           => $defaults['tmnhanphat_products_drag_enable'],
			'sanitize_callback' => 'tmnhanphat_sanitize_checkbox',
		),
		'tmnhanphat_products_dots_enable'      => array(
			'label'             => __( 'Enable Dots', 'tmnhanphat' ),
			'section'           => 'slider',
			'type'              => 'checkbox',
			'default'           => $defaults['tmnhanphat_products_dots_enable'],
			'sanitize_callback' => 'tmnhanphat_sanitize_checkbox',
		),
		'tmnhanphat_products_dot_color'        => array(
			'label'             => __( 'Dot Color', 'tmnhanphat' ),
			'section'           => 'slider',
			'type'              => 'color',
			'default'           => $defaults['tmnhanphat_products_dot_color'],
			'sanitize_callback' => 'sanitize_hex_color',
		),
		'tmnhanphat_products_dot_active_color' => array(
			'label'             => __( 'Dot Active Color', 'tmnhanphat' ),
			'section'           => 'slider',
			'type'              => 'color',
			'default'           => $defaults['tmnhanphat_products_dot_active_color'],
			'sanitize_callback' => 'sanitize_hex_color',
		),
		'tmnhanphat_products_dot_size'         => array(
			'label'             => __( 'Dot Size (px)', 'tmnhanphat' ),
			'section'           => 'slider',
			'type'              => 'number',
			'default'           => $defaults['tmnhanphat_products_dot_size'],
			'sanitize_callback' => 'absint',
			'input_attrs'       => array( 'min' => 6, 'max' => 20, 'step' => 1 ),
		),
		'tmnhanphat_products_dot_gap'          => array(
			'label'             => __( 'Dot Gap (px)', 'tmnhanphat' ),
			'section'           => 'slider',
			'type'              => 'number',
			'default'           => $defaults['tmnhanphat_products_dot_gap'],
			'sanitize_callback' => 'absint',
			'input_attrs'       => array( 'min' => 4, 'max' => 24, 'step' => 1 ),
		),
	);
}

/**
 * Đăng ký Panel "Product Home" + toàn bộ Section/Setting/Control.
 *
 * @param WP_Customize_Manager $wp_customize Customizer manager instance.
 */
function tmnhanphat_products_customizer_register( $wp_customize ) {
	$wp_customize->add_panel( 'tmnhanphat_products_panel', array(
		'title'    => __( 'Product Home', 'tmnhanphat' ),
		'priority' => 34,
	) );

	$sections = array(
		'general'     => __( 'General', 'tmnhanphat' ),
		'heading'     => __( 'Heading', 'tmnhanphat' ),
		'description' => __( 'Description', 'tmnhanphat' ),
		'categories'  => __( 'Categories', 'tmnhanphat' ),
		'query'       => __( 'Query', 'tmnhanphat' ),
		'card'        => __( 'Card', 'tmnhanphat' ),
		'slider'      => __( 'Slider', 'tmnhanphat' ),
	);

	foreach ( $sections as $section_id => $section_label ) {
		$wp_customize->add_section( "tmnhanphat_products_section_{$section_id}", array(
			'title' => $section_label,
			'panel' => 'tmnhanphat_products_panel',
		) );
	}

	foreach ( tmnhanphat_get_products_customizer_fields() as $field_id => $field ) {
		$wp_customize->add_setting( $field_id, array(
			'default'           => $field['default'],
			'sanitize_callback' => $field['sanitize_callback'],
			'transport'         => 'refresh',
		) );

		$control_args = array(
			'label'    => $field['label'],
			'section'  => 'tmnhanphat_products_section_' . $field['section'],
			'settings' => $field_id,
		);

		if ( ! empty( $field['choices'] ) ) {
			$control_args['type']    = 'select';
			$control_args['choices'] = $field['choices'];
			$wp_customize->add_control( $field_id, $control_args );
			continue;
		}

		if ( 'image' === $field['type'] ) {
			$wp_customize->add_control( new WP_Customize_Image_Control( $wp_customize, $field_id, $control_args ) );
			continue;
		}

		if ( 'color' === $field['type'] ) {
			$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, $field_id, $control_args ) );
			continue;
		}

		$control_args['type'] = $field['type'];

		if ( ! empty( $field['input_attrs'] ) ) {
			$control_args['input_attrs'] = $field['input_attrs'];
		}

		$wp_customize->add_control( $field_id, $control_args );
	}
}
add_action( 'customize_register', 'tmnhanphat_products_customizer_register' );
