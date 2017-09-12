<?php
/**
 * Extensions to general template tags that can go anywhere in a template.
 *
 * @package WordPress
 * @subpackage Template
 */

/**
 * Enqueue assets needed by the code editor for the given settings.
 *
 * @since 4.9.0
 *
 * @see wp_enqueue_editor()
 * @see _WP_Editors::parse_settings()
 * @param array $args {
 *     Args.
 *
 *     @type string   $type     The MIME type of the file to be edited.
 *     @type string   $file     Filename to be edited. Extension is used to sniff the type. Can be supplied as alternative to `$type` param.
 *     @type array    $settings Settings to merge on top of defaults which derive from `$type` or `$file` args.
 *     @type WP_Theme $theme    Theme being edited when on theme editor.
 *     @type string   $plugin   Plugin being edited when on plugin editor.
 * }
 * @returns array|false Settings for the enqueued code editor, or false if the editor was not enqueued .
 */
function wp_enqueue_code_editor( $args ) {
	if ( is_user_logged_in() && 'false' === wp_get_current_user()->syntax_highlighting ) {
		return false;
	}

	$settings = array(
		'codemirror' => array(
			'indentUnit' => 4,
			'indentWithTabs' => true,
			'inputStyle' => 'contenteditable',
			'lineNumbers' => true,
			'lineWrapping' => true,
			'styleActiveLine' => true,
			'continueComments' => true,
			'extraKeys' => array(
				'Ctrl-Space' => 'autocomplete',
				'Ctrl-/' => 'toggleComment',
				'Cmd-/' => 'toggleComment',
				'Alt-F' => 'findPersistent',
			),
			'direction' => 'ltr', // Code is shown in LTR even in RTL languages.
		),
		'csslint' => array(
			'errors' => true, // Parsing errors.
			'box-model' => true,
			'display-property-grouping' => true,
			'duplicate-properties' => true,
			'known-properties' => true,
			'outline-none' => true,
		),
		'jshint' => array(
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
		'htmlhint' => array(
			'tagname-lowercase' => true,
			'attr-lowercase' => true,
			'attr-value-double-quotes' => true,
			'doctype-first' => false,
			'tag-pair' => true,
			'spec-char-escape' => true,
			'id-unique' => true,
			'src-not-empty' => true,
			'attr-no-duplication' => true,
			'alt-require' => true,
			'space-tab-mixed-disabled' => 'tab',
			'attr-unsafe-chars' => true,
		),
	);

	$type = '';
	if ( isset( $args['type'] ) ) {
		$type = $args['type'];

		// Remap MIME types to ones that CodeMirror modes will recognize.
		if ( 'application/x-patch' === $type || 'text/x-patch' === $type ) {
			$type = 'text/x-diff';
		}
	} elseif ( isset( $args['file'] ) && false !== strpos( basename( $args['file'] ), '.' ) ) {
		$extension = strtolower( pathinfo( $args['file'], PATHINFO_EXTENSION ) );
		foreach ( wp_get_mime_types() as $exts => $mime ) {
			if ( preg_match( '!^(' . $exts . ')$!i', $extension ) ) {
				$type = $mime;
				break;
			}
		}

		// Supply any types that are not matched by wp_get_mime_types().
		if ( empty( $type ) ) {
			switch ( $extension ) {
				case 'conf':
					$type = 'text/nginx';
					break;
				case 'css':
					$type = 'text/css';
					break;
				case 'diff':
				case 'patch':
					$type = 'text/x-diff';
					break;
				case 'html':
				case 'htm':
					$type = 'text/html';
					break;
				case 'http':
					$type = 'message/http';
					break;
				case 'js':
					$type = 'text/javascript';
					break;
				case 'json':
					$type = 'application/json';
					break;
				case 'jsx':
					$type = 'text/jsx';
					break;
				case 'less':
					$type = 'text/x-less';
					break;
				case 'md':
					$type = 'text/x-gfm';
					break;
				case 'php':
				case 'phtml':
				case 'php3':
				case 'php4':
				case 'php5':
				case 'php7':
				case 'phps':
					$type = 'application/x-httpd-php';
					break;
				case 'scss':
					$type = 'text/x-scss';
					break;
				case 'sass':
					$type = 'text/x-sass';
					break;
				case 'sh':
				case 'bash':
					$type = 'text/x-sh';
					break;
				case 'sql':
					$type = 'text/x-sql';
					break;
				case 'svg':
					$type = 'application/svg+xml';
					break;
				case 'xml':
					$type = 'text/xml';
					break;
				case 'yml':
				case 'yaml':
					$type = 'text/x-yaml';
					break;
				case 'txt':
				default:
					$type = 'text/plain';
					break;
			}
		}
	}

	if ( 'text/css' === $type ) {
		$settings['codemirror'] = array_merge( $settings['codemirror'], array(
			'mode' => 'css',
			'lint' => true,
			'autoCloseBrackets' => true,
			'matchBrackets' => true,
		) );
	} elseif ( 'text/x-scss' === $type || 'text/x-less' === $type || 'text/x-sass' === $type ) {
		$settings['codemirror'] = array_merge( $settings['codemirror'], array(
			'mode' => $type,
			'autoCloseBrackets' => true,
			'matchBrackets' => true,
		) );
	} elseif ( 'text/x-diff' === $type ) {
		$settings['codemirror'] = array_merge( $settings['codemirror'], array(
			'mode' => 'diff',
		) );
	} elseif ( 'text/html' === $type ) {
		$settings['codemirror'] = array_merge( $settings['codemirror'], array(
			'mode' => 'htmlmixed',
			'lint' => true,
			'autoCloseBrackets' => true,
			'autoCloseTags' => true,
			'matchTags' => array(
				'bothTags' => true,
			),
		) );

		if ( ! current_user_can( 'unfiltered_html' ) ) {
			$settings['htmlhint']['kses'] = wp_kses_allowed_html( 'post' );
		}
	} elseif ( 'text/x-gfm' === $type ) {
		$settings['codemirror'] = array_merge( $settings['codemirror'], array(
			'mode' => 'gfm',
			'highlightFormatting' => true,
		) );
	} elseif ( 'application/javascript' === $type || 'text/javascript' === $type ) {
		$settings['codemirror'] = array_merge( $settings['codemirror'], array(
			'mode' => 'javascript',
			'lint' => true,
			'autoCloseBrackets' => true,
			'matchBrackets' => true,
		) );
	} elseif ( false !== strpos( $type, 'json' ) ) {
		$settings['codemirror'] = array_merge( $settings['codemirror'], array(
			'mode' => array(
				'name' => 'javascript',
			),
			'lint' => true,
			'autoCloseBrackets' => true,
			'matchBrackets' => true,
		) );
		if ( 'application/ld+json' === $type ) {
			$settings['codemirror']['mode']['jsonld'] = true;
		} else {
			$settings['codemirror']['mode']['json'] = true;
		}
	} elseif ( false !== strpos( $type, 'jsx' ) ) {
		$settings['codemirror'] = array_merge( $settings['codemirror'], array(
			'mode' => 'jsx',
			'autoCloseBrackets' => true,
			'matchBrackets' => true,
		) );
	} elseif ( 'text/x-markdown' === $type ) {
		$settings['codemirror'] = array_merge( $settings['codemirror'], array(
			'mode' => 'markdown',
			'highlightFormatting' => true,
		) );
	} elseif ( 'text/nginx' === $type ) {
		$settings['codemirror'] = array_merge( $settings['codemirror'], array(
			'mode' => 'nginx',
		) );
	} elseif ( 'application/x-httpd-php' === $type ) {
		$settings['codemirror'] = array_merge( $settings['codemirror'], array(
			'mode' => 'php',
			'autoCloseBrackets' => true,
			'autoCloseTags' => true,
			'matchBrackets' => true,
			'matchTags' => array(
				'bothTags' => true,
			),
		) );
	} elseif ( 'text/x-sql' === $type || 'text/x-mysql' === $type ) {
		$settings['codemirror'] = array_merge( $settings['codemirror'], array(
			'mode' => 'sql',
			'autoCloseBrackets' => true,
			'matchBrackets' => true,
		) );
	} elseif ( false !== strpos( $type, 'xml' ) ) {
		$settings['codemirror'] = array_merge( $settings['codemirror'], array(
			'mode' => 'xml',
			'autoCloseBrackets' => true,
			'autoCloseTags' => true,
			'matchTags' => array(
				'bothTags' => true,
			),
		) );
	} elseif ( 'text/x-yaml' === $type ) {
		$settings['codemirror'] = array_merge( $settings['codemirror'], array(
			'mode' => 'yaml',
		) );
	} else {
		$settings['codemirror']['mode'] = $type;
	}

	if ( ! empty( $settings['codemirror']['lint'] ) ) {
		$settings['codemirror']['gutters'][] = 'CodeMirror-lint-markers';
	}

	// Let settings supplied via args override any defaults.
	if ( isset( $args['settings'] ) ) {
		foreach ( $args['settings'] as $key => $value ) {
			$settings[ $key ] = array_merge(
				$settings[ $key ],
				$value
			);
		}
	}

	/**
	 * Filters settings that are passed into the code editor.
	 *
	 * Returning a falsey value will disable the syntax-highlighting code editor.
	 *
	 * @since 4.9.0
	 *
	 * @param array $settings The array of settings passed to the code editor. A falsey value disables the editor.
	 * @param array $args {
	 *     Args passed when calling `wp_enqueue_code_editor()`.
	 *
	 *     @type string   $type     The MIME type of the file to be edited.
	 *     @type string   $file     Filename being edited.
	 *     @type array    $settings Settings to merge on top of defaults which derive from `$type` or `$file` args.
	 *     @type WP_Theme $theme    Theme being edited when on theme editor.
	 *     @type string   $plugin   Plugin being edited when on plugin editor.
	 * }
	 */
	$settings = apply_filters( 'wp_code_editor_settings', $settings, $args );

	if ( empty( $settings ) || empty( $settings['codemirror'] ) ) {
		return false;
	}

	wp_enqueue_script( 'code-editor' );
	wp_enqueue_style( 'code-editor' );

	wp_enqueue_script( 'codemirror' );
	wp_enqueue_style( 'codemirror' );

	if ( isset( $settings['codemirror']['mode'] ) ) {
		$mode = $settings['codemirror']['mode'];
		if ( is_string( $mode ) ) {
			$mode = array(
				'name' => $mode,
			);
		}

		if ( ! empty( $settings['codemirror']['lint'] ) ) {
			switch ( $mode['name'] ) {
				case 'css':
				case 'text/css':
				case 'text/x-scss':
				case 'text/x-less':
					wp_enqueue_script( 'csslint' );
					break;
				case 'htmlmixed':
				case 'text/html':
				case 'php':
				case 'application/x-httpd-php':
				case 'text/x-php':
					wp_enqueue_script( 'htmlhint' );
					wp_enqueue_script( 'csslint' );
					wp_enqueue_script( 'jshint' );
					if ( ! current_user_can( 'unfiltered_html' ) ) {
						wp_enqueue_script( 'htmlhint-kses' );
					}
					break;
				case 'javascript':
				case 'application/ecmascript':
				case 'application/json':
				case 'application/javascript':
				case 'application/ld+json':
				case 'text/typescript':
				case 'application/typescript':
					wp_enqueue_script( 'jshint' );
					wp_enqueue_script( 'jsonlint' );
					break;
			}
		}
	}

	wp_add_inline_script( 'code-editor', sprintf( 'jQuery.extend( wp.codeEditor.defaultSettings, %s );', wp_json_encode( $settings ) ) );

	/**
	 * Fires when scripts and styles are enqueued for the code editor.
	 *
	 * @since 4.9.0
	 *
	 * @param array $settings Settings for the enqueued code editor.
	 */
	do_action( 'wp_enqueue_code_editor', $settings );

	return $settings;
}
