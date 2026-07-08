<?php
/**
 * Markup <head> + phần đầu <body> (site header). Header render qua template-parts/header/*
 * để header.php luôn ngắn gọn — xem PROJECT_RULES.md mục 4.
 *
 * Trạng thái transparent/sticky của <header> đọc từ inc/template-functions.php
 * (tmnhanphat_get_header_classes()), giá trị lấy từ Customizer panel "Header"
 * (inc/customizer/header-customizer.php) — xem PROJECT_RULES.md mục 22.
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

<header id="masthead" class="<?php echo esc_attr( implode( ' ', tmnhanphat_get_header_classes() ) ); ?>">
	<div class="site-header__inner">
		<?php
		get_template_part( 'template-parts/header/site-branding' );
		get_template_part( 'template-parts/header/navigation' );
		?>
	</div>
</header>
