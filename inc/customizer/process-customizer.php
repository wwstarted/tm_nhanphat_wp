<?php
/**
 * Đăng ký Customizer Panel riêng "Process Home" (PROJECT_RULES.md mục 26 — mỗi feature
 * Homepage 1 Panel cấp cao nhất, priority nối tiếp dải 30-59): General, Heading,
 * Description, Timeline, Animation, Image, Step 1-6. Cùng quy ước config-driven như
 * các module trước (mục 22). Toàn bộ NỘI DUNG nằm trong Customizer (fixed-slot
 * repeater, mục 24) — KHÔNG dùng WP_Query/ACF/Meta Box.
 *
 * Timeline luôn nằm bên TRÁI, Image bên PHẢI (đúng design — không có setting đảo vị
 * trí; mobile xếp dọc Header → Timeline → Image).
 *
 * @package TMNhanPhat
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Khai báo field cho 1 Step slot (dùng lại N lần — DRY).
 *
 * @param int   $index    Số thứ tự slot (1..tmnhanphat_get_process_slot_count()).
 * @param array $defaults Mảng defaults từ tmnhanphat_process_defaults().
 * @return array<string, array>
 */
function tmnhanphat_get_process_step_fields( $index, $defaults ) {
	$section = "step_{$index}";

	return array(
		"tmnhanphat_process_step{$index}_enable"      => array(
			/* translators: %d: số thứ tự Step (1-6) */
			'label'             => sprintf( __( 'Hiển thị Step %d', 'tmnhanphat' ), $index ),
			'section'           => $section,
			'type'              => 'checkbox',
			'default'           => $defaults[ "tmnhanphat_process_step{$index}_enable" ],
			'sanitize_callback' => 'tmnhanphat_sanitize_checkbox',
		),
		"tmnhanphat_process_step{$index}_number"      => array(
			'label'             => __( 'Step Number (bỏ trống = tự đánh số theo thứ tự)', 'tmnhanphat' ),
			'section'           => $section,
			'type'              => 'text',
			'default'           => $defaults[ "tmnhanphat_process_step{$index}_number" ],
			'sanitize_callback' => 'sanitize_text_field',
		),
		"tmnhanphat_process_step{$index}_title"       => array(
			'label'             => __( 'Title', 'tmnhanphat' ),
			'section'           => $section,
			'type'              => 'text',
			'default'           => $defaults[ "tmnhanphat_process_step{$index}_title" ],
			'sanitize_callback' => 'sanitize_text_field',
		),
		"tmnhanphat_process_step{$index}_description" => array(
			'label'             => __( 'Description', 'tmnhanphat' ),
			'section'           => $section,
			'type'              => 'textarea',
			'default'           => $defaults[ "tmnhanphat_process_step{$index}_description" ],
			'sanitize_callback' => 'sanitize_textarea_field',
		),
		"tmnhanphat_process_step{$index}_icon"        => array(
			'label'             => __( 'Icon (tuỳ chọn — bỏ trống hiển thị Circle Number)', 'tmnhanphat' ),
			'section'           => $section,
			'type'              => 'image',
			'default'           => $defaults[ "tmnhanphat_process_step{$index}_icon" ],
			'sanitize_callback' => 'esc_url_raw',
		),
		"tmnhanphat_process_step{$index}_image"       => array(
			'label'             => __( 'Image riêng của Step (tuỳ chọn — khi Step active, ảnh bên phải chuyển sang ảnh này; bỏ trống dùng ảnh mặc định)', 'tmnhanphat' ),
			'section'           => $section,
			'type'              => 'image',
			'default'           => $defaults[ "tmnhanphat_process_step{$index}_image" ],
			'sanitize_callback' => 'esc_url_raw',
		),
		"tmnhanphat_process_step{$index}_order"       => array(
			'label'             => __( 'Sort Order', 'tmnhanphat' ),
			'section'           => $section,
			'type'              => 'number',
			'default'           => $defaults[ "tmnhanphat_process_step{$index}_order" ],
			'sanitize_callback' => 'absint',
			'input_attrs'       => array( 'min' => 0, 'max' => 99, 'step' => 1 ),
		),
	);
}

/**
 * Khai báo toàn bộ field của Process Section dạng config (id => định nghĩa).
 * Field cho phép số ÂM/THẬP PHÂN khai thẳng callback thật trong config.
 *
 * @return array<string, array>
 */
function tmnhanphat_get_process_customizer_fields() {
	$defaults = tmnhanphat_process_defaults();

	$fields = array(
		// ----- General -----
		'tmnhanphat_process_enable'          => array(
			'label'             => __( 'Hiển thị Process Section', 'tmnhanphat' ),
			'section'           => 'general',
			'type'              => 'checkbox',
			'default'           => $defaults['tmnhanphat_process_enable'],
			'sanitize_callback' => 'tmnhanphat_sanitize_checkbox',
		),
		'tmnhanphat_process_section_id'      => array(
			'label'             => __( 'Section ID (anchor)', 'tmnhanphat' ),
			'section'           => 'general',
			'type'              => 'text',
			'default'           => $defaults['tmnhanphat_process_section_id'],
			'sanitize_callback' => 'sanitize_key',
		),
		'tmnhanphat_process_container_width' => array(
			'label'             => __( 'Container Width (px, 0 = dùng Global Settings)', 'tmnhanphat' ),
			'section'           => 'general',
			'type'              => 'number',
			'default'           => $defaults['tmnhanphat_process_container_width'],
			'sanitize_callback' => 'absint',
			'input_attrs'       => array( 'min' => 0, 'max' => 1920, 'step' => 10 ),
		),
		'tmnhanphat_process_bg_color'        => array(
			'label'             => __( 'Background Color', 'tmnhanphat' ),
			'section'           => 'general',
			'type'              => 'color',
			'default'           => $defaults['tmnhanphat_process_bg_color'],
			'sanitize_callback' => 'sanitize_hex_color',
		),
		'tmnhanphat_process_bg_image'        => array(
			'label'             => __( 'Background Image (tuỳ chọn)', 'tmnhanphat' ),
			'section'           => 'general',
			'type'              => 'image',
			'default'           => $defaults['tmnhanphat_process_bg_image'],
			'sanitize_callback' => 'esc_url_raw',
		),
		'tmnhanphat_process_padding_desktop' => array(
			'label'             => __( 'Section Padding — Desktop (px)', 'tmnhanphat' ),
			'section'           => 'general',
			'type'              => 'number',
			'default'           => $defaults['tmnhanphat_process_padding_desktop'],
			'sanitize_callback' => 'absint',
			'input_attrs'       => array( 'min' => 0, 'max' => 240, 'step' => 4 ),
		),
		'tmnhanphat_process_padding_tablet'  => array(
			'label'             => __( 'Section Padding — Tablet (px)', 'tmnhanphat' ),
			'section'           => 'general',
			'type'              => 'number',
			'default'           => $defaults['tmnhanphat_process_padding_tablet'],
			'sanitize_callback' => 'absint',
			'input_attrs'       => array( 'min' => 0, 'max' => 200, 'step' => 4 ),
		),
		'tmnhanphat_process_padding_mobile'  => array(
			'label'             => __( 'Section Padding — Mobile (px)', 'tmnhanphat' ),
			'section'           => 'general',
			'type'              => 'number',
			'default'           => $defaults['tmnhanphat_process_padding_mobile'],
			'sanitize_callback' => 'absint',
			'input_attrs'       => array( 'min' => 0, 'max' => 160, 'step' => 4 ),
		),
		'tmnhanphat_process_margin_desktop'  => array(
			'label'             => __( 'Section Margin — Desktop (px)', 'tmnhanphat' ),
			'section'           => 'general',
			'type'              => 'number',
			'default'           => $defaults['tmnhanphat_process_margin_desktop'],
			'sanitize_callback' => 'absint',
			'input_attrs'       => array( 'min' => 0, 'max' => 240, 'step' => 4 ),
		),
		'tmnhanphat_process_margin_tablet'   => array(
			'label'             => __( 'Section Margin — Tablet (px)', 'tmnhanphat' ),
			'section'           => 'general',
			'type'              => 'number',
			'default'           => $defaults['tmnhanphat_process_margin_tablet'],
			'sanitize_callback' => 'absint',
			'input_attrs'       => array( 'min' => 0, 'max' => 200, 'step' => 4 ),
		),
		'tmnhanphat_process_margin_mobile'   => array(
			'label'             => __( 'Section Margin — Mobile (px)', 'tmnhanphat' ),
			'section'           => 'general',
			'type'              => 'number',
			'default'           => $defaults['tmnhanphat_process_margin_mobile'],
			'sanitize_callback' => 'absint',
			'input_attrs'       => array( 'min' => 0, 'max' => 160, 'step' => 4 ),
		),

		// ----- Heading -----
		'tmnhanphat_process_heading_blue_text'      => array(
			'label'             => __( 'Blue Title (phần chữ màu xanh)', 'tmnhanphat' ),
			'section'           => 'heading',
			'type'              => 'text',
			'default'           => $defaults['tmnhanphat_process_heading_blue_text'],
			'sanitize_callback' => 'sanitize_text_field',
		),
		'tmnhanphat_process_heading_red_text'       => array(
			'label'             => __( 'Red Title (phần chữ màu đỏ)', 'tmnhanphat' ),
			'section'           => 'heading',
			'type'              => 'text',
			'default'           => $defaults['tmnhanphat_process_heading_red_text'],
			'sanitize_callback' => 'sanitize_text_field',
		),
		'tmnhanphat_process_heading_blue_color'     => array(
			'label'             => __( 'Blue Color', 'tmnhanphat' ),
			'section'           => 'heading',
			'type'              => 'color',
			'default'           => $defaults['tmnhanphat_process_heading_blue_color'],
			'sanitize_callback' => 'sanitize_hex_color',
		),
		'tmnhanphat_process_heading_red_color'      => array(
			'label'             => __( 'Red Color', 'tmnhanphat' ),
			'section'           => 'heading',
			'type'              => 'color',
			'default'           => $defaults['tmnhanphat_process_heading_red_color'],
			'sanitize_callback' => 'sanitize_hex_color',
		),
		'tmnhanphat_process_heading_size'           => array(
			'label'             => __( 'Title Font Size — Desktop (px)', 'tmnhanphat' ),
			'section'           => 'heading',
			'type'              => 'number',
			'default'           => $defaults['tmnhanphat_process_heading_size'],
			'sanitize_callback' => 'absint',
			'input_attrs'       => array( 'min' => 20, 'max' => 72, 'step' => 1 ),
		),
		'tmnhanphat_process_heading_size_tablet'    => array(
			'label'             => __( 'Title Font Size — Tablet (px)', 'tmnhanphat' ),
			'section'           => 'heading',
			'type'              => 'number',
			'default'           => $defaults['tmnhanphat_process_heading_size_tablet'],
			'sanitize_callback' => 'absint',
			'input_attrs'       => array( 'min' => 18, 'max' => 60, 'step' => 1 ),
		),
		'tmnhanphat_process_heading_size_mobile'    => array(
			'label'             => __( 'Title Font Size — Mobile (px)', 'tmnhanphat' ),
			'section'           => 'heading',
			'type'              => 'number',
			'default'           => $defaults['tmnhanphat_process_heading_size_mobile'],
			'sanitize_callback' => 'absint',
			'input_attrs'       => array( 'min' => 16, 'max' => 48, 'step' => 1 ),
		),
		'tmnhanphat_process_heading_weight'         => array(
			'label'             => __( 'Title Weight', 'tmnhanphat' ),
			'section'           => 'heading',
			'choices'           => tmnhanphat_get_font_weight_choices(),
			'default'           => $defaults['tmnhanphat_process_heading_weight'],
			'sanitize_callback' => 'tmnhanphat_sanitize_font_weight',
		),
		'tmnhanphat_process_heading_line_height'    => array(
			'label'             => __( 'Line Height (vd 1.2)', 'tmnhanphat' ),
			'section'           => 'heading',
			'type'              => 'number',
			'default'           => $defaults['tmnhanphat_process_heading_line_height'],
			'sanitize_callback' => 'tmnhanphat_sanitize_process_line_height',
			'input_attrs'       => array( 'min' => 0.8, 'max' => 3, 'step' => 0.05 ),
		),
		'tmnhanphat_process_heading_letter_spacing' => array(
			'label'             => __( 'Letter Spacing (px, cho phép âm)', 'tmnhanphat' ),
			'section'           => 'heading',
			'type'              => 'number',
			'default'           => $defaults['tmnhanphat_process_heading_letter_spacing'],
			'sanitize_callback' => 'tmnhanphat_sanitize_process_letter_spacing',
			'input_attrs'       => array( 'min' => -5, 'max' => 10, 'step' => 1 ),
		),
		'tmnhanphat_process_heading_align'          => array(
			'label'             => __( 'Title Alignment', 'tmnhanphat' ),
			'section'           => 'heading',
			'choices'           => tmnhanphat_get_services_align_choices(),
			'default'           => $defaults['tmnhanphat_process_heading_align'],
			'sanitize_callback' => 'tmnhanphat_sanitize_services_align',
		),
		'tmnhanphat_process_heading_margin_bottom'  => array(
			'label'             => __( 'Heading Margin Bottom (px)', 'tmnhanphat' ),
			'section'           => 'heading',
			'type'              => 'number',
			'default'           => $defaults['tmnhanphat_process_heading_margin_bottom'],
			'sanitize_callback' => 'absint',
			'input_attrs'       => array( 'min' => 0, 'max' => 80, 'step' => 2 ),
		),

		// ----- Description -----
		'tmnhanphat_process_desc_text'          => array(
			'label'             => __( 'Description', 'tmnhanphat' ),
			'section'           => 'description',
			'type'              => 'textarea',
			'default'           => $defaults['tmnhanphat_process_desc_text'],
			'sanitize_callback' => 'sanitize_textarea_field',
		),
		'tmnhanphat_process_desc_color'         => array(
			'label'             => __( 'Description Color', 'tmnhanphat' ),
			'section'           => 'description',
			'type'              => 'color',
			'default'           => $defaults['tmnhanphat_process_desc_color'],
			'sanitize_callback' => 'sanitize_hex_color',
		),
		'tmnhanphat_process_desc_size'          => array(
			'label'             => __( 'Description Font Size — Desktop (px)', 'tmnhanphat' ),
			'section'           => 'description',
			'type'              => 'number',
			'default'           => $defaults['tmnhanphat_process_desc_size'],
			'sanitize_callback' => 'absint',
			'input_attrs'       => array( 'min' => 12, 'max' => 24, 'step' => 1 ),
		),
		'tmnhanphat_process_desc_size_tablet'   => array(
			'label'             => __( 'Description Font Size — Tablet (px)', 'tmnhanphat' ),
			'section'           => 'description',
			'type'              => 'number',
			'default'           => $defaults['tmnhanphat_process_desc_size_tablet'],
			'sanitize_callback' => 'absint',
			'input_attrs'       => array( 'min' => 12, 'max' => 22, 'step' => 1 ),
		),
		'tmnhanphat_process_desc_size_mobile'   => array(
			'label'             => __( 'Description Font Size — Mobile (px)', 'tmnhanphat' ),
			'section'           => 'description',
			'type'              => 'number',
			'default'           => $defaults['tmnhanphat_process_desc_size_mobile'],
			'sanitize_callback' => 'absint',
			'input_attrs'       => array( 'min' => 12, 'max' => 20, 'step' => 1 ),
		),
		'tmnhanphat_process_desc_max_width'     => array(
			'label'             => __( 'Description Max Width (px)', 'tmnhanphat' ),
			'section'           => 'description',
			'type'              => 'number',
			'default'           => $defaults['tmnhanphat_process_desc_max_width'],
			'sanitize_callback' => 'absint',
			'input_attrs'       => array( 'min' => 300, 'max' => 1000, 'step' => 10 ),
		),
		'tmnhanphat_process_desc_clamp'         => array(
			'label'             => __( 'Description Line Clamp (0 = không giới hạn)', 'tmnhanphat' ),
			'section'           => 'description',
			'type'              => 'number',
			'default'           => $defaults['tmnhanphat_process_desc_clamp'],
			'sanitize_callback' => 'absint',
			'input_attrs'       => array( 'min' => 0, 'max' => 6, 'step' => 1 ),
		),
		'tmnhanphat_process_desc_margin_bottom' => array(
			'label'             => __( 'Header Bottom Spacing (px — khoảng cách xuống Content)', 'tmnhanphat' ),
			'section'           => 'description',
			'type'              => 'number',
			'default'           => $defaults['tmnhanphat_process_desc_margin_bottom'],
			'sanitize_callback' => 'absint',
			'input_attrs'       => array( 'min' => 0, 'max' => 120, 'step' => 2 ),
		),

		// ----- Timeline -----
		'tmnhanphat_process_timeline_width'        => array(
			'label'             => __( 'Timeline Width (% bề rộng content, Desktop)', 'tmnhanphat' ),
			'section'           => 'timeline',
			'type'              => 'number',
			'default'           => $defaults['tmnhanphat_process_timeline_width'],
			'sanitize_callback' => 'absint',
			'input_attrs'       => array( 'min' => 20, 'max' => 80, 'step' => 1 ),
		),
		'tmnhanphat_process_line_color'            => array(
			'label'             => __( 'Line Color (đường dọc)', 'tmnhanphat' ),
			'section'           => 'timeline',
			'type'              => 'color',
			'default'           => $defaults['tmnhanphat_process_line_color'],
			'sanitize_callback' => 'sanitize_hex_color',
		),
		'tmnhanphat_process_line_width'            => array(
			'label'             => __( 'Line Width (px)', 'tmnhanphat' ),
			'section'           => 'timeline',
			'type'              => 'number',
			'default'           => $defaults['tmnhanphat_process_line_width'],
			'sanitize_callback' => 'absint',
			'input_attrs'       => array( 'min' => 1, 'max' => 6, 'step' => 1 ),
		),
		'tmnhanphat_process_step_gap'              => array(
			'label'             => __( 'Step Gap (px)', 'tmnhanphat' ),
			'section'           => 'timeline',
			'type'              => 'number',
			'default'           => $defaults['tmnhanphat_process_step_gap'],
			'sanitize_callback' => 'absint',
			'input_attrs'       => array( 'min' => 8, 'max' => 100, 'step' => 2 ),
		),
		'tmnhanphat_process_circle_size'           => array(
			'label'             => __( 'Circle Size (px)', 'tmnhanphat' ),
			'section'           => 'timeline',
			'type'              => 'number',
			'default'           => $defaults['tmnhanphat_process_circle_size'],
			'sanitize_callback' => 'absint',
			'input_attrs'       => array( 'min' => 24, 'max' => 80, 'step' => 2 ),
		),
		'tmnhanphat_process_circle_border_width'   => array(
			'label'             => __( 'Circle Border Width (px)', 'tmnhanphat' ),
			'section'           => 'timeline',
			'type'              => 'number',
			'default'           => $defaults['tmnhanphat_process_circle_border_width'],
			'sanitize_callback' => 'absint',
			'input_attrs'       => array( 'min' => 0, 'max' => 6, 'step' => 1 ),
		),
		'tmnhanphat_process_circle_border_color'   => array(
			'label'             => __( 'Circle Border Color', 'tmnhanphat' ),
			'section'           => 'timeline',
			'type'              => 'color',
			'default'           => $defaults['tmnhanphat_process_circle_border_color'],
			'sanitize_callback' => 'sanitize_hex_color',
		),
		'tmnhanphat_process_circle_active_bg'      => array(
			'label'             => __( 'Active Circle Color (nền đỏ)', 'tmnhanphat' ),
			'section'           => 'timeline',
			'type'              => 'color',
			'default'           => $defaults['tmnhanphat_process_circle_active_bg'],
			'sanitize_callback' => 'sanitize_hex_color',
		),
		'tmnhanphat_process_circle_inactive_bg'    => array(
			'label'             => __( 'Inactive Circle Color (nền trắng)', 'tmnhanphat' ),
			'section'           => 'timeline',
			'type'              => 'color',
			'default'           => $defaults['tmnhanphat_process_circle_inactive_bg'],
			'sanitize_callback' => 'sanitize_hex_color',
		),
		'tmnhanphat_process_active_text_color'     => array(
			'label'             => __( 'Active Text Color', 'tmnhanphat' ),
			'section'           => 'timeline',
			'type'              => 'color',
			'default'           => $defaults['tmnhanphat_process_active_text_color'],
			'sanitize_callback' => 'sanitize_hex_color',
		),
		'tmnhanphat_process_inactive_text_color'   => array(
			'label'             => __( 'Inactive Text Color (xám nhạt)', 'tmnhanphat' ),
			'section'           => 'timeline',
			'type'              => 'color',
			'default'           => $defaults['tmnhanphat_process_inactive_text_color'],
			'sanitize_callback' => 'sanitize_hex_color',
		),
		'tmnhanphat_process_active_desc_opacity'   => array(
			'label'             => __( 'Active Description Opacity (%)', 'tmnhanphat' ),
			'section'           => 'timeline',
			'type'              => 'number',
			'default'           => $defaults['tmnhanphat_process_active_desc_opacity'],
			'sanitize_callback' => 'absint',
			'input_attrs'       => array( 'min' => 0, 'max' => 100, 'step' => 5 ),
		),
		'tmnhanphat_process_inactive_desc_opacity' => array(
			'label'             => __( 'Inactive Description Opacity (%)', 'tmnhanphat' ),
			'section'           => 'timeline',
			'type'              => 'number',
			'default'           => $defaults['tmnhanphat_process_inactive_desc_opacity'],
			'sanitize_callback' => 'absint',
			'input_attrs'       => array( 'min' => 0, 'max' => 100, 'step' => 5 ),
		),
		'tmnhanphat_process_step_padding'          => array(
			'label'             => __( 'Step Padding (px)', 'tmnhanphat' ),
			'section'           => 'timeline',
			'type'              => 'number',
			'default'           => $defaults['tmnhanphat_process_step_padding'],
			'sanitize_callback' => 'absint',
			'input_attrs'       => array( 'min' => 0, 'max' => 40, 'step' => 2 ),
		),
		'tmnhanphat_process_step_radius'           => array(
			'label'             => __( 'Step Radius (px)', 'tmnhanphat' ),
			'section'           => 'timeline',
			'type'              => 'number',
			'default'           => $defaults['tmnhanphat_process_step_radius'],
			'sanitize_callback' => 'absint',
			'input_attrs'       => array( 'min' => 0, 'max' => 30, 'step' => 1 ),
		),

		// ----- Animation -----
		'tmnhanphat_process_animation_enable'   => array(
			'label'             => __( 'Enable Animation (transition màu/opacity)', 'tmnhanphat' ),
			'section'           => 'animation',
			'type'              => 'checkbox',
			'default'           => $defaults['tmnhanphat_process_animation_enable'],
			'sanitize_callback' => 'tmnhanphat_sanitize_checkbox',
		),
		'tmnhanphat_process_auto_active_enable' => array(
			'label'             => __( 'Enable Auto Active (tự chuyển Step)', 'tmnhanphat' ),
			'section'           => 'animation',
			'type'              => 'checkbox',
			'default'           => $defaults['tmnhanphat_process_auto_active_enable'],
			'sanitize_callback' => 'tmnhanphat_sanitize_checkbox',
		),
		'tmnhanphat_process_interval'           => array(
			'label'             => __( 'Interval Time (ms)', 'tmnhanphat' ),
			'section'           => 'animation',
			'type'              => 'number',
			'default'           => $defaults['tmnhanphat_process_interval'],
			'sanitize_callback' => 'absint',
			'input_attrs'       => array( 'min' => 1000, 'max' => 15000, 'step' => 500 ),
		),
		'tmnhanphat_process_transition_speed'   => array(
			'label'             => __( 'Transition Speed (ms)', 'tmnhanphat' ),
			'section'           => 'animation',
			'type'              => 'number',
			'default'           => $defaults['tmnhanphat_process_transition_speed'],
			'sanitize_callback' => 'absint',
			'input_attrs'       => array( 'min' => 100, 'max' => 2000, 'step' => 50 ),
		),
		'tmnhanphat_process_pause_hover'        => array(
			'label'             => __( 'Pause On Hover', 'tmnhanphat' ),
			'section'           => 'animation',
			'type'              => 'checkbox',
			'default'           => $defaults['tmnhanphat_process_pause_hover'],
			'sanitize_callback' => 'tmnhanphat_sanitize_checkbox',
		),
		'tmnhanphat_process_loop'               => array(
			'label'             => __( 'Loop (Step cuối quay về Step 1)', 'tmnhanphat' ),
			'section'           => 'animation',
			'type'              => 'checkbox',
			'default'           => $defaults['tmnhanphat_process_loop'],
			'sanitize_callback' => 'tmnhanphat_sanitize_checkbox',
		),

		// ----- Right Image -----
		'tmnhanphat_process_image'        => array(
			'label'             => __( 'Upload Image (ảnh mặc định bên phải)', 'tmnhanphat' ),
			'section'           => 'image',
			'type'              => 'image',
			'default'           => $defaults['tmnhanphat_process_image'],
			'sanitize_callback' => 'esc_url_raw',
		),
		'tmnhanphat_process_image_width'  => array(
			'label'             => __( 'Image Width (px, 0 = auto theo cột)', 'tmnhanphat' ),
			'section'           => 'image',
			'type'              => 'number',
			'default'           => $defaults['tmnhanphat_process_image_width'],
			'sanitize_callback' => 'absint',
			'input_attrs'       => array( 'min' => 0, 'max' => 1200, 'step' => 10 ),
		),
		'tmnhanphat_process_image_height' => array(
			'label'             => __( 'Image Height (px, 0 = auto theo tỉ lệ ảnh)', 'tmnhanphat' ),
			'section'           => 'image',
			'type'              => 'number',
			'default'           => $defaults['tmnhanphat_process_image_height'],
			'sanitize_callback' => 'absint',
			'input_attrs'       => array( 'min' => 0, 'max' => 1000, 'step' => 10 ),
		),
		'tmnhanphat_process_image_fit'    => array(
			'label'             => __( 'Object Fit', 'tmnhanphat' ),
			'section'           => 'image',
			'choices'           => tmnhanphat_get_why_choose_image_fit_choices(),
			'default'           => $defaults['tmnhanphat_process_image_fit'],
			'sanitize_callback' => 'tmnhanphat_sanitize_why_choose_image_fit',
		),
		'tmnhanphat_process_image_align'  => array(
			'label'             => __( 'Image Alignment', 'tmnhanphat' ),
			'section'           => 'image',
			'choices'           => tmnhanphat_get_services_align_choices(),
			'default'           => $defaults['tmnhanphat_process_image_align'],
			'sanitize_callback' => 'tmnhanphat_sanitize_services_align',
		),
		'tmnhanphat_process_image_radius' => array(
			'label'             => __( 'Image Radius (px)', 'tmnhanphat' ),
			'section'           => 'image',
			'type'              => 'number',
			'default'           => $defaults['tmnhanphat_process_image_radius'],
			'sanitize_callback' => 'absint',
			'input_attrs'       => array( 'min' => 0, 'max' => 40, 'step' => 1 ),
		),
		'tmnhanphat_process_image_lazy'   => array(
			'label'             => __( 'Enable Lazy Load', 'tmnhanphat' ),
			'section'           => 'image',
			'type'              => 'checkbox',
			'default'           => $defaults['tmnhanphat_process_image_lazy'],
			'sanitize_callback' => 'tmnhanphat_sanitize_checkbox',
		),
	);

	for ( $i = 1; $i <= tmnhanphat_get_process_slot_count(); $i++ ) {
		$fields = array_merge( $fields, tmnhanphat_get_process_step_fields( $i, $defaults ) );
	}

	return $fields;
}

/**
 * Đăng ký Panel "Process Home" + toàn bộ Section/Setting/Control.
 * Priority 37: nối tiếp Why Choose Home (36) trong dải Homepage panel.
 *
 * @param WP_Customize_Manager $wp_customize Customizer manager instance.
 */
function tmnhanphat_process_customizer_register( $wp_customize ) {
	$wp_customize->add_panel( 'tmnhanphat_process_panel', array(
		'title'    => __( 'Process Home', 'tmnhanphat' ),
		'priority' => 37,
	) );

	$sections = array(
		'general'     => __( 'General', 'tmnhanphat' ),
		'heading'     => __( 'Heading', 'tmnhanphat' ),
		'description' => __( 'Description', 'tmnhanphat' ),
		'timeline'    => __( 'Timeline', 'tmnhanphat' ),
		'animation'   => __( 'Animation', 'tmnhanphat' ),
		'image'       => __( 'Right Image', 'tmnhanphat' ),
	);

	for ( $i = 1; $i <= tmnhanphat_get_process_slot_count(); $i++ ) {
		/* translators: %d: số thứ tự Step (1-6) */
		$sections[ "step_{$i}" ] = sprintf( __( 'Step %d', 'tmnhanphat' ), $i );
	}

	foreach ( $sections as $section_id => $section_label ) {
		$wp_customize->add_section( "tmnhanphat_process_section_{$section_id}", array(
			'title' => $section_label,
			'panel' => 'tmnhanphat_process_panel',
		) );
	}

	foreach ( tmnhanphat_get_process_customizer_fields() as $field_id => $field ) {
		$wp_customize->add_setting( $field_id, array(
			'default'           => $field['default'],
			'sanitize_callback' => $field['sanitize_callback'],
			'transport'         => 'refresh',
		) );

		$control_args = array(
			'label'    => $field['label'],
			'section'  => 'tmnhanphat_process_section_' . $field['section'],
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
add_action( 'customize_register', 'tmnhanphat_process_customizer_register' );
