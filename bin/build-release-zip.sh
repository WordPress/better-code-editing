#!/bin/bash

set -e

cd "$(dirname "$0")/.."

npm install

if [ -e build ]; then
	rm -r build
fi
mkdir build
rsync -avz ./ build/ --exclude-from=.svnignore
if [ -e better-code-editing.zip ]; then
	rm better-code-editing.zip
fi

cd build
zip -r ../better-code-editing.zip .
cd ..

echo
echo "Please see: $(pwd)/better-code-editing.zip"
