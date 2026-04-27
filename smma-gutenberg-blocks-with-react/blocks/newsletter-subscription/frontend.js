/**
 * Newsletter Subscription — front-end form handler.
 * Attached via wp_enqueue_scripts in index.php.
 * Handles AJAX-style submission to the smma/v1/newsletter-subscribe REST endpoint.
 */
( function () {
	'use strict';

	/**
	 * Initialises all newsletter forms on the page.
	 */
	function initNewsletterForms() {
		const forms = document.querySelectorAll( '.smma-newsletter__form' );

		forms.forEach( function ( form ) {
			form.addEventListener( 'submit', handleSubmit );
		} );
	}

	/**
	 * Handles the form submit event.
	 *
	 * @param {Event} event The submit event.
	 */
	function handleSubmit( event ) {
		event.preventDefault();

		const form        = event.currentTarget;
		const apiUrl      = form.dataset.apiUrl;
		const nonce       = form.dataset.nonce;
		const successMsg  = form.dataset.success;
		const messageEl   = form.querySelector( '.smma-newsletter__message' );
		const button      = form.querySelector( '.smma-newsletter__button' );
		const firstName   = form.querySelector( '[name="first_name"]' ).value.trim();
		const lastName    = form.querySelector( '[name="last_name"]' ).value.trim();
		const email       = form.querySelector( '[name="email"]' ).value.trim();

		// Basic client-side validation.
		if ( ! firstName || ! lastName || ! email ) {
			showMessage( messageEl, __( 'Please fill in all fields.' ), 'error' );
			return;
		}

		if ( ! isValidEmail( email ) ) {
			showMessage( messageEl, __( 'Please enter a valid email address.' ), 'error' );
			return;
		}

		setLoading( form, button, true );

		fetch( apiUrl, {
			method: 'POST',
			headers: {
				'Content-Type': 'application/json',
				'X-WP-Nonce': nonce,
			},
			body: JSON.stringify( {
				first_name: firstName,
				last_name: lastName,
				email: email,
			} ),
		} )
			.then( function ( response ) {
				return response.json().then( function ( data ) {
					return { ok: response.ok, data: data };
				} );
			} )
			.then( function ( result ) {
				if ( result.ok && result.data.success ) {
					showMessage( messageEl, successMsg || result.data.message, 'success' );
					form.reset();
				} else {
					showMessage(
						messageEl,
						result.data.message || __( 'Something went wrong. Please try again.' ),
						'error'
					);
				}
			} )
			.catch( function () {
				showMessage( messageEl, __( 'Network error. Please check your connection and try again.' ), 'error' );
			} )
			.finally( function () {
				setLoading( form, button, false );
			} );
	}

	/**
	 * Displays a status message inside the form.
	 *
	 * @param {Element} el      The message container element.
	 * @param {string}  message The message text.
	 * @param {string}  type    'success' or 'error'.
	 */
	function showMessage( el, message, type ) {
		if ( ! el ) {
			return;
		}
		el.textContent = message;
		el.className   = 'smma-newsletter__message smma-newsletter__message--' + type;
	}

	/**
	 * Toggles the loading state on the form and submit button.
	 *
	 * @param {Element} form    The form element.
	 * @param {Element} button  The submit button.
	 * @param {boolean} loading Whether loading is active.
	 */
	function setLoading( form, button, loading ) {
		if ( loading ) {
			form.classList.add( 'smma-newsletter__form--loading' );
			button.setAttribute( 'disabled', 'disabled' );
		} else {
			form.classList.remove( 'smma-newsletter__form--loading' );
			button.removeAttribute( 'disabled' );
		}
	}

	/**
	 * Validates an email address string.
	 *
	 * @param {string} email Email string to test.
	 * @return {boolean} Whether the email looks valid.
	 */
	function isValidEmail( email ) {
		return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test( email );
	}

	/**
	 * Minimal translation stub (real i18n is handled server-side).
	 *
	 * @param {string} text Source string.
	 * @return {string} The string unchanged.
	 */
	function __( text ) {
		return text;
	}

	// Boot once DOM is ready.
	if ( document.readyState === 'loading' ) {
		document.addEventListener( 'DOMContentLoaded', initNewsletterForms );
	} else {
		initNewsletterForms();
	}
} )();
