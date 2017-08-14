=== Syntax Highlighting Code Editor for WordPress Core ===
Contributors: georgestephanis, westonruter, obenland, wordpressdotorg
Tags: codemirror, syntax highlighting
Stable tag: trunk
Requires at least: 4.7
Tested up to: 4.9-alpha

Adding CodeMirror functionality to the Plugin and Theme file editors, as well as the Customizer Custom CSS box and Custom HTML widget.

== Description ==

This project is adding CodeMirror functionality to the Plugin and Theme file editors, as well as the Customizer Custom CSS box and the Custom HTML widget.

This is currently a Work In Progress playground for experimenting with bringing syntax highlighting to WordPress Core.

We're working around discussion on a Core ticket, [#12423](https://core.trac.wordpress.org/ticket/12423)

## Getting Started

You can locate a ZIP for this plugin on the [releases page](https://github.com/WordPress/codemirror-wp/releases) on GitHub. To install, simply go to your WP Admin and Plugins > Add New. Then click "Upload Plugin" and select the `codemirror-wp.zip` you downloaded from the releases page. Then click "Install Now" and on the next screen click "Activate Plugin".

Otherwise, to set up the plugin for development: clone this repository and run `npm install` to download CodeMirror and other assets.

<pre lang="bash">
cd wp-content/plugins/
git clone https://github.com/WordPress/codemirror-wp.git
cd codemirror-wp
npm install
</pre>

Any questions, reach out to #core-customize on WordPress.org Slack or better open an issue on GitHub!

**Development of this plugin is done [on GitHub](https://github.com/WordPress/codemirror-wp). Pull requests welcome. Please see [issues](https://github.com/WordPress/codemirror-wp/issues) reported there.**
