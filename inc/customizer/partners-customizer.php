<?php
/**
 * Đăng ký Customizer Panel riêng "Partner Home" (không lồng trong panel "Homepage" nào —
 * WordPress Customizer không hỗ trợ panel-trong-panel, xem PROJECT_RULES.md mục 26):
 * General, Layout, Responsive, Partner 1-8. Cùng quy ước config-driven như header/footer/
 * hero-customizer.php (PROJECT_RULES.md mục 22). Đây CHỈ là tổ chức UI — không đổi key
 * setting, sanitize callback hay default value nào so với trước khi tách panel.
 *
 * @package TMNhanPhat
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Khai báo field cho 1 Partner slot (dùng lại N lần — DRY).
 *
 * @param int   $index    Số thứ tự slot (1..tmnhanphat_get_partners_slot_count()).
 * @param array $defaults Mảng defaults từ tmnhanphat_partners_defaults().
 * @return array<string, array>
 */
function tmnhanphat_get_partner_item_fields( $index, $defaults ) {
	$section = "partner_{$index}";

	return array(
		"tmnhanphat_partner{$index}_enable"  => array(
			/* translators: %d: số thứ tự đối tác (1-8) */
			'label'             => sprintf( __( 'Hiển thị Partner %d', 'tmnhanphat' ), $index ),
			'section'           => $section,
			'type'              => 'checkbox',
			'default'           => $defaults[ "tmnhanphat_partner{$index}_enable" ],
			'sanitize_callback' => 'tmnhanphat_sanitize_checkbox',
		),
		"tmnhanphat_partner{$index}_logo"    => array(
			'label'             => __( 'Logo Image', 'tmnhanphat' ),
			'section'           => $section,
			'type'              => 'image',
			'default'           => $defaults[ "tmnhanphat_partner{$index}_logo" ],
			'sanitize_callback' => 'esc_url_raw',
		),
		"tmnhanphat_partner{$index}_alt"     => array(
			'label'             => __( 'Alt Text', 'tmnhanphat' ),
			'section'           => $section,
			'type'              => 'text',
			'default'           => $defaults[ "tmnhanphat_partner{$index}_alt" ],
			'sanitize_callback' => 'sanitize_text_field',
		),
		"tmnhanphat_partner{$index}_link"    => array(
			'label'             => __( 'Link (tuỳ chọn)', 'tmnhanphat' ),
			'section'           => $section,
			'type'              => 'text',
			'default'           => $defaults[ "tmnhanphat_partner{$index}_link" ],
			'sanitize_callback' => 'esc_url_raw',
		),
		"tmnhanphat_partner{$index}_new_tab" => array(
			'label'             => __( 'Open in New Tab', 'tmnhanphat' ),
			'section'           => $section,
			'type'              => 'checkbox',
			'default'           => $defaults[ "tmnhanphat_partner{$index}_new_tab" ],
			'sanitize_callback' => 'tmnhanphat_sanitize_checkbox',
		),
		"tmnhanphat_partner{$index}_order"   => array(
			'label'             => __( 'Display Order', 'tmnhanphat' ),
			'section'           => $section,
			'type'              => 'number',
			'default'           => $defaults[ "tmnhanphat_partner{$index}_order" ],
			'sanitize_callback' => 'absint',
		),
	);
}

/**
 * Khai báo toàn bộ field của Partners Section dạng config (id => định nghĩa).
 *
 * @return array<string, array>
 */
function tmnhanphat_get_partners_customizer_fields() {
	$defaults = tmnhanphat_partners_defaults();

	$fields = array(
		// ----- General -----
		'tmnhanphat_partners_enable'             => array(
			'label'             => __( 'Enable Section', 'tmnhanphat' ),
			'section'           => 'general',
			'type'              => 'checkbox',
			'default'           => $defaults['tmnhanphat_partners_enable'],
			'sanitize_callback' => 'tmnhanphat_sanitize_checkbox',
		),
		'tmnhanphat_partners_title_enable'       => array(
			'label'             => __( 'Enable Section Title', 'tmnhanphat' ),
			'section'           => 'general',
			'type'              => 'checkbox',
			'default'           => $defaults['tmnhanphat_partners_title_enable'],
			'sanitize_callback' => 'tmnhanphat_sanitize_checkbox',
		),
		'tmnhanphat_partners_title'               => array(
			'label'             => __( 'Section Title', 'tmnhanphat' ),
			'section'           => 'general',
			'type'              => 'text',
			'default'           => $defaults['tmnhanphat_partners_title'],
			'sanitize_callback' => 'sanitize_text_field',
		),
		'tmnhanphat_partners_description_enable' => array(
			'label'             => __( 'Enable Section Description', 'tmnhanphat' ),
			'section'           => 'general',
			'type'              => 'checkbox',
			'default'           => $defaults['tmnhanphat_partners_description_enable'],
			'sanitize_callback' => 'tmnhanphat_sanitize_checkbox',
		),
		'tmnhanphat_partners_description'        => array(
			'label'             => __( 'Section Description', 'tmnhanphat' ),
			'section'           => 'general',
			'type'              => 'text',
			'default'           => $defaults['tmnhanphat_partners_description'],
			'sanitize_callback' => 'sanitize_textarea_field',
		),
		'tmnhanphat_partners_padding_desktop'    => array(
			'label'             => __( 'Section Padding Desktop (px)', 'tmnhanphat' ),
			'section'           => 'responsive',
			'type'              => 'number',
			'default'           => $defaults['tmnhanphat_partners_padding_desktop'],
			'sanitize_callback' => 'absint',
		),
		'tmnhanphat_partners_padding_tablet'     => array(
			'label'             => __( 'Section Padding Tablet (px)', 'tmnhanphat' ),
			'section'           => 'responsive',
			'type'              => 'number',
			'default'           => $defaults['tmnhanphat_partners_padding_tablet'],
			'sanitize_callback' => 'absint',
		),
		'tmnhanphat_partners_padding_mobile'     => array(
			'label'             => __( 'Section Padding Mobile (px)', 'tmnhanphat' ),
			'section'           => 'responsive',
			'type'              => 'number',
			'default'           => $defaults['tmnhanphat_partners_padding_mobile'],
			'sanitize_callback' => 'absint',
		),
		'tmnhanphat_partners_bg'                  => array(
			'label'             => __( 'Background Color', 'tmnhanphat' ),
			'section'           => 'general',
			'type'              => 'color',
			'default'           => $defaults['tmnhanphat_partners_bg'],
			'sanitize_callback' => 'sanitize_hex_color',
		),

		// Số lượng Partner được RENDER — hoàn toàn không liên quan tới số cột (Layout bên dưới).
		'tmnhanphat_partners_display_limit'   => array(
			'label'             => __( 'Number of Partners', 'tmnhanphat' ),
			'section'           => 'general',
			'type'              => 'number',
			'default'           => $defaults['tmnhanphat_partners_display_limit'],
			'sanitize_callback' => 'absint',
			'input_attrs'       => array( 'min' => 1, 'max' => tmnhanphat_get_partners_slot_count(), 'step' => 1 ),
		),

		// ----- Layout (chỉ số cột — KHÔNG quyết định số Partner) -----
		'tmnhanphat_partners_columns_desktop' => array(
			'label'             => __( 'Desktop Columns', 'tmnhanphat' ),
			'section'           => 'layout',
			'choices'           => tmnhanphat_get_partners_column_choices(),
			'default'           => (string) $defaults['tmnhanphat_partners_columns_desktop'],
			'sanitize_callback' => 'tmnhanphat_sanitize_partners_columns',
		),
		'tmnhanphat_partners_columns_tablet'  => array(
			'label'             => __( 'Tablet Columns', 'tmnhanphat' ),
			'section'           => 'layout',
			'choices'           => tmnhanphat_get_partners_tablet_column_choices(),
			'default'           => (string) $defaults['tmnhanphat_partners_columns_tablet'],
			'sanitize_callback' => 'tmnhanphat_sanitize_partners_tablet_columns',
		),
		'tmnhanphat_partners_columns_mobile'  => array(
			'label'             => __( 'Mobile Columns', 'tmnhanphat' ),
			'section'           => 'layout',
			'choices'           => tmnhanphat_get_partners_mobile_column_choices(),
			'default'           => (string) $defaults['tmnhanphat_partners_columns_mobile'],
			'sanitize_callback' => 'tmnhanphat_sanitize_partners_mobile_columns',
		),
		'tmnhanphat_partners_gap_desktop'     => array(
			'label'             => __( 'Gap Desktop (px)', 'tmnhanphat' ),
			'section'           => 'layout',
			'type'              => 'number',
			'default'           => $defaults['tmnhanphat_partners_gap_desktop'],
			'sanitize_callback' => 'absint',
		),
		'tmnhanphat_partners_gap_tablet'      => array(
			'label'             => __( 'Gap Tablet (px)', 'tmnhanphat' ),
			'section'           => 'layout',
			'type'              => 'number',
			'default'           => $defaults['tmnhanphat_partners_gap_tablet'],
			'sanitize_callback' => 'absint',
		),
		'tmnhanphat_partners_gap_mobile'      => array(
			'label'             => __( 'Gap Mobile (px)', 'tmnhanphat' ),
			'section'           => 'layout',
			'type'              => 'number',
			'default'           => $defaults['tmnhanphat_partners_gap_mobile'],
			'sanitize_callback' => 'absint',
		),
		'tmnhanphat_partners_card_radius'     => array(
			'label'             => __( 'Card Border Radius (px)', 'tmnhanphat' ),
			'section'           => 'layout',
			'type'              => 'number',
			'default'           => $defaults['tmnhanphat_partners_card_radius'],
			'sanitize_callback' => 'absint',
		),
		'tmnhanphat_partners_card_padding'    => array(
			'label'             => __( 'Card Padding (px)', 'tmnhanphat' ),
			'section'           => 'layout',
			'type'              => 'number',
			'default'           => $defaults['tmnhanphat_partners_card_padding'],
			'sanitize_callback' => 'absint',
		),
		'tmnhanphat_partners_card_border'     => array(
			'label'             => __( 'Card Border Color', 'tmnhanphat' ),
			'section'           => 'layout',
			'type'              => 'color',
			'default'           => $defaults['tmnhanphat_partners_card_border'],
			'sanitize_callback' => 'sanitize_hex_color',
		),
		'tmnhanphat_partners_hover_enable'    => array(
			'label'             => __( 'Hover Effect Enable', 'tmnhanphat' ),
			'section'           => 'layout',
			'type'              => 'checkbox',
			'default'           => $defaults['tmnhanphat_partners_hover_enable'],
			'sanitize_callback' => 'tmnhanphat_sanitize_checkbox',
		),
	);

	for ( $i = 1; $i <= tmnhanphat_get_partners_slot_count(); $i++ ) {
		$fields = array_merge( $fields, tmnhanphat_get_partner_item_fields( $i, $defaults ) );
	}

	return $fields;
}

/**
 * Whitelist "Number of Columns (Desktop)".
 *
 * @param string $value Giá trị gửi lên từ Customizer.
 * @return string
 */
function tmnhanphat_sanitize_partners_columns( $value ) {
	$choices = array_keys( tmnhanphat_get_partners_column_choices() );

	return in_array( $value, $choices, true ) ? $value : '6';
}

/**
 * Whitelist "Tablet Columns".
 *
 * @param string $value Giá trị gửi lên từ Customizer.
 * @return string
 */
function tmnhanphat_sanitize_partners_tablet_columns( $value ) {
	$choices = array_keys( tmnhanphat_get_partners_tablet_column_choices() );

	return in_array( $value, $choices, true ) ? $value : '3';
}

/**
 * Whitelist "Mobile Columns".
 *
 * @param string $value Giá trị gửi lên từ Customizer.
 * @return string
 */
function tmnhanphat_sanitize_partners_mobile_columns( $value ) {
	$choices = array_keys( tmnhanphat_get_partners_mobile_column_choices() );

	return in_array( $value, $choices, true ) ? $value : '2';
}

/**
 * Đăng ký Panel riêng "Partner Home" + toàn bộ Section/Setting/Control của Partners.
 *
 * Mỗi feature Homepage (Hero/Partner/...) có Panel RIÊNG ở cấp cao nhất của Customizer,
 * KHÔNG lồng chung trong 1 panel "Homepage" — vì WordPress Customizer core không hỗ trợ
 * panel-trong-panel (PROJECT_RULES.md mục 26). Priority nối tiếp ngay sau Hero Home (30)
 * để 2 Panel nằm cạnh nhau trong danh sách gốc.
 *
 * @param WP_Customize_Manager $wp_customize Customizer manager instance.
 */
function tmnhanphat_partners_customizer_register( $wp_customize ) {
	$wp_customize->add_panel( 'tmnhanphat_partner_panel', array(
		'title'    => __( 'Partner Home', 'tmnhanphat' ),
		'priority' => 31,
	) );

	$sections = array(
		'general'    => __( 'General', 'tmnhanphat' ),
		'layout'     => __( 'Layout', 'tmnhanphat' ),
		'responsive' => __( 'Responsive', 'tmnhanphat' ),
	);

	for ( $i = 1; $i <= tmnhanphat_get_partners_slot_count(); $i++ ) {
		/* translators: %d: số thứ tự đối tác (1-8) */
		$sections[ "partner_{$i}" ] = sprintf( __( 'Partner %d', 'tmnhanphat' ), $i );
	}

	foreach ( $sections as $section_id => $section_label ) {
		$wp_customize->add_section( "tmnhanphat_partners_section_{$section_id}", array(
			'title' => $section_label,
			'panel' => 'tmnhanphat_partner_panel',
		) );
	}

	foreach ( tmnhanphat_get_partners_customizer_fields() as $field_id => $field ) {
		$wp_customize->add_setting( $field_id, array(
			'default'           => $field['default'],
			'sanitize_callback' => $field['sanitize_callback'],
			'transport'         => 'refresh',
		) );

		$control_args = array(
			'label'    => $field['label'],
			'section'  => 'tmnhanphat_partners_section_' . $field['section'],
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
add_action( 'customize_register', 'tmnhanphat_partners_customizer_register' );
