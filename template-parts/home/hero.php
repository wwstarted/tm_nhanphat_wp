<?php
/**
 * Hero Banner (trang chủ) — 2 phần trong 1 section:
 *  1) .hero__stage: ảnh nền + overlay + content căn GIỮA (subtitle → heading → desc → CTA).
 *     Desktop thường (992–2559px) cao đúng 100vh (ôm 1 màn hình); ngoài dải đó (mobile/
 *     tablet, và màn ≥2560) cao theo nội dung — tránh khoảng trắng thừa khi viewport quá cao.
 *  2) .hero__stats: card 4 cột STRADDLE — nửa trên đè đáy stage, nửa dưới tràn xuống nền
 *     phía dưới (chỉ desktop; tablet/mobile card nằm bình thường dưới stage). Nội dung mỗi
 *     cột (number/badge/desc) căn giữa.
 *
 * Toàn bộ nội dung/màu/kích thước lấy từ Customizer panel "Homepage" → Hero
 * (inc/customizer/hero-customizer.php), đọc qua tmnhanphat_get_hero_mod() — không hardcode.
 *
 * Heading dùng <h1> — H1 DUY NHẤT của trang chủ (PROJECT_RULES.md mục 14).
 *
 * @package TMNhanPhat
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$tmnhanphat_bg_image = tmnhanphat_get_hero_mod( 'tmnhanphat_hero_bg_image' );

$tmnhanphat_subtitle_enable = tmnhanphat_get_hero_mod( 'tmnhanphat_hero_subtitle_enable' );
$tmnhanphat_subtitle_text   = tmnhanphat_get_hero_mod( 'tmnhanphat_hero_subtitle_text' );

$tmnhanphat_heading_text = tmnhanphat_get_hero_mod( 'tmnhanphat_hero_heading_text' );
$tmnhanphat_description  = tmnhanphat_get_hero_mod( 'tmnhanphat_hero_description_text' );

$tmnhanphat_btn_primary_enable   = tmnhanphat_get_hero_mod( 'tmnhanphat_hero_btn_primary_enable' );
$tmnhanphat_btn_secondary_enable = tmnhanphat_get_hero_mod( 'tmnhanphat_hero_btn_secondary_enable' );

$tmnhanphat_hero_stats = array_filter(
	tmnhanphat_get_hero_stats(),
	static function ( $stat ) {
		return $stat['enable'];
	}
);

$tmnhanphat_animation_enable = tmnhanphat_get_hero_mod( 'tmnhanphat_hero_animation_enable' );
$tmnhanphat_counter_enable   = tmnhanphat_get_hero_mod( 'tmnhanphat_hero_counter_animation_enable' );
?>
<section
	class="hero"
	<?php if ( $tmnhanphat_animation_enable ) : ?>
		data-hero-animate="true"
	<?php endif; ?>
>
	<div class="hero__stage">
		<div class="hero__background">
			<?php
			if ( $tmnhanphat_bg_image ) :
				// Dùng wp_get_attachment_image (size 'full') để WP TỰ SINH srcset từ các bản
				// có sẵn (768/1024/1536/1600/1920) + sizes=100vw → trình duyệt chọn bản NÉT
				// NHẤT theo viewport × DPR (màn retina/lớn không bị phóng bản nhỏ → hết mờ).
				// Layout không đổi (vẫn .hero__bg-image object-fit:cover). Fallback <img src>
				// cứng nếu ảnh là URL ngoài Media Library (không tra được attachment ID).
				$tmnhanphat_bg_id = attachment_url_to_postid( $tmnhanphat_bg_image );
				if ( $tmnhanphat_bg_id ) {
					echo wp_get_attachment_image(
						$tmnhanphat_bg_id,
						'full',
						false,
						array(
							'class'         => 'hero__bg-image',
							'alt'           => '',
							'sizes'         => '100vw',
							'loading'       => 'eager',
							'fetchpriority' => 'high',
							'decoding'      => 'async',
						)
					);
				} else {
					?>
					<img
						class="hero__bg-image"
						src="<?php echo esc_url( $tmnhanphat_bg_image ); ?>"
						alt=""
						loading="eager"
						fetchpriority="high"
						decoding="async"
					/>
					<?php
				}
			endif;
			?>
		</div>

		<?php if ( tmnhanphat_get_hero_mod( 'tmnhanphat_hero_overlay_enable' ) ) : ?>
			<div class="hero__overlay" aria-hidden="true"></div>
		<?php endif; ?>

		<div class="hero__inner">
			<div class="hero__content">
				<?php if ( $tmnhanphat_subtitle_enable && $tmnhanphat_subtitle_text ) : ?>
					<p class="hero__subtitle"><?php echo esc_html( $tmnhanphat_subtitle_text ); ?></p>
				<?php endif; ?>

				<?php if ( $tmnhanphat_heading_text ) : ?>
					<h1 class="hero__heading"><?php echo esc_html( $tmnhanphat_heading_text ); ?></h1>
				<?php endif; ?>

				<?php if ( $tmnhanphat_description ) : ?>
					<?php
					$tmnhanphat_desc_class = 'hero__description';
					if ( tmnhanphat_get_hero_mod( 'tmnhanphat_hero_description_clamp_enable' ) ) {
						$tmnhanphat_desc_class .= ' hero__description--clamp';
					}
					?>
					<p class="<?php echo esc_attr( $tmnhanphat_desc_class ); ?>"><?php echo esc_html( $tmnhanphat_description ); ?></p>
				<?php endif; ?>

				<?php if ( $tmnhanphat_btn_primary_enable || $tmnhanphat_btn_secondary_enable ) : ?>
					<div class="hero__cta">
						<?php if ( $tmnhanphat_btn_primary_enable && tmnhanphat_get_hero_mod( 'tmnhanphat_hero_btn_primary_text' ) ) : ?>
							<a
								class="hero__btn hero__btn--primary"
								href="<?php echo esc_url( tmnhanphat_get_hero_mod( 'tmnhanphat_hero_btn_primary_url' ) ); ?>"
								<?php if ( tmnhanphat_get_hero_mod( 'tmnhanphat_hero_btn_primary_new_tab' ) ) : ?>
									target="_blank" rel="noopener noreferrer"
								<?php endif; ?>
							><?php echo esc_html( tmnhanphat_get_hero_mod( 'tmnhanphat_hero_btn_primary_text' ) ); ?></a>
						<?php endif; ?>

						<?php if ( $tmnhanphat_btn_secondary_enable && tmnhanphat_get_hero_mod( 'tmnhanphat_hero_btn_secondary_text' ) ) : ?>
							<a
								class="hero__btn hero__btn--secondary"
								href="<?php echo esc_url( tmnhanphat_get_hero_mod( 'tmnhanphat_hero_btn_secondary_url' ) ); ?>"
								<?php if ( tmnhanphat_get_hero_mod( 'tmnhanphat_hero_btn_secondary_new_tab' ) ) : ?>
									target="_blank" rel="noopener noreferrer"
								<?php endif; ?>
							><?php echo esc_html( tmnhanphat_get_hero_mod( 'tmnhanphat_hero_btn_secondary_text' ) ); ?></a>
						<?php endif; ?>
					</div>
				<?php endif; ?>
			</div>
		</div>
	</div>

	<?php if ( ! empty( $tmnhanphat_hero_stats ) ) : ?>
		<div class="hero__stats-wrap">
			<div class="hero__stats">
				<?php foreach ( array_values( $tmnhanphat_hero_stats ) as $tmnhanphat_index => $tmnhanphat_stat ) : ?>
					<?php $tmnhanphat_number_parts = tmnhanphat_get_hero_number_parts( $tmnhanphat_stat['number'] ); ?>
					<div class="hero__stat" style="--stagger-index: <?php echo esc_attr( $tmnhanphat_index ); ?>;">
						<?php if ( $tmnhanphat_stat['icon'] ) : ?>
							<img class="hero__stat-icon" src="<?php echo esc_url( $tmnhanphat_stat['icon'] ); ?>" alt="" loading="lazy" width="24" height="24" />
						<?php endif; ?>

						<?php /* Luôn in số liệu cuối cùng ngay từ PHP (progressive enhancement — không JS vẫn đúng số).
						JS (nếu chạy được) sẽ tự reset về 0 rồi đếm lên tới data-count-to khi cuộn tới. */ ?>
						<p class="hero__stat-number"
							<?php if ( $tmnhanphat_counter_enable && null !== $tmnhanphat_number_parts['value'] ) : ?>
								data-count-to="<?php echo esc_attr( $tmnhanphat_number_parts['value'] ); ?>"
								data-count-suffix="<?php echo esc_attr( $tmnhanphat_number_parts['suffix'] ); ?>"
							<?php endif; ?>
						><?php echo esc_html( $tmnhanphat_stat['number'] ); ?></p>

						<?php if ( $tmnhanphat_stat['badge'] ) : ?>
							<span class="hero__stat-badge"><?php echo esc_html( $tmnhanphat_stat['badge'] ); ?></span>
						<?php endif; ?>

						<?php if ( $tmnhanphat_stat['description'] ) : ?>
							<p class="hero__stat-description"><?php echo esc_html( $tmnhanphat_stat['description'] ); ?></p>
						<?php endif; ?>
					</div>
				<?php endforeach; ?>
			</div>
		</div>
	<?php endif; ?>
</section>
