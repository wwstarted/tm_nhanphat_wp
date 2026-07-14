<?php
/**
 * Đăng ký Customizer Panel riêng "Projects Home" — Featured Projects "Dự án tiêu biểu"
 * (PROJECT_RULES.md mục 26 — mỗi feature Homepage 1 Panel cấp cao nhất, priority nối
 * tiếp dải 30-59): General, Header, Query, Layout, Project Title, Project Description,
 * Meta, Thumbnail, Slider. Cùng quy ước config-driven như các module trước (mục 22).
 *
 * Dữ liệu là POST THƯỜNG thuộc Category chọn ở đây (WP_Query — KHÔNG CPT/Taxonomy mới,
 * không hardcode ID). Customizer chỉ điều khiển CÁCH hiển thị + query, nội dung dự án
 * nằm trong bài viết.
 *
 * @package TMNhanPhat
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Khai báo toàn bộ field của Projects Section dạng config (id => định nghĩa).
 *
 * @return array<string, array>
 */
function tmnhanphat_get_projects_customizer_fields() {
	$defaults = tmnhanphat_projects_defaults();

	$fields = array(
		// ----- General -----
		'tmnhanphat_projects_enable'          => array(
			'label'             => __( 'Hiển thị Projects Section', 'tmnhanphat' ),
			'section'           => 'general',
			'type'              => 'checkbox',
			'default'           => $defaults['tmnhanphat_projects_enable'],
			'sanitize_callback' => 'tmnhanphat_sanitize_checkbox',
		),
		'tmnhanphat_projects_section_id'      => array(
			'label'             => __( 'Section ID (anchor)', 'tmnhanphat' ),
			'section'           => 'general',
			'type'              => 'text',
			'default'           => $defaults['tmnhanphat_projects_section_id'],
			'sanitize_callback' => 'sanitize_key',
		),
		'tmnhanphat_projects_container_width' => array(
			'label'             => __( 'Container Width (px, 0 = dùng Global Settings)', 'tmnhanphat' ),
			'section'           => 'general',
			'type'              => 'number',
			'default'           => $defaults['tmnhanphat_projects_container_width'],
			'sanitize_callback' => 'absint',
			'input_attrs'       => array( 'min' => 0, 'max' => 1920, 'step' => 10 ),
		),
		'tmnhanphat_projects_bg_color'        => array(
			'label'             => __( 'Background Color (nền xanh)', 'tmnhanphat' ),
			'section'           => 'general',
			'type'              => 'color',
			'default'           => $defaults['tmnhanphat_projects_bg_color'],
			'sanitize_callback' => 'sanitize_hex_color',
		),
		'tmnhanphat_projects_deco_enable'     => array(
			'label'             => __( 'Hiển thị Background Decoration', 'tmnhanphat' ),
			'section'           => 'general',
			'type'              => 'checkbox',
			'default'           => $defaults['tmnhanphat_projects_deco_enable'],
			'sanitize_callback' => 'tmnhanphat_sanitize_checkbox',
		),
		'tmnhanphat_projects_deco_image'      => array(
			'label'             => __( 'Background Decoration Image (trang trí, nằm dưới nội dung)', 'tmnhanphat' ),
			'section'           => 'general',
			'type'              => 'image',
			'default'           => $defaults['tmnhanphat_projects_deco_image'],
			'sanitize_callback' => 'esc_url_raw',
		),
		'tmnhanphat_projects_deco_opacity'    => array(
			'label'             => __( 'Decoration Opacity (%)', 'tmnhanphat' ),
			'section'           => 'general',
			'type'              => 'number',
			'default'           => $defaults['tmnhanphat_projects_deco_opacity'],
			'sanitize_callback' => 'absint',
			'input_attrs'       => array( 'min' => 0, 'max' => 100, 'step' => 5 ),
		),
		'tmnhanphat_projects_deco_position'   => array(
			'label'             => __( 'Decoration Position', 'tmnhanphat' ),
			'section'           => 'general',
			'choices'           => tmnhanphat_get_about_bg_position_choices(),
			'default'           => $defaults['tmnhanphat_projects_deco_position'],
			'sanitize_callback' => 'tmnhanphat_sanitize_about_bg_position',
		),
		'tmnhanphat_projects_deco_size'       => array(
			'label'             => __( 'Decoration Size', 'tmnhanphat' ),
			'section'           => 'general',
			'choices'           => tmnhanphat_get_about_bg_size_choices(),
			'default'           => $defaults['tmnhanphat_projects_deco_size'],
			'sanitize_callback' => 'tmnhanphat_sanitize_about_bg_size',
		),
		'tmnhanphat_projects_padding_desktop' => array(
			'label'             => __( 'Section Padding — Desktop (px)', 'tmnhanphat' ),
			'section'           => 'general',
			'type'              => 'number',
			'default'           => $defaults['tmnhanphat_projects_padding_desktop'],
			'sanitize_callback' => 'absint',
			'input_attrs'       => array( 'min' => 0, 'max' => 240, 'step' => 4 ),
		),
		'tmnhanphat_projects_padding_tablet'  => array(
			'label'             => __( 'Section Padding — Tablet (px)', 'tmnhanphat' ),
			'section'           => 'general',
			'type'              => 'number',
			'default'           => $defaults['tmnhanphat_projects_padding_tablet'],
			'sanitize_callback' => 'absint',
			'input_attrs'       => array( 'min' => 0, 'max' => 200, 'step' => 4 ),
		),
		'tmnhanphat_projects_padding_mobile'  => array(
			'label'             => __( 'Section Padding — Mobile (px)', 'tmnhanphat' ),
			'section'           => 'general',
			'type'              => 'number',
			'default'           => $defaults['tmnhanphat_projects_padding_mobile'],
			'sanitize_callback' => 'absint',
			'input_attrs'       => array( 'min' => 0, 'max' => 160, 'step' => 4 ),
		),
		'tmnhanphat_projects_margin_desktop'  => array(
			'label'             => __( 'Section Margin — Desktop (px)', 'tmnhanphat' ),
			'section'           => 'general',
			'type'              => 'number',
			'default'           => $defaults['tmnhanphat_projects_margin_desktop'],
			'sanitize_callback' => 'absint',
			'input_attrs'       => array( 'min' => 0, 'max' => 240, 'step' => 4 ),
		),
		'tmnhanphat_projects_margin_tablet'   => array(
			'label'             => __( 'Section Margin — Tablet (px)', 'tmnhanphat' ),
			'section'           => 'general',
			'type'              => 'number',
			'default'           => $defaults['tmnhanphat_projects_margin_tablet'],
			'sanitize_callback' => 'absint',
			'input_attrs'       => array( 'min' => 0, 'max' => 200, 'step' => 4 ),
		),
		'tmnhanphat_projects_margin_mobile'   => array(
			'label'             => __( 'Section Margin — Mobile (px)', 'tmnhanphat' ),
			'section'           => 'general',
			'type'              => 'number',
			'default'           => $defaults['tmnhanphat_projects_margin_mobile'],
			'sanitize_callback' => 'absint',
			'input_attrs'       => array( 'min' => 0, 'max' => 160, 'step' => 4 ),
		),

		// ----- Header -----
		'tmnhanphat_projects_title'                => array(
			'label'             => __( 'Title', 'tmnhanphat' ),
			'section'           => 'header',
			'type'              => 'text',
			'default'           => $defaults['tmnhanphat_projects_title'],
			'sanitize_callback' => 'sanitize_text_field',
		),
		'tmnhanphat_projects_title_color'          => array(
			'label'             => __( 'Title Color', 'tmnhanphat' ),
			'section'           => 'header',
			'type'              => 'color',
			'default'           => $defaults['tmnhanphat_projects_title_color'],
			'sanitize_callback' => 'sanitize_hex_color',
		),
		'tmnhanphat_projects_title_size'           => array(
			'label'             => __( 'Title Font Size — Desktop (px)', 'tmnhanphat' ),
			'section'           => 'header',
			'type'              => 'number',
			'default'           => $defaults['tmnhanphat_projects_title_size'],
			'sanitize_callback' => 'absint',
			'input_attrs'       => array( 'min' => 20, 'max' => 72, 'step' => 1 ),
		),
		'tmnhanphat_projects_title_size_tablet'    => array(
			'label'             => __( 'Title Font Size — Tablet (px)', 'tmnhanphat' ),
			'section'           => 'header',
			'type'              => 'number',
			'default'           => $defaults['tmnhanphat_projects_title_size_tablet'],
			'sanitize_callback' => 'absint',
			'input_attrs'       => array( 'min' => 18, 'max' => 60, 'step' => 1 ),
		),
		'tmnhanphat_projects_title_size_mobile'    => array(
			'label'             => __( 'Title Font Size — Mobile (px)', 'tmnhanphat' ),
			'section'           => 'header',
			'type'              => 'number',
			'default'           => $defaults['tmnhanphat_projects_title_size_mobile'],
			'sanitize_callback' => 'absint',
			'input_attrs'       => array( 'min' => 16, 'max' => 48, 'step' => 1 ),
		),
		'tmnhanphat_projects_title_weight'         => array(
			'label'             => __( 'Title Weight', 'tmnhanphat' ),
			'section'           => 'header',
			'choices'           => tmnhanphat_get_font_weight_choices(),
			'default'           => $defaults['tmnhanphat_projects_title_weight'],
			'sanitize_callback' => 'tmnhanphat_sanitize_font_weight',
		),
		'tmnhanphat_projects_title_align'          => array(
			'label'             => __( 'Title Alignment', 'tmnhanphat' ),
			'section'           => 'header',
			'choices'           => tmnhanphat_get_services_align_choices(),
			'default'           => $defaults['tmnhanphat_projects_title_align'],
			'sanitize_callback' => 'tmnhanphat_sanitize_services_align',
		),
		'tmnhanphat_projects_desc_text'            => array(
			'label'             => __( 'Description', 'tmnhanphat' ),
			'section'           => 'header',
			'type'              => 'textarea',
			'default'           => $defaults['tmnhanphat_projects_desc_text'],
			'sanitize_callback' => 'sanitize_textarea_field',
		),
		'tmnhanphat_projects_desc_color'           => array(
			'label'             => __( 'Description Color', 'tmnhanphat' ),
			'section'           => 'header',
			'type'              => 'color',
			'default'           => $defaults['tmnhanphat_projects_desc_color'],
			'sanitize_callback' => 'sanitize_hex_color',
		),
		'tmnhanphat_projects_desc_size'            => array(
			'label'             => __( 'Description Font Size — Desktop (px)', 'tmnhanphat' ),
			'section'           => 'header',
			'type'              => 'number',
			'default'           => $defaults['tmnhanphat_projects_desc_size'],
			'sanitize_callback' => 'absint',
			'input_attrs'       => array( 'min' => 12, 'max' => 24, 'step' => 1 ),
		),
		'tmnhanphat_projects_desc_size_tablet'     => array(
			'label'             => __( 'Description Font Size — Tablet (px)', 'tmnhanphat' ),
			'section'           => 'header',
			'type'              => 'number',
			'default'           => $defaults['tmnhanphat_projects_desc_size_tablet'],
			'sanitize_callback' => 'absint',
			'input_attrs'       => array( 'min' => 12, 'max' => 22, 'step' => 1 ),
		),
		'tmnhanphat_projects_desc_size_mobile'     => array(
			'label'             => __( 'Description Font Size — Mobile (px)', 'tmnhanphat' ),
			'section'           => 'header',
			'type'              => 'number',
			'default'           => $defaults['tmnhanphat_projects_desc_size_mobile'],
			'sanitize_callback' => 'absint',
			'input_attrs'       => array( 'min' => 12, 'max' => 20, 'step' => 1 ),
		),
		'tmnhanphat_projects_desc_max_width'       => array(
			'label'             => __( 'Description Max Width (px)', 'tmnhanphat' ),
			'section'           => 'header',
			'type'              => 'number',
			'default'           => $defaults['tmnhanphat_projects_desc_max_width'],
			'sanitize_callback' => 'absint',
			'input_attrs'       => array( 'min' => 300, 'max' => 1000, 'step' => 10 ),
		),
		'tmnhanphat_projects_desc_clamp'           => array(
			'label'             => __( 'Description Line Clamp (0 = không giới hạn)', 'tmnhanphat' ),
			'section'           => 'header',
			'type'              => 'number',
			'default'           => $defaults['tmnhanphat_projects_desc_clamp'],
			'sanitize_callback' => 'absint',
			'input_attrs'       => array( 'min' => 0, 'max' => 6, 'step' => 1 ),
		),
		'tmnhanphat_projects_desc_align'           => array(
			'label'             => __( 'Description Alignment', 'tmnhanphat' ),
			'section'           => 'header',
			'choices'           => tmnhanphat_get_services_align_choices(),
			'default'           => $defaults['tmnhanphat_projects_desc_align'],
			'sanitize_callback' => 'tmnhanphat_sanitize_services_align',
		),
		'tmnhanphat_projects_header_margin_bottom' => array(
			'label'             => __( 'Header Bottom Spacing (px)', 'tmnhanphat' ),
			'section'           => 'header',
			'type'              => 'number',
			'default'           => $defaults['tmnhanphat_projects_header_margin_bottom'],
			'sanitize_callback' => 'absint',
			'input_attrs'       => array( 'min' => 0, 'max' => 120, 'step' => 2 ),
		),

		// ----- Query -----
		'tmnhanphat_projects_category'    => array(
			'label'             => __( 'Category "Dự án" (bắt buộc — chưa chọn thì section không hiển thị)', 'tmnhanphat' ),
			'section'           => 'query',
			'choices'           => tmnhanphat_get_products_category_choices(),
			'default'           => $defaults['tmnhanphat_projects_category'],
			'sanitize_callback' => 'absint',
		),
		'tmnhanphat_projects_count'       => array(
			'label'             => __( 'Posts Per Page (số dự án trong slider)', 'tmnhanphat' ),
			'section'           => 'query',
			'type'              => 'number',
			'default'           => $defaults['tmnhanphat_projects_count'],
			'sanitize_callback' => 'absint',
			'input_attrs'       => array( 'min' => 1, 'max' => 20, 'step' => 1 ),
		),
		'tmnhanphat_projects_orderby'     => array(
			'label'             => __( 'Order By', 'tmnhanphat' ),
			'section'           => 'query',
			'choices'           => tmnhanphat_get_products_orderby_choices(),
			'default'           => $defaults['tmnhanphat_projects_orderby'],
			'sanitize_callback' => 'tmnhanphat_sanitize_projects_orderby',
		),
		'tmnhanphat_projects_order'       => array(
			'label'             => __( 'Order', 'tmnhanphat' ),
			'section'           => 'query',
			'choices'           => array(
				'DESC' => __( 'Giảm dần (mới nhất trước)', 'tmnhanphat' ),
				'ASC'  => __( 'Tăng dần (cũ nhất trước)', 'tmnhanphat' ),
			),
			'default'           => $defaults['tmnhanphat_projects_order'],
			'sanitize_callback' => 'tmnhanphat_sanitize_projects_order',
		),
		'tmnhanphat_projects_exclude_ids' => array(
			'label'             => __( 'Exclude Post IDs (CSV, ví dụ: 12,34)', 'tmnhanphat' ),
			'section'           => 'query',
			'type'              => 'text',
			'default'           => $defaults['tmnhanphat_projects_exclude_ids'],
			'sanitize_callback' => 'tmnhanphat_sanitize_id_list',
		),

		// ----- Content Layout -----
		'tmnhanphat_projects_left_width'   => array(
			'label'             => __( 'Left Width (% — cột nội dung, mặc định 65)', 'tmnhanphat' ),
			'section'           => 'layout',
			'type'              => 'number',
			'default'           => $defaults['tmnhanphat_projects_left_width'],
			'sanitize_callback' => 'absint',
			'input_attrs'       => array( 'min' => 40, 'max' => 80, 'step' => 1 ),
		),
		'tmnhanphat_projects_content_gap'  => array(
			'label'             => __( 'Content Gap (px giữa 2 cột)', 'tmnhanphat' ),
			'section'           => 'layout',
			'type'              => 'number',
			'default'           => $defaults['tmnhanphat_projects_content_gap'],
			'sanitize_callback' => 'absint',
			'input_attrs'       => array( 'min' => 0, 'max' => 100, 'step' => 4 ),
		),
		'tmnhanphat_projects_valign'       => array(
			'label'             => __( 'Vertical Alignment (căn dọc 2 cột)', 'tmnhanphat' ),
			'section'           => 'layout',
			'choices'           => tmnhanphat_get_why_choose_content_align_choices(),
			'default'           => $defaults['tmnhanphat_projects_valign'],
			'sanitize_callback' => 'tmnhanphat_sanitize_projects_valign',
		),
		'tmnhanphat_projects_card_bg'      => array(
			'label'             => __( 'Card Background Color', 'tmnhanphat' ),
			'section'           => 'layout',
			'type'              => 'color',
			'default'           => $defaults['tmnhanphat_projects_card_bg'],
			'sanitize_callback' => 'sanitize_hex_color',
		),
		'tmnhanphat_projects_card_opacity' => array(
			'label'             => __( 'Card Background Opacity (% — lớp kính mờ trên nền xanh)', 'tmnhanphat' ),
			'section'           => 'layout',
			'type'              => 'number',
			'default'           => $defaults['tmnhanphat_projects_card_opacity'],
			'sanitize_callback' => 'absint',
			'input_attrs'       => array( 'min' => 0, 'max' => 100, 'step' => 1 ),
		),
		'tmnhanphat_projects_card_radius'  => array(
			'label'             => __( 'Card Border Radius (px)', 'tmnhanphat' ),
			'section'           => 'layout',
			'type'              => 'number',
			'default'           => $defaults['tmnhanphat_projects_card_radius'],
			'sanitize_callback' => 'absint',
			'input_attrs'       => array( 'min' => 0, 'max' => 48, 'step' => 2 ),
		),
		'tmnhanphat_projects_card_padding' => array(
			'label'             => __( 'Card Padding (px)', 'tmnhanphat' ),
			'section'           => 'layout',
			'type'              => 'number',
			'default'           => $defaults['tmnhanphat_projects_card_padding'],
			'sanitize_callback' => 'absint',
			'input_attrs'       => array( 'min' => 16, 'max' => 80, 'step' => 4 ),
		),

		// ----- Project Title -----
		'tmnhanphat_projects_ptitle_size'          => array(
			'label'             => __( 'Font Size — Desktop (px)', 'tmnhanphat' ),
			'section'           => 'ptitle',
			'type'              => 'number',
			'default'           => $defaults['tmnhanphat_projects_ptitle_size'],
			'sanitize_callback' => 'absint',
			'input_attrs'       => array( 'min' => 16, 'max' => 48, 'step' => 1 ),
		),
		'tmnhanphat_projects_ptitle_size_tablet'   => array(
			'label'             => __( 'Font Size — Tablet (px)', 'tmnhanphat' ),
			'section'           => 'ptitle',
			'type'              => 'number',
			'default'           => $defaults['tmnhanphat_projects_ptitle_size_tablet'],
			'sanitize_callback' => 'absint',
			'input_attrs'       => array( 'min' => 15, 'max' => 40, 'step' => 1 ),
		),
		'tmnhanphat_projects_ptitle_size_mobile'   => array(
			'label'             => __( 'Font Size — Mobile (px)', 'tmnhanphat' ),
			'section'           => 'ptitle',
			'type'              => 'number',
			'default'           => $defaults['tmnhanphat_projects_ptitle_size_mobile'],
			'sanitize_callback' => 'absint',
			'input_attrs'       => array( 'min' => 14, 'max' => 32, 'step' => 1 ),
		),
		'tmnhanphat_projects_ptitle_weight'        => array(
			'label'             => __( 'Weight', 'tmnhanphat' ),
			'section'           => 'ptitle',
			'choices'           => tmnhanphat_get_font_weight_choices(),
			'default'           => $defaults['tmnhanphat_projects_ptitle_weight'],
			'sanitize_callback' => 'tmnhanphat_sanitize_font_weight',
		),
		'tmnhanphat_projects_ptitle_color'         => array(
			'label'             => __( 'Color', 'tmnhanphat' ),
			'section'           => 'ptitle',
			'type'              => 'color',
			'default'           => $defaults['tmnhanphat_projects_ptitle_color'],
			'sanitize_callback' => 'sanitize_hex_color',
		),
		'tmnhanphat_projects_ptitle_clamp'         => array(
			'label'             => __( 'Line Clamp (0 = không giới hạn)', 'tmnhanphat' ),
			'section'           => 'ptitle',
			'type'              => 'number',
			'default'           => $defaults['tmnhanphat_projects_ptitle_clamp'],
			'sanitize_callback' => 'absint',
			'input_attrs'       => array( 'min' => 0, 'max' => 4, 'step' => 1 ),
		),
		'tmnhanphat_projects_ptitle_margin_bottom' => array(
			'label'             => __( 'Spacing Bottom (px)', 'tmnhanphat' ),
			'section'           => 'ptitle',
			'type'              => 'number',
			'default'           => $defaults['tmnhanphat_projects_ptitle_margin_bottom'],
			'sanitize_callback' => 'absint',
			'input_attrs'       => array( 'min' => 0, 'max' => 40, 'step' => 1 ),
		),

		// ----- Project Description -----
		'tmnhanphat_projects_pdesc_size'          => array(
			'label'             => __( 'Font Size — Desktop (px)', 'tmnhanphat' ),
			'section'           => 'pdesc',
			'type'              => 'number',
			'default'           => $defaults['tmnhanphat_projects_pdesc_size'],
			'sanitize_callback' => 'absint',
			'input_attrs'       => array( 'min' => 11, 'max' => 20, 'step' => 1 ),
		),
		'tmnhanphat_projects_pdesc_size_tablet'   => array(
			'label'             => __( 'Font Size — Tablet (px)', 'tmnhanphat' ),
			'section'           => 'pdesc',
			'type'              => 'number',
			'default'           => $defaults['tmnhanphat_projects_pdesc_size_tablet'],
			'sanitize_callback' => 'absint',
			'input_attrs'       => array( 'min' => 11, 'max' => 18, 'step' => 1 ),
		),
		'tmnhanphat_projects_pdesc_size_mobile'   => array(
			'label'             => __( 'Font Size — Mobile (px)', 'tmnhanphat' ),
			'section'           => 'pdesc',
			'type'              => 'number',
			'default'           => $defaults['tmnhanphat_projects_pdesc_size_mobile'],
			'sanitize_callback' => 'absint',
			'input_attrs'       => array( 'min' => 10, 'max' => 16, 'step' => 1 ),
		),
		'tmnhanphat_projects_pdesc_color'         => array(
			'label'             => __( 'Color', 'tmnhanphat' ),
			'section'           => 'pdesc',
			'type'              => 'color',
			'default'           => $defaults['tmnhanphat_projects_pdesc_color'],
			'sanitize_callback' => 'sanitize_hex_color',
		),
		'tmnhanphat_projects_pdesc_clamp'         => array(
			'label'             => __( 'Line Clamp (0 = không giới hạn)', 'tmnhanphat' ),
			'section'           => 'pdesc',
			'type'              => 'number',
			'default'           => $defaults['tmnhanphat_projects_pdesc_clamp'],
			'sanitize_callback' => 'absint',
			'input_attrs'       => array( 'min' => 0, 'max' => 8, 'step' => 1 ),
		),
		'tmnhanphat_projects_pdesc_margin_bottom' => array(
			'label'             => __( 'Spacing Bottom (px)', 'tmnhanphat' ),
			'section'           => 'pdesc',
			'type'              => 'number',
			'default'           => $defaults['tmnhanphat_projects_pdesc_margin_bottom'],
			'sanitize_callback' => 'absint',
			'input_attrs'       => array( 'min' => 0, 'max' => 60, 'step' => 2 ),
		),

		// ----- Meta -----
		'tmnhanphat_projects_meta_enable'          => array(
			'label'             => __( 'Enable Meta', 'tmnhanphat' ),
			'section'           => 'meta',
			'type'              => 'checkbox',
			'default'           => $defaults['tmnhanphat_projects_meta_enable'],
			'sanitize_callback' => 'tmnhanphat_sanitize_checkbox',
		),
		'tmnhanphat_projects_divider_enable'       => array(
			'label'             => __( 'Show Divider (đường kẻ trên mỗi Meta)', 'tmnhanphat' ),
			'section'           => 'meta',
			'type'              => 'checkbox',
			'default'           => $defaults['tmnhanphat_projects_divider_enable'],
			'sanitize_callback' => 'tmnhanphat_sanitize_checkbox',
		),
		'tmnhanphat_projects_divider_color'        => array(
			'label'             => __( 'Divider Color', 'tmnhanphat' ),
			'section'           => 'meta',
			'type'              => 'color',
			'default'           => $defaults['tmnhanphat_projects_divider_color'],
			'sanitize_callback' => 'sanitize_hex_color',
		),
		'tmnhanphat_projects_divider_width'        => array(
			'label'             => __( 'Divider Width (px)', 'tmnhanphat' ),
			'section'           => 'meta',
			'type'              => 'number',
			'default'           => $defaults['tmnhanphat_projects_divider_width'],
			'sanitize_callback' => 'absint',
			'input_attrs'       => array( 'min' => 1, 'max' => 4, 'step' => 1 ),
		),
		'tmnhanphat_projects_meta_label_color'     => array(
			'label'             => __( 'Label Color', 'tmnhanphat' ),
			'section'           => 'meta',
			'type'              => 'color',
			'default'           => $defaults['tmnhanphat_projects_meta_label_color'],
			'sanitize_callback' => 'sanitize_hex_color',
		),
		'tmnhanphat_projects_meta_value_color'     => array(
			'label'             => __( 'Value Color', 'tmnhanphat' ),
			'section'           => 'meta',
			'type'              => 'color',
			'default'           => $defaults['tmnhanphat_projects_meta_value_color'],
			'sanitize_callback' => 'sanitize_hex_color',
		),
		'tmnhanphat_projects_meta_label_size'      => array(
			'label'             => __( 'Label Font Size (px)', 'tmnhanphat' ),
			'section'           => 'meta',
			'type'              => 'number',
			'default'           => $defaults['tmnhanphat_projects_meta_label_size'],
			'sanitize_callback' => 'absint',
			'input_attrs'       => array( 'min' => 10, 'max' => 18, 'step' => 1 ),
		),
		'tmnhanphat_projects_meta_value_size'      => array(
			'label'             => __( 'Value Font Size (px)', 'tmnhanphat' ),
			'section'           => 'meta',
			'type'              => 'number',
			'default'           => $defaults['tmnhanphat_projects_meta_value_size'],
			'sanitize_callback' => 'absint',
			'input_attrs'       => array( 'min' => 12, 'max' => 28, 'step' => 1 ),
		),
		'tmnhanphat_projects_meta_gap'             => array(
			'label'             => __( 'Meta Gap (px giữa các Meta)', 'tmnhanphat' ),
			'section'           => 'meta',
			'type'              => 'number',
			'default'           => $defaults['tmnhanphat_projects_meta_gap'],
			'sanitize_callback' => 'absint',
			'input_attrs'       => array( 'min' => 4, 'max' => 48, 'step' => 2 ),
		),
		'tmnhanphat_projects_meta_fallback_enable' => array(
			'label'             => __( 'Fallback Enable (meta rỗng hiển thị giá trị mặc định — tắt thì ẨN meta rỗng)', 'tmnhanphat' ),
			'section'           => 'meta',
			'type'              => 'checkbox',
			'default'           => $defaults['tmnhanphat_projects_meta_fallback_enable'],
			'sanitize_callback' => 'tmnhanphat_sanitize_checkbox',
		),

		// ----- Thumbnail -----
		'tmnhanphat_projects_thumb_width'  => array(
			'label'             => __( 'Image Width (px, 0 = auto theo cột phải)', 'tmnhanphat' ),
			'section'           => 'thumbnail',
			'type'              => 'number',
			'default'           => $defaults['tmnhanphat_projects_thumb_width'],
			'sanitize_callback' => 'absint',
			'input_attrs'       => array( 'min' => 0, 'max' => 900, 'step' => 10 ),
		),
		'tmnhanphat_projects_thumb_height' => array(
			'label'             => __( 'Image Height (px — khung cố định)', 'tmnhanphat' ),
			'section'           => 'thumbnail',
			'type'              => 'number',
			'default'           => $defaults['tmnhanphat_projects_thumb_height'],
			'sanitize_callback' => 'absint',
			'input_attrs'       => array( 'min' => 200, 'max' => 700, 'step' => 10 ),
		),
		'tmnhanphat_projects_thumb_radius' => array(
			'label'             => __( 'Image Radius (px)', 'tmnhanphat' ),
			'section'           => 'thumbnail',
			'type'              => 'number',
			'default'           => $defaults['tmnhanphat_projects_thumb_radius'],
			'sanitize_callback' => 'absint',
			'input_attrs'       => array( 'min' => 0, 'max' => 40, 'step' => 1 ),
		),
		'tmnhanphat_projects_thumb_fit'    => array(
			'label'             => __( 'Object Fit', 'tmnhanphat' ),
			'section'           => 'thumbnail',
			'choices'           => tmnhanphat_get_why_choose_image_fit_choices(),
			'default'           => $defaults['tmnhanphat_projects_thumb_fit'],
			'sanitize_callback' => 'tmnhanphat_sanitize_why_choose_image_fit',
		),
		'tmnhanphat_projects_thumb_shadow' => array(
			'label'             => __( 'Image Shadow', 'tmnhanphat' ),
			'section'           => 'thumbnail',
			'choices'           => tmnhanphat_get_why_choose_shadow_choices(),
			'default'           => $defaults['tmnhanphat_projects_thumb_shadow'],
			'sanitize_callback' => 'tmnhanphat_sanitize_why_choose_shadow',
		),
		'tmnhanphat_projects_thumb_zoom'   => array(
			'label'             => __( 'Image Hover Zoom', 'tmnhanphat' ),
			'section'           => 'thumbnail',
			'type'              => 'checkbox',
			'default'           => $defaults['tmnhanphat_projects_thumb_zoom'],
			'sanitize_callback' => 'tmnhanphat_sanitize_checkbox',
		),

		// ----- Slider -----
		'tmnhanphat_projects_autoplay_enable'  => array(
			'label'             => __( 'Enable Autoplay', 'tmnhanphat' ),
			'section'           => 'slider',
			'type'              => 'checkbox',
			'default'           => $defaults['tmnhanphat_projects_autoplay_enable'],
			'sanitize_callback' => 'tmnhanphat_sanitize_checkbox',
		),
		'tmnhanphat_projects_autoplay_speed'   => array(
			'label'             => __( 'Autoplay Delay (ms)', 'tmnhanphat' ),
			'section'           => 'slider',
			'type'              => 'number',
			'default'           => $defaults['tmnhanphat_projects_autoplay_speed'],
			'sanitize_callback' => 'absint',
			'input_attrs'       => array( 'min' => 1000, 'max' => 15000, 'step' => 500 ),
		),
		'tmnhanphat_projects_transition_speed' => array(
			'label'             => __( 'Transition Speed (ms)', 'tmnhanphat' ),
			'section'           => 'slider',
			'type'              => 'number',
			'default'           => $defaults['tmnhanphat_projects_transition_speed'],
			'sanitize_callback' => 'absint',
			'input_attrs'       => array( 'min' => 150, 'max' => 2000, 'step' => 50 ),
		),
		'tmnhanphat_projects_infinite'         => array(
			'label'             => __( 'Loop (Infinite)', 'tmnhanphat' ),
			'section'           => 'slider',
			'type'              => 'checkbox',
			'default'           => $defaults['tmnhanphat_projects_infinite'],
			'sanitize_callback' => 'tmnhanphat_sanitize_checkbox',
		),
		'tmnhanphat_projects_pause_hover'      => array(
			'label'             => __( 'Pause On Hover', 'tmnhanphat' ),
			'section'           => 'slider',
			'type'              => 'checkbox',
			'default'           => $defaults['tmnhanphat_projects_pause_hover'],
			'sanitize_callback' => 'tmnhanphat_sanitize_checkbox',
		),
		'tmnhanphat_projects_drag_enable'      => array(
			'label'             => __( 'Enable Swipe/Drag (chuột + cảm ứng)', 'tmnhanphat' ),
			'section'           => 'slider',
			'type'              => 'checkbox',
			'default'           => $defaults['tmnhanphat_projects_drag_enable'],
			'sanitize_callback' => 'tmnhanphat_sanitize_checkbox',
		),
		'tmnhanphat_projects_show_dots'        => array(
			'label'             => __( 'Show Dot', 'tmnhanphat' ),
			'section'           => 'slider',
			'type'              => 'checkbox',
			'default'           => $defaults['tmnhanphat_projects_show_dots'],
			'sanitize_callback' => 'tmnhanphat_sanitize_checkbox',
		),
		'tmnhanphat_projects_dot_size'         => array(
			'label'             => __( 'Dot Size (px)', 'tmnhanphat' ),
			'section'           => 'slider',
			'type'              => 'number',
			'default'           => $defaults['tmnhanphat_projects_dot_size'],
			'sanitize_callback' => 'absint',
			'input_attrs'       => array( 'min' => 6, 'max' => 20, 'step' => 1 ),
		),
		'tmnhanphat_projects_dot_gap'          => array(
			'label'             => __( 'Dot Gap (px)', 'tmnhanphat' ),
			'section'           => 'slider',
			'type'              => 'number',
			'default'           => $defaults['tmnhanphat_projects_dot_gap'],
			'sanitize_callback' => 'absint',
			'input_attrs'       => array( 'min' => 4, 'max' => 30, 'step' => 1 ),
		),
		'tmnhanphat_projects_dot_color'        => array(
			'label'             => __( 'Dot Color', 'tmnhanphat' ),
			'section'           => 'slider',
			'type'              => 'color',
			'default'           => $defaults['tmnhanphat_projects_dot_color'],
			'sanitize_callback' => 'sanitize_hex_color',
		),
		'tmnhanphat_projects_dot_active_color' => array(
			'label'             => __( 'Dot Active Color', 'tmnhanphat' ),
			'section'           => 'slider',
			'type'              => 'color',
			'default'           => $defaults['tmnhanphat_projects_dot_active_color'],
			'sanitize_callback' => 'sanitize_hex_color',
		),
	);

	// 3 nhóm Meta: Label + Meta Key + Fallback (chỉnh được từng Meta).
	for ( $i = 1; $i <= tmnhanphat_get_projects_meta_count(); $i++ ) {
		$fields[ "tmnhanphat_projects_meta{$i}_label" ]    = array(
			/* translators: %d: số thứ tự Meta (1-3) */
			'label'             => sprintf( __( 'Meta %d: Label', 'tmnhanphat' ), $i ),
			'section'           => 'meta',
			'type'              => 'text',
			'default'           => $defaults[ "tmnhanphat_projects_meta{$i}_label" ],
			'sanitize_callback' => 'sanitize_text_field',
		);
		$fields[ "tmnhanphat_projects_meta{$i}_key" ]      = array(
			/* translators: %d: số thứ tự Meta (1-3) */
			'label'             => sprintf( __( 'Meta %d: Meta Key (đổi được sang key ACF)', 'tmnhanphat' ), $i ),
			'section'           => 'meta',
			'type'              => 'text',
			'default'           => $defaults[ "tmnhanphat_projects_meta{$i}_key" ],
			'sanitize_callback' => 'sanitize_text_field',
		);
		$fields[ "tmnhanphat_projects_meta{$i}_fallback" ] = array(
			/* translators: %d: số thứ tự Meta (1-3) */
			'label'             => sprintf( __( 'Meta %d: Fallback Text (hiển thị khi meta rỗng — không ghi vào database)', 'tmnhanphat' ), $i ),
			'section'           => 'meta',
			'type'              => 'text',
			'default'           => $defaults[ "tmnhanphat_projects_meta{$i}_fallback" ],
			'sanitize_callback' => 'sanitize_text_field',
		);
	}

	return $fields;
}

/**
 * Đăng ký Panel "Projects Home" + toàn bộ Section/Setting/Control.
 * Priority 38: nối tiếp Process Home (37) trong dải Homepage panel.
 *
 * @param WP_Customize_Manager $wp_customize Customizer manager instance.
 */
function tmnhanphat_projects_customizer_register( $wp_customize ) {
	$wp_customize->add_panel( 'tmnhanphat_projects_panel', array(
		'title'    => __( 'Projects Home', 'tmnhanphat' ),
		'priority' => 38,
	) );

	$sections = array(
		'general'   => __( 'General', 'tmnhanphat' ),
		'header'    => __( 'Header', 'tmnhanphat' ),
		'query'     => __( 'Query', 'tmnhanphat' ),
		'layout'    => __( 'Content Layout', 'tmnhanphat' ),
		'ptitle'    => __( 'Project Title', 'tmnhanphat' ),
		'pdesc'     => __( 'Project Description', 'tmnhanphat' ),
		'meta'      => __( 'Meta Box', 'tmnhanphat' ),
		'thumbnail' => __( 'Thumbnail', 'tmnhanphat' ),
		'slider'    => __( 'Slider', 'tmnhanphat' ),
	);

	foreach ( $sections as $section_id => $section_label ) {
		$wp_customize->add_section( "tmnhanphat_projects_section_{$section_id}", array(
			'title' => $section_label,
			'panel' => 'tmnhanphat_projects_panel',
		) );
	}

	foreach ( tmnhanphat_get_projects_customizer_fields() as $field_id => $field ) {
		$wp_customize->add_setting( $field_id, array(
			'default'           => $field['default'],
			'sanitize_callback' => $field['sanitize_callback'],
			'transport'         => 'refresh',
		) );

		$control_args = array(
			'label'    => $field['label'],
			'section'  => 'tmnhanphat_projects_section_' . $field['section'],
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
add_action( 'customize_register', 'tmnhanphat_projects_customizer_register' );
