<?php
/**
 * Product Card component — dùng ở Products Home Section (render lần đầu trong
 * template-parts/home/products.php VÀ response AJAX inc/ajax.php) — định nghĩa
 * một lần duy nhất (PROJECT_RULES.md mục 5), gọi trong vòng lặp WP_Query.
 *
 * Cấu trúc theo Figma: Thumbnail 438×403 (crop center) → Title (line-clamp) →
 * Meta Thương hiệu/Tiêu chuẩn (ẩn dòng nếu meta rỗng) → Footer: "Giá: Liên hệ"
 * (trái) + nút "Chi tiết" (phải). Toàn bộ label/màu từ Customizer panel
 * "Product Home", không hardcode.
 *
 * @package TMNhanPhat
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$tmnhanphat_product_id = get_the_ID();

$tmnhanphat_brand    = tmnhanphat_get_products_mod( 'tmnhanphat_products_brand_enable' ) ? tmnhanphat_get_product_meta_value( $tmnhanphat_product_id, 'brand' ) : '';
$tmnhanphat_standard = tmnhanphat_get_products_mod( 'tmnhanphat_products_standard_enable' ) ? tmnhanphat_get_product_meta_value( $tmnhanphat_product_id, 'standard' ) : '';

$tmnhanphat_price_label  = tmnhanphat_get_products_mod( 'tmnhanphat_products_price_label' );
$tmnhanphat_contact_text = tmnhanphat_get_products_mod( 'tmnhanphat_products_contact_text' );
$tmnhanphat_button_text  = tmnhanphat_get_products_mod( 'tmnhanphat_products_button_text' );
?>
<article class="product-card">
	<a class="product-card__media" href="<?php the_permalink(); ?>" aria-hidden="true" tabindex="-1">
		<?php if ( has_post_thumbnail() ) : ?>
			<?php
			the_post_thumbnail( 'tmnhanphat-product-card', array(
				'class' => 'product-card__image',
				'alt'   => get_the_title(),
			) );
			?>
		<?php endif; ?>
	</a>

	<div class="product-card__body">
		<h3 class="product-card__title">
			<a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
		</h3>

		<?php if ( $tmnhanphat_brand ) : ?>
			<p class="product-card__meta">
				<?php echo esc_html( tmnhanphat_get_products_mod( 'tmnhanphat_products_brand_label' ) ); ?>:
				<span><?php echo esc_html( $tmnhanphat_brand ); ?></span>
			</p>
		<?php endif; ?>

		<?php if ( $tmnhanphat_standard ) : ?>
			<p class="product-card__meta">
				<?php echo esc_html( tmnhanphat_get_products_mod( 'tmnhanphat_products_standard_label' ) ); ?>:
				<span><?php echo esc_html( $tmnhanphat_standard ); ?></span>
			</p>
		<?php endif; ?>

		<div class="product-card__footer">
			<p class="product-card__price">
				<strong><?php echo esc_html( $tmnhanphat_price_label ); ?></strong>
				<a class="product-card__contact" href="<?php echo esc_url( tmnhanphat_get_products_contact_url() ); ?>"><?php echo esc_html( $tmnhanphat_contact_text ); ?></a>
			</p>

			<a class="product-card__button" href="<?php the_permalink(); ?>">
				<?php echo esc_html( $tmnhanphat_button_text ); ?>
				<span class="screen-reader-text"><?php the_title(); ?></span>
			</a>
		</div>
	</div>
</article>
