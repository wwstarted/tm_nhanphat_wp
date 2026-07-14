<?php
/**
 * Featured News Section ("Tin tức nổi bật") — trang chủ. Component độc lập
 * (PROJECT_RULES.md mục 5). Dữ liệu là POST THƯỜNG (wp_posts) theo Category chọn
 * trong Customizer panel "Featured News Home" (WP_Query — không CPT/Taxonomy mới,
 * không hardcode ID); mọi tuỳ chỉnh hiển thị từ Customizer — không hardcode.
 *
 * Cấu trúc theo design: nền + Background Image (trang trí) + Decoration tam giác
 * xanh góc trên phải (381×464) → Header (Small Title + Blue/Red Title + gạch đôi
 * đỏ/xanh TÁI SỬ DỤNG style review-home__heading + Description) → Slider 3/2/1 card
 * → Dots + Arrows.
 *
 * Chưa có bài nào → tmnhanphat_get_news_items() tự đổ Demo Data (tầng hiển thị,
 * không ghi DB — mục 13). Slider tái sử dụng .tmnp-slider + slider.js.
 *
 * SEO (mục 14/23): Heading dùng H2, Card Title dùng H3. Không in Schema.
 *
 * @package TMNhanPhat
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! tmnhanphat_get_news_mod( 'tmnhanphat_news_enable' ) ) {
	return;
}

$tmnhanphat_nw_items = tmnhanphat_get_news_items();

if ( empty( $tmnhanphat_nw_items ) ) {
	return; // Không có bài + không demo (chỉ khi Demo bị lọc hết) — không render trống (mục 15).
}

$tmnhanphat_nw_section_id  = tmnhanphat_get_news_mod( 'tmnhanphat_news_section_id' );
$tmnhanphat_nw_deco_on     = tmnhanphat_get_news_mod( 'tmnhanphat_news_deco_enable' );
$tmnhanphat_nw_deco_image  = tmnhanphat_get_news_mod( 'tmnhanphat_news_deco_image' );

$tmnhanphat_nw_small_title = tmnhanphat_get_news_mod( 'tmnhanphat_news_small_title' );
$tmnhanphat_nw_blue_text   = tmnhanphat_get_news_mod( 'tmnhanphat_news_heading_blue_text' );
$tmnhanphat_nw_red_text    = tmnhanphat_get_news_mod( 'tmnhanphat_news_heading_red_text' );
$tmnhanphat_nw_description  = tmnhanphat_get_news_mod( 'tmnhanphat_news_desc_text' );
$tmnhanphat_nw_reuse       = tmnhanphat_get_news_mod( 'tmnhanphat_news_reuse_heading' );

$tmnhanphat_nw_show_date   = tmnhanphat_get_news_mod( 'tmnhanphat_news_show_date' );
$tmnhanphat_nw_show_author = tmnhanphat_get_news_mod( 'tmnhanphat_news_show_author' );
$tmnhanphat_nw_readmore    = tmnhanphat_get_news_mod( 'tmnhanphat_news_readmore_text' );

// Reuse style review-home__heading (mục 4): thêm class đó lên H2 để lấy gạch đôi
// đỏ/xanh + typography chung; header modifier --reuse cho CSS biết đang tái sử dụng.
$tmnhanphat_nw_heading_class = 'news-home__heading' . ( $tmnhanphat_nw_reuse ? ' review-home__heading' : '' );
$tmnhanphat_nw_header_class  = 'news-home__header' . ( $tmnhanphat_nw_reuse ? ' news-home__header--reuse' : '' );

$tmnhanphat_nw_slider_attrs = array(
	'data-tmnp-slider'    => '',
	'data-autoplay'       => tmnhanphat_get_news_mod( 'tmnhanphat_news_autoplay_enable' ) ? 'true' : 'false',
	'data-autoplay-speed' => absint( tmnhanphat_get_news_mod( 'tmnhanphat_news_autoplay_speed' ) ),
	'data-transition'     => absint( tmnhanphat_get_news_mod( 'tmnhanphat_news_transition_speed' ) ),
	'data-pause-hover'    => tmnhanphat_get_news_mod( 'tmnhanphat_news_pause_hover' ) ? 'true' : 'false',
	'data-infinite'       => tmnhanphat_get_news_mod( 'tmnhanphat_news_infinite' ) ? 'true' : 'false',
	'data-arrows'         => tmnhanphat_get_news_mod( 'tmnhanphat_news_show_arrows' ) ? 'true' : 'false',
	'data-dots'           => tmnhanphat_get_news_mod( 'tmnhanphat_news_show_dots' ) ? 'true' : 'false',
	'data-drag'           => tmnhanphat_get_news_mod( 'tmnhanphat_news_drag_enable' ) ? 'true' : 'false',
	'data-cards-desktop'  => 3,
	'data-cards-tablet'   => 2,
	'data-cards-mobile'   => 1,
);

$tmnhanphat_nw_attr_html = '';
foreach ( $tmnhanphat_nw_slider_attrs as $tmnhanphat_nw_attr_name => $tmnhanphat_nw_attr_value ) {
	$tmnhanphat_nw_attr_html .= sprintf( ' %s="%s"', esc_attr( $tmnhanphat_nw_attr_name ), esc_attr( $tmnhanphat_nw_attr_value ) );
}
?>
<section
	class="news-home tmnp-slider--news-scope"
	<?php if ( $tmnhanphat_nw_section_id ) : ?>
		id="<?php echo esc_attr( $tmnhanphat_nw_section_id ); ?>"
	<?php endif; ?>
>
	<?php if ( $tmnhanphat_nw_deco_on && $tmnhanphat_nw_deco_image ) : ?>
		<img
			class="news-home__decoration"
			src="<?php echo esc_url( $tmnhanphat_nw_deco_image ); ?>"
			alt=""
			aria-hidden="true"
			loading="lazy"
			decoding="async"
		/>
	<?php endif; ?>

	<div class="container news-home__container">
		<header class="<?php echo esc_attr( $tmnhanphat_nw_header_class ); ?>">
			<?php if ( $tmnhanphat_nw_small_title ) : ?>
				<span class="news-home__small"><?php echo esc_html( $tmnhanphat_nw_small_title ); ?></span>
			<?php endif; ?>

			<?php if ( $tmnhanphat_nw_blue_text || $tmnhanphat_nw_red_text ) : ?>
				<h2 class="<?php echo esc_attr( $tmnhanphat_nw_heading_class ); ?>">
					<?php if ( $tmnhanphat_nw_blue_text ) : ?>
						<span class="news-home__heading-blue"><?php echo esc_html( $tmnhanphat_nw_blue_text ); ?></span>
					<?php endif; ?>
					<?php if ( $tmnhanphat_nw_red_text ) : ?>
						<span class="news-home__heading-red"><?php echo esc_html( $tmnhanphat_nw_red_text ); ?></span>
					<?php endif; ?>
				</h2>
			<?php endif; ?>

			<?php if ( $tmnhanphat_nw_description ) : ?>
				<p class="news-home__description"><?php echo esc_html( $tmnhanphat_nw_description ); ?></p>
			<?php endif; ?>
		</header>

		<div
			class="news-home__slider tmnp-slider"
			role="region"
			aria-roledescription="carousel"
			aria-label="<?php esc_attr_e( 'Tin tức nổi bật', 'tmnhanphat' ); ?>"
			<?php echo $tmnhanphat_nw_attr_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- từng name/value đã esc_attr() khi build ở trên. ?>
		>
			<div class="tmnp-slider__viewport" tabindex="0">
				<div class="tmnp-slider__track">
					<?php foreach ( $tmnhanphat_nw_items as $tmnhanphat_nw_item ) : ?>
						<article class="tmnp-slider__slide news-card">
							<div class="news-card__media">
								<?php if ( $tmnhanphat_nw_item['permalink'] ) : ?>
									<a class="news-card__media-link" href="<?php echo esc_url( $tmnhanphat_nw_item['permalink'] ); ?>" aria-hidden="true" tabindex="-1">
								<?php else : ?>
									<span class="news-card__media-link">
								<?php endif; ?>

								<?php if ( $tmnhanphat_nw_item['has_thumb'] ) : ?>
									<?php
									echo wp_get_attachment_image( $tmnhanphat_nw_item['thumb_id'], 'tmnhanphat-news-card', false, array(
										'class' => 'news-card__image',
										'alt'   => $tmnhanphat_nw_item['title'],
									) );
									?>
								<?php else : ?>
									<span class="news-card__image news-card__image--placeholder" aria-hidden="true"></span>
								<?php endif; ?>

								<?php echo $tmnhanphat_nw_item['permalink'] ? '</a>' : '</span>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- chuỗi tĩnh đóng thẻ. ?>
							</div>

							<div class="news-card__body">
								<?php if ( $tmnhanphat_nw_show_date || $tmnhanphat_nw_show_author ) : ?>
									<div class="news-card__meta">
										<?php if ( $tmnhanphat_nw_show_date ) : ?>
											<span class="news-card__date"><?php echo esc_html( $tmnhanphat_nw_item['date'] ); ?></span>
										<?php endif; ?>
										<?php if ( $tmnhanphat_nw_show_author ) : ?>
											<span class="news-card__author">
												<?php
												/* translators: %s: tên tác giả bài viết */
												printf( esc_html__( 'Tác giả: %s', 'tmnhanphat' ), esc_html( $tmnhanphat_nw_item['author'] ) );
												?>
											</span>
										<?php endif; ?>
									</div>
								<?php endif; ?>

								<h3 class="news-card__title">
									<?php if ( $tmnhanphat_nw_item['permalink'] ) : ?>
										<a href="<?php echo esc_url( $tmnhanphat_nw_item['permalink'] ); ?>"><?php echo esc_html( $tmnhanphat_nw_item['title'] ); ?></a>
									<?php else : ?>
										<?php echo esc_html( $tmnhanphat_nw_item['title'] ); ?>
									<?php endif; ?>
								</h3>

								<?php if ( $tmnhanphat_nw_item['excerpt'] ) : ?>
									<p class="news-card__excerpt"><?php echo $tmnhanphat_nw_item['excerpt']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- helper trả chuỗi đã esc_html() sẵn. ?></p>
								<?php endif; ?>

								<?php if ( $tmnhanphat_nw_readmore ) : ?>
									<?php if ( $tmnhanphat_nw_item['permalink'] ) : ?>
										<a class="news-card__readmore" href="<?php echo esc_url( $tmnhanphat_nw_item['permalink'] ); ?>">
											<?php echo esc_html( $tmnhanphat_nw_readmore ); ?>
											<span class="screen-reader-text"><?php echo esc_html( $tmnhanphat_nw_item['title'] ); ?></span>
										</a>
									<?php else : ?>
										<span class="news-card__readmore"><?php echo esc_html( $tmnhanphat_nw_readmore ); ?></span>
									<?php endif; ?>
								<?php endif; ?>
							</div>
						</article>
					<?php endforeach; ?>
				</div>
			</div>
			<?php /* Dots/Arrows do slider.js tự sinh khi bật (không in sẵn DOM thừa — mục 15). */ ?>
		</div>
	</div>
</section>
