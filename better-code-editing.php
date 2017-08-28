<?php
/**
 * Plugin Name: Better Code Editing (formerly CodeMirror WP)
 * Plugin URI: https://wordpress.org/plugins/better-code-editing/
 * Description: Code highlighting and linting, powered by CodeMirror.
 * Version: 0.3.0
 * Author: The WordPress Team
 * Text Domain: better-code-editing
 *
 * @package WordPress
 */

define( 'BETTER_CODE_EDITING_PLUGIN_VERSION', '0.3.0' );
define( 'BETTER_CODE_EDITING_PLUGIN_FILE', __FILE__ );

// Short-circuit when the functionality is already merged into core.
if ( file_exists( 'wp_code_editor_settings' ) ) {
	return;
}

require_once dirname( __FILE__ ) . '/wp-includes/general-template-addendum.php';
require_once dirname( __FILE__ ) . '/wp-includes/script-loader-addendum.php';
require_once dirname( __FILE__ ) . '/wp-includes/customize-manager-addendum.php';
require_once dirname( __FILE__ ) . '/wp-includes/widgets-addendum.php';
require_once dirname( __FILE__ ) . '/wp-admin/user-edit-addendum.php';
require_once dirname( __FILE__ ) . '/wp-admin/includes/user.php';
require_once dirname( __FILE__ ) . '/wp-admin/file-editor-addendum.php';
