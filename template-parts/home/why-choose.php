<?php
/**
 * Why Choose Section ("Tại sao nên chọn Thang Máy Nhân Phát") — trang chủ.
 * Component độc lập (PROJECT_RULES.md mục 5). Toàn bộ nội dung từ Customizer panel
 * "Why Choose Home" (fixed-slot repeater, mục 24) — KHÔNG WP_Query/ACF/Meta Box,
 * không hardcode.
 *
 * Cấu trúc theo design: Background Image (1920×1291, cover, lazy) + Overlay tuỳ chọn
 * → Decoration tam giác xanh góc trái (828.5×857.5, thuần trang trí) → Header (Blue
 * Title / Red Title 2 dòng + Description) → Feature Slider (card 453×459, ảnh 423×290).
 *
 * Slider: tái sử dụng markup .tmnp-slider + assets/js/components/slider.js của Services
 * (generic, chỉ cần đúng markup + data-attribute). Section này KHÔNG có Dots/Arrows
 * (data-dots/data-arrows = false): autoplay tự trượt + drag/swipe trái phải. No-JS
 * fallback: track flex cuộn ngang + scroll-snap (progressive enhancement, không gắn
 * class ready từ PHP — bài học từ bug About Section).
 *
 * SEO (mục 14/23): Heading dùng H2 (H1 duy nhất thuộc về Hero), Card Title dùng H3.
 * Không in Schema — việc của Plugin SEO.
 *
 * @package TMNhanPhat
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! tmnhanphat_get_why_choose_mod( 'tmnhanphat_why_choose_enable' ) ) {
	return;
}

$tmnhanphat_wc_items = tmnhanphat_get_why_choose_items();

if ( empty( $tmnhanphat_wc_items ) ) {
	return; // Chưa có Feature Item nào — không render section trống (mục 15: không tạo DOM thừa).
}

$tmnhanphat_wc_section_id = tmnhanphat_get_why_choose_mod( 'tmnhanphat_why_choose_section_id' );
$tmnhanphat_wc_bg_image   = tmnhanphat_get_why_choose_mod( 'tmnhanphat_why_choose_bg_image' );
$tmnhanphat_wc_overlay_on = tmnhanphat_get_why_choose_mod( 'tmnhanphat_why_choose_overlay_enable' );

$tmnhanphat_wc_deco_on  = tmnhanphat_get_why_choose_mod( 'tmnhanphat_why_choose_deco_enable' );
$tmnhanphat_wc_deco_img = tmnhanphat_get_why_choose_mod( 'tmnhanphat_why_choose_deco_image' );

$tmnhanphat_wc_blue_text   = tmnhanphat_get_why_choose_mod( 'tmnhanphat_why_choose_heading_blue_text' );
$tmnhanphat_wc_red_text    = tmnhanphat_get_why_choose_mod( 'tmnhanphat_why_choose_heading_red_text' );
$tmnhanphat_wc_description = tmnhanphat_get_why_choose_mod( 'tmnhanphat_why_choose_desc_text' );

$tmnhanphat_wc_icon_enable = tmnhanphat_get_why_choose_mod( 'tmnhanphat_why_choose_icon_enable' );

$tmnhanphat_wc_slider_attrs = array(
	'data-tmnp-slider'    => '',
	'data-autoplay'       => tmnhanphat_get_why_choose_mod( 'tmnhanphat_why_choose_autoplay_enable' ) ? 'true' : 'false',
	'data-autoplay-speed' => absint( tmnhanphat_get_why_choose_mod( 'tmnhanphat_why_choose_autoplay_speed' ) ),
	'data-transition'     => absint( tmnhanphat_get_why_choose_mod( 'tmnhanphat_why_choose_transition_speed' ) ),
	'data-pause-hover'    => tmnhanphat_get_why_choose_mod( 'tmnhanphat_why_choose_pause_hover' ) ? 'true' : 'false',
	'data-infinite'       => tmnhanphat_get_why_choose_mod( 'tmnhanphat_why_choose_infinite' ) ? 'true' : 'false',
	'data-drag'           => tmnhanphat_get_why_choose_mod( 'tmnhanphat_why_choose_drag_enable' ) ? 'true' : 'false',
	// Dots BẬT (Taste review mục 2, owner duyệt): không có dots thì khi autoplay không
	// chạy (admin tắt / prefers-reduced-motion) các card ngoài khung nhìn vô hình hoàn
	// toàn — không affordance nào báo còn nội dung. Arrows vẫn tắt theo design.
	'data-dots'           => 'true',
	'data-arrows'         => 'false',
	'data-cards-desktop'  => max( 1, absint( tmnhanphat_get_why_choose_mod( 'tmnhanphat_why_choose_cards_desktop' ) ) ),
	'data-cards-tablet'   => max( 1, absint( tmnhanphat_get_why_choose_mod( 'tmnhanphat_why_choose_cards_tablet' ) ) ),
	'data-cards-mobile'   => max( 1, absint( tmnhanphat_get_why_choose_mod( 'tmnhanphat_why_choose_cards_mobile' ) ) ),
);

$tmnhanphat_wc_slider_attr_html = '';
foreach ( $tmnhanphat_wc_slider_attrs as $tmnhanphat_wc_attr_name => $tmnhanphat_wc_attr_value ) {
	$tmnhanphat_wc_slider_attr_html .= sprintf( ' %s="%s"', esc_attr( $tmnhanphat_wc_attr_name ), esc_attr( $tmnhanphat_wc_attr_value ) );
}
?>
<section
	class="why-choose-home"
	<?php if ( $tmnhanphat_wc_section_id ) : ?>
		id="<?php echo esc_attr( $tmnhanphat_wc_section_id ); ?>"
	<?php endif; ?>
>
	<?php if ( $tmnhanphat_wc_bg_image ) : ?>
		<div class="why-choose-home__background" aria-hidden="true">
			<?php
			// Ảnh nền render bằng <img> (không phải background-image CSS) để có lazy load
			// + srcset chuẩn WordPress; CSS object-fit:cover đảm nhiệm phủ kín không méo.
			echo tmnhanphat_get_why_choose_image_html( $tmnhanphat_wc_bg_image, '', 'full', 'why-choose-home__bg-image' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- helper đã escape toàn bộ bên trong.
			?>
		</div>
	<?php endif; ?>

	<?php if ( $tmnhanphat_wc_overlay_on ) : ?>
		<span class="why-choose-home__overlay" aria-hidden="true"></span>
	<?php endif; ?>

	<?php if ( $tmnhanphat_wc_deco_on && $tmnhanphat_wc_deco_img ) : ?>
		<img
			class="why-choose-home__decoration"
			src="<?php echo esc_url( $tmnhanphat_wc_deco_img ); ?>"
			alt=""
			aria-hidden="true"
			loading="lazy"
			decoding="async"
		/>
	<?php endif; ?>

	<div class="container why-choose-home__container">
		<header class="why-choose-home__header">
			<?php if ( $tmnhanphat_wc_blue_text || $tmnhanphat_wc_red_text ) : ?>
				<h2 class="why-choose-home__heading">
					<?php if ( $tmnhanphat_wc_blue_text ) : ?>
						<span class="why-choose-home__heading-blue"><?php echo esc_html( $tmnhanphat_wc_blue_text ); ?></span>
					<?php endif; ?>
					<?php if ( $tmnhanphat_wc_red_text ) : ?>
						<span class="why-choose-home__heading-red"><?php echo esc_html( $tmnhanphat_wc_red_text ); ?></span>
					<?php endif; ?>
				</h2>
			<?php endif; ?>

			<?php if ( $tmnhanphat_wc_description ) : ?>
				<p class="why-choose-home__description"><?php echo esc_html( $tmnhanphat_wc_description ); ?></p>
			<?php endif; ?>
		</header>

		<div
			class="why-choose-home__slider tmnp-slider"
			role="region"
			aria-roledescription="carousel"
			aria-label="<?php esc_attr_e( 'Lý do chọn chúng tôi', 'tmnhanphat' ); ?>"
			<?php echo $tmnhanphat_wc_slider_attr_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- từng name/value đã esc_attr() khi build ở trên. ?>
		>
			<div class="tmnp-slider__viewport" tabindex="0">
				<div class="tmnp-slider__track">
					<?php foreach ( $tmnhanphat_wc_items as $tmnhanphat_wc_item ) : ?>
						<article class="tmnp-slider__slide feature-card">
							<?php if ( $tmnhanphat_wc_icon_enable ) : ?>
								<span class="feature-card__icon" aria-hidden="true">
									<?php if ( $tmnhanphat_wc_item['icon'] ) : ?>
										<?php echo tmnhanphat_get_why_choose_image_html( $tmnhanphat_wc_item['icon'], '', 'thumbnail', 'feature-card__icon-image' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- helper đã escape toàn bộ bên trong. ?>
									<?php else : ?>
										<?php /* Icon mặc định của theme (check-circle) khi item chưa upload icon riêng. */ ?>
										<svg width="40" height="40" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" focusable="false">
											<circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="1.6" />
											<path d="M8 12.2l2.6 2.6L16 9.4" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" />
										</svg>
									<?php endif; ?>
								</span>
							<?php endif; ?>

							<h3 class="feature-card__title"><?php echo esc_html( $tmnhanphat_wc_item['title'] ); ?></h3>

							<?php if ( $tmnhanphat_wc_item['description'] ) : ?>
								<p class="feature-card__description"><?php echo esc_html( $tmnhanphat_wc_item['description'] ); ?></p>
							<?php endif; ?>

							<?php if ( $tmnhanphat_wc_item['btn_enable'] && $tmnhanphat_wc_item['btn_text'] && $tmnhanphat_wc_item['btn_url'] ) : ?>
								<a
									class="feature-card__button"
									href="<?php echo esc_url( $tmnhanphat_wc_item['btn_url'] ); ?>"
									<?php if ( $tmnhanphat_wc_item['btn_new_tab'] ) : ?>
										target="_blank" rel="noopener noreferrer"
									<?php endif; ?>
								><?php echo esc_html( $tmnhanphat_wc_item['btn_text'] ); ?></a>
							<?php endif; ?>

							<?php if ( $tmnhanphat_wc_item['image'] ) : ?>
								<div class="feature-card__media">
									<?php
									// Alt = Title của item (ảnh minh hoạ nội dung, không phải trang trí).
									echo tmnhanphat_get_why_choose_image_html( $tmnhanphat_wc_item['image'], $tmnhanphat_wc_item['title'], 'tmnhanphat-why-choose', 'feature-card__image' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- helper đã escape toàn bộ bên trong.
									?>
								</div>
							<?php endif; ?>
						</article>
					<?php endforeach; ?>
				</div>
			</div>
			<?php /* Dots do slider.js tự sinh (bật ở data-dots — xem chú thích trên); Arrows tắt theo design. */ ?>
		</div>
	</div>
</section>
