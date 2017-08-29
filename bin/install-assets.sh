#!/bin/bash

cd "$(dirname "$0")/.."

if [ -e wp-includes/js/codemirror ]; then
    rm -r wp-includes/js/codemirror
fi

mkdir -p wp-includes/js/codemirror
rsync -az --exclude-from bin/codemirror-rsync-excludes.txt node_modules/codemirror/ wp-includes/js/codemirror/

cp node_modules/csslint/dist/csslint.js wp-includes/js/csslint.js
cp node_modules/htmlhint/lib/htmlhint.js wp-includes/js/htmlhint.js
cp node_modules/jshint/dist/jshint.js wp-includes/js/jshint.js
cp node_modules/jsonlint/lib/jsonlint.js wp-includes/js/jsonlint.js
