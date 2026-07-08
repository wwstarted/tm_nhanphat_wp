<?php
/**
 * Nút chia sẻ mạng xã hội — component thuần liên kết, không phụ thuộc JS/thư viện ngoài
 * (PROJECT_RULES.md mục 15: không thêm thư viện nếu JS thuần đủ dùng).
 *
 * @package TMNhanPhat
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$tmnhanphat_url   = rawurlencode( get_permalink() );
$tmnhanphat_title = rawurlencode( get_the_title() );
?>
<div class="share-box">
	<span class="share-box__label"><?php esc_html_e( 'Chia sẻ:', 'tmnhanphat' ); ?></span>

	<a
		class="share-box__link share-box__link--facebook"
		href="https://www.facebook.com/sharer/sharer.php?u=<?php echo esc_attr( $tmnhanphat_url ); ?>"
		target="_blank"
		rel="noopener noreferrer nofollow"
		aria-label="<?php esc_attr_e( 'Chia sẻ lên Facebook', 'tmnhanphat' ); ?>"
	>Facebook</a>

	<a
		class="share-box__link share-box__link--twitter"
		href="https://twitter.com/intent/tweet?url=<?php echo esc_attr( $tmnhanphat_url ); ?>&text=<?php echo esc_attr( $tmnhanphat_title ); ?>"
		target="_blank"
		rel="noopener noreferrer nofollow"
		aria-label="<?php esc_attr_e( 'Chia sẻ lên X (Twitter)', 'tmnhanphat' ); ?>"
	>X</a>
</div>
