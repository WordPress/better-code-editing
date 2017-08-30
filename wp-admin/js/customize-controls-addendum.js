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

			// Workaround for disabling server-sent syntax checking notifications. This can be removed from core in the merge.
			control.setting.notifications.add = (function( originalAdd ) { // eslint-disable-line max-nested-callbacks
				return function( id, notification ) { // eslint-disable-line max-nested-callbacks
					if ( 'imbalanced_curly_brackets' === id && notification.fromServer ) {
						return null;
					} else {
						return originalAdd.call( this, id, notification );
					}
				};
			})( control.setting.notifications.add );

			// Make sure editor gets focused when control is focused.
			control.focus = (function( originalFocus ) { // eslint-disable-line max-nested-callbacks
				return function( params ) { // eslint-disable-line max-nested-callbacks
					var extendedParams = _.extend( {}, params ), originalCompleteCallback;
					originalCompleteCallback = extendedParams.completeCallback;
					extendedParams.completeCallback = function() {
						if ( originalCompleteCallback ) {
							originalCompleteCallback();
						}
						if ( control.editor ) {
							control.editor.focus();
						}
					};
					originalFocus.call( this, extendedParams );
				};
			})( control.focus );

			onceExpanded = function() {
				var $textarea = control.container.find( 'textarea' ), settings, currentAnnotations = [];

				settings = _.extend( {}, api.settings.codeEditor, {
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
				} );

				/**
				 * Update notifications on the setting based on the current CSSLint annotations.
				 *
				 * @returns {void}
				 */
				function updateNotifications() {
					if ( 1 === currentAnnotations.length ) {
						control.setting.notifications.remove( 'csslint_errors' );
						control.setting.notifications.add( 'csslint_error', new api.Notification( 'csslint_error', {
							message: 'There is 1 error in the CSS that must be fixed.', // @todo l10n
							type: 'error'
						} ) );
					} else if ( currentAnnotations.length > 1 ) {
						control.setting.notifications.remove( 'csslint_error' );
						control.setting.notifications.add( 'csslint_errors', new api.Notification( 'csslint_errors', {
							message: 'There is ' + String( currentAnnotations.length ) + ' error in the CSS that must be fixed.', // @todo l10n
							type: 'error'
						} ) );
					} else {
						control.setting.notifications.remove( 'csslint_error' );
						control.setting.notifications.remove( 'csslint_errors' );
					}
				}

				if ( settings.codemirror.lint ) {
					if ( true === settings.codemirror.lint ) {
						settings.codemirror.lint = {};
					}
					settings.codemirror.lint = _.extend( {}, settings.codemirror.lint, {
						onUpdateLinting: function( annotations, unsortedAnnotations, editor ) {
							currentAnnotations = annotations;

							/*
							 * Update notifications when the editor is not focused to prevent error message
							 * from overwhelming the user during input, unless there are no annotations
							 * and in that case update immediately so they can know that they fixed the
							 * errors.
							 */
							if ( ! editor.state.focused || 0 === currentAnnotations.length ) {
								updateNotifications();
							}
						}
					} );
				}

				control.editor = wp.codeEditor.initialize( $textarea, settings );

				// Refresh when receiving focus.
				control.editor.on( 'focus', function( editor ) {
					editor.refresh();
				});

				// Update notifications when blurring the field to prevent user from being inundated with errors during input.
				control.editor.on( 'blur', function() {
					updateNotifications();
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
