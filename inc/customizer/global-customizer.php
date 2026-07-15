<?php
/**
 * Đăng ký Customizer Panel riêng "Global Settings" → Section "Layout": Container
 * Padding trái/phải theo 3 breakpoint (Desktop/Tablet/Mobile) — nguồn dữ liệu DUY
 * NHẤT cho khoảng cách 2 bên của toàn site (PROJECT_RULES.md mục 27). Panel này
 * dành cho setting mang tính TOÀN SITE (không riêng Homepage) — Section "Layout"
 * là khởi đầu, sau này có thể thêm Section "Typography"/"Colors" cùng Panel.
 *
 * Cùng quy ước config-driven như hero/partners/header/footer-customizer.php
 * (PROJECT_RULES.md mục 22 & 26).
 *
 * @package TMNhanPhat
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Khai báo toàn bộ field của Global Layout dạng config (id => định nghĩa).
 *
 * @return array<string, array>
 */
function tmnhanphat_get_global_customizer_fields() {
	$defaults = tmnhanphat_global_defaults();

	return array(
		'tmnhanphat_container_width'            => array(
			'label'             => __( 'Container Width (px)', 'tmnhanphat' ),
			'section'           => 'layout',
			'type'              => 'number',
			'default'           => $defaults['tmnhanphat_container_width'],
			'sanitize_callback' => 'absint',
			'input_attrs'       => array( 'min' => 960, 'max' => 1920, 'step' => 10 ),
		),
		'tmnhanphat_container_padding_desktop' => array(
			'label'             => __( 'Container Padding — Desktop (px)', 'tmnhanphat' ),
			'section'           => 'layout',
			'type'              => 'number',
			'default'           => $defaults['tmnhanphat_container_padding_desktop'],
			'sanitize_callback' => 'absint',
			'input_attrs'       => array( 'min' => 0, 'max' => 120, 'step' => 1 ),
		),
		'tmnhanphat_container_padding_tablet'  => array(
			'label'             => __( 'Container Padding — Tablet (px)', 'tmnhanphat' ),
			'section'           => 'layout',
			'type'              => 'number',
			'default'           => $defaults['tmnhanphat_container_padding_tablet'],
			'sanitize_callback' => 'absint',
			'input_attrs'       => array( 'min' => 0, 'max' => 100, 'step' => 1 ),
		),
		'tmnhanphat_container_padding_mobile'  => array(
			'label'             => __( 'Container Padding — Mobile (px)', 'tmnhanphat' ),
			'section'           => 'layout',
			'type'              => 'number',
			'default'           => $defaults['tmnhanphat_container_padding_mobile'],
			'sanitize_callback' => 'absint',
			'input_attrs'       => array( 'min' => 0, 'max' => 80, 'step' => 1 ),
		),

		// ----- Homepage Spacing (áp dụng đồng loạt padding trên/dưới cho MỌI section
		// trang chủ; 0 = không override, giữ padding riêng từng section — mục XIV). -----
		'tmnhanphat_home_section_pt_desktop' => array(
			'label'             => __( 'Section Top Padding — Desktop (px, 0 = giữ từng section)', 'tmnhanphat' ),
			'section'           => 'spacing',
			'type'              => 'number',
			'default'           => $defaults['tmnhanphat_home_section_pt_desktop'],
			'sanitize_callback' => 'absint',
			'input_attrs'       => array( 'min' => 0, 'max' => 240, 'step' => 2 ),
		),
		'tmnhanphat_home_section_pb_desktop' => array(
			'label'             => __( 'Section Bottom Padding — Desktop (px, 0 = giữ từng section)', 'tmnhanphat' ),
			'section'           => 'spacing',
			'type'              => 'number',
			'default'           => $defaults['tmnhanphat_home_section_pb_desktop'],
			'sanitize_callback' => 'absint',
			'input_attrs'       => array( 'min' => 0, 'max' => 240, 'step' => 2 ),
		),
		'tmnhanphat_home_section_pt_tablet'  => array(
			'label'             => __( 'Section Top Padding — Tablet (px, 0 = giữ từng section)', 'tmnhanphat' ),
			'section'           => 'spacing',
			'type'              => 'number',
			'default'           => $defaults['tmnhanphat_home_section_pt_tablet'],
			'sanitize_callback' => 'absint',
			'input_attrs'       => array( 'min' => 0, 'max' => 200, 'step' => 2 ),
		),
		'tmnhanphat_home_section_pb_tablet'  => array(
			'label'             => __( 'Section Bottom Padding — Tablet (px, 0 = giữ từng section)', 'tmnhanphat' ),
			'section'           => 'spacing',
			'type'              => 'number',
			'default'           => $defaults['tmnhanphat_home_section_pb_tablet'],
			'sanitize_callback' => 'absint',
			'input_attrs'       => array( 'min' => 0, 'max' => 200, 'step' => 2 ),
		),
		'tmnhanphat_home_section_pt_mobile'  => array(
			'label'             => __( 'Section Top Padding — Mobile (px, 0 = giữ từng section)', 'tmnhanphat' ),
			'section'           => 'spacing',
			'type'              => 'number',
			'default'           => $defaults['tmnhanphat_home_section_pt_mobile'],
			'sanitize_callback' => 'absint',
			'input_attrs'       => array( 'min' => 0, 'max' => 160, 'step' => 2 ),
		),
		'tmnhanphat_home_section_pb_mobile'  => array(
			'label'             => __( 'Section Bottom Padding — Mobile (px, 0 = giữ từng section)', 'tmnhanphat' ),
			'section'           => 'spacing',
			'type'              => 'number',
			'default'           => $defaults['tmnhanphat_home_section_pb_mobile'],
			'sanitize_callback' => 'absint',
			'input_attrs'       => array( 'min' => 0, 'max' => 160, 'step' => 2 ),
		),
	);
}

/**
 * Đăng ký Panel "Global Settings" + Section "Layout" + toàn bộ Setting/Control.
 *
 * @param WP_Customize_Manager $wp_customize Customizer manager instance.
 */
function tmnhanphat_global_customizer_register( $wp_customize ) {
	$wp_customize->add_panel( 'tmnhanphat_global_panel', array(
		'title'       => __( 'Global Settings', 'tmnhanphat' ),
		'description' => __( 'Setting áp dụng cho toàn bộ website: Header, Hero, Partner, Footer, mọi Section trang chủ, Archive, Single, Page...', 'tmnhanphat' ),
		'priority'    => 5,
	) );

	$wp_customize->add_section( 'tmnhanphat_global_section_layout', array(
		'title' => __( 'Layout', 'tmnhanphat' ),
		'panel' => 'tmnhanphat_global_panel',
	) );

	$wp_customize->add_section( 'tmnhanphat_global_section_spacing', array(
		'title'       => __( 'Homepage Spacing', 'tmnhanphat' ),
		'description' => __( 'Đặt khoảng đệm trên/dưới đồng loạt cho mọi section trang chủ. Để 0 nếu muốn giữ khoảng cách riêng của từng section.', 'tmnhanphat' ),
		'panel'       => 'tmnhanphat_global_panel',
	) );

	foreach ( tmnhanphat_get_global_customizer_fields() as $field_id => $field ) {
		$wp_customize->add_setting( $field_id, array(
			'default'           => $field['default'],
			'sanitize_callback' => $field['sanitize_callback'],
			'transport'         => 'refresh',
		) );

		$control_args = array(
			'label'    => $field['label'],
			'section'  => 'tmnhanphat_global_section_' . $field['section'],
			'settings' => $field_id,
			'type'     => $field['type'],
		);

		if ( ! empty( $field['input_attrs'] ) ) {
			$control_args['input_attrs'] = $field['input_attrs'];
		}

		$wp_customize->add_control( $field_id, $control_args );
	}
}
add_action( 'customize_register', 'tmnhanphat_global_customizer_register' );
