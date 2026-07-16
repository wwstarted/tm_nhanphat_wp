<?php
/**
 * Quote Contact Section ("Liên hệ báo giá") — trang chủ. Component độc lập
 * (PROJECT_RULES.md mục 5). Layout 50/50: trái = Quote Form Card (nền xanh đậm),
 * phải = Company Information. Mọi tuỳ chỉnh từ Customizer panel "Quote Contact
 * Home"; Email/Phone/Address TÁI SỬ DỤNG mod Footer (nhập 1 lần ở panel Footer).
 *
 * Form: HTML + validation Frontend (assets/js/components/quote.js) + gửi mail qua
 * AJAX handler tmnhanphat_ajax_submit_quote (inc/ajax.php, wp_mail về admin_email).
 * Heading phải reuse review-home__heading
 * (mục 11): H2 gắn class review-home__heading để lấy gạch đôi đỏ/xanh + typography.
 *
 * SEO (mục 14/23): Heading dùng H2. Không in Schema.
 *
 * @package TMNhanPhat
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! tmnhanphat_get_quote_mod( 'tmnhanphat_quote_enable' ) ) {
	return;
}

$tmnhanphat_q_section_id = tmnhanphat_get_quote_mod( 'tmnhanphat_quote_section_id' );
$tmnhanphat_q_card_on    = tmnhanphat_get_quote_mod( 'tmnhanphat_quote_card_enable' );
$tmnhanphat_q_required   = tmnhanphat_get_quote_mod( 'tmnhanphat_quote_required' );

$tmnhanphat_q_header      = tmnhanphat_get_quote_mod( 'tmnhanphat_quote_header_title' );
$tmnhanphat_q_btn_text    = tmnhanphat_get_quote_mod( 'tmnhanphat_quote_btn_text' );
$tmnhanphat_q_btn_icon    = tmnhanphat_get_quote_mod( 'tmnhanphat_quote_btn_icon' );
$tmnhanphat_q_verify_on   = tmnhanphat_get_quote_mod( 'tmnhanphat_quote_verify_enable' );
$tmnhanphat_q_verify_text = tmnhanphat_get_quote_mod( 'tmnhanphat_quote_verify_text' );

$tmnhanphat_q_show_logo  = tmnhanphat_get_quote_mod( 'tmnhanphat_quote_show_logo' );
$tmnhanphat_q_logo       = tmnhanphat_get_quote_logo_url();
$tmnhanphat_q_blue       = tmnhanphat_get_quote_mod( 'tmnhanphat_quote_heading_blue_text' );
$tmnhanphat_q_red        = tmnhanphat_get_quote_mod( 'tmnhanphat_quote_heading_red_text' );
$tmnhanphat_q_desc       = tmnhanphat_get_quote_mod( 'tmnhanphat_quote_desc_text' );
$tmnhanphat_q_contacts   = tmnhanphat_get_quote_contact_items();
$tmnhanphat_q_services   = tmnhanphat_get_quote_service_options();

// Field config: bật/tắt + icon + type; render đúng thứ tự Name → Phone → Email → Service → Message.
$tmnhanphat_q_fields = array(
	'name'    => array( 'show' => tmnhanphat_get_quote_mod( 'tmnhanphat_quote_show_name' ), 'icon' => 'user', 'type' => 'text', 'placeholder' => __( 'Họ và tên', 'tmnhanphat' ) ),
	'phone'   => array( 'show' => tmnhanphat_get_quote_mod( 'tmnhanphat_quote_show_phone' ), 'icon' => 'phone', 'type' => 'tel', 'placeholder' => __( 'Số điện thoại', 'tmnhanphat' ) ),
	'email'   => array( 'show' => tmnhanphat_get_quote_mod( 'tmnhanphat_quote_show_email' ), 'icon' => 'email', 'type' => 'email', 'placeholder' => __( 'Email', 'tmnhanphat' ) ),
);
?>
<section
	class="quote-home"
	<?php if ( $tmnhanphat_q_section_id ) : ?>
		id="<?php echo esc_attr( $tmnhanphat_q_section_id ); ?>"
	<?php endif; ?>
>
	<div class="container quote-home__container">
		<div class="quote-home__grid">
			<!-- ==================== LEFT: FORM CARD ==================== -->
			<div class="quote-home__col quote-home__col--form">
				<form
					class="quote-form<?php echo $tmnhanphat_q_card_on ? ' quote-form--card' : ''; ?>"
					novalidate
					data-tmnp-quote-form
				>
					<div class="quote-form__header">
						<span class="quote-form__header-icon" aria-hidden="true">
							<?php echo tmnhanphat_get_quote_icon_svg( 'user' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- SVG tĩnh. ?>
						</span>
						<?php if ( $tmnhanphat_q_header ) : ?>
							<h3 class="quote-form__title"><?php echo esc_html( $tmnhanphat_q_header ); ?></h3>
						<?php endif; ?>
					</div>

					<div class="quote-form__fields">
						<?php foreach ( $tmnhanphat_q_fields as $tmnhanphat_q_key => $tmnhanphat_q_field ) : ?>
							<?php if ( ! $tmnhanphat_q_field['show'] ) : ?>
								<?php continue; ?>
							<?php endif; ?>
							<div class="quote-field">
								<span class="quote-field__icon" aria-hidden="true">
									<?php echo tmnhanphat_get_quote_icon_svg( $tmnhanphat_q_field['icon'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- SVG tĩnh. ?>
								</span>
								<input
									class="quote-field__input"
									type="<?php echo esc_attr( $tmnhanphat_q_field['type'] ); ?>"
									name="quote_<?php echo esc_attr( $tmnhanphat_q_key ); ?>"
									placeholder="<?php echo esc_attr( $tmnhanphat_q_field['placeholder'] ); ?>"
									<?php echo $tmnhanphat_q_required ? 'data-required="true"' : ''; ?>
									aria-label="<?php echo esc_attr( $tmnhanphat_q_field['placeholder'] ); ?>"
								/>
								<span class="quote-field__error" data-error></span>
							</div>
						<?php endforeach; ?>

						<?php if ( tmnhanphat_get_quote_mod( 'tmnhanphat_quote_show_service' ) && ! empty( $tmnhanphat_q_services ) ) : ?>
							<div class="quote-field quote-field--select">
								<span class="quote-field__icon" aria-hidden="true">
									<?php echo tmnhanphat_get_quote_icon_svg( 'service' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- SVG tĩnh. ?>
								</span>
								<select
									class="quote-field__input quote-field__select"
									name="quote_service"
									<?php echo $tmnhanphat_q_required ? 'data-required="true"' : ''; ?>
									aria-label="<?php esc_attr_e( 'Chọn loại dịch vụ', 'tmnhanphat' ); ?>"
								>
									<option value=""><?php esc_html_e( 'Chọn loại dịch vụ', 'tmnhanphat' ); ?></option>
									<?php foreach ( $tmnhanphat_q_services as $tmnhanphat_q_service ) : ?>
										<option value="<?php echo esc_attr( $tmnhanphat_q_service ); ?>"><?php echo esc_html( $tmnhanphat_q_service ); ?></option>
									<?php endforeach; ?>
								</select>
								<span class="quote-field__error" data-error></span>
							</div>
						<?php endif; ?>

						<?php if ( tmnhanphat_get_quote_mod( 'tmnhanphat_quote_show_message' ) ) : ?>
							<div class="quote-field quote-field--textarea">
								<span class="quote-field__icon quote-field__icon--top" aria-hidden="true">
									<?php echo tmnhanphat_get_quote_icon_svg( 'message' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- SVG tĩnh. ?>
								</span>
								<textarea
									class="quote-field__input quote-field__textarea"
									name="quote_message"
									rows="3"
									placeholder="<?php esc_attr_e( 'Nội dung yêu cầu ...', 'tmnhanphat' ); ?>"
									<?php echo $tmnhanphat_q_required ? 'data-required="true"' : ''; ?>
									aria-label="<?php esc_attr_e( 'Nội dung yêu cầu', 'tmnhanphat' ); ?>"
								></textarea>
								<span class="quote-field__error" data-error></span>
							</div>
						<?php endif; ?>
					</div>

					<?php if ( $tmnhanphat_q_btn_text ) : ?>
						<button type="submit" class="quote-form__submit">
							<?php if ( $tmnhanphat_q_btn_icon ) : ?>
								<span class="quote-form__submit-icon" aria-hidden="true">
									<?php echo tmnhanphat_get_quote_icon_svg( 'send' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- SVG tĩnh. ?>
								</span>
							<?php endif; ?>
							<?php echo esc_html( $tmnhanphat_q_btn_text ); ?>
						</button>
					<?php endif; ?>

					<?php if ( $tmnhanphat_q_verify_on && $tmnhanphat_q_verify_text ) : ?>
						<p class="quote-form__verify">
							<span class="quote-form__verify-icon" aria-hidden="true">
								<?php echo tmnhanphat_get_quote_icon_svg( 'verify' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- SVG tĩnh. ?>
							</span>
							<?php echo esc_html( $tmnhanphat_q_verify_text ); ?>
						</p>
					<?php endif; ?>

					<?php /* Thông báo kết quả gửi (JS điền từ response AJAX — thành công/thất bại). */ ?>
					<p class="quote-form__notice" data-quote-notice role="status" aria-live="polite" hidden></p>
				</form>
			</div>

			<!-- ==================== RIGHT: COMPANY INFO ==================== -->
			<div class="quote-home__col quote-home__col--info">
				<div class="quote-info">
					<?php if ( $tmnhanphat_q_show_logo && $tmnhanphat_q_logo ) : ?>
						<div class="quote-info__logo">
							<?php
							$tmnhanphat_q_logo_id = attachment_url_to_postid( $tmnhanphat_q_logo );
							if ( $tmnhanphat_q_logo_id ) {
								echo wp_get_attachment_image( $tmnhanphat_q_logo_id, 'medium', false, array(
									'class' => 'quote-info__logo-img',
									'alt'   => get_bloginfo( 'name' ),
								) );
							} else {
								printf(
									'<img class="quote-info__logo-img" src="%s" alt="%s" loading="lazy" decoding="async" />',
									esc_url( $tmnhanphat_q_logo ),
									esc_attr( get_bloginfo( 'name' ) )
								);
							}
							?>
						</div>
					<?php endif; ?>

					<?php if ( $tmnhanphat_q_blue || $tmnhanphat_q_red ) : ?>
						<h2 class="quote-info__heading review-home__heading">
							<?php if ( $tmnhanphat_q_blue ) : ?>
								<span class="quote-info__heading-blue"><?php echo esc_html( $tmnhanphat_q_blue ); ?></span>
							<?php endif; ?>
							<?php if ( $tmnhanphat_q_red ) : ?>
								<span class="quote-info__heading-red"><?php echo esc_html( $tmnhanphat_q_red ); ?></span>
							<?php endif; ?>
						</h2>
					<?php endif; ?>

					<?php if ( $tmnhanphat_q_desc ) : ?>
						<p class="quote-info__description"><?php echo esc_html( $tmnhanphat_q_desc ); ?></p>
					<?php endif; ?>

					<?php if ( ! empty( $tmnhanphat_q_contacts ) ) : ?>
						<ul class="quote-info__list">
							<?php foreach ( $tmnhanphat_q_contacts as $tmnhanphat_q_contact ) : ?>
								<li class="quote-info__item">
									<span class="quote-info__icon" aria-hidden="true">
										<?php echo tmnhanphat_get_quote_icon_svg( $tmnhanphat_q_contact['type'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- SVG tĩnh. ?>
									</span>
									<span class="quote-info__content">
										<span class="quote-info__label"><?php echo esc_html( $tmnhanphat_q_contact['label'] ); ?>:</span>
										<?php if ( $tmnhanphat_q_contact['href'] ) : ?>
											<a class="quote-info__value" href="<?php echo esc_url( $tmnhanphat_q_contact['href'] ); ?>"><?php echo esc_html( $tmnhanphat_q_contact['value'] ); ?></a>
										<?php else : ?>
											<span class="quote-info__value"><?php echo esc_html( $tmnhanphat_q_contact['value'] ); ?></span>
										<?php endif; ?>
									</span>
								</li>
							<?php endforeach; ?>
						</ul>
					<?php endif; ?>
				</div>
			</div>
		</div>
	</div>
</section>
