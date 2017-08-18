(function( api ) {
	'use strict';

	api.section( 'custom_css', function( section ) {
		api.control( 'custom_css', function( control ) {
			var onceExpanded, onExpandedChange;

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

				wp.codemirror = window.CodeMirror.fromTextArea( $textarea[0], api.settings.codeMirror );

				// Refresh when receiving focus.
				wp.codemirror.on( 'focus', function( editor ) {
					editor.refresh();
				} );

				/*
				 * When the CodeMirror instance changes, mirror to the textarea,
				 * where we have our "true" change event handler bound.
				 */
				wp.codemirror.on( 'change', function( editor ) {
					$textarea.val( editor.getValue() ).trigger( 'change' );
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
})( wp.customize );
