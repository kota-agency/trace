( function( $ ) {

	// ready event
	$( function() {
		var cnHiddenElements = {};

		// listen for the load
		document.addEventListener( 'load.hu', function( e ) {
			// set widget text strings
			hu.setTexts( cnFrontWelcome.textStrings );
		} );

		// listen for the reload
		document.addEventListener( 'reload.hu', function( e ) {
			var container = $( '#hu' );
			var customOptions = { config: {
				dontSellLink: true,
				privacyPolicyLink: true,
				privacyPaper: true,
				privacyContact: true
			} };

			// set widget options
			hu.setOptions( customOptions );
		} );

		// listen for the display
		document.addEventListener( 'display.hu', function( e ) {
			var val = [];
			var container = $( '#hu' );
			var customOptions = { config: {
				// make it empty
			} };

			$( parent.document ).find( 'input[name="cn_laws"]:checked' ).each( function() {
				val.push( $( this ).val() );
			} );

			// hide paper and contact
			if ( $( parent.document ).find( 'input[name="cn_privacy_paper"]' ).prop( 'checked' ) === true )
				$( container ).find( '#hu-cookies-paper' ).show();
			else
				$( container ).find( '#hu-cookies-paper' ).hide();

			if ( $( parent.document ).find( 'input[name="cn_privacy_contact"]' ).prop( 'checked' ) === true )
				$( container ).find( '#hu-cookies-contact' ).show();
			else
				$( container ).find( '#hu-cookies-contact' ).hide();

			if ( $.inArray( 'ccpa', val ) !== -1 ) {
				var htmlElement = $( $( container ).find( '#hu-cookies-notice-dontsell-btn' ) );

				if ( htmlElement.length === 0 ) {
					$( '#hu-policy-links' ).append( cnHiddenElements.ccpa );

					delete cnHiddenElements.ccpa;
				}

				$.extend( customOptions.config, { dontSellLink: true } );
			} else {
				var htmlElement = $( $( container ).find( '#hu-cookies-notice-dontsell-btn' ) );

				// add to hidden elements
				if ( htmlElement ) {
					cnHiddenElements['ccpa'] = htmlElement;

					// remove el
					$( htmlElement ).remove();
				}

				$.extend( customOptions.config, { dontSellLink: false } );
			}

			if ( $.inArray( 'gdpr', val ) !== -1 ) {
				var htmlElement = $( $( container ).find( '#hu-cookies-notice-privacy-btn' ) );

				if ( htmlElement.length === 0 ) {
					$( '#hu-policy-links' ).prepend( cnHiddenElements.gdpr );

					delete cnHiddenElements.gdpr;
				}

				$.extend( customOptions.config, { privacyPolicyLink: true } );
			} else {
				var htmlElement = $( $( container ).find( '#hu-cookies-notice-privacy-btn' ) );

				// add to hidden elements
				if ( htmlElement ) {
					cnHiddenElements['gdpr'] = htmlElement;

					// remove el
					$( htmlElement ).remove();
				}

				$.extend( customOptions.config, { privacyPolicyLink: false } );
			}

			// set widget options
			hu.setOptions( customOptions );
		} );

		// listen for the parent
		window.addEventListener( 'message', function( event ) {
			var iframe = $( parent.document ).find( '#cn_iframe_id' );
			var form = $( parent.document ).find( '#cn-form-configure' );

			// add spinner
			$( iframe ).closest( '.has-loader' ).addClass( 'cn-loading' ).append( '<span class="cn-spinner"></span>' );

			// lock options
			$( form ).addClass( 'cn-form-disabled' );

			// emit loader
			window.setTimeout( function() {
				if ( typeof event.data == 'object' ) {
					var container = $( '#hu' );
					var option = event.data.call;
					var customOptions = {};
					var customTexts = {};

					switch ( option ) {
						case 'position':
							$( container ).removeClass( 'hu-position-bottom hu-position-top hu-position-left hu-position-right hu-position-center' );
							$( container ).addClass( 'hu-position-' + event.data.value );

							customOptions = { design: { position: event.data.value } }
							break;

						case 'naming':
							var level1 = $( '.hu-cookies-notice-consent-choices-1' );
							var level2 = $( '.hu-cookies-notice-consent-choices-2' );
							var level3 = $( '.hu-cookies-notice-consent-choices-3' );
							var text1 = cnFrontWelcome.levelNames[event.data.value][1];
							var text2 = cnFrontWelcome.levelNames[event.data.value][2];
							var text3 = cnFrontWelcome.levelNames[event.data.value][3];

							// apply text to dom elements
							$( level1 ).find( '.hu-toggle-label' ).text( text1 );
							$( level2 ).find( '.hu-toggle-label' ).text( text2 );
							$( level3 ).find( '.hu-toggle-label' ).text( text3 );

							// apply text to text strings
							customTexts = {
								levelNameText_1: text1,
								levelNameText_2: text2,
								levelNameText_3: text3
							}
							break;

						case 'laws':
							customOptions.config = {}

							if ( $.inArray( 'ccpa', event.data.value ) !== -1 ) {
								var htmlElement = $( container ).find( '#hu-cookies-notice-dontsell-btn' );

								if ( htmlElement.length === 0 ) {
									$( '#hu-policy-links' ).append( cnHiddenElements.ccpa );

									delete cnHiddenElements.ccpa;
								}

								$.extend( customOptions.config, { dontSellLink: true } );
							} else {
								var htmlElement = $( container ).find( '#hu-cookies-notice-dontsell-btn' );

								// add to hidden elements
								if ( htmlElement && ! cnHiddenElements.hasOwnProperty( 'ccpa' ) ) {
									cnHiddenElements['ccpa'] = htmlElement;

									// remove el
									$( htmlElement ).remove();
								}

								$.extend( customOptions.config, { dontSellLink: false } );
							}

							if ( $.inArray( 'gdpr', event.data.value ) !== -1 ) {
								var htmlElement = $( container ).find( '#hu-cookies-notice-privacy-btn' );

								if ( htmlElement.length === 0 ) {
									$( '#hu-policy-links' ).prepend( cnHiddenElements.gdpr );

									delete cnHiddenElements.gdpr;
								}

								$.extend( customOptions.config, { privacyPolicyLink: true } );
							} else {
								var htmlElement = $( container ).find( '#hu-cookies-notice-privacy-btn' );

								// add to hidden elements
								if ( htmlElement && ! cnHiddenElements.hasOwnProperty( 'gdpr' ) ) {
									cnHiddenElements['gdpr'] = htmlElement;

									// remove el
									$( htmlElement ).remove();
								}

								$.extend( customOptions.config, { privacyPolicyLink: false } );
							}

							break;

						case 'privacy_paper':
							var value = event.data.value === true;
							var htmlElement = $( container ).find( '#hu-cookies-paper' );

							if ( value )
								$( htmlElement ).show();
							else
								$( htmlElement ).hide();

							$.extend( customOptions.config, { privacyPaper: value } );
							break;

						case 'privacy_contact':
							var value = event.data.value === true;
							var htmlElement = $( container ).find( '#hu-cookies-contact');

							if ( value )
								$( htmlElement ).show();
							else
								$( htmlElement ).hide();

							$.extend( customOptions.config, { privacyContact: value } );
							break;

						case 'color_primary':
							var iframeContents = $( iframe ).contents()[0];

							iframeContents.documentElement.style.setProperty( '--hu-primaryColor', event.data.value );
							customOptions = { design: { primaryColor: event.data.value } }
							break;

						case 'color_background':
							var iframeContents = $( iframe ).contents()[0];

							iframeContents.documentElement.style.setProperty( '--hu-bannerColor', event.data.value );
							customOptions = { design: { bannerColor: event.data.value } }
							break;

						case 'color_border':
							var iframeContents = $( iframe ).contents()[0];

							iframeContents.documentElement.style.setProperty( '--hu-borderColor', event.data.value );
							customOptions = { design: { borderColor: event.data.value } }
							break;

						case 'color_text':
							var iframeContents = $( iframe ).contents()[0];

							iframeContents.documentElement.style.setProperty( '--hu-textColor', event.data.value );
							customOptions = { design: { textColor: event.data.value } }
							break;

						case 'color_heading':
							var iframeContents = $( iframe ).contents()[0];

							iframeContents.documentElement.style.setProperty( '--hu-headingColor', event.data.value );
							customOptions = { design: { headingColor: event.data.value } }
							break;

						case 'color_button_text':
							var iframeContents = $( iframe ).contents()[0];

							iframeContents.documentElement.style.setProperty( '--hu-btnTextColor', event.data.value );
							customOptions = { design: { btnTextColor: event.data.value } }
							break;
					}

					// set widget options
					hu.setOptions( customOptions );

					// set widget texts
					hu.setTexts( customTexts );
				}

				// remove spinner
				$( iframe ).closest( '.has-loader' ).find( '.cn-spinner' ).remove();
				$( iframe ).closest( '.has-loader' ).removeClass( 'cn-loading' );

				// unlock options
				$( form ).removeClass( 'cn-form-disabled' );
			}, 500	);
		}, false );

		// is it iframe?
		if ( document !== parent.document && typeof cnFrontWelcome !== 'undefined' && cnFrontWelcome.previewMode ) {
			var iframe = $( parent.document ).find( '#cn_iframe_id' );

			// inject links into initial document
			$( document.body ).find( 'a[href], area[href]' ).each( function() {
				cnAddPreviewModeToLink( this, iframe );
			} );

			// inject links into initial document
			$( document.body ).find( 'form' ).each( function() {
				cnAddPreviewModeToForm( this, iframe );
			} );

			// inject links for new elements added to the page
			if ( typeof MutationObserver !== 'undefined' ) {
				var observer = new MutationObserver( function( mutations ) {
					_.each( mutations, function( mutation ) {
						$( mutation.target ).find( 'a[href], area[href]' ).each( function() {
							cnAddPreviewModeToLink( this, iframe );
						} );

						$( mutation.target ).find( 'form' ).each( function() {
							cnAddPreviewModeToForm( this, iframe );
						} );
					} );
				} );

				observer.observe( document.documentElement, {
					childList: true,
					subtree: true
				} );
			} else {
				// If mutation observers aren't available, fallback to just-in-time injection.
				$( document.documentElement ).on( 'click focus mouseover', 'a[href], area[href]', function() {
					cnAddPreviewModeToLink( this, iframe );
				} );
			}

			// remove spinner
			$( iframe ).closest( '.has-loader' ).find( '.cn-spinner' ).remove();
			$( iframe ).closest( '.has-loader' ).removeClass( 'cn-loading' );
		}
	} );

	/**
	 * Inject preview mode parameter into specific links on the frontend.
	 */
	function cnAddPreviewModeToLink( element, iframe ) {
		var params, $element = $( element );

		// skip elements with no href attribute
		if ( ! element.hasAttribute( 'href' ) )
			return;

		// skip links in admin bar
		if ( $element.closest( '#wpadminbar' ).length )
			return;

		// ignore links with href="#", href="#id", or non-HTTP protocols (e.g. javascript: and mailto:)
		if ( '#' === $element.attr( 'href' ).substr( 0, 1 ) || ! /^https?:$/.test( element.protocol ) )
			return;

		// make sure links in preview use HTTPS if parent frame uses HTTPS.
		// if ( api.settings.channel && 'https' === api.preview.scheme.get() && 'http:' === element.protocol && -1 !== api.settings.url.allowedHosts.indexOf( element.host ) )
			// element.protocol = 'https:';

		// ignore links with special class
		if ( $element.hasClass( 'wp-playlist-caption' ) )
			return;

		// check special links
		if ( ! cnIsLinkPreviewable( element ) )
			return;

		$( element ).on( 'click', function() {
			$( iframe ).closest( '.has-loader' ).addClass( 'cn-loading' );
		} );

		// parse query string
		params = cnParseQueryString( element.search.substring( 1 ) );

		// set preview mode
		params.cn_preview_mode = 1;

		element.search = $.param( params );
	}

	/**
	 * Inject preview mode parameter into specific forms on the frontend.
	 */
	function cnAddPreviewModeToForm( element, iframe ) {
		var input = document.createElement( 'input' );

		input.setAttribute( 'type', 'hidden' );
		input.setAttribute( 'name', 'cn_preview_mode' );
		input.setAttribute( 'value', 1 );

		element.appendChild( input );
	}

	/**
	 * Parse query string.
	 */
	function cnParseQueryString( string ) {
		var params = {};

		_.each( string.split( '&' ), function( pair ) {
			var parts, key, value;

			parts = pair.split( '=', 2 );

			if ( ! parts[0] )
				return;

			key = decodeURIComponent( parts[0].replace( /\+/g, ' ' ) );
			key = key.replace( / /g, '_' );

			if ( _.isUndefined( parts[1] ) )
				value = null;
			else
				value = decodeURIComponent( parts[1].replace( /\+/g, ' ' ) );

			params[ key ] = value;
		} );

		return params;
	}

	/**
	 * Whether the supplied link is previewable.
	 */
	function cnIsLinkPreviewable( element ) {
		var matchesAllowedUrl, parsedAllowedUrl, elementHost;

		if ( 'javascript:' === element.protocol )
			return true;

		// only web urls can be previewed
		if ( element.protocol !== 'https:' && element.protocol !== 'http:' )
			return false;

		elementHost = element.host.replace( /:(80|443)$/, '' );
		parsedAllowedUrl = document.createElement( 'a' );
		matchesAllowedUrl = ! _.isUndefined( _.find( cnFrontWelcome.allowedURLs, function( allowedUrl ) {
			parsedAllowedUrl.href = allowedUrl;

			return parsedAllowedUrl.protocol === element.protocol && parsedAllowedUrl.host.replace( /:(80|443)$/, '' ) === elementHost && 0 === element.pathname.indexOf( parsedAllowedUrl.pathname.replace( /\/$/, '' ) );
		} ) );

		if ( ! matchesAllowedUrl )
			return false;

		// skip wp login and signup pages
		if ( /\/wp-(login|signup)\.php$/.test( element.pathname ) )
			return false;

		// allow links to admin ajax as faux frontend URLs
		if ( /\/wp-admin\/admin-ajax\.php$/.test( element.pathname ) )
			return false;

		// disallow links to admin, includes, and content
		if ( /\/wp-(admin|includes|content)(\/|$)/.test( element.pathname ) )
			return false;

		return true;
	};

} )( jQuery );