/* global CSSLint */
( function() {
	'use strict';

	if ( window.CSSLint ) {
		CSSLint.addRule( { id: 'box-sizing', init: function() {} } );
		CSSLint.addRule( { id: 'fallback-colors', init: function() {} } );
	}
} )();
