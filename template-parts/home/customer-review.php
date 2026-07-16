<?php
/**
 * Customer Review Section ("Đánh giá thực tế của khách hàng") — trang chủ. Component
 * độc lập (PROJECT_RULES.md mục 5). Dữ liệu 100% từ CPT customer_review qua
 * tmnhanphat_get_customer_review_columns() — KHÔNG wp_posts thường/category Blog/Page.
 * Mọi tuỳ chỉnh hiển thị từ Customizer panel "Customer Review Home" — không hardcode.
 *
 * Marquee dọc vô hạn 3 cột (KHÔNG slider/Swiper): mỗi cột render 2 LẦN cùng 1 danh
 * sách (clone ở TẦNG HIỂN THỊ, không đụng database) rồi CSS animation translateY
 * 0 → -50% loop — 2 nửa giống nhau tuyệt đối nên điểm nối vô hình, không cần đo
 * chiều cao bằng JS. Cột 2/3 chỉ ẨN bằng CSS ở tablet/mobile, không bớt DOM.
 *
 * Fallback (mục 4) xử lý sẵn trong tmnhanphat_get_customer_review_item() — CHỈ ở
 * tầng hiển thị, không ghi Database.
 *
 * SEO (mục 14/23): Heading dùng H2, Reviewer Name dùng đúng ngữ nghĩa (không heading
 * — review không phải nội dung chính trang). Không in Schema (Review/AggregateRating
 * là việc của Plugin SEO).
 *
 * @package TMNhanPhat
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! tmnhanphat_get_customer_review_mod( 'tmnhanphat_review_enable' ) ) {
	return;
}

$tmnhanphat_rv_columns = tmnhanphat_get_customer_review_columns();
$tmnhanphat_rv_total   = array_sum( array_map( 'count', $tmnhanphat_rv_columns ) );

if ( 0 === $tmnhanphat_rv_total ) {
	return; // Chưa có Review nào — không render section trống (mục 15).
}

$tmnhanphat_rv_section_id     = tmnhanphat_get_customer_review_mod( 'tmnhanphat_review_section_id' );
$tmnhanphat_rv_title          = tmnhanphat_get_customer_review_mod( 'tmnhanphat_review_title' );
$tmnhanphat_rv_description    = tmnhanphat_get_customer_review_mod( 'tmnhanphat_review_desc_text' );
$tmnhanphat_rv_animation_on   = tmnhanphat_get_customer_review_mod( 'tmnhanphat_review_animation_enable' );
$tmnhanphat_rv_pause_hover_on = tmnhanphat_get_customer_review_mod( 'tmnhanphat_review_pause_hover' );

// Figma: title 2 màu 1 dòng — phần đầu đỏ (Title Color), 2 TỪ CUỐI xanh ("khách hàng").
// Tách ở tầng render, Title trong Customizer vẫn là 1 chuỗi duy nhất (không đổi setting).
$tmnhanphat_rv_title_words  = preg_split( '/\s+/u', trim( (string) $tmnhanphat_rv_title ), -1, PREG_SPLIT_NO_EMPTY );
$tmnhanphat_rv_title_accent = '';
if ( is_array( $tmnhanphat_rv_title_words ) && count( $tmnhanphat_rv_title_words ) > 2 ) {
	$tmnhanphat_rv_title_accent = implode( ' ', array_slice( $tmnhanphat_rv_title_words, -2 ) );
	$tmnhanphat_rv_title        = implode( ' ', array_slice( $tmnhanphat_rv_title_words, 0, -2 ) );
}

/**
 * In 1 Review Card — hàm nội bộ template, gọi 2 lần/cột khi Animation bật (clone
 * hiển thị để loop vô hạn, KHÔNG đụng database).
 *
 * @param array $tmnhanphat_rv_item Item đã xử lý fallback từ tmnhanphat_get_customer_review_item().
 * @param bool  $tmnhanphat_rv_is_clone Đánh dấu bản clone — ẩn khỏi accessibility tree (tránh đọc trùng cho screen reader).
 */
if ( ! function_exists( 'tmnhanphat_render_review_card' ) ) {
	function tmnhanphat_render_review_card( $tmnhanphat_rv_item, $tmnhanphat_rv_is_clone = false ) {
		?>
		<article class="review-card"<?php echo $tmnhanphat_rv_is_clone ? ' aria-hidden="true"' : ''; ?>>
			<div class="review-card__head">
				<span class="review-card__avatar">
					<?php if ( $tmnhanphat_rv_item['has_avatar'] ) : ?>
						<?php
						echo wp_get_attachment_image( $tmnhanphat_rv_item['avatar_id'], 'tmnhanphat-review-avatar', false, array(
							'class' => 'review-card__avatar-image',
							'alt'   => $tmnhanphat_rv_item['name'],
						) );
						?>
					<?php else : ?>
						<?php echo tmnhanphat_get_customer_review_default_avatar_svg(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- SVG tĩnh, không chứa dữ liệu động. ?>
					<?php endif; ?>
				</span>

				<span class="review-card__identity">
					<span class="review-card__name"><?php echo esc_html( $tmnhanphat_rv_item['name'] ); ?></span>
					<span class="review-card__role"><?php echo esc_html( $tmnhanphat_rv_item['role'] ); ?></span>
				</span>
			</div>

			<p class="review-card__comment"><?php echo $tmnhanphat_rv_item['comment']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- đã esc_html() sẵn trong tmnhanphat_get_excerpt()/hardcode fallback. ?></p>
		</article>
		<?php
	}
}
?>
<section
	class="review-home"
	<?php if ( $tmnhanphat_rv_section_id ) : ?>
		id="<?php echo esc_attr( $tmnhanphat_rv_section_id ); ?>"
	<?php endif; ?>
>
	<div class="container review-home__container">
		<header class="review-home__header">
			<?php if ( $tmnhanphat_rv_title || $tmnhanphat_rv_title_accent ) : ?>
				<h2 class="review-home__heading">
					<?php echo esc_html( $tmnhanphat_rv_title ); ?><?php if ( $tmnhanphat_rv_title_accent ) : ?>
						<span class="review-home__heading-accent"><?php echo esc_html( $tmnhanphat_rv_title_accent ); ?></span>
					<?php endif; ?>
				</h2>
			<?php endif; ?>

			<?php if ( $tmnhanphat_rv_description ) : ?>
				<p class="review-home__description"><?php echo esc_html( $tmnhanphat_rv_description ); ?></p>
			<?php endif; ?>
		</header>

		<div
			class="review-marquee<?php echo $tmnhanphat_rv_pause_hover_on ? ' review-marquee--pause-hover' : ''; ?>"
			data-tmnp-review-marquee
		>
			<?php if ( $tmnhanphat_rv_animation_on ) : ?>
				<?php // Nút Tạm dừng/Phát — WCAG 2.2.2: nội dung tự chuyển động cần cơ chế pause NHÌN THẤY được (pause-hover không dùng được trên touch). JS toggle .is-paused (customer-review.js). ?>
				<button
					type="button"
					class="review-marquee__toggle"
					data-marquee-toggle
					aria-pressed="false"
					aria-label="<?php esc_attr_e( 'Tạm dừng cuộn tự động', 'tmnhanphat' ); ?>"
				>
					<svg class="review-marquee__toggle-pause" viewBox="0 0 16 16" width="14" height="14" fill="currentColor" aria-hidden="true"><rect x="3" y="2" width="4" height="12" rx="1"/><rect x="9" y="2" width="4" height="12" rx="1"/></svg>
					<svg class="review-marquee__toggle-play" viewBox="0 0 16 16" width="14" height="14" fill="currentColor" aria-hidden="true"><path d="M4.5 2.5a1 1 0 0 1 1.53-.85l8 5.5a1 1 0 0 1 0 1.7l-8 5.5a1 1 0 0 1-1.53-.85v-11Z"/></svg>
				</button>
			<?php endif; ?>
			<?php foreach ( $tmnhanphat_rv_columns as $tmnhanphat_rv_col_index => $tmnhanphat_rv_items ) : ?>
				<?php if ( empty( $tmnhanphat_rv_items ) ) : ?>
					<?php continue; ?>
				<?php endif; ?>
				<?php
				// Đệm danh sách khi cột quá ngắn (1-2 review): lặp lại chính danh sách gốc
				// (1234 → 12341234...) cho tới khi 1 nửa track đủ dài PHỦ KÍN khung nhìn —
				// nếu không, lúc animation cuộn sẽ lộ khoảng trắng ở đáy cột (nửa danh
				// sách nhân đôi vẫn ngắn hơn viewport). Vẫn là clone TẦNG HIỂN THỊ.
				$tmnhanphat_rv_render_items = $tmnhanphat_rv_items;
				if ( $tmnhanphat_rv_animation_on ) {
					while ( count( $tmnhanphat_rv_render_items ) < 5 ) {
						$tmnhanphat_rv_render_items = array_merge( $tmnhanphat_rv_render_items, $tmnhanphat_rv_items );
					}
				}
				$tmnhanphat_rv_original_count = count( $tmnhanphat_rv_items );
				?>
				<div class="review-marquee__column" data-column="<?php echo esc_attr( $tmnhanphat_rv_col_index + 1 ); ?>">
					<div class="review-marquee__track">
						<?php foreach ( $tmnhanphat_rv_render_items as $tmnhanphat_rv_item_index => $tmnhanphat_rv_item ) : ?>
							<?php // Item đệm (vượt quá danh sách gốc) cũng là bản lặp — ẩn khỏi screen reader như clone. ?>
							<?php tmnhanphat_render_review_card( $tmnhanphat_rv_item, $tmnhanphat_rv_item_index >= $tmnhanphat_rv_original_count ); ?>
						<?php endforeach; ?>
						<?php if ( $tmnhanphat_rv_animation_on ) : ?>
							<?php // Clone TOÀN BỘ danh sách (đã đệm) để có 2 nửa giống nhau tuyệt đối — translateY -50% loop vô hạn, điểm nối vô hình (mục 8). ?>
							<?php foreach ( $tmnhanphat_rv_render_items as $tmnhanphat_rv_item ) : ?>
								<?php tmnhanphat_render_review_card( $tmnhanphat_rv_item, true ); ?>
							<?php endforeach; ?>
						<?php endif; ?>
					</div>
				</div>
			<?php endforeach; ?>
		</div>
	</div>
</section>
