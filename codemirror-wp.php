<?php
/**
 * Plugin Name: CodeMirror WP
 */

class CodeMirror_WP {
	static $codemirror_opts = false;
	const CODEMIRROR_VERSION = '5.26.0';

	public static function go() {
		add_action( 'admin_init', array( __CLASS__, 'register_scripts' ) );
		add_action( 'admin_init', array( __CLASS__, 'register_styles' ) );
		add_action( 'load-theme-editor.php', array( __CLASS__, 'load_theme_editor_php' ) );
		add_action( 'load-plugin-editor.php', array( __CLASS__, 'load_plugin_editor_php' ) );
		add_action( 'customize_controls_enqueue_scripts', array( __CLASS__, 'customize_controls_enqueue_scripts' ) );
	}

	public static function register_scripts() {
		wp_register_script( 'codemirror', plugins_url( "CodeMirror/lib/codemirror.js", __FILE__ ), array(), SELF::CODEMIRROR_VERSION );

		wp_register_script( 'codemirror-addon-hint-show',       plugins_url( "CodeMirror/addon/hint/show-hint.js", __FILE__ ),       array( 'codemirror' ), SELF::CODEMIRROR_VERSION );
		wp_register_script( 'codemirror-addon-hint-css',        plugins_url( "CodeMirror/addon/hint/css-hint.js", __FILE__ ),        array( 'codemirror-addon-hint-show', 'codemirror-mode-css' ), SELF::CODEMIRROR_VERSION );
		wp_register_script( 'codemirror-addon-hint-html',       plugins_url( "CodeMirror/addon/hint/html-hint.js", __FILE__ ),       array( 'codemirror-addon-hint-show', 'codemirror-mode-html' ), SELF::CODEMIRROR_VERSION );
		wp_register_script( 'codemirror-addon-hint-javascript', plugins_url( "CodeMirror/addon/hint/javascript-hint.js", __FILE__ ), array( 'codemirror-addon-hint-show', 'codemirror-mode-javascript' ), SELF::CODEMIRROR_VERSION );
		wp_register_script( 'codemirror-addon-hint-sql',        plugins_url( "CodeMirror/addon/hint/sql-hint.js", __FILE__ ),        array( 'codemirror-addon-hint-show', 'codemirror-mode-sql' ), SELF::CODEMIRROR_VERSION );
		wp_register_script( 'codemirror-addon-hint-xml',        plugins_url( "CodeMirror/addon/hint/xml-hint.js", __FILE__ ),        array( 'codemirror-addon-hint-show', 'codemirror-mode-xml' ), SELF::CODEMIRROR_VERSION );

		// The linting engines for the lint addons...
		wp_register_script( 'csslint',  'http://csslint.net/js/csslint.js' );
		wp_register_script( 'htmlhint', 'http://htmlhint.com/js/htmlhint.js' );
		wp_register_script( 'jshint',   '//ajax.aspnetcdn.com/ajax/jshint/r07/jshint.js' );
		wp_register_script( 'jsonlint', 'https://rawgithub.com/zaach/jsonlint/79b553fb65c192add9066da64043458981b3972b/lib/jsonlint.js' );

		wp_register_script( 'codemirror-addon-lint',            plugins_url( "CodeMirror/addon/lint/lint.js",      __FILE__ ),       array( 'codemirror' ),            SELF::CODEMIRROR_VERSION );
		wp_register_script( 'codemirror-addon-lint-css',        plugins_url( "CodeMirror/addon/lint/css-lint.js",  __FILE__ ),       array( 'codemirror-addon-lint', 'csslint' ), SELF::CODEMIRROR_VERSION );
		wp_register_script( 'codemirror-addon-lint-html',       plugins_url( "CodeMirror/addon/lint/html-lint.js", __FILE__ ),       array( 'codemirror-addon-lint', 'htmlhint' ), SELF::CODEMIRROR_VERSION );
		wp_register_script( 'codemirror-addon-lint-javascript', plugins_url( "CodeMirror/addon/lint/javascript-lint.js", __FILE__ ), array( 'codemirror-addon-lint', 'jshint' ), SELF::CODEMIRROR_VERSION );
		wp_register_script( 'codemirror-addon-lint-json',       plugins_url( "CodeMirror/addon/lint/json-lint.js", __FILE__ ),       array( 'codemirror-addon-lint', 'jsonlint' ), SELF::CODEMIRROR_VERSION );

		wp_register_script( 'codemirror-addon-comment',                 plugins_url( "CodeMirror/addon/comment/comment.js", __FILE__ ),         array( 'codemirror' ), SELF::CODEMIRROR_VERSION );
		wp_register_script( 'codemirror-addon-comment-continuecomment', plugins_url( "CodeMirror/addon/comment/continuecomment.js", __FILE__ ), array( 'codemirror' ), SELF::CODEMIRROR_VERSION );

		wp_register_script( 'codemirror-addon-edit-closebrackets', plugins_url( "CodeMirror/addon/edit/closebrackets.js", __FILE__ ), array( 'codemirror' ), SELF::CODEMIRROR_VERSION );
		wp_register_script( 'codemirror-addon-edit-closetag',      plugins_url( "CodeMirror/addon/edit/closetag.js", __FILE__ ),      array( 'codemirror' ), SELF::CODEMIRROR_VERSION );
		wp_register_script( 'codemirror-addon-edit-continuelist',  plugins_url( "CodeMirror/addon/edit/continuelist.js", __FILE__ ),  array( 'codemirror' ), SELF::CODEMIRROR_VERSION );
		wp_register_script( 'codemirror-addon-edit-matchbrackets', plugins_url( "CodeMirror/addon/edit/matchbrackets.js", __FILE__ ), array( 'codemirror' ), SELF::CODEMIRROR_VERSION );
		wp_register_script( 'codemirror-addon-edit-matchtags',     plugins_url( "CodeMirror/addon/edit/matchtags.js", __FILE__ ),     array( 'codemirror' ), SELF::CODEMIRROR_VERSION );
		wp_register_script( 'codemirror-addon-edit-trailingspace', plugins_url( "CodeMirror/addon/edit/trailingspace.js", __FILE__ ), array( 'codemirror' ), SELF::CODEMIRROR_VERSION );

		wp_register_script( 'codemirror-addon-selection-active-line',    plugins_url( "CodeMirror/addon/selection/active-line.js", __FILE__ ),       array( 'codemirror' ), SELF::CODEMIRROR_VERSION );
		wp_register_script( 'codemirror-addon-selection-mark-selection', plugins_url( "CodeMirror/addon/selection/mark-selection.js", __FILE__ ),    array( 'codemirror' ), SELF::CODEMIRROR_VERSION );
		wp_register_script( 'codemirror-addon-selection-pointer',        plugins_url( "CodeMirror/addon/selection/selection-pointer.js", __FILE__ ), array( 'codemirror' ), SELF::CODEMIRROR_VERSION );

		wp_register_script( 'codemirror-mode-clike',      plugins_url( "CodeMirror/mode/clike/clike.js", __FILE__ ),           array( 'codemirror' ), SELF::CODEMIRROR_VERSION );
		wp_register_script( 'codemirror-mode-css',        plugins_url( "CodeMirror/mode/css/css.js", __FILE__ ),               array( 'codemirror', 'codemirror-addon-edit-matchbrackets' ), SELF::CODEMIRROR_VERSION );
		wp_register_script( 'codemirror-mode-diff',       plugins_url( "CodeMirror/mode/diff/diff.js", __FILE__ ),             array( 'codemirror' ), SELF::CODEMIRROR_VERSION );
		wp_register_script( 'codemirror-mode-html',       plugins_url( "CodeMirror/mode/htmlmixed/htmlmixed.js", __FILE__ ),   array( 'codemirror' ), SELF::CODEMIRROR_VERSION );
		wp_register_script( 'codemirror-mode-http',       plugins_url( "CodeMirror/mode/http/http.js", __FILE__ ),             array( 'codemirror' ), SELF::CODEMIRROR_VERSION );
		wp_register_script( 'codemirror-mode-javascript', plugins_url( "CodeMirror/mode/javascript/javascript.js", __FILE__ ), array( 'codemirror' ), SELF::CODEMIRROR_VERSION );
		wp_register_script( 'codemirror-mode-markdown',   plugins_url( "CodeMirror/mode/markdown/markdown.js", __FILE__ ),     array( 'codemirror' ), SELF::CODEMIRROR_VERSION );
		wp_register_script( 'codemirror-mode-php',        plugins_url( "CodeMirror/mode/php/php.js", __FILE__ ),               array( 'codemirror-mode-clike', 'codemirror-addon-edit-matchbrackets' ), SELF::CODEMIRROR_VERSION );
		wp_register_script( 'codemirror-mode-shell',      plugins_url( "CodeMirror/mode/shell/shell.js", __FILE__ ),           array( 'codemirror' ), SELF::CODEMIRROR_VERSION );
		wp_register_script( 'codemirror-mode-sql',        plugins_url( "CodeMirror/mode/sql/sql.js", __FILE__ ),               array( 'codemirror' ), SELF::CODEMIRROR_VERSION );
		wp_register_script( 'codemirror-mode-xml',        plugins_url( "CodeMirror/mode/xml/xml.js", __FILE__ ),               array( 'codemirror' ), SELF::CODEMIRROR_VERSION );
	}

	public static function register_styles() {
		wp_register_style( 'codemirror',                 plugins_url( "CodeMirror/lib/codemirror.css", __FILE__ ),       array(),               SELF::CODEMIRROR_VERSION );
		wp_register_style( 'codemirror-addon-show-hint', plugins_url( "CodeMirror/addon/hint/show-hint.css", __FILE__ ), array( 'codemirror' ), SELF::CODEMIRROR_VERSION );
		wp_register_style( 'codemirror-addon-lint',      plugins_url( "CodeMirror/addon/lint/lint.css", __FILE__ ),      array( 'codemirror' ), SELF::CODEMIRROR_VERSION );
	}

	public static function prep_codemirror_for_file( $file ) {
		switch ( @pathinfo( $file, PATHINFO_EXTENSION ) ) {
			case 'css' :
				wp_enqueue_script( 'codemirror-mode-css' );
				wp_enqueue_script( 'codemirror-addon-lint-css' );
				wp_enqueue_style( 'codemirror' );
				wp_enqueue_style( 'codemirror-addon-lint' );
				self::$codemirror_opts = array(
					'inputStyle'  => 'contenteditable',
					'lineNumbers' => true,
					'mode'        => 'text/css',
					'gutters'     => array( 'CodeMirror-lint-markers' ),
					'lint'        => true,
				);
				break;
			case 'php' :
				wp_enqueue_script( 'codemirror-mode-html' );
				wp_enqueue_script( 'codemirror-mode-xml' );
				wp_enqueue_script( 'codemirror-mode-javascript' );
				wp_enqueue_script( 'codemirror-mode-css' );
				wp_enqueue_script( 'codemirror-mode-php' );
				wp_enqueue_style( 'codemirror' );
				self::$codemirror_opts = array(
					'inputStyle'     => 'contenteditable',
					'lineNumbers'    => true,
					'mode'           => 'application/x-httpd-php',
					'indentUnit'     => 4,
					'indentWithTabs' => true,
				);
				break;
			case 'js' :
				wp_enqueue_script( 'codemirror-mode-javascript' );
				wp_enqueue_script( 'codemirror-addon-lint-javascript' );
				wp_enqueue_style( 'codemirror' );
				wp_enqueue_style( 'codemirror-addon-lint' );
				self::$codemirror_opts = array(
					'inputStyle'     => 'contenteditable',
					'lineNumbers'    => true,
					'mode'           => 'text/javascript',
					'indentUnit'     => 4,
					'indentWithTabs' => true,
					'gutters'        => array( 'CodeMirror-lint-markers' ),
					'lint'           => true,
				);
				break;
			case 'xml' :
				wp_enqueue_script( 'codemirror-mode-xml' );
				wp_enqueue_style( 'codemirror' );
				self::$codemirror_opts = array(
					'inputStyle'     => 'contenteditable',
					'lineNumbers'    => true,
					'mode'           => 'application/xml',
					'indentUnit'     => 4,
					'indentWithTabs' => true,
				);
				break;
			case 'txt' :
			default :
				wp_enqueue_script( 'codemirror' );
				wp_enqueue_style( 'codemirror' );
				self::$codemirror_opts = array(
					'inputStyle'     => 'contenteditable',
					'lineNumbers'    => true,
					'mode'           => 'text/plain',
					'indentUnit'     => 4,
					'indentWithTabs' => true,
				);
				break;
		}
	}

	public static function load_theme_editor_php() {
		global $file, $theme;

		wp_reset_vars( array( 'file', 'theme' ) );

		if ( $theme ) {
			$stylesheet = $theme;
		} else {
			$stylesheet = get_stylesheet();
		}

		$theme = wp_get_theme( $stylesheet );

		if ( empty( $file ) ) {
			$file = 'style.css';
		}

		self::$codemirror_opts = false;

		self::prep_codemirror_for_file( $file );

		/**
		 * Give folks a chance to filter the arguments passed to CodeMirror -- This will let them enable
		 * or disable it (by returning something that evaluates to false) as they choose as well.
		 *
		 * @param $codemirror_opts The array of options to be passed to codemirror. Falsey doesn't use codemirror.
		 * @param $file            The file being displayed.
		 * @param $theme           The WP_Theme object for the current theme being edited.
		 */
		self::$codemirror_opts = apply_filters( 'theme_editor_codemirror_opts', self::$codemirror_opts, $file, $theme );

		if ( self::$codemirror_opts ) {
			add_action( 'admin_footer-theme-editor.php', array( __CLASS__, 'do_codemirror_admin_editor' ) );
		}
	}

	public static function load_plugin_editor_php() {
		$file = '';
		$plugin = '';
		$plugins = @get_plugins();
		if ( isset( $_REQUEST['file'] ) ) {
			$file = sanitize_text_field( $_REQUEST['file'] );
		}

		if ( isset( $_REQUEST['plugin'] ) ) {
			$plugin = sanitize_text_field( $_REQUEST['plugin'] );
		}

		if ( empty( $plugin ) ) {
			if ( $file ) {
				$plugin = $file;
			} else {
				$plugin = array_keys( $plugins );
				$plugin = $plugin[0];
			}
		}

		$plugin_files = get_plugin_files($plugin);

		if ( empty( $file ) ) {
			$file = $plugin_files[0];
		}

		$file = validate_file_to_edit( $file, $plugin_files );

		self::$codemirror_opts = false;

		self::prep_codemirror_for_file( $file );

		/**
		 * Give folks a chance to filter the arguments passed to CodeMirror -- This will let them enable
		 * or disable it (by returning something that evaluates to false) as they choose as well.
		 *
		 * @param $codemirror_opts The array of options to be passed to codemirror. Falsey doesn't use codemirror.
		 * @param $file            The file being displayed.
		 * @param $plugin          The plugin file.
		 */
		self::$codemirror_opts = apply_filters( 'plugin_editor_codemirror_opts', self::$codemirror_opts, $file, $plugin );

		if ( self::$codemirror_opts ) {
			add_action( 'admin_footer-plugin-editor.php', array( __CLASS__, 'do_codemirror_admin_editor' ) );
		}
	}

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
		jQuery(document).ready(function($){
			wp.codemirror = CodeMirror.fromTextArea( document.getElementById('newcontent'), <?php echo json_encode( self::$codemirror_opts ); ?> );
		});
		</script>
		<?php
	}

	public static function customize_controls_enqueue_scripts() {
		wp_enqueue_script( 'codemirror-mode-css' );
		wp_enqueue_script( 'codemirror-addon-lint-css' );
		wp_enqueue_style( 'codemirror' );
		wp_enqueue_style( 'codemirror-addon-lint' );
		self::$codemirror_opts = apply_filters( 'customizer_custom_css_codemirror_opts', array(
			'inputStyle'     => 'contenteditable',
			'lineNumbers'    => true,
			'mode'           => 'text/css',
			'indentUnit'     => 2,
			'indentWithTabs' => true,
			'gutters'        => array( 'CodeMirror-lint-markers' ),
			'lint'           => true,
		) );

		add_action( 'customize_controls_print_footer_scripts', array( __CLASS__, 'customize_controls_print_footer_scripts' ) );
	}

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

						wp.codemirror = CodeMirror.fromTextArea( $textarea[0], <?php echo json_encode( self::$codemirror_opts ); ?> );

						// also refresh when focusing
						wp.codemirror.on( 'focus', function( editor ) {
							editor.refresh();
						} );

						// when the CodeMirror instance changes, mirror to the textarea,
						// where we have our "true" change event handler bound.
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
}
CodeMirror_WP::go();
