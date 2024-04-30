( function( window, document, undefined ) {

	'use strict';

	/**
	 * Initialize recaptcha.
	 *
	 * @return {void}
	 */
	function initRecaptcha() {
		wpcf7_recaptcha = {
			...( wpcf7_recaptcha ?? {} )
		};

		const siteKey = wpcf7_recaptcha.sitekey;
		const { homepage, contactform } = wpcf7_recaptcha.actions;

		const execute = options => {
			const { action, func, params } = options;

			grecaptcha.execute( siteKey, {
				action,
			} ).then( token => {
				const event = new CustomEvent( 'wpcf7grecaptchaexecuted', {
					detail: {
						action,
						token
					}
				} );

				document.dispatchEvent( event );
			} ).then( () => {
				if ( typeof func === 'function' ) {
					func( ...params );
				}
			} ).catch( error => console.error( error ) );
		};

		grecaptcha.ready( () => {
			execute( {
				action: homepage
			} );
		} );

		document.addEventListener( 'change', event => {
			execute( {
				action: contactform
			} );
		} );

		if ( typeof wpcf7 !== 'undefined' && typeof wpcf7.submit === 'function' ) {
			const submit = wpcf7.submit;

			wpcf7.submit = ( form, options = {} ) => {
				execute( {
					action: contactform,
					func: submit,
					params: [ form, options ]
				} );
			};
		}

		document.addEventListener( 'wpcf7grecaptchaexecuted', event => {
			const fields = document.querySelectorAll( 'form.wpcf7-form input[name="_wpcf7_recaptcha_response"]' );

			for ( let i = 0; i < fields.length; i++ ) {
				let field = fields[ i ];

				field.setAttribute( 'value', event.detail.token );
			}
		} );
	}

	/**
	 * Handle cookies-unblocked event.
	 *
	 * @return {void}
	 */
	document.addEventListener( 'cookies-unblocked.hu', function( e ) {
		e.detail.data.scripts.forEach( function( script ) {
			// find google recaptcha in valid category
			if ( script.id === 'google-recaptcha-js' && e.detail.categories[script.dataset.huCategory] === true ) {
				script.onload = initRecaptcha;
				script.onreadystatechange = initRecaptcha;
			}
		} );
	}, false );

} )( window, document );