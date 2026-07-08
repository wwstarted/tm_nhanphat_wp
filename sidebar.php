<?php
/**
 * Sidebar chính — chỉ render khi có widget được gán.
 *
 * @package TMNhanPhat
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! is_active_sidebar( 'sidebar-primary' ) ) {
	return;
}
?>
<aside id="secondary" class="widget-area" aria-label="<?php esc_attr_e( 'Sidebar', 'tmnhanphat' ); ?>">
	<?php dynamic_sidebar( 'sidebar-primary' ); ?>
</aside>
