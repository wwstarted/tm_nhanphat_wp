<?php
/**
 * Đăng ký Customizer Panel "Header": Logo, Bố cục, Sticky/Transparent, Màu sắc,
 * Typography menu, Hiệu ứng viền/bóng đổ. Giá trị đọc lại qua tmnhanphat_get_header_mod()
 * (inc/template-functions.php), CSS output qua tmnhanphat_render_header_css_vars().
 *
 * @package TMNhanPhat
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Khai báo toàn bộ field của Header Panel dạng config (id => định nghĩa), tránh lặp lại
 * add_setting()/add_control() 21 lần thủ công (DRY — PROJECT_RULES.md mục 11/12).
 *
 * @return array<string, array>
 */
function tmnhanphat_get_header_customizer_fields() {
	$defaults = tmnhanphat_header_defaults();

	return array(
		// ----- Logo -----
		'tmnhanphat_header_logo'             => array(
			'label'             => __( 'Logo', 'tmnhanphat' ),
			'section'           => 'logo',
			'type'              => 'image',
			'default'           => $defaults['tmnhanphat_header_logo'],
			'sanitize_callback' => 'esc_url_raw',
		),
		'tmnhanphat_header_logo_retina'      => array(
			'label'             => __( 'Retina Logo (2x)', 'tmnhanphat' ),
			'section'           => 'logo',
			'type'              => 'image',
			'default'           => $defaults['tmnhanphat_header_logo_retina'],
			'sanitize_callback' => 'esc_url_raw',
		),
		'tmnhanphat_header_logo_width'       => array(
			'label'             => __( 'Độ rộng Logo (px)', 'tmnhanphat' ),
			'section'           => 'logo',
			'type'              => 'number',
			'default'           => $defaults['tmnhanphat_header_logo_width'],
			'sanitize_callback' => 'absint',
		),

		// ----- Bố cục -----
		'tmnhanphat_header_height'           => array(
			'label'             => __( 'Chiều cao Header (px)', 'tmnhanphat' ),
			'section'           => 'layout',
			'type'              => 'number',
			'default'           => $defaults['tmnhanphat_header_height'],
			'sanitize_callback' => 'absint',
		),
		'tmnhanphat_header_container_width'  => array(
			'label'             => __( 'Độ rộng Container (px)', 'tmnhanphat' ),
			'section'           => 'layout',
			'type'              => 'number',
			'default'           => $defaults['tmnhanphat_header_container_width'],
			'sanitize_callback' => 'absint',
		),
		'tmnhanphat_header_padding_top'      => array(
			'label'             => __( 'Padding Top (px)', 'tmnhanphat' ),
			'section'           => 'layout',
			'type'              => 'number',
			'default'           => $defaults['tmnhanphat_header_padding_top'],
			'sanitize_callback' => 'absint',
		),
		'tmnhanphat_header_padding_bottom'   => array(
			'label'             => __( 'Padding Bottom (px)', 'tmnhanphat' ),
			'section'           => 'layout',
			'type'              => 'number',
			'default'           => $defaults['tmnhanphat_header_padding_bottom'],
			'sanitize_callback' => 'absint',
		),
		'tmnhanphat_header_mobile_breakpoint' => array(
			'label'             => __( 'Mobile Breakpoint (px)', 'tmnhanphat' ),
			'section'           => 'layout',
			'type'              => 'number',
			'default'           => $defaults['tmnhanphat_header_mobile_breakpoint'],
			'sanitize_callback' => 'absint',
		),

		// ----- Sticky & Transparent -----
		'tmnhanphat_header_sticky_enable'    => array(
			'label'             => __( 'Bật Sticky Header', 'tmnhanphat' ),
			'section'           => 'sticky',
			'type'              => 'checkbox',
			'default'           => $defaults['tmnhanphat_header_sticky_enable'],
			'sanitize_callback' => 'tmnhanphat_sanitize_checkbox',
		),
		'tmnhanphat_header_transparent_enable' => array(
			'label'             => __( 'Header trong suốt ở Trang chủ', 'tmnhanphat' ),
			'section'           => 'sticky',
			'type'              => 'checkbox',
			'default'           => $defaults['tmnhanphat_header_transparent_enable'],
			'sanitize_callback' => 'tmnhanphat_sanitize_checkbox',
		),
		'tmnhanphat_header_transition_duration' => array(
			'label'             => __( 'Thời gian chuyển hiệu ứng Sticky (ms)', 'tmnhanphat' ),
			'section'           => 'sticky',
			'type'              => 'number',
			'default'           => $defaults['tmnhanphat_header_transition_duration'],
			'sanitize_callback' => 'absint',
		),

		// ----- Màu sắc -----
		'tmnhanphat_header_bg'               => array(
			'label'             => __( 'Màu nền Header (mặc định)', 'tmnhanphat' ),
			'section'           => 'colors',
			'type'              => 'color',
			'default'           => $defaults['tmnhanphat_header_bg'],
			'sanitize_callback' => 'sanitize_hex_color',
		),
		'tmnhanphat_header_sticky_bg'        => array(
			'label'             => __( 'Màu nền khi Sticky', 'tmnhanphat' ),
			'section'           => 'colors',
			'type'              => 'color',
			'default'           => $defaults['tmnhanphat_header_sticky_bg'],
			'sanitize_callback' => 'sanitize_hex_color',
		),
		'tmnhanphat_header_sticky_text'      => array(
			'label'             => __( 'Màu chữ khi Sticky', 'tmnhanphat' ),
			'section'           => 'colors',
			'type'              => 'color',
			'default'           => $defaults['tmnhanphat_header_sticky_text'],
			'sanitize_callback' => 'sanitize_hex_color',
		),
		'tmnhanphat_header_transparent_text' => array(
			'label'             => __( 'Màu chữ khi trong suốt', 'tmnhanphat' ),
			'section'           => 'colors',
			'type'              => 'color',
			'default'           => $defaults['tmnhanphat_header_transparent_text'],
			'sanitize_callback' => 'sanitize_hex_color',
		),

		// ----- Navigation: Floating (Header trong suốt) -----
		'tmnhanphat_header_nav_text_floating'   => array(
			'label'             => __( 'Floating: Menu Text Color', 'tmnhanphat' ),
			'section'           => 'navigation',
			'type'              => 'color',
			'default'           => $defaults['tmnhanphat_header_nav_text_floating'],
			'sanitize_callback' => 'sanitize_hex_color',
		),
		'tmnhanphat_header_nav_hover_floating'  => array(
			'label'             => __( 'Floating: Menu Hover Color', 'tmnhanphat' ),
			'section'           => 'navigation',
			'type'              => 'color',
			'default'           => $defaults['tmnhanphat_header_nav_hover_floating'],
			'sanitize_callback' => 'sanitize_hex_color',
		),
		'tmnhanphat_header_nav_active_floating' => array(
			'label'             => __( 'Floating: Menu Active Color', 'tmnhanphat' ),
			'section'           => 'navigation',
			'type'              => 'color',
			'default'           => $defaults['tmnhanphat_header_nav_active_floating'],
			'sanitize_callback' => 'sanitize_hex_color',
		),

		// ----- Navigation: Sticky (Header đã cuộn/solid) -----
		'tmnhanphat_header_nav_text_sticky'     => array(
			'label'             => __( 'Sticky: Menu Text Color', 'tmnhanphat' ),
			'section'           => 'navigation',
			'type'              => 'color',
			'default'           => $defaults['tmnhanphat_header_nav_text_sticky'],
			'sanitize_callback' => 'sanitize_hex_color',
		),
		'tmnhanphat_header_nav_hover_sticky'    => array(
			'label'             => __( 'Sticky: Menu Hover Color', 'tmnhanphat' ),
			'section'           => 'navigation',
			'type'              => 'color',
			'default'           => $defaults['tmnhanphat_header_nav_hover_sticky'],
			'sanitize_callback' => 'sanitize_hex_color',
		),
		'tmnhanphat_header_nav_active_sticky'   => array(
			'label'             => __( 'Sticky: Menu Active Color', 'tmnhanphat' ),
			'section'           => 'navigation',
			'type'              => 'color',
			'default'           => $defaults['tmnhanphat_header_nav_active_sticky'],
			'sanitize_callback' => 'sanitize_hex_color',
		),

		// ----- Typography menu -----
		'tmnhanphat_header_menu_font'        => array(
			'label'             => __( 'Font chữ Menu', 'tmnhanphat' ),
			'section'           => 'typography',
			'choices'           => tmnhanphat_get_menu_font_choices(),
			'default'           => $defaults['tmnhanphat_header_menu_font'],
			'sanitize_callback' => 'tmnhanphat_sanitize_menu_font',
		),
		'tmnhanphat_header_menu_font_size'   => array(
			'label'             => __( 'Cỡ chữ Menu (px)', 'tmnhanphat' ),
			'section'           => 'typography',
			'type'              => 'number',
			'default'           => $defaults['tmnhanphat_header_menu_font_size'],
			'sanitize_callback' => 'absint',
		),
		'tmnhanphat_header_menu_font_weight' => array(
			'label'             => __( 'Độ đậm chữ Menu', 'tmnhanphat' ),
			'section'           => 'typography',
			'choices'           => array(
				'400' => __( 'Normal (400)', 'tmnhanphat' ),
				'500' => __( 'Medium (500)', 'tmnhanphat' ),
				'600' => __( 'Semibold (600)', 'tmnhanphat' ),
				'700' => __( 'Bold (700)', 'tmnhanphat' ),
			),
			'default'           => $defaults['tmnhanphat_header_menu_font_weight'],
			'sanitize_callback' => 'tmnhanphat_sanitize_menu_font_weight',
		),
		'tmnhanphat_header_menu_spacing'     => array(
			'label'             => __( 'Khoảng cách giữa các mục Menu (px)', 'tmnhanphat' ),
			'section'           => 'typography',
			'type'              => 'number',
			'default'           => $defaults['tmnhanphat_header_menu_spacing'],
			'sanitize_callback' => 'absint',
		),

		// ----- Hiệu ứng viền/bóng đổ -----
		'tmnhanphat_header_shadow'           => array(
			'label'             => __( 'Bóng đổ Header (khi Sticky)', 'tmnhanphat' ),
			'section'           => 'style',
			'choices'           => tmnhanphat_get_header_shadow_choices(),
			'default'           => $defaults['tmnhanphat_header_shadow'],
			'sanitize_callback' => 'tmnhanphat_sanitize_header_shadow',
		),
		'tmnhanphat_header_border_bottom'    => array(
			'label'             => __( 'Viền dưới Header', 'tmnhanphat' ),
			'section'           => 'style',
			'type'              => 'checkbox',
			'default'           => $defaults['tmnhanphat_header_border_bottom'],
			'sanitize_callback' => 'tmnhanphat_sanitize_checkbox',
		),
	);
}

/**
 * Whitelist "Menu Font" — không cho lưu giá trị ngoài danh sách cho phép.
 *
 * @param string $value Giá trị gửi lên từ Customizer.
 * @return string
 */
function tmnhanphat_sanitize_menu_font( $value ) {
	$choices = array_keys( tmnhanphat_get_menu_font_choices() );

	return in_array( $value, $choices, true ) ? $value : 'inherit';
}

/**
 * Whitelist "Menu Font Weight".
 *
 * @param string $value Giá trị gửi lên từ Customizer.
 * @return string
 */
function tmnhanphat_sanitize_menu_font_weight( $value ) {
	$choices = array( '400', '500', '600', '700' );

	return in_array( $value, $choices, true ) ? $value : '500';
}

/**
 * Whitelist "Header Shadow".
 *
 * @param string $value Giá trị gửi lên từ Customizer.
 * @return string
 */
function tmnhanphat_sanitize_header_shadow( $value ) {
	$choices = array_keys( tmnhanphat_get_header_shadow_choices() );

	return in_array( $value, $choices, true ) ? $value : 'soft';
}

/**
 * Đăng ký Panel + Section + toàn bộ Setting/Control của Header vào Customizer.
 *
 * @param WP_Customize_Manager $wp_customize Customizer manager instance.
 */
function tmnhanphat_header_customizer_register( $wp_customize ) {
	$wp_customize->add_panel( 'tmnhanphat_header_panel', array(
		'title'    => __( 'Header', 'tmnhanphat' ),
		'priority' => 30,
	) );

	$sections = array(
		'logo'       => __( 'Logo', 'tmnhanphat' ),
		'layout'     => __( 'Bố cục', 'tmnhanphat' ),
		'sticky'     => __( 'Sticky & Trong suốt', 'tmnhanphat' ),
		'colors'     => __( 'Màu sắc', 'tmnhanphat' ),
		'navigation' => __( 'Navigation (Menu Color)', 'tmnhanphat' ),
		'typography' => __( 'Typography Menu', 'tmnhanphat' ),
		'style'      => __( 'Viền & Bóng đổ', 'tmnhanphat' ),
	);

	foreach ( $sections as $section_id => $section_label ) {
		$wp_customize->add_section( "tmnhanphat_header_section_{$section_id}", array(
			'title' => $section_label,
			'panel' => 'tmnhanphat_header_panel',
		) );
	}

	foreach ( tmnhanphat_get_header_customizer_fields() as $field_id => $field ) {
		$wp_customize->add_setting( $field_id, array(
			'default'           => $field['default'],
			'sanitize_callback' => $field['sanitize_callback'],
			'transport'         => 'refresh',
		) );

		$control_args = array(
			'label'    => $field['label'],
			'section'  => 'tmnhanphat_header_section_' . $field['section'],
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

		$control_args['type'] = 'checkbox' === $field['type'] ? 'checkbox' : 'number';
		$wp_customize->add_control( $field_id, $control_args );
	}
}
add_action( 'customize_register', 'tmnhanphat_header_customizer_register' );
