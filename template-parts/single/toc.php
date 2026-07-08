<?php
/**
 * Mục lục bài viết (Table of Contents) — tự build từ heading H2 trong nội dung.
 * Ẩn hoàn toàn nếu bài viết không có H2 nào.
 *
 * @package TMNhanPhat
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$tmnhanphat_heading_count = preg_match_all( '/<h2[^>]*>(.*?)<\/h2>/i', get_the_content(), $tmnhanphat_matches );

if ( ! $tmnhanphat_heading_count ) {
	return;
}
?>
<nav class="toc" aria-label="<?php esc_attr_e( 'Mục lục', 'tmnhanphat' ); ?>">
	<p class="toc__title"><?php esc_html_e( 'Mục lục', 'tmnhanphat' ); ?></p>
	<ol class="toc__list">
		<?php foreach ( $tmnhanphat_matches[1] as $tmnhanphat_heading ) : ?>
			<?php $tmnhanphat_heading_text = wp_strip_all_tags( $tmnhanphat_heading ); ?>
			<li>
				<a href="#<?php echo esc_attr( sanitize_title( $tmnhanphat_heading_text ) ); ?>">
					<?php echo esc_html( $tmnhanphat_heading_text ); ?>
				</a>
			</li>
		<?php endforeach; ?>
	</ol>
</nav>
