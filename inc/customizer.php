<?php
/**
 * Bootstrap toàn bộ module Customizer trong inc/customizer/*.php.
 *
 * Thêm 1 Panel/Section Customizer mới (ví dụ Footer) chỉ cần tạo file mới trong
 * thư mục inc/customizer/ (ví dụ inc/customizer/footer-customizer.php) — không cần
 * sửa file này hay functions.php (PROJECT_RULES.md mục 17: dễ mở rộng).
 *
 * @package TMNhanPhat
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

foreach ( glob( TMNHANPHAT_DIR . '/inc/customizer/*.php' ) as $tmnhanphat_customizer_file ) {
	require_once $tmnhanphat_customizer_file;
}
unset( $tmnhanphat_customizer_file );
