/* global cmLintPHP */
(function(mod) {
	if (typeof exports == "object" && typeof module == "object") // CommonJS
		mod(require("../../lib/codemirror"));
	else if (typeof define == "function" && define.amd) // AMD
		define(["../../lib/codemirror"], mod);
	else // Plain browser env
		mod(CodeMirror);
})(function(CodeMirror) {
	"use strict";

	console.log( 'HI THERE' );

	CodeMirror.registerHelper( 'lint', 'php', function( code ) {
		console.log( 'Running PHP linter!' );
		var found = [];

		jQuery.ajax( {
			async: false,
			url: cmLintPHP.endpoint,
			method: 'POST',
			beforeSend: function ( xhr ) {
				xhr.setRequestHeader( 'X-WP-Nonce', cmLintPHP.nonce );
			},
			data: {
				code : code
			}
		} ).done( function ( response ) {
			console.log( response );
			found = response;
		} );

		return found;
	});
});

