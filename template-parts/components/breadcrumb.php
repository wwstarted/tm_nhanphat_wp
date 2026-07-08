<?php
/**
 * Breadcrumb component — logic build danh sách nằm ở inc/breadcrumbs.php.
 *
 * @package TMNhanPhat
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( is_front_page() ) {
	return;
}

$tmnhanphat_items = tmnhanphat_get_breadcrumb_items();
?>
<nav class="breadcrumb" aria-label="<?php esc_attr_e( 'Breadcrumb', 'tmnhanphat' ); ?>">
	<ol class="breadcrumb__list">
		<?php foreach ( $tmnhanphat_items as $tmnhanphat_index => $tmnhanphat_item ) : ?>
			<li class="breadcrumb__item">
				<?php if ( ! empty( $tmnhanphat_item['url'] ) ) : ?>
					<a href="<?php echo esc_url( $tmnhanphat_item['url'] ); ?>"><?php echo esc_html( $tmnhanphat_item['label'] ); ?></a>
				<?php else : ?>
					<span aria-current="page"><?php echo esc_html( $tmnhanphat_item['label'] ); ?></span>
				<?php endif; ?>
			</li>
		<?php endforeach; ?>
	</ol>
</nav>
