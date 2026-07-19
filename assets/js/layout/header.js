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
	// Header sticky — toggle .is-scrolled (trạng thái 2: Top Bar collapse, menu dồn giữa).
	// LOGIC THEO HƯỚNG: cuộn XUỐNG → trạng thái 2; cuộn LÊN → trạng thái 1 (không cần về
	// đỉnh); gần đỉnh (< MIN) luôn trạng thái 1. DELTA bỏ qua rung nhỏ. Vì đổi trạng thái
	// liên tục theo hướng nên chuyển động do CSS transition lo cho mượt.
	var header = document.querySelector( '.site-header--sticky-enabled' );

	if ( ! header ) {
		return;
	}

	var MIN = 60;
	var DELTA = 5;
	var lastY = window.scrollY || window.pageYOffset;
	var ticking = false;

	function updateScrolledState() {
		var y = Math.max( 0, window.scrollY || window.pageYOffset );

		if ( y <= MIN ) {
			header.classList.remove( 'is-scrolled' ); // gần đỉnh → trạng thái 1 (đầy đủ)
		} else if ( y > lastY + DELTA ) {
			header.classList.add( 'is-scrolled' );     // cuộn XUỐNG → trạng thái 2
		} else if ( y < lastY - DELTA ) {
			header.classList.remove( 'is-scrolled' );  // cuộn LÊN → trạng thái 1
		}

		if ( Math.abs( y - lastY ) > DELTA ) {
			lastY = y;
		}

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
