/**
 * Highlight mục lục (template-parts/single/toc.php) tương ứng heading đang xem,
 * dùng IntersectionObserver — không thêm thư viện ngoài (PROJECT_RULES.md mục 15).
 */
const tocLinks = document.querySelectorAll( '.toc__list a' );

if ( tocLinks.length ) {
	const headings = Array.from( tocLinks )
		.map( ( link ) => document.getElementById( link.getAttribute( 'href' ).slice( 1 ) ) )
		.filter( Boolean );

	const setActiveLink = ( id ) => {
		tocLinks.forEach( ( link ) => {
			link.classList.toggle( 'is-active', link.getAttribute( 'href' ) === `#${ id }` );
		} );
	};

	const observer = new IntersectionObserver(
		( entries ) => {
			entries
				.filter( ( entry ) => entry.isIntersecting )
				.forEach( ( entry ) => setActiveLink( entry.target.id ) );
		},
		{ rootMargin: '0px 0px -70% 0px' }
	);

	headings.forEach( ( heading ) => observer.observe( heading ) );
}
