<?php
/**
 * Tuỳ chỉnh REST API của theme (đăng ký field bổ sung khi cần) — xem PROJECT_RULES.md mục 17.
 *
 * @package TMNhanPhat
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Đăng ký field "reading_time" cho REST response của post, phục vụ headless/AJAX.
 */
function tmnhanphat_register_rest_fields() {
	register_rest_field( 'post', 'reading_time', array(
		'get_callback' => function ( $post ) {
			return tmnhanphat_reading_time( $post['id'] );
		},
		'schema'       => array(
			'description' => __( 'Thời gian đọc ước tính (phút).', 'tmnhanphat' ),
			'type'        => 'integer',
			'context'     => array( 'view' ),
		),
	) );
}
add_action( 'rest_api_init', 'tmnhanphat_register_rest_fields' );
