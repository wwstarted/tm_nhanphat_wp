<?php
/**
 * Partners Section (Đối tác/Thương hiệu đồng hành) — trang chủ, ngay dưới Hero Banner.
 * Component độc lập (PROJECT_RULES.md mục 5). Toàn bộ nội dung lấy từ Customizer panel
 * "Homepage" → Partners (inc/customizer/partners-customizer.php), không hardcode.
 *
 * Cấu trúc HTML cố ý dùng <ul>/<li> phẳng (không bọc thêm track/slide) để sau này chỉ cần
 * thêm 1 script + vài class là chuyển được sang Slider/Marquee/Swiper mà KHÔNG phải đổi
 * markup từng item — xem mục "Future Ready" trong yêu cầu gốc.
 *
 * @package TMNhanPhat
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! tmnhanphat_get_partners_mod( 'tmnhanphat_partners_enable' ) ) {
	return;
}

$tmnhanphat_partners = tmnhanphat_get_partners();

if ( empty( $tmnhanphat_partners ) ) {
	return;
}

$tmnhanphat_title_enable = tmnhanphat_get_partners_mod( 'tmnhanphat_partners_title_enable' );
$tmnhanphat_title        = tmnhanphat_get_partners_mod( 'tmnhanphat_partners_title' );
$tmnhanphat_desc_enable  = tmnhanphat_get_partners_mod( 'tmnhanphat_partners_description_enable' );
$tmnhanphat_desc         = tmnhanphat_get_partners_mod( 'tmnhanphat_partners_description' );

$tmnhanphat_partners_class = 'partners';
if ( ! tmnhanphat_get_partners_mod( 'tmnhanphat_partners_hover_enable' ) ) {
	$tmnhanphat_partners_class .= ' partners--no-hover';
}
?>
<section class="<?php echo esc_attr( $tmnhanphat_partners_class ); ?>" aria-label="<?php esc_attr_e( 'Đối tác đồng hành', 'tmnhanphat' ); ?>">
	<div class="partners__inner">
		<?php if ( $tmnhanphat_title_enable && $tmnhanphat_title ) : ?>
			<h2 class="partners__title"><?php echo esc_html( $tmnhanphat_title ); ?></h2>
		<?php endif; ?>

		<?php if ( $tmnhanphat_desc_enable && $tmnhanphat_desc ) : ?>
			<p class="partners__description"><?php echo esc_html( $tmnhanphat_desc ); ?></p>
		<?php endif; ?>

		<ul class="partners__grid">
			<?php foreach ( $tmnhanphat_partners as $tmnhanphat_partner ) : ?>
				<li class="partners__item">
					<?php if ( $tmnhanphat_partner['link'] ) : ?>
						<a
							class="partners__link"
							href="<?php echo esc_url( $tmnhanphat_partner['link'] ); ?>"
							<?php if ( $tmnhanphat_partner['new_tab'] ) : ?>
								target="_blank" rel="noopener noreferrer"
							<?php endif; ?>
						>
					<?php endif; ?>

					<figure class="partners__figure">
						<?php echo tmnhanphat_get_partner_logo_html( $tmnhanphat_partner['logo'], $tmnhanphat_partner['alt'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- đã escape bên trong tmnhanphat_get_partner_logo_html(). ?>
					</figure>

					<?php if ( $tmnhanphat_partner['link'] ) : ?>
						</a>
					<?php endif; ?>
				</li>
			<?php endforeach; ?>
		</ul>
	</div>
</section>
