<?php
/**
 * Đăng ký Customizer Panel riêng "FAQ Home" — "Câu hỏi thường gặp" (PROJECT_RULES.md
 * mục 26 — mỗi feature Homepage 1 Panel cấp cao nhất, priority nối tiếp dải 30-59):
 * General, Header, Query, FAQ Card, Number Badge, Question, Answer, Icon, Accordion.
 * Cùng quy ước config-driven (mục 22).
 *
 * Nội dung FAQ nằm 100% ở CPT faq (Title=Question, Editor=Answer) — Customizer chỉ
 * điều khiển query + hiển thị + accordion, không có field nội dung.
 *
 * @package TMNhanPhat
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Khai báo toàn bộ field của FAQ Section dạng config (id => định nghĩa).
 *
 * @return array<string, array>
 */
function tmnhanphat_get_faq_customizer_fields() {
	$defaults = tmnhanphat_faq_defaults();

	return array(
		// ----- General -----
		'tmnhanphat_faq_enable'          => array(
			'label'             => __( 'Hiển thị FAQ Section', 'tmnhanphat' ),
			'section'           => 'general',
			'type'              => 'checkbox',
			'default'           => $defaults['tmnhanphat_faq_enable'],
			'sanitize_callback' => 'tmnhanphat_sanitize_checkbox',
		),
		'tmnhanphat_faq_section_id'      => array(
			'label'             => __( 'Section ID (anchor)', 'tmnhanphat' ),
			'section'           => 'general',
			'type'              => 'text',
			'default'           => $defaults['tmnhanphat_faq_section_id'],
			'sanitize_callback' => 'sanitize_key',
		),
		'tmnhanphat_faq_container_width' => array(
			'label'             => __( 'Container Width (px, 0 = dùng Global Settings)', 'tmnhanphat' ),
			'section'           => 'general',
			'type'              => 'number',
			'default'           => $defaults['tmnhanphat_faq_container_width'],
			'sanitize_callback' => 'absint',
			'input_attrs'       => array( 'min' => 0, 'max' => 1920, 'step' => 10 ),
		),
		'tmnhanphat_faq_bg_color'        => array(
			'label'             => __( 'Background Color', 'tmnhanphat' ),
			'section'           => 'general',
			'type'              => 'color',
			'default'           => $defaults['tmnhanphat_faq_bg_color'],
			'sanitize_callback' => 'sanitize_hex_color',
		),
		'tmnhanphat_faq_bg_image'        => array(
			'label'             => __( 'Background Image (tuỳ chọn)', 'tmnhanphat' ),
			'section'           => 'general',
			'type'              => 'image',
			'default'           => $defaults['tmnhanphat_faq_bg_image'],
			'sanitize_callback' => 'esc_url_raw',
		),
		'tmnhanphat_faq_padding_desktop' => array(
			'label'             => __( 'Section Padding — Desktop (px)', 'tmnhanphat' ),
			'section'           => 'general',
			'type'              => 'number',
			'default'           => $defaults['tmnhanphat_faq_padding_desktop'],
			'sanitize_callback' => 'absint',
			'input_attrs'       => array( 'min' => 0, 'max' => 240, 'step' => 4 ),
		),
		'tmnhanphat_faq_padding_tablet'  => array(
			'label'             => __( 'Section Padding — Tablet (px)', 'tmnhanphat' ),
			'section'           => 'general',
			'type'              => 'number',
			'default'           => $defaults['tmnhanphat_faq_padding_tablet'],
			'sanitize_callback' => 'absint',
			'input_attrs'       => array( 'min' => 0, 'max' => 200, 'step' => 4 ),
		),
		'tmnhanphat_faq_padding_mobile'  => array(
			'label'             => __( 'Section Padding — Mobile (px)', 'tmnhanphat' ),
			'section'           => 'general',
			'type'              => 'number',
			'default'           => $defaults['tmnhanphat_faq_padding_mobile'],
			'sanitize_callback' => 'absint',
			'input_attrs'       => array( 'min' => 0, 'max' => 160, 'step' => 4 ),
		),
		'tmnhanphat_faq_margin_desktop'  => array(
			'label'             => __( 'Section Margin — Desktop (px)', 'tmnhanphat' ),
			'section'           => 'general',
			'type'              => 'number',
			'default'           => $defaults['tmnhanphat_faq_margin_desktop'],
			'sanitize_callback' => 'absint',
			'input_attrs'       => array( 'min' => 0, 'max' => 240, 'step' => 4 ),
		),
		'tmnhanphat_faq_margin_tablet'   => array(
			'label'             => __( 'Section Margin — Tablet (px)', 'tmnhanphat' ),
			'section'           => 'general',
			'type'              => 'number',
			'default'           => $defaults['tmnhanphat_faq_margin_tablet'],
			'sanitize_callback' => 'absint',
			'input_attrs'       => array( 'min' => 0, 'max' => 200, 'step' => 4 ),
		),
		'tmnhanphat_faq_margin_mobile'   => array(
			'label'             => __( 'Section Margin — Mobile (px)', 'tmnhanphat' ),
			'section'           => 'general',
			'type'              => 'number',
			'default'           => $defaults['tmnhanphat_faq_margin_mobile'],
			'sanitize_callback' => 'absint',
			'input_attrs'       => array( 'min' => 0, 'max' => 160, 'step' => 4 ),
		),

		// ----- Header -----
		'tmnhanphat_faq_reuse_heading'        => array(
			'label'             => __( 'Reuse Heading Style (gạch đôi đỏ/xanh của Review Section)', 'tmnhanphat' ),
			'section'           => 'header',
			'type'              => 'checkbox',
			'default'           => $defaults['tmnhanphat_faq_reuse_heading'],
			'sanitize_callback' => 'tmnhanphat_sanitize_checkbox',
		),
		'tmnhanphat_faq_small_title'          => array(
			'label'             => __( 'Small Title', 'tmnhanphat' ),
			'section'           => 'header',
			'type'              => 'text',
			'default'           => $defaults['tmnhanphat_faq_small_title'],
			'sanitize_callback' => 'sanitize_text_field',
		),
		'tmnhanphat_faq_small_size'           => array(
			'label'             => __( 'Small Title Font Size (px)', 'tmnhanphat' ),
			'section'           => 'header',
			'type'              => 'number',
			'default'           => $defaults['tmnhanphat_faq_small_size'],
			'sanitize_callback' => 'absint',
			'input_attrs'       => array( 'min' => 10, 'max' => 20, 'step' => 1 ),
		),
		'tmnhanphat_faq_heading_blue_text'    => array(
			'label'             => __( 'Blue Title', 'tmnhanphat' ),
			'section'           => 'header',
			'type'              => 'text',
			'default'           => $defaults['tmnhanphat_faq_heading_blue_text'],
			'sanitize_callback' => 'sanitize_text_field',
		),
		'tmnhanphat_faq_heading_red_text'     => array(
			'label'             => __( 'Red Title', 'tmnhanphat' ),
			'section'           => 'header',
			'type'              => 'text',
			'default'           => $defaults['tmnhanphat_faq_heading_red_text'],
			'sanitize_callback' => 'sanitize_text_field',
		),
		'tmnhanphat_faq_heading_blue_color'   => array(
			'label'             => __( 'Title Color Blue', 'tmnhanphat' ),
			'section'           => 'header',
			'type'              => 'color',
			'default'           => $defaults['tmnhanphat_faq_heading_blue_color'],
			'sanitize_callback' => 'sanitize_hex_color',
		),
		'tmnhanphat_faq_heading_red_color'    => array(
			'label'             => __( 'Title Color Red', 'tmnhanphat' ),
			'section'           => 'header',
			'type'              => 'color',
			'default'           => $defaults['tmnhanphat_faq_heading_red_color'],
			'sanitize_callback' => 'sanitize_hex_color',
		),
		'tmnhanphat_faq_title_size'           => array(
			'label'             => __( 'Title Font Size — Desktop (px)', 'tmnhanphat' ),
			'section'           => 'header',
			'type'              => 'number',
			'default'           => $defaults['tmnhanphat_faq_title_size'],
			'sanitize_callback' => 'absint',
			'input_attrs'       => array( 'min' => 20, 'max' => 72, 'step' => 1 ),
		),
		'tmnhanphat_faq_title_size_tablet'    => array(
			'label'             => __( 'Title Font Size — Tablet (px)', 'tmnhanphat' ),
			'section'           => 'header',
			'type'              => 'number',
			'default'           => $defaults['tmnhanphat_faq_title_size_tablet'],
			'sanitize_callback' => 'absint',
			'input_attrs'       => array( 'min' => 18, 'max' => 60, 'step' => 1 ),
		),
		'tmnhanphat_faq_title_size_mobile'    => array(
			'label'             => __( 'Title Font Size — Mobile (px)', 'tmnhanphat' ),
			'section'           => 'header',
			'type'              => 'number',
			'default'           => $defaults['tmnhanphat_faq_title_size_mobile'],
			'sanitize_callback' => 'absint',
			'input_attrs'       => array( 'min' => 16, 'max' => 48, 'step' => 1 ),
		),
		'tmnhanphat_faq_desc_text'            => array(
			'label'             => __( 'Description', 'tmnhanphat' ),
			'section'           => 'header',
			'type'              => 'textarea',
			'default'           => $defaults['tmnhanphat_faq_desc_text'],
			'sanitize_callback' => 'sanitize_textarea_field',
		),
		'tmnhanphat_faq_desc_color'           => array(
			'label'             => __( 'Description Color', 'tmnhanphat' ),
			'section'           => 'header',
			'type'              => 'color',
			'default'           => $defaults['tmnhanphat_faq_desc_color'],
			'sanitize_callback' => 'sanitize_hex_color',
		),
		'tmnhanphat_faq_desc_size'            => array(
			'label'             => __( 'Description Font Size (px)', 'tmnhanphat' ),
			'section'           => 'header',
			'type'              => 'number',
			'default'           => $defaults['tmnhanphat_faq_desc_size'],
			'sanitize_callback' => 'absint',
			'input_attrs'       => array( 'min' => 12, 'max' => 24, 'step' => 1 ),
		),
		'tmnhanphat_faq_desc_max_width'       => array(
			'label'             => __( 'Description Max Width (px)', 'tmnhanphat' ),
			'section'           => 'header',
			'type'              => 'number',
			'default'           => $defaults['tmnhanphat_faq_desc_max_width'],
			'sanitize_callback' => 'absint',
			'input_attrs'       => array( 'min' => 300, 'max' => 1000, 'step' => 10 ),
		),
		'tmnhanphat_faq_header_align'         => array(
			'label'             => __( 'Header Alignment', 'tmnhanphat' ),
			'section'           => 'header',
			'choices'           => tmnhanphat_get_services_align_choices(),
			'default'           => $defaults['tmnhanphat_faq_header_align'],
			'sanitize_callback' => 'tmnhanphat_sanitize_services_align',
		),
		'tmnhanphat_faq_header_margin_bottom' => array(
			'label'             => __( 'Header Bottom Spacing (px)', 'tmnhanphat' ),
			'section'           => 'header',
			'type'              => 'number',
			'default'           => $defaults['tmnhanphat_faq_header_margin_bottom'],
			'sanitize_callback' => 'absint',
			'input_attrs'       => array( 'min' => 0, 'max' => 120, 'step' => 2 ),
		),

		// ----- Query -----
		'tmnhanphat_faq_count'      => array(
			'label'             => __( 'Posts Per Page', 'tmnhanphat' ),
			'section'           => 'query',
			'type'              => 'number',
			'default'           => $defaults['tmnhanphat_faq_count'],
			'sanitize_callback' => 'absint',
			'input_attrs'       => array( 'min' => 1, 'max' => 20, 'step' => 1 ),
		),
		'tmnhanphat_faq_orderby'    => array(
			'label'             => __( 'Order By', 'tmnhanphat' ),
			'section'           => 'query',
			'choices'           => tmnhanphat_get_faq_orderby_choices(),
			'default'           => $defaults['tmnhanphat_faq_orderby'],
			'sanitize_callback' => 'tmnhanphat_sanitize_faq_orderby',
		),
		'tmnhanphat_faq_order'      => array(
			'label'             => __( 'Order', 'tmnhanphat' ),
			'section'           => 'query',
			'choices'           => array(
				'ASC'  => __( 'Tăng dần', 'tmnhanphat' ),
				'DESC' => __( 'Giảm dần', 'tmnhanphat' ),
			),
			'default'           => $defaults['tmnhanphat_faq_order'],
			'sanitize_callback' => 'tmnhanphat_sanitize_faq_order',
		),
		'tmnhanphat_faq_hide_draft' => array(
			'label'             => __( 'Hide Draft', 'tmnhanphat' ),
			'section'           => 'query',
			'type'              => 'checkbox',
			'default'           => $defaults['tmnhanphat_faq_hide_draft'],
			'sanitize_callback' => 'tmnhanphat_sanitize_checkbox',
		),
		'tmnhanphat_faq_hide_empty' => array(
			'label'             => __( 'Hide Empty (ẩn FAQ trống)', 'tmnhanphat' ),
			'section'           => 'query',
			'type'              => 'checkbox',
			'default'           => $defaults['tmnhanphat_faq_hide_empty'],
			'sanitize_callback' => 'tmnhanphat_sanitize_checkbox',
		),

		// ----- FAQ Card -----
		'tmnhanphat_faq_cols_desktop'        => array(
			'label'             => __( 'Columns Desktop', 'tmnhanphat' ),
			'section'           => 'card',
			'type'              => 'number',
			'default'           => $defaults['tmnhanphat_faq_cols_desktop'],
			'sanitize_callback' => 'absint',
			'input_attrs'       => array( 'min' => 1, 'max' => 3, 'step' => 1 ),
		),
		'tmnhanphat_faq_cols_tablet'         => array(
			'label'             => __( 'Columns Tablet', 'tmnhanphat' ),
			'section'           => 'card',
			'type'              => 'number',
			'default'           => $defaults['tmnhanphat_faq_cols_tablet'],
			'sanitize_callback' => 'absint',
			'input_attrs'       => array( 'min' => 1, 'max' => 3, 'step' => 1 ),
		),
		'tmnhanphat_faq_cols_mobile'         => array(
			'label'             => __( 'Columns Mobile', 'tmnhanphat' ),
			'section'           => 'card',
			'type'              => 'number',
			'default'           => $defaults['tmnhanphat_faq_cols_mobile'],
			'sanitize_callback' => 'absint',
			'input_attrs'       => array( 'min' => 1, 'max' => 2, 'step' => 1 ),
		),
		'tmnhanphat_faq_gap'                 => array(
			'label'             => __( 'Gap (px)', 'tmnhanphat' ),
			'section'           => 'card',
			'type'              => 'number',
			'default'           => $defaults['tmnhanphat_faq_gap'],
			'sanitize_callback' => 'absint',
			'input_attrs'       => array( 'min' => 0, 'max' => 48, 'step' => 2 ),
		),
		'tmnhanphat_faq_card_radius'         => array(
			'label'             => __( 'Card Radius (px)', 'tmnhanphat' ),
			'section'           => 'card',
			'type'              => 'number',
			'default'           => $defaults['tmnhanphat_faq_card_radius'],
			'sanitize_callback' => 'absint',
			'input_attrs'       => array( 'min' => 0, 'max' => 30, 'step' => 1 ),
		),
		'tmnhanphat_faq_card_padding'        => array(
			'label'             => __( 'Card Padding (px)', 'tmnhanphat' ),
			'section'           => 'card',
			'type'              => 'number',
			'default'           => $defaults['tmnhanphat_faq_card_padding'],
			'sanitize_callback' => 'absint',
			'input_attrs'       => array( 'min' => 8, 'max' => 40, 'step' => 2 ),
		),
		'tmnhanphat_faq_border_width'        => array(
			'label'             => __( 'Border Width (px)', 'tmnhanphat' ),
			'section'           => 'card',
			'type'              => 'number',
			'default'           => $defaults['tmnhanphat_faq_border_width'],
			'sanitize_callback' => 'absint',
			'input_attrs'       => array( 'min' => 0, 'max' => 4, 'step' => 1 ),
		),
		'tmnhanphat_faq_border_color'        => array(
			'label'             => __( 'Border Color', 'tmnhanphat' ),
			'section'           => 'card',
			'type'              => 'color',
			'default'           => $defaults['tmnhanphat_faq_border_color'],
			'sanitize_callback' => 'sanitize_hex_color',
		),
		'tmnhanphat_faq_active_border_color' => array(
			'label'             => __( 'Active Border Color (viền trái khi mở)', 'tmnhanphat' ),
			'section'           => 'card',
			'type'              => 'color',
			'default'           => $defaults['tmnhanphat_faq_active_border_color'],
			'sanitize_callback' => 'sanitize_hex_color',
		),
		'tmnhanphat_faq_card_shadow'         => array(
			'label'             => __( 'Shadow', 'tmnhanphat' ),
			'section'           => 'card',
			'choices'           => tmnhanphat_get_why_choose_shadow_choices(),
			'default'           => $defaults['tmnhanphat_faq_card_shadow'],
			'sanitize_callback' => 'tmnhanphat_sanitize_why_choose_shadow',
		),
		'tmnhanphat_faq_card_hover_shadow'   => array(
			'label'             => __( 'Hover Shadow', 'tmnhanphat' ),
			'section'           => 'card',
			'choices'           => tmnhanphat_get_why_choose_shadow_choices(),
			'default'           => $defaults['tmnhanphat_faq_card_hover_shadow'],
			'sanitize_callback' => 'tmnhanphat_sanitize_why_choose_shadow',
		),

		// ----- Number Badge -----
		'tmnhanphat_faq_badge_enable'       => array(
			'label'             => __( 'Show Badge', 'tmnhanphat' ),
			'section'           => 'badge',
			'type'              => 'checkbox',
			'default'           => $defaults['tmnhanphat_faq_badge_enable'],
			'sanitize_callback' => 'tmnhanphat_sanitize_checkbox',
		),
		'tmnhanphat_faq_badge_size'         => array(
			'label'             => __( 'Badge Size (px)', 'tmnhanphat' ),
			'section'           => 'badge',
			'type'              => 'number',
			'default'           => $defaults['tmnhanphat_faq_badge_size'],
			'sanitize_callback' => 'absint',
			'input_attrs'       => array( 'min' => 24, 'max' => 56, 'step' => 2 ),
		),
		'tmnhanphat_faq_badge_radius'       => array(
			'label'             => __( 'Badge Radius (px)', 'tmnhanphat' ),
			'section'           => 'badge',
			'type'              => 'number',
			'default'           => $defaults['tmnhanphat_faq_badge_radius'],
			'sanitize_callback' => 'absint',
			'input_attrs'       => array( 'min' => 0, 'max' => 30, 'step' => 1 ),
		),
		'tmnhanphat_faq_badge_bg'           => array(
			'label'             => __( 'Normal Background', 'tmnhanphat' ),
			'section'           => 'badge',
			'type'              => 'color',
			'default'           => $defaults['tmnhanphat_faq_badge_bg'],
			'sanitize_callback' => 'sanitize_hex_color',
		),
		'tmnhanphat_faq_badge_color'        => array(
			'label'             => __( 'Normal Color', 'tmnhanphat' ),
			'section'           => 'badge',
			'type'              => 'color',
			'default'           => $defaults['tmnhanphat_faq_badge_color'],
			'sanitize_callback' => 'sanitize_hex_color',
		),
		'tmnhanphat_faq_badge_active_bg'    => array(
			'label'             => __( 'Active Background', 'tmnhanphat' ),
			'section'           => 'badge',
			'type'              => 'color',
			'default'           => $defaults['tmnhanphat_faq_badge_active_bg'],
			'sanitize_callback' => 'sanitize_hex_color',
		),
		'tmnhanphat_faq_badge_active_color' => array(
			'label'             => __( 'Active Color', 'tmnhanphat' ),
			'section'           => 'badge',
			'type'              => 'color',
			'default'           => $defaults['tmnhanphat_faq_badge_active_color'],
			'sanitize_callback' => 'sanitize_hex_color',
		),

		// ----- Question -----
		'tmnhanphat_faq_q_size'        => array(
			'label'             => __( 'Font Size — Desktop (px)', 'tmnhanphat' ),
			'section'           => 'question',
			'type'              => 'number',
			'default'           => $defaults['tmnhanphat_faq_q_size'],
			'sanitize_callback' => 'absint',
			'input_attrs'       => array( 'min' => 13, 'max' => 24, 'step' => 1 ),
		),
		'tmnhanphat_faq_q_size_tablet' => array(
			'label'             => __( 'Font Size — Tablet (px)', 'tmnhanphat' ),
			'section'           => 'question',
			'type'              => 'number',
			'default'           => $defaults['tmnhanphat_faq_q_size_tablet'],
			'sanitize_callback' => 'absint',
			'input_attrs'       => array( 'min' => 12, 'max' => 22, 'step' => 1 ),
		),
		'tmnhanphat_faq_q_size_mobile' => array(
			'label'             => __( 'Font Size — Mobile (px)', 'tmnhanphat' ),
			'section'           => 'question',
			'type'              => 'number',
			'default'           => $defaults['tmnhanphat_faq_q_size_mobile'],
			'sanitize_callback' => 'absint',
			'input_attrs'       => array( 'min' => 12, 'max' => 20, 'step' => 1 ),
		),
		'tmnhanphat_faq_q_weight'      => array(
			'label'             => __( 'Weight', 'tmnhanphat' ),
			'section'           => 'question',
			'choices'           => tmnhanphat_get_font_weight_choices(),
			'default'           => $defaults['tmnhanphat_faq_q_weight'],
			'sanitize_callback' => 'tmnhanphat_sanitize_font_weight',
		),
		'tmnhanphat_faq_q_color'       => array(
			'label'             => __( 'Color', 'tmnhanphat' ),
			'section'           => 'question',
			'type'              => 'color',
			'default'           => $defaults['tmnhanphat_faq_q_color'],
			'sanitize_callback' => 'sanitize_hex_color',
		),
		'tmnhanphat_faq_q_hover_color' => array(
			'label'             => __( 'Hover / Active Color', 'tmnhanphat' ),
			'section'           => 'question',
			'type'              => 'color',
			'default'           => $defaults['tmnhanphat_faq_q_hover_color'],
			'sanitize_callback' => 'sanitize_hex_color',
		),

		// ----- Answer -----
		'tmnhanphat_faq_a_size'           => array(
			'label'             => __( 'Font Size (px)', 'tmnhanphat' ),
			'section'           => 'answer',
			'type'              => 'number',
			'default'           => $defaults['tmnhanphat_faq_a_size'],
			'sanitize_callback' => 'absint',
			'input_attrs'       => array( 'min' => 12, 'max' => 18, 'step' => 1 ),
		),
		'tmnhanphat_faq_a_color'          => array(
			'label'             => __( 'Color', 'tmnhanphat' ),
			'section'           => 'answer',
			'type'              => 'color',
			'default'           => $defaults['tmnhanphat_faq_a_color'],
			'sanitize_callback' => 'sanitize_hex_color',
		),
		'tmnhanphat_faq_a_line_height'    => array(
			'label'             => __( 'Line Height (vd 1.7)', 'tmnhanphat' ),
			'section'           => 'answer',
			'type'              => 'number',
			'default'           => $defaults['tmnhanphat_faq_a_line_height'],
			'sanitize_callback' => 'tmnhanphat_sanitize_faq_line_height',
			'input_attrs'       => array( 'min' => 1, 'max' => 3, 'step' => 0.05 ),
		),
		'tmnhanphat_faq_a_padding_top'    => array(
			'label'             => __( 'Padding Top (px)', 'tmnhanphat' ),
			'section'           => 'answer',
			'type'              => 'number',
			'default'           => $defaults['tmnhanphat_faq_a_padding_top'],
			'sanitize_callback' => 'absint',
			'input_attrs'       => array( 'min' => 0, 'max' => 40, 'step' => 1 ),
		),
		'tmnhanphat_faq_a_padding_bottom' => array(
			'label'             => __( 'Padding Bottom (px)', 'tmnhanphat' ),
			'section'           => 'answer',
			'type'              => 'number',
			'default'           => $defaults['tmnhanphat_faq_a_padding_bottom'],
			'sanitize_callback' => 'absint',
			'input_attrs'       => array( 'min' => 0, 'max' => 40, 'step' => 1 ),
		),

		// ----- Icon -----
		'tmnhanphat_faq_icon_enable'      => array(
			'label'             => __( 'Show Icon', 'tmnhanphat' ),
			'section'           => 'icon',
			'type'              => 'checkbox',
			'default'           => $defaults['tmnhanphat_faq_icon_enable'],
			'sanitize_callback' => 'tmnhanphat_sanitize_checkbox',
		),
		'tmnhanphat_faq_icon_size'        => array(
			'label'             => __( 'Icon Size (px)', 'tmnhanphat' ),
			'section'           => 'icon',
			'type'              => 'number',
			'default'           => $defaults['tmnhanphat_faq_icon_size'],
			'sanitize_callback' => 'absint',
			'input_attrs'       => array( 'min' => 10, 'max' => 28, 'step' => 1 ),
		),
		'tmnhanphat_faq_circle_size'      => array(
			'label'             => __( 'Circle Size (px)', 'tmnhanphat' ),
			'section'           => 'icon',
			'type'              => 'number',
			'default'           => $defaults['tmnhanphat_faq_circle_size'],
			'sanitize_callback' => 'absint',
			'input_attrs'       => array( 'min' => 20, 'max' => 48, 'step' => 2 ),
		),
		'tmnhanphat_faq_circle_bg'        => array(
			'label'             => __( 'Circle Background', 'tmnhanphat' ),
			'section'           => 'icon',
			'type'              => 'color',
			'default'           => $defaults['tmnhanphat_faq_circle_bg'],
			'sanitize_callback' => 'sanitize_hex_color',
		),
		'tmnhanphat_faq_circle_active_bg' => array(
			'label'             => __( 'Circle Active Background', 'tmnhanphat' ),
			'section'           => 'icon',
			'type'              => 'color',
			'default'           => $defaults['tmnhanphat_faq_circle_active_bg'],
			'sanitize_callback' => 'sanitize_hex_color',
		),

		// ----- Accordion -----
		'tmnhanphat_faq_allow_multiple'    => array(
			'label'             => __( 'Allow Multiple Open (mặc định TẮT — chỉ 1 FAQ mở)', 'tmnhanphat' ),
			'section'           => 'accordion',
			'type'              => 'checkbox',
			'default'           => $defaults['tmnhanphat_faq_allow_multiple'],
			'sanitize_callback' => 'tmnhanphat_sanitize_checkbox',
		),
		'tmnhanphat_faq_expand_duration'   => array(
			'label'             => __( 'Expand Duration (ms)', 'tmnhanphat' ),
			'section'           => 'accordion',
			'type'              => 'number',
			'default'           => $defaults['tmnhanphat_faq_expand_duration'],
			'sanitize_callback' => 'absint',
			'input_attrs'       => array( 'min' => 100, 'max' => 1000, 'step' => 50 ),
		),
		'tmnhanphat_faq_collapse_duration' => array(
			'label'             => __( 'Collapse Duration (ms)', 'tmnhanphat' ),
			'section'           => 'accordion',
			'type'              => 'number',
			'default'           => $defaults['tmnhanphat_faq_collapse_duration'],
			'sanitize_callback' => 'absint',
			'input_attrs'       => array( 'min' => 100, 'max' => 1000, 'step' => 50 ),
		),
	);
}

/**
 * Đăng ký Panel "FAQ Home" + toàn bộ Section/Setting/Control.
 * Priority 42: nối tiếp Quote Contact Home (41) trong dải Homepage panel.
 *
 * @param WP_Customize_Manager $wp_customize Customizer manager instance.
 */
function tmnhanphat_faq_customizer_register( $wp_customize ) {
	$wp_customize->add_panel( 'tmnhanphat_faq_panel', array(
		'title'    => __( 'FAQ Home', 'tmnhanphat' ),
		'priority' => 42,
	) );

	$sections = array(
		'general'   => __( 'General', 'tmnhanphat' ),
		'header'    => __( 'Header', 'tmnhanphat' ),
		'query'     => __( 'Query', 'tmnhanphat' ),
		'card'      => __( 'FAQ Card', 'tmnhanphat' ),
		'badge'     => __( 'Number Badge', 'tmnhanphat' ),
		'question'  => __( 'Question', 'tmnhanphat' ),
		'answer'    => __( 'Answer', 'tmnhanphat' ),
		'icon'      => __( 'Icon', 'tmnhanphat' ),
		'accordion' => __( 'Accordion', 'tmnhanphat' ),
	);

	foreach ( $sections as $section_id => $section_label ) {
		$wp_customize->add_section( "tmnhanphat_faq_section_{$section_id}", array(
			'title' => $section_label,
			'panel' => 'tmnhanphat_faq_panel',
		) );
	}

	foreach ( tmnhanphat_get_faq_customizer_fields() as $field_id => $field ) {
		$wp_customize->add_setting( $field_id, array(
			'default'           => $field['default'],
			'sanitize_callback' => $field['sanitize_callback'],
			'transport'         => 'refresh',
		) );

		$control_args = array(
			'label'    => $field['label'],
			'section'  => 'tmnhanphat_faq_section_' . $field['section'],
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
add_action( 'customize_register', 'tmnhanphat_faq_customizer_register' );
