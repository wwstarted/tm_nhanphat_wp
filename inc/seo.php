<?php
/**
 * SEO cơ bản: meta description, canonical, Open Graph, Schema — xem PROJECT_RULES.md mục 14.
 *
 * Lưu ý: nếu site cài Yoast SEO / Rank Math, các hàm output thẻ meta bên dưới
 * sẽ tự nhường quyền (kiểm tra tồn tại plugin) để tránh trùng thẻ <meta> / JSON-LD.
 *
 * @package TMNhanPhat
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Có plugin SEO nào đang active không (Yoast, Rank Math, SEOPress...).
 *
 * @return bool
 */
function tmnhanphat_has_seo_plugin() {
	return defined( 'WPSEO_VERSION' ) || defined( 'RANK_MATH_VERSION' ) || defined( 'SEOPRESS_VERSION' );
}

/**
 * In thẻ meta description + canonical khi không có plugin SEO nào xử lý việc này.
 */
function tmnhanphat_output_meta_tags() {
	if ( tmnhanphat_has_seo_plugin() ) {
		return;
	}

	$description = '';

	if ( is_singular() ) {
		$description = tmnhanphat_get_excerpt( get_the_ID(), 30 );
	} elseif ( is_archive() ) {
		$description = wp_strip_all_tags( get_the_archive_description() );
	} elseif ( is_front_page() ) {
		$description = get_bloginfo( 'description' );
	}

	if ( $description ) {
		printf( '<meta name="description" content="%s" />' . "\n", esc_attr( $description ) );
	}

	printf( '<link rel="canonical" href="%s" />' . "\n", esc_url( tmnhanphat_get_canonical_url() ) );
}
add_action( 'wp_head', 'tmnhanphat_output_meta_tags', 1 );

/**
 * Lấy canonical URL cho trang hiện tại.
 *
 * @return string
 */
function tmnhanphat_get_canonical_url() {
	if ( is_singular() ) {
		return get_permalink();
	}

	global $wp;
	return home_url( add_query_arg( array(), $wp->request ) );
}

/**
 * JSON-LD Schema cơ bản cho WebSite (chỉ in ở trang chủ, khi không có plugin SEO).
 */
function tmnhanphat_output_website_schema() {
	if ( tmnhanphat_has_seo_plugin() || ! is_front_page() ) {
		return;
	}

	$schema = array(
		'@context' => 'https://schema.org',
		'@type'    => 'WebSite',
		'name'     => get_bloginfo( 'name' ),
		'url'      => home_url( '/' ),
	);

	printf( '<script type="application/ld+json">%s</script>' . "\n", wp_json_encode( $schema ) );
}
add_action( 'wp_head', 'tmnhanphat_output_website_schema', 2 );
