<?php
/**
 * Đăng ký Customizer Panel riêng "Why Choose Home" (PROJECT_RULES.md mục 26 — mỗi feature
 * Homepage 1 Panel cấp cao nhất, priority nối tiếp dải 30-59): General, Decoration, Heading,
 * Description, Slider, Card, Item 1-8. Cùng quy ước config-driven như các module trước
 * (mục 22). Toàn bộ NỘI DUNG section nằm trong Customizer (fixed-slot repeater, mục 24) —
 * KHÔNG dùng WP_Query/ACF/Meta Box.
 *
 * Slider của section này KHÔNG có Dots/Arrows (yêu cầu design): chỉ autoplay tự trượt +
 * drag/swipe — nên không có nhóm setting Dot nào ở đây.
 *
 * @package TMNhanPhat
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Khai báo field cho 1 Feature Item slot (dùng lại N lần — DRY).
 *
 * @param int   $index    Số thứ tự slot (1..tmnhanphat_get_why_choose_slot_count()).
 * @param array $defaults Mảng defaults từ tmnhanphat_why_choose_defaults().
 * @return array<string, array>
 */
function tmnhanphat_get_why_choose_item_fields( $index, $defaults ) {
	$section = "item_{$index}";

	return array(
		"tmnhanphat_why_choose_item{$index}_enable"      => array(
			/* translators: %d: số thứ tự Feature Item (1-8) */
			'label'             => sprintf( __( 'Hiển thị Item %d', 'tmnhanphat' ), $index ),
			'section'           => $section,
			'type'              => 'checkbox',
			'default'           => $defaults[ "tmnhanphat_why_choose_item{$index}_enable" ],
			'sanitize_callback' => 'tmnhanphat_sanitize_checkbox',
		),
		"tmnhanphat_why_choose_item{$index}_title"       => array(
			'label'             => __( 'Title', 'tmnhanphat' ),
			'section'           => $section,
			'type'              => 'text',
			'default'           => $defaults[ "tmnhanphat_why_choose_item{$index}_title" ],
			'sanitize_callback' => 'sanitize_text_field',
		),
		"tmnhanphat_why_choose_item{$index}_description" => array(
			'label'             => __( 'Description', 'tmnhanphat' ),
			'section'           => $section,
			'type'              => 'textarea',
			'default'           => $defaults[ "tmnhanphat_why_choose_item{$index}_description" ],
			'sanitize_callback' => 'sanitize_textarea_field',
		),
		"tmnhanphat_why_choose_item{$index}_icon"        => array(
			'label'             => __( 'Icon (tuỳ chọn — bỏ trống dùng icon mặc định của theme)', 'tmnhanphat' ),
			'section'           => $section,
			'type'              => 'image',
			'default'           => $defaults[ "tmnhanphat_why_choose_item{$index}_icon" ],
			'sanitize_callback' => 'esc_url_raw',
		),
		"tmnhanphat_why_choose_item{$index}_image"       => array(
			'label'             => __( 'Image (423×290)', 'tmnhanphat' ),
			'section'           => $section,
			'type'              => 'image',
			'default'           => $defaults[ "tmnhanphat_why_choose_item{$index}_image" ],
			'sanitize_callback' => 'esc_url_raw',
		),
		"tmnhanphat_why_choose_item{$index}_btn_enable"  => array(
			'label'             => __( 'Button: Enable', 'tmnhanphat' ),
			'section'           => $section,
			'type'              => 'checkbox',
			'default'           => $defaults[ "tmnhanphat_why_choose_item{$index}_btn_enable" ],
			'sanitize_callback' => 'tmnhanphat_sanitize_checkbox',
		),
		"tmnhanphat_why_choose_item{$index}_btn_text"    => array(
			'label'             => __( 'Button: Text', 'tmnhanphat' ),
			'section'           => $section,
			'type'              => 'text',
			'default'           => $defaults[ "tmnhanphat_why_choose_item{$index}_btn_text" ],
			'sanitize_callback' => 'sanitize_text_field',
		),
		"tmnhanphat_why_choose_item{$index}_btn_url"     => array(
			'label'             => __( 'Button: URL', 'tmnhanphat' ),
			'section'           => $section,
			'type'              => 'text',
			'default'           => $defaults[ "tmnhanphat_why_choose_item{$index}_btn_url" ],
			'sanitize_callback' => 'esc_url_raw',
		),
		"tmnhanphat_why_choose_item{$index}_btn_new_tab" => array(
			'label'             => __( 'Button: Open New Tab', 'tmnhanphat' ),
			'section'           => $section,
			'type'              => 'checkbox',
			'default'           => $defaults[ "tmnhanphat_why_choose_item{$index}_btn_new_tab" ],
			'sanitize_callback' => 'tmnhanphat_sanitize_checkbox',
		),
		"tmnhanphat_why_choose_item{$index}_order"       => array(
			'label'             => __( 'Display Order', 'tmnhanphat' ),
			'section'           => $section,
			'type'              => 'number',
			'default'           => $defaults[ "tmnhanphat_why_choose_item{$index}_order" ],
			'sanitize_callback' => 'absint',
			'input_attrs'       => array( 'min' => 0, 'max' => 99, 'step' => 1 ),
		),
	);
}

/**
 * Khai báo toàn bộ field của Why Choose Section dạng config (id => định nghĩa).
 *
 * Khác with about-customizer.php: các field cho phép số ÂM/THẬP PHÂN khai báo THẲNG
 * sanitize callback thật trong config (không override ngầm ở register) — tránh config
 * "nói dối" callback (bài học từ audit About).
 *
 * @return array<string, array>
 */
function tmnhanphat_get_why_choose_customizer_fields() {
	$defaults = tmnhanphat_why_choose_defaults();

	$fields = array(
		// ----- General -----
		'tmnhanphat_why_choose_enable'          => array(
			'label'             => __( 'Hiển thị Why Choose Section', 'tmnhanphat' ),
			'section'           => 'general',
			'type'              => 'checkbox',
			'default'           => $defaults['tmnhanphat_why_choose_enable'],
			'sanitize_callback' => 'tmnhanphat_sanitize_checkbox',
		),
		'tmnhanphat_why_choose_section_id'      => array(
			'label'             => __( 'Section ID (anchor)', 'tmnhanphat' ),
			'section'           => 'general',
			'type'              => 'text',
			'default'           => $defaults['tmnhanphat_why_choose_section_id'],
			'sanitize_callback' => 'sanitize_key',
		),
		'tmnhanphat_why_choose_container_width' => array(
			'label'             => __( 'Container Width (px, 0 = dùng Global Settings)', 'tmnhanphat' ),
			'section'           => 'general',
			'type'              => 'number',
			'default'           => $defaults['tmnhanphat_why_choose_container_width'],
			'sanitize_callback' => 'absint',
			'input_attrs'       => array( 'min' => 0, 'max' => 1920, 'step' => 10 ),
		),
		'tmnhanphat_why_choose_bg_color'        => array(
			'label'             => __( 'Background Color', 'tmnhanphat' ),
			'section'           => 'general',
			'type'              => 'color',
			'default'           => $defaults['tmnhanphat_why_choose_bg_color'],
			'sanitize_callback' => 'sanitize_hex_color',
		),
		'tmnhanphat_why_choose_bg_image'        => array(
			'label'             => __( 'Background Image (1920×1291, phủ toàn section)', 'tmnhanphat' ),
			'section'           => 'general',
			'type'              => 'image',
			'default'           => $defaults['tmnhanphat_why_choose_bg_image'],
			'sanitize_callback' => 'esc_url_raw',
		),
		'tmnhanphat_why_choose_overlay_enable'  => array(
			'label'             => __( 'Enable Background Overlay', 'tmnhanphat' ),
			'section'           => 'general',
			'type'              => 'checkbox',
			'default'           => $defaults['tmnhanphat_why_choose_overlay_enable'],
			'sanitize_callback' => 'tmnhanphat_sanitize_checkbox',
		),
		'tmnhanphat_why_choose_overlay_color'   => array(
			'label'             => __( 'Overlay Color', 'tmnhanphat' ),
			'section'           => 'general',
			'type'              => 'color',
			'default'           => $defaults['tmnhanphat_why_choose_overlay_color'],
			'sanitize_callback' => 'sanitize_hex_color',
		),
		'tmnhanphat_why_choose_overlay_opacity' => array(
			'label'             => __( 'Overlay Opacity (%)', 'tmnhanphat' ),
			'section'           => 'general',
			'type'              => 'number',
			'default'           => $defaults['tmnhanphat_why_choose_overlay_opacity'],
			'sanitize_callback' => 'absint',
			'input_attrs'       => array( 'min' => 0, 'max' => 100, 'step' => 5 ),
		),
		'tmnhanphat_why_choose_padding_desktop' => array(
			'label'             => __( 'Section Padding — Desktop (px)', 'tmnhanphat' ),
			'section'           => 'general',
			'type'              => 'number',
			'default'           => $defaults['tmnhanphat_why_choose_padding_desktop'],
			'sanitize_callback' => 'absint',
			'input_attrs'       => array( 'min' => 0, 'max' => 240, 'step' => 4 ),
		),
		'tmnhanphat_why_choose_padding_tablet'  => array(
			'label'             => __( 'Section Padding — Tablet (px)', 'tmnhanphat' ),
			'section'           => 'general',
			'type'              => 'number',
			'default'           => $defaults['tmnhanphat_why_choose_padding_tablet'],
			'sanitize_callback' => 'absint',
			'input_attrs'       => array( 'min' => 0, 'max' => 200, 'step' => 4 ),
		),
		'tmnhanphat_why_choose_padding_mobile'  => array(
			'label'             => __( 'Section Padding — Mobile (px)', 'tmnhanphat' ),
			'section'           => 'general',
			'type'              => 'number',
			'default'           => $defaults['tmnhanphat_why_choose_padding_mobile'],
			'sanitize_callback' => 'absint',
			'input_attrs'       => array( 'min' => 0, 'max' => 160, 'step' => 4 ),
		),
		'tmnhanphat_why_choose_margin_desktop'  => array(
			'label'             => __( 'Section Margin — Desktop (px)', 'tmnhanphat' ),
			'section'           => 'general',
			'type'              => 'number',
			'default'           => $defaults['tmnhanphat_why_choose_margin_desktop'],
			'sanitize_callback' => 'absint',
			'input_attrs'       => array( 'min' => 0, 'max' => 240, 'step' => 4 ),
		),
		'tmnhanphat_why_choose_margin_tablet'   => array(
			'label'             => __( 'Section Margin — Tablet (px)', 'tmnhanphat' ),
			'section'           => 'general',
			'type'              => 'number',
			'default'           => $defaults['tmnhanphat_why_choose_margin_tablet'],
			'sanitize_callback' => 'absint',
			'input_attrs'       => array( 'min' => 0, 'max' => 200, 'step' => 4 ),
		),
		'tmnhanphat_why_choose_margin_mobile'   => array(
			'label'             => __( 'Section Margin — Mobile (px)', 'tmnhanphat' ),
			'section'           => 'general',
			'type'              => 'number',
			'default'           => $defaults['tmnhanphat_why_choose_margin_mobile'],
			'sanitize_callback' => 'absint',
			'input_attrs'       => array( 'min' => 0, 'max' => 160, 'step' => 4 ),
		),

		// ----- Decoration (tam giác xanh góc trái) -----
		'tmnhanphat_why_choose_deco_enable'      => array(
			'label'             => __( 'Hiển thị Decoration', 'tmnhanphat' ),
			'section'           => 'decoration',
			'type'              => 'checkbox',
			'default'           => $defaults['tmnhanphat_why_choose_deco_enable'],
			'sanitize_callback' => 'tmnhanphat_sanitize_checkbox',
		),
		'tmnhanphat_why_choose_deco_image'       => array(
			'label'             => __( 'Decoration Image (tam giác xanh, thiết kế 828.5×857.5)', 'tmnhanphat' ),
			'section'           => 'decoration',
			'type'              => 'image',
			'default'           => $defaults['tmnhanphat_why_choose_deco_image'],
			'sanitize_callback' => 'esc_url_raw',
		),
		'tmnhanphat_why_choose_deco_width'       => array(
			'label'             => __( 'Decoration Width (px)', 'tmnhanphat' ),
			'section'           => 'decoration',
			'type'              => 'number',
			'default'           => $defaults['tmnhanphat_why_choose_deco_width'],
			'sanitize_callback' => 'absint',
			'input_attrs'       => array( 'min' => 100, 'max' => 1200, 'step' => 10 ),
		),
		'tmnhanphat_why_choose_deco_height'      => array(
			'label'             => __( 'Decoration Height (px, 0 = auto theo tỉ lệ ảnh)', 'tmnhanphat' ),
			'section'           => 'decoration',
			'type'              => 'number',
			'default'           => $defaults['tmnhanphat_why_choose_deco_height'],
			'sanitize_callback' => 'absint',
			'input_attrs'       => array( 'min' => 0, 'max' => 1200, 'step' => 10 ),
		),
		'tmnhanphat_why_choose_deco_offset_x'    => array(
			'label'             => __( 'Decoration Offset X (px từ mép trái, cho phép âm)', 'tmnhanphat' ),
			'section'           => 'decoration',
			'type'              => 'number',
			'default'           => $defaults['tmnhanphat_why_choose_deco_offset_x'],
			'sanitize_callback' => 'tmnhanphat_sanitize_why_choose_deco_offset',
			'input_attrs'       => array( 'min' => -800, 'max' => 800, 'step' => 10 ),
		),
		'tmnhanphat_why_choose_deco_offset_y'    => array(
			'label'             => __( 'Decoration Offset Y (px từ mép trên, cho phép âm)', 'tmnhanphat' ),
			'section'           => 'decoration',
			'type'              => 'number',
			'default'           => $defaults['tmnhanphat_why_choose_deco_offset_y'],
			'sanitize_callback' => 'tmnhanphat_sanitize_why_choose_deco_offset',
			'input_attrs'       => array( 'min' => -800, 'max' => 800, 'step' => 10 ),
		),
		'tmnhanphat_why_choose_deco_hide_mobile' => array(
			'label'             => __( 'Ẩn Decoration trên Mobile (không che nội dung)', 'tmnhanphat' ),
			'section'           => 'decoration',
			'type'              => 'checkbox',
			'default'           => $defaults['tmnhanphat_why_choose_deco_hide_mobile'],
			'sanitize_callback' => 'tmnhanphat_sanitize_checkbox',
		),

		// ----- Heading -----
		'tmnhanphat_why_choose_heading_blue_text'      => array(
			'label'             => __( 'Blue Title (dòng trên, màu xanh)', 'tmnhanphat' ),
			'section'           => 'heading',
			'type'              => 'text',
			'default'           => $defaults['tmnhanphat_why_choose_heading_blue_text'],
			'sanitize_callback' => 'sanitize_text_field',
		),
		'tmnhanphat_why_choose_heading_red_text'       => array(
			'label'             => __( 'Red Title (dòng dưới, màu đỏ)', 'tmnhanphat' ),
			'section'           => 'heading',
			'type'              => 'text',
			'default'           => $defaults['tmnhanphat_why_choose_heading_red_text'],
			'sanitize_callback' => 'sanitize_text_field',
		),
		'tmnhanphat_why_choose_heading_blue_color'     => array(
			'label'             => __( 'Blue Title Color', 'tmnhanphat' ),
			'section'           => 'heading',
			'type'              => 'color',
			'default'           => $defaults['tmnhanphat_why_choose_heading_blue_color'],
			'sanitize_callback' => 'sanitize_hex_color',
		),
		'tmnhanphat_why_choose_heading_red_color'      => array(
			'label'             => __( 'Red Title Color', 'tmnhanphat' ),
			'section'           => 'heading',
			'type'              => 'color',
			'default'           => $defaults['tmnhanphat_why_choose_heading_red_color'],
			'sanitize_callback' => 'sanitize_hex_color',
		),
		'tmnhanphat_why_choose_heading_size'           => array(
			'label'             => __( 'Title Font Size — Desktop (px)', 'tmnhanphat' ),
			'section'           => 'heading',
			'type'              => 'number',
			'default'           => $defaults['tmnhanphat_why_choose_heading_size'],
			'sanitize_callback' => 'absint',
			'input_attrs'       => array( 'min' => 20, 'max' => 72, 'step' => 1 ),
		),
		'tmnhanphat_why_choose_heading_size_tablet'    => array(
			'label'             => __( 'Title Font Size — Tablet (px)', 'tmnhanphat' ),
			'section'           => 'heading',
			'type'              => 'number',
			'default'           => $defaults['tmnhanphat_why_choose_heading_size_tablet'],
			'sanitize_callback' => 'absint',
			'input_attrs'       => array( 'min' => 18, 'max' => 60, 'step' => 1 ),
		),
		'tmnhanphat_why_choose_heading_size_mobile'    => array(
			'label'             => __( 'Title Font Size — Mobile (px)', 'tmnhanphat' ),
			'section'           => 'heading',
			'type'              => 'number',
			'default'           => $defaults['tmnhanphat_why_choose_heading_size_mobile'],
			'sanitize_callback' => 'absint',
			'input_attrs'       => array( 'min' => 16, 'max' => 48, 'step' => 1 ),
		),
		'tmnhanphat_why_choose_heading_weight'         => array(
			'label'             => __( 'Title Weight', 'tmnhanphat' ),
			'section'           => 'heading',
			'choices'           => tmnhanphat_get_font_weight_choices(),
			'default'           => $defaults['tmnhanphat_why_choose_heading_weight'],
			'sanitize_callback' => 'tmnhanphat_sanitize_font_weight',
		),
		'tmnhanphat_why_choose_heading_line_height'    => array(
			'label'             => __( 'Line Height (vd 1.2)', 'tmnhanphat' ),
			'section'           => 'heading',
			'type'              => 'number',
			'default'           => $defaults['tmnhanphat_why_choose_heading_line_height'],
			'sanitize_callback' => 'tmnhanphat_sanitize_why_choose_line_height',
			'input_attrs'       => array( 'min' => 0.8, 'max' => 3, 'step' => 0.05 ),
		),
		'tmnhanphat_why_choose_heading_letter_spacing' => array(
			'label'             => __( 'Letter Spacing (px, cho phép âm)', 'tmnhanphat' ),
			'section'           => 'heading',
			'type'              => 'number',
			'default'           => $defaults['tmnhanphat_why_choose_heading_letter_spacing'],
			'sanitize_callback' => 'tmnhanphat_sanitize_why_choose_letter_spacing',
			'input_attrs'       => array( 'min' => -5, 'max' => 10, 'step' => 1 ),
		),
		'tmnhanphat_why_choose_heading_align'          => array(
			'label'             => __( 'Title Alignment', 'tmnhanphat' ),
			'section'           => 'heading',
			'choices'           => tmnhanphat_get_services_align_choices(),
			'default'           => $defaults['tmnhanphat_why_choose_heading_align'],
			'sanitize_callback' => 'tmnhanphat_sanitize_services_align',
		),
		'tmnhanphat_why_choose_heading_margin_bottom'  => array(
			'label'             => __( 'Heading Margin Bottom (px)', 'tmnhanphat' ),
			'section'           => 'heading',
			'type'              => 'number',
			'default'           => $defaults['tmnhanphat_why_choose_heading_margin_bottom'],
			'sanitize_callback' => 'absint',
			'input_attrs'       => array( 'min' => 0, 'max' => 80, 'step' => 2 ),
		),

		// ----- Description -----
		'tmnhanphat_why_choose_desc_text'          => array(
			'label'             => __( 'Description', 'tmnhanphat' ),
			'section'           => 'description',
			'type'              => 'textarea',
			'default'           => $defaults['tmnhanphat_why_choose_desc_text'],
			'sanitize_callback' => 'sanitize_textarea_field',
		),
		'tmnhanphat_why_choose_desc_color'         => array(
			'label'             => __( 'Description Color', 'tmnhanphat' ),
			'section'           => 'description',
			'type'              => 'color',
			'default'           => $defaults['tmnhanphat_why_choose_desc_color'],
			'sanitize_callback' => 'sanitize_hex_color',
		),
		'tmnhanphat_why_choose_desc_size'          => array(
			'label'             => __( 'Description Font Size — Desktop (px)', 'tmnhanphat' ),
			'section'           => 'description',
			'type'              => 'number',
			'default'           => $defaults['tmnhanphat_why_choose_desc_size'],
			'sanitize_callback' => 'absint',
			'input_attrs'       => array( 'min' => 12, 'max' => 24, 'step' => 1 ),
		),
		'tmnhanphat_why_choose_desc_size_tablet'   => array(
			'label'             => __( 'Description Font Size — Tablet (px)', 'tmnhanphat' ),
			'section'           => 'description',
			'type'              => 'number',
			'default'           => $defaults['tmnhanphat_why_choose_desc_size_tablet'],
			'sanitize_callback' => 'absint',
			'input_attrs'       => array( 'min' => 12, 'max' => 22, 'step' => 1 ),
		),
		'tmnhanphat_why_choose_desc_size_mobile'   => array(
			'label'             => __( 'Description Font Size — Mobile (px)', 'tmnhanphat' ),
			'section'           => 'description',
			'type'              => 'number',
			'default'           => $defaults['tmnhanphat_why_choose_desc_size_mobile'],
			'sanitize_callback' => 'absint',
			'input_attrs'       => array( 'min' => 12, 'max' => 20, 'step' => 1 ),
		),
		'tmnhanphat_why_choose_desc_max_width'     => array(
			'label'             => __( 'Description Max Width (px)', 'tmnhanphat' ),
			'section'           => 'description',
			'type'              => 'number',
			'default'           => $defaults['tmnhanphat_why_choose_desc_max_width'],
			'sanitize_callback' => 'absint',
			'input_attrs'       => array( 'min' => 300, 'max' => 1000, 'step' => 10 ),
		),
		'tmnhanphat_why_choose_desc_clamp'         => array(
			'label'             => __( 'Description Line Clamp (0 = không giới hạn)', 'tmnhanphat' ),
			'section'           => 'description',
			'type'              => 'number',
			'default'           => $defaults['tmnhanphat_why_choose_desc_clamp'],
			'sanitize_callback' => 'absint',
			'input_attrs'       => array( 'min' => 0, 'max' => 6, 'step' => 1 ),
		),
		'tmnhanphat_why_choose_desc_margin_bottom' => array(
			'label'             => __( 'Header Bottom Spacing (px — khoảng cách xuống Slider)', 'tmnhanphat' ),
			'section'           => 'description',
			'type'              => 'number',
			'default'           => $defaults['tmnhanphat_why_choose_desc_margin_bottom'],
			'sanitize_callback' => 'absint',
			'input_attrs'       => array( 'min' => 0, 'max' => 120, 'step' => 2 ),
		),

		// ----- Slider -----
		'tmnhanphat_why_choose_cards_desktop'    => array(
			'label'             => __( 'Items Desktop', 'tmnhanphat' ),
			'section'           => 'slider',
			'choices'           => tmnhanphat_get_services_cards_choices( 'desktop' ),
			'default'           => $defaults['tmnhanphat_why_choose_cards_desktop'],
			'sanitize_callback' => 'tmnhanphat_sanitize_services_cards_desktop',
		),
		'tmnhanphat_why_choose_cards_tablet'     => array(
			'label'             => __( 'Items Tablet', 'tmnhanphat' ),
			'section'           => 'slider',
			'choices'           => tmnhanphat_get_services_cards_choices( 'tablet' ),
			'default'           => $defaults['tmnhanphat_why_choose_cards_tablet'],
			'sanitize_callback' => 'tmnhanphat_sanitize_services_cards_tablet',
		),
		'tmnhanphat_why_choose_cards_mobile'     => array(
			'label'             => __( 'Items Mobile', 'tmnhanphat' ),
			'section'           => 'slider',
			'choices'           => tmnhanphat_get_services_cards_choices( 'mobile' ),
			'default'           => $defaults['tmnhanphat_why_choose_cards_mobile'],
			'sanitize_callback' => 'tmnhanphat_sanitize_services_cards_mobile',
		),
		'tmnhanphat_why_choose_gap_desktop'      => array(
			'label'             => __( 'Gap Desktop (px)', 'tmnhanphat' ),
			'section'           => 'slider',
			'type'              => 'number',
			'default'           => $defaults['tmnhanphat_why_choose_gap_desktop'],
			'sanitize_callback' => 'absint',
			'input_attrs'       => array( 'min' => 0, 'max' => 60, 'step' => 2 ),
		),
		'tmnhanphat_why_choose_gap_tablet'       => array(
			'label'             => __( 'Gap Tablet (px)', 'tmnhanphat' ),
			'section'           => 'slider',
			'type'              => 'number',
			'default'           => $defaults['tmnhanphat_why_choose_gap_tablet'],
			'sanitize_callback' => 'absint',
			'input_attrs'       => array( 'min' => 0, 'max' => 48, 'step' => 2 ),
		),
		'tmnhanphat_why_choose_gap_mobile'       => array(
			'label'             => __( 'Gap Mobile (px)', 'tmnhanphat' ),
			'section'           => 'slider',
			'type'              => 'number',
			'default'           => $defaults['tmnhanphat_why_choose_gap_mobile'],
			'sanitize_callback' => 'absint',
			'input_attrs'       => array( 'min' => 0, 'max' => 40, 'step' => 2 ),
		),
		'tmnhanphat_why_choose_autoplay_enable'  => array(
			'label'             => __( 'Enable Autoplay (tự trượt)', 'tmnhanphat' ),
			'section'           => 'slider',
			'type'              => 'checkbox',
			'default'           => $defaults['tmnhanphat_why_choose_autoplay_enable'],
			'sanitize_callback' => 'tmnhanphat_sanitize_checkbox',
		),
		'tmnhanphat_why_choose_autoplay_speed'   => array(
			'label'             => __( 'Autoplay Delay (ms)', 'tmnhanphat' ),
			'section'           => 'slider',
			'type'              => 'number',
			'default'           => $defaults['tmnhanphat_why_choose_autoplay_speed'],
			'sanitize_callback' => 'absint',
			'input_attrs'       => array( 'min' => 1000, 'max' => 15000, 'step' => 500 ),
		),
		'tmnhanphat_why_choose_transition_speed' => array(
			'label'             => __( 'Transition Speed (ms)', 'tmnhanphat' ),
			'section'           => 'slider',
			'type'              => 'number',
			'default'           => $defaults['tmnhanphat_why_choose_transition_speed'],
			'sanitize_callback' => 'absint',
			'input_attrs'       => array( 'min' => 150, 'max' => 2000, 'step' => 50 ),
		),
		'tmnhanphat_why_choose_pause_hover'      => array(
			'label'             => __( 'Pause On Hover', 'tmnhanphat' ),
			'section'           => 'slider',
			'type'              => 'checkbox',
			'default'           => $defaults['tmnhanphat_why_choose_pause_hover'],
			'sanitize_callback' => 'tmnhanphat_sanitize_checkbox',
		),
		'tmnhanphat_why_choose_infinite'         => array(
			'label'             => __( 'Infinite Loop', 'tmnhanphat' ),
			'section'           => 'slider',
			'type'              => 'checkbox',
			'default'           => $defaults['tmnhanphat_why_choose_infinite'],
			'sanitize_callback' => 'tmnhanphat_sanitize_checkbox',
		),
		'tmnhanphat_why_choose_drag_enable'      => array(
			'label'             => __( 'Enable Drag/Swipe (chuột + cảm ứng)', 'tmnhanphat' ),
			'section'           => 'slider',
			'type'              => 'checkbox',
			'default'           => $defaults['tmnhanphat_why_choose_drag_enable'],
			'sanitize_callback' => 'tmnhanphat_sanitize_checkbox',
		),

		// ----- Card -----
		'tmnhanphat_why_choose_card_height'          => array(
			'label'             => __( 'Card Min Height (px, 0 = auto — thiết kế 459)', 'tmnhanphat' ),
			'section'           => 'card',
			'type'              => 'number',
			'default'           => $defaults['tmnhanphat_why_choose_card_height'],
			'sanitize_callback' => 'absint',
			'input_attrs'       => array( 'min' => 0, 'max' => 800, 'step' => 10 ),
		),
		'tmnhanphat_why_choose_card_radius'          => array(
			'label'             => __( 'Border Radius (px)', 'tmnhanphat' ),
			'section'           => 'card',
			'type'              => 'number',
			'default'           => $defaults['tmnhanphat_why_choose_card_radius'],
			'sanitize_callback' => 'absint',
			'input_attrs'       => array( 'min' => 0, 'max' => 40, 'step' => 1 ),
		),
		'tmnhanphat_why_choose_card_border_width'    => array(
			'label'             => __( 'Border Width (px, 0 = không viền)', 'tmnhanphat' ),
			'section'           => 'card',
			'type'              => 'number',
			'default'           => $defaults['tmnhanphat_why_choose_card_border_width'],
			'sanitize_callback' => 'absint',
			'input_attrs'       => array( 'min' => 0, 'max' => 6, 'step' => 1 ),
		),
		'tmnhanphat_why_choose_card_border_color'    => array(
			'label'             => __( 'Border Color', 'tmnhanphat' ),
			'section'           => 'card',
			'type'              => 'color',
			'default'           => $defaults['tmnhanphat_why_choose_card_border_color'],
			'sanitize_callback' => 'sanitize_hex_color',
		),
		'tmnhanphat_why_choose_card_bg'              => array(
			'label'             => __( 'Background Color', 'tmnhanphat' ),
			'section'           => 'card',
			'type'              => 'color',
			'default'           => $defaults['tmnhanphat_why_choose_card_bg'],
			'sanitize_callback' => 'sanitize_hex_color',
		),
		'tmnhanphat_why_choose_card_shadow'          => array(
			'label'             => __( 'Box Shadow', 'tmnhanphat' ),
			'section'           => 'card',
			'choices'           => tmnhanphat_get_why_choose_shadow_choices(),
			'default'           => $defaults['tmnhanphat_why_choose_card_shadow'],
			'sanitize_callback' => 'tmnhanphat_sanitize_why_choose_shadow',
		),
		'tmnhanphat_why_choose_card_hover_shadow'    => array(
			'label'             => __( 'Hover Shadow', 'tmnhanphat' ),
			'section'           => 'card',
			'choices'           => tmnhanphat_get_why_choose_shadow_choices(),
			'default'           => $defaults['tmnhanphat_why_choose_card_hover_shadow'],
			'sanitize_callback' => 'tmnhanphat_sanitize_why_choose_shadow',
		),
		'tmnhanphat_why_choose_card_hover_translate' => array(
			'label'             => __( 'Hover Translate (px nâng lên khi hover)', 'tmnhanphat' ),
			'section'           => 'card',
			'type'              => 'number',
			'default'           => $defaults['tmnhanphat_why_choose_card_hover_translate'],
			'sanitize_callback' => 'absint',
			'input_attrs'       => array( 'min' => 0, 'max' => 20, 'step' => 1 ),
		),
		'tmnhanphat_why_choose_card_padding'         => array(
			'label'             => __( 'Card Padding (px)', 'tmnhanphat' ),
			'section'           => 'card',
			'type'              => 'number',
			'default'           => $defaults['tmnhanphat_why_choose_card_padding'],
			'sanitize_callback' => 'absint',
			'input_attrs'       => array( 'min' => 8, 'max' => 48, 'step' => 2 ),
		),
		'tmnhanphat_why_choose_card_content_align'   => array(
			'label'             => __( 'Content Alignment (căn dọc trong Card)', 'tmnhanphat' ),
			'section'           => 'card',
			'choices'           => tmnhanphat_get_why_choose_content_align_choices(),
			'default'           => $defaults['tmnhanphat_why_choose_card_content_align'],
			'sanitize_callback' => 'tmnhanphat_sanitize_why_choose_content_align',
		),
		'tmnhanphat_why_choose_icon_enable'          => array(
			'label'             => __( 'Hiển thị Icon trên Card (item chưa upload dùng icon mặc định)', 'tmnhanphat' ),
			'section'           => 'card',
			'type'              => 'checkbox',
			'default'           => $defaults['tmnhanphat_why_choose_icon_enable'],
			'sanitize_callback' => 'tmnhanphat_sanitize_checkbox',
		),

		'tmnhanphat_why_choose_card_title_size'          => array(
			'label'             => __( 'Title Font Size — Desktop (px)', 'tmnhanphat' ),
			'section'           => 'card',
			'type'              => 'number',
			'default'           => $defaults['tmnhanphat_why_choose_card_title_size'],
			'sanitize_callback' => 'absint',
			'input_attrs'       => array( 'min' => 14, 'max' => 32, 'step' => 1 ),
		),
		'tmnhanphat_why_choose_card_title_size_tablet'   => array(
			'label'             => __( 'Title Font Size — Tablet (px)', 'tmnhanphat' ),
			'section'           => 'card',
			'type'              => 'number',
			'default'           => $defaults['tmnhanphat_why_choose_card_title_size_tablet'],
			'sanitize_callback' => 'absint',
			'input_attrs'       => array( 'min' => 13, 'max' => 28, 'step' => 1 ),
		),
		'tmnhanphat_why_choose_card_title_size_mobile'   => array(
			'label'             => __( 'Title Font Size — Mobile (px)', 'tmnhanphat' ),
			'section'           => 'card',
			'type'              => 'number',
			'default'           => $defaults['tmnhanphat_why_choose_card_title_size_mobile'],
			'sanitize_callback' => 'absint',
			'input_attrs'       => array( 'min' => 12, 'max' => 24, 'step' => 1 ),
		),
		'tmnhanphat_why_choose_card_title_weight'        => array(
			'label'             => __( 'Title Weight', 'tmnhanphat' ),
			'section'           => 'card',
			'choices'           => tmnhanphat_get_font_weight_choices(),
			'default'           => $defaults['tmnhanphat_why_choose_card_title_weight'],
			'sanitize_callback' => 'tmnhanphat_sanitize_font_weight',
		),
		'tmnhanphat_why_choose_card_title_color'         => array(
			'label'             => __( 'Title Color', 'tmnhanphat' ),
			'section'           => 'card',
			'type'              => 'color',
			'default'           => $defaults['tmnhanphat_why_choose_card_title_color'],
			'sanitize_callback' => 'sanitize_hex_color',
		),
		'tmnhanphat_why_choose_card_title_clamp'         => array(
			'label'             => __( 'Title Line Clamp (0 = không giới hạn)', 'tmnhanphat' ),
			'section'           => 'card',
			'type'              => 'number',
			'default'           => $defaults['tmnhanphat_why_choose_card_title_clamp'],
			'sanitize_callback' => 'absint',
			'input_attrs'       => array( 'min' => 0, 'max' => 4, 'step' => 1 ),
		),
		'tmnhanphat_why_choose_card_title_margin_bottom' => array(
			'label'             => __( 'Title Spacing Bottom (px)', 'tmnhanphat' ),
			'section'           => 'card',
			'type'              => 'number',
			'default'           => $defaults['tmnhanphat_why_choose_card_title_margin_bottom'],
			'sanitize_callback' => 'absint',
			'input_attrs'       => array( 'min' => 0, 'max' => 40, 'step' => 1 ),
		),

		'tmnhanphat_why_choose_card_desc_size'          => array(
			'label'             => __( 'Description Font Size — Desktop (px)', 'tmnhanphat' ),
			'section'           => 'card',
			'type'              => 'number',
			'default'           => $defaults['tmnhanphat_why_choose_card_desc_size'],
			'sanitize_callback' => 'absint',
			'input_attrs'       => array( 'min' => 11, 'max' => 20, 'step' => 1 ),
		),
		'tmnhanphat_why_choose_card_desc_size_tablet'   => array(
			'label'             => __( 'Description Font Size — Tablet (px)', 'tmnhanphat' ),
			'section'           => 'card',
			'type'              => 'number',
			'default'           => $defaults['tmnhanphat_why_choose_card_desc_size_tablet'],
			'sanitize_callback' => 'absint',
			'input_attrs'       => array( 'min' => 11, 'max' => 18, 'step' => 1 ),
		),
		'tmnhanphat_why_choose_card_desc_size_mobile'   => array(
			'label'             => __( 'Description Font Size — Mobile (px)', 'tmnhanphat' ),
			'section'           => 'card',
			'type'              => 'number',
			'default'           => $defaults['tmnhanphat_why_choose_card_desc_size_mobile'],
			'sanitize_callback' => 'absint',
			'input_attrs'       => array( 'min' => 10, 'max' => 16, 'step' => 1 ),
		),
		'tmnhanphat_why_choose_card_desc_color'         => array(
			'label'             => __( 'Description Color', 'tmnhanphat' ),
			'section'           => 'card',
			'type'              => 'color',
			'default'           => $defaults['tmnhanphat_why_choose_card_desc_color'],
			'sanitize_callback' => 'sanitize_hex_color',
		),
		'tmnhanphat_why_choose_card_desc_clamp'         => array(
			'label'             => __( 'Description Line Clamp (0 = không giới hạn)', 'tmnhanphat' ),
			'section'           => 'card',
			'type'              => 'number',
			'default'           => $defaults['tmnhanphat_why_choose_card_desc_clamp'],
			'sanitize_callback' => 'absint',
			'input_attrs'       => array( 'min' => 0, 'max' => 8, 'step' => 1 ),
		),
		'tmnhanphat_why_choose_card_desc_margin_bottom' => array(
			'label'             => __( 'Description Spacing Bottom (px)', 'tmnhanphat' ),
			'section'           => 'card',
			'type'              => 'number',
			'default'           => $defaults['tmnhanphat_why_choose_card_desc_margin_bottom'],
			'sanitize_callback' => 'absint',
			'input_attrs'       => array( 'min' => 0, 'max' => 40, 'step' => 1 ),
		),

		'tmnhanphat_why_choose_card_image_height'     => array(
			'label'             => __( 'Image Height (px — thiết kế 290)', 'tmnhanphat' ),
			'section'           => 'card',
			'type'              => 'number',
			'default'           => $defaults['tmnhanphat_why_choose_card_image_height'],
			'sanitize_callback' => 'absint',
			'input_attrs'       => array( 'min' => 100, 'max' => 500, 'step' => 10 ),
		),
		'tmnhanphat_why_choose_card_image_radius'     => array(
			'label'             => __( 'Image Radius (px)', 'tmnhanphat' ),
			'section'           => 'card',
			'type'              => 'number',
			'default'           => $defaults['tmnhanphat_why_choose_card_image_radius'],
			'sanitize_callback' => 'absint',
			'input_attrs'       => array( 'min' => 0, 'max' => 30, 'step' => 1 ),
		),
		'tmnhanphat_why_choose_card_image_fit'        => array(
			'label'             => __( 'Image Object Fit', 'tmnhanphat' ),
			'section'           => 'card',
			'choices'           => tmnhanphat_get_why_choose_image_fit_choices(),
			'default'           => $defaults['tmnhanphat_why_choose_card_image_fit'],
			'sanitize_callback' => 'tmnhanphat_sanitize_why_choose_image_fit',
		),
		'tmnhanphat_why_choose_card_image_zoom'       => array(
			'label'             => __( 'Image Hover Zoom (phóng nhẹ khi hover)', 'tmnhanphat' ),
			'section'           => 'card',
			'type'              => 'checkbox',
			'default'           => $defaults['tmnhanphat_why_choose_card_image_zoom'],
			'sanitize_callback' => 'tmnhanphat_sanitize_checkbox',
		),
		'tmnhanphat_why_choose_card_image_margin_top' => array(
			'label'             => __( 'Image Spacing Top (px)', 'tmnhanphat' ),
			'section'           => 'card',
			'type'              => 'number',
			'default'           => $defaults['tmnhanphat_why_choose_card_image_margin_top'],
			'sanitize_callback' => 'absint',
			'input_attrs'       => array( 'min' => 0, 'max' => 40, 'step' => 1 ),
		),
		'tmnhanphat_why_choose_card_image_inset'      => array(
			'label'             => __( 'Image Side Inset (px từ mép card — Figma 15, nhỏ hơn Card Padding để ảnh rộng hơn vùng text)', 'tmnhanphat' ),
			'section'           => 'card',
			'type'              => 'number',
			'default'           => $defaults['tmnhanphat_why_choose_card_image_inset'],
			'sanitize_callback' => 'absint',
			'input_attrs'       => array( 'min' => 0, 'max' => 48, 'step' => 1 ),
		),
	);

	for ( $i = 1; $i <= tmnhanphat_get_why_choose_slot_count(); $i++ ) {
		$fields = array_merge( $fields, tmnhanphat_get_why_choose_item_fields( $i, $defaults ) );
	}

	return $fields;
}

/**
 * Đăng ký Panel "Why Choose Home" + toàn bộ Section/Setting/Control.
 *
 * Priority 36: nối tiếp dải Homepage panel (Hero 30 → Products 34); 35 đã thuộc Footer
 * nên lấy số kế tiếp còn trống để không trùng priority (bài học từ audit Header/Hero).
 *
 * @param WP_Customize_Manager $wp_customize Customizer manager instance.
 */
function tmnhanphat_why_choose_customizer_register( $wp_customize ) {
	$wp_customize->add_panel( 'tmnhanphat_why_choose_panel', array(
		'title'    => __( 'Why Choose Home', 'tmnhanphat' ),
		'priority' => 36,
	) );

	$sections = array(
		'general'     => __( 'General', 'tmnhanphat' ),
		'decoration'  => __( 'Decoration', 'tmnhanphat' ),
		'heading'     => __( 'Heading', 'tmnhanphat' ),
		'description' => __( 'Description', 'tmnhanphat' ),
		'slider'      => __( 'Slider', 'tmnhanphat' ),
		'card'        => __( 'Card', 'tmnhanphat' ),
	);

	for ( $i = 1; $i <= tmnhanphat_get_why_choose_slot_count(); $i++ ) {
		/* translators: %d: số thứ tự Feature Item (1-8) */
		$sections[ "item_{$i}" ] = sprintf( __( 'Item %d', 'tmnhanphat' ), $i );
	}

	foreach ( $sections as $section_id => $section_label ) {
		$wp_customize->add_section( "tmnhanphat_why_choose_section_{$section_id}", array(
			'title' => $section_label,
			'panel' => 'tmnhanphat_why_choose_panel',
		) );
	}

	foreach ( tmnhanphat_get_why_choose_customizer_fields() as $field_id => $field ) {
		$wp_customize->add_setting( $field_id, array(
			'default'           => $field['default'],
			'sanitize_callback' => $field['sanitize_callback'],
			'transport'         => 'refresh',
		) );

		$control_args = array(
			'label'    => $field['label'],
			'section'  => 'tmnhanphat_why_choose_section_' . $field['section'],
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
add_action( 'customize_register', 'tmnhanphat_why_choose_customizer_register' );
