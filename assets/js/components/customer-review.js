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
 *
 * Pause on Hover đã xử lý HOÀN TOÀN bằng CSS (:hover thuần) — không cần JS.
 */
( function () {
	'use strict';

	var prefersReducedMotion = window.matchMedia && window.matchMedia( '(prefers-reduced-motion: reduce)' ).matches;

	function initMarquee( root ) {
		if ( prefersReducedMotion ) {
			root.classList.add( 'is-paused' );
			return; // Không cần theo dõi tab ẩn nữa — animation đã tắt hẳn.
		}

		document.addEventListener( 'visibilitychange', function () {
			root.classList.toggle( 'is-paused', document.hidden );
		} );
	}

	document.querySelectorAll( '[data-tmnp-review-marquee]' ).forEach( initMarquee );
} )();
