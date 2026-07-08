<?php
/**
 * Column 1 của Top Footer: Logo + thông tin liên hệ (address semantic) + Social icons.
 * Component độc lập, không copy sang nơi khác (PROJECT_RULES.md mục 5).
 *
 * @package TMNhanPhat
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$tmnhanphat_footer_logo = tmnhanphat_get_footer_mod( 'tmnhanphat_footer_logo' );
$tmnhanphat_logo_width  = absint( tmnhanphat_get_footer_mod( 'tmnhanphat_footer_logo_width' ) );

// Email/Phone/Address: chỉ hiển thị khi VỪA bật Enable VỪA có giá trị — tránh render nhãn
// suông không có nội dung, hoặc wrapper rỗng khi admin tắt item (PROJECT_RULES.md mục 11).
$tmnhanphat_email        = tmnhanphat_get_footer_mod( 'tmnhanphat_footer_email' );
$tmnhanphat_show_email   = $tmnhanphat_email && tmnhanphat_get_footer_mod( 'tmnhanphat_footer_email_enable' );

$tmnhanphat_phone        = tmnhanphat_get_footer_mod( 'tmnhanphat_footer_phone' );
$tmnhanphat_show_phone   = $tmnhanphat_phone && tmnhanphat_get_footer_mod( 'tmnhanphat_footer_phone_enable' );

$tmnhanphat_address      = tmnhanphat_get_footer_mod( 'tmnhanphat_footer_address' );
$tmnhanphat_show_address = $tmnhanphat_address && tmnhanphat_get_footer_mod( 'tmnhanphat_footer_address_enable' );

// address> chỉ mở khi có ít nhất 1 dòng để hiển thị — không để lại wrapper rỗng.
$tmnhanphat_show_contact = $tmnhanphat_show_email || $tmnhanphat_show_phone || $tmnhanphat_show_address;
?>
<div class="footer-branding">
	<?php if ( $tmnhanphat_footer_logo ) : ?>
		<a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="footer-branding__logo-link">
			<img
				src="<?php echo esc_url( $tmnhanphat_footer_logo ); ?>"
				alt="<?php echo esc_attr( get_bloginfo( 'name' ) ); ?>"
				width="<?php echo esc_attr( $tmnhanphat_logo_width ); ?>"
				loading="lazy"
			/>
		</a>
	<?php elseif ( has_custom_logo() ) : ?>
		<?php the_custom_logo(); ?>
	<?php else : ?>
		<p class="footer-branding__site-title"><?php bloginfo( 'name' ); ?></p>
	<?php endif; ?>

	<h2 class="footer-column__heading"><?php esc_html_e( 'Liên hệ chúng tôi', 'tmnhanphat' ); ?></h2>

	<?php if ( $tmnhanphat_show_contact ) : ?>
		<address class="footer-contact">
			<?php if ( $tmnhanphat_show_email ) : ?>
				<p class="footer-contact__item">
					<?php echo esc_html( tmnhanphat_get_footer_mod( 'tmnhanphat_footer_email_label' ) ); ?>:
					<a href="mailto:<?php echo esc_attr( $tmnhanphat_email ); ?>"><?php echo esc_html( $tmnhanphat_email ); ?></a>
				</p>
			<?php endif; ?>

			<?php if ( $tmnhanphat_show_phone ) : ?>
				<p class="footer-contact__item">
					<?php echo esc_html( tmnhanphat_get_footer_mod( 'tmnhanphat_footer_phone_label' ) ); ?>:
					<a href="tel:<?php echo esc_attr( preg_replace( '/[^0-9+]/', '', $tmnhanphat_phone ) ); ?>"><?php echo esc_html( $tmnhanphat_phone ); ?></a>
				</p>
			<?php endif; ?>

			<?php if ( $tmnhanphat_show_address ) : ?>
				<p class="footer-contact__item">
					<?php echo esc_html( tmnhanphat_get_footer_mod( 'tmnhanphat_footer_address_label' ) ); ?>:
					<?php echo esc_html( $tmnhanphat_address ); ?>
				</p>
			<?php endif; ?>
		</address>
	<?php endif; ?>

	<?php
	$tmnhanphat_show_social = false;

	if ( tmnhanphat_get_footer_mod( 'tmnhanphat_footer_social_enable' ) ) {
		foreach ( tmnhanphat_get_footer_social_networks() as $tmnhanphat_network ) {
			if ( tmnhanphat_get_footer_mod( $tmnhanphat_network['mod'] ) ) {
				$tmnhanphat_show_social = true;
				break;
			}
		}
	}
	?>
	<?php if ( $tmnhanphat_show_social ) : ?>
		<ul class="footer-social" aria-label="<?php esc_attr_e( 'Mạng xã hội', 'tmnhanphat' ); ?>">
			<?php foreach ( tmnhanphat_get_footer_social_networks() as $tmnhanphat_network ) : ?>
				<?php
				$tmnhanphat_social_url = tmnhanphat_get_footer_mod( $tmnhanphat_network['mod'] );
				if ( ! $tmnhanphat_social_url ) {
					continue;
				}
				?>
				<li>
					<a
						href="<?php echo esc_url( $tmnhanphat_social_url ); ?>"
						class="footer-social__link"
						target="_blank"
						rel="noopener noreferrer"
						aria-label="<?php echo esc_attr( $tmnhanphat_network['label'] ); ?>"
					><?php echo $tmnhanphat_network['icon']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- SVG tĩnh định nghĩa sẵn trong inc/template-functions.php, không chứa dữ liệu người dùng. ?></a>
				</li>
			<?php endforeach; ?>
		</ul>
	<?php endif; ?>
</div>
