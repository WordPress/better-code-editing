/* eslint no-magic-numbers: ["error", { "ignore": [0, 1] }] */
(function( api, $ ) {
	'use strict';

	api.section( 'custom_css', function( section ) {
		if ( ! api.settings.customCss ) {
			return;
		}

		// Close the section description when clicking the close button.
		section.container.find( '.section-description-buttons .section-description-close' ).on( 'click', function() {
			section.container.find( '.section-meta .customize-section-description:first' )
				.removeClass( 'open' )
				.slideUp()
				.attr( 'aria-expanded', 'false' );
		});

		api.control( 'custom_css', function( control ) {
			var onceExpanded, onExpandedChange;

			// Abort if CodeMirror disabled via customizer_custom_css_codemirror_opts filter.
			if ( ! api.settings.customCss.codeEditor ) {
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
							control.editor.codemirror.focus();
						}
					};
					originalFocus.call( this, extendedParams );
				};
			})( control.focus );

			onceExpanded = function() {
				var $textarea = control.container.find( 'textarea' ), settings, suspendEditorUpdate = false;

				if ( ! control.setting.get() ) {
					section.container.find( '.section-meta .customize-section-description:first' )
						.addClass( 'open' )
						.show()
						.attr( 'aria-expanded', 'true' );
				}

				settings = _.extend( {}, api.settings.customCss.codeEditor, {

					/**
					 * Handle tabbing to the field after the editor.
					 *
					 * @returns {void}
					 */
					onTabNext: function onTabNext() {
						var controls, controlIndex;
						controls = section.controls();
						controlIndex = controls.indexOf( control );
						if ( controls.length === controlIndex + 1 ) {
							$( '#customize-footer-actions .collapse-sidebar' ).focus();
						} else {
							controls[ controlIndex + 1 ].container.find( ':focusable:first' ).focus();
						}
					},

					/**
					 * Handle tabbing to the field before the editor.
					 *
					 * @returns {void}
					 */
					onTabPrevious: function onTabPrevious() {
						var controls, controlIndex;
						controls = section.controls();
						controlIndex = controls.indexOf( control );
						if ( 0 === controlIndex ) {
							section.contentContainer.find( '.customize-section-title .customize-help-toggle, .customize-section-title .customize-section-description.open .section-description-close' ).last().focus();
						} else {
							controls[ controlIndex - 1 ].contentContainer.find( ':focusable:first' ).focus();
						}
					},

					/**
					 * Update error notice.
					 *
					 * @param {Array} errorAnnotations - Error annotations.
					 * @returns {void}
					 */
					onUpdateErrorNotice: function onUpdateErrorNotice( errorAnnotations ) {
						var message;
						control.setting.notifications.remove( 'csslint_error' );

						if ( 0 !== errorAnnotations.length ) {
							if ( 1 === errorAnnotations.length ) {
								message = api.l10n.customCssErrorNotice.singular.replace( '%d', '1' );
							} else {
								message = api.l10n.customCssErrorNotice.plural.replace( '%d', String( errorAnnotations.length ) );
							}
							control.setting.notifications.add( 'csslint_error', new api.Notification( 'csslint_error', {
								message: message,
								type: 'error'
							} ) );
						}
					}
				});

				control.editor = wp.codeEditor.initialize( $textarea, settings );

				// Refresh when receiving focus.
				control.editor.codemirror.on( 'focus', function( codemirror ) {
					codemirror.refresh();
				});

				/*
				 * When the CodeMirror instance changes, mirror to the textarea,
				 * where we have our "true" change event handler bound.
				 */
				control.editor.codemirror.on( 'change', function( codemirror ) {
					suspendEditorUpdate = true;
					$textarea.val( codemirror.getValue() ).trigger( 'change' );
					suspendEditorUpdate = false;
				});

				// Update CodeMirror when the setting is changed by another plugin.
				control.setting.bind( function( value ) {
					if ( ! suspendEditorUpdate ) {
						control.editor.codemirror.setValue( value );
					}
				});

				// Prevent collapsing section when hitting Esc to tab out of editor.
				control.editor.codemirror.on( 'keydown', function onKeydown( codemirror, event ) {
					var escKeyCode = 27;
					if ( escKeyCode === event.keyCode ) {
						event.stopPropagation();
					}
				});
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
