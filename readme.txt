=== Better Code Editing ===
Contributors: georgestephanis, westonruter, obenland, melchoyce, wordpressdotorg
Tags: codemirror, syntax-highlighter, linting
Stable tag: 0.4.0
Requires at least: 4.7
Tested up to: 4.9-alpha

Adding CodeMirror functionality to the Plugin and Theme file editors, as well as the Customizer Custom CSS box and Custom HTML widget.

== Description ==

This project is adding CodeMirror functionality to the Plugin and Theme file editors, as well as the Customizer Custom CSS box and the Custom HTML widget.

This is currently a Work In Progress playground for experimenting with bringing syntax highlighting to WordPress Core.

We're working around discussion on a Core ticket, [#12423](https://core.trac.wordpress.org/ticket/12423)

Any questions, reach out to #core-customize on WordPress.org Slack or better open an issue on GitHub! See [contributing](https://github.com/WordPress/better-code-editing/blob/master/contributing.md).

**Development of this plugin is done [on GitHub](https://github.com/WordPress/better-code-editing). Pull requests welcome. Please see [issues](https://github.com/WordPress/better-code-editing/issues) reported there.**

== Changelog ==

= 0.4.0 - 2017-08-28 =

* Enable addon many goodies to improve UX and reduce accidental errors. See [#52](https://github.com/WordPress/better-code-editing/pull/52).
* Add autocomplete hinting. See [#51](https://github.com/WordPress/better-code-editing/pull/51) and [#50](https://github.com/WordPress/better-code-editing/issues/50).
* Improve mixed-mode autocomplete hints, including PHP. See [#58](https://github.com/WordPress/better-code-editing/issues/58).
* Configure HTMLHint including KSES rule. See [#47](https://github.com/WordPress/better-code-editing/pull/47).
* Configure JSHint with same rules as core. See [#46](https://github.com/WordPress/better-code-editing/pull/46).
* Limit CSSLint rules. See [#38](https://github.com/WordPress/better-code-editing/pull/38) and [#26](https://github.com/WordPress/better-code-editing/issues/26).
* Add tab trap escaping for CodeMirror in Custom HTML widget and theme/plugin editors. See [#43](https://github.com/WordPress/better-code-editing/pull/43) and [#37](https://github.com/WordPress/better-code-editing/issues/37).
* Rename codemirror-wp to better-code-editing. See [#42](https://github.com/WordPress/better-code-editing/pull/42).
* Add plugin icon. See [#40](https://github.com/WordPress/better-code-editing/pull/40).
* Fix errors on small screens. See [#39](https://github.com/WordPress/better-code-editing/pull/39) and [#11](https://github.com/WordPress/better-code-editing/issues/11).
* Refactor plugin class into include files to facilitate core patch creation. See [#54](https://github.com/WordPress/better-code-editing/pull/54).
* Add admin notice when plugin is obsolete. See [#57](https://github.com/WordPress/better-code-editing/pull/57).
* Upgrade CodeMirror to 5.29.0.
* See full commit log and diff: [0.3.0...0.4.0](https://github.com/WordPress/better-code-editing/compare/0.3.0...0.4.0)

= 0.3.0 - 2017-08-18 =

* Enable line-wrapping and constrain width for file editor to match `textarea`. See [#33](https://github.com/WordPress/better-code-editing/pull/33), [#5](https://github.com/WordPress/better-code-editing/issues/5), [#32](https://github.com/WordPress/better-code-editing/issues/32).
* Improve accessibility of CodeMirror in Customizer's Additional CSS, including escape method from Tab trap. See [#34](https://github.com/WordPress/better-code-editing/pull/34) and [#29](https://github.com/WordPress/better-code-editing/issues/29).
* Improve file organization to prepare for core merge.
* See full commit log and diff: [0.2.0...0.3.0](https://github.com/WordPress/better-code-editing/compare/0.2.0...0.3.0)

= 0.2.0 - 2017-08-16 =

* Add user setting for disabling Syntax Highlighting. See [#31](https://github.com/WordPress/better-code-editing/pull/31).
* Improve release builds.
* See full commit log and diff: [0.1.0...0.2.0](https://github.com/WordPress/better-code-editing/compare/0.1.0...0.2.0)

= 0.1.0 - 2017-08-14 =

Initial release.
