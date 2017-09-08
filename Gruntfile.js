/* eslint-env node */
/* jshint node:true */
/* eslint-disable no-param-reassign */

module.exports = function( grunt ) {
	'use strict';

	grunt.initConfig( {

		pkg: grunt.file.readJSON( 'package.json' ),

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
				command: 'npm run build-release-zip'
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
		'shell:verify_matching_versions',
		'shell:lint',
		'rtl',
		'shell:build_release_zip'
	] );

	grunt.registerTask( 'deploy', [
		'build',
		'wp_deploy',
		'clean'
	] );

};
