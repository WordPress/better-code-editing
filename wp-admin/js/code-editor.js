/* global CodeMirror, CSSLint */
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
	 * Override which CSSLint rules are added.
	 *
	 * @param {Array} rules - Subset or rules.
	 * @returns {void}
	 */
	function updateCSSLintRules( rules ) {
		var allRules = CSSLint.getRules(), i;
		CSSLint.clearRules();
		for ( i = 0; i < allRules.length; i++ ) {
			if ( rules[ allRules[ i ].id ] ) {
				CSSLint.addRule( allRules[ i ] );
			}
		}
	}

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

			// Configure JSHint.
			if ( 'text/javascript' === instanceSettings.codemirror.mode && true === instanceSettings.codemirror.lint && instanceSettings.jshint && instanceSettings.jshint.rules ) {
				instanceSettings.codemirror.lint = instanceSettings.jshint.rules;
			}

			// Configure HTMLHint.
			if ( ( 'text/html' === instanceSettings.codemirror.mode || 'htmlmixed' === instanceSettings.codemirror.mode ) && true === instanceSettings.codemirror.lint && instanceSettings.htmlhint && instanceSettings.htmlhint.rules ) {
				instanceSettings.codemirror.lint = $.extend( {}, instanceSettings.htmlhint );

				if ( instanceSettings.jshint && instanceSettings.jshint.rules ) {
					instanceSettings.codemirror.lint.rules.jshint = instanceSettings.jshint.rules;
				}
				if ( instanceSettings.csslint && instanceSettings.csslint.rules ) {
					instanceSettings.codemirror.lint.rules.csslint = instanceSettings.csslint.rules;
				}
			}

			// Configure CSSLint.
			if ( 'undefined' !== typeof CSSLint && instanceSettings.csslint && instanceSettings.csslint.rules ) {
				updateCSSLintRules( instanceSettings.csslint.rules );
			}
		}

		editor = CodeMirror.fromTextArea( $textarea[0], instanceSettings.codemirror );

		// Keep track of the instances that have been created.
		wp.codeEditor.instances.push( editor );

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
