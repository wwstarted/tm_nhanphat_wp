<?php
/**
 * Khu vực bình luận (comment list + comment form).
 *
 * @package TMNhanPhat
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( post_password_required() ) {
	return;
}
?>
<section id="comments" class="comments-area">

	<?php if ( have_comments() ) : ?>
		<h2 class="comments-title">
			<?php
			$comments_count = get_comments_number();
			printf(
				/* translators: %s: số lượng bình luận */
				esc_html( _n( '%s bình luận', '%s bình luận', $comments_count, 'tmnhanphat' ) ),
				esc_html( number_format_i18n( $comments_count ) )
			);
			?>
		</h2>

		<ol class="comment-list">
			<?php
			wp_list_comments( array(
				'style'      => 'ol',
				'short_ping' => true,
			) );
			?>
		</ol>

		<?php the_comments_pagination(); ?>
	<?php endif; ?>

	<?php if ( ! comments_open() && get_comments_number() ) : ?>
		<p class="no-comments"><?php esc_html_e( 'Bình luận đã đóng.', 'tmnhanphat' ); ?></p>
	<?php endif; ?>

	<?php comment_form(); ?>

</section>
