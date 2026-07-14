<?php
/**
 * Đăng ký Customizer Panel riêng "Quote Contact Home" — "Liên hệ báo giá"
 * (PROJECT_RULES.md mục 26 — mỗi feature Homepage 1 Panel cấp cao nhất, priority nối
 * tiếp dải 30-59): General, Left Card, Form Header, Form, Service Options, Button,
 * Verify, Right Content, Contact Information. Cùng quy ước config-driven (mục 22).
 *
 * Email/Phone/Address KHÔNG khai ở đây — tái sử dụng mod Footer (panel Footer là
 * nguồn DUY NHẤT cho thông tin công ty, xem tmnhanphat_get_quote_contact_items()).
 * Panel này chỉ có toggle hiển thị + icon/màu cho phần Contact.
 *
 * @package TMNhanPhat
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Khai báo toàn bộ field của Quote Section dạng config (id => định nghĩa).
 *
 * @return array<string, array>
 */
function tmnhanphat_get_quote_customizer_fields() {
	$defaults = tmnhanphat_quote_defaults();

	return array(
		// ----- General -----
		'tmnhanphat_quote_enable'          => array(
			'label'             => __( 'Hiển thị Quote Contact Section', 'tmnhanphat' ),
			'section'           => 'general',
			'type'              => 'checkbox',
			'default'           => $defaults['tmnhanphat_quote_enable'],
			'sanitize_callback' => 'tmnhanphat_sanitize_checkbox',
		),
		'tmnhanphat_quote_section_id'      => array(
			'label'             => __( 'Section ID (anchor)', 'tmnhanphat' ),
			'section'           => 'general',
			'type'              => 'text',
			'default'           => $defaults['tmnhanphat_quote_section_id'],
			'sanitize_callback' => 'sanitize_key',
		),
		'tmnhanphat_quote_container_width' => array(
			'label'             => __( 'Container Width (px, 0 = dùng Global Settings)', 'tmnhanphat' ),
			'section'           => 'general',
			'type'              => 'number',
			'default'           => $defaults['tmnhanphat_quote_container_width'],
			'sanitize_callback' => 'absint',
			'input_attrs'       => array( 'min' => 0, 'max' => 1920, 'step' => 10 ),
		),
		'tmnhanphat_quote_bg_color'        => array(
			'label'             => __( 'Background Color', 'tmnhanphat' ),
			'section'           => 'general',
			'type'              => 'color',
			'default'           => $defaults['tmnhanphat_quote_bg_color'],
			'sanitize_callback' => 'sanitize_hex_color',
		),
		'tmnhanphat_quote_bg_image'        => array(
			'label'             => __( 'Background Image (tuỳ chọn)', 'tmnhanphat' ),
			'section'           => 'general',
			'type'              => 'image',
			'default'           => $defaults['tmnhanphat_quote_bg_image'],
			'sanitize_callback' => 'esc_url_raw',
		),
		'tmnhanphat_quote_gap'             => array(
			'label'             => __( 'Gap giữa 2 cột (px)', 'tmnhanphat' ),
			'section'           => 'general',
			'type'              => 'number',
			'default'           => $defaults['tmnhanphat_quote_gap'],
			'sanitize_callback' => 'absint',
			'input_attrs'       => array( 'min' => 0, 'max' => 120, 'step' => 4 ),
		),
		'tmnhanphat_quote_padding_desktop' => array(
			'label'             => __( 'Section Padding — Desktop (px)', 'tmnhanphat' ),
			'section'           => 'general',
			'type'              => 'number',
			'default'           => $defaults['tmnhanphat_quote_padding_desktop'],
			'sanitize_callback' => 'absint',
			'input_attrs'       => array( 'min' => 0, 'max' => 240, 'step' => 4 ),
		),
		'tmnhanphat_quote_padding_tablet'  => array(
			'label'             => __( 'Section Padding — Tablet (px)', 'tmnhanphat' ),
			'section'           => 'general',
			'type'              => 'number',
			'default'           => $defaults['tmnhanphat_quote_padding_tablet'],
			'sanitize_callback' => 'absint',
			'input_attrs'       => array( 'min' => 0, 'max' => 200, 'step' => 4 ),
		),
		'tmnhanphat_quote_padding_mobile'  => array(
			'label'             => __( 'Section Padding — Mobile (px)', 'tmnhanphat' ),
			'section'           => 'general',
			'type'              => 'number',
			'default'           => $defaults['tmnhanphat_quote_padding_mobile'],
			'sanitize_callback' => 'absint',
			'input_attrs'       => array( 'min' => 0, 'max' => 160, 'step' => 4 ),
		),
		'tmnhanphat_quote_margin_desktop'  => array(
			'label'             => __( 'Section Margin — Desktop (px)', 'tmnhanphat' ),
			'section'           => 'general',
			'type'              => 'number',
			'default'           => $defaults['tmnhanphat_quote_margin_desktop'],
			'sanitize_callback' => 'absint',
			'input_attrs'       => array( 'min' => 0, 'max' => 240, 'step' => 4 ),
		),
		'tmnhanphat_quote_margin_tablet'   => array(
			'label'             => __( 'Section Margin — Tablet (px)', 'tmnhanphat' ),
			'section'           => 'general',
			'type'              => 'number',
			'default'           => $defaults['tmnhanphat_quote_margin_tablet'],
			'sanitize_callback' => 'absint',
			'input_attrs'       => array( 'min' => 0, 'max' => 200, 'step' => 4 ),
		),
		'tmnhanphat_quote_margin_mobile'   => array(
			'label'             => __( 'Section Margin — Mobile (px)', 'tmnhanphat' ),
			'section'           => 'general',
			'type'              => 'number',
			'default'           => $defaults['tmnhanphat_quote_margin_mobile'],
			'sanitize_callback' => 'absint',
			'input_attrs'       => array( 'min' => 0, 'max' => 160, 'step' => 4 ),
		),

		// ----- Left Card -----
		'tmnhanphat_quote_card_enable'  => array(
			'label'             => __( 'Enable Card (nền + shadow cho Form)', 'tmnhanphat' ),
			'section'           => 'card',
			'type'              => 'checkbox',
			'default'           => $defaults['tmnhanphat_quote_card_enable'],
			'sanitize_callback' => 'tmnhanphat_sanitize_checkbox',
		),
		'tmnhanphat_quote_card_bg'      => array(
			'label'             => __( 'Card Background (Dark Blue)', 'tmnhanphat' ),
			'section'           => 'card',
			'type'              => 'color',
			'default'           => $defaults['tmnhanphat_quote_card_bg'],
			'sanitize_callback' => 'sanitize_hex_color',
		),
		'tmnhanphat_quote_card_radius'  => array(
			'label'             => __( 'Card Radius (px)', 'tmnhanphat' ),
			'section'           => 'card',
			'type'              => 'number',
			'default'           => $defaults['tmnhanphat_quote_card_radius'],
			'sanitize_callback' => 'absint',
			'input_attrs'       => array( 'min' => 0, 'max' => 48, 'step' => 2 ),
		),
		'tmnhanphat_quote_card_shadow'  => array(
			'label'             => __( 'Card Shadow', 'tmnhanphat' ),
			'section'           => 'card',
			'choices'           => tmnhanphat_get_why_choose_shadow_choices(),
			'default'           => $defaults['tmnhanphat_quote_card_shadow'],
			'sanitize_callback' => 'tmnhanphat_sanitize_why_choose_shadow',
		),
		'tmnhanphat_quote_card_padding' => array(
			'label'             => __( 'Card Padding (px)', 'tmnhanphat' ),
			'section'           => 'card',
			'type'              => 'number',
			'default'           => $defaults['tmnhanphat_quote_card_padding'],
			'sanitize_callback' => 'absint',
			'input_attrs'       => array( 'min' => 16, 'max' => 72, 'step' => 2 ),
		),

		// ----- Form Header -----
		'tmnhanphat_quote_header_title'       => array(
			'label'             => __( 'Header Title', 'tmnhanphat' ),
			'section'           => 'header',
			'type'              => 'text',
			'default'           => $defaults['tmnhanphat_quote_header_title'],
			'sanitize_callback' => 'sanitize_text_field',
		),
		'tmnhanphat_quote_header_title_color' => array(
			'label'             => __( 'Header Title Color', 'tmnhanphat' ),
			'section'           => 'header',
			'type'              => 'color',
			'default'           => $defaults['tmnhanphat_quote_header_title_color'],
			'sanitize_callback' => 'sanitize_hex_color',
		),
		'tmnhanphat_quote_underline_color'    => array(
			'label'             => __( 'Underline Color', 'tmnhanphat' ),
			'section'           => 'header',
			'type'              => 'color',
			'default'           => $defaults['tmnhanphat_quote_underline_color'],
			'sanitize_callback' => 'sanitize_hex_color',
		),
		'tmnhanphat_quote_header_size'        => array(
			'label'             => __( 'Header Font Size — Desktop (px)', 'tmnhanphat' ),
			'section'           => 'header',
			'type'              => 'number',
			'default'           => $defaults['tmnhanphat_quote_header_size'],
			'sanitize_callback' => 'absint',
			'input_attrs'       => array( 'min' => 16, 'max' => 40, 'step' => 1 ),
		),
		'tmnhanphat_quote_header_size_tablet' => array(
			'label'             => __( 'Header Font Size — Tablet (px)', 'tmnhanphat' ),
			'section'           => 'header',
			'type'              => 'number',
			'default'           => $defaults['tmnhanphat_quote_header_size_tablet'],
			'sanitize_callback' => 'absint',
			'input_attrs'       => array( 'min' => 15, 'max' => 34, 'step' => 1 ),
		),
		'tmnhanphat_quote_header_size_mobile' => array(
			'label'             => __( 'Header Font Size — Mobile (px)', 'tmnhanphat' ),
			'section'           => 'header',
			'type'              => 'number',
			'default'           => $defaults['tmnhanphat_quote_header_size_mobile'],
			'sanitize_callback' => 'absint',
			'input_attrs'       => array( 'min' => 14, 'max' => 28, 'step' => 1 ),
		),

		// ----- Form -----
		'tmnhanphat_quote_show_name'       => array(
			'label'             => __( 'Show Full Name', 'tmnhanphat' ),
			'section'           => 'form',
			'type'              => 'checkbox',
			'default'           => $defaults['tmnhanphat_quote_show_name'],
			'sanitize_callback' => 'tmnhanphat_sanitize_checkbox',
		),
		'tmnhanphat_quote_show_phone'      => array(
			'label'             => __( 'Show Phone', 'tmnhanphat' ),
			'section'           => 'form',
			'type'              => 'checkbox',
			'default'           => $defaults['tmnhanphat_quote_show_phone'],
			'sanitize_callback' => 'tmnhanphat_sanitize_checkbox',
		),
		'tmnhanphat_quote_show_email'      => array(
			'label'             => __( 'Show Email', 'tmnhanphat' ),
			'section'           => 'form',
			'type'              => 'checkbox',
			'default'           => $defaults['tmnhanphat_quote_show_email'],
			'sanitize_callback' => 'tmnhanphat_sanitize_checkbox',
		),
		'tmnhanphat_quote_show_service'    => array(
			'label'             => __( 'Show Service', 'tmnhanphat' ),
			'section'           => 'form',
			'type'              => 'checkbox',
			'default'           => $defaults['tmnhanphat_quote_show_service'],
			'sanitize_callback' => 'tmnhanphat_sanitize_checkbox',
		),
		'tmnhanphat_quote_show_message'    => array(
			'label'             => __( 'Show Message', 'tmnhanphat' ),
			'section'           => 'form',
			'type'              => 'checkbox',
			'default'           => $defaults['tmnhanphat_quote_show_message'],
			'sanitize_callback' => 'tmnhanphat_sanitize_checkbox',
		),
		'tmnhanphat_quote_required'        => array(
			'label'             => __( 'Required Fields (bắt buộc nhập)', 'tmnhanphat' ),
			'section'           => 'form',
			'type'              => 'checkbox',
			'default'           => $defaults['tmnhanphat_quote_required'],
			'sanitize_callback' => 'tmnhanphat_sanitize_checkbox',
		),
		'tmnhanphat_quote_input_height'    => array(
			'label'             => __( 'Input Height (px)', 'tmnhanphat' ),
			'section'           => 'form',
			'type'              => 'number',
			'default'           => $defaults['tmnhanphat_quote_input_height'],
			'sanitize_callback' => 'absint',
			'input_attrs'       => array( 'min' => 36, 'max' => 72, 'step' => 2 ),
		),
		'tmnhanphat_quote_input_radius'    => array(
			'label'             => __( 'Input Radius (px)', 'tmnhanphat' ),
			'section'           => 'form',
			'type'              => 'number',
			'default'           => $defaults['tmnhanphat_quote_input_radius'],
			'sanitize_callback' => 'absint',
			'input_attrs'       => array( 'min' => 0, 'max' => 30, 'step' => 1 ),
		),
		'tmnhanphat_quote_input_font_size' => array(
			'label'             => __( 'Input Font Size (px)', 'tmnhanphat' ),
			'section'           => 'form',
			'type'              => 'number',
			'default'           => $defaults['tmnhanphat_quote_input_font_size'],
			'sanitize_callback' => 'absint',
			'input_attrs'       => array( 'min' => 12, 'max' => 20, 'step' => 1 ),
		),
		'tmnhanphat_quote_placeholder_color' => array(
			'label'             => __( 'Placeholder Color', 'tmnhanphat' ),
			'section'           => 'form',
			'type'              => 'color',
			'default'           => $defaults['tmnhanphat_quote_placeholder_color'],
			'sanitize_callback' => 'sanitize_hex_color',
		),
		'tmnhanphat_quote_border_color'    => array(
			'label'             => __( 'Border Color', 'tmnhanphat' ),
			'section'           => 'form',
			'type'              => 'color',
			'default'           => $defaults['tmnhanphat_quote_border_color'],
			'sanitize_callback' => 'sanitize_hex_color',
		),
		'tmnhanphat_quote_focus_color'     => array(
			'label'             => __( 'Focus Color', 'tmnhanphat' ),
			'section'           => 'form',
			'type'              => 'color',
			'default'           => $defaults['tmnhanphat_quote_focus_color'],
			'sanitize_callback' => 'sanitize_hex_color',
		),
		'tmnhanphat_quote_input_gap'       => array(
			'label'             => __( 'Gap Between Inputs (px)', 'tmnhanphat' ),
			'section'           => 'form',
			'type'              => 'number',
			'default'           => $defaults['tmnhanphat_quote_input_gap'],
			'sanitize_callback' => 'absint',
			'input_attrs'       => array( 'min' => 4, 'max' => 32, 'step' => 2 ),
		),
		'tmnhanphat_quote_service_options' => array(
			'label'             => __( 'Service Options (mỗi dòng 1 lựa chọn)', 'tmnhanphat' ),
			'section'           => 'form',
			'type'              => 'textarea',
			'default'           => $defaults['tmnhanphat_quote_service_options'],
			'sanitize_callback' => 'sanitize_textarea_field',
		),

		// ----- Button -----
		'tmnhanphat_quote_btn_text'       => array(
			'label'             => __( 'Button Text', 'tmnhanphat' ),
			'section'           => 'button',
			'type'              => 'text',
			'default'           => $defaults['tmnhanphat_quote_btn_text'],
			'sanitize_callback' => 'sanitize_text_field',
		),
		'tmnhanphat_quote_btn_bg'         => array(
			'label'             => __( 'Background', 'tmnhanphat' ),
			'section'           => 'button',
			'type'              => 'color',
			'default'           => $defaults['tmnhanphat_quote_btn_bg'],
			'sanitize_callback' => 'sanitize_hex_color',
		),
		'tmnhanphat_quote_btn_hover_bg'   => array(
			'label'             => __( 'Hover Background', 'tmnhanphat' ),
			'section'           => 'button',
			'type'              => 'color',
			'default'           => $defaults['tmnhanphat_quote_btn_hover_bg'],
			'sanitize_callback' => 'sanitize_hex_color',
		),
		'tmnhanphat_quote_btn_text_color' => array(
			'label'             => __( 'Text Color', 'tmnhanphat' ),
			'section'           => 'button',
			'type'              => 'color',
			'default'           => $defaults['tmnhanphat_quote_btn_text_color'],
			'sanitize_callback' => 'sanitize_hex_color',
		),
		'tmnhanphat_quote_btn_radius'     => array(
			'label'             => __( 'Radius (px)', 'tmnhanphat' ),
			'section'           => 'button',
			'type'              => 'number',
			'default'           => $defaults['tmnhanphat_quote_btn_radius'],
			'sanitize_callback' => 'absint',
			'input_attrs'       => array( 'min' => 0, 'max' => 30, 'step' => 1 ),
		),
		'tmnhanphat_quote_btn_height'     => array(
			'label'             => __( 'Height (px)', 'tmnhanphat' ),
			'section'           => 'button',
			'type'              => 'number',
			'default'           => $defaults['tmnhanphat_quote_btn_height'],
			'sanitize_callback' => 'absint',
			'input_attrs'       => array( 'min' => 40, 'max' => 72, 'step' => 2 ),
		),
		'tmnhanphat_quote_btn_icon'       => array(
			'label'             => __( 'Show Icon', 'tmnhanphat' ),
			'section'           => 'button',
			'type'              => 'checkbox',
			'default'           => $defaults['tmnhanphat_quote_btn_icon'],
			'sanitize_callback' => 'tmnhanphat_sanitize_checkbox',
		),

		// ----- Verify -----
		'tmnhanphat_quote_verify_enable' => array(
			'label'             => __( 'Enable Verify Text', 'tmnhanphat' ),
			'section'           => 'verify',
			'type'              => 'checkbox',
			'default'           => $defaults['tmnhanphat_quote_verify_enable'],
			'sanitize_callback' => 'tmnhanphat_sanitize_checkbox',
		),
		'tmnhanphat_quote_verify_text'   => array(
			'label'             => __( 'Verify Text', 'tmnhanphat' ),
			'section'           => 'verify',
			'type'              => 'text',
			'default'           => $defaults['tmnhanphat_quote_verify_text'],
			'sanitize_callback' => 'sanitize_text_field',
		),
		'tmnhanphat_quote_verify_color'  => array(
			'label'             => __( 'Verify Color', 'tmnhanphat' ),
			'section'           => 'verify',
			'type'              => 'color',
			'default'           => $defaults['tmnhanphat_quote_verify_color'],
			'sanitize_callback' => 'sanitize_hex_color',
		),
		'tmnhanphat_quote_verify_size'   => array(
			'label'             => __( 'Font Size (px)', 'tmnhanphat' ),
			'section'           => 'verify',
			'type'              => 'number',
			'default'           => $defaults['tmnhanphat_quote_verify_size'],
			'sanitize_callback' => 'absint',
			'input_attrs'       => array( 'min' => 10, 'max' => 16, 'step' => 1 ),
		),

		// ----- Right Content -----
		'tmnhanphat_quote_show_logo'          => array(
			'label'             => __( 'Show Logo', 'tmnhanphat' ),
			'section'           => 'right',
			'type'              => 'checkbox',
			'default'           => $defaults['tmnhanphat_quote_show_logo'],
			'sanitize_callback' => 'tmnhanphat_sanitize_checkbox',
		),
		'tmnhanphat_quote_logo'               => array(
			'label'             => __( 'Logo Upload (bỏ trống = dùng Logo Footer)', 'tmnhanphat' ),
			'section'           => 'right',
			'type'              => 'image',
			'default'           => $defaults['tmnhanphat_quote_logo'],
			'sanitize_callback' => 'esc_url_raw',
		),
		'tmnhanphat_quote_logo_width'         => array(
			'label'             => __( 'Logo Width (px)', 'tmnhanphat' ),
			'section'           => 'right',
			'type'              => 'number',
			'default'           => $defaults['tmnhanphat_quote_logo_width'],
			'sanitize_callback' => 'absint',
			'input_attrs'       => array( 'min' => 60, 'max' => 300, 'step' => 5 ),
		),
		'tmnhanphat_quote_heading_blue_text'  => array(
			'label'             => __( 'Blue Title', 'tmnhanphat' ),
			'section'           => 'right',
			'type'              => 'text',
			'default'           => $defaults['tmnhanphat_quote_heading_blue_text'],
			'sanitize_callback' => 'sanitize_text_field',
		),
		'tmnhanphat_quote_heading_red_text'   => array(
			'label'             => __( 'Red Title', 'tmnhanphat' ),
			'section'           => 'right',
			'type'              => 'text',
			'default'           => $defaults['tmnhanphat_quote_heading_red_text'],
			'sanitize_callback' => 'sanitize_text_field',
		),
		'tmnhanphat_quote_heading_blue_color' => array(
			'label'             => __( 'Blue Title Color', 'tmnhanphat' ),
			'section'           => 'right',
			'type'              => 'color',
			'default'           => $defaults['tmnhanphat_quote_heading_blue_color'],
			'sanitize_callback' => 'sanitize_hex_color',
		),
		'tmnhanphat_quote_heading_red_color'  => array(
			'label'             => __( 'Red Title Color', 'tmnhanphat' ),
			'section'           => 'right',
			'type'              => 'color',
			'default'           => $defaults['tmnhanphat_quote_heading_red_color'],
			'sanitize_callback' => 'sanitize_hex_color',
		),
		'tmnhanphat_quote_heading_size'       => array(
			'label'             => __( 'Heading Font Size (px)', 'tmnhanphat' ),
			'section'           => 'right',
			'type'              => 'number',
			'default'           => $defaults['tmnhanphat_quote_heading_size'],
			'sanitize_callback' => 'absint',
			'input_attrs'       => array( 'min' => 20, 'max' => 60, 'step' => 1 ),
		),
		'tmnhanphat_quote_desc_text'          => array(
			'label'             => __( 'Description', 'tmnhanphat' ),
			'section'           => 'right',
			'type'              => 'textarea',
			'default'           => $defaults['tmnhanphat_quote_desc_text'],
			'sanitize_callback' => 'sanitize_textarea_field',
		),
		'tmnhanphat_quote_desc_color'         => array(
			'label'             => __( 'Description Color', 'tmnhanphat' ),
			'section'           => 'right',
			'type'              => 'color',
			'default'           => $defaults['tmnhanphat_quote_desc_color'],
			'sanitize_callback' => 'sanitize_hex_color',
		),
		'tmnhanphat_quote_desc_size'          => array(
			'label'             => __( 'Description Font Size (px)', 'tmnhanphat' ),
			'section'           => 'right',
			'type'              => 'number',
			'default'           => $defaults['tmnhanphat_quote_desc_size'],
			'sanitize_callback' => 'absint',
			'input_attrs'       => array( 'min' => 12, 'max' => 22, 'step' => 1 ),
		),
		'tmnhanphat_quote_right_align'        => array(
			'label'             => __( 'Heading Alignment', 'tmnhanphat' ),
			'section'           => 'right',
			'choices'           => tmnhanphat_get_services_align_choices(),
			'default'           => $defaults['tmnhanphat_quote_right_align'],
			'sanitize_callback' => 'tmnhanphat_sanitize_services_align',
		),
		'tmnhanphat_quote_right_max_width'    => array(
			'label'             => __( 'Max Width (px)', 'tmnhanphat' ),
			'section'           => 'right',
			'type'              => 'number',
			'default'           => $defaults['tmnhanphat_quote_right_max_width'],
			'sanitize_callback' => 'absint',
			'input_attrs'       => array( 'min' => 300, 'max' => 700, 'step' => 10 ),
		),

		// ----- Contact Information (VALUE reuse Footer; đây chỉ hiển thị) -----
		'tmnhanphat_quote_show_contact_email'   => array(
			'label'             => __( 'Show Email (giá trị lấy từ panel Footer)', 'tmnhanphat' ),
			'section'           => 'contact',
			'type'              => 'checkbox',
			'default'           => $defaults['tmnhanphat_quote_show_contact_email'],
			'sanitize_callback' => 'tmnhanphat_sanitize_checkbox',
		),
		'tmnhanphat_quote_show_contact_phone'   => array(
			'label'             => __( 'Show Phone (giá trị lấy từ panel Footer)', 'tmnhanphat' ),
			'section'           => 'contact',
			'type'              => 'checkbox',
			'default'           => $defaults['tmnhanphat_quote_show_contact_phone'],
			'sanitize_callback' => 'tmnhanphat_sanitize_checkbox',
		),
		'tmnhanphat_quote_show_contact_address' => array(
			'label'             => __( 'Show Address (giá trị lấy từ panel Footer)', 'tmnhanphat' ),
			'section'           => 'contact',
			'type'              => 'checkbox',
			'default'           => $defaults['tmnhanphat_quote_show_contact_address'],
			'sanitize_callback' => 'tmnhanphat_sanitize_checkbox',
		),
		'tmnhanphat_quote_icon_bg'              => array(
			'label'             => __( 'Icon Background', 'tmnhanphat' ),
			'section'           => 'contact',
			'type'              => 'color',
			'default'           => $defaults['tmnhanphat_quote_icon_bg'],
			'sanitize_callback' => 'sanitize_hex_color',
		),
		'tmnhanphat_quote_icon_color'           => array(
			'label'             => __( 'Icon Color', 'tmnhanphat' ),
			'section'           => 'contact',
			'type'              => 'color',
			'default'           => $defaults['tmnhanphat_quote_icon_color'],
			'sanitize_callback' => 'sanitize_hex_color',
		),
		'tmnhanphat_quote_icon_shadow'          => array(
			'label'             => __( 'Icon Shadow', 'tmnhanphat' ),
			'section'           => 'contact',
			'choices'           => tmnhanphat_get_why_choose_shadow_choices(),
			'default'           => $defaults['tmnhanphat_quote_icon_shadow'],
			'sanitize_callback' => 'tmnhanphat_sanitize_why_choose_shadow',
		),
		'tmnhanphat_quote_label_size'           => array(
			'label'             => __( 'Label Font Size (px)', 'tmnhanphat' ),
			'section'           => 'contact',
			'type'              => 'number',
			'default'           => $defaults['tmnhanphat_quote_label_size'],
			'sanitize_callback' => 'absint',
			'input_attrs'       => array( 'min' => 11, 'max' => 18, 'step' => 1 ),
		),
		'tmnhanphat_quote_value_size'           => array(
			'label'             => __( 'Value Font Size (px)', 'tmnhanphat' ),
			'section'           => 'contact',
			'type'              => 'number',
			'default'           => $defaults['tmnhanphat_quote_value_size'],
			'sanitize_callback' => 'absint',
			'input_attrs'       => array( 'min' => 12, 'max' => 22, 'step' => 1 ),
		),
		'tmnhanphat_quote_divider_color'        => array(
			'label'             => __( 'Divider Color', 'tmnhanphat' ),
			'section'           => 'contact',
			'type'              => 'color',
			'default'           => $defaults['tmnhanphat_quote_divider_color'],
			'sanitize_callback' => 'sanitize_hex_color',
		),
		'tmnhanphat_quote_contact_gap'          => array(
			'label'             => __( 'Gap Between Items (px)', 'tmnhanphat' ),
			'section'           => 'contact',
			'type'              => 'number',
			'default'           => $defaults['tmnhanphat_quote_contact_gap'],
			'sanitize_callback' => 'absint',
			'input_attrs'       => array( 'min' => 8, 'max' => 48, 'step' => 2 ),
		),
	);
}

/**
 * Đăng ký Panel "Quote Contact Home" + toàn bộ Section/Setting/Control.
 * Priority 41: nối tiếp Featured News Home (40) trong dải Homepage panel.
 *
 * @param WP_Customize_Manager $wp_customize Customizer manager instance.
 */
function tmnhanphat_quote_customizer_register( $wp_customize ) {
	$wp_customize->add_panel( 'tmnhanphat_quote_panel', array(
		'title'    => __( 'Quote Contact Home', 'tmnhanphat' ),
		'priority' => 41,
	) );

	$sections = array(
		'general' => __( 'General', 'tmnhanphat' ),
		'card'    => __( 'Left Card', 'tmnhanphat' ),
		'header'  => __( 'Form Header', 'tmnhanphat' ),
		'form'    => __( 'Form', 'tmnhanphat' ),
		'button'  => __( 'Button', 'tmnhanphat' ),
		'verify'  => __( 'Verify', 'tmnhanphat' ),
		'right'   => __( 'Right Content', 'tmnhanphat' ),
		'contact' => __( 'Contact Information', 'tmnhanphat' ),
	);

	foreach ( $sections as $section_id => $section_label ) {
		$wp_customize->add_section( "tmnhanphat_quote_section_{$section_id}", array(
			'title' => $section_label,
			'panel' => 'tmnhanphat_quote_panel',
		) );
	}

	foreach ( tmnhanphat_get_quote_customizer_fields() as $field_id => $field ) {
		$wp_customize->add_setting( $field_id, array(
			'default'           => $field['default'],
			'sanitize_callback' => $field['sanitize_callback'],
			'transport'         => 'refresh',
		) );

		$control_args = array(
			'label'    => $field['label'],
			'section'  => 'tmnhanphat_quote_section_' . $field['section'],
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
add_action( 'customize_register', 'tmnhanphat_quote_customizer_register' );
