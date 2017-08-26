/* eslint no-magic-numbers: ["error", { "ignore": [0, 1] }] */
(function( api, $ ) {
	'use strict';

	api.section( 'custom_css', function( section ) {
		api.control( 'custom_css', function( control ) {
			var onceExpanded, onExpandedChange;

			// Abort if CodeMirror disabled via customizer_custom_css_codemirror_opts filter.
			if ( ! api.settings.codeEditor ) {
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

				control.editor = wp.codeEditor.initialize( $textarea, _.extend( {}, api.settings.codeEditor, {
					handleTabNext: function() {
						var controls, controlIndex;
						controls = section.controls();
						controlIndex = controls.indexOf( control );
						if ( controls.length === controlIndex + 1 ) {
							$( '#customize-footer-actions .collapse-sidebar' ).focus();
						} else {
							controls[ controlIndex + 1 ].container.find( ':focusable:first' ).focus();
						}
					},
					handleTabPrev: function() {
						var controls, controlIndex;
						controls = section.controls();
						controlIndex = controls.indexOf( control );
						if ( 0 === controlIndex ) {
							section.container.find( '.customize-help-toggle' ).focus();
						} else {
							controls[ controlIndex - 1 ].container.find( ':focusable:first' ).focus();
						}
					}
				} ) );

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

				control.editor.on( 'keydown', function onKeydown( editor, event ) {
					var escKeyCode = 27;
					if ( escKeyCode === event.keyCode ) {
						event.stopPropagation(); // Prevent collapsing the section.
					}
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
