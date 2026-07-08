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
 * Đăng ký các widget area: sidebar chính và footer.
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

	register_sidebar( array(
		'name'          => __( 'Footer', 'tmnhanphat' ),
		'id'            => 'footer-widgets',
		'description'   => __( 'Hiển thị trong footer.php.', 'tmnhanphat' ),
		'before_widget' => '<section id="%1$s" class="widget %2$s">',
		'after_widget'  => '</section>',
		'before_title'  => '<h2 class="widget-title">',
		'after_title'   => '</h2>',
	) );
}
add_action( 'widgets_init', 'tmnhanphat_register_sidebars' );
