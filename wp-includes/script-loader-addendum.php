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
	$codemirror_version = '5.29.1-alpha-ee20357-' . BETTER_CODE_EDITING_PLUGIN_VERSION;

	$scripts->add( 'codemirror', plugins_url( 'wp-includes/js/codemirror/codemirror.min.js', BETTER_CODE_EDITING_PLUGIN_FILE ), array(), $codemirror_version );

	// The linting engines for the lint addons...
	$scripts->add( 'csslint', plugins_url( 'wp-includes/js/csslint.js', BETTER_CODE_EDITING_PLUGIN_FILE ), array(), '1.0.5' );
	$scripts->add( 'jshint', plugins_url( 'wp-includes/js/jshint.js', BETTER_CODE_EDITING_PLUGIN_FILE ), array(), '2.9.5' );
	$scripts->add( 'jsonlint', plugins_url( 'wp-includes/js/jsonlint.js', BETTER_CODE_EDITING_PLUGIN_FILE ), array(), '1.6.2' );
	$scripts->add( 'htmlhint', plugins_url( 'wp-includes/js/htmlhint.js', BETTER_CODE_EDITING_PLUGIN_FILE ), array(), '0.9.14-xwp' );
	$scripts->add( 'htmlhint-kses', plugins_url( 'wp-includes/js/htmlhint-kses.js', BETTER_CODE_EDITING_PLUGIN_FILE ), array( 'htmlhint' ), BETTER_CODE_EDITING_PLUGIN_VERSION );

	$scripts->add( 'code-editor', plugins_url( 'wp-admin/js/code-editor.js', BETTER_CODE_EDITING_PLUGIN_FILE ), array( 'jquery', 'codemirror' ), BETTER_CODE_EDITING_PLUGIN_VERSION );

	$scripts->add( 'custom-html-widgets', plugins_url( 'wp-admin/js/widgets/custom-html-widgets.js', BETTER_CODE_EDITING_PLUGIN_FILE ), array( 'code-editor', 'jquery', 'backbone', 'wp-util', 'jquery-ui-core', 'wp-a11y' ), BETTER_CODE_EDITING_PLUGIN_VERSION );
	$scripts->add( 'wp-theme-plugin-editor', plugins_url( 'wp-admin/js/theme-plugin-editor.js', BETTER_CODE_EDITING_PLUGIN_FILE ), array( 'code-editor', 'jquery', 'jquery-ui-core', 'wp-a11y', 'underscore' ), BETTER_CODE_EDITING_PLUGIN_VERSION );
}

/**
 * Register styles.
 *
 * @param WP_Styles $styles Styles.
 */
function _better_code_editing_register_styles( WP_Styles $styles ) {
	$codemirror_version = '5.29.1-alpha-ee20357-' . BETTER_CODE_EDITING_PLUGIN_VERSION;

	/*
	 * Override common.css with patched version that has proper styling for CodeMirror and textarea.
	 */
	$styles->registered['common']->src = plugins_url( 'wp-admin/css/common.css', BETTER_CODE_EDITING_PLUGIN_FILE );
	$styles->registered['common']->ver = BETTER_CODE_EDITING_PLUGIN_VERSION;
	unset( $styles->registered['common']->extra['suffix'] ); // Prevent minified version from being attempted.

	$styles->add( 'codemirror', plugins_url( 'wp-includes/js/codemirror/codemirror.min.css', BETTER_CODE_EDITING_PLUGIN_FILE ), array(), $codemirror_version );
	$styles->add( 'code-editor', plugins_url( 'wp-admin/css/code-editor.css', BETTER_CODE_EDITING_PLUGIN_FILE ), array( 'codemirror' ), BETTER_CODE_EDITING_PLUGIN_VERSION );

	// RTL CSS.
	$rtl_styles = array(
		'code-editor',
	);
	foreach ( $rtl_styles as $rtl_style ) {
		$styles->add_data( $rtl_style, 'rtl', 'replace' );
	}

	// Patch the stylesheets.
	if ( function_exists( 'is_rtl' ) && is_rtl() && file_exists( plugin_dir_path( BETTER_CODE_EDITING_PLUGIN_FILE ) . 'wp-admin/css/common-rtl.css' ) ) {
		$suffix = '-rtl.css';
	} else {
		$suffix = '.css';
	}
	$styles->add_inline_style( 'widgets', file_get_contents( dirname( BETTER_CODE_EDITING_PLUGIN_FILE ) . '/wp-admin/css/widgets-addendum' . $suffix ) );
	$styles->add_inline_style( 'customize-controls', file_get_contents( dirname( BETTER_CODE_EDITING_PLUGIN_FILE ) . '/wp-admin/css/customize-controls-addendum' . $suffix ) );
}
