/**
 * Quote Contact Form ([data-tmnp-quote-form]) — validation Frontend thuần, không thư
 * viện ngoài (PROJECT_RULES.md mục 21). Bước này CHƯA gửi mail (backend sau, mục 15):
 * submit hợp lệ chỉ hiện thông báo tạm + reset form.
 *
 * Validation (mục 16): Tên không rỗng, Phone đúng định dạng, Email đúng định dạng,
 * Service phải chọn, Message không rỗng — CHỈ với field đang bật (data-required).
 * Lỗi hiển thị DƯỚI từng field (không alert). Field lỗi tự xoá lỗi khi người dùng sửa.
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

	function initForm( form ) {
		var controls = Array.prototype.slice.call(
			form.querySelectorAll( '.quote-field__input' )
		);
		var notice = form.querySelector( '[data-quote-notice]' );

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

			// Hợp lệ — bước này chưa gửi backend (mục 15). Hiện thông báo + reset.
			if ( notice ) {
				notice.hidden = false;
				notice.classList.add( 'is-success' );
				notice.textContent = 'Cảm ơn bạn! Yêu cầu đã được ghi nhận, chúng tôi sẽ liên hệ lại sớm.';
			}

			form.reset();
		} );
	}

	document.querySelectorAll( '[data-tmnp-quote-form]' ).forEach( initForm );
} )();
