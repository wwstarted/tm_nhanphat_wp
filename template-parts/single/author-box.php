<?php
/**
 * Thông tin tác giả cuối bài viết.
 *
 * @package TMNhanPhat
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<div class="author-box">
	<div class="author-box__avatar">
		<?php echo get_avatar( get_the_author_meta( 'ID' ), 80 ); ?>
	</div>

	<div class="author-box__body">
		<p class="author-box__name">
			<a href="<?php echo esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ); ?>">
				<?php the_author(); ?>
			</a>
		</p>

		<?php if ( get_the_author_meta( 'description' ) ) : ?>
			<p class="author-box__bio"><?php echo esc_html( get_the_author_meta( 'description' ) ); ?></p>
		<?php endif; ?>
	</div>
</div>
