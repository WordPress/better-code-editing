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

	$args = array();

	if ( 'theme-editor.php' === $hook ) {
		$theme = wp_get_theme( isset( $_REQUEST['theme'] ) ? wp_unslash( $_REQUEST['theme'] ) : get_stylesheet() );
		if ( $theme->errors() ) {
			wp_die( $theme->errors()->get_error_message() );
		}

		if ( isset( $_REQUEST['file'] ) ) {
			$args['file'] = sanitize_text_field( wp_unslash( $_REQUEST['file'] ) );
		} else {
			$args['file'] = 'style.css';
		}
	} elseif ( 'plugin-editor.php' === $hook ) {
		$args['plugin'] = isset( $_REQUEST['plugin'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['plugin'] ) ) : '';
		$args['file'] = isset( $_REQUEST['file'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['file'] ) ) : '';

		if ( empty( $args['plugin'] ) ) {
			$file_paths = array_keys( get_plugins() );
			$args['plugin'] = $args['file'] ? $args['file'] : array_shift( $file_paths );
		} elseif ( 0 !== validate_file( $args['plugin'] ) ) {
			wp_die( __( 'Sorry, that file cannot be edited.', 'better-code-editing' ) );
		}

		$plugin_files = get_plugin_files( $args['plugin'] );
		if ( empty( $args['file'] ) ) {
			$args['file'] = $plugin_files[0];
		}

		$args['file'] = validate_file_to_edit( $args['file'], $plugin_files );
	}

	$settings = wp_enqueue_code_editor( $args );
	if ( empty( $settings ) ) {
		return;
	}

	wp_enqueue_script( 'wp-theme-plugin-editor' );

	$l10n = wp_array_slice_assoc(
		/* translators: placeholder is error count */
		_n_noop( 'There is %d error which must be fixed before you can save.', 'There are %d errors which must be fixed before you can save.', 'better-code-editing' ),
		array( 'singular', 'plural' )
	);
	wp_add_inline_script( 'wp-theme-plugin-editor', sprintf( 'wp.themePluginEditor.l10n = %s;', wp_json_encode( $l10n ) ) );
	wp_add_inline_script( 'wp-theme-plugin-editor', sprintf( 'jQuery( function() { wp.themePluginEditor.init( %s ); } )', wp_json_encode( $settings ) ) );
}
