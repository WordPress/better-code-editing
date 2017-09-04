# Getting Started

You can locate a ZIP for this plugin on the [releases page](https://github.com/WordPress/better-code-editing/releases) on GitHub. To install, simply go to your WP Admin and Plugins > Add New. Then click "Upload Plugin" and select the `better-code-editing.zip` you downloaded from the releases page. Then click "Install Now" and on the next screen click "Activate Plugin". _Note on upgrading:_ If you want to update the plugin from a previous version, you must first deactivate it and uninstall it completely and then re-install and re-activate the new version (see [#9757](https://core.trac.wordpress.org/ticket/9757) for fixing this).

Otherwise, to set up the plugin for development: clone this repository and run `npm install` to download CodeMirror and other assets.

```bash
cd wp-content/plugins/
git clone --recursive https://github.com/WordPress/better-code-editing.git
cd better-code-editing
npm install
```

Also install the pre-commit hook via:

```bash
cd .git/hooks && ln -s ../../dev-lib/pre-commit pre-commit && cd -
```

# Creating a Release

Contributors who want to make a new release, follow these steps:

1. Bump plugin versions in `package.json` (×1), `package-lock.json` (×1, just do `npm install` first), and in `better-code-editing.php` (×2: the metadata block in the header and also the `BETTER_CODE_EDITING_PLUGIN_VERSION` constant).
2. Run `grunt deploy` to create a `better-code-editing.zip` in the plugin's root directory and to commit the plugin to WordPress.org.
3. [Create new release](https://github.com/WordPress/better-code-editing/releases/new) on GitHub targeting `master`, with the new plugin version as the tag and release title, and upload the `better-code-editing.zip` as the associated binary. Publish the release.
