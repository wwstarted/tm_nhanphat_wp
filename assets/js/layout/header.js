/**
 * Chuyển header sang trạng thái sticky (solid) sau khi cuộn qua ngưỡng, CHỈ áp dụng cho
 * header đang ở trạng thái transparent + sticky-enabled (Trang chủ). Header không tự thay
 * đổi position khi cuộn nữa — CSS đã đặt position:fixed cố định ngay từ đầu (xem
 * assets/css/layout/header.css), JS ở đây chỉ toggle 1 class để đổi màu/bóng đổ, tránh
 * hiện tượng header "biến mất rồi hiện lại" khi vượt ngưỡng sticky.
 *
 * Dùng requestAnimationFrame để gom việc đọc/ghi lại theo khung hình (thay vì debounce có
 * độ trễ cố định), đảm bảo header phản hồi ngay khi cuộn qua ngưỡng ở mọi tốc độ cuộn,
 * đạt 60fps kể cả trên mobile — event listener luôn passive để không chặn scroll thread.
 */
( function () {
	var header = document.querySelector( '.site-header--transparent.site-header--sticky-enabled' );

	if ( ! header ) {
		return;
	}

	var SCROLL_THRESHOLD = 100;
	var ticking = false;

	function updateScrolledState() {
		header.classList.toggle( 'is-scrolled', window.scrollY > SCROLL_THRESHOLD );
		ticking = false;
	}

	function requestUpdate() {
		if ( ticking ) {
			return;
		}

		ticking = true;
		window.requestAnimationFrame( updateScrolledState );
	}

	updateScrolledState();
	window.addEventListener( 'scroll', requestUpdate, { passive: true } );
} )();
