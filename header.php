<?php
/**
 * Markup <head> + phần đầu <body> (site header). Header render qua template-parts/header/*
 * để header.php luôn ngắn gọn — xem PROJECT_RULES.md mục 4.
 *
 * @package TMNhanPhat
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<!doctype html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>" />
	<meta name="viewport" content="width=device-width, initial-scale=1" />
	<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<a class="skip-link" href="#main"><?php esc_html_e( 'Bỏ qua đến nội dung chính', 'tmnhanphat' ); ?></a>

<header id="masthead" class="site-header">
	<?php
	get_template_part( 'template-parts/header/site-branding' );
	get_template_part( 'template-parts/header/navigation' );
	?>
</header>
