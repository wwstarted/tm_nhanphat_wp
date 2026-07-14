<?php
/**
 * Products Section (Sản phẩm của doanh nghiệp) — trang chủ. Component độc lập
 * (PROJECT_RULES.md mục 5). Dữ liệu là POST THƯỜNG thuộc Category cha "Sản phẩm"
 * (không phải CPT); mọi tuỳ chỉnh hiển thị từ Customizer panel "Product Home".
 *
 * Category Navigation: các Sub Category của Category cha, tab active nền xanh/chữ
 * trắng. Click tab → AJAX đổi danh sách (assets/js/components/products.js), fallback
 * KHÔNG JS = link thẳng tới trang archive của category đó (href thật, không phải #).
 *
 * Slider dạng LƯỚI: mỗi slide = Cols × Rows card (Desktop 3×2=6, Tablet 2×2=4,
 * Mobile 1×1=1). PHP in danh sách card PHẲNG trong track — không JS thì track là
 * grid hiển thị đủ mọi card (progressive enhancement); products.js (nếu chạy) tự
 * chia trang theo breakpoint, gắn .is-enhanced + js-products-ready (KHÔNG gắn sẵn
 * từ PHP — bài học từ bug progressive-enhancement của About Section).
 *
 * SEO (mục 14/23): Heading dùng H2 (H1 duy nhất thuộc về Hero), Card Title dùng H3.
 * Không in Schema — việc của Plugin SEO.
 *
 * @package TMNhanPhat
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! tmnhanphat_get_products_mod( 'tmnhanphat_products_enable' ) ) {
	return;
}

$tmnhanphat_parent_id  = tmnhanphat_get_products_parent_id();
$tmnhanphat_categories = tmnhanphat_get_product_categories();

if ( ! $tmnhanphat_parent_id ) {
	return; // Chưa cấu hình Parent Category — không render section trống.
}

// Tab active mặc định = Sub Category đầu tiên; nếu cha chưa có con thì query chính cha.
$tmnhanphat_active_term = ! empty( $tmnhanphat_categories ) ? (int) $tmnhanphat_categories[0]->term_id : $tmnhanphat_parent_id;

$tmnhanphat_products_query = tmnhanphat_get_products_query( $tmnhanphat_active_term );

if ( ! $tmnhanphat_products_query->have_posts() && empty( $tmnhanphat_categories ) ) {
	return; // Không có danh mục con lẫn sản phẩm — không render section trống (mục 15).
}

$tmnhanphat_red_text    = tmnhanphat_get_products_mod( 'tmnhanphat_products_heading_red_text' );
$tmnhanphat_blue_text   = tmnhanphat_get_products_mod( 'tmnhanphat_products_heading_blue_text' );
$tmnhanphat_description = tmnhanphat_get_products_mod( 'tmnhanphat_products_description_text' );
$tmnhanphat_section_id  = tmnhanphat_get_products_mod( 'tmnhanphat_products_section_id' );

$tmnhanphat_decoration_on  = tmnhanphat_get_products_mod( 'tmnhanphat_products_decoration_enable' );
$tmnhanphat_decoration_img = tmnhanphat_get_products_mod( 'tmnhanphat_products_decoration_image' );

$tmnhanphat_slider_attrs = array(
	'data-tmnp-products'  => '',
	'data-autoplay'       => tmnhanphat_get_products_mod( 'tmnhanphat_products_autoplay_enable' ) ? 'true' : 'false',
	'data-delay'          => absint( tmnhanphat_get_products_mod( 'tmnhanphat_products_autoplay_delay' ) ),
	'data-transition'     => absint( tmnhanphat_get_products_mod( 'tmnhanphat_products_transition_speed' ) ),
	'data-loop'           => tmnhanphat_get_products_mod( 'tmnhanphat_products_infinite' ) ? 'true' : 'false',
	'data-pause-hover'    => tmnhanphat_get_products_mod( 'tmnhanphat_products_pause_hover' ) ? 'true' : 'false',
	'data-drag'           => tmnhanphat_get_products_mod( 'tmnhanphat_products_drag_enable' ) ? 'true' : 'false',
	'data-dots'           => tmnhanphat_get_products_mod( 'tmnhanphat_products_dots_enable' ) ? 'true' : 'false',
	'data-cols-desktop'   => max( 1, absint( tmnhanphat_get_products_mod( 'tmnhanphat_products_cols_desktop' ) ) ),
	'data-cols-tablet'    => max( 1, absint( tmnhanphat_get_products_mod( 'tmnhanphat_products_cols_tablet' ) ) ),
	'data-cols-mobile'    => max( 1, absint( tmnhanphat_get_products_mod( 'tmnhanphat_products_cols_mobile' ) ) ),
	'data-rows-desktop'   => max( 1, absint( tmnhanphat_get_products_mod( 'tmnhanphat_products_rows_desktop' ) ) ),
	'data-rows-tablet'    => max( 1, absint( tmnhanphat_get_products_mod( 'tmnhanphat_products_rows_tablet' ) ) ),
	'data-rows-mobile'    => max( 1, absint( tmnhanphat_get_products_mod( 'tmnhanphat_products_rows_mobile' ) ) ),
);

$tmnhanphat_slider_attr_html = '';
foreach ( $tmnhanphat_slider_attrs as $tmnhanphat_attr_name => $tmnhanphat_attr_value ) {
	$tmnhanphat_slider_attr_html .= sprintf( ' %s="%s"', esc_attr( $tmnhanphat_attr_name ), esc_attr( $tmnhanphat_attr_value ) );
}
?>
<section
	class="products-home"
	<?php if ( $tmnhanphat_section_id ) : ?>
		id="<?php echo esc_attr( $tmnhanphat_section_id ); ?>"
	<?php endif; ?>
>
	<?php if ( $tmnhanphat_decoration_on && $tmnhanphat_decoration_img ) : ?>
		<img
			class="products-home__decoration"
			src="<?php echo esc_url( $tmnhanphat_decoration_img ); ?>"
			alt=""
			aria-hidden="true"
			loading="lazy"
			decoding="async"
		/>
	<?php endif; ?>

	<div class="container products-home__container">
		<header class="products-home__header">
			<?php if ( $tmnhanphat_red_text || $tmnhanphat_blue_text ) : ?>
				<h2 class="products-home__heading">
					<?php if ( $tmnhanphat_red_text ) : ?>
						<span class="products-home__heading-red"><?php echo esc_html( $tmnhanphat_red_text ); ?></span>
					<?php endif; ?>
					<?php if ( $tmnhanphat_blue_text ) : ?>
						<span class="products-home__heading-blue"><?php echo esc_html( $tmnhanphat_blue_text ); ?></span>
					<?php endif; ?>
				</h2>
			<?php endif; ?>

			<?php if ( $tmnhanphat_description ) : ?>
				<p class="products-home__description"><?php echo esc_html( $tmnhanphat_description ); ?></p>
			<?php endif; ?>
		</header>

		<?php if ( ! empty( $tmnhanphat_categories ) ) : ?>
			<nav class="products-home__cats" aria-label="<?php esc_attr_e( 'Danh mục sản phẩm', 'tmnhanphat' ); ?>">
				<ul class="products-home__cat-list">
					<?php foreach ( $tmnhanphat_categories as $tmnhanphat_cat ) : ?>
						<?php $tmnhanphat_is_active = (int) $tmnhanphat_cat->term_id === $tmnhanphat_active_term; ?>
						<li class="products-home__cat-item">
							<a
								class="products-home__cat-link<?php echo $tmnhanphat_is_active ? ' is-active' : ''; ?>"
								href="<?php echo esc_url( get_term_link( $tmnhanphat_cat ) ); ?>"
								data-term-id="<?php echo esc_attr( $tmnhanphat_cat->term_id ); ?>"
								<?php if ( $tmnhanphat_is_active ) : ?>
									aria-current="true"
								<?php endif; ?>
							><?php echo esc_html( $tmnhanphat_cat->name ); ?></a>
						</li>
					<?php endforeach; ?>
				</ul>
			</nav>
		<?php endif; ?>

		<div
			class="products-home__slider tmnp-products-slider"
			role="region"
			aria-label="<?php esc_attr_e( 'Danh sách sản phẩm', 'tmnhanphat' ); ?>"
			<?php echo $tmnhanphat_slider_attr_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- từng name/value đã esc_attr() khi build ở trên. ?>
		>
			<div class="tmnp-products-slider__viewport" tabindex="0">
				<div class="tmnp-products-slider__track" aria-live="polite">
					<?php
					while ( $tmnhanphat_products_query->have_posts() ) :
						$tmnhanphat_products_query->the_post();
						get_template_part( 'template-parts/components/product-card' );
					endwhile;
					wp_reset_postdata();
					?>
				</div>
			</div>
			<p class="products-home__empty" hidden><?php esc_html_e( 'Chưa có sản phẩm nào trong danh mục này.', 'tmnhanphat' ); ?></p>
			<?php /* Dots do products.js tự sinh khi bật (không in sẵn DOM thừa — mục 15). */ ?>
		</div>
	</div>
</section>
