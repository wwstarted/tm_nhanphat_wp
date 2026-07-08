<?php
/**
 * Entry point cho 1 item trong vòng lặp archive/category/tag/author/index.
 * Tái sử dụng component post-card thay vì lặp lại markup (PROJECT_RULES.md mục 5 & 11).
 *
 * @package TMNhanPhat
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_template_part( 'template-parts/components/post-card' );
