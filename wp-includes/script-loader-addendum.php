<?php
/**
 * Extensions to WordPress scripts and styles default loader.
 *
 * @package WordPress
 */

add_action( 'wp_default_scripts', '_better_code_editing_default_scripts' );
add_action( 'wp_default_styles', '_better_code_editing_register_styles' );

/**
 * Register scripts.
 *
 * @param WP_Scripts $scripts Scripts.
 */
function _better_code_editing_default_scripts( WP_Scripts $scripts ) {
	$codemirror_version = '5.29.0';

	$scripts->add( 'codemirror', plugins_url( 'wp-includes/js/codemirror/lib/codemirror.js', BETTER_CODE_EDITING_PLUGIN_FILE ), array(), $codemirror_version );

	$scripts->add( 'codemirror-addon-hint-show',       plugins_url( 'wp-includes/js/codemirror/addon/hint/show-hint.js', BETTER_CODE_EDITING_PLUGIN_FILE ),       array( 'codemirror' ), $codemirror_version );
	$scripts->add( 'codemirror-addon-hint-anyword',    plugins_url( 'wp-includes/js/codemirror/addon/hint/anyword-hint.js', BETTER_CODE_EDITING_PLUGIN_FILE ),    array( 'codemirror' ), $codemirror_version );
	$scripts->add( 'codemirror-addon-hint-css',        plugins_url( 'wp-includes/js/codemirror/addon/hint/css-hint.js', BETTER_CODE_EDITING_PLUGIN_FILE ),        array( 'codemirror-addon-hint-show', 'codemirror-mode-css' ), $codemirror_version );
	$scripts->add( 'codemirror-addon-hint-html',       plugins_url( 'wp-includes/js/codemirror/addon/hint/html-hint.js', BETTER_CODE_EDITING_PLUGIN_FILE ),       array( 'codemirror-addon-hint-show', 'codemirror-addon-hint-xml', 'codemirror-mode-htmlmixed' ), $codemirror_version );
	$scripts->add( 'codemirror-addon-hint-javascript', plugins_url( 'wp-includes/js/codemirror/addon/hint/javascript-hint.js', BETTER_CODE_EDITING_PLUGIN_FILE ), array( 'codemirror-addon-hint-show', 'codemirror-mode-javascript' ), $codemirror_version );
	$scripts->add( 'codemirror-addon-hint-sql',        plugins_url( 'wp-includes/js/codemirror/addon/hint/sql-hint.js', BETTER_CODE_EDITING_PLUGIN_FILE ),        array( 'codemirror-addon-hint-show', 'codemirror-mode-sql' ), $codemirror_version );
	$scripts->add( 'codemirror-addon-hint-xml',        plugins_url( 'wp-includes/js/codemirror/addon/hint/xml-hint.js', BETTER_CODE_EDITING_PLUGIN_FILE ),        array( 'codemirror-addon-hint-show', 'codemirror-mode-xml' ), $codemirror_version );

	// The linting engines for the lint addons...
	$scripts->add( 'csslint',  plugins_url( 'wp-includes/js/csslint.js', BETTER_CODE_EDITING_PLUGIN_FILE ), array(), '1.0.5' );
	$scripts->add( 'htmlhint', plugins_url( 'wp-includes/js/htmlhint.js', BETTER_CODE_EDITING_PLUGIN_FILE ), array(), '0.9.14-xwp' );
	$scripts->add( 'jshint',   plugins_url( 'wp-includes/js/jshint.js', BETTER_CODE_EDITING_PLUGIN_FILE ), array(), '2.9.5' );
	$scripts->add( 'jsonlint', plugins_url( 'wp-includes/js/jsonlint.js', BETTER_CODE_EDITING_PLUGIN_FILE ), array(), '1.6.2' );

	$scripts->add( 'htmlhint-kses', plugins_url( 'wp-includes/js/htmlhint-kses.js', BETTER_CODE_EDITING_PLUGIN_FILE ), array( 'htmlhint' ), BETTER_CODE_EDITING_PLUGIN_VERSION );

	$scripts->add( 'codemirror-addon-lint',            plugins_url( 'wp-includes/js/codemirror/addon/lint/lint.js',      BETTER_CODE_EDITING_PLUGIN_FILE ),       array( 'codemirror' ),            $codemirror_version );
	$scripts->add( 'codemirror-addon-lint-css',        plugins_url( 'wp-includes/js/codemirror/addon/lint/css-lint.js',  BETTER_CODE_EDITING_PLUGIN_FILE ),       array( 'codemirror-addon-lint', 'csslint' ), $codemirror_version );
	$scripts->add( 'codemirror-addon-lint-html',       plugins_url( 'wp-includes/js/codemirror/addon/lint/html-lint.js', BETTER_CODE_EDITING_PLUGIN_FILE ),       array( 'codemirror-addon-lint', 'htmlhint', 'csslint', 'jshint' ), $codemirror_version );
	$scripts->add( 'codemirror-addon-lint-javascript', plugins_url( 'wp-includes/js/codemirror/addon/lint/javascript-lint.js', BETTER_CODE_EDITING_PLUGIN_FILE ), array( 'codemirror-addon-lint', 'jshint' ), $codemirror_version );
	$scripts->add( 'codemirror-addon-lint-json',       plugins_url( 'wp-includes/js/codemirror/addon/lint/json-lint.js', BETTER_CODE_EDITING_PLUGIN_FILE ),       array( 'codemirror-addon-lint', 'jsonlint' ), $codemirror_version );

	$scripts->add( 'codemirror-addon-comment',                 plugins_url( 'wp-includes/js/codemirror/addon/comment/comment.js', BETTER_CODE_EDITING_PLUGIN_FILE ),         array( 'codemirror' ), $codemirror_version );
	$scripts->add( 'codemirror-addon-comment-continuecomment', plugins_url( 'wp-includes/js/codemirror/addon/comment/continuecomment.js', BETTER_CODE_EDITING_PLUGIN_FILE ), array( 'codemirror' ), $codemirror_version );
	$scripts->add( 'codemirror-addon-fold-xml-fold',           plugins_url( 'wp-includes/js/codemirror/addon/fold/xml-fold.js', BETTER_CODE_EDITING_PLUGIN_FILE ),           array( 'codemirror' ), $codemirror_version );

	$scripts->add( 'codemirror-addon-edit-closebrackets', plugins_url( 'wp-includes/js/codemirror/addon/edit/closebrackets.js', BETTER_CODE_EDITING_PLUGIN_FILE ), array( 'codemirror' ), $codemirror_version );
	$scripts->add( 'codemirror-addon-edit-closetag',      plugins_url( 'wp-includes/js/codemirror/addon/edit/closetag.js', BETTER_CODE_EDITING_PLUGIN_FILE ),      array( 'codemirror' ), $codemirror_version );
	$scripts->add( 'codemirror-addon-edit-continuelist',  plugins_url( 'wp-includes/js/codemirror/addon/edit/continuelist.js', BETTER_CODE_EDITING_PLUGIN_FILE ),  array( 'codemirror' ), $codemirror_version );
	$scripts->add( 'codemirror-addon-edit-matchbrackets', plugins_url( 'wp-includes/js/codemirror/addon/edit/matchbrackets.js', BETTER_CODE_EDITING_PLUGIN_FILE ), array( 'codemirror' ), $codemirror_version );
	$scripts->add( 'codemirror-addon-edit-matchtags',     plugins_url( 'wp-includes/js/codemirror/addon/edit/matchtags.js', BETTER_CODE_EDITING_PLUGIN_FILE ),     array( 'codemirror', 'codemirror-addon-fold-xml-fold' ), $codemirror_version );
	$scripts->add( 'codemirror-addon-edit-trailingspace', plugins_url( 'wp-includes/js/codemirror/addon/edit/trailingspace.js', BETTER_CODE_EDITING_PLUGIN_FILE ), array( 'codemirror' ), $codemirror_version );

	$scripts->add( 'codemirror-addon-selection-active-line',    plugins_url( 'wp-includes/js/codemirror/addon/selection/active-line.js', BETTER_CODE_EDITING_PLUGIN_FILE ),       array( 'codemirror' ), $codemirror_version );
	$scripts->add( 'codemirror-addon-selection-mark-selection', plugins_url( 'wp-includes/js/codemirror/addon/selection/mark-selection.js', BETTER_CODE_EDITING_PLUGIN_FILE ),    array( 'codemirror' ), $codemirror_version );
	$scripts->add( 'codemirror-addon-selection-pointer',        plugins_url( 'wp-includes/js/codemirror/addon/selection/selection-pointer.js', BETTER_CODE_EDITING_PLUGIN_FILE ), array( 'codemirror' ), $codemirror_version );

	$scripts->add( 'codemirror-mode-clike',      plugins_url( 'wp-includes/js/codemirror/mode/clike/clike.js', BETTER_CODE_EDITING_PLUGIN_FILE ),           array( 'codemirror' ), $codemirror_version );
	$scripts->add( 'codemirror-mode-css',        plugins_url( 'wp-includes/js/codemirror/mode/css/css.js', BETTER_CODE_EDITING_PLUGIN_FILE ),               array( 'codemirror' ), $codemirror_version );
	$scripts->add( 'codemirror-mode-diff',       plugins_url( 'wp-includes/js/codemirror/mode/diff/diff.js', BETTER_CODE_EDITING_PLUGIN_FILE ),             array( 'codemirror' ), $codemirror_version );
	$scripts->add( 'codemirror-mode-htmlmixed',  plugins_url( 'wp-includes/js/codemirror/mode/htmlmixed/htmlmixed.js', BETTER_CODE_EDITING_PLUGIN_FILE ),   array( 'codemirror', 'codemirror-mode-css', 'codemirror-mode-javascript', 'codemirror-mode-xml' ), $codemirror_version );
	$scripts->add( 'codemirror-mode-http',       plugins_url( 'wp-includes/js/codemirror/mode/http/http.js', BETTER_CODE_EDITING_PLUGIN_FILE ),             array( 'codemirror' ), $codemirror_version );
	$scripts->add( 'codemirror-mode-javascript', plugins_url( 'wp-includes/js/codemirror/mode/javascript/javascript.js', BETTER_CODE_EDITING_PLUGIN_FILE ), array( 'codemirror' ), $codemirror_version );
	$scripts->add( 'codemirror-mode-jsx',        plugins_url( 'wp-includes/js/codemirror/mode/jsx/jsx.js', BETTER_CODE_EDITING_PLUGIN_FILE ),               array( 'codemirror' ), $codemirror_version );
	$scripts->add( 'codemirror-mode-markdown',   plugins_url( 'wp-includes/js/codemirror/mode/markdown/markdown.js', BETTER_CODE_EDITING_PLUGIN_FILE ),     array( 'codemirror' ), $codemirror_version );
	$scripts->add( 'codemirror-mode-nginx',      plugins_url( 'wp-includes/js/codemirror/mode/nginx/nginx.js', BETTER_CODE_EDITING_PLUGIN_FILE ),        array( 'codemirror' ), $codemirror_version );
	$scripts->add( 'codemirror-mode-php',        plugins_url( 'wp-includes/js/codemirror/mode/php/php.js', BETTER_CODE_EDITING_PLUGIN_FILE ),               array( 'codemirror-mode-clike', 'codemirror-mode-xml', 'codemirror-mode-javascript', 'codemirror-mode-css', 'codemirror-mode-htmlmixed' ), $codemirror_version );
	$scripts->add( 'codemirror-mode-sass',       plugins_url( 'wp-includes/js/codemirror/mode/sass/sass.js', BETTER_CODE_EDITING_PLUGIN_FILE ),             array( 'codemirror' ), $codemirror_version );
	$scripts->add( 'codemirror-mode-shell',      plugins_url( 'wp-includes/js/codemirror/mode/shell/shell.js', BETTER_CODE_EDITING_PLUGIN_FILE ),           array( 'codemirror' ), $codemirror_version );
	$scripts->add( 'codemirror-mode-sql',        plugins_url( 'wp-includes/js/codemirror/mode/sql/sql.js', BETTER_CODE_EDITING_PLUGIN_FILE ),               array( 'codemirror' ), $codemirror_version );
	$scripts->add( 'codemirror-mode-xml',        plugins_url( 'wp-includes/js/codemirror/mode/xml/xml.js', BETTER_CODE_EDITING_PLUGIN_FILE ),               array( 'codemirror' ), $codemirror_version );
	$scripts->add( 'codemirror-mode-yaml',       plugins_url( 'wp-includes/js/codemirror/mode/yaml/yaml.js', BETTER_CODE_EDITING_PLUGIN_FILE ),               array( 'codemirror' ), $codemirror_version );

	$scripts->add( 'code-editor', plugins_url( 'wp-admin/js/code-editor.js', BETTER_CODE_EDITING_PLUGIN_FILE ), array( 'jquery', 'codemirror' ), BETTER_CODE_EDITING_PLUGIN_VERSION );

	$scripts->add( 'custom-html-widgets', plugins_url( 'wp-admin/js/widgets/custom-html-widgets.js', BETTER_CODE_EDITING_PLUGIN_FILE ), array( 'code-editor', 'jquery', 'backbone', 'wp-util' ), BETTER_CODE_EDITING_PLUGIN_VERSION );

	// Make sure all CodeMirror assets present are registered. This will not be included in core merge.
	if ( defined( 'SCRIPT_DEBUG' ) ) {
		$plugin_dir_url = plugins_url( '', BETTER_CODE_EDITING_PLUGIN_FILE );
		$codemirror_path = 'wp-includes/js/codemirror';
		$codemirror_registered_paths = array();
		$plugin_dir_path = plugin_dir_path( BETTER_CODE_EDITING_PLUGIN_FILE );

		foreach ( $scripts->registered as $handle => $script ) {
			if ( 0 === strpos( $script->src, $plugin_dir_url ) ) {
				$path = substr( $script->src, strlen( $plugin_dir_url ) + 1 );
				if ( ! file_exists( $plugin_dir_path . $path ) ) {
					trigger_error( "Missing '$handle' script src: $path'", E_USER_WARNING );
				}
				if ( 0 === strpos( $path, $codemirror_path ) ) {
					$codemirror_registered_paths[] = $path;
				}
			}
		}

		$directory = new RecursiveDirectoryIterator( $plugin_dir_path . $codemirror_path );
		$iterator = new RecursiveIteratorIterator( $directory );
		$js_iterator = new RegexIterator( $iterator, '#\.js$#' );

		$codemirror_existing_paths = array();
		foreach ( $js_iterator as $js_file ) {
			$codemirror_existing_paths[] = substr( $js_file->getPathname(), strlen( $plugin_dir_path ) );
		}
		$unregistered_script_assets = array_diff( $codemirror_existing_paths, $codemirror_registered_paths );
		if ( ! empty( $unregistered_script_assets ) ) {
			trigger_error( sprintf( 'There are %d script assets from CodeMirror that are not registered: %s', count( $unregistered_script_assets ), join( ', ', $unregistered_script_assets ) ), E_USER_WARNING );
		}
	}
}

/**
 * Register styles.
 *
 * @param WP_Styles $styles Styles.
 */
function _better_code_editing_register_styles( WP_Styles $styles ) {
	$codemirror_version = '5.29.0';

	/*
	 * Override common.css with patched version that has proper styling for CodeMirror and textarea.
	 */
	$styles->registered['common']->src = plugins_url( 'wp-admin/css/common.css', BETTER_CODE_EDITING_PLUGIN_FILE );
	$styles->registered['common']->ver = BETTER_CODE_EDITING_PLUGIN_VERSION;

	$styles->add( 'codemirror',                 plugins_url( 'wp-includes/js/codemirror/lib/codemirror.css', BETTER_CODE_EDITING_PLUGIN_FILE ),       array(),               $codemirror_version );
	$styles->add( 'codemirror-addon-show-hint', plugins_url( 'wp-includes/js/codemirror/addon/hint/show-hint.css', BETTER_CODE_EDITING_PLUGIN_FILE ), array( 'codemirror' ), $codemirror_version );
	$styles->add( 'codemirror-addon-lint',      plugins_url( 'wp-includes/js/codemirror/addon/lint/lint.css', BETTER_CODE_EDITING_PLUGIN_FILE ),      array( 'codemirror' ), $codemirror_version );

	$styles->add( 'code-editor', plugins_url( 'wp-admin/css/code-editor.css', BETTER_CODE_EDITING_PLUGIN_FILE ), array( 'codemirror' ), BETTER_CODE_EDITING_PLUGIN_VERSION );

	// Patch the stylesheets.
	$styles->add_inline_style( 'widgets', file_get_contents( dirname( BETTER_CODE_EDITING_PLUGIN_FILE ) . '/wp-admin/css/widgets-addendum.css' ) );
	$styles->add_inline_style( 'customize-controls', file_get_contents( dirname( BETTER_CODE_EDITING_PLUGIN_FILE ) . '/wp-admin/css/customize-controls-addendum.css' ) );

	// Make sure all CodeMirror assets present are registered. This will not be included in core merge.
	if ( defined( 'SCRIPT_DEBUG' ) ) {
		$plugin_dir_url = plugins_url( '', BETTER_CODE_EDITING_PLUGIN_FILE );
		$codemirror_path = 'wp-includes/js/codemirror';
		$codemirror_registered_paths = array();
		$plugin_dir_path = plugin_dir_path( BETTER_CODE_EDITING_PLUGIN_FILE );

		foreach ( $styles->registered as $handle => $style ) {
			if ( 'common' === $handle ) {
				continue;
			}

			if ( 0 === strpos( $style->src, $plugin_dir_url ) ) {
				$path = substr( $style->src, strlen( $plugin_dir_url ) + 1 );
				if ( ! file_exists( $plugin_dir_path . $path ) ) {
					trigger_error( "Missing '$handle' style src: $path'", E_USER_WARNING );
				}
				if ( 0 === strpos( $path, $codemirror_path ) ) {
					$codemirror_registered_paths[] = $path;
				}
			}
		}

		$directory = new RecursiveDirectoryIterator( $plugin_dir_path . $codemirror_path );
		$iterator = new RecursiveIteratorIterator( $directory );
		$css_iterator = new RegexIterator( $iterator, '#\.css$#' );

		$codemirror_existing_paths = array();
		foreach ( $css_iterator as $js_file ) {
			$codemirror_existing_paths[] = substr( $js_file->getPathname(), strlen( $plugin_dir_path ) );
		}
		$unregistered_assets = array_diff( $codemirror_existing_paths, $codemirror_registered_paths );
		if ( ! empty( $unregistered_assets ) ) {
			trigger_error( sprintf( 'There are %d style assets from CodeMirror that are not registered: %s', count( $unregistered_assets ), join( ', ', $unregistered_assets ) ), E_USER_WARNING );
		}
	}
}
