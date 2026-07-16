/**
 * Quote Contact Form ([data-tmnp-quote-form]) — validation Frontend thuần, không thư
 * viện ngoài (PROJECT_RULES.md mục 21). Submit hợp lệ gửi AJAX tới handler
 * tmnhanphat_ajax_submit_quote (inc/ajax.php — validate lại server + wp_mail), dùng
 * ajaxUrl/nonce từ window.tmnhanphatData (wp_localize_script gắn vào handle app).
 *
 * Validation (mục 16): Tên không rỗng, Phone đúng định dạng, Email đúng định dạng,
 * Service phải chọn, Message không rỗng — CHỈ với field đang bật (data-required).
 * Lỗi hiển thị DƯỚI từng field (không alert). Field lỗi tự xoá lỗi khi người dùng sửa.
 * Lỗi server theo field (errors{name: message}) cũng đổ về đúng field tương ứng.
 */
( function () {
	'use strict';

	var PHONE_RE = /^[0-9()+\-.\s]{8,15}$/;
	var EMAIL_RE = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

	function setError( field, message ) {
		field.classList.add( 'has-error' );
		var el = field.querySelector( '[data-error]' );
		if ( el ) {
			el.textContent = message;
		}
	}

	function clearError( field ) {
		field.classList.remove( 'has-error' );
		var el = field.querySelector( '[data-error]' );
		if ( el ) {
			el.textContent = '';
		}
	}

	function validateControl( control ) {
		var field = control.closest( '.quote-field' );
		if ( ! field ) {
			return true;
		}

		var required = control.getAttribute( 'data-required' ) === 'true';
		var value = ( control.value || '' ).trim();
		var name = control.getAttribute( 'name' ) || '';

		// Field không bắt buộc + để trống → hợp lệ, không kiểm tra định dạng.
		if ( ! required && '' === value ) {
			clearError( field );
			return true;
		}

		if ( required && '' === value ) {
			setError( field, 'quote_service' === name ? 'Vui lòng chọn loại dịch vụ.' : 'Vui lòng điền thông tin này.' );
			return false;
		}

		if ( 'quote_phone' === name && ! PHONE_RE.test( value ) ) {
			setError( field, 'Số điện thoại không hợp lệ.' );
			return false;
		}

		if ( 'quote_email' === name && ! EMAIL_RE.test( value ) ) {
			setError( field, 'Email không hợp lệ.' );
			return false;
		}

		clearError( field );
		return true;
	}

	function showNotice( notice, message, isSuccess ) {
		if ( ! notice ) {
			return;
		}

		notice.hidden = false;
		notice.classList.toggle( 'is-success', isSuccess );
		notice.textContent = message;
	}

	function initForm( form ) {
		var controls = Array.prototype.slice.call(
			form.querySelectorAll( '.quote-field__input' )
		);
		var notice = form.querySelector( '[data-quote-notice]' );
		var submitBtn = form.querySelector( '.quote-form__submit' );
		var isSending = false;

		// Xoá lỗi ngay khi người dùng sửa lại field.
		controls.forEach( function ( control ) {
			var evt = 'SELECT' === control.tagName ? 'change' : 'input';
			control.addEventListener( evt, function () {
				var field = control.closest( '.quote-field' );
				if ( field && field.classList.contains( 'has-error' ) ) {
					validateControl( control );
				}
			} );
		} );

		form.addEventListener( 'submit', function ( event ) {
			event.preventDefault();

			if ( isSending ) {
				return; // Chống double-submit khi request trước chưa xong.
			}

			var valid = true;
			var firstInvalid = null;

			controls.forEach( function ( control ) {
				if ( ! validateControl( control ) ) {
					valid = false;
					if ( ! firstInvalid ) {
						firstInvalid = control;
					}
				}
			} );

			if ( ! valid ) {
				if ( firstInvalid ) {
					firstInvalid.focus();
				}
				return;
			}

			var config = window.tmnhanphatData || {};

			if ( ! config.ajaxUrl || ! window.fetch ) {
				// Không có endpoint/fetch (rất hiếm) — không được báo thành công giả.
				showNotice( notice, 'Không thể gửi yêu cầu lúc này. Vui lòng liên hệ trực tiếp qua hotline.', false );
				return;
			}

			var data = new FormData( form );
			data.append( 'action', 'tmnhanphat_submit_quote' );
			data.append( 'nonce', config.nonce || '' );

			isSending = true;
			form.classList.add( 'is-loading' );
			if ( submitBtn ) {
				submitBtn.disabled = true;
			}
			if ( notice ) {
				notice.hidden = true;
				notice.classList.remove( 'is-success' );
			}

			window.fetch( config.ajaxUrl, {
				method: 'POST',
				credentials: 'same-origin',
				body: data
			} )
				.then( function ( response ) {
					return response.json();
				} )
				.then( function ( result ) {
					if ( result && result.success ) {
						showNotice( notice, ( result.data && result.data.message ) || 'Cảm ơn bạn! Yêu cầu đã được gửi, chúng tôi sẽ liên hệ lại sớm.', true );
						form.reset();
						return;
					}

					// Lỗi validate server theo field — đổ về đúng field tương ứng.
					var errors = result && result.data && result.data.errors;
					if ( errors ) {
						controls.forEach( function ( control ) {
							var name = control.getAttribute( 'name' ) || '';
							if ( errors[ name ] ) {
								var field = control.closest( '.quote-field' );
								if ( field ) {
									setError( field, errors[ name ] );
								}
							}
						} );
					}

					showNotice( notice, ( result && result.data && result.data.message ) || 'Không thể gửi yêu cầu lúc này. Vui lòng thử lại sau.', false );
				} )
				.catch( function () {
					showNotice( notice, 'Không thể gửi yêu cầu lúc này. Vui lòng kiểm tra kết nối và thử lại.', false );
				} )
				.then( function () {
					isSending = false;
					form.classList.remove( 'is-loading' );
					if ( submitBtn ) {
						submitBtn.disabled = false;
					}
				} );
		} );
	}

	document.querySelectorAll( '[data-tmnp-quote-form]' ).forEach( initForm );
} )();
