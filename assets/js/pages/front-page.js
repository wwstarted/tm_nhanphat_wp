/**
 * Hero Banner — Company Stats animation (fade-up + stagger + scale nhẹ khi cuộn tới,
 * count-up cho Number). Vanilla JS thuần, chỉ dùng IntersectionObserver + requestAnimationFrame
 * (không thư viện animation) — xem PROJECT_RULES.md mục 21.
 *
 * "Enable Counter Animation" chỉ có tác dụng khi "Enable Animation" (data-hero-animate)
 * đang bật, vì việc đếm số được gắn vào đúng thời điểm reveal do IntersectionObserver kích
 * hoạt — tắt Enable Animation nghĩa là tắt luôn cơ chế reveal-khi-cuộn nói chung.
 *
 * Progressive enhancement: HTML từ PHP đã in sẵn số liệu cuối cùng (template-parts/home/hero.php),
 * nên nếu JS không chạy được (lỗi/chặn) hoặc trình duyệt không hỗ trợ IntersectionObserver,
 * người dùng vẫn thấy đúng nội dung — chỉ mất hiệu ứng, không mất dữ liệu.
 */
( function () {
	var hero = document.querySelector( '.hero[data-hero-animate="true"]' );
	var statsContainer = document.querySelector( '.hero__stats' );

	if ( ! hero || ! statsContainer || ! ( 'IntersectionObserver' in window ) ) {
		return;
	}

	var prefersReducedMotion = window.matchMedia && window.matchMedia( '(prefers-reduced-motion: reduce)' ).matches;

	function parseMsVar( name, fallback ) {
		var raw = getComputedStyle( document.documentElement ).getPropertyValue( name ).trim();
		var value = parseFloat( raw );

		return isNaN( value ) ? fallback : value;
	}

	function animateCount( el ) {
		var target = parseInt( el.getAttribute( 'data-count-to' ), 10 );
		var suffix = el.getAttribute( 'data-count-suffix' ) || '';

		if ( isNaN( target ) ) {
			return;
		}

		var duration = parseMsVar( '--hero-animation-duration', 600 );
		var startTime = null;

		el.textContent = '0' + suffix;

		function step( timestamp ) {
			if ( null === startTime ) {
				startTime = timestamp;
			}

			var progress = Math.min( ( timestamp - startTime ) / duration, 1 );
			el.textContent = Math.round( progress * target ) + suffix;

			if ( progress < 1 ) {
				window.requestAnimationFrame( step );
			}
		}

		window.requestAnimationFrame( step );
	}

	function revealStat( stat ) {
		stat.classList.add( 'is-visible' );

		if ( prefersReducedMotion ) {
			return;
		}

		var numberEl = stat.querySelector( '[data-count-to]' );

		if ( numberEl ) {
			animateCount( numberEl );
		}
	}

	hero.classList.add( 'js-hero-stats-ready' );

	var observer = new IntersectionObserver(
		function ( entries, obs ) {
			entries.forEach( function ( entry ) {
				if ( entry.isIntersecting ) {
					revealStat( entry.target );
					obs.unobserve( entry.target );
				}
			} );
		},
		{ threshold: 0.3 }
	);

	statsContainer.querySelectorAll( '.hero__stat' ).forEach( function ( stat ) {
		observer.observe( stat );
	} );
} )();

/**
 * About Company Section — fade nhẹ khi cuộn tới (Shape/Elevator/Content), độc lập hoàn toàn
 * với Hero Stats ở trên. Cùng cơ chế progressive enhancement: HTML đã đầy đủ nội dung sẵn,
 * JS chỉ thêm hiệu ứng reveal, không thư viện animation ngoài (mục 21).
 */
( function () {
	var about = document.querySelector( '.about-home' );

	if ( ! about || ! ( 'IntersectionObserver' in window ) ) {
		return;
	}

	about.classList.add( 'js-about-ready' );

	var targets = about.querySelectorAll( '.about-home__shape, .about-home__image, .about-home__content' );

	var observer = new IntersectionObserver(
		function ( entries, obs ) {
			entries.forEach( function ( entry ) {
				if ( entry.isIntersecting ) {
					entry.target.classList.add( 'is-visible' );
					obs.unobserve( entry.target );
				}
			} );
		},
		{ threshold: 0.2 }
	);

	targets.forEach( function ( target ) {
		observer.observe( target );
	} );
} )();
