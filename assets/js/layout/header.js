/**
 * Thêm class .is-scrolled cho .site-header khi cuộn trang (đổi bóng đổ — xem layout/header.css).
 * Phụ thuộc window.tmnhanphat.debounce (global/helpers.js phải load trước — xem inc/enqueue.php).
 */
( function () {
	var header = document.querySelector( '.site-header' );

	if ( ! header ) {
		return;
	}

	function updateScrolledState() {
		header.classList.toggle( 'is-scrolled', window.scrollY > 10 );
	}

	updateScrolledState();
	window.addEventListener( 'scroll', window.tmnhanphat.debounce( updateScrolledState, 50 ), { passive: true } );
} )();
