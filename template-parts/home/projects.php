<?php
/**
 * Featured Projects Section ("Dự án tiêu biểu") — trang chủ. Component độc lập
 * (PROJECT_RULES.md mục 5). Dữ liệu là POST THƯỜNG thuộc Category chọn trong
 * Customizer panel "Projects Home" (WP_Query — không CPT/Taxonomy mới, không
 * hardcode ID); mọi tuỳ chỉnh hiển thị từ Customizer — không hardcode.
 *
 * Cấu trúc theo design: nền xanh + Background Decoration (thuần trang trí, dưới
 * nội dung) → Header (Title trắng + Description căn giữa) → Slider 1 dự án/slide:
 * card kính mờ 2 cột 65/35 (trái = Title/Excerpt/3 Meta có divider, phải = Featured
 * Image khung cố định) → Dots.
 *
 * Meta Loại hình/Tải trọng/Bàn giao: đọc post meta theo key cấu hình, RỖNG thì dùng
 * Fallback CHỈ Ở TẦNG HIỂN THỊ (tmnhanphat_get_project_meta_items() — không ghi DB).
 *
 * Slider tái sử dụng markup .tmnp-slider + assets/js/components/slider.js
 * (data-cards 1/1/1 + dots). No-JS fallback: track cuộn ngang + scroll-snap.
 *
 * SEO (mục 14/23): Heading dùng H2, Project Title dùng H3. Không in Schema.
 *
 * 
 * @package TMNhanPhat
 * 
 */

if (!defined('ABSPATH')) {
	exit;
}

if (!tmnhanphat_get_projects_mod('tmnhanphat_projects_enable')) {
	return;
}

if (!tmnhanphat_get_projects_category_id()) {
	return; // Admin chưa chọn Category "Dự án" — không render (giống Products).
}

$tmnhanphat_pj_query = tmnhanphat_get_projects_query();

if (!$tmnhanphat_pj_query->have_posts()) {
	return; // Chưa có dự án nào — không render section trống (mục 15).
}

$tmnhanphat_pj_section_id = tmnhanphat_get_projects_mod('tmnhanphat_projects_section_id');
$tmnhanphat_pj_deco_on = tmnhanphat_get_projects_mod('tmnhanphat_projects_deco_enable');
$tmnhanphat_pj_deco_image = tmnhanphat_get_projects_mod('tmnhanphat_projects_deco_image');
$tmnhanphat_pj_title = tmnhanphat_get_projects_mod('tmnhanphat_projects_title');
$tmnhanphat_pj_description = tmnhanphat_get_projects_mod('tmnhanphat_projects_desc_text');
$tmnhanphat_pj_meta_enable = tmnhanphat_get_projects_mod('tmnhanphat_projects_meta_enable');

$tmnhanphat_pj_slider_attrs = array(
	'data-tmnp-slider' => '',
	'data-autoplay' => tmnhanphat_get_projects_mod('tmnhanphat_projects_autoplay_enable') ? 'true' : 'false',
	'data-autoplay-speed' => absint(tmnhanphat_get_projects_mod('tmnhanphat_projects_autoplay_speed')),
	'data-transition' => absint(tmnhanphat_get_projects_mod('tmnhanphat_projects_transition_speed')),
	'data-pause-hover' => tmnhanphat_get_projects_mod('tmnhanphat_projects_pause_hover') ? 'true' : 'false',
	'data-infinite' => tmnhanphat_get_projects_mod('tmnhanphat_projects_infinite') ? 'true' : 'false',
	'data-drag' => tmnhanphat_get_projects_mod('tmnhanphat_projects_drag_enable') ? 'true' : 'false',
	'data-dots' => tmnhanphat_get_projects_mod('tmnhanphat_projects_show_dots') ? 'true' : 'false',
	'data-arrows' => 'false',
	// 1 dự án / khung nhìn ở MỌI breakpoint — đúng design.
	'data-cards-desktop' => 1,
	'data-cards-tablet' => 1,
	'data-cards-mobile' => 1,
);

$tmnhanphat_pj_attr_html = '';
foreach ($tmnhanphat_pj_slider_attrs as $tmnhanphat_pj_attr_name => $tmnhanphat_pj_attr_value) {
	$tmnhanphat_pj_attr_html .= sprintf(' %s="%s"', esc_attr($tmnhanphat_pj_attr_name), esc_attr($tmnhanphat_pj_attr_value));
}
?>
<section class="projects-home" <?php if ($tmnhanphat_pj_section_id): ?>
		id="<?php echo esc_attr($tmnhanphat_pj_section_id); ?>" <?php endif; ?>>
	<?php if ($tmnhanphat_pj_deco_on && $tmnhanphat_pj_deco_image): ?>
		<?php /* Background Decoration — thuần trang trí, luôn dưới nội dung (z-index 0 < container 1); ảnh/vị trí/size/opacity qua CSS vars từ Customizer. */ ?>
		<div class="projects-home__decoration" aria-hidden="true"></div>
	<?php endif; ?>

	<div class="container projects-home__container">
		<header class="projects-home__header">
			<?php if ($tmnhanphat_pj_title): ?>
				<h2 class="projects-home__heading"><?php echo esc_html($tmnhanphat_pj_title); ?></h2>
			<?php endif; ?>

			<?php if ($tmnhanphat_pj_description): ?>
				<p class="projects-home__description"><?php echo esc_html($tmnhanphat_pj_description); ?></p>
			<?php endif; ?>
		</header>

		<div class="projects-home__slider tmnp-slider" role="region" aria-roledescription="carousel"
			aria-label="<?php esc_attr_e('Dự án tiêu biểu', 'tmnhanphat'); ?>" <?php echo $tmnhanphat_pj_attr_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- từng name/value đã esc_attr() khi build ở trên. ?>>
			<div class="tmnp-slider__viewport" tabindex="0">
				<div class="tmnp-slider__track">
					<?php
					while ($tmnhanphat_pj_query->have_posts()):
						$tmnhanphat_pj_query->the_post();
						$tmnhanphat_pj_meta_items = $tmnhanphat_pj_meta_enable ? tmnhanphat_get_project_meta_items(get_the_ID()) : array();
						?>
						<article class="tmnp-slider__slide project-card">
							<div class="project-card__content">
								<h3 class="project-card__title">
									<a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
								</h3>

								<?php $tmnhanphat_pj_excerpt = tmnhanphat_get_excerpt(get_the_ID(), 30); ?>
								<?php if ($tmnhanphat_pj_excerpt): ?>
									<p class="project-card__description">
										<?php echo $tmnhanphat_pj_excerpt; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- helper trả chuỗi đã esc_html() sẵn. ?>
									</p>
								<?php endif; ?>

								<?php if (!empty($tmnhanphat_pj_meta_items)): ?>
									<ul class="project-card__meta">
										<?php foreach ($tmnhanphat_pj_meta_items as $tmnhanphat_pj_meta): ?>
											<li class="project-card__meta-item">
												<span
													class="project-card__meta-label"><?php echo esc_html($tmnhanphat_pj_meta['label']); ?></span>
												<span
													class="project-card__meta-value"><?php echo esc_html($tmnhanphat_pj_meta['value']); ?></span>
											</li>
										<?php endforeach; ?>
									</ul>
								<?php endif; ?>
							</div>

							<div class="project-card__media">
								<a class="project-card__media-link" href="<?php the_permalink(); ?>" aria-hidden="true"
									tabindex="-1">
									<?php if (has_post_thumbnail()): ?>
										<?php
										the_post_thumbnail('tmnhanphat-project-card', array(
											'class' => 'project-card__image',
											'alt' => get_the_title(),
										));
										?>
									<?php endif; ?>
								</a>
							</div>
						</article>
					<?php endwhile; ?>
					<?php wp_reset_postdata(); ?>
				</div>
			</div>
			<?php /* Dots do slider.js tự sinh khi bật (không in sẵn DOM thừa — mục 15). */ ?>
		</div>
	</div>
</section>