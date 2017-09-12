#!/bin/bash

cd "$(dirname "$0")/.."

set -e

# Todo: Replace with grunt-copy.
cp node_modules/csslint/dist/csslint.js wp-includes/js/csslint.js
cp node_modules/htmlhint/lib/htmlhint.js wp-includes/js/htmlhint.js
cp node_modules/jshint/dist/jshint.js wp-includes/js/jshint.js
cp node_modules/jsonlint/lib/jsonlint.js wp-includes/js/jsonlint.js
