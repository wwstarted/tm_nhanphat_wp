<?php
/**
 * Quản lý tập trung toàn bộ CSS/JS của theme — xem PROJECT_RULES.md mục 9.
 *
 * Dự án chỉ dùng PHP/HTML/CSS/JS thuần, không build tool, không thư viện ngoài.
 * Mỗi file trong assets/css, assets/js được enqueue trực tiếp bằng wp_enqueue_style()/
 * wp_enqueue_script(), có điều kiện theo loại trang hiện tại (PROJECT_RULES.md mục 7).
 *
 * @package TMNhanPhat
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Version cache-busting theo thời gian sửa file thay vì hằng số cố định,
 * để trình duyệt luôn lấy bản mới nhất khi sửa CSS/JS mà không cần đổi version thủ công.
 *
 * @param string $relative_path Đường dẫn tương đối từ theme root, ví dụ 'assets/css/global/reset.css'.
 * @return string|int
 */
function tmnhanphat_asset_version( $relative_path ) {
	$file = TMNHANPHAT_DIR . '/' . ltrim( $relative_path, '/' );

	return file_exists( $file ) ? filemtime( $file ) : TMNHANPHAT_VERSION;
}

/**
 * Enqueue 1 file CSS trong assets/css.
 *
 * @param string $handle        Handle duy nhất.
 * @param string $relative_path Đường dẫn tương đối từ theme root.
 * @param array  $deps          Handle CSS phụ thuộc (load trước).
 */
function tmnhanphat_enqueue_style_file( $handle, $relative_path, $deps = array() ) {
	wp_enqueue_style( $handle, TMNHANPHAT_URI . '/' . $relative_path, $deps, tmnhanphat_asset_version( $relative_path ) );
}

/**
 * Enqueue 1 file JS trong assets/js (luôn load ở footer).
 *
 * @param string $handle        Handle duy nhất.
 * @param string $relative_path Đường dẫn tương đối từ theme root.
 * @param array  $deps          Handle JS phụ thuộc (load trước).
 */
function tmnhanphat_enqueue_script_file( $handle, $relative_path, $deps = array() ) {
	wp_enqueue_script( $handle, TMNHANPHAT_URI . '/' . $relative_path, $deps, tmnhanphat_asset_version( $relative_path ), true );
}

/**
 * Điều phối conditional asset loading: global luôn load, CSS/JS riêng theo từng loại trang.
 */
function tmnhanphat_enqueue_assets() {
	// ----- Global: load ở MỌI trang (reset/variables/typography/helpers + layout + mobile-menu). -----
	tmnhanphat_enqueue_style_file( 'tmnhanphat-reset', 'assets/css/global/reset.css' );
	tmnhanphat_enqueue_style_file( 'tmnhanphat-variables', 'assets/css/global/variables.css' );
	tmnhanphat_enqueue_style_file( 'tmnhanphat-typography', 'assets/css/global/typography.css', array( 'tmnhanphat-variables' ) );
	tmnhanphat_enqueue_style_file( 'tmnhanphat-helpers-css', 'assets/css/global/helpers.css', array( 'tmnhanphat-variables' ) );
	tmnhanphat_enqueue_style_file( 'tmnhanphat-animation', 'assets/css/global/animation.css' );
	tmnhanphat_enqueue_style_file( 'tmnhanphat-header-css', 'assets/css/layout/header.css', array( 'tmnhanphat-variables' ) );
	tmnhanphat_enqueue_style_file( 'tmnhanphat-footer-css', 'assets/css/layout/footer.css', array( 'tmnhanphat-variables' ) );
	tmnhanphat_enqueue_style_file( 'tmnhanphat-sidebar-css', 'assets/css/layout/sidebar.css', array( 'tmnhanphat-variables' ) );

	tmnhanphat_enqueue_script_file( 'tmnhanphat-helpers', 'assets/js/global/helpers.js' );
	tmnhanphat_enqueue_script_file( 'tmnhanphat-app', 'assets/js/global/app.js' );
	tmnhanphat_enqueue_script_file( 'tmnhanphat-header-js', 'assets/js/layout/header.js', array( 'tmnhanphat-helpers' ) );
	tmnhanphat_enqueue_script_file( 'tmnhanphat-mobile-menu', 'assets/js/layout/mobile-menu.js' );

	// ----- Theo từng loại trang: chỉ load đúng 1 nhóm CSS/JS cần thiết. -----
	if ( is_front_page() ) {
		tmnhanphat_enqueue_style_file( 'tmnhanphat-hero', 'assets/css/components/hero.css' );
		tmnhanphat_enqueue_style_file( 'tmnhanphat-card', 'assets/css/components/card.css' );
		tmnhanphat_enqueue_style_file( 'tmnhanphat-front-page', 'assets/css/pages/front-page.css' );
	} elseif ( is_singular( 'post' ) ) {
		tmnhanphat_enqueue_style_file( 'tmnhanphat-breadcrumb', 'assets/css/components/breadcrumb.css' );
		tmnhanphat_enqueue_style_file( 'tmnhanphat-single', 'assets/css/pages/single.css' );
		tmnhanphat_enqueue_script_file( 'tmnhanphat-single', 'assets/js/pages/single.js' );
	} elseif ( is_archive() || is_home() ) {
		tmnhanphat_enqueue_style_file( 'tmnhanphat-card', 'assets/css/components/card.css' );
		tmnhanphat_enqueue_style_file( 'tmnhanphat-pagination', 'assets/css/components/pagination.css' );
		tmnhanphat_enqueue_style_file( 'tmnhanphat-breadcrumb', 'assets/css/components/breadcrumb.css' );
		tmnhanphat_enqueue_style_file( 'tmnhanphat-archive', 'assets/css/pages/archive.css' );
	} elseif ( is_search() ) {
		tmnhanphat_enqueue_style_file( 'tmnhanphat-card', 'assets/css/components/card.css' );
		tmnhanphat_enqueue_style_file( 'tmnhanphat-pagination', 'assets/css/components/pagination.css' );
		tmnhanphat_enqueue_style_file( 'tmnhanphat-breadcrumb', 'assets/css/components/breadcrumb.css' );
		tmnhanphat_enqueue_style_file( 'tmnhanphat-search', 'assets/css/pages/search.css' );
	} elseif ( is_page() ) {
		tmnhanphat_enqueue_style_file( 'tmnhanphat-breadcrumb', 'assets/css/components/breadcrumb.css' );
		tmnhanphat_enqueue_style_file( 'tmnhanphat-page', 'assets/css/pages/page.css' );
	}

	wp_localize_script( 'tmnhanphat-app', 'tmnhanphatData', array(
		'ajaxUrl' => admin_url( 'admin-ajax.php' ),
		'nonce'   => wp_create_nonce( 'tmnhanphat_ajax_nonce' ),
	) );
}
add_action( 'wp_enqueue_scripts', 'tmnhanphat_enqueue_assets' );
