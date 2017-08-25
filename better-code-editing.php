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

/**
 * Plugin class.
 */
class Better_Code_Editing_Plugin {

	/**
	 * CodeMirror version.
	 */
	const VERSION = '0.3.0';

	/**
	 * CodeMirror version.
	 */
	const CODEMIRROR_VERSION = '5.28.0';

	/**
	 * CodeMirror options.
	 *
	 * @var array|bool
	 */
	static $options = array(
		'indentUnit'     => 4,
		'indentWithTabs' => true,
		'inputStyle'     => 'contenteditable',
		'lineNumbers'    => true,
		'lineWrapping'   => true,
	);

	/**
	 * Add hooks.
	 */
	public static function go() {
		add_action( 'wp_default_scripts', array( __CLASS__, 'register_scripts' ) );
		add_action( 'wp_default_styles', array( __CLASS__, 'register_styles' ) );
		add_action( 'load-theme-editor.php', array( __CLASS__, 'load_theme_editor_php' ) );
		add_action( 'load-plugin-editor.php', array( __CLASS__, 'load_plugin_editor_php' ) );
		add_action( 'customize_register', array( __CLASS__, 'amend_custom_css_help_text' ), 11 );
		add_action( 'customize_controls_enqueue_scripts', array( __CLASS__, 'customize_controls_enqueue_scripts' ) );
		add_action( 'customize_controls_print_footer_scripts', array( __CLASS__, 'amend_customize_pane_settings' ), 1001 );
		add_action( 'widgets_init', array( __CLASS__, 'register_custom_html_widget' ) );

		add_action( 'personal_options', array( __CLASS__, 'add_syntax_highlighting_user_setting_field' ) );
		add_action( 'personal_options_update', array( __CLASS__, 'update_syntax_highlighting_user_setting' ) );
		add_action( 'edit_user_profile_update', array( __CLASS__, 'update_syntax_highlighting_user_setting' ) );
	}

	/**
	 * Register scripts.
	 *
	 * @param WP_Scripts $scripts Scripts.
	 */
	public static function register_scripts( WP_Scripts $scripts ) {
		$scripts->add( 'codemirror', plugins_url( 'wp-includes/js/codemirror/lib/codemirror.js', __FILE__ ), array(), self::CODEMIRROR_VERSION );

		$scripts->add( 'codemirror-addon-hint-show',       plugins_url( 'wp-includes/js/codemirror/addon/hint/show-hint.js', __FILE__ ),       array( 'codemirror' ), self::CODEMIRROR_VERSION );
		$scripts->add( 'codemirror-addon-hint-css',        plugins_url( 'wp-includes/js/codemirror/addon/hint/css-hint.js', __FILE__ ),        array( 'codemirror-addon-hint-show', 'codemirror-mode-css' ), self::CODEMIRROR_VERSION );
		$scripts->add( 'codemirror-addon-hint-html',       plugins_url( 'wp-includes/js/codemirror/addon/hint/html-hint.js', __FILE__ ),       array( 'codemirror-addon-hint-show', 'codemirror-mode-html' ), self::CODEMIRROR_VERSION );
		$scripts->add( 'codemirror-addon-hint-javascript', plugins_url( 'wp-includes/js/codemirror/addon/hint/javascript-hint.js', __FILE__ ), array( 'codemirror-addon-hint-show', 'codemirror-mode-javascript' ), self::CODEMIRROR_VERSION );
		$scripts->add( 'codemirror-addon-hint-sql',        plugins_url( 'wp-includes/js/codemirror/addon/hint/sql-hint.js', __FILE__ ),        array( 'codemirror-addon-hint-show', 'codemirror-mode-sql' ), self::CODEMIRROR_VERSION );
		$scripts->add( 'codemirror-addon-hint-xml',        plugins_url( 'wp-includes/js/codemirror/addon/hint/xml-hint.js', __FILE__ ),        array( 'codemirror-addon-hint-show', 'codemirror-mode-xml' ), self::CODEMIRROR_VERSION );

		// The linting engines for the lint addons...
		$scripts->add( 'csslint',  plugins_url( 'wp-includes/js/csslint.js', __FILE__ ), array(), self::VERSION );
		$scripts->add( 'htmlhint', plugins_url( 'wp-includes/js/htmlhint.js', __FILE__ ), array(), self::VERSION );
		$scripts->add( 'jshint',   plugins_url( 'wp-includes/js/htmlhint.js', __FILE__ ), array(), self::VERSION );
		$scripts->add( 'jsonlint', plugins_url( 'wp-includes/js/jsonlint.js', __FILE__ ), array(), self::VERSION );

		$scripts->add( 'codemirror-addon-lint',            plugins_url( 'wp-includes/js/codemirror/addon/lint/lint.js',      __FILE__ ),       array( 'codemirror' ),            self::CODEMIRROR_VERSION );
		$scripts->add( 'codemirror-addon-lint-css',        plugins_url( 'wp-includes/js/codemirror/addon/lint/css-lint.js',  __FILE__ ),       array( 'codemirror-addon-lint', 'csslint' ), self::CODEMIRROR_VERSION );
		$scripts->add( 'codemirror-addon-lint-html',       plugins_url( 'wp-includes/js/codemirror/addon/lint/html-lint.js', __FILE__ ),       array( 'codemirror-addon-lint', 'htmlhint' ), self::CODEMIRROR_VERSION );
		$scripts->add( 'codemirror-addon-lint-javascript', plugins_url( 'wp-includes/js/codemirror/addon/lint/javascript-lint.js', __FILE__ ), array( 'codemirror-addon-lint', 'jshint' ), self::CODEMIRROR_VERSION );
		$scripts->add( 'codemirror-addon-lint-json',       plugins_url( 'wp-includes/js/codemirror/addon/lint/json-lint.js', __FILE__ ),       array( 'codemirror-addon-lint', 'jsonlint' ), self::CODEMIRROR_VERSION );

		$scripts->add( 'codemirror-addon-comment',                 plugins_url( 'wp-includes/js/codemirror/addon/comment/comment.js', __FILE__ ),         array( 'codemirror' ), self::CODEMIRROR_VERSION );
		$scripts->add( 'codemirror-addon-comment-continuecomment', plugins_url( 'wp-includes/js/codemirror/addon/comment/continuecomment.js', __FILE__ ), array( 'codemirror' ), self::CODEMIRROR_VERSION );

		$scripts->add( 'codemirror-addon-edit-closebrackets', plugins_url( 'wp-includes/js/codemirror/addon/edit/closebrackets.js', __FILE__ ), array( 'codemirror' ), self::CODEMIRROR_VERSION );
		$scripts->add( 'codemirror-addon-edit-closetag',      plugins_url( 'wp-includes/js/codemirror/addon/edit/closetag.js', __FILE__ ),      array( 'codemirror' ), self::CODEMIRROR_VERSION );
		$scripts->add( 'codemirror-addon-edit-continuelist',  plugins_url( 'wp-includes/js/codemirror/addon/edit/continuelist.js', __FILE__ ),  array( 'codemirror' ), self::CODEMIRROR_VERSION );
		$scripts->add( 'codemirror-addon-edit-matchbrackets', plugins_url( 'wp-includes/js/codemirror/addon/edit/matchbrackets.js', __FILE__ ), array( 'codemirror' ), self::CODEMIRROR_VERSION );
		$scripts->add( 'codemirror-addon-edit-matchtags',     plugins_url( 'wp-includes/js/codemirror/addon/edit/matchtags.js', __FILE__ ),     array( 'codemirror' ), self::CODEMIRROR_VERSION );
		$scripts->add( 'codemirror-addon-edit-trailingspace', plugins_url( 'wp-includes/js/codemirror/addon/edit/trailingspace.js', __FILE__ ), array( 'codemirror' ), self::CODEMIRROR_VERSION );

		$scripts->add( 'codemirror-addon-selection-active-line',    plugins_url( 'wp-includes/js/codemirror/addon/selection/active-line.js', __FILE__ ),       array( 'codemirror' ), self::CODEMIRROR_VERSION );
		$scripts->add( 'codemirror-addon-selection-mark-selection', plugins_url( 'wp-includes/js/codemirror/addon/selection/mark-selection.js', __FILE__ ),    array( 'codemirror' ), self::CODEMIRROR_VERSION );
		$scripts->add( 'codemirror-addon-selection-pointer',        plugins_url( 'wp-includes/js/codemirror/addon/selection/selection-pointer.js', __FILE__ ), array( 'codemirror' ), self::CODEMIRROR_VERSION );

		$scripts->add( 'codemirror-mode-clike',      plugins_url( 'wp-includes/js/codemirror/mode/clike/clike.js', __FILE__ ),           array( 'codemirror' ), self::CODEMIRROR_VERSION );
		$scripts->add( 'codemirror-mode-css',        plugins_url( 'wp-includes/js/codemirror/mode/css/css.js', __FILE__ ),               array( 'codemirror', 'codemirror-addon-edit-matchbrackets' ), self::CODEMIRROR_VERSION );
		$scripts->add( 'codemirror-mode-diff',       plugins_url( 'wp-includes/js/codemirror/mode/diff/diff.js', __FILE__ ),             array( 'codemirror' ), self::CODEMIRROR_VERSION );
		$scripts->add( 'codemirror-mode-html',       plugins_url( 'wp-includes/js/codemirror/mode/htmlmixed/htmlmixed.js', __FILE__ ),   array( 'codemirror', 'codemirror-mode-css', 'codemirror-mode-javascript', 'codemirror-mode-xml' ), self::CODEMIRROR_VERSION );
		$scripts->add( 'codemirror-mode-http',       plugins_url( 'wp-includes/js/codemirror/mode/http/http.js', __FILE__ ),             array( 'codemirror' ), self::CODEMIRROR_VERSION );
		$scripts->add( 'codemirror-mode-javascript', plugins_url( 'wp-includes/js/codemirror/mode/javascript/javascript.js', __FILE__ ), array( 'codemirror' ), self::CODEMIRROR_VERSION );
		$scripts->add( 'codemirror-mode-markdown',   plugins_url( 'wp-includes/js/codemirror/mode/markdown/markdown.js', __FILE__ ),     array( 'codemirror' ), self::CODEMIRROR_VERSION );
		$scripts->add( 'codemirror-mode-php',        plugins_url( 'wp-includes/js/codemirror/mode/php/php.js', __FILE__ ),               array( 'codemirror-mode-clike', 'codemirror-addon-edit-matchbrackets' ), self::CODEMIRROR_VERSION );
		$scripts->add( 'codemirror-mode-shell',      plugins_url( 'wp-includes/js/codemirror/mode/shell/shell.js', __FILE__ ),           array( 'codemirror' ), self::CODEMIRROR_VERSION );
		$scripts->add( 'codemirror-mode-sql',        plugins_url( 'wp-includes/js/codemirror/mode/sql/sql.js', __FILE__ ),               array( 'codemirror' ), self::CODEMIRROR_VERSION );
		$scripts->add( 'codemirror-mode-xml',        plugins_url( 'wp-includes/js/codemirror/mode/xml/xml.js', __FILE__ ),               array( 'codemirror' ), self::CODEMIRROR_VERSION );

		$scripts->add( 'file-editor', plugins_url( 'wp-admin/js/file-editor.js', __FILE__ ), array( 'jquery', 'codemirror', 'jquery-ui-core' ), self::VERSION );

		$scripts->add( 'custom-html-widgets', plugins_url( 'wp-admin/js/widgets/custom-html-widgets.js', __FILE__ ), array( 'jquery', 'backbone', 'wp-util' ), self::VERSION );
		$options = array_merge( self::$options, array(
			'mode' => 'htmlmixed',
			'gutters' => array( 'CodeMirror-lint-markers' ),
			'lint' => true,
		) );
		$scripts->add_inline_script( 'custom-html-widgets', sprintf( 'wp.customHtmlWidgets.init( %s );', wp_json_encode( $options ) ), 'after' );
	}

	/**
	 * Register styles.
	 *
	 * @param WP_Styles $styles Styles.
	 */
	public static function register_styles( WP_Styles $styles ) {
		/*
		 * Override common.css with patched version that has proper styling for CodeMirror and textarea.
		 */
		$styles->registered['common']->src = plugins_url( 'wp-admin/css/common.css', __FILE__ );
		$styles->registered['common']->ver = self::VERSION;

		$styles->add( 'codemirror',                 plugins_url( 'wp-includes/js/codemirror/lib/codemirror.css', __FILE__ ),       array(),               self::CODEMIRROR_VERSION );
		$styles->add( 'codemirror-addon-show-hint', plugins_url( 'wp-includes/js/codemirror/addon/hint/show-hint.css', __FILE__ ), array( 'codemirror' ), self::CODEMIRROR_VERSION );
		$styles->add( 'codemirror-addon-lint',      plugins_url( 'wp-includes/js/codemirror/addon/lint/lint.css', __FILE__ ),      array( 'codemirror' ), self::CODEMIRROR_VERSION );

		// Patch the stylesheets.
		$styles->add_inline_style( 'widgets', file_get_contents( dirname( __FILE__ ) . '/wp-admin/css/widgets-addendum.css' ) );
		$styles->add_inline_style( 'customize-controls', file_get_contents( dirname( __FILE__ ) . '/wp-admin/css/customize-controls-addendum.css' ) );
	}

	/**
	 * Prepare CodeMirror for editing a given file.
	 *
	 * @param string $file File being edited.
	 */
	public static function prep_codemirror_for_file( $file ) {
		switch ( @pathinfo( $file, PATHINFO_EXTENSION ) ) {

			case 'css':
				wp_enqueue_script( 'codemirror-mode-css' );
				wp_enqueue_script( 'codemirror-addon-lint-css' );
				wp_enqueue_style( 'codemirror' );
				wp_enqueue_style( 'codemirror-addon-lint' );

				self::$options = array_merge( self::$options, array(
					'mode'    => 'text/css',
					'gutters' => array( 'CodeMirror-lint-markers' ),
					'lint'    => true,
				) );
				break;

			case 'php':
				wp_enqueue_script( 'codemirror-mode-html' );
				wp_enqueue_script( 'codemirror-mode-php' );
				wp_enqueue_style( 'codemirror' );

				self::$options['mode'] = 'application/x-httpd-php';
				break;

			case 'js':
				wp_enqueue_script( 'codemirror-mode-javascript' );
				wp_enqueue_script( 'codemirror-addon-lint-javascript' );
				wp_enqueue_style( 'codemirror' );
				wp_enqueue_style( 'codemirror-addon-lint' );

				self::$options = array(
					'mode'           => 'text/javascript',
					'gutters'        => array( 'CodeMirror-lint-markers' ),
					'lint'           => true,
				);
				break;

			case 'html':
				wp_enqueue_script( 'codemirror-mode-html' );
				wp_enqueue_style( 'codemirror' );

				self::$options['mode'] = 'text/html';
				break;

			case 'xml':
				wp_enqueue_script( 'codemirror-mode-xml' );
				wp_enqueue_style( 'codemirror' );

				self::$options['mode'] = 'application/xml';
				break;

			case 'txt':
			default:
				wp_enqueue_script( 'codemirror' );
				wp_enqueue_style( 'codemirror' );

				self::$options['mode'] = 'text/plain';
				break;
		}
	}

	/**
	 * Load theme editor config.
	 */
	public static function load_theme_editor_php() {
		global $file, $theme;

		if ( 'false' === wp_get_current_user()->syntax_highlighting ) {
			return;
		}

		wp_reset_vars( array( 'file', 'theme' ) );

		$stylesheet = $theme ? $theme : get_stylesheet();
		$wp_theme   = wp_get_theme( $stylesheet );

		if ( empty( $file ) ) {
			$file = 'style.css';
		}

		wp_enqueue_script( 'file-editor' );
		self::prep_codemirror_for_file( $file );

		/**
		 * Give folks a chance to filter the arguments passed to CodeMirror -- This will let them enable
		 * or disable it (by returning something that evaluates to false) as they choose as well.
		 *
		 * @param array    $options The array of options to be passed to CodeMirror. Falsey doesn't use CodeMirror.
		 * @param string   $file    The file being displayed.
		 * @param WP_Theme $theme   The WP_Theme object for the current theme being edited.
		 */
		$options = apply_filters( 'theme_editor_codemirror_opts', self::$options, $file, $wp_theme );

		wp_add_inline_script( 'file-editor', sprintf( 'var _wpCodeMirrorOptions = %s;', wp_json_encode( $options ) ) );
	}

	/**
	 * Load plugin editor PHP.
	 */
	public static function load_plugin_editor_php() {

		if ( 'false' === wp_get_current_user()->syntax_highlighting ) {
			return;
		}

		$file    = isset( $_REQUEST['file'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['file'] ) ) : '';
		$plugin  = isset( $_REQUEST['plugin'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['plugin'] ) ) : '';

		if ( empty( $plugin ) ) {
			$file_paths = array_keys( get_plugins() );
			$plugin = $file ? $file : array_shift( $file_paths );
		} elseif ( 0 !== validate_file( $plugin ) ) {
			wp_die( __( 'Sorry, that file cannot be edited.' ) );
		}

		$plugin_files = get_plugin_files( $plugin );

		if ( empty( $file ) ) {
			$file = $plugin_files[0];
		}

		$file = validate_file_to_edit( $file, $plugin_files );

		wp_enqueue_script( 'file-editor' );
		self::prep_codemirror_for_file( $file );

		/**
		 * Give folks a chance to filter the arguments passed to CodeMirror -- This will let them enable
		 * or disable it (by returning something that evaluates to false) as they choose as well.
		 *
		 * @param array   $options The array of options to be passed to CodeMirror. Falsey doesn't use CodeMirror.
		 * @param string  $file    The file being displayed.
		 * @param string  $plugin  The plugin slug for the file being edited.
		 */
		$options = apply_filters( 'plugin_editor_codemirror_opts', self::$options, $file, $plugin );

		wp_add_inline_script( 'file-editor', sprintf( 'var _wpCodeMirrorOptions = %s;', wp_json_encode( $options ) ) );
	}

	/**
	 * Enqueue assets for Customizer.
	 */
	public static function customize_controls_enqueue_scripts() {
		wp_enqueue_script( 'codemirror-mode-css' );
		wp_enqueue_script( 'codemirror-addon-lint-css' );
		wp_enqueue_style( 'codemirror' );
		wp_enqueue_style( 'codemirror-addon-lint' );

		wp_add_inline_script( 'customize-controls', file_get_contents( dirname( __FILE__ ) . '/wp-admin/js/customize-controls-addendum.js' ) );
	}

	/**
	 * Amend help text for Custom CSS.
	 *
	 * @param WP_Customize_Manager $wp_customize Manager.
	 */
	public static function amend_custom_css_help_text( WP_Customize_Manager $wp_customize ) {
		if ( 'false' === wp_get_current_user()->syntax_highlighting ) {
			return;
		}

		$section = $wp_customize->get_section( 'custom_css' );
		if ( ! $section ) {
			return;
		}

		$section->description = sprintf( '%s<br /><a href="%s" class="external-link" target="_blank">%s<span class="screen-reader-text">%s</span></a>',
			sprintf(
				/* translators: placeholder is profile URL */
				__( 'CSS allows you to customize the appearance and layout of your site with code. Separate CSS is saved for each of your themes. In the editing area the Tab key enters a tab character. To move keyboard focus to another element, press the Esc key followed by the Tab key for the next element or Shift+Tab key for the previous element. You can disable the code syntax highlighter in your <a href="%s" target="blank" class="external-link">user profile</a>. This will allow you to work in plain text mode.' ),
				esc_url( get_edit_profile_url() . '#syntax_highlighting' )
			),
			esc_url( __( 'https://codex.wordpress.org/CSS' ) ),
			__( 'Learn more about CSS' ),
			/* translators: accessibility text */
			__( '(opens in a new window)' )
		);
	}

	/**
	 * Add Customizer integration.
	 *
	 * @see WP_Customize_Manager::customize_pane_settings()
	 */
	public static function amend_customize_pane_settings() {

		if ( 'false' === wp_get_current_user()->syntax_highlighting ) {
			return;
		}

		$options = apply_filters( 'customizer_custom_css_codemirror_opts', array_merge( self::$options, array(
			'mode'    => 'text/css',
			'gutters' => array( 'CodeMirror-lint-markers' ),
			'lint'    => true,
		) ) );

		if ( $options ) {
			printf( '<script>window._wpCustomizeSettings.codeMirror = %s</script>;', wp_json_encode( $options ) );
		}
	}

	/**
	 * Replace Custom HTML widget with CodeMirror Custom HTML Widget.
	 */
	public static function register_custom_html_widget() {
		if ( class_exists( 'WP_Widget_Custom_HTML' ) ) {
			require_once dirname( __FILE__ ) . '/wp-includes/widgets/class-wp-widget-custom-html-codemirror.php';
			unregister_widget( 'WP_Widget_Custom_HTML' );
			register_widget( 'WP_Widget_Custom_HTML_CodeMirror' );
		}
	}

	/**
	 * Render the toggle to disable CodeMirror for the current user.
	 *
	 * @param WP_User $profileuser Current user being edited.
	 */
	public static function add_syntax_highlighting_user_setting_field( $profileuser ) {
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
			<th scope="row"><?php _e( 'Syntax Highlighting' ); ?></th>
			<td>
				<label for="syntax_highlighting"><input name="syntax_highlighting" type="checkbox" id="syntax_highlighting" value="false" <?php checked( 'false', $profileuser->syntax_highlighting ); ?> /> <?php _e( 'Disable syntax highlighting when editing code' ); ?></label>
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

	/**
	 * Update the syntax_highlighting user setting.
	 *
	 * @param int $user_id User being edited.
	 */
	public function update_syntax_highlighting_user_setting( $user_id ) {
		if ( ! current_user_can( 'edit_user', $user_id ) ) {
			return;
		}
		update_user_meta( $user_id, 'syntax_highlighting', isset( $_POST['syntax_highlighting'] ) && 'false' === $_POST['syntax_highlighting'] ? 'false' : 'true' );
	}
}

Better_Code_Editing_Plugin::go();
