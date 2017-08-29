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
	wp_enqueue_code_editor( $settings );

	ob_start();
	?>
	<script>
		jQuery( function( $ ) {
			var settings = {};
			settings = <?php echo wp_json_encode( $settings ); ?>;
			settings.handleTabPrev = function() {
				$( '#templateside' ).find( ':tabbable' ).last().focus();
			};
			settings.handleTabNext = function() {
				$( '#template' ).find( ':tabbable:not(.CodeMirror-code)' ).first().focus();
			};
			wp.codeEditor.initialize( $( '#newcontent' ), settings );
		} );
		</script>
	<?php
	wp_add_inline_script( 'code-editor', str_replace( array( '<script>', '</script>' ), '', ob_get_clean() ) );
}
