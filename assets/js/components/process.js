/**
 * Process Timeline ([data-tmnp-process]) — vanilla JS thuần, không thư viện ngoài
 * (PROJECT_RULES.md mục 21). Auto-active xoay vòng các Step (1 → 2 → 3 → 1...),
 * toàn bộ config đọc từ data-attribute do Customizer render (không hardcode).
 *
 * Đồng bộ ảnh theo Step: nếu Step active có data-step-image, ảnh bên phải fade sang
 * ảnh đó; Step không có ảnh riêng → trả về ảnh mặc định của section.
 *
 * Progressive enhancement: PHP render Step 1 active sẵn — file này chỉ NÂNG CẤP
 * (xoay class .is-active), không ẩn nội dung nào. prefers-reduced-motion: tắt
 * auto-active hoàn toàn, giữ trạng thái tĩnh Step 1.
 */
( function () {
	'use strict';

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

	function initProcess( root ) {
		var steps = Array.prototype.slice.call( root.querySelectorAll( '.process-step' ) );

		if ( steps.length < 2 ) {
			return; // 0-1 step thì không có gì để xoay.
		}

		var options = {
			auto: dataBool( root, 'auto', true ) && ! prefersReducedMotion,
			interval: dataInt( root, 'interval', 3000 ),
			transition: dataInt( root, 'transition', 400 ),
			pauseHover: dataBool( root, 'pause-hover', true ),
			loop: dataBool( root, 'loop', true )
		};

		if ( ! options.auto ) {
			return;
		}

		var section = root.closest( '.process-home' );
		var media = section ? section.querySelector( '.process-home__media' ) : null;
		var image = media ? media.querySelector( '.process-home__image' ) : null;
		// Ảnh mặc định lấy từ data-default-image (template gắn trên wrapper) — KHÔNG đọc
		// từ src hiện tại vì src ban đầu có thể là ảnh riêng của Step 1.
		var defaultSrc = ( media && media.getAttribute( 'data-default-image' ) ) || ( image ? image.getAttribute( 'src' ) : '' );
		var current = 0;
		var timer = null;
		var isPaused = false;
		var pendingSrc = '';

		// Swap mượt: PRELOAD ảnh mới trước, ảnh tải xong mới fade-out → đổi src (đã nằm
		// sẵn trong cache nên hiện tức thì) → fade-in. Không có khung trắng chờ tải.
		// pendingSrc chống race khi step đổi nhanh hơn tốc độ tải ảnh: chỉ lần preload
		// mới nhất được phép swap. Bỏ srcset/sizes từ lần swap đầu để trình duyệt không
		// ưu tiên srcset cũ đè lên src mới.
		function setImage( src ) {
			if ( ! image || ! src || image.getAttribute( 'src' ) === src ) {
				return;
			}

			pendingSrc = src;

			var preload = new Image();

			preload.onload = function () {
				if ( pendingSrc !== src ) {
					return; // Đã có yêu cầu swap mới hơn — bỏ qua lần này.
				}

				image.classList.add( 'is-fading' );

				window.setTimeout( function () {
					if ( pendingSrc !== src ) {
						return;
					}

					image.removeAttribute( 'srcset' );
					image.removeAttribute( 'sizes' );
					image.setAttribute( 'src', src );
					image.classList.remove( 'is-fading' );
				}, options.transition );
			};

			preload.onerror = function () {
				if ( pendingSrc === src ) {
					pendingSrc = ''; // Ảnh hỏng — giữ nguyên ảnh hiện tại, không fade.
				}
			};

			preload.src = src;
		}

		function activate( index ) {
			steps.forEach( function ( step, i ) {
				var isActive = i === index;

				step.classList.toggle( 'is-active', isActive );

				if ( isActive ) {
					step.setAttribute( 'aria-current', 'step' );
				} else {
					step.removeAttribute( 'aria-current' );
				}
			} );

			current = index;

			var stepImage = steps[ index ].getAttribute( 'data-step-image' );
			setImage( stepImage || defaultSrc );
		}

		function tick() {
			var next = current + 1;

			if ( next >= steps.length ) {
				if ( ! options.loop ) {
					stop();
					return;
				}

				next = 0;
			}

			activate( next );
		}

		function stop() {
			if ( timer ) {
				window.clearInterval( timer );
				timer = null;
			}
		}

		function start() {
			stop();

			if ( isPaused ) {
				return;
			}

			timer = window.setInterval( tick, options.interval );
		}

		if ( options.pauseHover ) {
			root.addEventListener( 'mouseenter', function () {
				isPaused = true;
				stop();
			} );
			root.addEventListener( 'mouseleave', function () {
				isPaused = false;
				start();
			} );
		}

		// Tab ẩn thì dừng — không xoay "chay" tốn CPU khi người dùng không nhìn thấy.
		document.addEventListener( 'visibilitychange', function () {
			if ( document.hidden ) {
				stop();
			} else {
				start();
			}
		} );

		start();
	}

	document.querySelectorAll( '[data-tmnp-process]' ).forEach( initProcess );
} )();
