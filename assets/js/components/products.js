/**
 * Products Home Section — grid slider + category filter AJAX. Vanilla JS thuần,
 * không thư viện ngoài (PROJECT_RULES.md mục 21).
 *
 * GRID SLIDER (khác slider.js của Services — trượt từng card đơn): mỗi trang =
 * Cols × Rows card (Desktop 3×2=6, Tablet 2×2=4, Mobile 1×1=1 theo data-attribute).
 * PHP in danh sách card PHẲNG; file này gom card thành từng trang, đổi breakpoint
 * thì gom lại — translateX theo %, dots theo số trang, autoplay/loop/pause hover/
 * swipe (Pointer Events)/keyboard, tôn trọng prefers-reduced-motion.
 *
 * CATEGORY FILTER: click tab → POST admin-ajax (action tmnhanphat_filter_products,
 * nonce từ tmnhanphatData do inc/enqueue.php localize) → thay card + rebuild slider.
 * Lỗi mạng/AJAX → điều hướng theo href thật của tab (trang archive category) — cùng
 * hành vi với khi JS tắt hoàn toàn (progressive enhancement).
 *
 * Chỉ gắn .is-enhanced + js-products-ready SAU khi khởi tạo thành công — không bao
 * giờ ẩn nội dung trước đó (bài học từ bug progressive-enhancement của About Section).
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

	/**
	 * Khởi tạo grid slider cho 1 root [data-tmnp-products].
	 * Trả về API { setCards } cho phần AJAX filter dùng lại.
	 */
	function initProductsSlider( root ) {
		var viewport = root.querySelector( '.tmnp-products-slider__viewport' );
		var track = root.querySelector( '.tmnp-products-slider__track' );
		var emptyMessage = root.querySelector( '.products-home__empty' );

		if ( ! viewport || ! track ) {
			return null;
		}

		var options = {
			autoplay: dataBool( root, 'autoplay', true ) && ! prefersReducedMotion,
			delay: dataInt( root, 'delay', 4000 ),
			transition: prefersReducedMotion ? 0 : dataInt( root, 'transition', 600 ),
			loop: dataBool( root, 'loop', true ),
			pauseHover: dataBool( root, 'pause-hover', true ),
			drag: dataBool( root, 'drag', true ),
			dots: dataBool( root, 'dots', true ),
			colsDesktop: dataInt( root, 'cols-desktop', 3 ),
			colsTablet: dataInt( root, 'cols-tablet', 2 ),
			colsMobile: dataInt( root, 'cols-mobile', 1 ),
			rowsDesktop: dataInt( root, 'rows-desktop', 2 ),
			rowsTablet: dataInt( root, 'rows-tablet', 2 ),
			rowsMobile: dataInt( root, 'rows-mobile', 1 )
		};

		var cards = Array.prototype.slice.call( track.children );
		var pages = [];
		var dots = [];
		var dotsWrap = null;
		var current = 0;
		var perPage = 0;
		var autoplayTimer = null;
		var isPaused = false;

		function getPerPage() {
			var width = window.innerWidth;

			if ( width <= BREAKPOINT_MOBILE ) {
				return Math.max( 1, options.colsMobile * options.rowsMobile );
			}

			if ( width <= BREAKPOINT_TABLET ) {
				return Math.max( 1, options.colsTablet * options.rowsTablet );
			}

			return Math.max( 1, options.colsDesktop * options.rowsDesktop );
		}

		function pageCount() {
			return pages.length;
		}

		function setTransform( withTransition ) {
			track.style.transition = withTransition ? 'transform ' + options.transition + 'ms ease' : 'none';
			track.style.transform = 'translateX(' + ( -current * 100 ) + '%)';
		}

		function updateDots() {
			dots.forEach( function ( dot, index ) {
				dot.classList.toggle( 'is-active', index === current );
				dot.setAttribute( 'aria-current', index === current ? 'true' : 'false' );
			} );
		}

		function goTo( index, withTransition ) {
			var last = pageCount() - 1;

			if ( last < 0 ) {
				return;
			}

			if ( index < 0 ) {
				index = options.loop ? last : 0;
			} else if ( index > last ) {
				index = options.loop ? 0 : last;
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

		// ----- Dots (sinh DOM động khi bật — không in sẵn từ PHP, mục 15) -----
		function buildDots() {
			if ( dotsWrap ) {
				dotsWrap.remove();
				dotsWrap = null;
			}
			dots = [];

			if ( ! options.dots || pageCount() < 2 ) {
				return;
			}

			dotsWrap = document.createElement( 'div' );
			dotsWrap.className = 'tmnp-products-slider__dots';

			pages.forEach( function ( page, index ) {
				var dot = document.createElement( 'button' );
				dot.type = 'button';
				dot.className = 'tmnp-products-slider__dot';
				dot.setAttribute( 'aria-label', 'Chuyển tới trang ' + ( index + 1 ) );
				dot.addEventListener( 'click', function () {
					goTo( index );
					restartAutoplay();
				} );
				dotsWrap.appendChild( dot );
				dots.push( dot );
			} );

			root.appendChild( dotsWrap );
			updateDots();
		}

		/**
		 * Gom card phẳng thành từng trang Cols × Rows theo breakpoint hiện tại.
		 * appendChild DI CHUYỂN node card (không clone) — cards[] luôn là nguồn sự thật.
		 */
		function buildPages() {
			perPage = getPerPage();
			pages = [];
			track.innerHTML = '';

			for ( var i = 0; i < cards.length; i += perPage ) {
				var page = document.createElement( 'div' );
				page.className = 'tmnp-products-slider__page';

				cards.slice( i, i + perPage ).forEach( function ( card ) {
					page.appendChild( card );
				} );

				track.appendChild( page );
				pages.push( page );
			}

			if ( emptyMessage ) {
				emptyMessage.hidden = cards.length > 0;
			}

			current = Math.min( current, Math.max( 0, pageCount() - 1 ) );
			setTransform( false );
			buildDots();
		}

		// ----- Autoplay -----
		function stopAutoplay() {
			if ( autoplayTimer ) {
				clearInterval( autoplayTimer );
				autoplayTimer = null;
			}
		}

		function startAutoplay() {
			stopAutoplay();

			if ( ! options.autoplay || isPaused || pageCount() < 2 ) {
				return;
			}

			autoplayTimer = setInterval( function () {
				if ( ! options.loop && current >= pageCount() - 1 ) {
					goTo( 0 );
					return;
				}

				next();
			}, options.delay );
		}

		function restartAutoplay() {
			isPaused = false;
			startAutoplay();
		}

		if ( options.pauseHover ) {
			root.addEventListener( 'mouseenter', function () {
				isPaused = true;
				stopAutoplay();
			} );
			root.addEventListener( 'mouseleave', restartAutoplay );
		}

		root.addEventListener( 'focusin', function () {
			isPaused = true;
			stopAutoplay();
		} );
		root.addEventListener( 'focusout', restartAutoplay );

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

		// ----- Swipe / Drag (Pointer Events: chuột + cảm ứng chung 1 code path) -----
		if ( options.drag && window.PointerEvent ) {
			var dragStartX = 0;
			var isDragging = false;

			viewport.addEventListener( 'pointerdown', function ( event ) {
				if ( pageCount() < 2 ) {
					return;
				}

				isDragging = true;
				dragStartX = event.clientX;
				track.classList.add( 'is-dragging' );
				viewport.setPointerCapture( event.pointerId );
				stopAutoplay();
			} );

			viewport.addEventListener( 'pointermove', function ( event ) {
				if ( ! isDragging ) {
					return;
				}

				var deltaPercent = ( ( event.clientX - dragStartX ) / viewport.offsetWidth ) * 100;
				track.style.transition = 'none';
				track.style.transform = 'translateX(' + ( -current * 100 + deltaPercent ) + '%)';
			} );

			var endDrag = function ( event ) {
				if ( ! isDragging ) {
					return;
				}

				isDragging = false;
				track.classList.remove( 'is-dragging' );

				var delta = event.clientX - dragStartX;
				var threshold = viewport.offsetWidth * 0.2;

				if ( delta <= -threshold ) {
					next();
				} else if ( delta >= threshold ) {
					prev();
				} else {
					setTransform( true ); // trả về vị trí cũ
				}

				restartAutoplay();
			};

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

		// ----- Resize: số card/trang đổi theo breakpoint → gom trang lại. -----
		window.addEventListener( 'resize', debounce( function () {
			if ( getPerPage() === perPage ) {
				setTransform( false ); // chỉ đổi bề rộng — giữ nguyên trang hiện tại
				return;
			}

			// Giữ đúng card đầu tiên đang xem khi đổi cỡ trang.
			var firstCardIndex = current * perPage;
			buildPages();
			goTo( Math.floor( firstCardIndex / perPage ), false );
			startAutoplay();
		}, 150 ) );

		// ----- Kích hoạt -----
		root.classList.add( 'is-enhanced' );

		var section = root.closest( '.products-home' );
		if ( section ) {
			section.classList.add( 'js-products-ready' );
		}

		buildPages();
		startAutoplay();

		return {
			/**
			 * Thay toàn bộ card (sau AJAX đổi category) rồi rebuild từ trang đầu.
			 *
			 * @param {Element[]} newCards Danh sách element .product-card mới.
			 */
			setCards: function ( newCards ) {
				cards = newCards;
				current = 0;
				buildPages();
				startAutoplay();
			}
		};
	}

	// ----- Category filter AJAX -----
	function initCategoryFilter( section, sliderApi ) {
		var links = section.querySelectorAll( '.products-home__cat-link' );
		var sliderRoot = section.querySelector( '[data-tmnp-products]' );
		var ajaxConfig = window.tmnhanphatData || null;

		if ( ! links.length || ! sliderRoot || ! ajaxConfig || ! window.fetch ) {
			return; // Không đủ điều kiện AJAX — tab hoạt động như link thường (fallback).
		}

		var isLoading = false;

		function setActive( activeLink ) {
			links.forEach( function ( link ) {
				var isActive = link === activeLink;
				link.classList.toggle( 'is-active', isActive );

				if ( isActive ) {
					link.setAttribute( 'aria-current', 'true' );
				} else {
					link.removeAttribute( 'aria-current' );
				}
			} );
		}

		links.forEach( function ( link ) {
			link.addEventListener( 'click', function ( event ) {
				var termId = parseInt( link.getAttribute( 'data-term-id' ), 10 );

				if ( isNaN( termId ) || link.classList.contains( 'is-active' ) || isLoading ) {
					if ( link.classList.contains( 'is-active' ) ) {
						event.preventDefault(); // tab đang chọn — không làm gì
					}
					return;
				}

				event.preventDefault();
				isLoading = true;
				sliderRoot.classList.add( 'is-loading' );

				var body = new FormData();
				body.append( 'action', 'tmnhanphat_filter_products' );
				body.append( 'nonce', ajaxConfig.nonce );
				body.append( 'term_id', termId );

				window.fetch( ajaxConfig.ajaxUrl, { method: 'POST', body: body, credentials: 'same-origin' } )
					.then( function ( response ) {
						if ( ! response.ok ) {
							throw new Error( 'HTTP ' + response.status );
						}
						return response.json();
					} )
					.then( function ( payload ) {
						if ( ! payload || ! payload.success ) {
							throw new Error( 'AJAX error' );
						}

						var holder = document.createElement( 'div' );
						holder.innerHTML = payload.data.html || '';
						var newCards = Array.prototype.slice.call( holder.querySelectorAll( '.product-card' ) );

						sliderApi.setCards( newCards );
						setActive( link );
					} )
					.catch( function () {
						// Lỗi mạng/server — điều hướng như khi không có JS (không nuốt lỗi im lặng).
						window.location.href = link.href;
					} )
					.then( function () {
						isLoading = false;
						sliderRoot.classList.remove( 'is-loading' );
					} );
			} );
		} );
	}

	document.querySelectorAll( '.products-home' ).forEach( function ( section ) {
		var sliderRoot = section.querySelector( '[data-tmnp-products]' );

		if ( ! sliderRoot ) {
			return;
		}

		var api = initProductsSlider( sliderRoot );

		if ( api ) {
			initCategoryFilter( section, api );
		}
	} );
} )();
