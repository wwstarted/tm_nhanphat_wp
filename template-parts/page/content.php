<?php
/**
 * Nội dung 1 page — tách khỏi page.php để dễ tái sử dụng ở custom Page Template (PROJECT_RULES.md mục 4).
 *
 * @package TMNhanPhat
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<article id="post-<?php the_ID(); ?>" <?php post_class( 'page-content' ); ?>>
	<header class="page-content__header">
		<h1 class="page-content__title"><?php the_title(); ?></h1>
	</header>

	<?php if ( has_post_thumbnail() ) : ?>
		<div class="page-content__thumbnail">
			<?php the_post_thumbnail( 'tmnhanphat-hero' ); ?>
		</div>
	<?php endif; ?>

	<div class="page-content__body">
		<?php
		the_content();

		wp_link_pages( array(
			'before' => '<nav class="page-links" aria-label="' . esc_attr__( 'Các trang', 'tmnhanphat' ) . '">',
			'after'  => '</nav>',
		) );
		?>
	</div>
</article>
