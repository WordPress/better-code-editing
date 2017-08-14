#!/bin/bash

set -e

cd "$(dirname "$0")/.."

npm install

if [ -e build ]; then
	rm -r build
fi
mkdir build
rsync -avz ./ build/ --exclude-from=.svnignore
cd build
zip -r ../codemirror-wp.zip .
cd ..

echo
echo "Please see: $(pwd)/codemirror-wp.zip"
