<?php
/**
 * Widget API: WP_Widget_Custom_HTML class
 *
 * @package WordPress
 * @subpackage Widgets
 * @since 4.8.1
 */

/**
 * Core class used to implement a Custom HTML widget.
 *
 * @see WP_Widget
 */
class WP_Widget_Custom_HTML_CodeMirror extends WP_Widget_Custom_HTML {

	/**
	 * Whether or not the widget has been registered yet.
	 *
	 * @since 4.9.0
	 * @var bool
	 */
	protected $registered = false;

	/**
	 * Add hooks for enqueueing assets when registering all widget instances of this widget class.
	 *
	 * @since 4.9.0
	 *
	 * @param integer $number Optional. The unique order number of this widget instance
	 *                        compared to other instances of the same class. Default -1.
	 */
	public function _register_one( $number = -1 ) {
		parent::_register_one( $number );
		if ( $this->registered ) {
			return;
		}
		$this->registered = true;

		wp_add_inline_script( 'custom-html-widgets', sprintf( 'wp.customHtmlWidgets.idBases.push( %s );', wp_json_encode( $this->id_base ) ) );

		// Note that the widgets component in the customizer will also do the 'admin_print_scripts-widgets.php' action in WP_Customize_Widgets::print_scripts().
		add_action( 'admin_print_scripts-widgets.php', array( $this, 'enqueue_admin_scripts' ) );

		// Note that the widgets component in the customizer will also do the 'admin_footer-widgets.php' action in WP_Customize_Widgets::print_footer_scripts().
		add_action( 'admin_footer-widgets.php', array( 'WP_Widget_Custom_HTML_CodeMirror', 'render_control_template_scripts' ) );

		// Note this action is used to ensure the help text is added to the end.
		add_action( 'admin_head-widgets.php', array( 'WP_Widget_Custom_HTML_CodeMirror', 'add_help_text' ) );
	}

	/**
	 * Loads the required scripts and styles for the widget control.
	 *
	 * @since 4.9.0
	 */
	public function enqueue_admin_scripts() {
		$settings = wp_enqueue_code_editor( array(
			'type' => 'text/html',
		) );

		wp_enqueue_script( 'custom-html-widgets' );
		if ( empty( $settings ) ) {
			$settings = array(
				'disabled' => true,
			);
		}
		wp_add_inline_script( 'custom-html-widgets', sprintf( 'wp.customHtmlWidgets.init( %s );', wp_json_encode( $settings ) ), 'after' );

		$l10n = array(
			'errorNotice' => wp_array_slice_assoc(
				/* translators: placeholder is error count */
				_n_noop( 'There is %d error which must be fixed before you can save.', 'There are %d errors which must be fixed before you can save.', 'better-code-editing' ),
				array( 'singular', 'plural' )
			),
		);
		wp_add_inline_script( 'custom-html-widgets', sprintf( 'jQuery.extend( wp.customHtmlWidgets.l10n, %s );', wp_json_encode( $l10n ) ), 'after' );
	}

	/**
	 * Outputs the Custom HTML widget settings form.
	 *
	 * @since 4.8.1
	 *
	 * @param array $instance Current instance.
	 * @returns void
	 */
	public function form( $instance ) {
		$instance = wp_parse_args( (array) $instance, $this->default_instance );
		?>
		<input id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" class="title sync-input" type="hidden" value="<?php echo esc_attr( $instance['title'] ); ?>"/>
		<textarea id="<?php echo $this->get_field_id( 'content' ); ?>" name="<?php echo $this->get_field_name( 'content' ); ?>" class="content sync-input" hidden><?php echo esc_textarea( $instance['content'] ); ?></textarea>
		<?php
	}

	/**
	 * Render form template scripts.
	 *
	 * @since 4.9.0
	 */
	static public function render_control_template_scripts() {
		?>
		<script type="text/html" id="tmpl-widget-custom-html-control-fields">
			<# var elementIdPrefix = 'el' + String( Math.random() ).replace( /\D/g, '' ) + '_' #>
			<p>
				<label for="{{ elementIdPrefix }}title"><?php esc_html_e( 'Title:', 'default' ); ?></label>
				<input id="{{ elementIdPrefix }}title" type="text" class="widefat title">
			</p>

			<p>
				<label for="{{ elementIdPrefix }}content" class="screen-reader-text"><?php esc_html_e( 'Content:', 'default' ); ?></label>
				<textarea id="{{ elementIdPrefix }}content" class="widefat code content" rows="16" cols="20"></textarea>
			</p>

			<?php if ( ! current_user_can( 'unfiltered_html' ) ) : ?>
				<?php
				$probably_unsafe_html = array( 'script', 'iframe', 'form', 'input', 'style' );
				$allowed_html = wp_kses_allowed_html( 'post' );
				$disallowed_html = array_diff( $probably_unsafe_html, array_keys( $allowed_html ) );
				?>
				<?php if ( ! empty( $disallowed_html ) ) : ?>
					<# if ( data.codeEditorDisabled ) { #>
						<p>
							<?php _e( 'Some HTML tags are not permitted, including:', 'default' ); ?>
							<code><?php echo join( '</code>, <code>', $disallowed_html ); ?></code>
						</p>
					<# } #>
				<?php endif; ?>
			<?php endif; ?>

			<div class="code-editor-error-container"></div>
		</script>
		<?php
	}

	/**
	 * Add help text to widgets admin screen.
	 *
	 * @since 4.9.0
	 */
	static public function add_help_text() {
		$screen = get_current_screen();

		$content = '<p>';
		$content .= __( 'Use the Custom HTML widget to add arbitrary HTML code to your widget areas.', 'better-code-editing' );
		$content .= '</p>';

		$content .= '<p>' . __( 'When using a keyboard to navigate:', 'better-code-editing' ) . '</p>';
		$content .= '<ul>';
		$content .= '<li>' . __( 'In the HTML edit field, Tab enters a tab character.', 'better-code-editing' ) . '</li>';
		$content .= '<li>' . __( 'To move keyboard focus, press Esc then Tab for the next element, or Esc then Shift+Tab for the previous element.', 'better-code-editing' ) . '</li>';
		$content .= '</ul>';

		$content .= '<p>';
		$content .= sprintf(
			/* translators: placeholder is link to user profile */
			__( 'The edit field automatically highlights code syntax. You can disable this in your %s to work in plan text mode.', 'better-code-editing' ),
			sprintf(
				' <a href="%1$s" class="external-link" target="_blank">%2$s<span class="screen-reader-text">%3$s</span></a>',
				esc_url( get_edit_profile_url() . '#syntax_highlighting' ),
				__( 'user profile', 'better-code-editing' ),
				/* translators: accessibility text */
				__( '(opens in a new window)', 'default' )
			)
		);
		$content .= '</p>';

		$screen->add_help_tab( array(
			'id' => 'custom_html_widget',
			'title' => __( 'Custom HTML Widget', 'better-code-editing' ),
			'content' => $content,
		) );
	}
}
