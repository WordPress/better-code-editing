<!-- DO NOT EDIT THIS FILE; it is auto-generated from readme.txt -->
# Syntax Highlighting Code Editor for WordPress Core

Adding CodeMirror functionality to the Plugin and Theme file editors, as well as the Customizer Custom CSS box and Custom HTML widget.

**Contributors:** [georgestephanis](https://profiles.wordpress.org/georgestephanis), [westonruter](https://profiles.wordpress.org/westonruter), [obenland](https://profiles.wordpress.org/obenland), [wordpressdotorg](https://profiles.wordpress.org/wordpressdotorg)  
**Tags:** [codemirror](https://wordpress.org/plugins/tags/codemirror), [syntax-highlighter](https://wordpress.org/plugins/tags/syntax-highlighter), [linting](https://wordpress.org/plugins/tags/linting)  
**Requires at least:** 4.7  
**Tested up to:** 4.9-alpha  
**Stable tag:** 0.3.0  

[![Build Status](https://travis-ci.org/WordPress/codemirror-wp.svg?branch=master)](https://travis-ci.org/WordPress/codemirror-wp) 

## Description ##

This project is adding CodeMirror functionality to the Plugin and Theme file editors, as well as the Customizer Custom CSS box and the Custom HTML widget.

This is currently a Work In Progress playground for experimenting with bringing syntax highlighting to WordPress Core.

We're working around discussion on a Core ticket, [#12423](https://core.trac.wordpress.org/ticket/12423)

## Getting Started ##

You can locate a ZIP for this plugin on the [releases page](https://github.com/WordPress/codemirror-wp/releases) on GitHub. To install, simply go to your WP Admin and Plugins > Add New. Then click "Upload Plugin" and select the `codemirror-wp.zip` you downloaded from the releases page. Then click "Install Now" and on the next screen click "Activate Plugin". _Note on upgrading:_ If you want to update the plugin from a previous version, you must first deactivate it and uninstall it completely and then re-install and re-activate the new version (see [#9757](https://core.trac.wordpress.org/ticket/9757) for fixing this).

Otherwise, to set up the plugin for development: clone this repository and run `npm install` to download CodeMirror and other assets.

```bash
cd wp-content/plugins/
git clone --recursive https://github.com/WordPress/codemirror-wp.git
cd codemirror-wp
npm install
```

Also install the pre-commit hook via:

```bash
cd .git/hooks && ln -s ../../dev-lib/pre-commit pre-commit && cd -
```

Any questions, reach out to #core-customize on WordPress.org Slack or better open an issue on GitHub!

**Development of this plugin is done [on GitHub](https://github.com/WordPress/codemirror-wp). Pull requests welcome. Please see [issues](https://github.com/WordPress/codemirror-wp/issues) reported there.**

## Creating a Release ##

Contributors who want to make a new release, follow these steps:

1. Bump plugin versions in `package.json` (×1), `package-lock.json` (×1, just do `npm install` first), `readme.txt` (×1 in `Stable Tag`), and in `codemirror-wp.php` (×2: the metadata block in the header and also the `CodeMirror_WP::VERSION` constant).
2. Run `npm run build-release-zip` to create a `codemirror-wp.zip` in the plugin's root directory.
3. [Create new release](https://github.com/WordPress/codemirror-wp/releases/new) on GitHub targeting `master`, with the new plugin version as the tag and release title, and upload the `codemirror-wp.zip` as the associated binary. Publish the release.

## Changelog ##

### [0.3.0](https://github.com/WordPress/codemirror-wp/releases/tag/0.3.0) - 2017-08-18 ###
* Enable line-wrapping and constrain width for file editor to match `textarea`. See [#33](https://github.com/WordPress/codemirror-wp/pull/33), [#5](https://github.com/WordPress/codemirror-wp/issues/5), [#32](https://github.com/WordPress/codemirror-wp/issues/32).
* Improve accessibility of CodeMirror in Customizer's Additional CSS, including escape method from Tab trap. See [#34](https://github.com/WordPress/codemirror-wp/pull/34) and [#29](https://github.com/WordPress/codemirror-wp/issues/29).
* Improve file organization to prepare for core merge.
* See full commit log and diff: [0.2.0...0.3.0](https://github.com/WordPress/codemirror-wp/compare/0.2.0...0.3.0)

### [0.2.0](https://github.com/WordPress/codemirror-wp/releases/tag/0.2.0) - 2017-08-16 ###
* Add user setting for disabling Syntax Highlighting. See [#31](https://github.com/WordPress/codemirror-wp/pull/31).
* Improve release builds.
* See full commit log and diff: [0.1.0...0.2.0](https://github.com/WordPress/codemirror-wp/compare/0.1.0...0.2.0)

### [0.1.0](https://github.com/WordPress/codemirror-wp/releases/tag/0.1.0) - 2017-08-14 ###
Initial release.


