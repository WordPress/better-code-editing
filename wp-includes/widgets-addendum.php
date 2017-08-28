<?php
/**
 * Extensions to core Widgets API
 *
 * @package WordPress
 * @subpackage Widgets
 */

add_action( 'widgets_init', '_better_code_editing_register_custom_html_widget' );

/**
 * Replace Custom HTML widget with CodeMirror Custom HTML Widget.
 *
 * This will not be included in the core merge since the `WP_Widget_Custom_HTML` would be updated itself.
 */
function _better_code_editing_register_custom_html_widget() {
	if ( class_exists( 'WP_Widget_Custom_HTML' ) ) {
		require_once dirname( __FILE__ ) . '/widgets/class-wp-widget-custom-html-codemirror.php';
		unregister_widget( 'WP_Widget_Custom_HTML' );
		register_widget( 'WP_Widget_Custom_HTML_CodeMirror' );
	}
}
