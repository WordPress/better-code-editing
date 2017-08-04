# Syntax Highlighting Code Editor for WordPress

Howdy!  This is currently a Work In Progress playground for experimenting with bringing syntax highlighting to WordPress Core.

We're working around discussion on a Core ticket, [#12423](https://core.trac.wordpress.org/ticket/12423)

## Getting Started

Clone this repository, and then build CodeMirror -- the last step is necessary, otherwise the main CodeMirror JS library won't be available to be enqueued.

```bash
cd wp-content/plugins/
git clone https://github.com/WordPress/codemirror-wp.git --recursive
cd codemirror-wp/CodeMirror/
npm install
npm run-script build
```

For simplicity's sake we're running CM as a Git Submodule, rather than duplicating it in this repository.

Any questions, ping @georgestephanis on WordPress.org Slack!
