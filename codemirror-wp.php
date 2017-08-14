<?php
/**
 * Plugin Name: CodeMirror WP
 * Plugin URI: https://wordpress.org/plugins/codemirror-wp/
 * Description: Code highlighting and linting, powered by CodeMirror.
 * Version: 0.1.0
 * Author: The WordPress Team
 * Text Domain: codemirror-wp
 *
 * @package WordPress
 */

/**
 * Plugin class.
 */
class CodeMirror_WP {

	/**
	 * CodeMirror version.
	 */
	const VERSION = '0.1.0';

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
	);

	/**
	 * Add hooks.
	 */
	public static function go() {
		add_action( 'admin_init', array( __CLASS__, 'register_scripts' ) );
		add_action( 'admin_init', array( __CLASS__, 'register_styles' ) );
		add_action( 'load-theme-editor.php', array( __CLASS__, 'load_theme_editor_php' ) );
		add_action( 'load-plugin-editor.php', array( __CLASS__, 'load_plugin_editor_php' ) );
		add_action( 'customize_controls_enqueue_scripts', array( __CLASS__, 'customize_controls_enqueue_scripts' ) );
		add_action( 'widgets_init', array( __CLASS__, 'register_custom_html_widget' ) );
	}

	/**
	 * Register scripts.
	 */
	public static function register_scripts() {
		wp_register_script( 'codemirror', plugins_url( 'wp-includes/js/codemirror/lib/codemirror.js', __FILE__ ), array(), self::CODEMIRROR_VERSION );

		wp_register_script( 'codemirror-addon-hint-show',       plugins_url( 'wp-includes/js/codemirror/addon/hint/show-hint.js', __FILE__ ),       array( 'codemirror' ), self::CODEMIRROR_VERSION );
		wp_register_script( 'codemirror-addon-hint-css',        plugins_url( 'wp-includes/js/codemirror/addon/hint/css-hint.js', __FILE__ ),        array( 'codemirror-addon-hint-show', 'codemirror-mode-css' ), self::CODEMIRROR_VERSION );
		wp_register_script( 'codemirror-addon-hint-html',       plugins_url( 'wp-includes/js/codemirror/addon/hint/html-hint.js', __FILE__ ),       array( 'codemirror-addon-hint-show', 'codemirror-mode-html' ), self::CODEMIRROR_VERSION );
		wp_register_script( 'codemirror-addon-hint-javascript', plugins_url( 'wp-includes/js/codemirror/addon/hint/javascript-hint.js', __FILE__ ), array( 'codemirror-addon-hint-show', 'codemirror-mode-javascript' ), self::CODEMIRROR_VERSION );
		wp_register_script( 'codemirror-addon-hint-sql',        plugins_url( 'wp-includes/js/codemirror/addon/hint/sql-hint.js', __FILE__ ),        array( 'codemirror-addon-hint-show', 'codemirror-mode-sql' ), self::CODEMIRROR_VERSION );
		wp_register_script( 'codemirror-addon-hint-xml',        plugins_url( 'wp-includes/js/codemirror/addon/hint/xml-hint.js', __FILE__ ),        array( 'codemirror-addon-hint-show', 'codemirror-mode-xml' ), self::CODEMIRROR_VERSION );

		// The linting engines for the lint addons...
		wp_register_script( 'csslint',  plugins_url( 'wp-includes/js/csslint.js', __FILE__ ), array(), self::VERSION );
		wp_register_script( 'htmlhint', plugins_url( 'wp-includes/js/htmlhint.js', __FILE__ ), array(), self::VERSION );
		wp_register_script( 'jshint',   plugins_url( 'wp-includes/js/htmlhint.js', __FILE__ ), array(), self::VERSION );
		wp_register_script( 'jsonlint', plugins_url( 'wp-includes/js/jsonlint.js', __FILE__ ), array(), self::VERSION );

		wp_register_script( 'codemirror-addon-lint',            plugins_url( 'wp-includes/js/codemirror/addon/lint/lint.js',      __FILE__ ),       array( 'codemirror' ),            self::CODEMIRROR_VERSION );
		wp_register_script( 'codemirror-addon-lint-css',        plugins_url( 'wp-includes/js/codemirror/addon/lint/css-lint.js',  __FILE__ ),       array( 'codemirror-addon-lint', 'csslint' ), self::CODEMIRROR_VERSION );
		wp_register_script( 'codemirror-addon-lint-html',       plugins_url( 'wp-includes/js/codemirror/addon/lint/html-lint.js', __FILE__ ),       array( 'codemirror-addon-lint', 'htmlhint' ), self::CODEMIRROR_VERSION );
		wp_register_script( 'codemirror-addon-lint-javascript', plugins_url( 'wp-includes/js/codemirror/addon/lint/javascript-lint.js', __FILE__ ), array( 'codemirror-addon-lint', 'jshint' ), self::CODEMIRROR_VERSION );
		wp_register_script( 'codemirror-addon-lint-json',       plugins_url( 'wp-includes/js/codemirror/addon/lint/json-lint.js', __FILE__ ),       array( 'codemirror-addon-lint', 'jsonlint' ), self::CODEMIRROR_VERSION );

		wp_register_script( 'codemirror-addon-comment',                 plugins_url( 'wp-includes/js/codemirror/addon/comment/comment.js', __FILE__ ),         array( 'codemirror' ), self::CODEMIRROR_VERSION );
		wp_register_script( 'codemirror-addon-comment-continuecomment', plugins_url( 'wp-includes/js/codemirror/addon/comment/continuecomment.js', __FILE__ ), array( 'codemirror' ), self::CODEMIRROR_VERSION );

		wp_register_script( 'codemirror-addon-edit-closebrackets', plugins_url( 'wp-includes/js/codemirror/addon/edit/closebrackets.js', __FILE__ ), array( 'codemirror' ), self::CODEMIRROR_VERSION );
		wp_register_script( 'codemirror-addon-edit-closetag',      plugins_url( 'wp-includes/js/codemirror/addon/edit/closetag.js', __FILE__ ),      array( 'codemirror' ), self::CODEMIRROR_VERSION );
		wp_register_script( 'codemirror-addon-edit-continuelist',  plugins_url( 'wp-includes/js/codemirror/addon/edit/continuelist.js', __FILE__ ),  array( 'codemirror' ), self::CODEMIRROR_VERSION );
		wp_register_script( 'codemirror-addon-edit-matchbrackets', plugins_url( 'wp-includes/js/codemirror/addon/edit/matchbrackets.js', __FILE__ ), array( 'codemirror' ), self::CODEMIRROR_VERSION );
		wp_register_script( 'codemirror-addon-edit-matchtags',     plugins_url( 'wp-includes/js/codemirror/addon/edit/matchtags.js', __FILE__ ),     array( 'codemirror' ), self::CODEMIRROR_VERSION );
		wp_register_script( 'codemirror-addon-edit-trailingspace', plugins_url( 'wp-includes/js/codemirror/addon/edit/trailingspace.js', __FILE__ ), array( 'codemirror' ), self::CODEMIRROR_VERSION );

		wp_register_script( 'codemirror-addon-selection-active-line',    plugins_url( 'wp-includes/js/codemirror/addon/selection/active-line.js', __FILE__ ),       array( 'codemirror' ), self::CODEMIRROR_VERSION );
		wp_register_script( 'codemirror-addon-selection-mark-selection', plugins_url( 'wp-includes/js/codemirror/addon/selection/mark-selection.js', __FILE__ ),    array( 'codemirror' ), self::CODEMIRROR_VERSION );
		wp_register_script( 'codemirror-addon-selection-pointer',        plugins_url( 'wp-includes/js/codemirror/addon/selection/selection-pointer.js', __FILE__ ), array( 'codemirror' ), self::CODEMIRROR_VERSION );

		wp_register_script( 'codemirror-mode-clike',      plugins_url( 'wp-includes/js/codemirror/mode/clike/clike.js', __FILE__ ),           array( 'codemirror' ), self::CODEMIRROR_VERSION );
		wp_register_script( 'codemirror-mode-css',        plugins_url( 'wp-includes/js/codemirror/mode/css/css.js', __FILE__ ),               array( 'codemirror', 'codemirror-addon-edit-matchbrackets' ), self::CODEMIRROR_VERSION );
		wp_register_script( 'codemirror-mode-diff',       plugins_url( 'wp-includes/js/codemirror/mode/diff/diff.js', __FILE__ ),             array( 'codemirror' ), self::CODEMIRROR_VERSION );
		wp_register_script( 'codemirror-mode-html',       plugins_url( 'wp-includes/js/codemirror/mode/htmlmixed/htmlmixed.js', __FILE__ ),   array( 'codemirror', 'codemirror-mode-css', 'codemirror-mode-javascript', 'codemirror-mode-xml' ), self::CODEMIRROR_VERSION );
		wp_register_script( 'codemirror-mode-http',       plugins_url( 'wp-includes/js/codemirror/mode/http/http.js', __FILE__ ),             array( 'codemirror' ), self::CODEMIRROR_VERSION );
		wp_register_script( 'codemirror-mode-javascript', plugins_url( 'wp-includes/js/codemirror/mode/javascript/javascript.js', __FILE__ ), array( 'codemirror' ), self::CODEMIRROR_VERSION );
		wp_register_script( 'codemirror-mode-markdown',   plugins_url( 'wp-includes/js/codemirror/mode/markdown/markdown.js', __FILE__ ),     array( 'codemirror' ), self::CODEMIRROR_VERSION );
		wp_register_script( 'codemirror-mode-php',        plugins_url( 'wp-includes/js/codemirror/mode/php/php.js', __FILE__ ),               array( 'codemirror-mode-clike', 'codemirror-addon-edit-matchbrackets' ), self::CODEMIRROR_VERSION );
		wp_register_script( 'codemirror-mode-shell',      plugins_url( 'wp-includes/js/codemirror/mode/shell/shell.js', __FILE__ ),           array( 'codemirror' ), self::CODEMIRROR_VERSION );
		wp_register_script( 'codemirror-mode-sql',        plugins_url( 'wp-includes/js/codemirror/mode/sql/sql.js', __FILE__ ),               array( 'codemirror' ), self::CODEMIRROR_VERSION );
		wp_register_script( 'codemirror-mode-xml',        plugins_url( 'wp-includes/js/codemirror/mode/xml/xml.js', __FILE__ ),               array( 'codemirror' ), self::CODEMIRROR_VERSION );

		wp_register_script( 'custom-html-widgets', plugins_url( 'wp-admin/js/widgets/custom-html-widgets.js', __FILE__ ), array( 'jquery', 'backbone', 'wp-util', 'codemirror-mode-html', 'codemirror-addon-lint-html' ), self::VERSION );
		$options = array_merge( self::$options, array(
			'mode' => 'htmlmixed',
			'gutters' => array( 'CodeMirror-lint-markers' ),
			'lint' => true,
		) );
		wp_scripts()->add_inline_script( 'custom-html-widgets', sprintf( 'wp.customHtmlWidgets.init( %s );', wp_json_encode( $options ) ), 'after' );

		// Patch the stylesheet.
		wp_styles()->add_inline_style( 'widgets', file_get_contents( dirname( __FILE__ ) . '/wp-admin/css/widgets.css' ) );
	}

	/**
	 * Register styles.
	 */
	public static function register_styles() {
		wp_register_style( 'codemirror',                 plugins_url( 'wp-includes/js/codemirror/lib/codemirror.css', __FILE__ ),       array(),               self::CODEMIRROR_VERSION );
		wp_register_style( 'codemirror-addon-show-hint', plugins_url( 'wp-includes/js/codemirror/addon/hint/show-hint.css', __FILE__ ), array( 'codemirror' ), self::CODEMIRROR_VERSION );
		wp_register_style( 'codemirror-addon-lint',      plugins_url( 'wp-includes/js/codemirror/addon/lint/lint.css', __FILE__ ),      array( 'codemirror' ), self::CODEMIRROR_VERSION );
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

		wp_reset_vars( array( 'file', 'theme' ) );

		$stylesheet = $theme ? $theme : get_stylesheet();
		$wp_theme   = wp_get_theme( $stylesheet );

		if ( empty( $file ) ) {
			$file = 'style.css';
		}

		self::prep_codemirror_for_file( $file );

		/**
		 * Give folks a chance to filter the arguments passed to CodeMirror -- This will let them enable
		 * or disable it (by returning something that evaluates to false) as they choose as well.
		 *
		 * @param array    $options The array of options to be passed to CodeMirror. Falsey doesn't use CodeMirror.
		 * @param string   $file    The file being displayed.
		 * @param WP_Theme $theme   The WP_Theme object for the current theme being edited.
		 */
		self::$options = apply_filters( 'theme_editor_codemirror_opts', self::$options, $file, $wp_theme );

		if ( self::$options ) {
			add_action( 'admin_footer-theme-editor.php', array( __CLASS__, 'do_codemirror_admin_editor' ) );
		}
	}

	/**
	 * Load plugin editor PHP.
	 */
	public static function load_plugin_editor_php() {
		$file    = isset( $_REQUEST['file'] ) ? sanitize_text_field( $_REQUEST['file'] ) : '';
		$plugin  = isset( $_REQUEST['plugin'] ) ? sanitize_text_field( $_REQUEST['plugin'] ) : '';

		if ( empty( $plugin ) ) {
			$file_paths = array_keys( get_plugins() );
			$plugin = $file ? $file : array_shift( $file_paths );
		}

		$plugin_files = get_plugin_files( $plugin );

		if ( empty( $file ) ) {
			$file = $plugin_files[0];
		}

		$file = validate_file_to_edit( $file, $plugin_files );

		self::prep_codemirror_for_file( $file );

		/**
		 * Give folks a chance to filter the arguments passed to CodeMirror -- This will let them enable
		 * or disable it (by returning something that evaluates to false) as they choose as well.
		 *
		 * @param array    $options The array of options to be passed to CodeMirror. Falsey doesn't use CodeMirror.
		 * @param string   $file    The file being displayed.
		 * @param WP_Theme $theme   The WP_Theme object for the current theme being edited.
		 */
		self::$options = apply_filters( 'plugin_editor_codemirror_opts', self::$options, $file, $plugin );

		if ( self::$options ) {
			add_action( 'admin_footer-plugin-editor.php', array( __CLASS__, 'do_codemirror_admin_editor' ) );
		}
	}

	/**
	 * Integrate with admin editor.
	 */
	public static function do_codemirror_admin_editor() {
		?>
		<style>
		#template div {
			margin-right: 0;
		}
		#template > div {
			margin-right: 190px;
		}
		@media screen and (max-width: 782px) {
			#template > div {
				margin-right: 0;
			}
		}
		.CodeMirror {
			height: calc( 100vh - 220px );
		}
		</style>
		<script>
		jQuery( function() {
			wp.codemirror = window.CodeMirror.fromTextArea( document.getElementById( 'newcontent' ), <?php echo json_encode( self::$options ); ?> );
		} );
		</script>
		<?php
	}

	/**
	 * Enqueue assets for Customizer.
	 */
	public static function customize_controls_enqueue_scripts() {
		wp_enqueue_script( 'codemirror-mode-css' );
		wp_enqueue_script( 'codemirror-addon-lint-css' );
		wp_enqueue_style( 'codemirror' );
		wp_enqueue_style( 'codemirror-addon-lint' );

		self::$options = apply_filters( 'customizer_custom_css_codemirror_opts', array_merge( self::$options, array(
			'mode'    => 'text/css',
			'gutters' => array( 'CodeMirror-lint-markers' ),
			'lint'    => true,
		) ) );

		add_action( 'customize_controls_print_footer_scripts', array( __CLASS__, 'customize_controls_print_footer_scripts' ) );
	}

	/**
	 * Add Customizer integration.
	 */
	public static function customize_controls_print_footer_scripts() {
		?>
		<style>
		#customize-control-custom_css .CodeMirror {
			height: calc( 100vh - 185px );
		}
		.CodeMirror-lint-tooltip {
			z-index: 500000;
		}
		</style>
		<script>
			wp.customize.section( 'custom_css', function( section ) {
				wp.customize.control( 'custom_css', function( control ) {
					var onceExpanded, onExpandedChange;

					// Workaround for disabling server-sent syntax checking notifications.
					// @todo Listen for errors in CodeMirror and opt-to add invalidity notifications for them? The presence of such notification error allows saving to be blocked.
					control.setting.notifications.add = (function( originalAdd ) {
						return function( id, notification ) {
							if ( 'imbalanced_curly_brackets' === id && notification.fromServer ) {
								return null;
							} else {
								return originalAdd( id, notification );
							}
						};
					})( control.setting.notifications );

					onceExpanded = function() {
						var $textarea = control.container.find( 'textarea' );

						wp.codemirror = window.CodeMirror.fromTextArea( $textarea[0], <?php echo json_encode( self::$options ); ?> );

						// Refresh when receiving focus.
						wp.codemirror.on( 'focus', function( editor ) {
							editor.refresh();
						} );

						/*
						 * When the CodeMirror instance changes, mirror to the textarea,
						 * where we have our "true" change event handler bound.
						 */
						wp.codemirror.on( 'change', function( editor ) {
							$textarea.val( editor.getValue() ).trigger( 'change' );
						} );

						// To do: bind something to setting change, so that we can catch other plugins modifying the css and update CodeMirror?
					};

					onExpandedChange = function( isExpanded ) {
						if ( isExpanded ) {
							onceExpanded();
							section.expanded.unbind( onExpandedChange );
						}
					};
					control.deferred.embedded.done( function() {
						if ( section.expanded() ) {
							onceExpanded();
						} else {
							section.expanded.bind( onExpandedChange );
						}
					});
				});
			});
		</script>
		<?php
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
}

CodeMirror_WP::go();
