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
	}

	public static function register_scripts() {
		wp_register_script( 'codemirror', plugins_url( "CodeMirror/lib/codemirror.js", __FILE__ ), array(), SELF::CODEMIRROR_VERSION );

		wp_register_script( 'codemirror-addon-hint-show',       plugins_url( "CodeMirror/addon/hint/show-hint.js", __FILE__ ),       array( 'codemirror' ), SELF::CODEMIRROR_VERSION );
		wp_register_script( 'codemirror-addon-hint-css',        plugins_url( "CodeMirror/addon/hint/css-hint.js", __FILE__ ),        array( 'codemirror-addon-hint-show', 'codemirror-mode-css' ), SELF::CODEMIRROR_VERSION );
		wp_register_script( 'codemirror-addon-hint-html',       plugins_url( "CodeMirror/addon/hint/html-hint.js", __FILE__ ),       array( 'codemirror-addon-hint-show', 'codemirror-mode-html' ), SELF::CODEMIRROR_VERSION );
		wp_register_script( 'codemirror-addon-hint-javascript', plugins_url( "CodeMirror/addon/hint/javascript-hint.js", __FILE__ ), array( 'codemirror-addon-hint-show', 'codemirror-mode-javascript' ), SELF::CODEMIRROR_VERSION );
		wp_register_script( 'codemirror-addon-hint-sql',        plugins_url( "CodeMirror/addon/hint/sql-hint.js", __FILE__ ),        array( 'codemirror-addon-hint-show', 'codemirror-mode-sql' ), SELF::CODEMIRROR_VERSION );
		wp_register_script( 'codemirror-addon-hint-xml',        plugins_url( "CodeMirror/addon/hint/xml-hint.js", __FILE__ ),        array( 'codemirror-addon-hint-show', 'codemirror-mode-xml' ), SELF::CODEMIRROR_VERSION );

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
		wp_register_style( 'codemirror',                 plugins_url( "CodeMirror/lib/codemirror.css", __FILE__ ), array(), SELF::CODEMIRROR_VERSION );
		wp_register_style( 'codemirror-addon-show-hint', plugins_url( "CodeMirror/lib/codemirror.css", __FILE__ ), array( 'codemirror' ), SELF::CODEMIRROR_VERSION );
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

		switch ( @pathinfo( $file, PATHINFO_EXTENSION ) ) {
			case 'css' :
				wp_enqueue_script( 'codemirror-mode-css' );
				wp_enqueue_style( 'codemirror' );
				self::$codemirror_opts = array(
					'inputStyle'  => 'contenteditable',
					'lineNumbers' => true,
					'mode'        => 'text/css',
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
			default :
				break;
		}

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
			add_action( 'admin_footer-theme-editor.php', array( __CLASS__, 'do_codemirror' ) );
		}
	}

	public static function do_codemirror() {
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
}
CodeMirror_WP::go();
