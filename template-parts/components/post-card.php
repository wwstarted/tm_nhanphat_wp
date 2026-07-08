<?php
/**
 * Post Card component — dùng ở front-page, archive, related-posts, search.
 * Định nghĩa một lần duy nhất (PROJECT_RULES.md mục 5).
 *
 * @package TMNhanPhat
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<article id="post-<?php the_ID(); ?>" <?php post_class( 'post-card' ); ?>>
	<?php if ( has_post_thumbnail() ) : ?>
		<a class="post-card__thumbnail" href="<?php the_permalink(); ?>" aria-hidden="true" tabindex="-1">
			<?php the_post_thumbnail( 'tmnhanphat-card' ); ?>
		</a>
	<?php endif; ?>

	<div class="post-card__body">
		<h3 class="post-card__title">
			<a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
		</h3>

		<p class="post-card__meta">
			<time datetime="<?php echo esc_attr( get_the_date( 'c' ) ); ?>"><?php echo esc_html( get_the_date() ); ?></time>
			&middot;
			<?php
			/* translators: %d: số phút đọc */
			printf( esc_html__( '%d phút đọc', 'tmnhanphat' ), (int) tmnhanphat_reading_time( get_the_ID() ) );
			?>
		</p>

		<p class="post-card__excerpt"><?php echo esc_html( tmnhanphat_get_excerpt( get_the_ID(), 20 ) ); ?></p>
	</div>
</article>
