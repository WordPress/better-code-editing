#!/bin/bash

cd "$(dirname "$0")/.."

mkdir -p wp-includes/js
wget -O wp-includes/js/csslint.js http://csslint.net/js/csslint.js
wget -O wp-includes/js/htmlhint.js http://htmlhint.com/js/htmlhint.js
wget -O wp-includes/js/jshint.js //ajax.aspnetcdn.com/ajax/jshint/r07/jshint.js
wget -O wp-includes/js/jsonlint.js https://rawgithub.com/zaach/jsonlint/79b553fb65c192add9066da64043458981b3972b/lib/jsonlint.js
