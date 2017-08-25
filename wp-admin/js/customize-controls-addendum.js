/* global CodeMirror */
/* eslint no-magic-numbers: ["error", { "ignore": [0, 1] }] */
(function( api, $ ) {
	'use strict';

	api.section( 'custom_css', function( section ) {
		api.control( 'custom_css', function( control ) {
			var onceExpanded, onExpandedChange;

			// Abort if CodeMirror disabled via customizer_custom_css_codemirror_opts filter.
			if ( ! api.settings.codeMirror ) {
				return;
			}

			// Workaround for disabling server-sent syntax checking notifications.
			// @todo Listen for errors in CodeMirror and opt-to add invalidity notifications for them? The presence of such notification error allows saving to be blocked.
			control.setting.notifications.add = (function( originalAdd ) { // eslint-disable-line max-nested-callbacks
				return function( id, notification ) { // eslint-disable-line max-nested-callbacks
					if ( 'imbalanced_curly_brackets' === id && notification.fromServer ) {
						return null;
					} else {
						return originalAdd( id, notification );
					}
				};
			})( control.setting.notifications );

			onceExpanded = function() {
				var $textarea = control.container.find( 'textarea' );

				control.editor = CodeMirror.fromTextArea( $textarea[0], api.settings.codeMirror );

				// Refresh when receiving focus.
				control.editor.on( 'focus', function( editor ) {
					editor.refresh();
				});

				/*
				 * When the CodeMirror instance changes, mirror to the textarea,
				 * where we have our "true" change event handler bound.
				 */
				control.editor.on( 'change', function( editor ) {
					$textarea.val( editor.getValue() ).trigger( 'change' );
				});

				control.editor.on( 'blur', function onBlur() {
					$textarea.data( 'next-tab-blurs', false );
				} );

				control.editor.on( 'keydown', function onKeydown( editor, event ) {
					var tabKeyCode = 9, escKeyCode = 27, controls, controlIndex;

					if ( escKeyCode === event.keyCode ) {
						if ( ! $textarea.data( 'next-tab-blurs' ) ) {
							$textarea.data( 'next-tab-blurs', true );
							event.stopPropagation(); // Prevent collapsing the section.
						}
						return;
					}

					// Short-circuit if tab key is not being pressed or the tab key press should move focus.
					if ( tabKeyCode !== event.keyCode || ! $textarea.data( 'next-tab-blurs' ) ) {
						return;
					}

					// Focus on previous or next focusable item.
					controls = section.controls();
					controlIndex = controls.indexOf( control );
					if ( event.shiftKey ) {
						if ( 0 === controlIndex ) {
							section.container.find( '.customize-help-toggle' ).focus();
						} else {
							controls[ controlIndex - 1 ].container.find( ':focusable:first' ).focus();
						}
					} else if ( controls.length === controlIndex + 1 ) {
						$( '#customize-footer-actions .collapse-sidebar' ).focus();
					} else {
						controls[ controlIndex + 1 ].container.find( ':focusable:first' ).focus();
					}

					// Prevent tab character from being added.
					event.preventDefault();

					// Reset tab state.
					$textarea.data( 'next-tab-blurs', false );
				} );

				// @todo: bind something to setting change, so that we can catch other plugins modifying the css and update CodeMirror?
			};

			onExpandedChange = function( isExpanded ) {
				if ( isExpanded ) {
					onceExpanded();
					section.expanded.unbind( onExpandedChange );
				}
			};
			control.deferred.embedded.done( function() {
				if ( section.expanded() ) {
					onceExpanded();
				} else {
					section.expanded.bind( onExpandedChange );
				}
			});
		});
	});
})( wp.customize, jQuery );
