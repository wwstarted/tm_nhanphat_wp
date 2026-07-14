/**
 * FAQ Accordion ([data-tmnp-faq]) — vanilla JS thuần, không thư viện ngoài
 * (PROJECT_RULES.md mục 21). Slide down/up bằng cách animate max-height (JS đo
 * scrollHeight thực tế) — mượt, không giật, không nhảy. Mặc định chỉ 1 FAQ mở cùng
 * lúc; data-allow-multiple="true" cho phép mở nhiều.
 *
 * Progressive enhancement: PHP render FAQ Expand Default mở sẵn (.is-open + panel
 * không [hidden]) — không JS thì FAQ đó vẫn xem được nội dung, các FAQ khác đóng.
 * JS chỉ NÂNG CẤP hành vi click + animation.
 */
( function () {
	'use strict';

	var prefersReducedMotion = window.matchMedia && window.matchMedia( '(prefers-reduced-motion: reduce)' ).matches;

	function openCard( card, panel, duration ) {
		card.classList.add( 'is-open' );
		card.querySelector( '.faq-card__toggle' ).setAttribute( 'aria-expanded', 'true' );
		panel.hidden = false;

		if ( prefersReducedMotion ) {
			panel.style.maxHeight = '';
			return;
		}

		panel.classList.add( 'is-animating' );
		panel.style.maxHeight = panel.scrollHeight + 'px';

		// Sau khi mở xong, bỏ max-height cố định để nội dung tự co giãn (responsive).
		window.setTimeout( function () {
			if ( card.classList.contains( 'is-open' ) ) {
				panel.style.maxHeight = 'none';
				panel.classList.remove( 'is-animating' );
			}
		}, duration );
	}

	function closeCard( card, panel, duration ) {
		var toggle = card.querySelector( '.faq-card__toggle' );

		if ( prefersReducedMotion ) {
			card.classList.remove( 'is-open' );
			toggle.setAttribute( 'aria-expanded', 'false' );
			panel.hidden = true;
			return;
		}

		// Từ 'none' → giá trị px cụ thể trước khi thu về 0 (transition cần điểm đầu rõ ràng).
		panel.style.maxHeight = panel.scrollHeight + 'px';
		panel.classList.add( 'is-animating' );

		// Ép reflow để trình duyệt ghi nhận max-height hiện tại trước khi hạ về 0.
		void panel.offsetHeight; // eslint-disable-line no-void

		card.classList.remove( 'is-open' );
		toggle.setAttribute( 'aria-expanded', 'false' );
		panel.style.maxHeight = '0px';

		window.setTimeout( function () {
			if ( ! card.classList.contains( 'is-open' ) ) {
				panel.hidden = true;
				panel.style.maxHeight = '';
				panel.classList.remove( 'is-animating' );
			}
		}, duration );
	}

	function initFaq( root ) {
		var cards = Array.prototype.slice.call( root.querySelectorAll( '.faq-card' ) );

		if ( ! cards.length ) {
			return;
		}

		var allowMultiple = root.getAttribute( 'data-allow-multiple' ) === 'true';

		// Duration đọc từ CSS var --faq-expand-duration (Customizer). Fallback 300ms.
		var durationRaw = getComputedStyle( root ).getPropertyValue( '--faq-expand-duration' );
		var duration = parseInt( durationRaw, 10 );
		if ( isNaN( duration ) ) {
			duration = 300;
		}

		cards.forEach( function ( card ) {
			var toggle = card.querySelector( '.faq-card__toggle' );
			var panel = card.querySelector( '.faq-card__panel' );

			if ( ! toggle || ! panel ) {
				return;
			}

			// Card mở sẵn từ server: để max-height tự do (không cố định).
			if ( card.classList.contains( 'is-open' ) ) {
				panel.hidden = false;
				panel.style.maxHeight = 'none';
			} else {
				panel.style.maxHeight = '0px';
			}

			toggle.addEventListener( 'click', function () {
				var isOpen = card.classList.contains( 'is-open' );

				if ( isOpen ) {
					closeCard( card, panel, duration ); // Click FAQ đang mở → đóng lại.
					return;
				}

				// Đóng các FAQ khác nếu không cho mở nhiều (mục 11: chỉ 1 FAQ mở).
				if ( ! allowMultiple ) {
					cards.forEach( function ( other ) {
						if ( other !== card && other.classList.contains( 'is-open' ) ) {
							closeCard( other, other.querySelector( '.faq-card__panel' ), duration );
						}
					} );
				}

				openCard( card, panel, duration );
			} );
		} );
	}

	document.querySelectorAll( '[data-tmnp-faq]' ).forEach( initFaq );
} )();
