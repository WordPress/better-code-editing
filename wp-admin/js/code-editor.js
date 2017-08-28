/* global CodeMirror */
if ( 'undefined' === typeof window.wp ) {
	window.wp = {};
}
if ( 'undefined' === typeof window.wp.codeEditor ) {
	window.wp.codeEditor = {};
}

( function( $, wp ) {
	'use strict';

	/**
	 * Default settings for code editor.
	 *
	 * @since 4.9.0
	 * @type {object}
	 */
	wp.codeEditor.defaultSettings = {
		codemirror: {},
		csslint: {},
		htmlhint: {},
		jshint: {},
		handleTabNext: function() {},
		handleTabPrev: function() {}
	};

	/**
	 * All instances of code editors.
	 *
	 * @since 4.9.0
	 * @type {CodeMirror[]}
	 */
	wp.codeEditor.instances = [];

	/**
	 * Initialize Code Editor (CodeMirror) for an existing textarea.
	 *
	 * @since 4.9.0
	 *
	 * @param {string|jQuery|Element} textarea The HTML id, jQuery object, or DOM Element for the textarea that is used for the editor.
	 * @param {object} [settings] Settings to override defaults.
	 * @returns {CodeMirror} CodeMirror instance.
	 */
	wp.codeEditor.initialize = function initialize( textarea, settings ) { // eslint-disable-line complexity
		var $textarea, editor, instanceSettings;
		if ( 'string' === typeof textarea ) {
			$textarea = $( '#' + textarea );
		} else {
			$textarea = $( textarea );
		}

		instanceSettings = $.extend( {}, wp.codeEditor.defaultSettings, settings );
		instanceSettings.codemirror = $.extend( {}, instanceSettings.codemirror );

		if ( true === instanceSettings.codemirror.lint ) {

			instanceSettings.codemirror.lint = {};

			// Configure JSHint.
			if ( 'text/javascript' === instanceSettings.codemirror.mode && instanceSettings.jshint ) {
				$.extend( instanceSettings.codemirror.lint, instanceSettings.jshint );
			}

			// Configure CSSLint.
			if ( 'text/css' === instanceSettings.codemirror.mode && instanceSettings.csslint ) {
				$.extend( instanceSettings.codemirror.lint, instanceSettings.csslint );
			}

			// Configure HTMLHint.
			if ( 'htmlmixed' === instanceSettings.codemirror.mode && instanceSettings.htmlhint ) {
				instanceSettings.codemirror.lint.rules = $.extend( {}, instanceSettings.htmlhint );

				if ( instanceSettings.jshint ) {
					instanceSettings.codemirror.lint.rules.jshint = instanceSettings.jshint;
				}
				if ( instanceSettings.csslint ) {
					instanceSettings.codemirror.lint.rules.csslint = instanceSettings.csslint;
				}
			}
		}

		editor = CodeMirror.fromTextArea( $textarea[0], instanceSettings.codemirror );

		// Keep track of the instances that have been created.
		wp.codeEditor.instances.push( editor );

		if ( editor.showHint ) {
			editor.on( 'keyup', function( _editor, event ) { // eslint-disable-line complexity
				var shouldAutocomplete, isAlphaKey = /^[a-zA-Z]$/.test( event.key ), lineBeforeCursor, innerMode, token;
				if ( editor.state.completionActive && isAlphaKey ) {
					return;
				}

				// Prevent autocompletion in string literals or comments.
				token = editor.getTokenAt( editor.getCursor() );
				if ( 'string' === token.type || 'comment' === token.type ) {
					return;
				}

				innerMode = CodeMirror.innerMode( editor.getMode(), token.state ).mode.name;
				lineBeforeCursor = editor.doc.getLine( editor.doc.getCursor().line ).substr( 0, editor.doc.getCursor().ch );
				if ( 'html' === innerMode || 'xml' === innerMode ) {
					shouldAutocomplete =
						'<' === event.key ||
						'/' === event.key && 'tag' === token.type ||
						isAlphaKey && 'tag' === token.type ||
						isAlphaKey && 'attribute' === token.type ||
						'=' === token.string && token.state.htmlState && token.state.htmlState.tagName;
				} else if ( 'css' === innerMode ) {
					shouldAutocomplete =
						isAlphaKey ||
						':' === event.key ||
						' ' === event.key && /:\s+$/.test( lineBeforeCursor );
				} else if ( 'javascript' === innerMode ) {
					shouldAutocomplete = isAlphaKey || '.' === event.key;
				} else if ( 'clike' === innerMode && 'application/x-httpd-php' === editor.options.mode ) {
					shouldAutocomplete = 'keyword' === token.type || 'variable' === token.type;
				}
				if ( shouldAutocomplete ) {
					CodeMirror.commands.autocomplete( editor, null, { completeSingle: false } );
				}
			});
		}

		// Make sure the editor gets updated if the content was updated on the server (sanitization) but not updated in the editor since it was focused.
		editor.on( 'blur', function() {
			$textarea.data( 'next-tab-blurs', false );
		});

		editor.on( 'focus', function() {
			if ( editor.display.wrapper.scrollIntoViewIfNeeded ) {
				editor.display.wrapper.scrollIntoViewIfNeeded();
			} else {
				editor.display.wrapper.scrollIntoView();
			}
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
				settings.handleTabPrev( editor, event );
			} else {
				settings.handleTabNext( editor, event );
			}

			// Reset tab state.
			$textarea.data( 'next-tab-blurs', false );

			// Prevent tab character from being added.
			event.preventDefault();
		});

		return editor;
	};

})( window.jQuery, window.wp );
