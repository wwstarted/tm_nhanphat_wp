/**
 * Offcanvas menu (tablet/mobile) + dropdown submenu bằng cảm ứng/bàn phím.
 * Markup: template-parts/header/navigation.php. Style: assets/css/layout/header.css.
 * Phụ thuộc window.tmnhanphat.debounce (global/helpers.js phải load trước — xem inc/enqueue.php).
 */
( function () {
	var nav = document.querySelector( '.main-navigation' );
	var toggle = document.querySelector( '.main-navigation__toggle' );
	var panel = document.querySelector( '.main-navigation__panel' );
	var overlay = document.querySelector( '.main-navigation__overlay' );
	var closeBtn = document.querySelector( '.main-navigation__panel-close' );

	if ( ! nav || ! toggle || ! panel ) {
		return;
	}

	var breakpoint = parseInt( nav.getAttribute( 'data-breakpoint' ), 10 ) || 992;

	function openMenu() {
		nav.classList.add( 'is-open' );
		toggle.setAttribute( 'aria-expanded', 'true' );
		document.body.classList.add( 'no-scroll' );
		closeBtn && closeBtn.focus();
	}

	function closeMenu() {
		nav.classList.remove( 'is-open' );
		toggle.setAttribute( 'aria-expanded', 'false' );
		document.body.classList.remove( 'no-scroll' );
		toggle.focus();
	}

	toggle.addEventListener( 'click', function () {
		if ( nav.classList.contains( 'is-open' ) ) {
			closeMenu();
		} else {
			openMenu();
		}
	} );

	if ( overlay ) {
		overlay.addEventListener( 'click', closeMenu );
	}

	if ( closeBtn ) {
		closeBtn.addEventListener( 'click', closeMenu );
	}

	document.addEventListener( 'keydown', function ( event ) {
		if ( 'Escape' === event.key && nav.classList.contains( 'is-open' ) ) {
			closeMenu();
		}
	} );

	window.addEventListener(
		'resize',
		window.tmnhanphat.debounce( function () {
			if ( window.innerWidth > breakpoint && nav.classList.contains( 'is-open' ) ) {
				closeMenu();
			}
		}, 150 )
	);

	// Toggle dropdown submenu (cấp 1 có con) bằng cảm ứng/bàn phím trong offcanvas.
	var submenuToggles = document.querySelectorAll( '.submenu-toggle' );

	submenuToggles.forEach( function ( button ) {
		button.addEventListener( 'click', function () {
			var parentItem = button.closest( '.menu-item-has-children' );

			if ( ! parentItem ) {
				return;
			}

			var isOpen = parentItem.classList.toggle( 'is-submenu-open' );
			button.setAttribute( 'aria-expanded', isOpen ? 'true' : 'false' );
		} );
	} );
} )();
