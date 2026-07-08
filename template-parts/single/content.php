<?php
/**
 * Nội dung chính của bài viết (title, meta, thumbnail, body).
 *
 * @package TMNhanPhat
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<article id="post-<?php the_ID(); ?>" <?php post_class( 'single-content' ); ?>>
	<header class="single-content__header">
		<h1 class="single-content__title"><?php the_title(); ?></h1>

		<p class="single-content__meta">
			<span class="single-content__author"><?php the_author(); ?></span>
			&middot;
			<time datetime="<?php echo esc_attr( get_the_date( 'c' ) ); ?>"><?php echo esc_html( get_the_date() ); ?></time>
			&middot;
			<?php
			/* translators: %d: số phút đọc */
			printf( esc_html__( '%d phút đọc', 'tmnhanphat' ), (int) tmnhanphat_reading_time( get_the_ID() ) );
			?>
		</p>
	</header>

	<?php if ( has_post_thumbnail() ) : ?>
		<div class="single-content__thumbnail">
			<?php the_post_thumbnail( 'tmnhanphat-hero', array( 'loading' => 'eager', 'fetchpriority' => 'high' ) ); ?>
		</div>
	<?php endif; ?>

	<div class="single-content__body">
		<?php
		the_content();

		wp_link_pages( array(
			'before' => '<nav class="page-links" aria-label="' . esc_attr__( 'Các trang', 'tmnhanphat' ) . '">',
			'after'  => '</nav>',
		) );
		?>
	</div>

	<footer class="single-content__footer">
		<?php
		$tmnhanphat_tags = get_the_tag_list( '', ', ' );
		if ( $tmnhanphat_tags ) :
			?>
			<div class="single-content__tags"><?php echo wp_kses_post( $tmnhanphat_tags ); ?></div>
		<?php endif; ?>
	</footer>
</article>
