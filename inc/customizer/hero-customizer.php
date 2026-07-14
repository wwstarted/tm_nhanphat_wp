<?php
/**
 * Đăng ký Customizer Panel riêng "Hero Home" (không lồng trong panel "Homepage" nào —
 * WordPress Customizer không hỗ trợ panel-trong-panel, xem PROJECT_RULES.md mục 26):
 * General, Content, Buttons, Background, Overlay, Statistics 1-4, Statistics Style,
 * Responsive, Animation. Cùng quy ước config-driven như header-customizer.php/
 * footer-customizer.php (PROJECT_RULES.md mục 22) — không đụng tới bất kỳ setting/hàm
 * nào của Header/Footer/Partner. Đây CHỈ là tổ chức UI — không đổi key setting, sanitize
 * callback hay default value nào so với trước khi tách panel.
 *
 * @package TMNhanPhat
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Khai báo field cho 1 Company Stat (dùng lại 4 lần cho Stat 1..4 — DRY).
 *
 * @param int   $index    Số thứ tự stat (1-4).
 * @param array $defaults Mảng defaults từ tmnhanphat_hero_defaults().
 * @return array<string, array>
 */
function tmnhanphat_get_hero_stat_fields( $index, $defaults ) {
	$section = "statistics_{$index}";

	return array(
		"tmnhanphat_hero_stat{$index}_enable"      => array(
			/* translators: %d: số thứ tự stat (1-4) */
			'label'             => sprintf( __( 'Hiển thị Stat %d', 'tmnhanphat' ), $index ),
			'section'           => $section,
			'type'              => 'checkbox',
			'default'           => $defaults[ "tmnhanphat_hero_stat{$index}_enable" ],
			'sanitize_callback' => 'tmnhanphat_sanitize_checkbox',
		),
		"tmnhanphat_hero_stat{$index}_number"      => array(
			'label'             => __( 'Number', 'tmnhanphat' ),
			'section'           => $section,
			'type'              => 'text',
			'default'           => $defaults[ "tmnhanphat_hero_stat{$index}_number" ],
			'sanitize_callback' => 'sanitize_text_field',
		),
		"tmnhanphat_hero_stat{$index}_badge"       => array(
			'label'             => __( 'Badge', 'tmnhanphat' ),
			'section'           => $section,
			'type'              => 'text',
			'default'           => $defaults[ "tmnhanphat_hero_stat{$index}_badge" ],
			'sanitize_callback' => 'sanitize_text_field',
		),
		"tmnhanphat_hero_stat{$index}_description" => array(
			'label'             => __( 'Description', 'tmnhanphat' ),
			'section'           => $section,
			'type'              => 'text',
			'default'           => $defaults[ "tmnhanphat_hero_stat{$index}_description" ],
			'sanitize_callback' => 'sanitize_text_field',
		),
		"tmnhanphat_hero_stat{$index}_icon"        => array(
			'label'             => __( 'Icon (tuỳ chọn)', 'tmnhanphat' ),
			'section'           => $section,
			'type'              => 'image',
			'default'           => $defaults[ "tmnhanphat_hero_stat{$index}_icon" ],
			'sanitize_callback' => 'esc_url_raw',
		),
	);
}

/**
 * Khai báo toàn bộ field của Hero Banner dạng config (id => định nghĩa).
 *
 * @return array<string, array>
 */
function tmnhanphat_get_hero_customizer_fields() {
	$defaults = tmnhanphat_hero_defaults();

	$fields = array(
		// ----- Background -----
		'tmnhanphat_hero_bg_image'          => array(
			'label'             => __( 'Background Image', 'tmnhanphat' ),
			'section'           => 'background',
			'type'              => 'image',
			'default'           => $defaults['tmnhanphat_hero_bg_image'],
			'sanitize_callback' => 'esc_url_raw',
		),
		'tmnhanphat_hero_fallback_bg_color' => array(
			'label'             => __( 'Màu nền dự phòng (khi chưa chọn ảnh)', 'tmnhanphat' ),
			'section'           => 'background',
			'type'              => 'color',
			'default'           => $defaults['tmnhanphat_hero_fallback_bg_color'],
			'sanitize_callback' => 'sanitize_hex_color',
		),

		// ----- Overlay -----
		'tmnhanphat_hero_overlay_enable'  => array(
			'label'             => __( 'Enable Overlay', 'tmnhanphat' ),
			'section'           => 'overlay',
			'type'              => 'checkbox',
			'default'           => $defaults['tmnhanphat_hero_overlay_enable'],
			'sanitize_callback' => 'tmnhanphat_sanitize_checkbox',
		),
		'tmnhanphat_hero_overlay_color'   => array(
			'label'             => __( 'Overlay Color', 'tmnhanphat' ),
			'section'           => 'overlay',
			'type'              => 'color',
			'default'           => $defaults['tmnhanphat_hero_overlay_color'],
			'sanitize_callback' => 'sanitize_hex_color',
		),
		'tmnhanphat_hero_overlay_opacity' => array(
			'label'             => __( 'Overlay Opacity (%)', 'tmnhanphat' ),
			'section'           => 'overlay',
			'type'              => 'number',
			'default'           => $defaults['tmnhanphat_hero_overlay_opacity'],
			'sanitize_callback' => 'absint',
			'input_attrs'       => array( 'min' => 0, 'max' => 100, 'step' => 1 ),
		),
		'tmnhanphat_hero_overlay_blend'   => array(
			'label'             => __( 'Overlay Blend Mode', 'tmnhanphat' ),
			'section'           => 'overlay',
			'choices'           => tmnhanphat_get_hero_overlay_blend_choices(),
			'default'           => $defaults['tmnhanphat_hero_overlay_blend'],
			'sanitize_callback' => 'tmnhanphat_sanitize_hero_overlay_blend',
		),

		// ----- Subtitle -----
		'tmnhanphat_hero_subtitle_enable'      => array(
			'label'             => __( 'Enable Subtitle', 'tmnhanphat' ),
			'section'           => 'content',
			'type'              => 'checkbox',
			'default'           => $defaults['tmnhanphat_hero_subtitle_enable'],
			'sanitize_callback' => 'tmnhanphat_sanitize_checkbox',
		),
		'tmnhanphat_hero_subtitle_text'        => array(
			'label'             => __( 'Subtitle Text', 'tmnhanphat' ),
			'section'           => 'content',
			'type'              => 'text',
			'default'           => $defaults['tmnhanphat_hero_subtitle_text'],
			'sanitize_callback' => 'sanitize_text_field',
		),
		'tmnhanphat_hero_subtitle_color'       => array(
			'label'             => __( 'Subtitle Color', 'tmnhanphat' ),
			'section'           => 'content',
			'type'              => 'color',
			'default'           => $defaults['tmnhanphat_hero_subtitle_color'],
			'sanitize_callback' => 'sanitize_hex_color',
		),
		'tmnhanphat_hero_subtitle_font_size'   => array(
			'label'             => __( 'Subtitle Font Size (px)', 'tmnhanphat' ),
			'section'           => 'content',
			'type'              => 'number',
			'default'           => $defaults['tmnhanphat_hero_subtitle_font_size'],
			'sanitize_callback' => 'absint',
		),
		'tmnhanphat_hero_subtitle_font_weight' => array(
			'label'             => __( 'Subtitle Font Weight', 'tmnhanphat' ),
			'section'           => 'content',
			'choices'           => tmnhanphat_get_font_weight_choices(),
			'default'           => $defaults['tmnhanphat_hero_subtitle_font_weight'],
			'sanitize_callback' => 'tmnhanphat_sanitize_font_weight',
		),

		// ----- Heading -----
		'tmnhanphat_hero_heading_text'        => array(
			'label'             => __( 'Heading Text', 'tmnhanphat' ),
			'section'           => 'content',
			'type'              => 'text',
			'default'           => $defaults['tmnhanphat_hero_heading_text'],
			'sanitize_callback' => 'sanitize_text_field',
		),
		'tmnhanphat_hero_heading_color'       => array(
			'label'             => __( 'Heading Color', 'tmnhanphat' ),
			'section'           => 'content',
			'type'              => 'color',
			'default'           => $defaults['tmnhanphat_hero_heading_color'],
			'sanitize_callback' => 'sanitize_hex_color',
		),
		'tmnhanphat_hero_heading_font_size'   => array(
			'label'             => __( 'Heading Font Size (px, Desktop)', 'tmnhanphat' ),
			'section'           => 'content',
			'type'              => 'number',
			'default'           => $defaults['tmnhanphat_hero_heading_font_size'],
			'sanitize_callback' => 'absint',
		),
		'tmnhanphat_hero_heading_font_weight' => array(
			'label'             => __( 'Heading Font Weight', 'tmnhanphat' ),
			'section'           => 'content',
			'choices'           => tmnhanphat_get_font_weight_choices(),
			'default'           => $defaults['tmnhanphat_hero_heading_font_weight'],
			'sanitize_callback' => 'tmnhanphat_sanitize_font_weight',
		),

		// ----- Description -----
		'tmnhanphat_hero_description_text'      => array(
			'label'             => __( 'Description Text', 'tmnhanphat' ),
			'section'           => 'content',
			'type'              => 'text',
			'default'           => $defaults['tmnhanphat_hero_description_text'],
			'sanitize_callback' => 'sanitize_textarea_field',
		),
		'tmnhanphat_hero_description_color'     => array(
			'label'             => __( 'Description Color', 'tmnhanphat' ),
			'section'           => 'content',
			'type'              => 'color',
			'default'           => $defaults['tmnhanphat_hero_description_color'],
			'sanitize_callback' => 'sanitize_hex_color',
		),
		'tmnhanphat_hero_description_font_size' => array(
			'label'             => __( 'Description Font Size (px)', 'tmnhanphat' ),
			'section'           => 'content',
			'type'              => 'number',
			'default'           => $defaults['tmnhanphat_hero_description_font_size'],
			'sanitize_callback' => 'absint',
		),
		'tmnhanphat_hero_description_clamp_enable' => array(
			'label'             => __( 'Enable Text Clamp', 'tmnhanphat' ),
			'section'           => 'content',
			'type'              => 'checkbox',
			'default'           => $defaults['tmnhanphat_hero_description_clamp_enable'],
			'sanitize_callback' => 'tmnhanphat_sanitize_checkbox',
		),
		'tmnhanphat_hero_description_max_lines'    => array(
			'label'             => __( 'Max Lines', 'tmnhanphat' ),
			'section'           => 'content',
			'type'              => 'number',
			'default'           => $defaults['tmnhanphat_hero_description_max_lines'],
			'sanitize_callback' => 'absint',
			'input_attrs'       => array( 'min' => 1, 'max' => 6, 'step' => 1 ),
		),

		// ----- Buttons: Primary -----
		'tmnhanphat_hero_btn_primary_enable'  => array(
			'label'             => __( 'Primary: Enable', 'tmnhanphat' ),
			'section'           => 'buttons',
			'type'              => 'checkbox',
			'default'           => $defaults['tmnhanphat_hero_btn_primary_enable'],
			'sanitize_callback' => 'tmnhanphat_sanitize_checkbox',
		),
		'tmnhanphat_hero_btn_primary_text'    => array(
			'label'             => __( 'Primary: Text', 'tmnhanphat' ),
			'section'           => 'buttons',
			'type'              => 'text',
			'default'           => $defaults['tmnhanphat_hero_btn_primary_text'],
			'sanitize_callback' => 'sanitize_text_field',
		),
		'tmnhanphat_hero_btn_primary_url'     => array(
			'label'             => __( 'Primary: URL', 'tmnhanphat' ),
			'section'           => 'buttons',
			'type'              => 'text',
			'default'           => $defaults['tmnhanphat_hero_btn_primary_url'],
			'sanitize_callback' => 'esc_url_raw',
		),
		'tmnhanphat_hero_btn_primary_new_tab' => array(
			'label'             => __( 'Primary: Open New Tab', 'tmnhanphat' ),
			'section'           => 'buttons',
			'type'              => 'checkbox',
			'default'           => $defaults['tmnhanphat_hero_btn_primary_new_tab'],
			'sanitize_callback' => 'tmnhanphat_sanitize_checkbox',
		),

		// ----- Buttons: Secondary -----
		'tmnhanphat_hero_btn_secondary_enable'  => array(
			'label'             => __( 'Secondary: Enable', 'tmnhanphat' ),
			'section'           => 'buttons',
			'type'              => 'checkbox',
			'default'           => $defaults['tmnhanphat_hero_btn_secondary_enable'],
			'sanitize_callback' => 'tmnhanphat_sanitize_checkbox',
		),
		'tmnhanphat_hero_btn_secondary_text'    => array(
			'label'             => __( 'Secondary: Text', 'tmnhanphat' ),
			'section'           => 'buttons',
			'type'              => 'text',
			'default'           => $defaults['tmnhanphat_hero_btn_secondary_text'],
			'sanitize_callback' => 'sanitize_text_field',
		),
		'tmnhanphat_hero_btn_secondary_url'     => array(
			'label'             => __( 'Secondary: URL', 'tmnhanphat' ),
			'section'           => 'buttons',
			'type'              => 'text',
			'default'           => $defaults['tmnhanphat_hero_btn_secondary_url'],
			'sanitize_callback' => 'esc_url_raw',
		),
		'tmnhanphat_hero_btn_secondary_new_tab' => array(
			'label'             => __( 'Secondary: Open New Tab', 'tmnhanphat' ),
			'section'           => 'buttons',
			'type'              => 'checkbox',
			'default'           => $defaults['tmnhanphat_hero_btn_secondary_new_tab'],
			'sanitize_callback' => 'tmnhanphat_sanitize_checkbox',
		),

		// ----- Buttons: Style dùng chung (màu riêng từng nút, radius/padding chung) -----
		'tmnhanphat_hero_btn_primary_bg'          => array(
			'label'             => __( 'Primary: Background', 'tmnhanphat' ),
			'section'           => 'buttons',
			'type'              => 'color',
			'default'           => $defaults['tmnhanphat_hero_btn_primary_bg'],
			'sanitize_callback' => 'sanitize_hex_color',
		),
		'tmnhanphat_hero_btn_primary_text_color'  => array(
			'label'             => __( 'Primary: Text Color', 'tmnhanphat' ),
			'section'           => 'buttons',
			'type'              => 'color',
			'default'           => $defaults['tmnhanphat_hero_btn_primary_text_color'],
			'sanitize_callback' => 'sanitize_hex_color',
		),
		'tmnhanphat_hero_btn_primary_hover_bg'    => array(
			'label'             => __( 'Primary: Hover Background', 'tmnhanphat' ),
			'section'           => 'buttons',
			'type'              => 'color',
			'default'           => $defaults['tmnhanphat_hero_btn_primary_hover_bg'],
			'sanitize_callback' => 'sanitize_hex_color',
		),
		'tmnhanphat_hero_btn_secondary_bg'         => array(
			'label'             => __( 'Secondary: Background', 'tmnhanphat' ),
			'section'           => 'buttons',
			'type'              => 'color',
			'default'           => $defaults['tmnhanphat_hero_btn_secondary_bg'],
			'sanitize_callback' => 'sanitize_hex_color',
		),
		'tmnhanphat_hero_btn_secondary_text_color' => array(
			'label'             => __( 'Secondary: Text Color', 'tmnhanphat' ),
			'section'           => 'buttons',
			'type'              => 'color',
			'default'           => $defaults['tmnhanphat_hero_btn_secondary_text_color'],
			'sanitize_callback' => 'sanitize_hex_color',
		),
		'tmnhanphat_hero_btn_secondary_hover_bg'   => array(
			'label'             => __( 'Secondary: Hover Background', 'tmnhanphat' ),
			'section'           => 'buttons',
			'type'              => 'color',
			'default'           => $defaults['tmnhanphat_hero_btn_secondary_hover_bg'],
			'sanitize_callback' => 'sanitize_hex_color',
		),
		'tmnhanphat_hero_btn_secondary_border_color' => array(
			'label'             => __( 'Secondary: Border Color', 'tmnhanphat' ),
			'section'           => 'buttons',
			'type'              => 'color',
			'default'           => $defaults['tmnhanphat_hero_btn_secondary_border_color'],
			'sanitize_callback' => 'sanitize_hex_color',
		),
		'tmnhanphat_hero_btn_border_radius'       => array(
			'label'             => __( 'Border Radius (px, áp dụng cả 2 nút)', 'tmnhanphat' ),
			'section'           => 'buttons',
			'type'              => 'number',
			'default'           => $defaults['tmnhanphat_hero_btn_border_radius'],
			'sanitize_callback' => 'absint',
		),
		'tmnhanphat_hero_btn_padding_x'            => array(
			'label'             => __( 'Padding ngang (px, áp dụng cả 2 nút)', 'tmnhanphat' ),
			'section'           => 'buttons',
			'type'              => 'number',
			'default'           => $defaults['tmnhanphat_hero_btn_padding_x'],
			'sanitize_callback' => 'absint',
		),
		'tmnhanphat_hero_btn_padding_y'            => array(
			'label'             => __( 'Padding dọc (px, áp dụng cả 2 nút)', 'tmnhanphat' ),
			'section'           => 'buttons',
			'type'              => 'number',
			'default'           => $defaults['tmnhanphat_hero_btn_padding_y'],
			'sanitize_callback' => 'absint',
		),

		// ----- Layout -----
		'tmnhanphat_hero_content_max_width'        => array(
			'label'             => __( 'Hero Content Max Width (Desktop, px)', 'tmnhanphat' ),
			'section'           => 'general',
			'type'              => 'number',
			'default'           => $defaults['tmnhanphat_hero_content_max_width'],
			'sanitize_callback' => 'absint',
			'input_attrs'       => array( 'min' => 400, 'max' => 900, 'step' => 10 ),
		),
		'tmnhanphat_hero_content_max_width_tablet' => array(
			'label'             => __( 'Hero Content Max Width (Tablet, px)', 'tmnhanphat' ),
			'section'           => 'general',
			'type'              => 'number',
			'default'           => $defaults['tmnhanphat_hero_content_max_width_tablet'],
			'sanitize_callback' => 'absint',
		),
		'tmnhanphat_hero_content_max_width_mobile' => array(
			'label'             => __( 'Hero Content Max Width (Mobile, px)', 'tmnhanphat' ),
			'section'           => 'general',
			'type'              => 'number',
			'default'           => $defaults['tmnhanphat_hero_content_max_width_mobile'],
			'sanitize_callback' => 'absint',
		),
		'tmnhanphat_hero_content_align'             => array(
			'label'             => __( 'Content Vertical Alignment', 'tmnhanphat' ),
			'section'           => 'general',
			'choices'           => tmnhanphat_get_hero_content_align_choices(),
			'default'           => $defaults['tmnhanphat_hero_content_align'],
			'sanitize_callback' => 'tmnhanphat_sanitize_hero_content_align',
		),
		'tmnhanphat_hero_height_desktop'            => array(
			'label'             => __( 'Hero Height (vh, Desktop only)', 'tmnhanphat' ),
			'section'           => 'general',
			'type'              => 'number',
			'default'           => $defaults['tmnhanphat_hero_height_desktop'],
			'sanitize_callback' => 'absint',
			'input_attrs'       => array( 'min' => 50, 'max' => 100, 'step' => 1 ),
		),

		// ----- Hero Stats Card -----
		'tmnhanphat_hero_stats_card_enable'       => array(
			'label'             => __( 'Enable Background Card', 'tmnhanphat' ),
			'section'           => 'statistics_style',
			'type'              => 'checkbox',
			'default'           => $defaults['tmnhanphat_hero_stats_card_enable'],
			'sanitize_callback' => 'tmnhanphat_sanitize_checkbox',
		),
		'tmnhanphat_hero_stats_card_bg'           => array(
			'label'             => __( 'Background Color', 'tmnhanphat' ),
			'section'           => 'statistics_style',
			'type'              => 'color',
			'default'           => $defaults['tmnhanphat_hero_stats_card_bg'],
			'sanitize_callback' => 'sanitize_hex_color',
		),
		'tmnhanphat_hero_stats_card_opacity'      => array(
			'label'             => __( 'Background Opacity (%)', 'tmnhanphat' ),
			'section'           => 'statistics_style',
			'type'              => 'number',
			'default'           => $defaults['tmnhanphat_hero_stats_card_opacity'],
			'sanitize_callback' => 'absint',
			'input_attrs'       => array( 'min' => 0, 'max' => 100, 'step' => 1 ),
		),
		'tmnhanphat_hero_stats_card_radius'       => array(
			'label'             => __( 'Border Radius (px)', 'tmnhanphat' ),
			'section'           => 'statistics_style',
			'type'              => 'number',
			'default'           => $defaults['tmnhanphat_hero_stats_card_radius'],
			'sanitize_callback' => 'absint',
		),
		'tmnhanphat_hero_stats_card_border_color' => array(
			'label'             => __( 'Border Color', 'tmnhanphat' ),
			'section'           => 'statistics_style',
			'type'              => 'color',
			'default'           => $defaults['tmnhanphat_hero_stats_card_border_color'],
			'sanitize_callback' => 'sanitize_hex_color',
		),
		'tmnhanphat_hero_stats_card_border_width' => array(
			'label'             => __( 'Border Width (px)', 'tmnhanphat' ),
			'section'           => 'statistics_style',
			'type'              => 'number',
			'default'           => $defaults['tmnhanphat_hero_stats_card_border_width'],
			'sanitize_callback' => 'absint',
		),
		'tmnhanphat_hero_stats_card_blur'         => array(
			'label'             => __( 'Backdrop Blur (px)', 'tmnhanphat' ),
			'section'           => 'statistics_style',
			'type'              => 'number',
			'default'           => $defaults['tmnhanphat_hero_stats_card_blur'],
			'sanitize_callback' => 'absint',
		),
		'tmnhanphat_hero_stats_card_shadow'       => array(
			'label'             => __( 'Shadow', 'tmnhanphat' ),
			'section'           => 'statistics_style',
			'choices'           => tmnhanphat_get_hero_stats_card_shadow_choices(),
			'default'           => $defaults['tmnhanphat_hero_stats_card_shadow'],
			'sanitize_callback' => 'tmnhanphat_sanitize_hero_stats_card_shadow',
		),
		'tmnhanphat_hero_stats_number_color'      => array(
			'label'             => __( 'Stat Number Color', 'tmnhanphat' ),
			'section'           => 'statistics_style',
			'type'              => 'color',
			'default'           => $defaults['tmnhanphat_hero_stats_number_color'],
			'sanitize_callback' => 'sanitize_hex_color',
		),
		'tmnhanphat_hero_stats_badge_bg'          => array(
			'label'             => __( 'Stat Badge Background', 'tmnhanphat' ),
			'section'           => 'statistics_style',
			'type'              => 'color',
			'default'           => $defaults['tmnhanphat_hero_stats_badge_bg'],
			'sanitize_callback' => 'sanitize_hex_color',
		),
		'tmnhanphat_hero_stats_badge_text_color'  => array(
			'label'             => __( 'Stat Badge Text Color', 'tmnhanphat' ),
			'section'           => 'statistics_style',
			'type'              => 'color',
			'default'           => $defaults['tmnhanphat_hero_stats_badge_text_color'],
			'sanitize_callback' => 'sanitize_hex_color',
		),

		// ----- Hero Spacing (Desktop) -----
		'tmnhanphat_hero_padding_top'               => array(
			'label'             => __( 'Top Padding (px)', 'tmnhanphat' ),
			'section'           => 'responsive',
			'type'              => 'number',
			'default'           => $defaults['tmnhanphat_hero_padding_top'],
			'sanitize_callback' => 'absint',
		),
		'tmnhanphat_hero_padding_bottom'            => array(
			'label'             => __( 'Bottom Padding (px)', 'tmnhanphat' ),
			'section'           => 'responsive',
			'type'              => 'number',
			'default'           => $defaults['tmnhanphat_hero_padding_bottom'],
			'sanitize_callback' => 'absint',
		),
		'tmnhanphat_hero_heading_margin_bottom'     => array(
			'label'             => __( 'Heading Margin Bottom (px)', 'tmnhanphat' ),
			'section'           => 'responsive',
			'type'              => 'number',
			'default'           => $defaults['tmnhanphat_hero_heading_margin_bottom'],
			'sanitize_callback' => 'absint',
		),
		'tmnhanphat_hero_description_margin_bottom' => array(
			'label'             => __( 'Description Margin Bottom (px)', 'tmnhanphat' ),
			'section'           => 'responsive',
			'type'              => 'number',
			'default'           => $defaults['tmnhanphat_hero_description_margin_bottom'],
			'sanitize_callback' => 'absint',
		),
		'tmnhanphat_hero_cta_margin_bottom'         => array(
			'label'             => __( 'CTA Margin Bottom (px)', 'tmnhanphat' ),
			'section'           => 'responsive',
			'type'              => 'number',
			'default'           => $defaults['tmnhanphat_hero_cta_margin_bottom'],
			'sanitize_callback' => 'absint',
		),
		'tmnhanphat_hero_stats_margin_top'          => array(
			'label'             => __( 'Stats Margin Top (px)', 'tmnhanphat' ),
			'section'           => 'responsive',
			'type'              => 'number',
			'default'           => $defaults['tmnhanphat_hero_stats_margin_top'],
			'sanitize_callback' => 'absint',
		),

		// ----- Hero Spacing (Tablet/Mobile — dùng chung padding-block, giữ tương thích ngược) -----
		'tmnhanphat_hero_padding_tablet'     => array(
			'label'             => __( 'Padding Tablet (px)', 'tmnhanphat' ),
			'section'           => 'responsive',
			'type'              => 'number',
			'default'           => $defaults['tmnhanphat_hero_padding_tablet'],
			'sanitize_callback' => 'absint',
		),
		'tmnhanphat_hero_padding_mobile'     => array(
			'label'             => __( 'Padding Mobile (px)', 'tmnhanphat' ),
			'section'           => 'responsive',
			'type'              => 'number',
			'default'           => $defaults['tmnhanphat_hero_padding_mobile'],
			'sanitize_callback' => 'absint',
		),

		// ----- Animation -----
		'tmnhanphat_hero_animation_enable'         => array(
			'label'             => __( 'Enable Animation', 'tmnhanphat' ),
			'section'           => 'animation',
			'type'              => 'checkbox',
			'default'           => $defaults['tmnhanphat_hero_animation_enable'],
			'sanitize_callback' => 'tmnhanphat_sanitize_checkbox',
		),
		'tmnhanphat_hero_counter_animation_enable' => array(
			'label'             => __( 'Enable Counter Animation', 'tmnhanphat' ),
			'section'           => 'animation',
			'type'              => 'checkbox',
			'default'           => $defaults['tmnhanphat_hero_counter_animation_enable'],
			'sanitize_callback' => 'tmnhanphat_sanitize_checkbox',
		),
		'tmnhanphat_hero_animation_duration'       => array(
			'label'             => __( 'Animation Duration (ms)', 'tmnhanphat' ),
			'section'           => 'animation',
			'type'              => 'number',
			'default'           => $defaults['tmnhanphat_hero_animation_duration'],
			'sanitize_callback' => 'absint',
			'input_attrs'       => array( 'min' => 200, 'max' => 2000, 'step' => 50 ),
		),
		'tmnhanphat_hero_animation_delay'          => array(
			'label'             => __( 'Animation Delay giữa các Stat (ms)', 'tmnhanphat' ),
			'section'           => 'animation',
			'type'              => 'number',
			'default'           => $defaults['tmnhanphat_hero_animation_delay'],
			'sanitize_callback' => 'absint',
			'input_attrs'       => array( 'min' => 0, 'max' => 500, 'step' => 10 ),
		),
	);

	for ( $i = 1; $i <= 4; $i++ ) {
		$fields = array_merge( $fields, tmnhanphat_get_hero_stat_fields( $i, $defaults ) );
	}

	return $fields;
}

/**
 * Danh sách lựa chọn Font Weight dùng chung cho Subtitle/Heading.
 *
 * @return array<string, string>
 */
function tmnhanphat_get_font_weight_choices() {
	return array(
		'400' => __( 'Normal (400)', 'tmnhanphat' ),
		'500' => __( 'Medium (500)', 'tmnhanphat' ),
		'600' => __( 'Semibold (600)', 'tmnhanphat' ),
		'700' => __( 'Bold (700)', 'tmnhanphat' ),
		'800' => __( 'Extrabold (800)', 'tmnhanphat' ),
	);
}

/**
 * Whitelist Font Weight.
 *
 * @param string $value Giá trị gửi lên từ Customizer.
 * @return string
 */
function tmnhanphat_sanitize_font_weight( $value ) {
	$choices = array_keys( tmnhanphat_get_font_weight_choices() );

	return in_array( $value, $choices, true ) ? $value : '500';
}

/**
 * Whitelist Overlay Blend Mode.
 *
 * @param string $value Giá trị gửi lên từ Customizer.
 * @return string
 */
function tmnhanphat_sanitize_hero_overlay_blend( $value ) {
	$choices = array_keys( tmnhanphat_get_hero_overlay_blend_choices() );

	return in_array( $value, $choices, true ) ? $value : 'normal';
}

/**
 * Whitelist Content Vertical Alignment.
 *
 * @param string $value Giá trị gửi lên từ Customizer.
 * @return string
 */
function tmnhanphat_sanitize_hero_content_align( $value ) {
	$choices = array_keys( tmnhanphat_get_hero_content_align_choices() );

	return in_array( $value, $choices, true ) ? $value : 'center';
}

/**
 * Whitelist Stats Card Shadow.
 *
 * @param string $value Giá trị gửi lên từ Customizer.
 * @return string
 */
function tmnhanphat_sanitize_hero_stats_card_shadow( $value ) {
	$choices = array_keys( tmnhanphat_get_hero_stats_card_shadow_choices() );

	return in_array( $value, $choices, true ) ? $value : 'soft';
}

/**
 * Đăng ký Panel riêng "Hero Home" + toàn bộ Section/Setting/Control của Hero Banner.
 *
 * Mỗi feature Homepage (Hero/Partner/...) có Panel RIÊNG ở cấp cao nhất của Customizer,
 * KHÔNG lồng chung trong 1 panel "Homepage" — vì WordPress Customizer core không hỗ trợ
 * panel-trong-panel (PROJECT_RULES.md mục 26). Đặt `priority` liền nhau (30, 31, 32...) để
 * các Panel "...Home" hiển thị cạnh nhau trong danh sách gốc, tạo cảm giác nhóm dù không có
 * khung bao literal.
 *
 * @param WP_Customize_Manager $wp_customize Customizer manager instance.
 */
function tmnhanphat_hero_customizer_register( $wp_customize ) {
	$wp_customize->add_panel( 'tmnhanphat_hero_panel', array(
		'title'    => __( 'Hero Home', 'tmnhanphat' ),
		'priority' => 30,
	) );

	$sections = array(
		'general'          => __( 'General', 'tmnhanphat' ),
		'content'          => __( 'Content', 'tmnhanphat' ),
		'buttons'          => __( 'Buttons', 'tmnhanphat' ),
		'background'       => __( 'Background', 'tmnhanphat' ),
		'overlay'          => __( 'Overlay', 'tmnhanphat' ),
		'statistics_1'     => __( 'Statistics 1', 'tmnhanphat' ),
		'statistics_2'     => __( 'Statistics 2', 'tmnhanphat' ),
		'statistics_3'     => __( 'Statistics 3', 'tmnhanphat' ),
		'statistics_4'     => __( 'Statistics 4', 'tmnhanphat' ),
		'statistics_style' => __( 'Statistics Style', 'tmnhanphat' ),
		'responsive'       => __( 'Responsive', 'tmnhanphat' ),
		'animation'        => __( 'Animation', 'tmnhanphat' ),
	);

	foreach ( $sections as $section_id => $section_label ) {
		$wp_customize->add_section( "tmnhanphat_hero_section_{$section_id}", array(
			'title' => $section_label,
			'panel' => 'tmnhanphat_hero_panel',
		) );
	}

	foreach ( tmnhanphat_get_hero_customizer_fields() as $field_id => $field ) {
		$wp_customize->add_setting( $field_id, array(
			'default'           => $field['default'],
			'sanitize_callback' => $field['sanitize_callback'],
			'transport'         => 'refresh',
		) );

		$control_args = array(
			'label'    => $field['label'],
			'section'  => 'tmnhanphat_hero_section_' . $field['section'],
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
add_action( 'customize_register', 'tmnhanphat_hero_customizer_register' );
