<?php
/**
 * Homepage Section Manager (Customize → panel "Homepage Sections") — bật/tắt và đổi
 * thứ tự các section trang chủ mà không cần sửa code (PROJECT_RULES.md mục 22/26).
 *
 * Core Customizer KHÔNG hỗ trợ kéo-thả reorder sẵn (cần custom JS control phức tạp),
 * nên dùng "Order Number" cho từng section (spec cho phép fallback này). front-page.php
 * đọc tmnhanphat_get_ordered_home_sections() để render đúng thứ tự sau khi Save.
 *
 * Mặc định: mọi section Enable = true, Order = vị trí registry (1..12) → giao diện
 * KHÔNG đổi nếu admin chưa chỉnh (mục XIV).
 *
 * @package TMNhanPhat
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Đăng ký Panel "Homepage Sections" + 1 Section chứa Enable/Order của từng section.
 * Priority 6: ngay sau "Global Settings" (5), trước các panel section (30+).
 *
 * @param WP_Customize_Manager $wp_customize Customizer manager instance.
 */
function tmnhanphat_section_manager_customizer_register( $wp_customize ) {
	$wp_customize->add_panel( 'tmnhanphat_section_manager_panel', array(
		'title'       => __( 'Homepage Sections', 'tmnhanphat' ),
		'description' => __( 'Bật/tắt và đổi thứ tự các section trang chủ. Order nhỏ hiển thị trước. Mỗi section vẫn có thiết lập chi tiết riêng ở panel tương ứng.', 'tmnhanphat' ),
		'priority'    => 6,
	) );

	$wp_customize->add_section( 'tmnhanphat_section_manager_section', array(
		'title' => __( 'Sections', 'tmnhanphat' ),
		'panel' => 'tmnhanphat_section_manager_panel',
	) );

	$sections = tmnhanphat_get_home_sections();

	foreach ( $sections as $index => $section ) {
		$order_default = $index + 1;
		$enable_id     = tmnhanphat_home_section_setting_id( $section['slug'], 'enable' );
		$order_id      = tmnhanphat_home_section_setting_id( $section['slug'], 'order' );

		// Enable.
		$wp_customize->add_setting( $enable_id, array(
			'default'           => true,
			'sanitize_callback' => 'tmnhanphat_sanitize_checkbox',
			'transport'         => 'refresh',
		) );
		$wp_customize->add_control( $enable_id, array(
			/* translators: 1: số thứ tự, 2: tên section */
			'label'    => sprintf( __( '%1$02d. %2$s — Hiển thị', 'tmnhanphat' ), $order_default, $section['label'] ),
			'section'  => 'tmnhanphat_section_manager_section',
			'type'     => 'checkbox',
			'settings' => $enable_id,
			'priority' => ( $index * 10 ) + 1,
		) );

		// Order.
		$wp_customize->add_setting( $order_id, array(
			'default'           => $order_default,
			'sanitize_callback' => 'absint',
			'transport'         => 'refresh',
		) );
		$wp_customize->add_control( $order_id, array(
			/* translators: %s: tên section */
			'label'       => sprintf( __( '↳ %s — Thứ tự (Order)', 'tmnhanphat' ), $section['label'] ),
			'section'     => 'tmnhanphat_section_manager_section',
			'type'        => 'number',
			'settings'    => $order_id,
			'input_attrs' => array( 'min' => 1, 'max' => 99, 'step' => 1 ),
			'priority'    => ( $index * 10 ) + 2,
		) );
	}
}
add_action( 'customize_register', 'tmnhanphat_section_manager_customizer_register' );
