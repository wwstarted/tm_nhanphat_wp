<?php
/**
 * Pagination component — logic lấy link nằm ở inc/pagination.php (PROJECT_RULES.md mục 11).
 *
 * @package TMNhanPhat
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$tmnhanphat_links = tmnhanphat_get_pagination_links();

if ( empty( $tmnhanphat_links ) ) {
	return;
}
?>
<nav class="pagination" aria-label="<?php esc_attr_e( 'Điều hướng phân trang', 'tmnhanphat' ); ?>">
	<ul class="pagination__list">
		<?php foreach ( $tmnhanphat_links as $tmnhanphat_link ) : ?>
			<li class="pagination__item"><?php echo wp_kses_post( $tmnhanphat_link ); ?></li>
		<?php endforeach; ?>
	</ul>
</nav>
