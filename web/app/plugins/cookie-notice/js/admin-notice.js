( function( $ ) {

	// ready event
	$( function() {
		// save dismiss state // .is-dismissible
		$( '.cn-notice' ).on( 'click', '.notice-dismiss, .cn-notice-dismiss', function( e ) {
			var notice_action = 'dismiss';
			var param = '';

			if ( $( e.currentTarget ).hasClass( 'cn-approve' ) )
				notice_action = 'approve';
			else if ( $( e.currentTarget ).hasClass( 'cn-delay' ) )
				notice_action = 'delay';
			else if ( $( e.delegateTarget ).hasClass( 'cn-threshold' ) ) {
				notice_action = 'threshold';

				var delay = $( e.delegateTarget ).find( '.cn-notice-text' ).data( 'delay' );

				param = parseInt( delay );
			}

			$.ajax( {
				url: cnArgsNotice.ajaxURL,
				type: 'POST',
				dataType: 'json',
				data: {
					action: 'cn_dismiss_notice',
					notice_action: notice_action,
					nonce: cnArgsNotice.nonce,
					param: param,
					cn_network: cnArgsNotice.network ? 1 : 0
				}
			} );

			$( e.delegateTarget ).slideUp( 'fast' );
		} );
	} );

} )( jQuery );