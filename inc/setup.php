<?php
/**
 * Theme setup: theme supports, content width, text domain.
 *
 * @package TMNhanPhat
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Đăng ký các tính năng theme (theme supports) và load text domain.
 */
function tmnhanphat_setup() {
	load_theme_textdomain( 'tmnhanphat', TMNHANPHAT_DIR . '/languages' );

	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'automatic-feed-links' );
	add_theme_support( 'html5', array( 'search-form', 'comment-form', 'comment-list', 'gallery', 'caption', 'style', 'script', 'navigation-widgets' ) );
	add_theme_support( 'custom-logo', array(
		'height'      => 80,
		'width'       => 200,
		'flex-height' => true,
		'flex-width'  => true,
	) );
	add_theme_support( 'responsive-embeds' );
	add_theme_support( 'align-wide' );
	add_theme_support( 'editor-styles' );
	add_theme_support( 'wp-block-styles' );

	// SOLID/DRY: content width dùng chung cho embed & oEmbed, khớp với biến CSS --container-max-width.
	$GLOBALS['content_width'] = apply_filters( 'tmnhanphat_content_width', 1200 );
}
add_action( 'after_setup_theme', 'tmnhanphat_setup' );
