jQuery( 'document' ).ready( function ( $ ) {
	'use strict';

	const s = window[ '%%wp_localize_script_var%%' ];

	$( '.' + s.app.slugPrefix + 'notice.is-persistent.is-dismissable .notice-dismiss' ).on( 'click', function () {
		const $this   = $( this );
		const $notice = $this.closest( '.' + s.app.slugPrefix + 'notice' );

		const postData                                       = {};
		postData.action                                      = s.app.varPrefix + 'admin_notice_dismiss';
		postData._wpnonce                                    = s.ajax.nonces.adminNoticeDismiss;
		postData[ s.app.varPrefix + 'admin_notice_dismiss' ] = $notice.data( 'idx' );

		$.post( s.ajax.url, postData );
	} );
} );
