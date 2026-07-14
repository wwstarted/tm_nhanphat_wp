<?php
/**
 * Đăng ký Customizer Panel riêng "Service Home" (PROJECT_RULES.md mục 26 — mỗi feature
 * Homepage 1 Panel cấp cao nhất, priority nối tiếp dải 30-59): General, Heading, Description,
 * Query, Slider, Card, CTA. Cùng quy ước config-driven như các module trước (mục 22).
 * Dữ liệu hiển thị lấy từ CPT tmnp_service (inc/post-types.php) — Customizer chỉ điều khiển
 * CÁCH hiển thị (query/slider/style), không chứa nội dung dịch vụ.
 *
 * @package TMNhanPhat
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Khai báo toàn bộ field của Services Home Section dạng config (id => định nghĩa).
 *
 * @return array<string, array>
 */
function tmnhanphat_get_services_customizer_fields() {
	$defaults = tmnhanphat_services_defaults();

	return array(
		// ----- General -----
		'tmnhanphat_services_enable'          => array(
			'label'             => __( 'Hiển thị Services Section', 'tmnhanphat' ),
			'section'           => 'general',
			'type'              => 'checkbox',
			'default'           => $defaults['tmnhanphat_services_enable'],
			'sanitize_callback' => 'tmnhanphat_sanitize_checkbox',
		),
		'tmnhanphat_services_section_id'      => array(
			'label'             => __( 'Section ID (anchor)', 'tmnhanphat' ),
			'section'           => 'general',
			'type'              => 'text',
			'default'           => $defaults['tmnhanphat_services_section_id'],
			'sanitize_callback' => 'sanitize_key',
		),
		'tmnhanphat_services_bg_color'        => array(
			'label'             => __( 'Background Color', 'tmnhanphat' ),
			'section'           => 'general',
			'type'              => 'color',
			'default'           => $defaults['tmnhanphat_services_bg_color'],
			'sanitize_callback' => 'sanitize_hex_color',
		),
		'tmnhanphat_services_container_width' => array(
			'label'             => __( 'Container Width (px, 0 = dùng Global Settings)', 'tmnhanphat' ),
			'section'           => 'general',
			'type'              => 'number',
			'default'           => $defaults['tmnhanphat_services_container_width'],
			'sanitize_callback' => 'absint',
			'input_attrs'       => array( 'min' => 0, 'max' => 1920, 'step' => 10 ),
		),
		'tmnhanphat_services_padding_desktop' => array(
			'label'             => __( 'Section Padding — Desktop (px)', 'tmnhanphat' ),
			'section'           => 'general',
			'type'              => 'number',
			'default'           => $defaults['tmnhanphat_services_padding_desktop'],
			'sanitize_callback' => 'absint',
		),
		'tmnhanphat_services_padding_tablet'  => array(
			'label'             => __( 'Section Padding — Tablet (px)', 'tmnhanphat' ),
			'section'           => 'general',
			'type'              => 'number',
			'default'           => $defaults['tmnhanphat_services_padding_tablet'],
			'sanitize_callback' => 'absint',
		),
		'tmnhanphat_services_padding_mobile'  => array(
			'label'             => __( 'Section Padding — Mobile (px)', 'tmnhanphat' ),
			'section'           => 'general',
			'type'              => 'number',
			'default'           => $defaults['tmnhanphat_services_padding_mobile'],
			'sanitize_callback' => 'absint',
		),

		// ----- Heading -----
		'tmnhanphat_services_heading_red_text'      => array(
			'label'             => __( 'Red Title (phần chữ màu đỏ)', 'tmnhanphat' ),
			'section'           => 'heading',
			'type'              => 'text',
			'default'           => $defaults['tmnhanphat_services_heading_red_text'],
			'sanitize_callback' => 'sanitize_text_field',
		),
		'tmnhanphat_services_heading_blue_text'     => array(
			'label'             => __( 'Blue Title (phần chữ màu xanh)', 'tmnhanphat' ),
			'section'           => 'heading',
			'type'              => 'text',
			'default'           => $defaults['tmnhanphat_services_heading_blue_text'],
			'sanitize_callback' => 'sanitize_text_field',
		),
		'tmnhanphat_services_heading_red_color'     => array(
			'label'             => __( 'Red Title Color', 'tmnhanphat' ),
			'section'           => 'heading',
			'type'              => 'color',
			'default'           => $defaults['tmnhanphat_services_heading_red_color'],
			'sanitize_callback' => 'sanitize_hex_color',
		),
		'tmnhanphat_services_heading_blue_color'    => array(
			'label'             => __( 'Blue Title Color', 'tmnhanphat' ),
			'section'           => 'heading',
			'type'              => 'color',
			'default'           => $defaults['tmnhanphat_services_heading_blue_color'],
			'sanitize_callback' => 'sanitize_hex_color',
		),
		'tmnhanphat_services_heading_font_size'     => array(
			'label'             => __( 'Heading Font Size (px, Desktop)', 'tmnhanphat' ),
			'section'           => 'heading',
			'type'              => 'number',
			'default'           => $defaults['tmnhanphat_services_heading_font_size'],
			'sanitize_callback' => 'absint',
		),
		'tmnhanphat_services_heading_font_weight'   => array(
			'label'             => __( 'Heading Font Weight', 'tmnhanphat' ),
			'section'           => 'heading',
			'choices'           => tmnhanphat_get_font_weight_choices(),
			'default'           => $defaults['tmnhanphat_services_heading_font_weight'],
			'sanitize_callback' => 'tmnhanphat_sanitize_font_weight',
		),
		'tmnhanphat_services_heading_align'         => array(
			'label'             => __( 'Heading Alignment', 'tmnhanphat' ),
			'section'           => 'heading',
			'choices'           => tmnhanphat_get_services_align_choices(),
			'default'           => $defaults['tmnhanphat_services_heading_align'],
			'sanitize_callback' => 'tmnhanphat_sanitize_services_align',
		),
		'tmnhanphat_services_heading_margin_bottom' => array(
			'label'             => __( 'Heading Margin Bottom (px)', 'tmnhanphat' ),
			'section'           => 'heading',
			'type'              => 'number',
			'default'           => $defaults['tmnhanphat_services_heading_margin_bottom'],
			'sanitize_callback' => 'absint',
		),

		// ----- Description -----
		'tmnhanphat_services_description_text'          => array(
			'label'             => __( 'Description', 'tmnhanphat' ),
			'section'           => 'description',
			'type'              => 'textarea',
			'default'           => $defaults['tmnhanphat_services_description_text'],
			'sanitize_callback' => 'sanitize_textarea_field',
		),
		'tmnhanphat_services_description_color'         => array(
			'label'             => __( 'Description Color', 'tmnhanphat' ),
			'section'           => 'description',
			'type'              => 'color',
			'default'           => $defaults['tmnhanphat_services_description_color'],
			'sanitize_callback' => 'sanitize_hex_color',
		),
		'tmnhanphat_services_description_font_size'     => array(
			'label'             => __( 'Description Font Size (px)', 'tmnhanphat' ),
			'section'           => 'description',
			'type'              => 'number',
			'default'           => $defaults['tmnhanphat_services_description_font_size'],
			'sanitize_callback' => 'absint',
		),
		'tmnhanphat_services_description_max_width'     => array(
			'label'             => __( 'Description Max Width (px)', 'tmnhanphat' ),
			'section'           => 'description',
			'type'              => 'number',
			'default'           => $defaults['tmnhanphat_services_description_max_width'],
			'sanitize_callback' => 'absint',
			'input_attrs'       => array( 'min' => 400, 'max' => 1000, 'step' => 10 ),
		),
		'tmnhanphat_services_description_margin_bottom' => array(
			'label'             => __( 'Description Margin Bottom (px)', 'tmnhanphat' ),
			'section'           => 'description',
			'type'              => 'number',
			'default'           => $defaults['tmnhanphat_services_description_margin_bottom'],
			'sanitize_callback' => 'absint',
		),

		// ----- Query -----
		'tmnhanphat_services_count'       => array(
			'label'             => __( 'Number of Services', 'tmnhanphat' ),
			'section'           => 'query',
			'type'              => 'number',
			'default'           => $defaults['tmnhanphat_services_count'],
			'sanitize_callback' => 'absint',
			'input_attrs'       => array( 'min' => 1, 'max' => 24, 'step' => 1 ),
		),
		'tmnhanphat_services_orderby'     => array(
			'label'             => __( 'Order By', 'tmnhanphat' ),
			'section'           => 'query',
			'choices'           => tmnhanphat_get_services_orderby_choices(),
			'default'           => $defaults['tmnhanphat_services_orderby'],
			'sanitize_callback' => 'tmnhanphat_sanitize_services_orderby',
		),
		'tmnhanphat_services_order'       => array(
			'label'             => __( 'Order', 'tmnhanphat' ),
			'section'           => 'query',
			'choices'           => tmnhanphat_get_services_order_choices(),
			'default'           => $defaults['tmnhanphat_services_order'],
			'sanitize_callback' => 'tmnhanphat_sanitize_services_order',
		),
		'tmnhanphat_services_offset'      => array(
			'label'             => __( 'Offset (bỏ qua N dịch vụ đầu)', 'tmnhanphat' ),
			'section'           => 'query',
			'type'              => 'number',
			'default'           => $defaults['tmnhanphat_services_offset'],
			'sanitize_callback' => 'absint',
			'input_attrs'       => array( 'min' => 0, 'max' => 50, 'step' => 1 ),
		),
		'tmnhanphat_services_exclude_ids' => array(
			'label'             => __( 'Exclude IDs (CSV, ví dụ: 12,34)', 'tmnhanphat' ),
			'section'           => 'query',
			'type'              => 'text',
			'default'           => $defaults['tmnhanphat_services_exclude_ids'],
			'sanitize_callback' => 'tmnhanphat_sanitize_id_list',
		),
		'tmnhanphat_services_include_ids' => array(
			'label'             => __( 'Include IDs (CSV — chỉ hiển thị các ID này)', 'tmnhanphat' ),
			'section'           => 'query',
			'type'              => 'text',
			'default'           => $defaults['tmnhanphat_services_include_ids'],
			'sanitize_callback' => 'tmnhanphat_sanitize_id_list',
		),

		// ----- Slider -----
		'tmnhanphat_services_autoplay_enable'    => array(
			'label'             => __( 'Enable Autoplay', 'tmnhanphat' ),
			'section'           => 'slider',
			'type'              => 'checkbox',
			'default'           => $defaults['tmnhanphat_services_autoplay_enable'],
			'sanitize_callback' => 'tmnhanphat_sanitize_checkbox',
		),
		'tmnhanphat_services_autoplay_speed'     => array(
			'label'             => __( 'Autoplay Speed (ms)', 'tmnhanphat' ),
			'section'           => 'slider',
			'type'              => 'number',
			'default'           => $defaults['tmnhanphat_services_autoplay_speed'],
			'sanitize_callback' => 'absint',
			'input_attrs'       => array( 'min' => 1000, 'max' => 15000, 'step' => 500 ),
		),
		'tmnhanphat_services_transition_speed'   => array(
			'label'             => __( 'Transition Speed (ms)', 'tmnhanphat' ),
			'section'           => 'slider',
			'type'              => 'number',
			'default'           => $defaults['tmnhanphat_services_transition_speed'],
			'sanitize_callback' => 'absint',
			'input_attrs'       => array( 'min' => 150, 'max' => 2000, 'step' => 50 ),
		),
		'tmnhanphat_services_pause_hover'        => array(
			'label'             => __( 'Pause On Hover', 'tmnhanphat' ),
			'section'           => 'slider',
			'type'              => 'checkbox',
			'default'           => $defaults['tmnhanphat_services_pause_hover'],
			'sanitize_callback' => 'tmnhanphat_sanitize_checkbox',
		),
		'tmnhanphat_services_infinite'           => array(
			'label'             => __( 'Infinite Loop', 'tmnhanphat' ),
			'section'           => 'slider',
			'type'              => 'checkbox',
			'default'           => $defaults['tmnhanphat_services_infinite'],
			'sanitize_callback' => 'tmnhanphat_sanitize_checkbox',
		),
		'tmnhanphat_services_show_arrows'        => array(
			'label'             => __( 'Show Arrows (nút Prev/Next)', 'tmnhanphat' ),
			'section'           => 'slider',
			'type'              => 'checkbox',
			'default'           => $defaults['tmnhanphat_services_show_arrows'],
			'sanitize_callback' => 'tmnhanphat_sanitize_checkbox',
		),
		'tmnhanphat_services_show_dots'          => array(
			'label'             => __( 'Show Dots', 'tmnhanphat' ),
			'section'           => 'slider',
			'type'              => 'checkbox',
			'default'           => $defaults['tmnhanphat_services_show_dots'],
			'sanitize_callback' => 'tmnhanphat_sanitize_checkbox',
		),
		'tmnhanphat_services_drag_enable'        => array(
			'label'             => __( 'Enable Drag/Swipe', 'tmnhanphat' ),
			'section'           => 'slider',
			'type'              => 'checkbox',
			'default'           => $defaults['tmnhanphat_services_drag_enable'],
			'sanitize_callback' => 'tmnhanphat_sanitize_checkbox',
		),
		'tmnhanphat_services_gap'                => array(
			'label'             => __( 'Gap giữa các Card (px)', 'tmnhanphat' ),
			'section'           => 'slider',
			'type'              => 'number',
			'default'           => $defaults['tmnhanphat_services_gap'],
			'sanitize_callback' => 'absint',
			'input_attrs'       => array( 'min' => 0, 'max' => 60, 'step' => 2 ),
		),
		'tmnhanphat_services_cards_desktop'      => array(
			'label'             => __( 'Cards Desktop', 'tmnhanphat' ),
			'section'           => 'slider',
			'choices'           => tmnhanphat_get_services_cards_choices( 'desktop' ),
			'default'           => $defaults['tmnhanphat_services_cards_desktop'],
			'sanitize_callback' => 'tmnhanphat_sanitize_services_cards_desktop',
		),
		'tmnhanphat_services_cards_tablet'       => array(
			'label'             => __( 'Cards Tablet', 'tmnhanphat' ),
			'section'           => 'slider',
			'choices'           => tmnhanphat_get_services_cards_choices( 'tablet' ),
			'default'           => $defaults['tmnhanphat_services_cards_tablet'],
			'sanitize_callback' => 'tmnhanphat_sanitize_services_cards_tablet',
		),
		'tmnhanphat_services_cards_mobile'       => array(
			'label'             => __( 'Cards Mobile', 'tmnhanphat' ),
			'section'           => 'slider',
			'choices'           => tmnhanphat_get_services_cards_choices( 'mobile' ),
			'default'           => $defaults['tmnhanphat_services_cards_mobile'],
			'sanitize_callback' => 'tmnhanphat_sanitize_services_cards_mobile',
		),
		'tmnhanphat_services_dot_active_color'   => array(
			'label'             => __( 'Dot Active Color', 'tmnhanphat' ),
			'section'           => 'slider',
			'type'              => 'color',
			'default'           => $defaults['tmnhanphat_services_dot_active_color'],
			'sanitize_callback' => 'sanitize_hex_color',
		),
		'tmnhanphat_services_dot_inactive_color' => array(
			'label'             => __( 'Dot Inactive Color', 'tmnhanphat' ),
			'section'           => 'slider',
			'type'              => 'color',
			'default'           => $defaults['tmnhanphat_services_dot_inactive_color'],
			'sanitize_callback' => 'sanitize_hex_color',
		),

		// ----- Card -----
		'tmnhanphat_services_card_radius'       => array(
			'label'             => __( 'Border Radius (px)', 'tmnhanphat' ),
			'section'           => 'card',
			'type'              => 'number',
			'default'           => $defaults['tmnhanphat_services_card_radius'],
			'sanitize_callback' => 'absint',
		),
		'tmnhanphat_services_card_ratio'        => array(
			'label'             => __( 'Image Ratio', 'tmnhanphat' ),
			'section'           => 'card',
			'choices'           => tmnhanphat_get_services_ratio_choices(),
			'default'           => $defaults['tmnhanphat_services_card_ratio'],
			'sanitize_callback' => 'tmnhanphat_sanitize_services_ratio',
		),
		'tmnhanphat_services_overlay_color'     => array(
			'label'             => __( 'Overlay Color', 'tmnhanphat' ),
			'section'           => 'card',
			'type'              => 'color',
			'default'           => $defaults['tmnhanphat_services_overlay_color'],
			'sanitize_callback' => 'sanitize_hex_color',
		),
		'tmnhanphat_services_overlay_opacity'   => array(
			'label'             => __( 'Overlay Opacity (%)', 'tmnhanphat' ),
			'section'           => 'card',
			'type'              => 'number',
			'default'           => $defaults['tmnhanphat_services_overlay_opacity'],
			'sanitize_callback' => 'absint',
			'input_attrs'       => array( 'min' => 0, 'max' => 100, 'step' => 5 ),
		),
		'tmnhanphat_services_title_color'       => array(
			'label'             => __( 'Title Color', 'tmnhanphat' ),
			'section'           => 'card',
			'type'              => 'color',
			'default'           => $defaults['tmnhanphat_services_title_color'],
			'sanitize_callback' => 'sanitize_hex_color',
		),
		'tmnhanphat_services_title_hover_color' => array(
			'label'             => __( 'Title Hover Color', 'tmnhanphat' ),
			'section'           => 'card',
			'type'              => 'color',
			'default'           => $defaults['tmnhanphat_services_title_hover_color'],
			'sanitize_callback' => 'sanitize_hex_color',
		),
		'tmnhanphat_services_title_font_size'   => array(
			'label'             => __( 'Title Font Size (px)', 'tmnhanphat' ),
			'section'           => 'card',
			'type'              => 'number',
			'default'           => $defaults['tmnhanphat_services_title_font_size'],
			'sanitize_callback' => 'absint',
		),
		'tmnhanphat_services_excerpt_color'     => array(
			'label'             => __( 'Description Color (trong Card)', 'tmnhanphat' ),
			'section'           => 'card',
			'type'              => 'color',
			'default'           => $defaults['tmnhanphat_services_excerpt_color'],
			'sanitize_callback' => 'sanitize_hex_color',
		),
		'tmnhanphat_services_excerpt_font_size' => array(
			'label'             => __( 'Description Font Size (px, trong Card)', 'tmnhanphat' ),
			'section'           => 'card',
			'type'              => 'number',
			'default'           => $defaults['tmnhanphat_services_excerpt_font_size'],
			'sanitize_callback' => 'absint',
		),
		'tmnhanphat_services_content_padding'   => array(
			'label'             => __( 'Content Padding (px)', 'tmnhanphat' ),
			'section'           => 'card',
			'type'              => 'number',
			'default'           => $defaults['tmnhanphat_services_content_padding'],
			'sanitize_callback' => 'absint',
		),
		'tmnhanphat_services_arrow_color'       => array(
			'label'             => __( 'Arrow Icon Color', 'tmnhanphat' ),
			'section'           => 'card',
			'type'              => 'color',
			'default'           => $defaults['tmnhanphat_services_arrow_color'],
			'sanitize_callback' => 'sanitize_hex_color',
		),
		'tmnhanphat_services_hover_zoom_enable' => array(
			'label'             => __( 'Hover Effect (Image Zoom nhẹ)', 'tmnhanphat' ),
			'section'           => 'card',
			'type'              => 'checkbox',
			'default'           => $defaults['tmnhanphat_services_hover_zoom_enable'],
			'sanitize_callback' => 'tmnhanphat_sanitize_checkbox',
		),
		'tmnhanphat_services_hover_duration'    => array(
			'label'             => __( 'Animation Duration (ms)', 'tmnhanphat' ),
			'section'           => 'card',
			'type'              => 'number',
			'default'           => $defaults['tmnhanphat_services_hover_duration'],
			'sanitize_callback' => 'absint',
			'input_attrs'       => array( 'min' => 100, 'max' => 1000, 'step' => 50 ),
		),

		// ----- CTA -----
		'tmnhanphat_services_cta_enable'     => array(
			'label'             => __( 'Enable CTA', 'tmnhanphat' ),
			'section'           => 'cta',
			'type'              => 'checkbox',
			'default'           => $defaults['tmnhanphat_services_cta_enable'],
			'sanitize_callback' => 'tmnhanphat_sanitize_checkbox',
		),
		'tmnhanphat_services_cta_text'       => array(
			'label'             => __( 'Button Text', 'tmnhanphat' ),
			'section'           => 'cta',
			'type'              => 'text',
			'default'           => $defaults['tmnhanphat_services_cta_text'],
			'sanitize_callback' => 'sanitize_text_field',
		),
		'tmnhanphat_services_cta_url'        => array(
			'label'             => __( 'Button URL (bỏ trống = Archive Dịch vụ)', 'tmnhanphat' ),
			'section'           => 'cta',
			'type'              => 'text',
			'default'           => $defaults['tmnhanphat_services_cta_url'],
			'sanitize_callback' => 'esc_url_raw',
		),
		'tmnhanphat_services_cta_bg'         => array(
			'label'             => __( 'Background Color', 'tmnhanphat' ),
			'section'           => 'cta',
			'type'              => 'color',
			'default'           => $defaults['tmnhanphat_services_cta_bg'],
			'sanitize_callback' => 'sanitize_hex_color',
		),
		'tmnhanphat_services_cta_text_color' => array(
			'label'             => __( 'Text Color', 'tmnhanphat' ),
			'section'           => 'cta',
			'type'              => 'color',
			'default'           => $defaults['tmnhanphat_services_cta_text_color'],
			'sanitize_callback' => 'sanitize_hex_color',
		),
		'tmnhanphat_services_cta_hover_bg'   => array(
			'label'             => __( 'Hover Background', 'tmnhanphat' ),
			'section'           => 'cta',
			'type'              => 'color',
			'default'           => $defaults['tmnhanphat_services_cta_hover_bg'],
			'sanitize_callback' => 'sanitize_hex_color',
		),
		'tmnhanphat_services_cta_radius'     => array(
			'label'             => __( 'Border Radius (px)', 'tmnhanphat' ),
			'section'           => 'cta',
			'type'              => 'number',
			'default'           => $defaults['tmnhanphat_services_cta_radius'],
			'sanitize_callback' => 'absint',
		),
		'tmnhanphat_services_cta_padding_x'  => array(
			'label'             => __( 'Padding ngang (px)', 'tmnhanphat' ),
			'section'           => 'cta',
			'type'              => 'number',
			'default'           => $defaults['tmnhanphat_services_cta_padding_x'],
			'sanitize_callback' => 'absint',
		),
		'tmnhanphat_services_cta_padding_y'  => array(
			'label'             => __( 'Padding dọc (px)', 'tmnhanphat' ),
			'section'           => 'cta',
			'type'              => 'number',
			'default'           => $defaults['tmnhanphat_services_cta_padding_y'],
			'sanitize_callback' => 'absint',
		),
		'tmnhanphat_services_cta_font_size'  => array(
			'label'             => __( 'Font Size (px)', 'tmnhanphat' ),
			'section'           => 'cta',
			'type'              => 'number',
			'default'           => $defaults['tmnhanphat_services_cta_font_size'],
			'sanitize_callback' => 'absint',
		),
		'tmnhanphat_services_cta_margin_top' => array(
			'label'             => __( 'Margin Top (px)', 'tmnhanphat' ),
			'section'           => 'cta',
			'type'              => 'number',
			'default'           => $defaults['tmnhanphat_services_cta_margin_top'],
			'sanitize_callback' => 'absint',
		),
	);
}

/**
 * Đăng ký Panel "Service Home" + toàn bộ Section/Setting/Control.
 *
 * @param WP_Customize_Manager $wp_customize Customizer manager instance.
 */
function tmnhanphat_services_customizer_register( $wp_customize ) {
	$wp_customize->add_panel( 'tmnhanphat_services_panel', array(
		'title'    => __( 'Service Home', 'tmnhanphat' ),
		'priority' => 33,
	) );

	$sections = array(
		'general'     => __( 'General', 'tmnhanphat' ),
		'heading'     => __( 'Heading', 'tmnhanphat' ),
		'description' => __( 'Description', 'tmnhanphat' ),
		'query'       => __( 'Query', 'tmnhanphat' ),
		'slider'      => __( 'Slider', 'tmnhanphat' ),
		'card'        => __( 'Card', 'tmnhanphat' ),
		'cta'         => __( 'CTA', 'tmnhanphat' ),
	);

	foreach ( $sections as $section_id => $section_label ) {
		$wp_customize->add_section( "tmnhanphat_services_section_{$section_id}", array(
			'title' => $section_label,
			'panel' => 'tmnhanphat_services_panel',
		) );
	}

	foreach ( tmnhanphat_get_services_customizer_fields() as $field_id => $field ) {
		$wp_customize->add_setting( $field_id, array(
			'default'           => $field['default'],
			'sanitize_callback' => $field['sanitize_callback'],
			'transport'         => 'refresh',
		) );

		$control_args = array(
			'label'    => $field['label'],
			'section'  => 'tmnhanphat_services_section_' . $field['section'],
			'settings' => $field_id,
		);

		if ( ! empty( $field['choices'] ) ) {
			$control_args['type']    = 'select';
			$control_args['choices'] = $field['choices'];
			$wp_customize->add_control( $field_id, $control_args );
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
add_action( 'customize_register', 'tmnhanphat_services_customizer_register' );
