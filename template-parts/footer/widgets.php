<?php
/**
 * Widget area của footer — chỉ render khi có widget được gán (PROJECT_RULES.md mục 5).
 *
 * @package TMNhanPhat
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! is_active_sidebar( 'footer-widgets' ) ) {
	return;
}
?>
<div class="footer-widgets">
	<?php dynamic_sidebar( 'footer-widgets' ); ?>
</div>
