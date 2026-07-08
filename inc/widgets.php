<?php
/**
 * Đăng ký sidebar / widget areas.
 *
 * @package TMNhanPhat
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Đăng ký widget area sidebar chính (single/archive). Footer KHÔNG dùng widget area —
 * Top Footer là bố cục cố định 4 cột điều khiển qua Customizer + WordPress Menu
 * (xem template-parts/footer/*.php, inc/customizer/footer-customizer.php).
 */
function tmnhanphat_register_sidebars() {
	register_sidebar( array(
		'name'          => __( 'Sidebar chính', 'tmnhanphat' ),
		'id'            => 'sidebar-primary',
		'description'   => __( 'Hiển thị ở single/archive khi layout có sidebar.', 'tmnhanphat' ),
		'before_widget' => '<section id="%1$s" class="widget %2$s">',
		'after_widget'  => '</section>',
		'before_title'  => '<h2 class="widget-title">',
		'after_title'   => '</h2>',
	) );
}
add_action( 'widgets_init', 'tmnhanphat_register_sidebars' );
