<?php
/**
 * Services Section (Dịch vụ) — trang chủ. Component độc lập (PROJECT_RULES.md mục 5).
 * Dữ liệu từ CPT tmnp_service (inc/post-types.php) qua tmnhanphat_get_services_query();
 * mọi tuỳ chỉnh hiển thị từ Customizer panel "Service Home" — không hardcode.
 *
 * Slider: markup thuần là 1 track flex cuộn ngang được (overflow-x + scroll-snap) — KHÔNG
 * phụ thuộc JS để đọc nội dung (progressive enhancement). assets/js/components/slider.js
 * (nếu chạy được) nâng cấp thành slider transform: autoplay/loop/drag/keyboard/dots,
 * và tự gắn class js-services-ready lên section (KHÔNG gắn sẵn từ PHP — bài học từ bug
 * progressive-enhancement của About Section).
 *
 * SEO (mục 14/23): Heading dùng H2 (H1 duy nhất thuộc về Hero), Card Title dùng H3,
 * CTA là <a>. Không in Schema — việc của Plugin SEO.
 *
 * @package TMNhanPhat
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! tmnhanphat_get_services_mod( 'tmnhanphat_services_enable' ) ) {
	return;
}

$tmnhanphat_services_query = tmnhanphat_get_services_query();

if ( ! $tmnhanphat_services_query->have_posts() ) {
	return; // Chưa có dịch vụ nào — không render section trống (mục 15: không tạo DOM thừa).
}

$tmnhanphat_red_text    = tmnhanphat_get_services_mod( 'tmnhanphat_services_heading_red_text' );
$tmnhanphat_blue_text   = tmnhanphat_get_services_mod( 'tmnhanphat_services_heading_blue_text' );
$tmnhanphat_description = tmnhanphat_get_services_mod( 'tmnhanphat_services_description_text' );
$tmnhanphat_section_id  = tmnhanphat_get_services_mod( 'tmnhanphat_services_section_id' );

$tmnhanphat_cta_enable = tmnhanphat_get_services_mod( 'tmnhanphat_services_cta_enable' );
$tmnhanphat_cta_text   = tmnhanphat_get_services_mod( 'tmnhanphat_services_cta_text' );

$tmnhanphat_slider_attrs = array(
	'data-tmnp-slider'    => '',
	'data-autoplay'       => tmnhanphat_get_services_mod( 'tmnhanphat_services_autoplay_enable' ) ? 'true' : 'false',
	'data-autoplay-speed' => absint( tmnhanphat_get_services_mod( 'tmnhanphat_services_autoplay_speed' ) ),
	'data-transition'     => absint( tmnhanphat_get_services_mod( 'tmnhanphat_services_transition_speed' ) ),
	'data-pause-hover'    => tmnhanphat_get_services_mod( 'tmnhanphat_services_pause_hover' ) ? 'true' : 'false',
	'data-infinite'       => tmnhanphat_get_services_mod( 'tmnhanphat_services_infinite' ) ? 'true' : 'false',
	'data-arrows'         => tmnhanphat_get_services_mod( 'tmnhanphat_services_show_arrows' ) ? 'true' : 'false',
	'data-dots'           => tmnhanphat_get_services_mod( 'tmnhanphat_services_show_dots' ) ? 'true' : 'false',
	'data-drag'           => tmnhanphat_get_services_mod( 'tmnhanphat_services_drag_enable' ) ? 'true' : 'false',
	'data-cards-desktop'  => absint( tmnhanphat_get_services_mod( 'tmnhanphat_services_cards_desktop' ) ),
	'data-cards-tablet'   => absint( tmnhanphat_get_services_mod( 'tmnhanphat_services_cards_tablet' ) ),
	'data-cards-mobile'   => absint( tmnhanphat_get_services_mod( 'tmnhanphat_services_cards_mobile' ) ),
);

$tmnhanphat_slider_attr_html = '';
foreach ( $tmnhanphat_slider_attrs as $tmnhanphat_attr_name => $tmnhanphat_attr_value ) {
	$tmnhanphat_slider_attr_html .= sprintf( ' %s="%s"', esc_attr( $tmnhanphat_attr_name ), esc_attr( $tmnhanphat_attr_value ) );
}
?>
<section
	class="services-home"
	<?php if ( $tmnhanphat_section_id ) : ?>
		id="<?php echo esc_attr( $tmnhanphat_section_id ); ?>"
	<?php endif; ?>
>
	<div class="container services-home__container">
		<header class="services-home__header">
			<?php if ( $tmnhanphat_red_text || $tmnhanphat_blue_text ) : ?>
				<h2 class="services-home__heading">
					<?php if ( $tmnhanphat_red_text ) : ?>
						<span class="services-home__heading-red"><?php echo esc_html( $tmnhanphat_red_text ); ?></span>
					<?php endif; ?>
					<?php if ( $tmnhanphat_blue_text ) : ?>
						<span class="services-home__heading-blue"><?php echo esc_html( $tmnhanphat_blue_text ); ?></span>
					<?php endif; ?>
				</h2>
			<?php endif; ?>

			<?php if ( $tmnhanphat_description ) : ?>
				<p class="services-home__description"><?php echo esc_html( $tmnhanphat_description ); ?></p>
			<?php endif; ?>
		</header>

		<div
			class="services-home__slider tmnp-slider"
			role="region"
			aria-roledescription="carousel"
			aria-label="<?php esc_attr_e( 'Danh sách dịch vụ', 'tmnhanphat' ); ?>"
			<?php echo $tmnhanphat_slider_attr_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- từng name/value đã esc_attr() khi build ở trên. ?>
		>
			<div class="tmnp-slider__viewport" tabindex="0">
				<div class="tmnp-slider__track">
					<?php
					while ( $tmnhanphat_services_query->have_posts() ) :
						$tmnhanphat_services_query->the_post();
						$tmnhanphat_service_url = tmnhanphat_get_service_link( get_the_ID() );
						?>
						<article class="tmnp-slider__slide service-card">
							<a class="service-card__link" href="<?php echo esc_url( $tmnhanphat_service_url ); ?>">
								<div class="service-card__media">
									<?php if ( has_post_thumbnail() ) : ?>
										<?php
										the_post_thumbnail( 'tmnhanphat-service-card', array(
											'class' => 'service-card__image',
											'alt'   => get_the_title(),
										) );
										?>
									<?php endif; ?>
									<span class="service-card__overlay" aria-hidden="true"></span>
								</div>

								<div class="service-card__body">
									<h3 class="service-card__title"><?php the_title(); ?></h3>

									<?php if ( has_excerpt() ) : ?>
										<p class="service-card__excerpt"><?php echo esc_html( get_the_excerpt() ); ?></p>
									<?php endif; ?>

									<span class="service-card__arrow" aria-hidden="true">
										<svg width="20" height="14" viewBox="0 0 20 14" fill="none" xmlns="http://www.w3.org/2000/svg" focusable="false">
											<path d="M13 1l6 6-6 6M19 7H1" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
										</svg>
									</span>
								</div>
							</a>
						</article>
					<?php endwhile; ?>
					<?php wp_reset_postdata(); ?>
				</div>
			</div>
			<?php /* Dots/Arrows do slider.js tự sinh khi bật (không in sẵn DOM thừa — mục 15). */ ?>
		</div>

		<?php if ( $tmnhanphat_cta_enable && $tmnhanphat_cta_text ) : ?>
			<div class="services-home__cta-wrap">
				<a class="services-home__cta" href="<?php echo esc_url( tmnhanphat_get_services_cta_url() ); ?>">
					<?php echo esc_html( $tmnhanphat_cta_text ); ?>
				</a>
			</div>
		<?php endif; ?>
	</div>
</section>
