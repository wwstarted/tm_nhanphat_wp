/**
 * Hàm JS dùng chung nhiều nơi, gắn vào namespace window.tmnhanphat để các file JS khác
 * dùng lại được mà không cần module bundler (JS thuần — xem PROJECT_RULES.md mục 21).
 */
window.tmnhanphat = window.tmnhanphat || {};

/**
 * Debounce một hàm — dùng cho scroll/resize handler (layout/header.js...).
 *
 * @param {Function} fn Hàm cần debounce.
 * @param {number} delay Thời gian chờ (ms).
 * @returns {Function}
 */
window.tmnhanphat.debounce = function ( fn, delay ) {
	delay = delay || 150;
	var timeoutId;

	return function () {
		var context = this;
		var args = arguments;
		clearTimeout( timeoutId );
		timeoutId = setTimeout( function () {
			fn.apply( context, args );
		}, delay );
	};
};
