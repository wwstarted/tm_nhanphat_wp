/**
 * Slider component dùng chung (.tmnp-slider / [data-tmnp-slider]) — vanilla JS thuần,
 * không thư viện ngoài (PROJECT_RULES.md mục 21). Hiện dùng cho Services Home Section,
 * tái sử dụng được cho mọi section slider sau này (chỉ cần đúng markup + data-attribute).
 *
 * Tính năng: autoplay (pause on hover/focus/tab ẩn), infinite loop (clone slide 2 đầu),
 * drag/swipe bằng Pointer Events (chuột + cảm ứng), keyboard (←/→ khi focus viewport),
 * dots + arrows (sinh DOM động khi bật — không in sẵn từ PHP), responsive theo
 * data-cards-desktop/tablet/mobile (breakpoint 991/599 đồng bộ toàn theme).
 *
 * Progressive enhancement: markup gốc là track cuộn ngang thuần CSS — file này chỉ NÂNG CẤP:
 * gắn .is-enhanced lên slider và js-services-ready lên section bao ngoài (.services-home)
 * SAU khi khởi tạo thành công, không bao giờ ẩn nội dung trước đó (bài học từ About Section).
 * prefers-reduced-motion: tắt autoplay + transition tức thời, vẫn điều khiển tay được.
 */
( function () {
	'use strict';

	var BREAKPOINT_TABLET = 991;
	var BREAKPOINT_MOBILE = 599;

	var debounce = ( window.tmnhanphat && window.tmnhanphat.debounce ) || function ( fn, wait ) {
		var timer;

		return function () {
			var args = arguments;
			var context = this;

			clearTimeout( timer );
			timer = setTimeout( function () {
				fn.apply( context, args );
			}, wait );
		};
	};

	var prefersReducedMotion = window.matchMedia && window.matchMedia( '(prefers-reduced-motion: reduce)' ).matches;

	/**
	 * Đọc data-attribute boolean ("true"/"false").
	 */
	function dataBool( el, name, fallback ) {
		var raw = el.getAttribute( 'data-' + name );

		if ( null === raw ) {
			return fallback;
		}

		return 'true' === raw;
	}

	/**
	 * Đọc data-attribute số.
	 */
	function dataInt( el, name, fallback ) {
		var value = parseInt( el.getAttribute( 'data-' + name ), 10 );

		return isNaN( value ) ? fallback : value;
	}

	function initSlider( root ) {
		var viewport = root.querySelector( '.tmnp-slider__viewport' );
		var track = root.querySelector( '.tmnp-slider__track' );

		if ( ! viewport || ! track ) {
			return;
		}

		var originalSlides = Array.prototype.slice.call( track.children );
		var total = originalSlides.length;

		if ( total < 1 ) {
			return;
		}

		var options = {
			autoplay: dataBool( root, 'autoplay', true ) && ! prefersReducedMotion,
			autoplaySpeed: dataInt( root, 'autoplay-speed', 4000 ),
			transition: prefersReducedMotion ? 0 : dataInt( root, 'transition', 600 ),
			pauseHover: dataBool( root, 'pause-hover', true ),
			infinite: dataBool( root, 'infinite', true ),
			arrows: dataBool( root, 'arrows', false ),
			dots: dataBool( root, 'dots', true ),
			drag: dataBool( root, 'drag', true ),
			cardsDesktop: dataInt( root, 'cards-desktop', 3 ),
			cardsTablet: dataInt( root, 'cards-tablet', 2 ),
			cardsMobile: dataInt( root, 'cards-mobile', 1 )
		};

		function getPerView() {
			var width = window.innerWidth;

			if ( width <= BREAKPOINT_MOBILE ) {
				return Math.max( 1, options.cardsMobile );
			}

			if ( width <= BREAKPOINT_TABLET ) {
				return Math.max( 1, options.cardsTablet );
			}

			return Math.max( 1, options.cardsDesktop );
		}

		// Không đủ slide để trượt ở mọi breakpoint → giữ nguyên layout tĩnh, không nâng cấp.
		var maxPerView = Math.max( options.cardsDesktop, options.cardsTablet, options.cardsMobile );
		if ( total <= Math.min( options.cardsMobile, options.cardsTablet, options.cardsDesktop ) ) {
			return;
		}

		// ----- Infinite: clone maxPerView slide ở cả 2 đầu (đủ cho mọi breakpoint). -----
		var cloneCount = options.infinite ? maxPerView : 0;
		var i;
		var clone;

		if ( cloneCount > 0 ) {
			for ( i = 0; i < cloneCount; i++ ) {
				clone = originalSlides[ i % total ].cloneNode( true );
				clone.setAttribute( 'aria-hidden', 'true' );
				clone.setAttribute( 'data-tmnp-clone', 'true' );
				track.appendChild( clone );
			}

			for ( i = 0; i < cloneCount; i++ ) {
				clone = originalSlides[ ( total - 1 - i % total + total ) % total ].cloneNode( true );
				clone.setAttribute( 'aria-hidden', 'true' );
				clone.setAttribute( 'data-tmnp-clone', 'true' );
				track.insertBefore( clone, track.firstChild );
			}
		}

		var allSlides = Array.prototype.slice.call( track.children );
		var current = cloneCount; // index trong allSlides — bắt đầu tại slide gốc đầu tiên.
		var autoplayTimer = null;
		var isPaused = false;
		var isJumping = false;
		var dots = [];

		// ARIA cho từng slide gốc (mục Accessibility).
		originalSlides.forEach( function ( slide, index ) {
			slide.setAttribute( 'role', 'group' );
			slide.setAttribute( 'aria-roledescription', 'slide' );
			slide.setAttribute( 'aria-label', ( index + 1 ) + ' / ' + total );
		} );

		function getStep() {
			var slide = allSlides[ 0 ];
			var gap = parseFloat( getComputedStyle( track ).columnGap || getComputedStyle( track ).gap ) || 0;

			return slide.offsetWidth + gap;
		}

		function setTransform( withTransition ) {
			track.style.transition = withTransition ? 'transform ' + options.transition + 'ms ease' : 'none';
			track.style.transform = 'translateX(' + ( -current * getStep() ) + 'px)';
		}

		function currentRealIndex() {
			return ( ( current - cloneCount ) % total + total ) % total;
		}

		function updateDots() {
			var active = currentRealIndex();

			dots.forEach( function ( dot, index ) {
				dot.classList.toggle( 'is-active', index === active );
				dot.setAttribute( 'aria-current', index === active ? 'true' : 'false' );
			} );
		}

		function goTo( index, withTransition ) {
			var lastStart = options.infinite ? allSlides.length - cloneCount : total - getPerView();

			if ( ! options.infinite ) {
				index = Math.max( 0, Math.min( index, lastStart ) );
			}

			current = index;
			setTransform( withTransition !== false );
			updateDots();
		}

		function next() {
			goTo( current + 1 );
		}

		function prev() {
			goTo( current - 1 );
		}

		// Sau transition, nếu đang đứng ở vùng clone → nhảy thầm về slide gốc tương ứng.
		track.addEventListener( 'transitionend', function ( event ) {
			if ( event.target !== track || ! options.infinite || isJumping ) {
				return;
			}

			if ( current >= cloneCount + total || current < cloneCount ) {
				isJumping = true;
				current = cloneCount + currentRealIndex();
				setTransform( false );
				// Ép reflow để lần transform kế tiếp có transition trở lại.
				void track.offsetWidth; // eslint-disable-line no-void
				isJumping = false;
			}
		} );

		// ----- Autoplay -----
		function stopAutoplay() {
			if ( autoplayTimer ) {
				clearInterval( autoplayTimer );
				autoplayTimer = null;
			}
		}

		function startAutoplay() {
			stopAutoplay();

			if ( ! options.autoplay || isPaused ) {
				return;
			}

			autoplayTimer = setInterval( function () {
				if ( ! options.infinite && current >= total - getPerView() ) {
					goTo( 0 );
					return;
				}

				next();
			}, options.autoplaySpeed );
		}

		if ( options.pauseHover ) {
			root.addEventListener( 'mouseenter', function () {
				isPaused = true;
				stopAutoplay();
			} );
			root.addEventListener( 'mouseleave', function () {
				isPaused = false;
				startAutoplay();
			} );
		}

		root.addEventListener( 'focusin', function () {
			isPaused = true;
			stopAutoplay();
		} );
		root.addEventListener( 'focusout', function () {
			isPaused = false;
			startAutoplay();
		} );

		document.addEventListener( 'visibilitychange', function () {
			if ( document.hidden ) {
				stopAutoplay();
			} else {
				startAutoplay();
			}
		} );

		// ----- Keyboard (viewport có tabindex="0" sẵn trong markup) -----
		viewport.addEventListener( 'keydown', function ( event ) {
			if ( 'ArrowLeft' === event.key ) {
				event.preventDefault();
				prev();
			} else if ( 'ArrowRight' === event.key ) {
				event.preventDefault();
				next();
			}
		} );

		// ----- Drag / Swipe (Pointer Events: chuột + touch chung 1 code path) -----
		if ( options.drag && window.PointerEvent ) {
			var dragStartX = 0;
			var dragStartOffset = 0;
			var isDragging = false;

			viewport.addEventListener( 'pointerdown', function ( event ) {
				isDragging = true;
				dragStartX = event.clientX;
				dragStartOffset = -current * getStep();
				track.classList.add( 'is-dragging' );
				viewport.setPointerCapture( event.pointerId );
				stopAutoplay();
			} );

			viewport.addEventListener( 'pointermove', function ( event ) {
				if ( ! isDragging ) {
					return;
				}

				track.style.transition = 'none';
				track.style.transform = 'translateX(' + ( dragStartOffset + ( event.clientX - dragStartX ) ) + 'px)';
			} );

			function endDrag( event ) {
				if ( ! isDragging ) {
					return;
				}

				isDragging = false;
				track.classList.remove( 'is-dragging' );

				var delta = event.clientX - dragStartX;
				var threshold = getStep() * 0.2;

				if ( delta <= -threshold ) {
					next();
				} else if ( delta >= threshold ) {
					prev();
				} else {
					setTransform( true ); // trả về vị trí cũ
				}

				startAutoplay();
			}

			viewport.addEventListener( 'pointerup', endDrag );
			viewport.addEventListener( 'pointercancel', endDrag );

			// Chặn click "vô tình" vào card sau khi vừa kéo xong.
			viewport.addEventListener( 'click', function ( event ) {
				if ( Math.abs( event.clientX - dragStartX ) > 10 && dragStartX !== 0 ) {
					event.preventDefault();
				}
			}, true );

			// Kéo ngang không cuộn dọc trang trên cảm ứng.
			viewport.style.touchAction = 'pan-y';
		}

		// ----- Dots -----
		if ( options.dots && total > 1 ) {
			var dotsWrap = document.createElement( 'div' );
			dotsWrap.className = 'tmnp-slider__dots';
			dotsWrap.setAttribute( 'role', 'tablist' );

			for ( i = 0; i < total; i++ ) {
				( function ( index ) {
					var dot = document.createElement( 'button' );
					dot.type = 'button';
					dot.className = 'tmnp-slider__dot';
					dot.setAttribute( 'aria-label', 'Chuyển tới slide ' + ( index + 1 ) );
					dot.addEventListener( 'click', function () {
						goTo( cloneCount + index );
					} );
					dotsWrap.appendChild( dot );
					dots.push( dot );
				}( i ) );
			}

			root.appendChild( dotsWrap );
		}

		// ----- Arrows (tuỳ chọn) -----
		if ( options.arrows ) {
			var prevBtn = document.createElement( 'button' );
			prevBtn.type = 'button';
			prevBtn.className = 'tmnp-slider__arrow tmnp-slider__arrow--prev';
			prevBtn.setAttribute( 'aria-label', 'Slide trước' );
			prevBtn.innerHTML = '<svg width="9" height="14" viewBox="0 0 9 14" fill="none" aria-hidden="true" focusable="false"><path d="M8 1L2 7l6 6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>';
			prevBtn.addEventListener( 'click', prev );

			var nextBtn = document.createElement( 'button' );
			nextBtn.type = 'button';
			nextBtn.className = 'tmnp-slider__arrow tmnp-slider__arrow--next';
			nextBtn.setAttribute( 'aria-label', 'Slide sau' );
			nextBtn.innerHTML = '<svg width="9" height="14" viewBox="0 0 9 14" fill="none" aria-hidden="true" focusable="false"><path d="M1 1l6 6-6 6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>';
			nextBtn.addEventListener( 'click', next );

			root.appendChild( prevBtn );
			root.appendChild( nextBtn );
		}

		// ----- Resize: tính lại offset theo bề rộng slide mới. -----
		window.addEventListener( 'resize', debounce( function () {
			setTransform( false );
		}, 150 ) );

		// ----- Kích hoạt -----
		root.classList.add( 'is-enhanced' );

		var section = root.closest( '.services-home' );
		if ( section ) {
			section.classList.add( 'js-services-ready' );
		}

		setTransform( false );
		updateDots();
		startAutoplay();
	}

	document.querySelectorAll( '[data-tmnp-slider]' ).forEach( initSlider );
} )();
