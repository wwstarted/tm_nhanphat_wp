<?php
/**
 * Logo / tên site trong header — component độc lập (PROJECT_RULES.md mục 5).
 *
 * Thứ tự ưu tiên hiển thị (đúng SEO — chỉ 1 nguồn logo mỗi lần, không trùng lặp):
 * 1. Logo tùy chỉnh trong Customizer panel "Header" (kèm Retina Logo qua srcset).
 * 2. Site Identity Logo mặc định của WordPress (custom_logo core).
 * 3. Site Title + Tagline dạng text.
 *
 * @package TMNhanPhat
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$tmnhanphat_logo_url        = tmnhanphat_get_header_mod( 'tmnhanphat_header_logo' );
$tmnhanphat_logo_retina_url = tmnhanphat_get_header_mod( 'tmnhanphat_header_logo_retina' );
$tmnhanphat_logo_width      = absint( tmnhanphat_get_header_mod( 'tmnhanphat_header_logo_width' ) );
?>
<div class="site-branding">
	<?php if ( $tmnhanphat_logo_url ) : ?>
		<a href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home" class="site-branding__logo-link">
			<img
				src="<?php echo esc_url( $tmnhanphat_logo_url ); ?>"
				<?php if ( $tmnhanphat_logo_retina_url ) : ?>
				srcset="<?php echo esc_url( $tmnhanphat_logo_url ); ?> 1x, <?php echo esc_url( $tmnhanphat_logo_retina_url ); ?> 2x"
				<?php endif; ?>
				alt="<?php echo esc_attr( get_bloginfo( 'name' ) ); ?>"
				width="<?php echo esc_attr( $tmnhanphat_logo_width ); ?>"
				loading="eager"
				fetchpriority="high"
			/>
		</a>
	<?php elseif ( has_custom_logo() ) : ?>
		<?php the_custom_logo(); ?>
	<?php else : ?>
		<p class="site-title">
			<a href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home">
				<?php bloginfo( 'name' ); ?>
			</a>
		</p>
		<?php if ( get_bloginfo( 'description' ) ) : ?>
			<p class="site-description"><?php bloginfo( 'description' ); ?></p>
		<?php endif; ?>
	<?php endif; ?>
</div>
