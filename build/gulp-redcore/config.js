var gulp = require('gulp'),
	argv = require('yargs').argv;

// Third part extension using redCORE / redCORE build folder
try {
	var config = require(process.cwd() + '/gulp-config.json');
	config.extensions = require(process.cwd() + '/gulp-extensions.json');
}
// redCORE repo relative
catch(err) {
	var config = require('../../build/gulp-config.json');
	config.extensions = require('../../build/gulp-extensions.json');
}

if (argv.wwwDir)
{
	config.wwwDir = argv.wwwDir;
}

if (argv.testRelease)
{
	config.release_dir = config.testrelease_dir;
}

config.skipVersion = argv.skipVersion ? 1 : 0;

module.exports = config;
