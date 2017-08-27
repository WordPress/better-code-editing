<?php
/**
 * Extensions to WP_Customize_Manager.
 *
 * @package WordPress
 */

add_action( 'customize_register', '_better_code_editing_amend_custom_css_help_text', 11 );
add_action( 'customize_controls_enqueue_scripts', '_better_code_editing_customize_controls_enqueue_scripts' );
add_action( 'customize_controls_print_footer_scripts', '_better_code_editing_amend_customize_pane_settings', 1001 );


/**
 * Set up code editor for Custom CSS.
 *
 * @param WP_Customize_Manager $wp_customize Manager.
 */
function _better_code_editing_amend_custom_css_help_text( WP_Customize_Manager $wp_customize ) {
	$wp_customize->custom_css_code_editor_settings = wp_code_editor_settings( array(
		'file' => 'custom.css',
	) );

	if ( empty( $wp_customize->custom_css_code_editor_settings ) ) {
		return;
	}

	$section = $wp_customize->get_section( 'custom_css' );
	if ( ! $section ) {
		return;
	}

	$section->description = sprintf( '%s<br /><a href="%s" class="external-link" target="_blank">%s<span class="screen-reader-text">%s</span></a>',
		sprintf(
			/* translators: placeholder is profile URL */
			__( 'CSS allows you to customize the appearance and layout of your site with code. Separate CSS is saved for each of your themes. In the editing area the Tab key enters a tab character. To move keyboard focus to another element, press the Esc key followed by the Tab key for the next element or Shift+Tab key for the previous element. You can disable the code syntax highlighter in your <a href="%s" target="blank" class="external-link">user profile</a>. This will allow you to work in plain text mode.', 'better-code-editing' ),
			esc_url( get_edit_profile_url() . '#syntax_highlighting' )
		),
		esc_url( __( 'https://codex.wordpress.org/CSS', 'default' ) ),
		__( 'Learn more about CSS', 'default' ),
		/* translators: accessibility text */
		__( '(opens in a new window)', 'default' )
	);
}

/**
 * Enqueue assets for Customizer.
 *
 * @global WP_Customize_Manager $wp_customize
 */
function _better_code_editing_customize_controls_enqueue_scripts() {
	global $wp_customize;
	if ( ! empty( $wp_customize->custom_css_code_editor_settings ) ) {
		wp_enqueue_code_editor( $wp_customize->custom_css_code_editor_settings );
	}
	wp_add_inline_script( 'customize-controls', file_get_contents( dirname( BETTER_CODE_EDITING_PLUGIN_FILE ) . '/wp-admin/js/customize-controls-addendum.js' ) );
}

/**
 * Add Customizer integration.
 *
 * @see WP_Customize_Manager::customize_pane_settings()
 * @global WP_Customize_Manager $wp_customize
 */
function _better_code_editing_amend_customize_pane_settings() {
	global $wp_customize;
	if ( ! empty( $wp_customize->custom_css_code_editor_settings ) ) {
		printf( '<script>window._wpCustomizeSettings.codeEditor = %s</script>;', wp_json_encode( $wp_customize->custom_css_code_editor_settings ) );
	}
}
