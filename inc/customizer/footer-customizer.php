<?php
/**
 * Đăng ký Customizer Panel "Footer": Màu sắc, Bottom Bar, Logo & Công ty, Liên hệ,
 * Mạng xã hội, Bố cục. Cùng quy ước config-driven như header-customizer.php
 * (PROJECT_RULES.md mục 22) — không đụng tới bất kỳ setting/hàm nào của Header.
 *
 * @package TMNhanPhat
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Khai báo toàn bộ field của Footer Panel dạng config (id => định nghĩa).
 *
 * @return array<string, array>
 */
function tmnhanphat_get_footer_customizer_fields() {
	$defaults = tmnhanphat_footer_defaults();

	return array(
		// ----- Màu sắc -----
		'tmnhanphat_footer_bg'            => array(
			'label'             => __( 'Footer Background', 'tmnhanphat' ),
			'section'           => 'colors',
			'type'              => 'color',
			'default'           => $defaults['tmnhanphat_footer_bg'],
			'sanitize_callback' => 'sanitize_hex_color',
		),
		'tmnhanphat_footer_text'          => array(
			'label'             => __( 'Footer Text Color', 'tmnhanphat' ),
			'section'           => 'colors',
			'type'              => 'color',
			'default'           => $defaults['tmnhanphat_footer_text'],
			'sanitize_callback' => 'sanitize_hex_color',
		),
		'tmnhanphat_footer_link'          => array(
			'label'             => __( 'Footer Link Color', 'tmnhanphat' ),
			'section'           => 'colors',
			'type'              => 'color',
			'default'           => $defaults['tmnhanphat_footer_link'],
			'sanitize_callback' => 'sanitize_hex_color',
		),
		'tmnhanphat_footer_link_hover'    => array(
			'label'             => __( 'Footer Hover Color', 'tmnhanphat' ),
			'section'           => 'colors',
			'type'              => 'color',
			'default'           => $defaults['tmnhanphat_footer_link_hover'],
			'sanitize_callback' => 'sanitize_hex_color',
		),
		'tmnhanphat_footer_heading_color' => array(
			'label'             => __( 'Heading Color', 'tmnhanphat' ),
			'section'           => 'colors',
			'type'              => 'color',
			'default'           => $defaults['tmnhanphat_footer_heading_color'],
			'sanitize_callback' => 'sanitize_hex_color',
		),
		'tmnhanphat_footer_divider_color' => array(
			'label'             => __( 'Divider Color', 'tmnhanphat' ),
			'section'           => 'colors',
			'type'              => 'color',
			'default'           => $defaults['tmnhanphat_footer_divider_color'],
			'sanitize_callback' => 'sanitize_hex_color',
		),

		// ----- Bottom Footer -----
		'tmnhanphat_footer_bottom_bg'     => array(
			'label'             => __( 'Bottom Background', 'tmnhanphat' ),
			'section'           => 'bottom',
			'type'              => 'color',
			'default'           => $defaults['tmnhanphat_footer_bottom_bg'],
			'sanitize_callback' => 'sanitize_hex_color',
		),
		'tmnhanphat_footer_bottom_text'   => array(
			'label'             => __( 'Bottom Text Color', 'tmnhanphat' ),
			'section'           => 'bottom',
			'type'              => 'color',
			'default'           => $defaults['tmnhanphat_footer_bottom_text'],
			'sanitize_callback' => 'sanitize_hex_color',
		),
		'tmnhanphat_footer_copyright'     => array(
			'label'             => __( 'Copyright Text', 'tmnhanphat' ),
			'section'           => 'bottom',
			'type'              => 'text',
			'default'           => $defaults['tmnhanphat_footer_copyright'],
			'sanitize_callback' => 'sanitize_text_field',
		),

		// ----- Logo & Công ty -----
		'tmnhanphat_footer_logo'          => array(
			'label'             => __( 'Logo', 'tmnhanphat' ),
			'section'           => 'branding',
			'type'              => 'image',
			'default'           => $defaults['tmnhanphat_footer_logo'],
			'sanitize_callback' => 'esc_url_raw',
		),
		'tmnhanphat_footer_logo_width'    => array(
			'label'             => __( 'Logo Width (px)', 'tmnhanphat' ),
			'section'           => 'branding',
			'type'              => 'number',
			'default'           => $defaults['tmnhanphat_footer_logo_width'],
			'sanitize_callback' => 'absint',
		),
		'tmnhanphat_footer_company_name'  => array(
			'label'             => __( 'Company Name (tiêu đề cột menu công ty)', 'tmnhanphat' ),
			'section'           => 'branding',
			'type'              => 'text',
			'default'           => $defaults['tmnhanphat_footer_company_name'],
			'sanitize_callback' => 'sanitize_text_field',
		),

		// ----- Liên hệ -----
		'tmnhanphat_footer_email'         => array(
			'label'             => __( 'Email Value', 'tmnhanphat' ),
			'section'           => 'contact',
			'type'              => 'text',
			'default'           => $defaults['tmnhanphat_footer_email'],
			'sanitize_callback' => 'sanitize_email',
		),
		'tmnhanphat_footer_email_label'   => array(
			'label'             => __( 'Email Label', 'tmnhanphat' ),
			'section'           => 'contact',
			'type'              => 'text',
			'default'           => $defaults['tmnhanphat_footer_email_label'],
			'sanitize_callback' => 'sanitize_text_field',
		),
		'tmnhanphat_footer_email_enable'  => array(
			'label'             => __( 'Hiển thị Email', 'tmnhanphat' ),
			'section'           => 'contact',
			'type'              => 'checkbox',
			'default'           => $defaults['tmnhanphat_footer_email_enable'],
			'sanitize_callback' => 'tmnhanphat_sanitize_checkbox',
		),
		'tmnhanphat_footer_phone'         => array(
			'label'             => __( 'Phone Value', 'tmnhanphat' ),
			'section'           => 'contact',
			'type'              => 'text',
			'default'           => $defaults['tmnhanphat_footer_phone'],
			'sanitize_callback' => 'sanitize_text_field',
		),
		'tmnhanphat_footer_phone_label'   => array(
			'label'             => __( 'Phone Label', 'tmnhanphat' ),
			'section'           => 'contact',
			'type'              => 'text',
			'default'           => $defaults['tmnhanphat_footer_phone_label'],
			'sanitize_callback' => 'sanitize_text_field',
		),
		'tmnhanphat_footer_phone_enable'  => array(
			'label'             => __( 'Hiển thị Phone', 'tmnhanphat' ),
			'section'           => 'contact',
			'type'              => 'checkbox',
			'default'           => $defaults['tmnhanphat_footer_phone_enable'],
			'sanitize_callback' => 'tmnhanphat_sanitize_checkbox',
		),
		'tmnhanphat_footer_address'       => array(
			'label'             => __( 'Address Value', 'tmnhanphat' ),
			'section'           => 'contact',
			'type'              => 'text',
			'default'           => $defaults['tmnhanphat_footer_address'],
			'sanitize_callback' => 'sanitize_text_field',
		),
		'tmnhanphat_footer_address_label' => array(
			'label'             => __( 'Address Label', 'tmnhanphat' ),
			'section'           => 'contact',
			'type'              => 'text',
			'default'           => $defaults['tmnhanphat_footer_address_label'],
			'sanitize_callback' => 'sanitize_text_field',
		),
		'tmnhanphat_footer_address_enable' => array(
			'label'             => __( 'Hiển thị Address', 'tmnhanphat' ),
			'section'           => 'contact',
			'type'              => 'checkbox',
			'default'           => $defaults['tmnhanphat_footer_address_enable'],
			'sanitize_callback' => 'tmnhanphat_sanitize_checkbox',
		),

		// ----- Mạng xã hội -----
		'tmnhanphat_footer_facebook'      => array(
			'label'             => __( 'Facebook URL', 'tmnhanphat' ),
			'section'           => 'social',
			'type'              => 'text',
			'default'           => $defaults['tmnhanphat_footer_facebook'],
			'sanitize_callback' => 'esc_url_raw',
		),
		'tmnhanphat_footer_youtube'       => array(
			'label'             => __( 'Youtube URL', 'tmnhanphat' ),
			'section'           => 'social',
			'type'              => 'text',
			'default'           => $defaults['tmnhanphat_footer_youtube'],
			'sanitize_callback' => 'esc_url_raw',
		),
		'tmnhanphat_footer_zalo'          => array(
			'label'             => __( 'Zalo URL', 'tmnhanphat' ),
			'section'           => 'social',
			'type'              => 'text',
			'default'           => $defaults['tmnhanphat_footer_zalo'],
			'sanitize_callback' => 'esc_url_raw',
		),
		'tmnhanphat_footer_tiktok'        => array(
			'label'             => __( 'Tiktok URL', 'tmnhanphat' ),
			'section'           => 'social',
			'type'              => 'text',
			'default'           => $defaults['tmnhanphat_footer_tiktok'],
			'sanitize_callback' => 'esc_url_raw',
		),
		'tmnhanphat_footer_linkedin'      => array(
			'label'             => __( 'LinkedIn URL', 'tmnhanphat' ),
			'section'           => 'social',
			'type'              => 'text',
			'default'           => $defaults['tmnhanphat_footer_linkedin'],
			'sanitize_callback' => 'esc_url_raw',
		),
		'tmnhanphat_footer_social_enable' => array(
			'label'             => __( 'Hiển thị Social', 'tmnhanphat' ),
			'section'           => 'social',
			'type'              => 'checkbox',
			'default'           => $defaults['tmnhanphat_footer_social_enable'],
			'sanitize_callback' => 'tmnhanphat_sanitize_checkbox',
		),

		// ----- Bố cục -----
		'tmnhanphat_footer_padding'          => array(
			'label'             => __( 'Footer Padding (px)', 'tmnhanphat' ),
			'section'           => 'layout',
			'type'              => 'number',
			'default'           => $defaults['tmnhanphat_footer_padding'],
			'sanitize_callback' => 'absint',
		),
		'tmnhanphat_footer_container_width' => array(
			'label'             => __( 'Container Width (px)', 'tmnhanphat' ),
			'section'           => 'layout',
			'type'              => 'number',
			'default'           => $defaults['tmnhanphat_footer_container_width'],
			'sanitize_callback' => 'absint',
		),
		'tmnhanphat_footer_logo_spacing'    => array(
			'label'             => __( 'Footer Logo Bottom Spacing (px)', 'tmnhanphat' ),
			'section'           => 'layout',
			'type'              => 'number',
			'default'           => $defaults['tmnhanphat_footer_logo_spacing'],
			'sanitize_callback' => 'absint',
			'input_attrs'       => array( 'min' => 0, 'max' => 80, 'step' => 1 ),
		),
		'tmnhanphat_footer_column_gap'      => array(
			'label'             => __( 'Footer Column Gap (px)', 'tmnhanphat' ),
			'section'           => 'layout',
			'type'              => 'number',
			'default'           => $defaults['tmnhanphat_footer_column_gap'],
			'sanitize_callback' => 'absint',
			'input_attrs'       => array( 'min' => 20, 'max' => 80, 'step' => 1 ),
		),
	);
}

/**
 * Đăng ký Panel + Section + toàn bộ Setting/Control của Footer vào Customizer.
 *
 * @param WP_Customize_Manager $wp_customize Customizer manager instance.
 */
function tmnhanphat_footer_customizer_register( $wp_customize ) {
	$wp_customize->add_panel( 'tmnhanphat_footer_panel', array(
		'title'    => __( 'Footer', 'tmnhanphat' ),
		'priority' => 35,
	) );

	$sections = array(
		'colors'   => __( 'Màu sắc', 'tmnhanphat' ),
		'bottom'   => __( 'Bottom Footer', 'tmnhanphat' ),
		'branding' => __( 'Logo & Công ty', 'tmnhanphat' ),
		'contact'  => __( 'Liên hệ', 'tmnhanphat' ),
		'social'   => __( 'Mạng xã hội', 'tmnhanphat' ),
		'layout'   => __( 'Bố cục', 'tmnhanphat' ),
	);

	foreach ( $sections as $section_id => $section_label ) {
		$wp_customize->add_section( "tmnhanphat_footer_section_{$section_id}", array(
			'title' => $section_label,
			'panel' => 'tmnhanphat_footer_panel',
		) );
	}

	foreach ( tmnhanphat_get_footer_customizer_fields() as $field_id => $field ) {
		$wp_customize->add_setting( $field_id, array(
			'default'           => $field['default'],
			'sanitize_callback' => $field['sanitize_callback'],
			'transport'         => 'refresh',
		) );

		$control_args = array(
			'label'    => $field['label'],
			'section'  => 'tmnhanphat_footer_section_' . $field['section'],
			'settings' => $field_id,
		);

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
add_action( 'customize_register', 'tmnhanphat_footer_customizer_register' );
