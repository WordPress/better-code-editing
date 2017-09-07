/* eslint no-magic-numbers: ["error", { "ignore": [-1, 0, 1] }] */

if ( ! window.wp ) {
	window.wp = {};
}

wp.themePluginEditor = (function( $ ) {
	'use strict';

	var component = {
		l10n: {
			lintError: {
				singular: '',
				plural: ''
			}
		},
		instance: null
	};

	/**
	 * Initialize component.
	 *
	 * @param {object} settings Settings.
	 * @returns {void}
	 */
	component.init = function( settings ) {
		var codeEditorSettings, noticeContainer, errorNotice, updateNotice, currentErrorAnnotations = [], editor, previousErrorCount = 0;

		codeEditorSettings = $.extend( {}, settings );

		codeEditorSettings.handleTabPrev = function() {
			$( '#templateside' ).find( ':tabbable' ).last().focus();
		};
		codeEditorSettings.handleTabNext = function() {
			$( '#template' ).find( ':tabbable:not(.CodeMirror-code)' ).first().focus();
		};

		updateNotice = function() {
			var message;

			// Short-circuit if there is no update for the message.
			if ( currentErrorAnnotations.length === previousErrorCount ) {
				return;
			}

			previousErrorCount = currentErrorAnnotations.length;

			$( '#submit' ).prop( 'disabled', 0 !== currentErrorAnnotations.length );
			if ( 0 !== currentErrorAnnotations.length ) {
				errorNotice.empty();
				if ( 1 === currentErrorAnnotations.length ) {
					message = component.l10n.singular.replace( '%d', '1' );
				} else {
					message = component.l10n.plural.replace( '%d', String( currentErrorAnnotations.length ) );
				}
				errorNotice.append( $( '<p></p>', {
					text: message
				} ) );
				noticeContainer.slideDown( 'fast' );
				wp.a11y.speak( message );
			} else {
				noticeContainer.slideUp( 'fast' );
			}
		};

		if ( codeEditorSettings.codemirror.lint ) {
			if ( true === codeEditorSettings.codemirror.lint ) {
				codeEditorSettings.codemirror.lint = {};
			}
			noticeContainer = $( '<div id="file-editor-linting-error"></div>' );
			errorNotice = $( '<div class="inline notice notice-error"></div>' );
			noticeContainer.append( errorNotice );
			noticeContainer.hide();
			$( 'p.submit' ).before( noticeContainer );

			// @todo All of this should be abstracted into an onHasLintingErrors handler, something like that.
			codeEditorSettings.codemirror.lint = _.extend( {}, codeEditorSettings.codemirror.lint, {
				onUpdateLinting: function( annotations, annotationsSorted, cm ) {
					currentErrorAnnotations = _.filter( annotations, function( annotation ) {
						return 'error' === annotation.severity;
					} );

					/*
					 * Update notifications when the editor is not focused to prevent error message
					 * from overwhelming the user during input, unless there are no annotations
					 * or there are previous notifications already being displayed, and in that
					 * case update immediately so they can know that they fixed the errors.
					 */
					if ( ! cm.state.focused || 0 === currentErrorAnnotations.length || previousErrorCount > 0 ) {
						updateNotice();
					}
				}
			} );
		}
		editor = wp.codeEditor.initialize( $( '#newcontent' ), codeEditorSettings );

		// @todo All of this logic needs to be added to wp.codeEditor for re-use, in a onSignalLintingErrors handler.
		if ( codeEditorSettings.codemirror.lint ) {
			editor.on( 'blur', function() {
				updateNotice();
			});
			$( editor.display.wrapper ).on( 'mouseenter mouseleave', function( event ) {
				var onHoverHints, editorHovered;
				editorHovered = 'mouseenter' === event.type;

				onHoverHints = function() {
					editorHovered = true;
				};
				$( document.body ).on( 'mouseenter', '.CodeMirror-hints', onHoverHints );

				_.delay( function() { // Delay to wait for mouseenter on .CodeMirror-hints to trigger.
					$( document.body ).off( 'mouseenter', '.CodeMirror-hints', onHoverHints );
					if ( ! editorHovered ) {
						updateNotice();
					}
				} );
			});
		}

		component.instance = editor;
	};

	return component;
})( jQuery );
