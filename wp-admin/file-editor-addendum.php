<?php
/**
 * Extensions to the theme and plugin editor administration panels.
 *
 * @package WordPress
 * @subpackage Administration
 */

add_filter( 'editable_extensions', '_better_code_editing_filter_editable_extensions' );
add_filter( 'wp_theme_editor_filetypes', '_better_code_editing_filter_editable_extensions' );
add_action( 'admin_enqueue_scripts', '_better_code_editing_admin_enqueue_scripts_for_file_editor' );

/**
 * Add extensions that are editable.
 *
 * @param array $extensions Extensions.
 * @return array Merged extensions.
 */
function _better_code_editing_filter_editable_extensions( $extensions ) {
	return array_merge( $extensions, array(
		'conf',
		'css',
		'diff',
		'patch',
		'html',
		'htm',
		'http',
		'js',
		'json',
		'jsx',
		'less',
		'md',
		'php',
		'phtml',
		'php3',
		'php4',
		'php5',
		'php7',
		'phps',
		'scss',
		'sass',
		'sh',
		'bash',
		'sql',
		'svg',
		'xml',
		'yml',
		'yaml',
		'txt',
	) );
}

/**
 * Enqueue scripts for theme and plugin editors.
 *
 * @param string $hook Hook.
 */
function _better_code_editing_admin_enqueue_scripts_for_file_editor( $hook ) {

	if ( 'theme-editor.php' !== $hook && 'plugin-editor.php' !== $hook ) {
		return;
	}

	$context = array();

	if ( 'theme-editor.php' === $hook ) {
		$theme = wp_get_theme( isset( $_REQUEST['theme'] ) ? wp_unslash( $_REQUEST['theme'] ) : get_stylesheet() );
		if ( $theme->errors() ) {
			wp_die( $theme->errors()->get_error_message() );
		}

		if ( isset( $_REQUEST['file'] ) ) {
			$context['file'] = sanitize_text_field( wp_unslash( $_REQUEST['file'] ) );
		} else {
			$context['file'] = 'style.css';
		}
	} elseif ( 'plugin-editor.php' === $hook ) {
		$context['plugin'] = isset( $_REQUEST['plugin'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['plugin'] ) ) : '';
		$context['file'] = isset( $_REQUEST['file'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['file'] ) ) : '';

		if ( empty( $context['plugin'] ) ) {
			$file_paths = array_keys( get_plugins() );
			$context['plugin'] = $context['file'] ? $context['file'] : array_shift( $file_paths );
		} elseif ( 0 !== validate_file( $context['plugin'] ) ) {
			wp_die( __( 'Sorry, that file cannot be edited.', 'better-code-editing' ) );
		}

		$plugin_files = get_plugin_files( $context['plugin'] );
		if ( empty( $context['file'] ) ) {
			$context['file'] = $plugin_files[0];
		}

		$context['file'] = validate_file_to_edit( $context['file'], $plugin_files );
	}

	$settings = wp_code_editor_settings( $context );
	if ( empty( $settings ) ) {
		return;
	}

	wp_enqueue_script( 'jquery-ui-core' ); // For :tabbable pseudo-selector.
	wp_enqueue_script( 'underscore' );
	wp_enqueue_code_editor( $settings );

	/* translators: placeholder is error count */
	$l10n = _n_noop( 'There is %d error which must be fixed before you can save.', 'There are %d errors which must be fixed before you can save.', 'better-code-editing' );

	ob_start();
	?>
	<script>
		jQuery( function( $ ) {
			var settings = {}, noticeContainer, errorNotice, l10n, updateNotice, currentErrorAnnotations = [], editor;
			settings = <?php echo wp_json_encode( $settings ); ?>;
			l10n = <?php echo wp_json_encode( $l10n ); ?>;
			settings.handleTabPrev = function() {
				$( '#templateside' ).find( ':tabbable' ).last().focus();
			};
			settings.handleTabNext = function() {
				$( '#template' ).find( ':tabbable:not(.CodeMirror-code)' ).first().focus();
			};

			updateNotice = function() {
				$( '#submit' ).prop( 'disabled', 0 !== currentErrorAnnotations.length );
				if ( 0 !== currentErrorAnnotations.length ) {
					errorNotice.empty();
					errorNotice.append( $( '<p></p>', {
						text: 1 === currentErrorAnnotations.length ? l10n.singular.replace( '%d', '1' ) : l10n.plural.replace( '%d', currentErrorAnnotations.length ),
					} ) );
					noticeContainer.slideDown( 'fast' );
				} else {
					noticeContainer.slideUp( 'fast' );
				}
			};

			if ( settings.codemirror.lint ) {
				if ( true === settings.codemirror.lint ) {
					settings.codemirror.lint = {};
				}
				noticeContainer = $( '<div id="file-editor-linting-error"></div>' );
				errorNotice = $( '<div class="inline notice notice-error"></div>' );
				noticeContainer.append( errorNotice );
				noticeContainer.hide();
				$( 'p.submit' ).before( noticeContainer );
				settings.codemirror.lint.onUpdateLinting = function ( annotations, annotationsSorted, editor ) {
					currentErrorAnnotations = _.filter( annotations, function( annotation ) {
						return 'error' === annotation.severity;
					} );

					/*
					 * Update notifications when the editor is not focused to prevent error message
					 * from overwhelming the user during input, unless there are no annotations
					 * and in that case update immediately so they can know that they fixed the
					 * errors.
					 */
					if ( ! editor.state.focused || 0 === currentErrorAnnotations.length ) {
						updateNotice();
					}
				};
			}
			editor = wp.codeEditor.initialize( $( '#newcontent' ), settings );

			if ( settings.codemirror.lint ) {
				editor.on( 'blur', function() {
					updateNotice();
				});
				$( editor.display.wrapper ).on( 'mouseout', function() {
					updateNotice();
				});
			}
		} );
		</script>
	<?php
	wp_add_inline_script( 'code-editor', str_replace( array( '<script>', '</script>' ), '', ob_get_clean() ) );
}
