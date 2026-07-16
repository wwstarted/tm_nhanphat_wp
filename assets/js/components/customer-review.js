/**
 * Customer Review Marquee ([data-tmnp-review-marquee]) — vanilla JS thuần, không thư
 * viện ngoài (PROJECT_RULES.md mục 21). Animation cuộn CHÍNH là CSS @keyframes thuần
 * (assets/css/components/customer-review.css) — file này chỉ làm 2 việc KHÔNG thể
 * làm bằng CSS một mình:
 *
 * 1) Dừng animation khi tab ẩn (tiết kiệm CPU/GPU — cùng pattern slider.js/process.js).
 * 2) Đảm bảo prefers-reduced-motion tắt animation ngay cả khi trình duyệt cũ không
 *    hỗ trợ @media (prefers-reduced-motion) trong keyframe animation (rất hiếm, nhưng
 *    gắn class tường minh vẫn an toàn hơn chỉ dựa vào CSS).
 * 3) Nút Tạm dừng/Phát [data-marquee-toggle] — WCAG 2.2.2: pause-hover không dùng được
 *    trên touch nên cần cơ chế pause nhìn thấy được. Pause thủ công ưu tiên hơn
 *    visibilitychange (đã bấm dừng thì quay lại tab vẫn dừng).
 *
 * Pause on Hover đã xử lý HOÀN TOÀN bằng CSS (:hover thuần) — không cần JS.
 */
( function () {
	'use strict';

	var prefersReducedMotion = window.matchMedia && window.matchMedia( '(prefers-reduced-motion: reduce)' ).matches;

	function initMarquee( root ) {
		var toggle = root.querySelector( '[data-marquee-toggle]' );
		// prefers-reduced-motion: mặc định DỪNG, nhưng vẫn cho phép người dùng chủ động
		// bấm Phát (opt-in) — tôn trọng hệ điều hành nhưng không tước quyền lựa chọn.
		var userPaused = prefersReducedMotion;

		function apply() {
			root.classList.toggle( 'is-paused', userPaused || document.hidden );

			if ( toggle ) {
				toggle.setAttribute( 'aria-pressed', userPaused ? 'true' : 'false' );
				toggle.setAttribute( 'aria-label', userPaused ? 'Phát cuộn tự động' : 'Tạm dừng cuộn tự động' );
			}
		}

		if ( toggle ) {
			toggle.addEventListener( 'click', function () {
				userPaused = ! userPaused;
				apply();
			} );
		}

		document.addEventListener( 'visibilitychange', apply );
		apply();
	}

	document.querySelectorAll( '[data-tmnp-review-marquee]' ).forEach( initMarquee );
} )();
