<?php
/**
 * Plugin Name: Better Code Editing (formerly CodeMirror WP)
 * Plugin URI: https://wordpress.org/plugins/better-code-editing/
 * Description: Code highlighting and linting, powered by CodeMirror.
 * Version: 0.7.0
 * Author: The WordPress Team
 * Text Domain: better-code-editing
 *
 * @package WordPress
 */

define( 'BETTER_CODE_EDITING_PLUGIN_VERSION', '0.7.0' );
define( 'BETTER_CODE_EDITING_PLUGIN_FILE', __FILE__ );

/**
 * Show admin notice on plugins screen when plugin is obsolete.
 */
function _better_code_editing_plugin_obsolete_admin_notice() {
	if ( 'plugins' !== get_current_screen()->base ) {
		return;
	}
	?>
	<div class="notice notice-info">
		<p><?php esc_html_e( 'The Better Code Editing plugin\'s functionality has been merged into core. This plugin can be deactivated and uninstalled.', 'better-code-editing' ); ?></p>
	</div>
	<?php
}

/**
 * Show admin notice when npm install has not been run.
 */
function _better_code_editing_plugin_npm_install_required() {
	?>
	<div class="notice notice-error">
		<p><?php _e( 'The Better Code Editing plugin\'s has been installed from source. In order complete installation, you must run <code>npm install</code> from the command line. Otherwise, please install the plugin from WordPress.org or via a ZIP from the GitHub releases page..', 'better-code-editing' ); ?></p>
	</div>
	<?php
}

// Short-circuit when the functionality is already merged into core.
if ( function_exists( 'wp_enqueue_code_editor' ) ) {
	add_action( 'admin_notices', '_better_code_editing_plugin_obsolete_admin_notice' );
	return;
}

// Show notice if repo was cloned from source without running npm install.
if ( ! file_exists( dirname( __FILE__ ) . '/wp-includes/js/codemirror/codemirror.min.js' ) ) {
	add_action( 'admin_notices', '_better_code_editing_plugin_npm_install_required' );
	return;
}

require_once dirname( __FILE__ ) . '/wp-includes/general-template-addendum.php';
require_once dirname( __FILE__ ) . '/wp-includes/script-loader-addendum.php';
require_once dirname( __FILE__ ) . '/wp-includes/customize-manager-addendum.php';
require_once dirname( __FILE__ ) . '/wp-includes/widgets-addendum.php';
require_once dirname( __FILE__ ) . '/wp-admin/user-edit-addendum.php';
require_once dirname( __FILE__ ) . '/wp-admin/includes/user.php';
require_once dirname( __FILE__ ) . '/wp-admin/file-editor-addendum.php';
