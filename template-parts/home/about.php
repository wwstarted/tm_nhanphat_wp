<?php
/**
 * About Company Section (Về chúng tôi) — trang chủ. BẢN V3: dùng ẢNH THIẾT KẾ THẬT do designer
 * xuất từ Figma (Rectangle 19.png làm nền góc cạnh, ảnh thang máy PNG nền trong suốt đè lên
 * trên) thay vì tự vẽ bằng CSS clip-path — xem PROJECT_RULES.md mục 28 (bản v3). Component
 * độc lập (mục 5). Toàn bộ nội dung lấy từ Customizer panel "About Home"
 * (inc/customizer/about-customizer.php).
 *
 * Cấu trúc BẮT BUỘC — Ảnh KHÔNG nằm trong .container, KHÔNG dùng Grid/Flex chia cột:
 * Background trắng → .about-home__shape (ảnh nền, thuần decoration) → .container >
 * .about-home__content (Text) → .about-home__image (Ảnh, 1 layer riêng, trên cùng).
 *
 * @package TMNhanPhat
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! tmnhanphat_get_about_mod( 'tmnhanphat_about_enable' ) ) {
	return;
}

$tmnhanphat_about_image = tmnhanphat_get_about_mod( 'tmnhanphat_about_image' );
$tmnhanphat_heading     = tmnhanphat_get_about_mod( 'tmnhanphat_about_heading' );
$tmnhanphat_label       = tmnhanphat_get_about_mod( 'tmnhanphat_about_label' );
$tmnhanphat_description = tmnhanphat_get_about_mod( 'tmnhanphat_about_description' );
$tmnhanphat_section_id  = tmnhanphat_get_about_mod( 'tmnhanphat_about_section_id' );
$tmnhanphat_shape_on    = tmnhanphat_get_about_mod( 'tmnhanphat_about_shape_enable' );

$tmnhanphat_about_class = 'about-home js-about-ready';
if ( 'image_first' === tmnhanphat_get_about_mod( 'tmnhanphat_about_mobile_layout' ) ) {
	$tmnhanphat_about_class .= ' about-home--image-first';
}
?>
<section
	class="<?php echo esc_attr( $tmnhanphat_about_class ); ?>"
	<?php if ( $tmnhanphat_section_id ) : ?>
		id="<?php echo esc_attr( $tmnhanphat_section_id ); ?>"
	<?php endif; ?>
>
	<?php if ( $tmnhanphat_shape_on ) : ?>
		<div class="about-home__shape" aria-hidden="true"></div>
	<?php endif; ?>

	<div class="container">
		<div class="about-home__content">
			<?php if ( $tmnhanphat_label ) : ?>
				<p class="about-home__label"><?php echo esc_html( $tmnhanphat_label ); ?></p>
			<?php endif; ?>

			<?php if ( $tmnhanphat_heading ) : ?>
				<h2 class="about-home__heading"><?php echo esc_html( $tmnhanphat_heading ); ?></h2>
			<?php endif; ?>

			<?php if ( $tmnhanphat_description ) : ?>
				<p class="about-home__description"><?php echo esc_html( $tmnhanphat_description ); ?></p>
			<?php endif; ?>
		</div>
	</div>

	<?php if ( $tmnhanphat_about_image ) : ?>
		<div class="about-home__image">
			<?php echo tmnhanphat_get_about_image_html( $tmnhanphat_about_image, $tmnhanphat_heading ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- đã escape bên trong tmnhanphat_get_about_image_html(). ?>
		</div>
	<?php endif; ?>
</section>
