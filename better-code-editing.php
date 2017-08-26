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
	 * @var array
	 */
	static $default_settings = array(
		'codemirror' => array(
			'indentUnit' => 4,
			'indentWithTabs' => true,
			'inputStyle' => 'contenteditable',
			'lineNumbers' => true,
			'lineWrapping' => true,
		),
		'csslint' => array(
			'rules' => array(
				'errors' => true, // Parsing errors.
				'box-model' => true,
				'display-property-grouping' => true,
				'duplicate-properties' => true,
				'empty-rules' => true,
				'known-properties' => true,
				'outline-none' => true,
			),
		),
		'jshint' => array(
			'rules' => array(
				// The following are copied from <https://github.com/WordPress/wordpress-develop/blob/4.8.1/.jshintrc>.
				'boss' => true,
				'curly' => true,
				'eqeqeq' => true,
				'eqnull' => true,
				'es3' => true,
				'expr' => true,
				'immed' => true,
				'noarg' => true,
				'nonbsp' => true,
				'onevar' => true,
				'quotmark' => 'single',
				'trailing' => true,
				'undef' => true,
				'unused' => true,

				'browser' => true,

				'globals' => array(
					'_' => false,
					'Backbone' => false,
					'jQuery' => false,
					'JSON' => false,
					'wp' => false,
				),
			),
		),
	);

	/**
	 * Add hooks.
	 */
	public static function go() {
		add_action( 'wp_default_scripts', array( __CLASS__, 'register_scripts' ) );
		add_action( 'wp_default_styles', array( __CLASS__, 'register_styles' ) );
		add_action( 'admin_enqueue_scripts', array( __CLASS__, 'admin_enqueue_scripts_for_file_editor' ) );
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
		$scripts->add( 'csslint',  plugins_url( 'wp-includes/js/csslint.js', __FILE__ ), array(), self::VERSION ); // @todo Version like '1.0.3'.
		$scripts->add( 'htmlhint', plugins_url( 'wp-includes/js/htmlhint.js', __FILE__ ), array(), self::VERSION ); // @todo Version like '0.9.13'.
		$scripts->add( 'jshint',   plugins_url( 'wp-includes/js/jshint.js', __FILE__ ), array(), self::VERSION );
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

		$scripts->add( 'code-editor', plugins_url( 'wp-admin/js/code-editor.js', __FILE__ ), array( 'jquery', 'codemirror' ), self::VERSION );
		$scripts->add_inline_script( 'code-editor', sprintf( 'jQuery.extend( wp.codeEditor.defaultSettings, %s );', wp_json_encode( self::$default_settings ) ) );

		$scripts->add( 'file-editor', plugins_url( 'wp-admin/js/file-editor.js', __FILE__ ), array( 'jquery', 'codemirror', 'jquery-ui-core' ), self::VERSION );

		$scripts->add( 'custom-html-widgets', plugins_url( 'wp-admin/js/widgets/custom-html-widgets.js', __FILE__ ), array( 'code-editor', 'jquery', 'backbone', 'wp-util' ), self::VERSION );
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
	 * Get settings for initializing the code editor.
	 *
	 * @param array $context Context.
	 * @return array|false Settings for code editor or false if disabled.
	 */
	public static function get_settings( $context ) {

		if ( is_user_logged_in() && 'false' === wp_get_current_user()->syntax_highlighting ) {
			return false;
		}

		$settings = self::$default_settings;
		$type = '';
		$extension = '';
		if ( isset( $context['file'] ) && false !== strpos( basename( $context['file'] ), '.' ) ) {
			$extension = strtolower( pathinfo( $context['file'], PATHINFO_EXTENSION ) );
			if ( ! empty( $extension ) ) {
				foreach ( wp_get_mime_types() as $exts => $mime ) {
					if ( preg_match( '!^(' . $exts . ')$!i', $extension ) ) {
						$type = $mime;
						break;
					}
				}
			}
		}

		if ( 'text/css' === $type || in_array( $extension, array( 'sass', 'scss', 'less' ), true ) ) {
			$settings['codemirror'] = array_merge( $settings['codemirror'], array(
				'mode' => 'text/css',
				'gutters' => array( 'CodeMirror-lint-markers' ),
				'lint' => true,
			) );
		} elseif ( in_array( $extension, array( 'php', 'phtml', 'php3', 'php4', 'php5', 'php7', 'phps' ), true ) ) {
			$settings['codemirror']['mode'] = 'application/x-httpd-php';
			$settings['codemirror'] = array_merge( $settings['codemirror'], array(
				'gutters' => array( 'CodeMirror-lint-markers' ),
				'lint' => true,
			) );
		} elseif ( 'application/javascript' === $type ) {
			$settings['codemirror'] = array_merge( $settings['codemirror'], array(
				'mode' => 'text/javascript',
				'gutters' => array( 'CodeMirror-lint-markers' ),
				'lint' => true,
			) );
		} elseif ( 'text/html' === $type ) {
			$settings['codemirror'] = array_merge( $settings['codemirror'], array(
				'mode' => 'htmlmixed',
				'gutters' => array( 'CodeMirror-lint-markers' ),
				'lint' => true,
			) );
		} elseif ( false !== strpos( $type, 'xml' ) || in_array( $extension, array( 'xml', 'svg' ), true ) ) {
			$settings['codemirror']['mode'] = 'application/xml';
		} else {
			$settings['codemirror']['mode'] = 'text/plain';
		}

		/**
		 * Filters settings that are passed into the code editor.
		 *
		 * Returning a falsey value will disable the syntax-highlighting code editor.
		 *
		 * @since 4.9.0
		 *
		 * @param array $settings The array of settings passed to the code editor. A falsey value disables the editor.
		 * @param array $context  {
		 *     Context for where the editor will appear.
		 *
		 *     @type string    $file   File being edited.
		 *     @type WP_Theme  $theme  Theme being edited when on theme editor.
		 *     @type string    $plugin Plugin being edited when on plugin editor.
		 * }
		 */
		$settings = apply_filters( 'wp_code_editor_settings', $settings, $context );

		return $settings;
	}

	/**
	 * Enqueue assets needed by the code editor for the given settings.
	 *
	 * @see Better_Code_Editing_Plugin::get_settings()
	 * @param array|false $settings Code editor settings.
	 * @returns boolean Whether assets were enqueued.
	 */
	public static function enqueue_assets( $settings ) {
		if ( empty( $settings ) || empty( $settings['codemirror'] ) ) {
			return false;
		}

		wp_enqueue_script( 'codemirror' );
		wp_enqueue_style( 'codemirror' );
		if ( isset( $settings['codemirror']['mode'] ) ) {
			switch ( $settings['codemirror']['mode'] ) {
				case 'application/x-httpd-php':
					wp_enqueue_script( 'codemirror-mode-html' );
					wp_enqueue_script( 'codemirror-mode-php' );
					wp_enqueue_script( 'codemirror-mode-javascript' );
					wp_enqueue_script( 'codemirror-mode-css' );
					break;
				case 'htmlmixed':
					wp_enqueue_script( 'codemirror-mode-html' );

					if ( ! empty( $settings['codemirror']['lint'] ) ) {
						wp_enqueue_script( 'codemirror-addon-lint-html' );
					}
					break;
				case 'text/javascript':
					wp_enqueue_script( 'codemirror-mode-javascript' );

					if ( ! empty( $settings['codemirror']['lint'] ) ) {
						wp_enqueue_script( 'jshint' );
						wp_enqueue_script( 'codemirror-addon-lint-javascript' );
					}
					break;
				case 'application/xml':
					wp_enqueue_script( 'codemirror-mode-xml' );
					break;
				case 'text/css':
					wp_enqueue_script( 'codemirror-mode-css' );

					if ( ! empty( $settings['codemirror']['lint'] ) ) {
						wp_enqueue_script( 'codemirror-addon-lint-css' );
					}
					break;
			}

			if ( ! empty( $settings['codemirror']['lint'] ) ) {
				wp_enqueue_style( 'codemirror-addon-show-hint' );
				wp_enqueue_style( 'codemirror-addon-lint' );
			}
		}
		return true;
	}

	/**
	 * Enqueue scripts for theme and plugin editors.
	 *
	 * @param string $hook Hook.
	 */
	public static function admin_enqueue_scripts_for_file_editor( $hook ) {

		if ( 'theme-editor.php' !== $hook && 'plugin-editor.php' !== $hook ) {
			return;
		}

		$context = array();

		if ( 'theme-editor.php' === $hook ) {
			$theme = wp_get_theme( isset( $_REQUEST['theme'] ) ? wp_unslash( $_REQUEST['theme'] ) : get_stylesheet() );
			if ( $theme->errors() ) {
				wp_die( $theme->errors()->get_error_message() );
			}

			if ( isset( $_REQUEST['file'] ) ) {
				$context['file'] = sanitize_text_field( wp_unslash( $_REQUEST['file'] ) );
			} else {
				$context['file'] = 'style.css';
			}
		} elseif ( 'plugin-editor.php' === $hook ) {
			$context['plugin'] = isset( $_REQUEST['plugin'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['plugin'] ) ) : '';
			$context['file'] = isset( $_REQUEST['file'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['file'] ) ) : '';

			if ( empty( $context['plugin'] ) ) {
				$file_paths = array_keys( get_plugins() );
				$context['plugin'] = $context['file'] ? $context['file'] : array_shift( $file_paths );
			} elseif ( 0 !== validate_file( $context['plugin'] ) ) {
				wp_die( __( 'Sorry, that file cannot be edited.' ) );
			}

			$plugin_files = get_plugin_files( $context['plugin'] );
			if ( empty( $context['file'] ) ) {
				$context['file'] = $plugin_files[0];
			}

			$context['file'] = validate_file_to_edit( $context['file'], $plugin_files );
		}

		$settings = self::get_settings( $context );
		if ( empty( $settings ) ) {
			return;
		}

		wp_enqueue_script( 'jquery-ui-core' ); // For :tabbable pseudo-selector.
		self::enqueue_assets( $settings );
		wp_enqueue_script( 'code-editor' );

		ob_start();
		?>
		<script>
		jQuery( function( $ ) {
			var settings = {};
			settings = <?php echo wp_json_encode( $settings ); ?>;
			settings.handleTabPrev = function() {
				$( '#templateside' ).find( ':tabbable' ).last().focus();
			};
			settings.handleTabNext = function() {
				$( '#template' ).find( ':tabbable:not(.CodeMirror-code)' ).first().focus();
			};
			wp.codeEditor.initialize( $( '#newcontent' ), settings );
		} );
		</script>
		<?php
		wp_add_inline_script( 'code-editor', str_replace( array( '<script>', '</script>' ), '', ob_get_clean() ) );
	}

	/**
	 * Code editor settings for Custom CSS.
	 *
	 * @var array
	 */
	public static $custom_css_code_editor_settings;

	/**
	 * Set up code editor for Custom CSS.
	 *
	 * @param WP_Customize_Manager $wp_customize Manager.
	 */
	public static function amend_custom_css_help_text( WP_Customize_Manager $wp_customize ) {
		self::$custom_css_code_editor_settings = self::get_settings( array(
			'file' => 'custom.css',
		) );

		if ( empty( self::$custom_css_code_editor_settings ) ) {
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
	 * Enqueue assets for Customizer.
	 */
	public static function customize_controls_enqueue_scripts() {
		if ( ! empty( self::$custom_css_code_editor_settings ) ) {
			self::enqueue_assets( self::$custom_css_code_editor_settings );
		}
		wp_add_inline_script( 'customize-controls', file_get_contents( dirname( __FILE__ ) . '/wp-admin/js/customize-controls-addendum.js' ) );
	}

	/**
	 * Add Customizer integration.
	 *
	 * @see WP_Customize_Manager::customize_pane_settings()
	 */
	public static function amend_customize_pane_settings() {
		if ( ! empty( self::$custom_css_code_editor_settings ) ) {
			printf( '<script>window._wpCustomizeSettings.codeEditor = %s</script>;', wp_json_encode( self::$custom_css_code_editor_settings ) );
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
