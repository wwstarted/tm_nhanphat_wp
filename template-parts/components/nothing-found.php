<?php
/**
 * Trạng thái "không có bài viết" — dùng chung cho archive/search rỗng.
 *
 * @package TMNhanPhat
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<section class="nothing-found">
	<h2><?php esc_html_e( 'Không tìm thấy nội dung nào', 'tmnhanphat' ); ?></h2>
	<p><?php esc_html_e( 'Rất tiếc, không có kết quả phù hợp với yêu cầu của bạn.', 'tmnhanphat' ); ?></p>

	<?php get_search_form(); ?>
</section>
