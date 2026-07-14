<?php
/**
 * FAQ Section ("Câu hỏi thường gặp") — trang chủ. Component độc lập (PROJECT_RULES.md
 * mục 5). Dữ liệu 100% từ CPT faq (Title=Question, Editor=Answer) qua
 * tmnhanphat_get_faq_items() — KHÔNG wp_posts thường/category Blog. Mọi tuỳ chỉnh từ
 * Customizer panel "FAQ Home"; chưa có FAQ nào → Demo Data (tầng hiển thị, mục 3).
 *
 * Grid 2 cột × 3 hàng. Accordion: chỉ 1 FAQ mở cùng lúc (assets/js/components/faq.js
 * — slide down/up), FAQ Expand Default mở sẵn từ server (class .is-open + [hidden]
 * bỏ trên panel) → không JS vẫn xem được câu trả lời của FAQ mở sẵn (progressive
 * enhancement). Heading reuse review-home__heading (mục 5).
 *
 * SEO (mục 14/23): Heading H2, Question H3. Không in Schema (FAQPage là việc Plugin SEO).
 *
 * @package TMNhanPhat
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! tmnhanphat_get_faq_mod( 'tmnhanphat_faq_enable' ) ) {
	return;
}

$tmnhanphat_faq_items = tmnhanphat_get_faq_items();

if ( empty( $tmnhanphat_faq_items ) ) {
	return; // Không FAQ + không demo — không render trống (mục 15).
}

$tmnhanphat_faq_section_id  = tmnhanphat_get_faq_mod( 'tmnhanphat_faq_section_id' );
$tmnhanphat_faq_small       = tmnhanphat_get_faq_mod( 'tmnhanphat_faq_small_title' );
$tmnhanphat_faq_blue        = tmnhanphat_get_faq_mod( 'tmnhanphat_faq_heading_blue_text' );
$tmnhanphat_faq_red         = tmnhanphat_get_faq_mod( 'tmnhanphat_faq_heading_red_text' );
$tmnhanphat_faq_desc        = tmnhanphat_get_faq_mod( 'tmnhanphat_faq_desc_text' );
$tmnhanphat_faq_reuse       = tmnhanphat_get_faq_mod( 'tmnhanphat_faq_reuse_heading' );
$tmnhanphat_faq_badge_on    = tmnhanphat_get_faq_mod( 'tmnhanphat_faq_badge_enable' );
$tmnhanphat_faq_icon_on     = tmnhanphat_get_faq_mod( 'tmnhanphat_faq_icon_enable' );
$tmnhanphat_faq_allow_multi = tmnhanphat_get_faq_mod( 'tmnhanphat_faq_allow_multiple' );

$tmnhanphat_faq_heading_class = 'faq-home__heading' . ( $tmnhanphat_faq_reuse ? ' review-home__heading' : '' );
$tmnhanphat_faq_header_class  = 'faq-home__header' . ( $tmnhanphat_faq_reuse ? ' faq-home__header--reuse' : '' );
?>
<section
	class="faq-home"
	<?php if ( $tmnhanphat_faq_section_id ) : ?>
		id="<?php echo esc_attr( $tmnhanphat_faq_section_id ); ?>"
	<?php endif; ?>
>
	<div class="container faq-home__container">
		<header class="<?php echo esc_attr( $tmnhanphat_faq_header_class ); ?>">
			<?php if ( $tmnhanphat_faq_small ) : ?>
				<span class="faq-home__small"><?php echo esc_html( $tmnhanphat_faq_small ); ?></span>
			<?php endif; ?>

			<?php if ( $tmnhanphat_faq_blue || $tmnhanphat_faq_red ) : ?>
				<h2 class="<?php echo esc_attr( $tmnhanphat_faq_heading_class ); ?>">
					<?php if ( $tmnhanphat_faq_blue ) : ?>
						<span class="faq-home__heading-blue"><?php echo esc_html( $tmnhanphat_faq_blue ); ?></span>
					<?php endif; ?>
					<?php if ( $tmnhanphat_faq_red ) : ?>
						<span class="faq-home__heading-red"><?php echo esc_html( $tmnhanphat_faq_red ); ?></span>
					<?php endif; ?>
				</h2>
			<?php endif; ?>

			<?php if ( $tmnhanphat_faq_desc ) : ?>
				<p class="faq-home__description"><?php echo esc_html( $tmnhanphat_faq_desc ); ?></p>
			<?php endif; ?>
		</header>

		<div
			class="faq-home__grid"
			data-tmnp-faq
			data-allow-multiple="<?php echo $tmnhanphat_faq_allow_multi ? 'true' : 'false'; ?>"
		>
			<?php foreach ( $tmnhanphat_faq_items as $tmnhanphat_faq_index => $tmnhanphat_faq_item ) : ?>
				<?php
				$tmnhanphat_faq_is_open  = ! empty( $tmnhanphat_faq_item['expand'] );
				$tmnhanphat_faq_panel_id = 'faq-panel-' . ( $tmnhanphat_faq_index + 1 );
				?>
				<article class="faq-card<?php echo $tmnhanphat_faq_is_open ? ' is-open' : ''; ?>">
					<h3 class="faq-card__question-heading">
						<button
							type="button"
							class="faq-card__toggle"
							aria-expanded="<?php echo $tmnhanphat_faq_is_open ? 'true' : 'false'; ?>"
							aria-controls="<?php echo esc_attr( $tmnhanphat_faq_panel_id ); ?>"
						>
							<?php if ( $tmnhanphat_faq_badge_on ) : ?>
								<span class="faq-card__badge" aria-hidden="true"><?php echo esc_html( sprintf( '%02d', $tmnhanphat_faq_item['number'] ) ); ?></span>
							<?php endif; ?>

							<span class="faq-card__question"><?php echo esc_html( $tmnhanphat_faq_item['question'] ); ?></span>

							<?php if ( $tmnhanphat_faq_icon_on ) : ?>
								<span class="faq-card__icon" aria-hidden="true">
									<svg class="faq-card__icon-plus" viewBox="0 0 24 24" width="16" height="16" focusable="false"><path d="M12 5v14M5 12h14" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
									<svg class="faq-card__icon-minus" viewBox="0 0 24 24" width="16" height="16" focusable="false"><path d="M5 12h14" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
								</span>
							<?php endif; ?>
						</button>
					</h3>

					<div
						class="faq-card__panel"
						id="<?php echo esc_attr( $tmnhanphat_faq_panel_id ); ?>"
						<?php echo $tmnhanphat_faq_is_open ? '' : 'hidden'; ?>
					>
						<div class="faq-card__answer">
							<?php echo wp_kses_post( $tmnhanphat_faq_item['answer'] ); ?>
						</div>
					</div>
				</article>
			<?php endforeach; ?>
		</div>
	</div>
</section>
