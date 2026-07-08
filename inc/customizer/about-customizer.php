<?php
/**
 * Đăng ký Customizer Panel riêng "About Home" (không lồng trong panel "Homepage" nào —
 * WordPress Customizer không hỗ trợ panel-trong-panel, xem PROJECT_RULES.md mục 26):
 * General, Content, Background Shape, Elevator Image, Responsive. Cùng quy ước config-driven
 * như hero/partners-customizer.php (PROJECT_RULES.md mục 22).
 *
 * BẢN V3: field "Background"/"Layout"/"Responsive" bản v1-v2 (dùng để tự vẽ hình bằng
 * clip-path) đã được thay bằng "Background Shape" (Upload Shape Image + Size/Position/
 * Opacity) và "Elevator Image" (Upload + Size/Offset/Z-index) — khớp cách render mới dùng
 * ảnh PNG thật do designer xuất, xem PROJECT_RULES.md mục 28 (bản v3).
 *
 * @package TMNhanPhat
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Khai báo toàn bộ field của About Company Section dạng config (id => định nghĩa).
 *
 * @return array<string, array>
 */
function tmnhanphat_get_about_customizer_fields() {
	$defaults = tmnhanphat_about_defaults();

	return array(
		// ----- General -----
		'tmnhanphat_about_enable'          => array(
			'label'             => __( 'Enable Section', 'tmnhanphat' ),
			'section'           => 'general',
			'type'              => 'checkbox',
			'default'           => $defaults['tmnhanphat_about_enable'],
			'sanitize_callback' => 'tmnhanphat_sanitize_checkbox',
		),
		'tmnhanphat_about_section_id'      => array(
			'label'             => __( 'Section ID', 'tmnhanphat' ),
			'section'           => 'general',
			'type'              => 'text',
			'default'           => $defaults['tmnhanphat_about_section_id'],
			'sanitize_callback' => 'sanitize_key',
		),
		'tmnhanphat_about_container_width' => array(
			'label'             => __( 'Container Width (px) — 0 = dùng Global Settings', 'tmnhanphat' ),
			'section'           => 'general',
			'type'              => 'number',
			'default'           => $defaults['tmnhanphat_about_container_width'],
			'sanitize_callback' => 'absint',
			'input_attrs'       => array( 'min' => 0, 'max' => 1920, 'step' => 10 ),
		),

		// ----- Content -----
		'tmnhanphat_about_label'                 => array(
			'label'             => __( 'Small Label', 'tmnhanphat' ),
			'section'           => 'content',
			'type'              => 'text',
			'default'           => $defaults['tmnhanphat_about_label'],
			'sanitize_callback' => 'sanitize_text_field',
		),
		'tmnhanphat_about_heading'                => array(
			'label'             => __( 'Heading', 'tmnhanphat' ),
			'section'           => 'content',
			'type'              => 'text',
			'default'           => $defaults['tmnhanphat_about_heading'],
			'sanitize_callback' => 'sanitize_text_field',
		),
		'tmnhanphat_about_description'             => array(
			'label'             => __( 'Description', 'tmnhanphat' ),
			'section'           => 'content',
			'type'              => 'textarea',
			'default'           => $defaults['tmnhanphat_about_description'],
			'sanitize_callback' => 'sanitize_textarea_field',
		),
		'tmnhanphat_about_text_width'              => array(
			'label'             => __( 'Content Width (%)', 'tmnhanphat' ),
			'section'           => 'content',
			'type'              => 'number',
			'default'           => $defaults['tmnhanphat_about_text_width'],
			'sanitize_callback' => 'absint',
			'input_attrs'       => array( 'min' => 25, 'max' => 60, 'step' => 1 ),
		),
		'tmnhanphat_about_description_max_width'   => array(
			'label'             => __( 'Max Description Width (px)', 'tmnhanphat' ),
			'section'           => 'content',
			'type'              => 'number',
			'default'           => $defaults['tmnhanphat_about_description_max_width'],
			'sanitize_callback' => 'absint',
			'input_attrs'       => array( 'min' => 240, 'max' => 700, 'step' => 10 ),
		),
		'tmnhanphat_about_vertical_align'          => array(
			'label'             => __( 'Vertical Alignment', 'tmnhanphat' ),
			'section'           => 'content',
			'choices'           => tmnhanphat_get_about_vertical_align_choices(),
			'default'           => $defaults['tmnhanphat_about_vertical_align'],
			'sanitize_callback' => 'tmnhanphat_sanitize_about_vertical_align',
		),

		// ----- Background Shape -----
		'tmnhanphat_about_shape_enable'      => array(
			'label'             => __( 'Enable Background Shape', 'tmnhanphat' ),
			'section'           => 'background',
			'type'              => 'checkbox',
			'default'           => $defaults['tmnhanphat_about_shape_enable'],
			'sanitize_callback' => 'tmnhanphat_sanitize_checkbox',
		),
		'tmnhanphat_about_shape_image'       => array(
			'label'             => __( 'Upload Shape Image (Rectangle 19.png)', 'tmnhanphat' ),
			'section'           => 'background',
			'type'              => 'image',
			'default'           => $defaults['tmnhanphat_about_shape_image'],
			'sanitize_callback' => 'esc_url_raw',
		),
		'tmnhanphat_about_bg_color'          => array(
			'label'             => __( 'Fallback Background Color (hiện khi chưa upload ảnh)', 'tmnhanphat' ),
			'section'           => 'background',
			'type'              => 'color',
			'default'           => $defaults['tmnhanphat_about_bg_color'],
			'sanitize_callback' => 'sanitize_hex_color',
		),
		'tmnhanphat_about_shape_bg_size'     => array(
			'label'             => __( 'Background Size', 'tmnhanphat' ),
			'section'           => 'background',
			'choices'           => tmnhanphat_get_about_bg_size_choices(),
			'default'           => $defaults['tmnhanphat_about_shape_bg_size'],
			'sanitize_callback' => 'tmnhanphat_sanitize_about_bg_size',
		),
		'tmnhanphat_about_shape_bg_position' => array(
			'label'             => __( 'Background Position', 'tmnhanphat' ),
			'section'           => 'background',
			'choices'           => tmnhanphat_get_about_bg_position_choices(),
			'default'           => $defaults['tmnhanphat_about_shape_bg_position'],
			'sanitize_callback' => 'tmnhanphat_sanitize_about_bg_position',
		),
		'tmnhanphat_about_shape_opacity'     => array(
			'label'             => __( 'Background Opacity (%)', 'tmnhanphat' ),
			'section'           => 'background',
			'type'              => 'number',
			'default'           => $defaults['tmnhanphat_about_shape_opacity'],
			'sanitize_callback' => 'absint',
			'input_attrs'       => array( 'min' => 0, 'max' => 100, 'step' => 5 ),
		),

		// ----- Elevator Image -----
		'tmnhanphat_about_image'               => array(
			'label'             => __( 'Upload Elevator Image (PNG nền trong suốt)', 'tmnhanphat' ),
			'section'           => 'image',
			'type'              => 'image',
			'default'           => $defaults['tmnhanphat_about_image'],
			'sanitize_callback' => 'esc_url_raw',
		),
		'tmnhanphat_about_image_width_desktop' => array(
			'label'             => __( 'Elevator Size — Desktop (% chiều cao section)', 'tmnhanphat' ),
			'section'           => 'image',
			'type'              => 'number',
			'default'           => $defaults['tmnhanphat_about_image_width_desktop'],
			'sanitize_callback' => 'absint',
			'input_attrs'       => array( 'min' => 30, 'max' => 100, 'step' => 1 ),
		),
		'tmnhanphat_about_image_width_tablet'  => array(
			'label'             => __( 'Elevator Size — Tablet (%)', 'tmnhanphat' ),
			'section'           => 'image',
			'type'              => 'number',
			'default'           => $defaults['tmnhanphat_about_image_width_tablet'],
			'sanitize_callback' => 'absint',
			'input_attrs'       => array( 'min' => 20, 'max' => 100, 'step' => 1 ),
		),
		'tmnhanphat_about_image_width_mobile'  => array(
			'label'             => __( 'Elevator Size — Mobile (%)', 'tmnhanphat' ),
			'section'           => 'image',
			'type'              => 'number',
			'default'           => $defaults['tmnhanphat_about_image_width_mobile'],
			'sanitize_callback' => 'absint',
			'input_attrs'       => array( 'min' => 20, 'max' => 100, 'step' => 1 ),
		),
		'tmnhanphat_about_image_offset_x'      => array(
			'label'             => __( 'Horizontal Offset (px, âm = đè thêm vào Shape)', 'tmnhanphat' ),
			'section'           => 'image',
			'type'              => 'number',
			'default'           => $defaults['tmnhanphat_about_image_offset_x'],
			'sanitize_callback' => 'absint', // ghi đè cho phép âm — xem $signed_fields bên dưới.
			'input_attrs'       => array( 'min' => -200, 'max' => 200, 'step' => 5 ),
		),
		'tmnhanphat_about_image_offset_y'      => array(
			'label'             => __( 'Vertical Offset (px)', 'tmnhanphat' ),
			'section'           => 'image',
			'type'              => 'number',
			'default'           => $defaults['tmnhanphat_about_image_offset_y'],
			'sanitize_callback' => 'absint', // ghi đè cho phép âm — xem $signed_fields bên dưới.
			'input_attrs'       => array( 'min' => -150, 'max' => 150, 'step' => 5 ),
		),
		'tmnhanphat_about_elevator_zindex'     => array(
			'label'             => __( 'Z-index', 'tmnhanphat' ),
			'section'           => 'image',
			'type'              => 'number',
			'default'           => $defaults['tmnhanphat_about_elevator_zindex'],
			'sanitize_callback' => 'absint',
			'input_attrs'       => array( 'min' => 1, 'max' => 10, 'step' => 1 ),
		),

		// ----- Responsive -----
		'tmnhanphat_about_mobile_layout'       => array(
			'label'             => __( 'Mobile Layout', 'tmnhanphat' ),
			'section'           => 'responsive',
			'choices'           => tmnhanphat_get_about_mobile_layout_choices(),
			'default'           => $defaults['tmnhanphat_about_mobile_layout'],
			'sanitize_callback' => 'tmnhanphat_sanitize_about_mobile_layout',
		),
	);
}

/**
 * Sanitize callback riêng cho 2 field CHO PHÉP SỐ ÂM (Elevator Offset X/Y) — absint() sẽ
 * làm mất dấu âm nên không dùng được, cần intval() rồi tự giới hạn khoảng cho phép.
 *
 * @param mixed $value Giá trị gửi lên từ Customizer.
 * @return int
 */
function tmnhanphat_sanitize_about_offset_x( $value ) {
	return max( -200, min( 200, (int) $value ) );
}

/**
 * @param mixed $value Giá trị gửi lên từ Customizer.
 * @return int
 */
function tmnhanphat_sanitize_about_offset_y( $value ) {
	return max( -150, min( 150, (int) $value ) );
}

/**
 * Đăng ký Panel riêng "About Home" + toàn bộ Section/Setting/Control của About Company.
 *
 * Mỗi feature Homepage (Hero/Partner/About...) có Panel RIÊNG ở cấp cao nhất của Customizer,
 * KHÔNG lồng chung trong 1 panel "Homepage" — vì WordPress Customizer core không hỗ trợ
 * panel-trong-panel (PROJECT_RULES.md mục 26). Priority nối tiếp ngay sau Partner Home (31).
 *
 * @param WP_Customize_Manager $wp_customize Customizer manager instance.
 */
function tmnhanphat_about_customizer_register( $wp_customize ) {
	$wp_customize->add_panel( 'tmnhanphat_about_panel', array(
		'title'    => __( 'About Home', 'tmnhanphat' ),
		'priority' => 32,
	) );

	$sections = array(
		'general'    => __( 'General', 'tmnhanphat' ),
		'content'    => __( 'Content', 'tmnhanphat' ),
		'background' => __( 'Background Shape', 'tmnhanphat' ),
		'image'      => __( 'Elevator Image', 'tmnhanphat' ),
		'responsive' => __( 'Responsive', 'tmnhanphat' ),
	);

	foreach ( $sections as $section_id => $section_label ) {
		$wp_customize->add_section( "tmnhanphat_about_section_{$section_id}", array(
			'title' => $section_label,
			'panel' => 'tmnhanphat_about_panel',
		) );
	}

	// Field cho phép số âm cần sanitize_callback riêng — ghi đè config chung phía trên.
	$signed_fields = array(
		'tmnhanphat_about_image_offset_x' => 'tmnhanphat_sanitize_about_offset_x',
		'tmnhanphat_about_image_offset_y' => 'tmnhanphat_sanitize_about_offset_y',
	);

	foreach ( tmnhanphat_get_about_customizer_fields() as $field_id => $field ) {
		$sanitize_callback = isset( $signed_fields[ $field_id ] ) ? $signed_fields[ $field_id ] : $field['sanitize_callback'];

		$wp_customize->add_setting( $field_id, array(
			'default'           => $field['default'],
			'sanitize_callback' => $sanitize_callback,
			'transport'         => 'refresh',
		) );

		$control_args = array(
			'label'    => $field['label'],
			'section'  => 'tmnhanphat_about_section_' . $field['section'],
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
		$wp_customize->add_control( $field_id, $control_args );
	}
}
add_action( 'customize_register', 'tmnhanphat_about_customizer_register' );
