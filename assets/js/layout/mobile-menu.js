/**
 * Toggle mobile menu (button.mobile-menu-toggle trong template-parts/header/navigation.php).
 */
const toggle = document.querySelector( '.mobile-menu-toggle' );
const nav = document.querySelector( '.main-navigation' );

if ( toggle && nav ) {
	toggle.addEventListener( 'click', () => {
		const isOpen = nav.classList.toggle( 'is-open' );
		toggle.setAttribute( 'aria-expanded', isOpen ? 'true' : 'false' );
	} );
}
