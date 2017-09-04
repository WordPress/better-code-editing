<?php
/**
 * Extensions to general template tags that can go anywhere in a template.
 *
 * @package WordPress
 * @subpackage Template
 */

/**
 * Gets settings for initializing the code editor.
 *
 * @since 4.9.0
 *
 * @param array $context {
 *     Context.
 *
 *     @type string $type The MIME type of the file to be edited.
 *     @type string $file Filename to be edited. Extension is used to sniff the type. Can be supplied as alternative to `$type` param.
 * }
 * @return array|false Settings for code editor or false if disabled.
 */
function wp_code_editor_settings( $context ) {

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
			'showTrailingSpace' => true,
			'styleActiveLine' => true,
			'continueComments' => true,
			'extraKeys' => array(
				'Ctrl-Space' => 'autocomplete',
				'Ctrl-/' => 'toggleComment',
				'Cmd-/' => 'toggleComment',
				'Alt-F' => 'findPersistent',
			),
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
		'htmlhint' => array(
			'rules' => array(
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
		),
	);

	$type = '';
	if ( isset( $context['type'] ) ) {
		$type = $context['type'];

		// Remap MIME types to ones that CodeMirror modes will recognize.
		if ( 'application/x-patch' === $type || 'text/x-patch' === $type ) {
			$type = 'text/x-diff';
		}
	} elseif ( isset( $context['file'] ) && false !== strpos( basename( $context['file'] ), '.' ) ) {
		$extension = strtolower( pathinfo( $context['file'], PATHINFO_EXTENSION ) );
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
			$settings['htmlhint']['rules']['kses'] = wp_kses_allowed_html( 'post' );
		}
	} elseif ( 'text/x-gfm' === $type ) {
		$settings['codemirror'] = array_merge( $settings['codemirror'], array(
			'mode' => 'gfm',
			'highlightFormatting' => true,
			'showTrailingSpace' => false, // GitHub-flavored markdown uses trailing spaces as a feature.
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

	/**
	 * Filters settings that are passed into the code editor.
	 *
	 * Returning a falsey value will disable the syntax-highlighting code editor.
	 *
	 * @since 4.9.0
	 *
	 * @param array $settings The array of settings passed to the code editor. A falsey value disables the editor.
	 * @param array $context {
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
 * @since 4.9.0
 *
 * @see wp_code_editor_settings()
 * @param array|false $settings Code editor settings.
 * @returns boolean Whether assets were enqueued.
 */
function wp_enqueue_code_editor( $settings ) {
	if ( empty( $settings ) || empty( $settings['codemirror'] ) ) {
		return false;
	}

	wp_enqueue_script( 'code-editor' );
	wp_enqueue_style( 'code-editor' );

	// @todo All of the following could be done in JS instead, similar to the post Editor?
	wp_enqueue_script( 'codemirror' );
	wp_enqueue_style( 'codemirror' );

	// Enqueue addons.
	$option_asset_mappings = array(
		'showTrailingSpace' => array( 'codemirror-addon-edit-trailingspace' ),
		'styleActiveLine'   => array( 'codemirror-addon-selection-active-line' ),
		'autoCloseBrackets' => array( 'codemirror-addon-edit-closebrackets' ),
		'matchBrackets'     => array( 'codemirror-addon-edit-matchbrackets' ),
		'autoCloseTags'     => array( 'codemirror-addon-edit-closetag' ),
		'matchTags'         => array( 'codemirror-addon-edit-matchtags' ),
		'continueComments'  => array( 'codemirror-addon-comment-continuecomment' ),
		// @todo Add recognition for all of the addon configs.
	);
	foreach ( $option_asset_mappings as $option => $handles ) {
		if ( ! empty( $settings['codemirror'][ $option ] ) ) {
			foreach ( $handles as $handle ) {
				wp_enqueue_script( $handle );
			}
		}
	}

	wp_enqueue_script( 'codemirror-addon-comment' );
	wp_enqueue_script( 'codemirror-addon-dialog' );
	wp_enqueue_style( 'codemirror-addon-dialog' );
	wp_enqueue_script( 'codemirror-addon-search-searchcursor' );
	wp_enqueue_script( 'codemirror-addon-search' );
	wp_enqueue_script( 'codemirror-addon-scroll-annotatescrollbar' );
	wp_enqueue_script( 'codemirror-addon-search-matchesonscrollbar' );
	wp_enqueue_script( 'codemirror-addon-search-jump-to-line' );

	if ( isset( $settings['codemirror']['mode'] ) ) {
		$mode = $settings['codemirror']['mode'];
		if ( is_string( $mode ) ) {
			$mode = array(
				'name' => $mode,
			);
		}

		switch ( $mode['name'] ) {
			case 'css':
			case 'text/css':
			case 'text/x-scss':
			case 'text/x-less':
				wp_enqueue_script( 'codemirror-mode-css' );
				wp_enqueue_script( 'codemirror-addon-hint-css' );
				wp_enqueue_style( 'codemirror-addon-show-hint' );

				if ( ! empty( $settings['codemirror']['lint'] ) ) {
					wp_enqueue_script( 'codemirror-addon-lint-css' );
				}
				break;
			case 'diff':
			case 'text/x-diff':
				wp_enqueue_script( 'codemirror-mode-diff' );
				break;
			case 'gfm':
			case 'text/x-gfm':
				wp_enqueue_script( 'codemirror-mode-gfm' );
				break;
			case 'htmlmixed':
			case 'text/html':
			case 'php':
			case 'application/x-httpd-php':
			case 'text/x-php':
				if ( false !== strpos( $mode['name'], 'php' ) ) {
					wp_enqueue_script( 'codemirror-mode-php' );
				}

				wp_enqueue_script( 'codemirror-mode-htmlmixed' );
				wp_enqueue_script( 'codemirror-addon-hint-html' );
				wp_enqueue_script( 'codemirror-addon-hint-javascript' );
				wp_enqueue_script( 'codemirror-addon-hint-css' );
				wp_enqueue_style( 'codemirror-addon-show-hint' );

				if ( ! empty( $settings['codemirror']['lint'] ) ) {
					if ( ! current_user_can( 'unfiltered_html' ) ) {
						wp_enqueue_script( 'htmlhint-kses' );
					}
					wp_enqueue_script( 'codemirror-addon-lint-html' );
				}
				break;
			case 'http':
			case 'message/http':
				wp_enqueue_script( 'codemirror-mode-http' );
				break;
			case 'javascript':
			case 'application/ecmascript':
			case 'application/json':
			case 'application/javascript':
			case 'application/ld+json':
			case 'text/typescript':
			case 'application/typescript':
				wp_enqueue_script( 'codemirror-mode-javascript' );
				wp_enqueue_script( 'codemirror-addon-hint-javascript' );
				wp_enqueue_style( 'codemirror-addon-show-hint' );

				if ( ! empty( $settings['codemirror']['lint'] ) ) {
					if ( ! empty( $mode['json'] ) || ! empty( $mode['jsonld'] ) ) {
							wp_enqueue_script( 'codemirror-addon-lint-json' );
					} else {
						wp_enqueue_script( 'codemirror-addon-lint-javascript' );
					}
				}
				break;
			case 'jsx':
			case 'text/jsx':
			case 'text/typescript-jsx':
				wp_enqueue_script( 'codemirror-mode-jsx' );
				break;
			case 'markdown':
			case 'text/x-markdown':
				wp_enqueue_script( 'codemirror-mode-markdown' );
				break;
			case 'nginx':
			case 'text/nginx':
				wp_enqueue_script( 'codemirror-mode-nginx' );
				break;
			case 'sass':
			case 'text/x-sass':
				wp_enqueue_script( 'codemirror-mode-sass' );
				break;
			case 'sh':
			case 'text/x-sh':
			case 'application/x-sh':
				wp_enqueue_script( 'codemirror-mode-shell' );
				break;
			case 'sql':
			case 'text/x-sql':
			case 'text/x-mysql':
			case 'text/x-mariadb':
			case 'text/x-cassandra':
			case 'text/x-plsql':
			case 'text/x-mssql':
			case 'text/x-hive':
			case 'text/x-pgsql':
			case 'text/x-gql':
			case 'text/x-gpsql':
				wp_enqueue_script( 'codemirror-mode-sql' );
				break;
			case 'xml':
			case 'application/xml':
			case 'application/svg+xml':
				wp_enqueue_script( 'codemirror-mode-xml' );
				break;
			case 'yaml':
			case 'codemirror-mode-yaml':
				wp_enqueue_script( 'codemirror-mode-yaml' );
				break;
		}

		if ( ! empty( $settings['codemirror']['lint'] ) ) {
			wp_enqueue_style( 'codemirror-addon-lint' );
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

	return true;
}
