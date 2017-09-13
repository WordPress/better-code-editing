/* eslint-env node */
/* jshint node:true */
/* eslint-disable no-param-reassign */

module.exports = function( grunt ) {
	'use strict';

	grunt.initConfig( {

		pkg: grunt.file.readJSON( 'package.json' ),
		pkgLock: grunt.file.readJSON( 'package-lock.json' ),
		codemirrorLicenseBlock: grunt.file.read( 'node_modules/codemirror/lib/codemirror.js' ).replace( /\(function(.|\s)+$/, '' ).replace( /(^|\n)\/\/ */g, '$1' ).trim(),

		browserify: {
			codemirror: {
				options: {
					banner: '/*! This file is auto-generated from CodeMirror - <%= pkgLock.dependencies.codemirror.version %>\n\n<%= codemirrorLicenseBlock %>\n*/\n\n'
				},
				src: 'wp-includes/js/codemirror/codemirror.manifest.js',
				dest: 'wp-includes/js/codemirror/codemirror.js'
			}
		},

		uglify: {
			options: {
				ASCIIOnly: true,
				screwIE8: false
			},
			codemirror: {
				options: {

					// Preserve comments that start with a bang.
					preserveComments: /^!/
				},
				src: 'wp-includes/js/codemirror/codemirror.js',
				dest: 'wp-includes/js/codemirror/codemirror.min.js'
			}
		},

		concat: {
			codemirror: {
				options: {
					banner: '/*! This file is auto-generated from CodeMirror - <%= pkgLock.dependencies.codemirror.version %>\n\n<%= codemirrorLicenseBlock %>\n*/\n\n',
					separator: '\n',
					process: function( src, filepath ) {
						return '/* Source: ' + filepath.replace( 'node_modules/', '' ) + '*/\n' + src;
					}
				},
				src: [
					'node_modules/codemirror/lib/codemirror.css',
					'node_modules/codemirror/addon/hint/show-hint.css',
					'node_modules/codemirror/addon/lint/lint.css',
					'node_modules/codemirror/addon/dialog/dialog.css',
					'node_modules/codemirror/addon/display/fullscreen.css',
					'node_modules/codemirror/addon/fold/foldgutter.css',
					'node_modules/codemirror/addon/merge/merge.css',
					'node_modules/codemirror/addon/scroll/simplescrollbars.css',
					'node_modules/codemirror/addon/search/matchesonscrollbar.css',
					'node_modules/codemirror/addon/tern/tern.css'
				],
				dest: 'wp-includes/js/codemirror/codemirror.css'
			}
		},

		cssmin: {
			options: {
				compatibility: 'ie7'
			},
			codemirror: {
				expand: true,
				ext: '.min.css',
				src: [
					'wp-includes/js/codemirror/codemirror.css'
				]
			}
		},

		copy: {
			csslint: {
				src: 'node_modules/csslint/dist/csslint.js',
				dest: 'wp-includes/js/csslint.js'
			},
			htmlhint: {
				src: 'node_modules/htmlhint/lib/htmlhint.js',
				dest: 'wp-includes/js/htmlhint.js'
			},
			jshint: {
				src: 'node_modules/jshint/dist/jshint.js',
				dest: 'wp-includes/js/jshint.js'
			},
			jsonlint: {
				src: 'node_modules/jsonlint/lib/jsonlint.js',
				dest: 'wp-includes/js/jsonlint.js'
			}
		},

		rtlcss: {
			options: {
				opts: {
					clean: false,
					processUrls: { atrule: true, decl: false },
					stringMap: [
						{
							name: 'import-rtl-stylesheet',
							priority: 10,
							exclusive: true,
							search: [ '.css' ],
							replace: [ '-rtl.css' ],
							options: {
								scope: 'url',
								ignoreCase: false
							}
						}
					]
				},
				saveUnmodified: true,
				plugins: [
					{
						name: 'swap-dashicons-left-right-arrows',
						priority: 10,
						directives: {
							control: {},
							value: []
						},
						processors: [
							{
								expr: /content/im,
								action: function( prop, value ) {
									if ( '"\\f141"' === value ) { // Glyph: dashicons-arrow-left.
										value = '"\\f139"';
									} else if ( '"\\f340"' === value ) { // Glyph: dashicons-arrow-left-alt.
										value = '"\\f344"';
									} else if ( '"\\f341"' === value ) { // Glyph: dashicons-arrow-left-alt2.
										value = '"\\f345"';
									} else if ( '"\\f139"' === value ) { // Glyph: dashicons-arrow-right.
										value = '"\\f141"';
									} else if ( '"\\f344"' === value ) { // Glyph: dashicons-arrow-right-alt.
										value = '"\\f340"';
									} else if ( '"\\f345"' === value ) { // Glyph: dashicons-arrow-right-alt2.
										value = '"\\f341"';
									}
									return { prop: prop, value: value };
								}
							}
						]
					}
				]
			},
			core: {
				expand: true,
				ext: '-rtl.css',
				src: [
					'wp-admin/css/*.css',
					'!wp-admin/css/*-rtl.css'
				]
			}
		},

		// Clean up the build
		clean: {
			build: {
				src: [ 'build' ]
			}
		},

		// Shell actions
		shell: {
			options: {
				stdout: true,
				stderr: true
			},
			readme: {
				command: 'php dev-lib/generate-markdown-readme' // Generate the readme.md
			},
			lint: {
				command: 'CHECK_SCOPE=all bash dev-lib/pre-commit'
			},
			build_release_zip: {
				command: 'if [ -e build ]; then rm -r build; fi; mkdir build; rsync -avz ./ build/ --exclude-from=.svnignore; if [ -e better-code-editing.zip ]; then rm better-code-editing.zip; fi; cd build; zip -r ../better-code-editing.zip .; cd ..; echo; echo "Please see: $(pwd)/better-code-editing.zip"'
			},
			verify_matching_versions: {
				command: 'php bin/verify-version-consistency.php'
			}
		},

		// Deploys a git Repo to the WordPress SVN repo
		wp_deploy: {
			deploy: {
				options: {
					plugin_slug: '<%= pkg.name %>',
					build_dir: 'build',
					assets_dir: 'wp-assets'
				}
			}
		}

	} );

	// Load tasks
	grunt.loadNpmTasks( 'grunt-contrib-clean' );
	grunt.loadNpmTasks( 'grunt-contrib-copy' );
	grunt.loadNpmTasks( 'grunt-shell' );
	grunt.loadNpmTasks( 'grunt-wp-deploy' );
	grunt.loadNpmTasks( 'grunt-rtlcss' );
	grunt.loadNpmTasks( 'grunt-browserify' );
	grunt.loadNpmTasks( 'grunt-contrib-concat' );
	grunt.loadNpmTasks( 'grunt-contrib-uglify' );
	grunt.loadNpmTasks( 'grunt-contrib-cssmin' );

	grunt.registerTask( 'rtl', [ 'rtlcss:core' ] );

	// Register tasks
	grunt.registerTask( 'default', [
		'build'
	] );

	grunt.registerTask( 'readme', [
		'shell:readme'
	] );

	grunt.registerTask( 'build', [
		'readme',
		'copy',
		'shell:verify_matching_versions',
		'shell:lint',

		'browserify',
		'uglify',

		'concat',
		'cssmin',
		'rtl'
	] );

	grunt.registerTask( 'build-release-zip', [
		'build',
		'shell:build_release_zip'
	] );

	grunt.registerTask( 'deploy', [
		'build',
		'shell:build_release_zip',
		'wp_deploy',
		'clean'
	] );

};
