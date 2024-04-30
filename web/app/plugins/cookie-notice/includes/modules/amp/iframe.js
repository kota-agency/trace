function cnDisableRevoke() {
	// force revoke button to be disabled
	hu.options.config.revokeConsent = false;
}

function cnConsentResponse( event ) {
	// allow this event to run only once
	if ( event.type === 'set-consent.hu' )
		document.removeEventListener( 'hide.hu', cnConsentResponse );

	document.addEventListener( 'save-consent-response.hu', cnConsentSet( event.detail.categories ) );
}

function cnConsentSet( categories ) {
	// it has to use return function to wait for save-consent-response event
	return function cnRequestFinished( event ) {
		var action = 'accept';

		// only basic operations?
		if ( categories[1] && ! categories[2] && ! categories[3] && ! categories[4] )
			action = 'reject';

		// inform amp to save consent
		window.parent.postMessage(
			{
				type: 'consent-response',
				action: action,
				purposeConsents: {
					'basic_operations': categories[1],
					'content_personalization': categories[2],
					'site_optimization': categories[3],
					'ad_personalization': categories[4]
				}
			},
			'*'
		);
	}
}

// set consent
document.addEventListener( 'hide.hu', cnConsentResponse );
document.addEventListener( 'set-consent.hu', cnConsentResponse );

// disable revoke button
document.addEventListener( 'load.hu', cnDisableRevoke );
