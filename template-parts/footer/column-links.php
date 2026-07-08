<?php
/**
 * 1 khối "Tiêu đề + Menu" — component dùng lại cho Column 2/3 và 2 nửa của Column 4
 * (PROJECT_RULES.md mục 5: 1 component chỉ định nghĩa 1 lần, không copy HTML).
 *
 * Biến truyền vào qua get_template_part( 'template-parts/footer/column-links', null, $args ):
 * - heading         (string) tiêu đề hiển thị (H2).
 * - theme_location  (string) vị trí menu đã register_nav_menus() trong inc/menus.php.
 * - fallback_column (string) key khớp tmnhanphat_get_footer_menu_fallback_items().
 *
 * @package TMNhanPhat
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$tmnhanphat_heading  = isset( $args['heading'] ) ? $args['heading'] : '';
$tmnhanphat_location = isset( $args['theme_location'] ) ? $args['theme_location'] : '';
$tmnhanphat_fallback = isset( $args['fallback_column'] ) ? $args['fallback_column'] : '';
?>
<div class="footer-column__block">
	<?php if ( $tmnhanphat_heading ) : ?>
		<h2 class="footer-column__heading"><?php echo esc_html( $tmnhanphat_heading ); ?></h2>
	<?php endif; ?>

	<nav class="footer-column__nav" aria-label="<?php echo esc_attr( $tmnhanphat_heading ); ?>">
		<?php
		wp_nav_menu( array(
			'theme_location'    => $tmnhanphat_location,
			'menu_class'        => 'footer-menu',
			'container'         => false,
			'depth'             => 1,
			'fallback_cb'       => 'tmnhanphat_footer_menu_fallback',
			'tmnhanphat_column' => $tmnhanphat_fallback,
		) );
		?>
	</nav>
</div>
