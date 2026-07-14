<?php
/**
 * Process Section ("Quy trình hợp tác") — trang chủ. Component độc lập (PROJECT_RULES.md
 * mục 5). Toàn bộ nội dung từ Customizer panel "Process Home" (fixed-slot repeater,
 * mục 24) — KHÔNG WP_Query/ACF/Meta Box, không hardcode.
 *
 * Cấu trúc theo design: Header (Blue + Red Title 1 dòng, gạch chân + Description) →
 * Content 2 cột: trái = Timeline Steps (đường dọc + circle số, auto-active xoay vòng
 * qua assets/js/components/process.js), phải = Image (đồng bộ theo Step active nếu
 * Step có ảnh riêng qua data-step-image — fallback ảnh mặc định).
 *
 * Progressive enhancement: PHP render Step ĐẦU TIÊN active sẵn — không có JS thì
 * timeline đứng yên ở Step 1, toàn bộ nội dung vẫn đọc được (không gắn class ready
 * từ PHP — bài học từ bug About Section). prefers-reduced-motion: JS tự tắt auto-active.
 *
 * SEO (mục 14/23): Heading dùng H2 (H1 duy nhất thuộc Hero), Step Title dùng H3.
 * Không in Schema — việc của Plugin SEO.
 *
 * @package TMNhanPhat
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! tmnhanphat_get_process_mod( 'tmnhanphat_process_enable' ) ) {
	return;
}

$tmnhanphat_pr_steps = tmnhanphat_get_process_steps();

if ( empty( $tmnhanphat_pr_steps ) ) {
	return; // Chưa có Step nào — không render section trống (mục 15).
}

$tmnhanphat_pr_section_id = tmnhanphat_get_process_mod( 'tmnhanphat_process_section_id' );
$tmnhanphat_pr_bg_image   = tmnhanphat_get_process_mod( 'tmnhanphat_process_bg_image' );

$tmnhanphat_pr_blue_text   = tmnhanphat_get_process_mod( 'tmnhanphat_process_heading_blue_text' );
$tmnhanphat_pr_red_text    = tmnhanphat_get_process_mod( 'tmnhanphat_process_heading_red_text' );
$tmnhanphat_pr_description = tmnhanphat_get_process_mod( 'tmnhanphat_process_desc_text' );

$tmnhanphat_pr_image      = tmnhanphat_get_process_mod( 'tmnhanphat_process_image' );
$tmnhanphat_pr_image_lazy = tmnhanphat_get_process_mod( 'tmnhanphat_process_image_lazy' );

// Ảnh hiển thị ban đầu = ảnh riêng của Step 1 (đang active) nếu có, fallback ảnh mặc
// định. Cột ảnh render cả khi CHƯA có ảnh mặc định nhưng có ảnh per-step — nếu không,
// JS không có <img> nào để đồng bộ theo Step. data-default-image cho JS biết ảnh nền
// khi Step active không có ảnh riêng.
$tmnhanphat_pr_initial_image = ! empty( $tmnhanphat_pr_steps[0]['image'] ) ? $tmnhanphat_pr_steps[0]['image'] : $tmnhanphat_pr_image;
$tmnhanphat_pr_default_image = $tmnhanphat_pr_image ? $tmnhanphat_pr_image : $tmnhanphat_pr_initial_image;

$tmnhanphat_pr_timeline_attrs = array(
	'data-tmnp-process' => '',
	'data-auto'         => tmnhanphat_get_process_mod( 'tmnhanphat_process_auto_active_enable' ) ? 'true' : 'false',
	'data-interval'     => absint( tmnhanphat_get_process_mod( 'tmnhanphat_process_interval' ) ),
	'data-transition'   => absint( tmnhanphat_get_process_mod( 'tmnhanphat_process_transition_speed' ) ),
	'data-pause-hover'  => tmnhanphat_get_process_mod( 'tmnhanphat_process_pause_hover' ) ? 'true' : 'false',
	'data-loop'         => tmnhanphat_get_process_mod( 'tmnhanphat_process_loop' ) ? 'true' : 'false',
	'data-animate'      => tmnhanphat_get_process_mod( 'tmnhanphat_process_animation_enable' ) ? 'true' : 'false',
);

$tmnhanphat_pr_attr_html = '';
foreach ( $tmnhanphat_pr_timeline_attrs as $tmnhanphat_pr_attr_name => $tmnhanphat_pr_attr_value ) {
	$tmnhanphat_pr_attr_html .= sprintf( ' %s="%s"', esc_attr( $tmnhanphat_pr_attr_name ), esc_attr( $tmnhanphat_pr_attr_value ) );
}
?>
<section
	class="process-home"
	<?php if ( $tmnhanphat_pr_section_id ) : ?>
		id="<?php echo esc_attr( $tmnhanphat_pr_section_id ); ?>"
	<?php endif; ?>
>
	<?php if ( $tmnhanphat_pr_bg_image ) : ?>
		<div class="process-home__background" aria-hidden="true">
			<?php echo tmnhanphat_get_why_choose_image_html( $tmnhanphat_pr_bg_image, '', 'full', 'process-home__bg-image' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- helper đã escape toàn bộ bên trong. ?>
		</div>
	<?php endif; ?>

	<div class="container process-home__container">
		<header class="process-home__header">
			<?php if ( $tmnhanphat_pr_blue_text || $tmnhanphat_pr_red_text ) : ?>
				<h2 class="process-home__heading">
					<?php if ( $tmnhanphat_pr_blue_text ) : ?>
						<span class="process-home__heading-blue"><?php echo esc_html( $tmnhanphat_pr_blue_text ); ?></span>
					<?php endif; ?>
					<?php if ( $tmnhanphat_pr_red_text ) : ?>
						<span class="process-home__heading-red"><?php echo esc_html( $tmnhanphat_pr_red_text ); ?></span>
					<?php endif; ?>
				</h2>
			<?php endif; ?>

			<?php if ( $tmnhanphat_pr_description ) : ?>
				<p class="process-home__description"><?php echo esc_html( $tmnhanphat_pr_description ); ?></p>
			<?php endif; ?>
		</header>

		<div class="process-home__content">
			<div
				class="process-home__timeline"
				<?php echo $tmnhanphat_pr_attr_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- từng name/value đã esc_attr() khi build ở trên. ?>
			>
				<ol class="process-home__steps">
					<?php foreach ( $tmnhanphat_pr_steps as $tmnhanphat_pr_index => $tmnhanphat_pr_step ) : ?>
						<li
							class="process-step<?php echo 0 === $tmnhanphat_pr_index ? ' is-active' : ''; ?>"
							<?php if ( $tmnhanphat_pr_step['image'] ) : ?>
								data-step-image="<?php echo esc_url( $tmnhanphat_pr_step['image'] ); ?>"
							<?php endif; ?>
							<?php if ( 0 === $tmnhanphat_pr_index ) : ?>
								aria-current="step"
							<?php endif; ?>
						>
							<span class="process-step__marker" aria-hidden="true">
								<?php if ( $tmnhanphat_pr_step['icon'] ) : ?>
									<?php echo tmnhanphat_get_why_choose_image_html( $tmnhanphat_pr_step['icon'], '', 'thumbnail', 'process-step__icon' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- helper đã escape toàn bộ bên trong. ?>
								<?php else : ?>
									<span class="process-step__number"><?php echo esc_html( $tmnhanphat_pr_step['number'] ); ?></span>
								<?php endif; ?>
							</span>

							<div class="process-step__body">
								<h3 class="process-step__title"><?php echo esc_html( $tmnhanphat_pr_step['title'] ); ?></h3>

								<?php if ( $tmnhanphat_pr_step['description'] ) : ?>
									<p class="process-step__description"><?php echo esc_html( $tmnhanphat_pr_step['description'] ); ?></p>
								<?php endif; ?>
							</div>
						</li>
					<?php endforeach; ?>
				</ol>
			</div>

			<?php if ( $tmnhanphat_pr_initial_image ) : ?>
				<div class="process-home__media" data-default-image="<?php echo esc_url( $tmnhanphat_pr_default_image ); ?>">
					<?php if ( $tmnhanphat_pr_image_lazy ) : ?>
						<?php echo tmnhanphat_get_why_choose_image_html( $tmnhanphat_pr_initial_image, trim( $tmnhanphat_pr_blue_text . ' ' . $tmnhanphat_pr_red_text ), 'large', 'process-home__image' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- helper đã escape toàn bộ bên trong. ?>
					<?php else : ?>
						<img
							class="process-home__image"
							src="<?php echo esc_url( $tmnhanphat_pr_initial_image ); ?>"
							alt="<?php echo esc_attr( trim( $tmnhanphat_pr_blue_text . ' ' . $tmnhanphat_pr_red_text ) ); ?>"
							decoding="async"
						/>
					<?php endif; ?>
				</div>
			<?php endif; ?>
		</div>
	</div>
</section>
