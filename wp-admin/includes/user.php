<?php
/**
 * Extensions to WordPress user administration API.
 *
 * @package WordPress
 * @subpackage Administration
 */

add_action( 'personal_options_update', '_better_code_editing_update_syntax_highlighting_user_setting' );
add_action( 'edit_user_profile_update', '_better_code_editing_update_syntax_highlighting_user_setting' );

/**
 * Update the syntax_highlighting user setting.
 *
 * @param int $user_id User being edited.
 */
function _better_code_editing_update_syntax_highlighting_user_setting( $user_id ) {
	if ( ! current_user_can( 'edit_user', $user_id ) ) {
		return;
	}
	update_user_meta( $user_id, 'syntax_highlighting', isset( $_POST['syntax_highlighting'] ) && 'false' === $_POST['syntax_highlighting'] ? 'false' : 'true' );
}
