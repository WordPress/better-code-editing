/* global _wpCodeMirrorOptions, CodeMirror */

jQuery( function( $ ) {
	'use strict';

	var $textarea = $( '#newcontent' ), editor;
	editor = CodeMirror.fromTextArea( $textarea[0], _wpCodeMirrorOptions );

	// Make sure the editor gets updated if the content was updated on the server (sanitization) but not updated in the editor since it was focused.
	editor.on( 'blur', function() {
		$textarea.data( 'next-tab-blurs', false );
	});

	editor.on( 'keydown', function onKeydown( _editor, event ) {
		var tabKeyCode = 9, escKeyCode = 27;

		// Take note of the ESC keypress so that the next TAB can focus outside the editor.
		if ( escKeyCode === event.keyCode ) {
			$textarea.data( 'next-tab-blurs', true );
			return;
		}

		// Short-circuit if tab key is not being pressed or the tab key press should move focus.
		if ( tabKeyCode !== event.keyCode || ! $textarea.data( 'next-tab-blurs' ) ) {
			return;
		}

		// Focus on previous or next focusable item.
		if ( event.shiftKey ) {
			$( '#templateside' ).find( ':tabbable' ).last().focus();
		} else {
			$( '#template' ).find( ':tabbable:not(.CodeMirror-code)' ).first().focus();
		}

		// Reset tab state.
		$textarea.data( 'next-tab-blurs', false );

		// Prevent tab character from being added.
		event.preventDefault();
	});

	wp.codemirror = editor;
});
