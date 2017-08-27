<?php
/**
 * Extensions to edit user administration panel.
 *
 * @package WordPress
 * @subpackage Administration
 */

add_action( 'personal_options', '_better_code_editing_add_syntax_highlighting_user_setting_field' );

/**
 * Render the toggle to disable CodeMirror for the current user.
 *
 * @param WP_User $profileuser Current user being edited.
 */
function _better_code_editing_add_syntax_highlighting_user_setting_field( $profileuser ) {
	$should_show_setting = (
		// For Custom HTML widget and Additional CSS in Customizer.
		user_can( $profileuser, 'edit_theme_options' )
		||
		// Edit plugins.
		user_can( $profileuser, 'edit_plugins' )
		||
		// Edit themes.
		user_can( $profileuser, 'edit_themes' )
	);
	if ( ! $should_show_setting ) {
		return;
	}

	?>
	<tr class="user-syntax-highlighting-wrap">
			<th scope="row"><?php _e( 'Syntax Highlighting', 'better-code-editing' ); ?></th>
			<td>
				<label for="syntax_highlighting"><input name="syntax_highlighting" type="checkbox" id="syntax_highlighting" value="false" <?php checked( 'false', $profileuser->syntax_highlighting ); ?> /> <?php _e( 'Disable syntax highlighting when editing code', 'better-code-editing' ); ?></label>
				<script>
					// Move the option right after the Visual Editor.
					jQuery( function( $ ) {
						$( '.user-rich-editing-wrap' ).after( $( '.user-syntax-highlighting-wrap' ) );
					} );
				</script>
			</td>
		</tr>
	<?php
}
