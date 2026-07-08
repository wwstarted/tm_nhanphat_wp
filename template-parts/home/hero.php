<?php
/**
 * Hero section trang chủ — component độc lập (PROJECT_RULES.md mục 5).
 *
 * @package TMNhanPhat
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<section class="hero">
	<div class="container hero__inner">
		<h1 class="hero__title"><?php bloginfo( 'name' ); ?></h1>
		<?php if ( get_bloginfo( 'description' ) ) : ?>
			<p class="hero__subtitle"><?php bloginfo( 'description' ); ?></p>
		<?php endif; ?>
	</div>
</section>
