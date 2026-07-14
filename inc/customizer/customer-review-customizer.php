<?php
/**
 * Đăng ký Customizer Panel riêng "Customer Review Home" (PROJECT_RULES.md mục 26 —
 * mỗi feature Homepage 1 Panel cấp cao nhất, priority nối tiếp dải 30-59): General,
 * Header, Query, Layout, Avatar, Name, Role, Comment, Animation. Cùng quy ước
 * config-driven như các module trước (mục 22).
 *
 * Nội dung Review nằm 100% ở CPT customer_review (Title/Editor/Featured Image) —
 * Customizer ở đây CHỈ điều khiển query + cách hiển thị Marquee, không có field
 * nội dung nào (không giống Why Choose/Process — không có Repeater item ở đây).
 *
 * @package TMNhanPhat
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Khai báo toàn bộ field của Customer Review Section dạng config (id => định nghĩa).
 *
 * @return array<string, array>
 */
function tmnhanphat_get_customer_review_customizer_fields() {
	$defaults = tmnhanphat_customer_review_defaults();

	return array(
		// ----- General -----
		'tmnhanphat_review_enable'          => array(
			'label'             => __( 'Hiển thị Customer Review Section', 'tmnhanphat' ),
			'section'           => 'general',
			'type'              => 'checkbox',
			'default'           => $defaults['tmnhanphat_review_enable'],
			'sanitize_callback' => 'tmnhanphat_sanitize_checkbox',
		),
		'tmnhanphat_review_section_id'      => array(
			'label'             => __( 'Section ID (anchor)', 'tmnhanphat' ),
			'section'           => 'general',
			'type'              => 'text',
			'default'           => $defaults['tmnhanphat_review_section_id'],
			'sanitize_callback' => 'sanitize_key',
		),
		'tmnhanphat_review_container_width' => array(
			'label'             => __( 'Container Width (px, 0 = dùng Global Settings)', 'tmnhanphat' ),
			'section'           => 'general',
			'type'              => 'number',
			'default'           => $defaults['tmnhanphat_review_container_width'],
			'sanitize_callback' => 'absint',
			'input_attrs'       => array( 'min' => 0, 'max' => 1920, 'step' => 10 ),
		),
		'tmnhanphat_review_bg_color'        => array(
			'label'             => __( 'Background Color', 'tmnhanphat' ),
			'section'           => 'general',
			'type'              => 'color',
			'default'           => $defaults['tmnhanphat_review_bg_color'],
			'sanitize_callback' => 'sanitize_hex_color',
		),
		'tmnhanphat_review_bg_image'        => array(
			'label'             => __( 'Background Image (tuỳ chọn)', 'tmnhanphat' ),
			'section'           => 'general',
			'type'              => 'image',
			'default'           => $defaults['tmnhanphat_review_bg_image'],
			'sanitize_callback' => 'esc_url_raw',
		),
		'tmnhanphat_review_padding_desktop' => array(
			'label'             => __( 'Section Padding — Desktop (px)', 'tmnhanphat' ),
			'section'           => 'general',
			'type'              => 'number',
			'default'           => $defaults['tmnhanphat_review_padding_desktop'],
			'sanitize_callback' => 'absint',
			'input_attrs'       => array( 'min' => 0, 'max' => 240, 'step' => 4 ),
		),
		'tmnhanphat_review_padding_tablet'  => array(
			'label'             => __( 'Section Padding — Tablet (px)', 'tmnhanphat' ),
			'section'           => 'general',
			'type'              => 'number',
			'default'           => $defaults['tmnhanphat_review_padding_tablet'],
			'sanitize_callback' => 'absint',
			'input_attrs'       => array( 'min' => 0, 'max' => 200, 'step' => 4 ),
		),
		'tmnhanphat_review_padding_mobile'  => array(
			'label'             => __( 'Section Padding — Mobile (px)', 'tmnhanphat' ),
			'section'           => 'general',
			'type'              => 'number',
			'default'           => $defaults['tmnhanphat_review_padding_mobile'],
			'sanitize_callback' => 'absint',
			'input_attrs'       => array( 'min' => 0, 'max' => 160, 'step' => 4 ),
		),
		'tmnhanphat_review_margin_desktop'  => array(
			'label'             => __( 'Section Margin — Desktop (px)', 'tmnhanphat' ),
			'section'           => 'general',
			'type'              => 'number',
			'default'           => $defaults['tmnhanphat_review_margin_desktop'],
			'sanitize_callback' => 'absint',
			'input_attrs'       => array( 'min' => 0, 'max' => 240, 'step' => 4 ),
		),
		'tmnhanphat_review_margin_tablet'   => array(
			'label'             => __( 'Section Margin — Tablet (px)', 'tmnhanphat' ),
			'section'           => 'general',
			'type'              => 'number',
			'default'           => $defaults['tmnhanphat_review_margin_tablet'],
			'sanitize_callback' => 'absint',
			'input_attrs'       => array( 'min' => 0, 'max' => 200, 'step' => 4 ),
		),
		'tmnhanphat_review_margin_mobile'   => array(
			'label'             => __( 'Section Margin — Mobile (px)', 'tmnhanphat' ),
			'section'           => 'general',
			'type'              => 'number',
			'default'           => $defaults['tmnhanphat_review_margin_mobile'],
			'sanitize_callback' => 'absint',
			'input_attrs'       => array( 'min' => 0, 'max' => 160, 'step' => 4 ),
		),

		// ----- Header -----
		'tmnhanphat_review_title'                => array(
			'label'             => __( 'Title', 'tmnhanphat' ),
			'section'           => 'header',
			'type'              => 'text',
			'default'           => $defaults['tmnhanphat_review_title'],
			'sanitize_callback' => 'sanitize_text_field',
		),
		'tmnhanphat_review_title_color'          => array(
			'label'             => __( 'Title Color', 'tmnhanphat' ),
			'section'           => 'header',
			'type'              => 'color',
			'default'           => $defaults['tmnhanphat_review_title_color'],
			'sanitize_callback' => 'sanitize_hex_color',
		),
		'tmnhanphat_review_title_size'           => array(
			'label'             => __( 'Title Font Size — Desktop (px)', 'tmnhanphat' ),
			'section'           => 'header',
			'type'              => 'number',
			'default'           => $defaults['tmnhanphat_review_title_size'],
			'sanitize_callback' => 'absint',
			'input_attrs'       => array( 'min' => 20, 'max' => 72, 'step' => 1 ),
		),
		'tmnhanphat_review_title_size_tablet'    => array(
			'label'             => __( 'Title Font Size — Tablet (px)', 'tmnhanphat' ),
			'section'           => 'header',
			'type'              => 'number',
			'default'           => $defaults['tmnhanphat_review_title_size_tablet'],
			'sanitize_callback' => 'absint',
			'input_attrs'       => array( 'min' => 18, 'max' => 60, 'step' => 1 ),
		),
		'tmnhanphat_review_title_size_mobile'    => array(
			'label'             => __( 'Title Font Size — Mobile (px)', 'tmnhanphat' ),
			'section'           => 'header',
			'type'              => 'number',
			'default'           => $defaults['tmnhanphat_review_title_size_mobile'],
			'sanitize_callback' => 'absint',
			'input_attrs'       => array( 'min' => 16, 'max' => 48, 'step' => 1 ),
		),
		'tmnhanphat_review_title_align'          => array(
			'label'             => __( 'Alignment (áp dụng cho cả Title và Description)', 'tmnhanphat' ),
			'section'           => 'header',
			'choices'           => tmnhanphat_get_services_align_choices(),
			'default'           => $defaults['tmnhanphat_review_title_align'],
			'sanitize_callback' => 'tmnhanphat_sanitize_services_align',
		),
		'tmnhanphat_review_desc_text'            => array(
			'label'             => __( 'Description', 'tmnhanphat' ),
			'section'           => 'header',
			'type'              => 'textarea',
			'default'           => $defaults['tmnhanphat_review_desc_text'],
			'sanitize_callback' => 'sanitize_textarea_field',
		),
		'tmnhanphat_review_desc_color'           => array(
			'label'             => __( 'Description Color', 'tmnhanphat' ),
			'section'           => 'header',
			'type'              => 'color',
			'default'           => $defaults['tmnhanphat_review_desc_color'],
			'sanitize_callback' => 'sanitize_hex_color',
		),
		'tmnhanphat_review_desc_size'            => array(
			'label'             => __( 'Description Font Size — Desktop (px)', 'tmnhanphat' ),
			'section'           => 'header',
			'type'              => 'number',
			'default'           => $defaults['tmnhanphat_review_desc_size'],
			'sanitize_callback' => 'absint',
			'input_attrs'       => array( 'min' => 12, 'max' => 22, 'step' => 1 ),
		),
		'tmnhanphat_review_desc_size_tablet'     => array(
			'label'             => __( 'Description Font Size — Tablet (px)', 'tmnhanphat' ),
			'section'           => 'header',
			'type'              => 'number',
			'default'           => $defaults['tmnhanphat_review_desc_size_tablet'],
			'sanitize_callback' => 'absint',
			'input_attrs'       => array( 'min' => 12, 'max' => 20, 'step' => 1 ),
		),
		'tmnhanphat_review_desc_size_mobile'     => array(
			'label'             => __( 'Description Font Size — Mobile (px)', 'tmnhanphat' ),
			'section'           => 'header',
			'type'              => 'number',
			'default'           => $defaults['tmnhanphat_review_desc_size_mobile'],
			'sanitize_callback' => 'absint',
			'input_attrs'       => array( 'min' => 11, 'max' => 18, 'step' => 1 ),
		),
		'tmnhanphat_review_desc_max_width'       => array(
			'label'             => __( 'Description Max Width (px)', 'tmnhanphat' ),
			'section'           => 'header',
			'type'              => 'number',
			'default'           => $defaults['tmnhanphat_review_desc_max_width'],
			'sanitize_callback' => 'absint',
			'input_attrs'       => array( 'min' => 300, 'max' => 1100, 'step' => 10 ),
		),
		'tmnhanphat_review_header_margin_bottom' => array(
			'label'             => __( 'Header Bottom Spacing (px)', 'tmnhanphat' ),
			'section'           => 'header',
			'type'              => 'number',
			'default'           => $defaults['tmnhanphat_review_header_margin_bottom'],
			'sanitize_callback' => 'absint',
			'input_attrs'       => array( 'min' => 0, 'max' => 120, 'step' => 2 ),
		),

		// ----- Query -----
		'tmnhanphat_review_posts_per_column' => array(
			'label'             => __( 'Posts Per Column', 'tmnhanphat' ),
			'section'           => 'query',
			'type'              => 'number',
			'default'           => $defaults['tmnhanphat_review_posts_per_column'],
			'sanitize_callback' => 'absint',
			'input_attrs'       => array( 'min' => 1, 'max' => 20, 'step' => 1 ),
		),
		'tmnhanphat_review_order'            => array(
			'label'             => __( 'Order', 'tmnhanphat' ),
			'section'           => 'query',
			'choices'           => array(
				'ASC'  => __( 'Tăng dần', 'tmnhanphat' ),
				'DESC' => __( 'Giảm dần', 'tmnhanphat' ),
			),
			'default'           => $defaults['tmnhanphat_review_order'],
			'sanitize_callback' => 'tmnhanphat_sanitize_customer_review_order',
		),
		'tmnhanphat_review_orderby'          => array(
			'label'             => __( 'Order By', 'tmnhanphat' ),
			'section'           => 'query',
			'choices'           => tmnhanphat_get_customer_review_orderby_choices(),
			'default'           => $defaults['tmnhanphat_review_orderby'],
			'sanitize_callback' => 'tmnhanphat_sanitize_customer_review_orderby',
		),
		'tmnhanphat_review_hide_draft'       => array(
			'label'             => __( 'Hide Draft (chỉ hiển thị bài Publish — nên luôn để bật)', 'tmnhanphat' ),
			'section'           => 'query',
			'type'              => 'checkbox',
			'default'           => $defaults['tmnhanphat_review_hide_draft'],
			'sanitize_callback' => 'tmnhanphat_sanitize_checkbox',
		),
		'tmnhanphat_review_hide_empty'       => array(
			'label'             => __( 'Hide Empty (ẩn Review hoàn toàn trống — không Tên, không Comment)', 'tmnhanphat' ),
			'section'           => 'query',
			'type'              => 'checkbox',
			'default'           => $defaults['tmnhanphat_review_hide_empty'],
			'sanitize_callback' => 'tmnhanphat_sanitize_checkbox',
		),

		// ----- Layout -----
		'tmnhanphat_review_cols_desktop'      => array(
			'label'             => __( 'Columns Desktop (tối đa 3)', 'tmnhanphat' ),
			'section'           => 'layout',
			'type'              => 'number',
			'default'           => $defaults['tmnhanphat_review_cols_desktop'],
			'sanitize_callback' => 'absint',
			'input_attrs'       => array( 'min' => 1, 'max' => 3, 'step' => 1 ),
		),
		'tmnhanphat_review_cols_tablet'       => array(
			'label'             => __( 'Columns Tablet (tối đa 3)', 'tmnhanphat' ),
			'section'           => 'layout',
			'type'              => 'number',
			'default'           => $defaults['tmnhanphat_review_cols_tablet'],
			'sanitize_callback' => 'absint',
			'input_attrs'       => array( 'min' => 1, 'max' => 3, 'step' => 1 ),
		),
		'tmnhanphat_review_cols_mobile'       => array(
			'label'             => __( 'Columns Mobile (tối đa 3)', 'tmnhanphat' ),
			'section'           => 'layout',
			'type'              => 'number',
			'default'           => $defaults['tmnhanphat_review_cols_mobile'],
			'sanitize_callback' => 'absint',
			'input_attrs'       => array( 'min' => 1, 'max' => 3, 'step' => 1 ),
		),
		'tmnhanphat_review_card_gap'          => array(
			'label'             => __( 'Card Gap (px — khoảng cách giữa Card trong 1 Column)', 'tmnhanphat' ),
			'section'           => 'layout',
			'type'              => 'number',
			'default'           => $defaults['tmnhanphat_review_card_gap'],
			'sanitize_callback' => 'absint',
			'input_attrs'       => array( 'min' => 4, 'max' => 48, 'step' => 2 ),
		),
		'tmnhanphat_review_column_gap'        => array(
			'label'             => __( 'Column Gap (px — khoảng cách giữa các Column)', 'tmnhanphat' ),
			'section'           => 'layout',
			'type'              => 'number',
			'default'           => $defaults['tmnhanphat_review_column_gap'],
			'sanitize_callback' => 'absint',
			'input_attrs'       => array( 'min' => 0, 'max' => 60, 'step' => 2 ),
		),
		'tmnhanphat_review_card_radius'       => array(
			'label'             => __( 'Card Border Radius (px)', 'tmnhanphat' ),
			'section'           => 'layout',
			'type'              => 'number',
			'default'           => $defaults['tmnhanphat_review_card_radius'],
			'sanitize_callback' => 'absint',
			'input_attrs'       => array( 'min' => 0, 'max' => 32, 'step' => 1 ),
		),
		'tmnhanphat_review_card_border_width' => array(
			'label'             => __( 'Card Border Width (px, 0 = không viền)', 'tmnhanphat' ),
			'section'           => 'layout',
			'type'              => 'number',
			'default'           => $defaults['tmnhanphat_review_card_border_width'],
			'sanitize_callback' => 'absint',
			'input_attrs'       => array( 'min' => 0, 'max' => 4, 'step' => 1 ),
		),
		'tmnhanphat_review_card_border_color' => array(
			'label'             => __( 'Card Border Color', 'tmnhanphat' ),
			'section'           => 'layout',
			'type'              => 'color',
			'default'           => $defaults['tmnhanphat_review_card_border_color'],
			'sanitize_callback' => 'sanitize_hex_color',
		),
		'tmnhanphat_review_card_bg'           => array(
			'label'             => __( 'Card Background Color', 'tmnhanphat' ),
			'section'           => 'layout',
			'type'              => 'color',
			'default'           => $defaults['tmnhanphat_review_card_bg'],
			'sanitize_callback' => 'sanitize_hex_color',
		),
		'tmnhanphat_review_card_shadow'       => array(
			'label'             => __( 'Card Shadow', 'tmnhanphat' ),
			'section'           => 'layout',
			'choices'           => tmnhanphat_get_why_choose_shadow_choices(),
			'default'           => $defaults['tmnhanphat_review_card_shadow'],
			'sanitize_callback' => 'tmnhanphat_sanitize_why_choose_shadow',
		),
		'tmnhanphat_review_card_hover_shadow' => array(
			'label'             => __( 'Hover Shadow', 'tmnhanphat' ),
			'section'           => 'layout',
			'choices'           => tmnhanphat_get_why_choose_shadow_choices(),
			'default'           => $defaults['tmnhanphat_review_card_hover_shadow'],
			'sanitize_callback' => 'tmnhanphat_sanitize_why_choose_shadow',
		),
		'tmnhanphat_review_card_padding'      => array(
			'label'             => __( 'Card Padding (px)', 'tmnhanphat' ),
			'section'           => 'layout',
			'type'              => 'number',
			'default'           => $defaults['tmnhanphat_review_card_padding'],
			'sanitize_callback' => 'absint',
			'input_attrs'       => array( 'min' => 8, 'max' => 48, 'step' => 2 ),
		),

		// ----- Avatar -----
		'tmnhanphat_review_avatar_size'        => array(
			'label'             => __( 'Size — Desktop (px)', 'tmnhanphat' ),
			'section'           => 'avatar',
			'type'              => 'number',
			'default'           => $defaults['tmnhanphat_review_avatar_size'],
			'sanitize_callback' => 'absint',
			'input_attrs'       => array( 'min' => 24, 'max' => 80, 'step' => 2 ),
		),
		'tmnhanphat_review_avatar_size_tablet' => array(
			'label'             => __( 'Size — Tablet (px)', 'tmnhanphat' ),
			'section'           => 'avatar',
			'type'              => 'number',
			'default'           => $defaults['tmnhanphat_review_avatar_size_tablet'],
			'sanitize_callback' => 'absint',
			'input_attrs'       => array( 'min' => 24, 'max' => 72, 'step' => 2 ),
		),
		'tmnhanphat_review_avatar_size_mobile' => array(
			'label'             => __( 'Size — Mobile (px)', 'tmnhanphat' ),
			'section'           => 'avatar',
			'type'              => 'number',
			'default'           => $defaults['tmnhanphat_review_avatar_size_mobile'],
			'sanitize_callback' => 'absint',
			'input_attrs'       => array( 'min' => 20, 'max' => 64, 'step' => 2 ),
		),
		'tmnhanphat_review_avatar_radius'      => array(
			'label'             => __( 'Border Radius (%, 100 = tròn hoàn toàn)', 'tmnhanphat' ),
			'section'           => 'avatar',
			'type'              => 'number',
			'default'           => $defaults['tmnhanphat_review_avatar_radius'],
			'sanitize_callback' => 'absint',
			'input_attrs'       => array( 'min' => 0, 'max' => 100, 'step' => 5 ),
		),
		'tmnhanphat_review_avatar_fit'         => array(
			'label'             => __( 'Object Fit', 'tmnhanphat' ),
			'section'           => 'avatar',
			'choices'           => tmnhanphat_get_why_choose_image_fit_choices(),
			'default'           => $defaults['tmnhanphat_review_avatar_fit'],
			'sanitize_callback' => 'tmnhanphat_sanitize_why_choose_image_fit',
		),

		// ----- Name -----
		'tmnhanphat_review_name_size'   => array(
			'label'             => __( 'Font Size (px)', 'tmnhanphat' ),
			'section'           => 'name',
			'type'              => 'number',
			'default'           => $defaults['tmnhanphat_review_name_size'],
			'sanitize_callback' => 'absint',
			'input_attrs'       => array( 'min' => 12, 'max' => 20, 'step' => 1 ),
		),
		'tmnhanphat_review_name_weight' => array(
			'label'             => __( 'Weight', 'tmnhanphat' ),
			'section'           => 'name',
			'choices'           => tmnhanphat_get_font_weight_choices(),
			'default'           => $defaults['tmnhanphat_review_name_weight'],
			'sanitize_callback' => 'tmnhanphat_sanitize_font_weight',
		),
		'tmnhanphat_review_name_color'  => array(
			'label'             => __( 'Color', 'tmnhanphat' ),
			'section'           => 'name',
			'type'              => 'color',
			'default'           => $defaults['tmnhanphat_review_name_color'],
			'sanitize_callback' => 'sanitize_hex_color',
		),

		// ----- Role -----
		'tmnhanphat_review_role_size'  => array(
			'label'             => __( 'Font Size (px)', 'tmnhanphat' ),
			'section'           => 'role',
			'type'              => 'number',
			'default'           => $defaults['tmnhanphat_review_role_size'],
			'sanitize_callback' => 'absint',
			'input_attrs'       => array( 'min' => 10, 'max' => 16, 'step' => 1 ),
		),
		'tmnhanphat_review_role_color' => array(
			'label'             => __( 'Color', 'tmnhanphat' ),
			'section'           => 'role',
			'type'              => 'color',
			'default'           => $defaults['tmnhanphat_review_role_color'],
			'sanitize_callback' => 'sanitize_hex_color',
		),

		// ----- Comment -----
		'tmnhanphat_review_comment_size'         => array(
			'label'             => __( 'Font Size (px)', 'tmnhanphat' ),
			'section'           => 'comment',
			'type'              => 'number',
			'default'           => $defaults['tmnhanphat_review_comment_size'],
			'sanitize_callback' => 'absint',
			'input_attrs'       => array( 'min' => 11, 'max' => 18, 'step' => 1 ),
		),
		'tmnhanphat_review_comment_color'        => array(
			'label'             => __( 'Color', 'tmnhanphat' ),
			'section'           => 'comment',
			'type'              => 'color',
			'default'           => $defaults['tmnhanphat_review_comment_color'],
			'sanitize_callback' => 'sanitize_hex_color',
		),
		'tmnhanphat_review_comment_clamp'        => array(
			'label'             => __( 'Line Clamp — Desktop/Tablet (0 = không giới hạn)', 'tmnhanphat' ),
			'section'           => 'comment',
			'type'              => 'number',
			'default'           => $defaults['tmnhanphat_review_comment_clamp'],
			'sanitize_callback' => 'absint',
			'input_attrs'       => array( 'min' => 0, 'max' => 10, 'step' => 1 ),
		),
		'tmnhanphat_review_comment_clamp_mobile' => array(
			'label'             => __( 'Line Clamp — Mobile (giảm để tránh Card quá cao)', 'tmnhanphat' ),
			'section'           => 'comment',
			'type'              => 'number',
			'default'           => $defaults['tmnhanphat_review_comment_clamp_mobile'],
			'sanitize_callback' => 'absint',
			'input_attrs'       => array( 'min' => 0, 'max' => 8, 'step' => 1 ),
		),

		// ----- Animation -----
		'tmnhanphat_review_animation_enable' => array(
			'label'             => __( 'Enable Animation', 'tmnhanphat' ),
			'section'           => 'animation',
			'type'              => 'checkbox',
			'default'           => $defaults['tmnhanphat_review_animation_enable'],
			'sanitize_callback' => 'tmnhanphat_sanitize_checkbox',
		),
		'tmnhanphat_review_speed'            => array(
			'label'             => __( 'Animation Speed — Desktop/Tablet (giây/vòng, số nhỏ = nhanh hơn)', 'tmnhanphat' ),
			'section'           => 'animation',
			'type'              => 'number',
			'default'           => $defaults['tmnhanphat_review_speed'],
			'sanitize_callback' => 'absint',
			'input_attrs'       => array( 'min' => 5, 'max' => 120, 'step' => 5 ),
		),
		'tmnhanphat_review_speed_mobile'     => array(
			'label'             => __( 'Animation Speed — Mobile (giây/vòng, nên lớn hơn Desktop)', 'tmnhanphat' ),
			'section'           => 'animation',
			'type'              => 'number',
			'default'           => $defaults['tmnhanphat_review_speed_mobile'],
			'sanitize_callback' => 'absint',
			'input_attrs'       => array( 'min' => 5, 'max' => 150, 'step' => 5 ),
		),
		'tmnhanphat_review_pause_hover'      => array(
			'label'             => __( 'Pause On Hover', 'tmnhanphat' ),
			'section'           => 'animation',
			'type'              => 'checkbox',
			'default'           => $defaults['tmnhanphat_review_pause_hover'],
			'sanitize_callback' => 'tmnhanphat_sanitize_checkbox',
		),
		'tmnhanphat_review_loop'             => array(
			'label'             => __( 'Loop (tắt = chạy 1 vòng rồi dừng)', 'tmnhanphat' ),
			'section'           => 'animation',
			'type'              => 'checkbox',
			'default'           => $defaults['tmnhanphat_review_loop'],
			'sanitize_callback' => 'tmnhanphat_sanitize_checkbox',
		),
		'tmnhanphat_review_col1_direction'   => array(
			'label'             => __( 'Column 1 Direction', 'tmnhanphat' ),
			'section'           => 'animation',
			'choices'           => tmnhanphat_get_customer_review_direction_choices(),
			'default'           => $defaults['tmnhanphat_review_col1_direction'],
			'sanitize_callback' => 'tmnhanphat_sanitize_customer_review_direction',
		),
		'tmnhanphat_review_col2_direction'   => array(
			'label'             => __( 'Column 2 Direction', 'tmnhanphat' ),
			'section'           => 'animation',
			'choices'           => tmnhanphat_get_customer_review_direction_choices(),
			'default'           => $defaults['tmnhanphat_review_col2_direction'],
			'sanitize_callback' => 'tmnhanphat_sanitize_customer_review_direction',
		),
		'tmnhanphat_review_col3_direction'   => array(
			'label'             => __( 'Column 3 Direction', 'tmnhanphat' ),
			'section'           => 'animation',
			'choices'           => tmnhanphat_get_customer_review_direction_choices(),
			'default'           => $defaults['tmnhanphat_review_col3_direction'],
			'sanitize_callback' => 'tmnhanphat_sanitize_customer_review_direction',
		),
		'tmnhanphat_review_mask_enable'      => array(
			'label'             => __( 'Enable Gradient Mask', 'tmnhanphat' ),
			'section'           => 'animation',
			'type'              => 'checkbox',
			'default'           => $defaults['tmnhanphat_review_mask_enable'],
			'sanitize_callback' => 'tmnhanphat_sanitize_checkbox',
		),
		'tmnhanphat_review_mask_fade_top'    => array(
			'label'             => __( 'Fade Top', 'tmnhanphat' ),
			'section'           => 'animation',
			'type'              => 'checkbox',
			'default'           => $defaults['tmnhanphat_review_mask_fade_top'],
			'sanitize_callback' => 'tmnhanphat_sanitize_checkbox',
		),
		'tmnhanphat_review_mask_fade_bottom' => array(
			'label'             => __( 'Fade Bottom', 'tmnhanphat' ),
			'section'           => 'animation',
			'type'              => 'checkbox',
			'default'           => $defaults['tmnhanphat_review_mask_fade_bottom'],
			'sanitize_callback' => 'tmnhanphat_sanitize_checkbox',
		),
		'tmnhanphat_review_mask_height'      => array(
			'label'             => __( 'Gradient Mask Height (px)', 'tmnhanphat' ),
			'section'           => 'animation',
			'type'              => 'number',
			'default'           => $defaults['tmnhanphat_review_mask_height'],
			'sanitize_callback' => 'absint',
			'input_attrs'       => array( 'min' => 20, 'max' => 200, 'step' => 10 ),
		),
	);
}

/**
 * Đăng ký Panel "Customer Review Home" + toàn bộ Section/Setting/Control.
 * Priority 39: nối tiếp Projects Home (38) trong dải Homepage panel.
 *
 * @param WP_Customize_Manager $wp_customize Customizer manager instance.
 */
function tmnhanphat_customer_review_customizer_register( $wp_customize ) {
	$wp_customize->add_panel( 'tmnhanphat_review_panel', array(
		'title'    => __( 'Customer Review Home', 'tmnhanphat' ),
		'priority' => 39,
	) );

	$sections = array(
		'general'   => __( 'General', 'tmnhanphat' ),
		'header'    => __( 'Header', 'tmnhanphat' ),
		'query'     => __( 'Query', 'tmnhanphat' ),
		'layout'    => __( 'Layout', 'tmnhanphat' ),
		'avatar'    => __( 'Avatar', 'tmnhanphat' ),
		'name'      => __( 'Name', 'tmnhanphat' ),
		'role'      => __( 'Role', 'tmnhanphat' ),
		'comment'   => __( 'Comment', 'tmnhanphat' ),
		'animation' => __( 'Animation', 'tmnhanphat' ),
	);

	foreach ( $sections as $section_id => $section_label ) {
		$wp_customize->add_section( "tmnhanphat_review_section_{$section_id}", array(
			'title' => $section_label,
			'panel' => 'tmnhanphat_review_panel',
		) );
	}

	foreach ( tmnhanphat_get_customer_review_customizer_fields() as $field_id => $field ) {
		$wp_customize->add_setting( $field_id, array(
			'default'           => $field['default'],
			'sanitize_callback' => $field['sanitize_callback'],
			'transport'         => 'refresh',
		) );

		$control_args = array(
			'label'    => $field['label'],
			'section'  => 'tmnhanphat_review_section_' . $field['section'],
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
add_action( 'customize_register', 'tmnhanphat_customer_review_customizer_register' );
