<?php
/**
 * Top Footer: lưới 4 cột (branding | công ty | chính sách | sản phẩm+dịch vụ).
 * Chỉ lo bố cục lưới — nội dung từng cột nằm ở column-branding.php / column-links.php
 * (PROJECT_RULES.md mục 4: template lớn phải chia nhỏ).
 *
 * @package TMNhanPhat
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<div class="footer-top">
	<div class="footer-top__inner">
		<div class="footer-column footer-column--branding">
			<?php get_template_part( 'template-parts/footer/column-branding' ); ?>
		</div>

		<div class="footer-column">
			<?php
			get_template_part( 'template-parts/footer/column-links', null, array(
				'heading'         => tmnhanphat_get_footer_mod( 'tmnhanphat_footer_company_name' ),
				'theme_location'  => 'footer_company',
				'fallback_column' => 'company',
			) );
			?>
		</div>

		<div class="footer-column">
			<?php
			get_template_part( 'template-parts/footer/column-links', null, array(
				'heading'         => __( 'Chính sách', 'tmnhanphat' ),
				'theme_location'  => 'footer_policy',
				'fallback_column' => 'policy',
			) );
			?>
		</div>

		<div class="footer-column footer-column--products-services">
			<?php
			get_template_part( 'template-parts/footer/column-links', null, array(
				'heading'         => __( 'Sản phẩm', 'tmnhanphat' ),
				'theme_location'  => 'footer_product',
				'fallback_column' => 'product',
			) );
			?>

			<hr class="footer-column__divider" />

			<?php
			get_template_part( 'template-parts/footer/column-links', null, array(
				'heading'         => __( 'Dịch vụ', 'tmnhanphat' ),
				'theme_location'  => 'footer_service',
				'fallback_column' => 'service',
			) );
			?>
		</div>
	</div>
</div>
