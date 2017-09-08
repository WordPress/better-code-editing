#!/usr/bin/env
<?php
/**
 * Verify versions referenced in plugin match.
 *
 * @package WordPress
 */

$versions = array();

$versions['package.json'] = json_decode( file_get_contents( dirname( __FILE__ ) . '/../package.json' ) )->version;
$versions['package-lock.json'] = json_decode( file_get_contents( dirname( __FILE__ ) . '/../package-lock.json' ) )->version;
$versions['composer.json'] = json_decode( file_get_contents( dirname( __FILE__ ) . '/../composer.json' ) )->version;

$readme_txt = file_get_contents( dirname( __FILE__ ) . '/../readme.txt' );
if ( ! preg_match( '/Stable tag:\s+(?P<version>\S+)/i', $readme_txt, $matches ) ) {
	echo "Could not find stable tag in readme\n";
	exit( 1 );
}
$versions['readme.txt#stable-tag'] = $matches['version'];

if ( ! preg_match( '/== Changelog ==\s+=\s+(?P<version>\d+\.\d+(?:.\d+)?)/', $readme_txt, $matches ) ) {
	echo "Could not find version in readme.txt changelog\n";
	exit( 1 );
}
$versions['readme.txt#changelog'] = $matches['version'];

$readme_md = file_get_contents( dirname( __FILE__ ) . '/../readme.md' );
if ( ! preg_match( '/## Changelog ##\s+###\s+(?P<version>\d+\.\d+(?:.\d+)?)/', $readme_md, $matches ) ) {
	echo "Could not find version in readme.md changelog\n";
	exit( 1 );
}
$versions['readme.md#changelog'] = $matches['version'];

$plugin_file = file_get_contents( dirname( __FILE__ ) . '/../better-code-editing.php' );
if ( ! preg_match( '/\*\s*Version:\s*(?P<version>\d+\.\d+(?:.\d+)?)/', $plugin_file, $matches ) ) {
	echo "Could not find version in readme metadata\n";
	exit( 1 );
}
$versions['better-code-editing.php#metadata'] = $matches['version'];

if ( ! preg_match( '/define\( \'BETTER_CODE_EDITING_PLUGIN_VERSION\', \'(?P<version>\d+\.\d+(?:.\d+)?)\'/', $plugin_file, $matches ) ) {
	echo "Could not find version in BETTER_CODE_EDITING_PLUGIN_VERSION constant\n";
	exit( 1 );
}
$versions['BETTER_CODE_EDITING_PLUGIN_VERSION'] = $matches['version'];

echo "Version references:\n";
print_r( $versions );

if ( 1 !== count( array_unique( $versions ) ) ) {
	echo "Not all version references have been updated\n.";
	exit( 1 );
}
