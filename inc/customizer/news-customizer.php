<?php
/**
 * Đăng ký Customizer Panel riêng "Featured News Home" (PROJECT_RULES.md mục 26 —
 * mỗi feature Homepage 1 Panel cấp cao nhất, priority nối tiếp dải 30-59): General,
 * Header, Query, Card, Thumbnail, Meta, Title, Description, Read More, Slider. Cùng
 * quy ước config-driven như các module trước (mục 22).
 *
 * Dữ liệu là POST THƯỜNG theo Category chọn ở đây (WP_Query — KHÔNG CPT/Taxonomy
 * mới, không hardcode ID; Category = 0 nghĩa là mọi bài viết). Header tái sử dụng
 * style review-home__heading (mục 4 spec) — chỉ có toggle "Reuse Heading Style".
 *
 * @package TMNhanPhat
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Khai báo toàn bộ field của News Section dạng config (id => định nghĩa).
 *
 * @return array<string, array>
 */
function tmnhanphat_get_news_customizer_fields() {
	$defaults = tmnhanphat_news_defaults();

	return array(
		// ----- General -----
		'tmnhanphat_news_enable'          => array(
			'label'             => __( 'Hiển thị Featured News Section', 'tmnhanphat' ),
			'section'           => 'general',
			'type'              => 'checkbox',
			'default'           => $defaults['tmnhanphat_news_enable'],
			'sanitize_callback' => 'tmnhanphat_sanitize_checkbox',
		),
		'tmnhanphat_news_section_id'      => array(
			'label'             => __( 'Section ID (anchor)', 'tmnhanphat' ),
			'section'           => 'general',
			'type'              => 'text',
			'default'           => $defaults['tmnhanphat_news_section_id'],
			'sanitize_callback' => 'sanitize_key',
		),
		'tmnhanphat_news_container_width' => array(
			'label'             => __( 'Container Width / Max Width (px, 0 = dùng Global Settings)', 'tmnhanphat' ),
			'section'           => 'general',
			'type'              => 'number',
			'default'           => $defaults['tmnhanphat_news_container_width'],
			'sanitize_callback' => 'absint',
			'input_attrs'       => array( 'min' => 0, 'max' => 1920, 'step' => 10 ),
		),
		'tmnhanphat_news_bg_color'        => array(
			'label'             => __( 'Background Color', 'tmnhanphat' ),
			'section'           => 'general',
			'type'              => 'color',
			'default'           => $defaults['tmnhanphat_news_bg_color'],
			'sanitize_callback' => 'sanitize_hex_color',
		),
		'tmnhanphat_news_bg_image'        => array(
			'label'             => __( 'Background Image (trang trí, dưới toàn bộ nội dung)', 'tmnhanphat' ),
			'section'           => 'general',
			'type'              => 'image',
			'default'           => $defaults['tmnhanphat_news_bg_image'],
			'sanitize_callback' => 'esc_url_raw',
		),
		'tmnhanphat_news_bg_opacity'      => array(
			'label'             => __( 'Background Opacity (%)', 'tmnhanphat' ),
			'section'           => 'general',
			'type'              => 'number',
			'default'           => $defaults['tmnhanphat_news_bg_opacity'],
			'sanitize_callback' => 'absint',
			'input_attrs'       => array( 'min' => 0, 'max' => 100, 'step' => 5 ),
		),
		'tmnhanphat_news_bg_position'     => array(
			'label'             => __( 'Background Position', 'tmnhanphat' ),
			'section'           => 'general',
			'choices'           => tmnhanphat_get_about_bg_position_choices(),
			'default'           => $defaults['tmnhanphat_news_bg_position'],
			'sanitize_callback' => 'tmnhanphat_sanitize_about_bg_position',
		),
		'tmnhanphat_news_bg_size'         => array(
			'label'             => __( 'Background Size', 'tmnhanphat' ),
			'section'           => 'general',
			'choices'           => tmnhanphat_get_about_bg_size_choices(),
			'default'           => $defaults['tmnhanphat_news_bg_size'],
			'sanitize_callback' => 'tmnhanphat_sanitize_about_bg_size',
		),
		'tmnhanphat_news_deco_enable'     => array(
			'label'             => __( 'Hiển thị Decoration (tam giác xanh góc trên phải)', 'tmnhanphat' ),
			'section'           => 'general',
			'type'              => 'checkbox',
			'default'           => $defaults['tmnhanphat_news_deco_enable'],
			'sanitize_callback' => 'tmnhanphat_sanitize_checkbox',
		),
		'tmnhanphat_news_deco_image'      => array(
			'label'             => __( 'Decoration Image (thiết kế 381×464)', 'tmnhanphat' ),
			'section'           => 'general',
			'type'              => 'image',
			'default'           => $defaults['tmnhanphat_news_deco_image'],
			'sanitize_callback' => 'esc_url_raw',
		),
		'tmnhanphat_news_deco_width'      => array(
			'label'             => __( 'Decoration Width (px)', 'tmnhanphat' ),
			'section'           => 'general',
			'type'              => 'number',
			'default'           => $defaults['tmnhanphat_news_deco_width'],
			'sanitize_callback' => 'absint',
			'input_attrs'       => array( 'min' => 100, 'max' => 800, 'step' => 1 ),
		),
		'tmnhanphat_news_deco_height'     => array(
			'label'             => __( 'Decoration Height (px)', 'tmnhanphat' ),
			'section'           => 'general',
			'type'              => 'number',
			'default'           => $defaults['tmnhanphat_news_deco_height'],
			'sanitize_callback' => 'absint',
			'input_attrs'       => array( 'min' => 100, 'max' => 900, 'step' => 1 ),
		),
		'tmnhanphat_news_padding_desktop' => array(
			'label'             => __( 'Section Padding — Desktop (px)', 'tmnhanphat' ),
			'section'           => 'general',
			'type'              => 'number',
			'default'           => $defaults['tmnhanphat_news_padding_desktop'],
			'sanitize_callback' => 'absint',
			'input_attrs'       => array( 'min' => 0, 'max' => 240, 'step' => 4 ),
		),
		'tmnhanphat_news_padding_tablet'  => array(
			'label'             => __( 'Section Padding — Tablet (px)', 'tmnhanphat' ),
			'section'           => 'general',
			'type'              => 'number',
			'default'           => $defaults['tmnhanphat_news_padding_tablet'],
			'sanitize_callback' => 'absint',
			'input_attrs'       => array( 'min' => 0, 'max' => 200, 'step' => 4 ),
		),
		'tmnhanphat_news_padding_mobile'  => array(
			'label'             => __( 'Section Padding — Mobile (px)', 'tmnhanphat' ),
			'section'           => 'general',
			'type'              => 'number',
			'default'           => $defaults['tmnhanphat_news_padding_mobile'],
			'sanitize_callback' => 'absint',
			'input_attrs'       => array( 'min' => 0, 'max' => 160, 'step' => 4 ),
		),
		'tmnhanphat_news_margin_desktop'  => array(
			'label'             => __( 'Section Margin — Desktop (px)', 'tmnhanphat' ),
			'section'           => 'general',
			'type'              => 'number',
			'default'           => $defaults['tmnhanphat_news_margin_desktop'],
			'sanitize_callback' => 'absint',
			'input_attrs'       => array( 'min' => 0, 'max' => 240, 'step' => 4 ),
		),
		'tmnhanphat_news_margin_tablet'   => array(
			'label'             => __( 'Section Margin — Tablet (px)', 'tmnhanphat' ),
			'section'           => 'general',
			'type'              => 'number',
			'default'           => $defaults['tmnhanphat_news_margin_tablet'],
			'sanitize_callback' => 'absint',
			'input_attrs'       => array( 'min' => 0, 'max' => 200, 'step' => 4 ),
		),
		'tmnhanphat_news_margin_mobile'   => array(
			'label'             => __( 'Section Margin — Mobile (px)', 'tmnhanphat' ),
			'section'           => 'general',
			'type'              => 'number',
			'default'           => $defaults['tmnhanphat_news_margin_mobile'],
			'sanitize_callback' => 'absint',
			'input_attrs'       => array( 'min' => 0, 'max' => 160, 'step' => 4 ),
		),

		// ----- Header -----
		'tmnhanphat_news_small_title'          => array(
			'label'             => __( 'Small Title (chữ nhỏ trên cùng)', 'tmnhanphat' ),
			'section'           => 'header',
			'type'              => 'text',
			'default'           => $defaults['tmnhanphat_news_small_title'],
			'sanitize_callback' => 'sanitize_text_field',
		),
		'tmnhanphat_news_small_size'           => array(
			'label'             => __( 'Small Title Font Size (px)', 'tmnhanphat' ),
			'section'           => 'header',
			'type'              => 'number',
			'default'           => $defaults['tmnhanphat_news_small_size'],
			'sanitize_callback' => 'absint',
			'input_attrs'       => array( 'min' => 10, 'max' => 20, 'step' => 1 ),
		),
		'tmnhanphat_news_heading_blue_text'    => array(
			'label'             => __( 'Blue Title (phần chữ màu xanh)', 'tmnhanphat' ),
			'section'           => 'header',
			'type'              => 'text',
			'default'           => $defaults['tmnhanphat_news_heading_blue_text'],
			'sanitize_callback' => 'sanitize_text_field',
		),
		'tmnhanphat_news_heading_red_text'     => array(
			'label'             => __( 'Red Title (phần chữ màu đỏ)', 'tmnhanphat' ),
			'section'           => 'header',
			'type'              => 'text',
			'default'           => $defaults['tmnhanphat_news_heading_red_text'],
			'sanitize_callback' => 'sanitize_text_field',
		),
		'tmnhanphat_news_heading_blue_color'   => array(
			'label'             => __( 'Title Color Blue', 'tmnhanphat' ),
			'section'           => 'header',
			'type'              => 'color',
			'default'           => $defaults['tmnhanphat_news_heading_blue_color'],
			'sanitize_callback' => 'sanitize_hex_color',
		),
		'tmnhanphat_news_heading_red_color'    => array(
			'label'             => __( 'Title Color Red', 'tmnhanphat' ),
			'section'           => 'header',
			'type'              => 'color',
			'default'           => $defaults['tmnhanphat_news_heading_red_color'],
			'sanitize_callback' => 'sanitize_hex_color',
		),
		'tmnhanphat_news_title_size'           => array(
			'label'             => __( 'Title Font Size — Desktop (px)', 'tmnhanphat' ),
			'section'           => 'header',
			'type'              => 'number',
			'default'           => $defaults['tmnhanphat_news_title_size'],
			'sanitize_callback' => 'absint',
			'input_attrs'       => array( 'min' => 20, 'max' => 72, 'step' => 1 ),
		),
		'tmnhanphat_news_title_size_tablet'    => array(
			'label'             => __( 'Title Font Size — Tablet (px)', 'tmnhanphat' ),
			'section'           => 'header',
			'type'              => 'number',
			'default'           => $defaults['tmnhanphat_news_title_size_tablet'],
			'sanitize_callback' => 'absint',
			'input_attrs'       => array( 'min' => 18, 'max' => 60, 'step' => 1 ),
		),
		'tmnhanphat_news_title_size_mobile'    => array(
			'label'             => __( 'Title Font Size — Mobile (px)', 'tmnhanphat' ),
			'section'           => 'header',
			'type'              => 'number',
			'default'           => $defaults['tmnhanphat_news_title_size_mobile'],
			'sanitize_callback' => 'absint',
			'input_attrs'       => array( 'min' => 16, 'max' => 48, 'step' => 1 ),
		),
		'tmnhanphat_news_desc_text'            => array(
			'label'             => __( 'Description', 'tmnhanphat' ),
			'section'           => 'header',
			'type'              => 'textarea',
			'default'           => $defaults['tmnhanphat_news_desc_text'],
			'sanitize_callback' => 'sanitize_textarea_field',
		),
		'tmnhanphat_news_desc_color'           => array(
			'label'             => __( 'Description Color', 'tmnhanphat' ),
			'section'           => 'header',
			'type'              => 'color',
			'default'           => $defaults['tmnhanphat_news_desc_color'],
			'sanitize_callback' => 'sanitize_hex_color',
		),
		'tmnhanphat_news_desc_size'            => array(
			'label'             => __( 'Description Font Size (px)', 'tmnhanphat' ),
			'section'           => 'header',
			'type'              => 'number',
			'default'           => $defaults['tmnhanphat_news_desc_size'],
			'sanitize_callback' => 'absint',
			'input_attrs'       => array( 'min' => 12, 'max' => 24, 'step' => 1 ),
		),
		'tmnhanphat_news_desc_max_width'       => array(
			'label'             => __( 'Description Max Width (px)', 'tmnhanphat' ),
			'section'           => 'header',
			'type'              => 'number',
			'default'           => $defaults['tmnhanphat_news_desc_max_width'],
			'sanitize_callback' => 'absint',
			'input_attrs'       => array( 'min' => 300, 'max' => 1000, 'step' => 10 ),
		),
		'tmnhanphat_news_header_align'         => array(
			'label'             => __( 'Header Alignment', 'tmnhanphat' ),
			'section'           => 'header',
			'choices'           => tmnhanphat_get_services_align_choices(),
			'default'           => $defaults['tmnhanphat_news_header_align'],
			'sanitize_callback' => 'tmnhanphat_sanitize_services_align',
		),
		'tmnhanphat_news_header_margin_bottom' => array(
			'label'             => __( 'Header Bottom Spacing (px)', 'tmnhanphat' ),
			'section'           => 'header',
			'type'              => 'number',
			'default'           => $defaults['tmnhanphat_news_header_margin_bottom'],
			'sanitize_callback' => 'absint',
			'input_attrs'       => array( 'min' => 0, 'max' => 120, 'step' => 2 ),
		),
		'tmnhanphat_news_reuse_heading'        => array(
			'label'             => __( 'Reuse Heading Style (dùng gạch đôi đỏ/xanh của Review Section)', 'tmnhanphat' ),
			'section'           => 'header',
			'type'              => 'checkbox',
			'default'           => $defaults['tmnhanphat_news_reuse_heading'],
			'sanitize_callback' => 'tmnhanphat_sanitize_checkbox',
		),

		// ----- Query -----
		'tmnhanphat_news_category'     => array(
			'label'             => __( 'Category (— Chọn — = mọi bài viết mới nhất)', 'tmnhanphat' ),
			'section'           => 'query',
			'choices'           => tmnhanphat_get_products_category_choices(),
			'default'           => $defaults['tmnhanphat_news_category'],
			'sanitize_callback' => 'absint',
		),
		'tmnhanphat_news_count'        => array(
			'label'             => __( 'Posts Per Page (tổng số bài trong slider)', 'tmnhanphat' ),
			'section'           => 'query',
			'type'              => 'number',
			'default'           => $defaults['tmnhanphat_news_count'],
			'sanitize_callback' => 'absint',
			'input_attrs'       => array( 'min' => 1, 'max' => 30, 'step' => 1 ),
		),
		'tmnhanphat_news_orderby'      => array(
			'label'             => __( 'Order By', 'tmnhanphat' ),
			'section'           => 'query',
			'choices'           => tmnhanphat_get_products_orderby_choices(),
			'default'           => $defaults['tmnhanphat_news_orderby'],
			'sanitize_callback' => 'tmnhanphat_sanitize_projects_orderby',
		),
		'tmnhanphat_news_order'        => array(
			'label'             => __( 'Order', 'tmnhanphat' ),
			'section'           => 'query',
			'choices'           => array(
				'DESC' => __( 'Giảm dần (mới nhất trước)', 'tmnhanphat' ),
				'ASC'  => __( 'Tăng dần (cũ nhất trước)', 'tmnhanphat' ),
			),
			'default'           => $defaults['tmnhanphat_news_order'],
			'sanitize_callback' => 'tmnhanphat_sanitize_projects_order',
		),
		'tmnhanphat_news_exclude_cats' => array(
			'label'             => __( 'Exclude Category IDs (CSV, ví dụ: 5,9)', 'tmnhanphat' ),
			'section'           => 'query',
			'type'              => 'text',
			'default'           => $defaults['tmnhanphat_news_exclude_cats'],
			'sanitize_callback' => 'tmnhanphat_sanitize_id_list',
		),
		'tmnhanphat_news_hide_sticky'  => array(
			'label'             => __( 'Hide Sticky (bỏ ưu tiên bài ghim)', 'tmnhanphat' ),
			'section'           => 'query',
			'type'              => 'checkbox',
			'default'           => $defaults['tmnhanphat_news_hide_sticky'],
			'sanitize_callback' => 'tmnhanphat_sanitize_checkbox',
		),

		// ----- Card -----
		'tmnhanphat_news_card_width'        => array(
			'label'             => __( 'Card Width (px — trần, Desktop)', 'tmnhanphat' ),
			'section'           => 'card',
			'type'              => 'number',
			'default'           => $defaults['tmnhanphat_news_card_width'],
			'sanitize_callback' => 'absint',
			'input_attrs'       => array( 'min' => 260, 'max' => 700, 'step' => 1 ),
		),
		'tmnhanphat_news_card_height'       => array(
			'label'             => __( 'Card Min Height (px, 0 = auto)', 'tmnhanphat' ),
			'section'           => 'card',
			'type'              => 'number',
			'default'           => $defaults['tmnhanphat_news_card_height'],
			'sanitize_callback' => 'absint',
			'input_attrs'       => array( 'min' => 0, 'max' => 800, 'step' => 1 ),
		),
		'tmnhanphat_news_card_radius'       => array(
			'label'             => __( 'Card Border Radius (px)', 'tmnhanphat' ),
			'section'           => 'card',
			'type'              => 'number',
			'default'           => $defaults['tmnhanphat_news_card_radius'],
			'sanitize_callback' => 'absint',
			'input_attrs'       => array( 'min' => 0, 'max' => 40, 'step' => 1 ),
		),
		'tmnhanphat_news_card_shadow'       => array(
			'label'             => __( 'Card Shadow', 'tmnhanphat' ),
			'section'           => 'card',
			'choices'           => tmnhanphat_get_why_choose_shadow_choices(),
			'default'           => $defaults['tmnhanphat_news_card_shadow'],
			'sanitize_callback' => 'tmnhanphat_sanitize_why_choose_shadow',
		),
		'tmnhanphat_news_card_hover_shadow' => array(
			'label'             => __( 'Hover Shadow', 'tmnhanphat' ),
			'section'           => 'card',
			'choices'           => tmnhanphat_get_why_choose_shadow_choices(),
			'default'           => $defaults['tmnhanphat_news_card_hover_shadow'],
			'sanitize_callback' => 'tmnhanphat_sanitize_why_choose_shadow',
		),
		'tmnhanphat_news_card_padding'      => array(
			'label'             => __( 'Card Padding (px)', 'tmnhanphat' ),
			'section'           => 'card',
			'type'              => 'number',
			'default'           => $defaults['tmnhanphat_news_card_padding'],
			'sanitize_callback' => 'absint',
			'input_attrs'       => array( 'min' => 0, 'max' => 48, 'step' => 2 ),
		),
		'tmnhanphat_news_gap'               => array(
			'label'             => __( 'Gap giữa các Card (px)', 'tmnhanphat' ),
			'section'           => 'card',
			'type'              => 'number',
			'default'           => $defaults['tmnhanphat_news_gap'],
			'sanitize_callback' => 'absint',
			'input_attrs'       => array( 'min' => 0, 'max' => 60, 'step' => 2 ),
		),

		// ----- Thumbnail -----
		'tmnhanphat_news_thumb_height' => array(
			'label'             => __( 'Image Height (px)', 'tmnhanphat' ),
			'section'           => 'thumbnail',
			'type'              => 'number',
			'default'           => $defaults['tmnhanphat_news_thumb_height'],
			'sanitize_callback' => 'absint',
			'input_attrs'       => array( 'min' => 150, 'max' => 500, 'step' => 10 ),
		),
		'tmnhanphat_news_thumb_radius' => array(
			'label'             => __( 'Border Radius (px)', 'tmnhanphat' ),
			'section'           => 'thumbnail',
			'type'              => 'number',
			'default'           => $defaults['tmnhanphat_news_thumb_radius'],
			'sanitize_callback' => 'absint',
			'input_attrs'       => array( 'min' => 0, 'max' => 30, 'step' => 1 ),
		),
		'tmnhanphat_news_thumb_fit'    => array(
			'label'             => __( 'Object Fit', 'tmnhanphat' ),
			'section'           => 'thumbnail',
			'choices'           => tmnhanphat_get_why_choose_image_fit_choices(),
			'default'           => $defaults['tmnhanphat_news_thumb_fit'],
			'sanitize_callback' => 'tmnhanphat_sanitize_why_choose_image_fit',
		),
		'tmnhanphat_news_thumb_zoom'   => array(
			'label'             => __( 'Hover Zoom', 'tmnhanphat' ),
			'section'           => 'thumbnail',
			'type'              => 'checkbox',
			'default'           => $defaults['tmnhanphat_news_thumb_zoom'],
			'sanitize_callback' => 'tmnhanphat_sanitize_checkbox',
		),

		// ----- Meta -----
		'tmnhanphat_news_show_date'   => array(
			'label'             => __( 'Show Date', 'tmnhanphat' ),
			'section'           => 'meta',
			'type'              => 'checkbox',
			'default'           => $defaults['tmnhanphat_news_show_date'],
			'sanitize_callback' => 'tmnhanphat_sanitize_checkbox',
		),
		'tmnhanphat_news_show_author' => array(
			'label'             => __( 'Show Author', 'tmnhanphat' ),
			'section'           => 'meta',
			'type'              => 'checkbox',
			'default'           => $defaults['tmnhanphat_news_show_author'],
			'sanitize_callback' => 'tmnhanphat_sanitize_checkbox',
		),
		'tmnhanphat_news_date_format' => array(
			'label'             => __( 'Date Format (PHP, ví dụ d/m/Y)', 'tmnhanphat' ),
			'section'           => 'meta',
			'type'              => 'text',
			'default'           => $defaults['tmnhanphat_news_date_format'],
			'sanitize_callback' => 'sanitize_text_field',
		),
		'tmnhanphat_news_meta_size'   => array(
			'label'             => __( 'Meta Font Size (px)', 'tmnhanphat' ),
			'section'           => 'meta',
			'type'              => 'number',
			'default'           => $defaults['tmnhanphat_news_meta_size'],
			'sanitize_callback' => 'absint',
			'input_attrs'       => array( 'min' => 10, 'max' => 18, 'step' => 1 ),
		),
		'tmnhanphat_news_meta_color'  => array(
			'label'             => __( 'Meta Color', 'tmnhanphat' ),
			'section'           => 'meta',
			'type'              => 'color',
			'default'           => $defaults['tmnhanphat_news_meta_color'],
			'sanitize_callback' => 'sanitize_hex_color',
		),

		// ----- Title -----
		'tmnhanphat_news_ptitle_size'        => array(
			'label'             => __( 'Font Size — Desktop (px)', 'tmnhanphat' ),
			'section'           => 'title',
			'type'              => 'number',
			'default'           => $defaults['tmnhanphat_news_ptitle_size'],
			'sanitize_callback' => 'absint',
			'input_attrs'       => array( 'min' => 14, 'max' => 32, 'step' => 1 ),
		),
		'tmnhanphat_news_ptitle_size_tablet' => array(
			'label'             => __( 'Font Size — Tablet (px)', 'tmnhanphat' ),
			'section'           => 'title',
			'type'              => 'number',
			'default'           => $defaults['tmnhanphat_news_ptitle_size_tablet'],
			'sanitize_callback' => 'absint',
			'input_attrs'       => array( 'min' => 13, 'max' => 28, 'step' => 1 ),
		),
		'tmnhanphat_news_ptitle_size_mobile' => array(
			'label'             => __( 'Font Size — Mobile (px)', 'tmnhanphat' ),
			'section'           => 'title',
			'type'              => 'number',
			'default'           => $defaults['tmnhanphat_news_ptitle_size_mobile'],
			'sanitize_callback' => 'absint',
			'input_attrs'       => array( 'min' => 12, 'max' => 24, 'step' => 1 ),
		),
		'tmnhanphat_news_ptitle_weight'      => array(
			'label'             => __( 'Weight', 'tmnhanphat' ),
			'section'           => 'title',
			'choices'           => tmnhanphat_get_font_weight_choices(),
			'default'           => $defaults['tmnhanphat_news_ptitle_weight'],
			'sanitize_callback' => 'tmnhanphat_sanitize_font_weight',
		),
		'tmnhanphat_news_ptitle_clamp'       => array(
			'label'             => __( 'Line Clamp (0 = không giới hạn)', 'tmnhanphat' ),
			'section'           => 'title',
			'type'              => 'number',
			'default'           => $defaults['tmnhanphat_news_ptitle_clamp'],
			'sanitize_callback' => 'absint',
			'input_attrs'       => array( 'min' => 0, 'max' => 4, 'step' => 1 ),
		),
		'tmnhanphat_news_ptitle_color'       => array(
			'label'             => __( 'Color', 'tmnhanphat' ),
			'section'           => 'title',
			'type'              => 'color',
			'default'           => $defaults['tmnhanphat_news_ptitle_color'],
			'sanitize_callback' => 'sanitize_hex_color',
		),

		// ----- Description -----
		'tmnhanphat_news_pdesc_size'         => array(
			'label'             => __( 'Font Size (px)', 'tmnhanphat' ),
			'section'           => 'description',
			'type'              => 'number',
			'default'           => $defaults['tmnhanphat_news_pdesc_size'],
			'sanitize_callback' => 'absint',
			'input_attrs'       => array( 'min' => 11, 'max' => 18, 'step' => 1 ),
		),
		'tmnhanphat_news_pdesc_clamp'        => array(
			'label'             => __( 'Line Clamp — Desktop (0 = không giới hạn)', 'tmnhanphat' ),
			'section'           => 'description',
			'type'              => 'number',
			'default'           => $defaults['tmnhanphat_news_pdesc_clamp'],
			'sanitize_callback' => 'absint',
			'input_attrs'       => array( 'min' => 0, 'max' => 6, 'step' => 1 ),
		),
		'tmnhanphat_news_pdesc_clamp_mobile' => array(
			'label'             => __( 'Line Clamp — Mobile', 'tmnhanphat' ),
			'section'           => 'description',
			'type'              => 'number',
			'default'           => $defaults['tmnhanphat_news_pdesc_clamp_mobile'],
			'sanitize_callback' => 'absint',
			'input_attrs'       => array( 'min' => 0, 'max' => 5, 'step' => 1 ),
		),
		'tmnhanphat_news_pdesc_color'        => array(
			'label'             => __( 'Color', 'tmnhanphat' ),
			'section'           => 'description',
			'type'              => 'color',
			'default'           => $defaults['tmnhanphat_news_pdesc_color'],
			'sanitize_callback' => 'sanitize_hex_color',
		),

		// ----- Read More -----
		'tmnhanphat_news_readmore_text'        => array(
			'label'             => __( 'Text', 'tmnhanphat' ),
			'section'           => 'readmore',
			'type'              => 'text',
			'default'           => $defaults['tmnhanphat_news_readmore_text'],
			'sanitize_callback' => 'sanitize_text_field',
		),
		'tmnhanphat_news_readmore_color'       => array(
			'label'             => __( 'Color', 'tmnhanphat' ),
			'section'           => 'readmore',
			'type'              => 'color',
			'default'           => $defaults['tmnhanphat_news_readmore_color'],
			'sanitize_callback' => 'sanitize_hex_color',
		),
		'tmnhanphat_news_readmore_hover_color' => array(
			'label'             => __( 'Hover Color', 'tmnhanphat' ),
			'section'           => 'readmore',
			'type'              => 'color',
			'default'           => $defaults['tmnhanphat_news_readmore_hover_color'],
			'sanitize_callback' => 'sanitize_hex_color',
		),

		// ----- Slider -----
		'tmnhanphat_news_autoplay_enable'  => array(
			'label'             => __( 'Enable Autoplay', 'tmnhanphat' ),
			'section'           => 'slider',
			'type'              => 'checkbox',
			'default'           => $defaults['tmnhanphat_news_autoplay_enable'],
			'sanitize_callback' => 'tmnhanphat_sanitize_checkbox',
		),
		'tmnhanphat_news_autoplay_speed'   => array(
			'label'             => __( 'Delay (ms)', 'tmnhanphat' ),
			'section'           => 'slider',
			'type'              => 'number',
			'default'           => $defaults['tmnhanphat_news_autoplay_speed'],
			'sanitize_callback' => 'absint',
			'input_attrs'       => array( 'min' => 1000, 'max' => 15000, 'step' => 500 ),
		),
		'tmnhanphat_news_transition_speed' => array(
			'label'             => __( 'Speed (ms)', 'tmnhanphat' ),
			'section'           => 'slider',
			'type'              => 'number',
			'default'           => $defaults['tmnhanphat_news_transition_speed'],
			'sanitize_callback' => 'absint',
			'input_attrs'       => array( 'min' => 150, 'max' => 2000, 'step' => 50 ),
		),
		'tmnhanphat_news_infinite'         => array(
			'label'             => __( 'Loop', 'tmnhanphat' ),
			'section'           => 'slider',
			'type'              => 'checkbox',
			'default'           => $defaults['tmnhanphat_news_infinite'],
			'sanitize_callback' => 'tmnhanphat_sanitize_checkbox',
		),
		'tmnhanphat_news_pause_hover'      => array(
			'label'             => __( 'Pause On Hover', 'tmnhanphat' ),
			'section'           => 'slider',
			'type'              => 'checkbox',
			'default'           => $defaults['tmnhanphat_news_pause_hover'],
			'sanitize_callback' => 'tmnhanphat_sanitize_checkbox',
		),
		'tmnhanphat_news_drag_enable'      => array(
			'label'             => __( 'Enable Swipe / Mouse Drag', 'tmnhanphat' ),
			'section'           => 'slider',
			'type'              => 'checkbox',
			'default'           => $defaults['tmnhanphat_news_drag_enable'],
			'sanitize_callback' => 'tmnhanphat_sanitize_checkbox',
		),
		'tmnhanphat_news_show_arrows'      => array(
			'label'             => __( 'Show Arrow (tự ẩn trên Mobile)', 'tmnhanphat' ),
			'section'           => 'slider',
			'type'              => 'checkbox',
			'default'           => $defaults['tmnhanphat_news_show_arrows'],
			'sanitize_callback' => 'tmnhanphat_sanitize_checkbox',
		),
		'tmnhanphat_news_show_dots'        => array(
			'label'             => __( 'Show Dot', 'tmnhanphat' ),
			'section'           => 'slider',
			'type'              => 'checkbox',
			'default'           => $defaults['tmnhanphat_news_show_dots'],
			'sanitize_callback' => 'tmnhanphat_sanitize_checkbox',
		),
		'tmnhanphat_news_dot_size'         => array(
			'label'             => __( 'Dot Size (px)', 'tmnhanphat' ),
			'section'           => 'slider',
			'type'              => 'number',
			'default'           => $defaults['tmnhanphat_news_dot_size'],
			'sanitize_callback' => 'absint',
			'input_attrs'       => array( 'min' => 6, 'max' => 20, 'step' => 1 ),
		),
		'tmnhanphat_news_dot_gap'          => array(
			'label'             => __( 'Dot Gap (px)', 'tmnhanphat' ),
			'section'           => 'slider',
			'type'              => 'number',
			'default'           => $defaults['tmnhanphat_news_dot_gap'],
			'sanitize_callback' => 'absint',
			'input_attrs'       => array( 'min' => 4, 'max' => 30, 'step' => 1 ),
		),
		'tmnhanphat_news_dot_active'       => array(
			'label'             => __( 'Dot Active Color', 'tmnhanphat' ),
			'section'           => 'slider',
			'type'              => 'color',
			'default'           => $defaults['tmnhanphat_news_dot_active'],
			'sanitize_callback' => 'sanitize_hex_color',
		),
		'tmnhanphat_news_dot_inactive'     => array(
			'label'             => __( 'Dot Inactive Color', 'tmnhanphat' ),
			'section'           => 'slider',
			'type'              => 'color',
			'default'           => $defaults['tmnhanphat_news_dot_inactive'],
			'sanitize_callback' => 'sanitize_hex_color',
		),
		'tmnhanphat_news_arrow_size'       => array(
			'label'             => __( 'Arrow Size (px)', 'tmnhanphat' ),
			'section'           => 'slider',
			'type'              => 'number',
			'default'           => $defaults['tmnhanphat_news_arrow_size'],
			'sanitize_callback' => 'absint',
			'input_attrs'       => array( 'min' => 28, 'max' => 64, 'step' => 2 ),
		),
		'tmnhanphat_news_arrow_bg'         => array(
			'label'             => __( 'Arrow Background', 'tmnhanphat' ),
			'section'           => 'slider',
			'type'              => 'color',
			'default'           => $defaults['tmnhanphat_news_arrow_bg'],
			'sanitize_callback' => 'sanitize_hex_color',
		),
		'tmnhanphat_news_arrow_color'      => array(
			'label'             => __( 'Arrow Icon Color', 'tmnhanphat' ),
			'section'           => 'slider',
			'type'              => 'color',
			'default'           => $defaults['tmnhanphat_news_arrow_color'],
			'sanitize_callback' => 'sanitize_hex_color',
		),
		'tmnhanphat_news_arrow_radius'     => array(
			'label'             => __( 'Arrow Radius (%)', 'tmnhanphat' ),
			'section'           => 'slider',
			'type'              => 'number',
			'default'           => $defaults['tmnhanphat_news_arrow_radius'],
			'sanitize_callback' => 'absint',
			'input_attrs'       => array( 'min' => 0, 'max' => 100, 'step' => 5 ),
		),
	);
}

/**
 * Đăng ký Panel "Featured News Home" + toàn bộ Section/Setting/Control.
 * Priority 40: nối tiếp Customer Review Home (39) trong dải Homepage panel.
 *
 * @param WP_Customize_Manager $wp_customize Customizer manager instance.
 */
function tmnhanphat_news_customizer_register( $wp_customize ) {
	$wp_customize->add_panel( 'tmnhanphat_news_panel', array(
		'title'    => __( 'Featured News Home', 'tmnhanphat' ),
		'priority' => 40,
	) );

	$sections = array(
		'general'     => __( 'General', 'tmnhanphat' ),
		'header'      => __( 'Header', 'tmnhanphat' ),
		'query'       => __( 'Query', 'tmnhanphat' ),
		'card'        => __( 'Card', 'tmnhanphat' ),
		'thumbnail'   => __( 'Thumbnail', 'tmnhanphat' ),
		'meta'        => __( 'Meta', 'tmnhanphat' ),
		'title'       => __( 'Title', 'tmnhanphat' ),
		'description' => __( 'Description', 'tmnhanphat' ),
		'readmore'    => __( 'Read More', 'tmnhanphat' ),
		'slider'      => __( 'Slider', 'tmnhanphat' ),
	);

	foreach ( $sections as $section_id => $section_label ) {
		$wp_customize->add_section( "tmnhanphat_news_section_{$section_id}", array(
			'title' => $section_label,
			'panel' => 'tmnhanphat_news_panel',
		) );
	}

	foreach ( tmnhanphat_get_news_customizer_fields() as $field_id => $field ) {
		$wp_customize->add_setting( $field_id, array(
			'default'           => $field['default'],
			'sanitize_callback' => $field['sanitize_callback'],
			'transport'         => 'refresh',
		) );

		$control_args = array(
			'label'    => $field['label'],
			'section'  => 'tmnhanphat_news_section_' . $field['section'],
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
add_action( 'customize_register', 'tmnhanphat_news_customizer_register' );
