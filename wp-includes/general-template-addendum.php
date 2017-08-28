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
 * @param array $context Context.
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
	if ( empty( $extension ) ) {
		_doing_it_wrong( __FUNCTION__, __( 'Missing valid "file" name in supplied context array.', 'better-code-editing' ), '4.9.0' );
	}

	if ( 'text/css' === $type || in_array( $extension, array( 'sass', 'scss', 'less' ), true ) ) {
		$settings['codemirror'] = array_merge( $settings['codemirror'], array(
			'mode' => 'text/css',
			'lint' => true,
			'autoCloseBrackets' => true,
			'matchBrackets' => true,
		) );
	} elseif ( in_array( $extension, array( 'php', 'phtml', 'php3', 'php4', 'php5', 'php7', 'phps' ), true ) ) {
		$settings['codemirror'] = array_merge( $settings['codemirror'], array(
			'mode' => 'application/x-httpd-php',
			'lint' => true,
			'autoCloseBrackets' => true,
			'autoCloseTags' => true,
			'matchBrackets' => true,
			'matchTags' => array(
				'bothTags' => true,
			),
		) );
	} elseif ( 'application/javascript' === $type ) {
		$settings['codemirror'] = array_merge( $settings['codemirror'], array(
			'mode' => 'text/javascript',
			'lint' => true,
			'autoCloseBrackets' => true,
			'matchBrackets' => true,
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
	} elseif ( false !== strpos( $type, 'xml' ) || in_array( $extension, array( 'xml', 'svg' ), true ) ) {
		$settings['codemirror'] = array_merge( $settings['codemirror'], array(
			'mode' => 'application/xml',
			'autoCloseBrackets' => true,
			'autoCloseTags' => true,
			'matchTags' => array(
				'bothTags' => true,
			),
		) );
	} else {
		$settings['codemirror']['mode'] = 'text/plain';
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
	wp_enqueue_script( 'codemirror' );
	wp_enqueue_style( 'codemirror' );
	if ( ! empty( $settings['codemirror']['showTrailingSpace'] ) ) {
		wp_enqueue_script( 'codemirror-addon-edit-trailingspace' );
	}
	if ( ! empty( $settings['codemirror']['styleActiveLine'] ) ) {
		wp_enqueue_script( 'codemirror-addon-selection-active-line' );
	}
	if ( ! empty( $settings['codemirror']['autoCloseBrackets'] ) ) {
		wp_enqueue_script( 'codemirror-addon-edit-closebrackets' );
	}
	if ( ! empty( $settings['codemirror']['matchBrackets'] ) ) {
		wp_enqueue_script( 'codemirror-addon-edit-matchbrackets' );
	}
	if ( ! empty( $settings['codemirror']['autoCloseTags'] ) ) {
		wp_enqueue_script( 'codemirror-addon-edit-closetag' );
	}
	if ( ! empty( $settings['codemirror']['matchTags'] ) ) {
		wp_enqueue_script( 'codemirror-addon-edit-matchtags' );
	}
	if ( ! empty( $settings['codemirror']['continueComments'] ) ) {
		wp_enqueue_script( 'codemirror-addon-comment-continuecomment' );
	}
	wp_enqueue_script( 'codemirror-addon-comment' );

	if ( isset( $settings['codemirror']['mode'] ) ) {
		switch ( $settings['codemirror']['mode'] ) {
			case 'application/x-httpd-php':
				wp_enqueue_script( 'codemirror-mode-php' );
				/* falls through */
			case 'htmlmixed':
				wp_enqueue_script( 'codemirror-mode-html' );
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
			case 'text/javascript':
				wp_enqueue_script( 'codemirror-mode-javascript' );
				wp_enqueue_script( 'codemirror-addon-hint-javascript' );
				wp_enqueue_style( 'codemirror-addon-show-hint' );

				if ( ! empty( $settings['codemirror']['lint'] ) ) {
					wp_enqueue_script( 'codemirror-addon-lint-javascript' );
				}
				break;
			case 'application/xml':
				wp_enqueue_script( 'codemirror-mode-xml' );
				break;
			case 'text/css':
				wp_enqueue_script( 'codemirror-mode-css' );
				wp_enqueue_script( 'codemirror-addon-hint-css' );
				wp_enqueue_style( 'codemirror-addon-show-hint' );

				if ( ! empty( $settings['codemirror']['lint'] ) ) {
					wp_enqueue_script( 'codemirror-addon-lint-css' );
				}
				break;
		}

		if ( ! empty( $settings['codemirror']['lint'] ) ) {
			wp_enqueue_style( 'codemirror-addon-lint' );
		}
	}

	wp_add_inline_script( 'code-editor', sprintf( 'jQuery.extend( wp.codeEditor.defaultSettings, %s );', wp_json_encode( $settings ) ) );

	return true;
}
