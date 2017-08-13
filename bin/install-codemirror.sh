#!/bin/bash

cd "$(dirname "$0")"

if [ -e ../wp-includes/js/codemirror ]; then
    rm -r ../wp-includes/js/codemirror
fi

mkdir -p ../wp-includes/js/codemirror
rsync -az --exclude-from codemirror-rsync-excludes.txt ../node_modules/codemirror/ ../wp-includes/js/codemirror/
