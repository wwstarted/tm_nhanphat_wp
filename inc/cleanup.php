<?php
/**
 * Dọn dẹp <head> và các output mặc định không cần thiết của WordPress core.
 *
 * @package TMNhanPhat
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Gỡ các thẻ <link> mặc định không dùng tới trong <head>.
 */
function tmnhanphat_cleanup_head() {
	remove_action( 'wp_head', 'rsd_link' );
	remove_action( 'wp_head', 'wlwmanifest_link' );
	remove_action( 'wp_head', 'wp_shortlink_wp_head' );
	remove_action( 'wp_head', 'adjacent_posts_rel_link_wp_head' );
	remove_action( 'wp_head', 'feed_links_extra', 3 );
}
add_action( 'init', 'tmnhanphat_cleanup_head' );

/**
 * Tắt emoji script/style mặc định (giảm request không cần thiết — Core Web Vitals).
 */
function tmnhanphat_disable_emojis() {
	remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
	remove_action( 'wp_print_styles', 'print_emoji_styles' );
	remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
	remove_action( 'admin_print_styles', 'print_emoji_styles' );
	remove_filter( 'the_content_feed', 'wp_staticize_emoji' );
	remove_filter( 'comment_text_rss', 'wp_staticize_emoji' );
	remove_filter( 'wp_mail', 'wp_staticize_emoji_for_email' );
}
add_action( 'init', 'tmnhanphat_disable_emojis' );

/**
 * Gỡ block-library CSS mặc định khi không dùng Block Editor cho layout chính
 * (theme tự quản lý CSS trong assets/css — xem PROJECT_RULES.md mục 6).
 */
function tmnhanphat_dequeue_block_library_css() {
	wp_dequeue_style( 'wp-block-library' );
	wp_dequeue_style( 'wp-block-library-theme' );
	wp_dequeue_style( 'classic-theme-styles' );
}
add_action( 'wp_enqueue_scripts', 'tmnhanphat_dequeue_block_library_css', 100 );
