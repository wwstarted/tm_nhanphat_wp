<?php
/**
 * TMNhanPhat Theme bootstrap.
 *
 * File này chỉ có nhiệm vụ require các module trong inc/.
 * Toàn bộ logic thực tế nằm trong từng file inc/*.php tương ứng — xem PROJECT_RULES.md mục 8.
 *
 * @package TMNhanPhat
 */

if (!defined('ABSPATH')) {
	exit;
}

define('TMNHANPHAT_VERSION', '1.0.0');
define('TMNHANPHAT_DIR', get_template_directory());
define('TMNHANPHAT_URI', get_template_directory_uri());

add_filter('show_admin_bar', '__return_false');


/**
 * Danh sách module bootstrap theo đúng thứ tự cần thiết
 * (setup phải load trước enqueue vì enqueue dùng theme supports).
 */
$tmnhanphat_modules = array(
	'inc/helpers.php',
	'inc/template-functions.php',
	'inc/setup.php',
	'inc/post-types.php',
	'inc/enqueue.php',
	'inc/menus.php',
	'inc/widgets.php',
	'inc/images.php',
	'inc/breadcrumbs.php',
	'inc/pagination.php',
	'inc/security.php',
	'inc/cleanup.php',
	'inc/performance.php',
	'inc/ajax.php',
	'inc/api.php',
	'inc/customizer.php',
);

foreach ($tmnhanphat_modules as $tmnhanphat_module) {
	$tmnhanphat_module_path = TMNHANPHAT_DIR . '/' . $tmnhanphat_module;

	if (file_exists($tmnhanphat_module_path)) {
		require_once $tmnhanphat_module_path;
	}
}
unset($tmnhanphat_modules, $tmnhanphat_module, $tmnhanphat_module_path);