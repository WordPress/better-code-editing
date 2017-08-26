#!/bin/bash

cd "$(dirname "$0")/.."

mkdir -p wp-includes/js
wget -O wp-includes/js/csslint.js http://csslint.net/js/csslint.js
wget -O wp-includes/js/htmlhint.js http://htmlhint.com/js/htmlhint.js
wget -O wp-includes/js/jshint.js https://ajax.aspnetcdn.com/ajax/jshint/r07/jshint.js
wget -O wp-includes/js/jsonlint.js https://raw.githubusercontent.com/zaach/jsonlint/master/lib/jsonlint.js
