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
	// CSS custom property --container-padding (Desktop/Tablet/Mobile) theo Customizer panel
	// "Global Settings" → Layout (mục 27) — nguồn DUY NHẤT cho khoảng cách 2 bên toàn site.
	// Gắn ngay sau variables.css, TRƯỚC mọi module khác, để Header/Hero/Partner/Footer/
	// .container (Archive/Single/Page...) phía sau đều tự động dùng đúng giá trị mới.
	wp_add_inline_style( 'tmnhanphat-variables', tmnhanphat_render_global_css_vars() );
	tmnhanphat_enqueue_style_file( 'tmnhanphat-typography', 'assets/css/global/typography.css', array( 'tmnhanphat-variables' ) );
	tmnhanphat_enqueue_style_file( 'tmnhanphat-helpers-css', 'assets/css/global/helpers.css', array( 'tmnhanphat-variables' ) );
	tmnhanphat_enqueue_style_file( 'tmnhanphat-animation', 'assets/css/global/animation.css' );
	tmnhanphat_enqueue_style_file( 'tmnhanphat-header-css', 'assets/css/layout/header.css', array( 'tmnhanphat-variables' ) );
	// CSS custom properties + media query theo Customizer panel "Header" (mục 22) — gắn ngay
	// sau stylesheet header để override đúng giá trị mặc định của var(--header-*, ...).
	wp_add_inline_style( 'tmnhanphat-header-css', tmnhanphat_render_header_css_vars() );
	tmnhanphat_enqueue_style_file( 'tmnhanphat-footer-css', 'assets/css/layout/footer.css', array( 'tmnhanphat-variables' ) );
	// CSS custom properties theo Customizer panel "Footer" (mục 22) — gắn ngay sau stylesheet footer.
	wp_add_inline_style( 'tmnhanphat-footer-css', tmnhanphat_render_footer_css_vars() );
	tmnhanphat_enqueue_style_file( 'tmnhanphat-sidebar-css', 'assets/css/layout/sidebar.css', array( 'tmnhanphat-variables' ) );

	tmnhanphat_enqueue_script_file( 'tmnhanphat-helpers', 'assets/js/global/helpers.js' );
	tmnhanphat_enqueue_script_file( 'tmnhanphat-app', 'assets/js/global/app.js' );
	tmnhanphat_enqueue_script_file( 'tmnhanphat-header-js', 'assets/js/layout/header.js', array( 'tmnhanphat-helpers' ) );
	tmnhanphat_enqueue_script_file( 'tmnhanphat-mobile-menu', 'assets/js/layout/mobile-menu.js', array( 'tmnhanphat-helpers' ) );

	// ----- Theo từng loại trang: chỉ load đúng 1 nhóm CSS/JS cần thiết. -----
	if ( is_front_page() ) {
		tmnhanphat_enqueue_style_file( 'tmnhanphat-hero', 'assets/css/components/hero.css' );
		// CSS custom properties theo Customizer panel "Homepage" → Hero (mục 22) — chỉ trang chủ.
		wp_add_inline_style( 'tmnhanphat-hero', tmnhanphat_render_hero_css_vars() );
		tmnhanphat_enqueue_style_file( 'tmnhanphat-card', 'assets/css/components/card.css' );
		tmnhanphat_enqueue_style_file( 'tmnhanphat-partners', 'assets/css/components/partners.css' );
		// CSS custom properties theo Customizer panel "Homepage" → Partners (mục 22) — chỉ trang chủ.
		wp_add_inline_style( 'tmnhanphat-partners', tmnhanphat_render_partners_css_vars() );
		tmnhanphat_enqueue_style_file( 'tmnhanphat-about', 'assets/css/components/about.css' );
		// CSS custom properties theo Customizer panel "About Home" (mục 22) — chỉ trang chủ.
		wp_add_inline_style( 'tmnhanphat-about', tmnhanphat_render_about_css_vars() );
		tmnhanphat_enqueue_style_file( 'tmnhanphat-services', 'assets/css/components/services.css' );
		// CSS custom properties theo Customizer panel "Service Home" (mục 22) — chỉ trang chủ.
		wp_add_inline_style( 'tmnhanphat-services', tmnhanphat_render_services_css_vars() );
		tmnhanphat_enqueue_style_file( 'tmnhanphat-products', 'assets/css/components/products.css' );
		// CSS custom properties theo Customizer panel "Product Home" (mục 22) — chỉ trang chủ.
		wp_add_inline_style( 'tmnhanphat-products', tmnhanphat_render_products_css_vars() );
		// Why Choose Section — phụ thuộc services.css vì base .tmnp-slider (viewport/track/
		// is-enhanced) định nghĩa ở đó, file này chỉ override theo scope .why-choose-home.
		tmnhanphat_enqueue_style_file( 'tmnhanphat-why-choose', 'assets/css/components/why-choose.css', array( 'tmnhanphat-services' ) );
		// CSS custom properties theo Customizer panel "Why Choose Home" (mục 22) — chỉ trang chủ.
		wp_add_inline_style( 'tmnhanphat-why-choose', tmnhanphat_render_why_choose_css_vars() );
		tmnhanphat_enqueue_style_file( 'tmnhanphat-process', 'assets/css/components/process.css' );
		// CSS custom properties theo Customizer panel "Process Home" (mục 22) — chỉ trang chủ.
		wp_add_inline_style( 'tmnhanphat-process', tmnhanphat_render_process_css_vars() );
		// Projects Section — phụ thuộc services.css vì base .tmnp-slider (viewport/track/
		// dots/is-enhanced) định nghĩa ở đó, file này chỉ override theo scope .projects-home.
		tmnhanphat_enqueue_style_file( 'tmnhanphat-projects', 'assets/css/components/projects.css', array( 'tmnhanphat-services' ) );
		// CSS custom properties theo Customizer panel "Projects Home" (mục 22) — chỉ trang chủ.
		wp_add_inline_style( 'tmnhanphat-projects', tmnhanphat_render_projects_css_vars() );
		tmnhanphat_enqueue_style_file( 'tmnhanphat-review', 'assets/css/components/customer-review.css' );
		// CSS custom properties theo Customizer panel "Customer Review Home" (mục 22) — chỉ trang chủ.
		wp_add_inline_style( 'tmnhanphat-review', tmnhanphat_render_customer_review_css_vars() );
		// News Section — phụ thuộc services.css (base .tmnp-slider) VÀ customer-review.css
		// (tái sử dụng .review-home__heading khi Reuse Heading Style bật).
		tmnhanphat_enqueue_style_file( 'tmnhanphat-news', 'assets/css/components/news.css', array( 'tmnhanphat-services', 'tmnhanphat-review' ) );
		// CSS custom properties theo Customizer panel "Featured News Home" (mục 22) — chỉ trang chủ.
		wp_add_inline_style( 'tmnhanphat-news', tmnhanphat_render_news_css_vars() );
		// Quote Contact Section — phụ thuộc customer-review.css (reuse .review-home__heading).
		tmnhanphat_enqueue_style_file( 'tmnhanphat-quote', 'assets/css/components/quote.css', array( 'tmnhanphat-review' ) );
		// CSS custom properties theo Customizer panel "Quote Contact Home" (mục 22) — chỉ trang chủ.
		wp_add_inline_style( 'tmnhanphat-quote', tmnhanphat_render_quote_css_vars() );
		// FAQ Section — phụ thuộc customer-review.css (reuse .review-home__heading khi bật).
		tmnhanphat_enqueue_style_file( 'tmnhanphat-faq', 'assets/css/components/faq.css', array( 'tmnhanphat-review' ) );
		// CSS custom properties theo Customizer panel "FAQ Home" (mục 22) — chỉ trang chủ.
		wp_add_inline_style( 'tmnhanphat-faq', tmnhanphat_render_faq_css_vars() );
		tmnhanphat_enqueue_style_file( 'tmnhanphat-front-page', 'assets/css/pages/front-page.css' );
		tmnhanphat_enqueue_script_file( 'tmnhanphat-front-page', 'assets/js/pages/front-page.js' );
		// Slider component (Services Home) — chỉ trang chủ, phụ thuộc helpers (window.tmnhanphat).
		tmnhanphat_enqueue_script_file( 'tmnhanphat-slider', 'assets/js/components/slider.js', array( 'tmnhanphat-helpers' ) );
		// Products Home (grid slider + category filter AJAX) — phụ thuộc app vì cần
		// tmnhanphatData (ajaxUrl + nonce) được wp_localize_script gắn vào handle đó.
		tmnhanphat_enqueue_script_file( 'tmnhanphat-products', 'assets/js/components/products.js', array( 'tmnhanphat-helpers', 'tmnhanphat-app' ) );
		// Process Home (timeline auto-active + đồng bộ ảnh theo Step) — JS độc lập, không deps.
		tmnhanphat_enqueue_script_file( 'tmnhanphat-process', 'assets/js/components/process.js' );
		// Customer Review Marquee (pause khi tab ẩn/prefers-reduced-motion) — JS độc lập, không deps.
		tmnhanphat_enqueue_script_file( 'tmnhanphat-review', 'assets/js/components/customer-review.js' );
		// Quote Contact Form (validation Frontend + gửi AJAX) — phụ thuộc app vì cần
		// tmnhanphatData (ajaxUrl + nonce) được wp_localize_script gắn vào handle đó.
		tmnhanphat_enqueue_script_file( 'tmnhanphat-quote', 'assets/js/components/quote.js', array( 'tmnhanphat-app' ) );
		// FAQ Accordion (slide down/up, 1 FAQ mở cùng lúc) — JS độc lập, không deps.
		tmnhanphat_enqueue_script_file( 'tmnhanphat-faq', 'assets/js/components/faq.js' );
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
